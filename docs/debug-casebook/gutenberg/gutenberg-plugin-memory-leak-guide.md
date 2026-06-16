# Gutenberg Plugin Memory Leak — Debug Guide

<!-- AGENT CONTEXT
This document is a technical reference for diagnosing and fixing memory leaks
in a custom WordPress Gutenberg plugin. It is structured for both human developers
and AI coding agents. Each section is self-contained. When given a specific file or
symptom, jump to the relevant section and follow the pseudocode patterns.
-->

---

## 1. Plugin context

| Field | Value |
|---|---|
| Platform | WordPress, Gutenberg block editor |
| Plugin type | Gutenberg enhancement — custom blocks + core block extensions |
| Frontend stack | TypeScript (vanilla, no framework wrapper) |
| UI components | Swiper.js slider, custom sidebar panel extensions |
| Affected areas | Block editor page load, sidebar Inspector Controls |

### Observed symptoms

- **Slow load**: opening the block editor page takes abnormally long
- **Sidebar freeze**: sidebar controls stop reflecting changes; user edits have no effect on UI
- **Progressive degradation**: the longer the session, the worse it gets
- **Memory growth**: RAM used by the browser tab increases over time without recovering

### Likely root cause areas (in priority order)

1. `wp.data.subscribe()` accumulating without unsubscribe
2. Swiper instances not destroyed on block unmount / re-render
3. React `useEffect` hooks missing cleanup return functions
4. Native event listeners added on each render without removal
5. Module-level or closure-level collections growing unbounded

---

## 2. Diagnostic tools

### 2.1 Browser DevTools

#### Memory tab (primary for leak detection)

| Tool | When to use |
|---|---|
| **Heap Snapshot** | Compare memory state before/after an action sequence |
| **Allocation Timeline** | See which objects are being allocated continuously over time |
| **Allocation Sampling** | Lower-overhead sampling; use for long-running sessions |

Workflow:
```
1. Open editor, do nothing → Snapshot A
2. Perform 5–10 edit cycles (open/close panels, change sidebar controls)
3. Snapshot B → Compare with Snapshot A
4. Filter: "Objects allocated between snapshots"
5. Sort by "Retained Size" DESC

Signals to look for:
  - "(closure)"          → closure overcapture
  - "Detached HTMLElement" → DOM node held after unmount
  - "Swiper"             → instance not destroyed
  - "Subscribe" / fn refs → wp.data subscriptions accumulating
```

#### Performance tab (primary for slowness)

```
1. Record while loading or editing
2. Look for Long Tasks > 50ms in the flame chart
3. Identify task type:
   - JS evaluation      → check Swiper init being called repeatedly
   - Layout / Paint     → check DOM mutation loops
   - Unknown JS         → expand call stack, check subscribe callbacks
```

#### Task Manager (Shift+Esc)

Quick sanity check: watch the Memory column for the editor tab.
If it grows steadily with no plateaus → active leak confirmed.

---

### 2.2 React / Gutenberg tools

#### React DevTools Profiler

```
1. Open Profiler tab → Start recording
2. Click a sidebar control once
3. Stop recording
4. Inspect flame chart

Signal: if the same component renders 10+ times from a single user action
→ render loop caused by a subscribe callback updating state continuously
```

#### wp.data store — quick console audit

```javascript
// Paste in browser console to count how many times store fires per action:

count = 0
unsub = wp.data.subscribe(() => count++)
// Perform ONE action in the sidebar
unsub()
console.log('Subscribe fires per action:', count)

// Healthy: count < 5
// Leak indicator: count > 20 (each previous subscribe still firing)
```

#### Detecting duplicate subscribers

```javascript
// Check if subscribe is accumulating over renders:
// Before doing anything:
window.__subCount = 0
const _orig = wp.data.subscribe
wp.data.subscribe = function(cb) {
  window.__subCount++
  console.log('Total subscribes registered:', window.__subCount)
  return _orig(cb)
}
// Then navigate around the editor and watch the count grow
```

---

### 2.3 TypeScript / Build

- Enable full source maps (`"sourceMap": true` in tsconfig) so stack traces point to `.ts` files
- Enable `"strictNullChecks": true` — catches cleanup paths that can receive `undefined`
- Use Webpack Bundle Analyzer to verify Swiper is not imported twice (duplicate instances)

---

## 3. Diagnostic workflow (step-by-step)

```
START: Symptom observed
│
├─ [Slow load / high CPU]
│   └─ Performance Tab → Record on load
│       ├─ Long Task: JS eval   → check Swiper init (section 4.B)
│       ├─ Long Task: Layout    → check DOM mutation loop (section 4.D)
│       └─ No clear task        → Allocation Timeline (section 2.1)
│
├─ [Sidebar freeze / no UI update]
│   └─ React DevTools Profiler → Record → click one control
│       ├─ Same component renders 10+ times → render loop
│       │   └─ Source: wp.data subscribe without guard (section 4.A)
│       ├─ Swiper wrapper re-renders each time → Swiper not destroyed (section 4.B)
│       └─ Custom block → useEffect missing cleanup (section 4.C)
│
└─ [RAM grows over time]
    └─ Memory Tab → Heap Snapshot A → actions → Snapshot B → compare delta
        ├─ "(closure)" grows      → closure overcapture (section 4.E)
        ├─ "Detached HTMLElement" → DOM ref held after unmount (section 4.F)
        ├─ Swiper objects grow    → destroy() not called (section 4.B)
        └─ Map / Array grows      → unbounded collection (section 4.G)
```

---

## 4. Leak patterns — identification and fix

Each pattern includes:
- **Smell**: what to look for when reading code
- **Bad pseudocode**: the problematic pattern
- **Fix pseudocode**: the corrected version

---

### 4.A — wp.data subscribe without unsubscribe

**Category**: Lifecycle asymmetry  
**Impact**: High — causes sidebar freeze and render storm

**Smell indicators**:
- `wp.data.subscribe(...)` call with no stored return value
- Subscribe call inside a function that runs on each render
- No `unsubscribe()` call in any cleanup / unmount path

```
// BAD — subscribe on every render, never cleaned up
FUNCTION render():
    wp.data.subscribe(() => {
        newValue = wp.data.select('core/editor').getEditedPostAttribute('meta')
        updateUI(newValue)
    })
    // Each render adds one more subscriber
    // After N renders: N subscribers all firing simultaneously on every state change

// GOOD — single subscription with cleanup
FUNCTION onMount():
    previousValue = null

    unsubscribeFn = wp.data.subscribe(() => {
        newValue = wp.data.select('core/editor').getEditedPostAttribute('meta')

        IF newValue === previousValue: RETURN   // guard: skip if no real change
        previousValue = newValue
        updateUI(newValue)
    })

    RETURN () => unsubscribeFn()   // cleanup — call this on unmount
```

**Agent instruction**: Search all `.ts` / `.tsx` files for `wp.data.subscribe`. For each call:
1. Check if the return value is stored in a variable
2. Check if that variable is called as a function in any cleanup/unmount/`useEffect` return
3. Check if there is a guard comparing old vs new value inside the callback
4. Flag any subscribe call missing any of these three

---

### 4.B — Swiper instance not destroyed

**Category**: Lifecycle asymmetry  
**Impact**: High — DOM nodes, event listeners, MutationObservers all accumulate

**Smell indicators**:
- `new Swiper(...)` call without a corresponding `.destroy()`
- Swiper init inside a function that can be called multiple times
- No `swiperRef` or similar variable tracking the instance

```
// BAD — new Swiper on each render, old instance never destroyed
FUNCTION initSlider(container):
    new Swiper(container, {
        slidesPerView: 3,
        on: { slideChange: handleChange }
    })
    // Previous Swiper on same container still has:
    //   - its own event listeners
    //   - MutationObserver watching container
    //   - internal RAF loops

// GOOD — destroy previous, track instance, cleanup on unmount
swiperInstance = null

FUNCTION initSlider(container):
    IF swiperInstance IS NOT null:
        swiperInstance.destroy(true, true)   // cleanStyles=true, deleteInstance=true
        swiperInstance = null

    swiperInstance = new Swiper(container, {
        slidesPerView: 3,
        on: { slideChange: handleChange }
    })

FUNCTION onBlockUnmount():
    IF swiperInstance IS NOT null:
        swiperInstance.destroy(true, true)
        swiperInstance = null
```

**Agent instruction**: Search for `new Swiper(`. For each instantiation:
1. Check if the return value is stored
2. Check if `.destroy(true, true)` is called before re-initializing
3. Check if `.destroy()` is called in unmount / `useEffect` cleanup
4. Flag instantiations inside loops or functions called on state change

---

### 4.C — useEffect missing cleanup return

**Category**: Lifecycle asymmetry  
**Impact**: Medium-High — depends on what the effect sets up

**Smell indicators**:
- `useEffect` with an async-like setup (observer, subscription, listener) but no `return`
- `useEffect` return value is a non-function (e.g., a Promise)
- Effect that calls `subscribe`, `observe`, `addEventListener`, or `setInterval`

```
// BAD — no cleanup, observer accumulates
useEffect(() => {
    const observer = new IntersectionObserver(callback, options)
    observer.observe(containerRef.current)
    // No return → observer never disconnected
    // Each dependency change adds one more observer on the same element
}, [dependency])

// GOOD — cleanup in return
useEffect(() => {
    const observer = new IntersectionObserver(callback, options)
    observer.observe(containerRef.current)

    RETURN () => {
        observer.disconnect()
    }
}, [dependency])

// BAD — async effect returns Promise (React ignores it as cleanup)
useEffect(async () => {
    const sub = await store.subscribe(handler)
    return sub   // This is a Promise, not a cleanup function — React ignores it
}, [])

// GOOD — handle async properly
useEffect(() => {
    let cancelled = false
    let sub = null

    store.subscribe(handler).then(s => {
        IF NOT cancelled: sub = s
    })

    RETURN () => {
        cancelled = true
        IF sub IS NOT null: sub.unsubscribe()
    }
}, [])
```

**Agent instruction**: Find all `useEffect` calls. For each:
1. Check if the callback contains any of: `addEventListener`, `subscribe`, `observe`, `setInterval`, `setTimeout`, `new Observer`
2. If yes, check that the callback has a `return () => { ... }` at the end
3. Check that the return is a plain function, not a Promise
4. Flag useEffects with setup calls but no return, or with `async` as the callback

---

### 4.D — Event listeners added without removal

**Category**: Lifecycle asymmetry  
**Impact**: Medium — grows proportionally to number of user actions

**Smell indicators**:
- `addEventListener` without a corresponding `removeEventListener` in the same scope
- Handler function created inline (as arrow function) — cannot be removed by reference later
- `addEventListener` inside a function that runs on render/update

```
// BAD — inline arrow function cannot be removed
FUNCTION setup():
    element.addEventListener('keydown', (e) => handleKey(e))
    // removeEventListener requires the exact same function reference
    // anonymous arrow function → impossible to remove

// BAD — added on each update
FUNCTION onUpdate():
    document.addEventListener('click', outsideClickHandler)
    // called each time block updates → N listeners after N updates

// GOOD — named reference, single registration with guard
handlerRef = null

FUNCTION setup():
    IF handlerRef IS NOT null: RETURN   // guard against double-registration

    handlerRef = (e) => handleKey(e)
    element.addEventListener('keydown', handlerRef)

FUNCTION teardown():
    IF handlerRef IS NOT null:
        element.removeEventListener('keydown', handlerRef)
        handlerRef = null
```

**Agent instruction**: Search for `addEventListener`. For each call:
1. Check if the second argument is an inline arrow function — flag it
2. Check if there is a `removeEventListener` call with the same event name and handler reference
3. Check if the `addEventListener` is inside a function called on render/update — flag if no guard

---

### 4.E — Closure overcapture

**Category**: Reference trapping  
**Impact**: Medium — depends on size of captured object

**Smell indicators**:
- Callback / handler created inside a function that receives a large object as parameter
- Callback only uses one or two fields from a large config/state object
- Large object passed into `setTimeout`, `setInterval`, or event handler callback

```
// BAD — callback captures entire large object
FUNCTION registerBlock(blockConfig):
    // blockConfig might be 100KB of settings
    handler = () => {
        log(blockConfig.id)   // only needs .id but captures everything
    }
    element.addEventListener('change', handler)
    // blockConfig cannot be GC'd while handler is alive

// GOOD — extract only what's needed before creating callback
FUNCTION registerBlock(blockConfig):
    blockId = blockConfig.id   // extract primitive
    handler = () => {
        log(blockId)   // captures only the string, not the object
    }
    element.addEventListener('change', handler)
    // blockConfig can be GC'd once this function returns
```

---

### 4.F — Detached DOM reference

**Category**: Reference trapping  
**Impact**: Medium — each detached node retains its full subtree

**Smell indicators**:
- DOM query result (`.querySelector`, `.getElementById`) stored in a module-level or long-lived variable
- DOM nodes stored in a Map, cache, or object at module scope
- References not nulled out when the corresponding block/component unmounts

```
// BAD — DOM ref stored in module-level cache
MODULE sliderCache = {}

FUNCTION initBlock(blockId, container):
    slider = new Swiper(container)
    sliderCache[blockId] = {
        swiper: slider,
        el: container    // DOM node stored here
    }
    // When block unmounts and container is removed from DOM:
    // sliderCache still holds container → "Detached HTMLElement" in heap snapshot

// GOOD — clean up cache entry on unmount
FUNCTION destroyBlock(blockId):
    IF sliderCache[blockId] EXISTS:
        sliderCache[blockId].swiper.destroy(true, true)
        sliderCache[blockId] = null
        DELETE sliderCache[blockId]
```

---

### 4.G — Unbounded collection

**Category**: Collection growth  
**Impact**: Low-Medium — grows slowly but never shrinks

**Smell indicators**:
- `push()`, `.set()`, or assignment to array/map/object at module scope
- No corresponding `delete`, `splice`, `.clear()`, or size limit check nearby
- Key generated from dynamic data (IDs, timestamps, event names)

```
// BAD — history array grows forever
MODULE editHistory = []

FUNCTION recordEdit(change):
    editHistory.push(change)
    // Never trimmed → grows for the entire browser session

// GOOD — bounded collection
MAX_HISTORY = 50

FUNCTION recordEdit(change):
    editHistory.push(change)
    IF editHistory.length > MAX_HISTORY:
        editHistory.splice(0, editHistory.length - MAX_HISTORY)

// BAD — cache with no eviction
blockMetaCache = new Map()

FUNCTION getBlockMeta(blockId):
    IF NOT blockMetaCache.has(blockId):
        blockMetaCache.set(blockId, computeMeta(blockId))
    RETURN blockMetaCache.get(blockId)
    // Cache grows with every unique blockId seen in the session

// GOOD — use WeakMap when key is an object (auto-evicts with key)
blockMetaCache = new WeakMap()   // key = DOM node or block object, auto-GC'd
```

---

### 4.H — Uncleared timer / interval

**Category**: Timing & async  
**Impact**: Medium — interval keeps closure alive, keeps firing after component gone

**Smell indicators**:
- `setInterval` or `setTimeout` return value not stored
- Timer setup inside a component/block init with no corresponding clear in unmount
- `setInterval` that references `this` or a DOM node inside its callback

```
// BAD — interval ID not stored, cannot be cleared
FUNCTION startSync():
    setInterval(() => {
        syncBlockData(this.blockRef)   // this and blockRef kept alive by closure
    }, 5000)

// GOOD — store ID, clear on unmount
syncIntervalId = null

FUNCTION startSync():
    IF syncIntervalId IS NOT null: RETURN   // guard

    syncIntervalId = setInterval(() => {
        syncBlockData(blockRef)
    }, 5000)

FUNCTION onUnmount():
    IF syncIntervalId IS NOT null:
        clearInterval(syncIntervalId)
        syncIntervalId = null
```

---

## 5. Fix priority for this plugin

Based on the observed symptoms (slow load + sidebar freeze), fix in this order:

### Step 1 — Audit all wp.data.subscribe calls (pattern 4.A)

```
Search: grep -r "wp.data.subscribe" src/
For each result:
  - Is the return value stored? If not → fix immediately
  - Is there a value-change guard inside the callback? If not → add one
  - Is unsubscribe() called in useEffect return or unmount? If not → add it
```

### Step 2 — Audit all Swiper instantiations (pattern 4.B)

```
Search: grep -r "new Swiper" src/
For each result:
  - Is the instance stored in a ref or variable?
  - Is destroy(true, true) called before re-init?
  - Is destroy(true, true) called in cleanup/unmount?
```

### Step 3 — Audit useEffect with side effects (pattern 4.C)

```
Search: grep -r "useEffect" src/ (then inspect each)
Flag any useEffect that:
  - Contains subscribe / observe / addEventListener / setInterval
  - Does NOT have a return () => { ... } at the end
  - Has async as the callback function
```

### Step 4 — Run Heap Snapshot comparison

After fixing steps 1-3, run Heap Snapshot comparison again.
If "Detached HTMLElement" or "(closure)" still grows → investigate patterns 4.E and 4.F.

---

## 6. Code smell — quick reference checklist

Use this checklist when reviewing any file in the plugin:

```
LIFECYCLE
[ ] Every new X() has a matching X.destroy() / X.disconnect() in cleanup
[ ] Every subscribe() stores its return value and calls it on unmount
[ ] Every addEventListener has a matching removeEventListener with same reference
[ ] Every useEffect with side-effects has a return cleanup function
[ ] setInterval / setTimeout IDs are stored and cleared on unmount

REFERENCES
[ ] No DOM nodes stored in module-level variables or Maps
[ ] Callback functions extract only needed primitives (not whole objects)
[ ] Module-level Maps / objects have delete / cleanup logic when entries are removed

COLLECTIONS
[ ] No array/map/object at module scope that only gets added to
[ ] Any cache has a max size or eviction policy
[ ] WeakMap used instead of Map when keys are DOM nodes or objects

GUARDS
[ ] Subscription / listener setup functions check "already registered?" before adding
[ ] Event handlers are named references, not inline arrow functions
[ ] wp.data.subscribe callbacks compare old vs new value before updating UI
```

---

## 7. Glossary (for agent context)

| Term | Meaning |
|---|---|
| `wp.data.subscribe` | WordPress data store subscription. Returns an unsubscribe function. Must be called on cleanup. |
| Swiper | JavaScript slider library. Instances must be destroyed with `.destroy(true, true)` on unmount. |
| `useEffect` cleanup | The function returned from a `useEffect` callback. React calls it before re-running the effect and on unmount. |
| Retained size | In heap snapshots: the memory that would be freed if this object were GC'd, including all objects it exclusively holds. |
| Detached HTMLElement | A DOM node that has been removed from the document tree but is still referenced by JavaScript, preventing GC. |
| Gutenberg sidebar | The "Inspector Controls" panel on the right side of the block editor. Rendered via WordPress `InspectorControls` SlotFill. |
| Long Task | A browser task taking > 50ms. Blocks the main thread, causing UI freeze. Visible in Chrome Performance tab. |

---

*Generated from debugging session. Last updated: 2026-06-16.*  
*Covers: WordPress Gutenberg plugin, Swiper.js, TypeScript, React hooks, wp.data store.*
