# Memory Leak Prevention Rules

<!-- AGENT DIRECTIVE
This is a PRESCRIPTIVE ruleset, not a guide. Every rule is MANDATORY unless marked [SHOULD].
When writing or reviewing code for this Gutenberg plugin, enforce every [MUST] rule.
When you generate code that sets up a resource, you MUST generate its teardown in the same edit.
If you cannot satisfy a rule, STOP and flag it explicitly rather than producing leaky code.
-->

These rules exist so that memory leaks are prevented at write-time, not discovered at debug-time. They apply equally to human developers and AI agents.

Severity levels:
- `[MUST]` — blocking. Code violating this cannot be merged.
- `[SHOULD]` — strong default. Deviation requires a one-line comment explaining why.

---

## Rule 0 — The Pairing Principle (the one rule that prevents most leaks)

`[MUST]` Every resource acquisition MUST be written together with its release, in the same commit, in the same logical unit.

If you write any of the **left** column, you MUST also write the **right** column before moving on:

| Acquisition | Required release |
|---|---|
| `new Swiper(...)` | `.destroy(true, true)` |
| `wp.data.subscribe(...)` | call the returned unsubscribe fn |
| `addEventListener(...)` | `removeEventListener(...)` (same ref) |
| `new IntersectionObserver(...)` / `MutationObserver` / `ResizeObserver` | `.disconnect()` |
| `setInterval(...)` / `setTimeout(...)` | `clearInterval` / `clearTimeout` |
| `requestAnimationFrame(...)` | `cancelAnimationFrame(...)` |
| `useEffect(setup)` with side effects | `return () => cleanup` |
| `store.set(key, value)` at module scope | a `delete` / eviction path |

**Workflow rule**: write the teardown line *first* (as a stub), then write the setup. This guarantees you never forget it.

```
// Write in THIS order:
FUNCTION onUnmount(): cleanup()    // 1. stub the teardown first
FUNCTION onMount():  setup()       // 2. then write the setup
```

---

## Rule 1 — Resource ownership

`[MUST]` Every resource (Swiper instance, subscription, observer, timer) MUST have exactly one owner — a single variable or ref that holds it.

`[MUST]` A resource MUST NOT be created without storing its handle. A bare `new Swiper(el)` or `setInterval(fn, 1000)` with no assignment is forbidden.

```
// FORBIDDEN
new Swiper(container)
setInterval(sync, 1000)

// REQUIRED
swiperRef = new Swiper(container)
syncTimerRef = setInterval(sync, 1000)
```

`[MUST]` Before creating a new instance on an element that may already have one, destroy the old instance first.

```
// REQUIRED — idempotent init
FUNCTION init(container):
    IF swiperRef IS NOT null:
        swiperRef.destroy(true, true)
        swiperRef = null
    swiperRef = new Swiper(container, options)
```

---

## Rule 2 — Subscriptions and wp.data

`[MUST]` Every `wp.data.subscribe()` return value MUST be stored and called on cleanup.

`[MUST]` Every subscribe callback MUST contain a change-guard: compare the new value to the previous value and return early if unchanged. This prevents render storms.

```
// REQUIRED pattern
prev = null
unsub = wp.data.subscribe(() => {
    next = wp.data.select(STORE).getValue()
    IF next === prev: RETURN        // change-guard — mandatory
    prev = next
    react(next)
})
// ...later...
unsub()
```

`[SHOULD]` Prefer `wp.data.useSelect()` over manual `subscribe()` inside React components. `useSelect` handles subscription lifecycle automatically.

```
// PREFERRED in React components
value = useSelect(select => select(STORE).getValue(), [])
// No manual subscribe / unsubscribe needed
```

---

## Rule 3 — Event listeners

`[MUST]` Handlers passed to `addEventListener` MUST be named references (variable or class method), never inline arrow functions. Inline functions cannot be removed.

```
// FORBIDDEN — cannot be removed
element.addEventListener('click', () => handle())

// REQUIRED — named reference
clickHandler = (e) => handle(e)
element.addEventListener('click', clickHandler)
element.removeEventListener('click', clickHandler)
```

`[SHOULD]` Prefer `AbortController` for grouped listener cleanup — one signal removes all listeners at once.

```
// PREFERRED — clean teardown of many listeners
controller = new AbortController()
el.addEventListener('click', onClick, { signal: controller.signal })
el.addEventListener('keydown', onKey, { signal: controller.signal })
window.addEventListener('resize', onResize, { signal: controller.signal })
// teardown: one call removes all three
controller.abort()
```

---

## Rule 4 — useEffect discipline

`[MUST]` Any `useEffect` that calls `subscribe`, `observe`, `addEventListener`, `setInterval`, `setTimeout`, or creates an instance MUST return a cleanup function.

`[MUST]` The `useEffect` callback MUST NOT be declared `async`. An async callback returns a Promise, which React cannot use as cleanup.

```
// FORBIDDEN — async effect, return is a Promise
useEffect(async () => {
    sub = await subscribe()
    return sub
}, [])

// REQUIRED — sync effect, cancellation flag for async work
useEffect(() => {
    let cancelled = false
    let sub = null
    subscribe().then(s => { IF NOT cancelled: sub = s })
    RETURN () => {
        cancelled = true
        sub?.unsubscribe()
    }
}, [])
```

`[SHOULD]` Keep dependency arrays accurate. A missing dependency causes stale closures; an over-broad one causes excessive re-subscription. Use the ESLint `react-hooks/exhaustive-deps` rule.

---

## Rule 5 — References and closures

`[MUST]` DOM nodes MUST NOT be stored in module-level variables, module-level `Map`/`Set`/object, or any structure that outlives the node.

`[MUST]` When a structure must reference a DOM node or component instance keyed by it, use `WeakMap` / `WeakSet` so the entry is GC'd automatically when the key dies.

```
// FORBIDDEN — strong ref keeps detached node alive
cache = {}
cache[id] = domNode

// REQUIRED — WeakMap auto-evicts
cache = new WeakMap()
cache.set(domNode, metadata)   // entry dies when domNode is GC'd
```

`[SHOULD]` When creating a callback, capture only the primitives it needs — not the whole config/state object.

```
// AVOID — captures entire large object
handler = () => use(bigConfig.id)

// PREFER — extract primitive first
id = bigConfig.id
handler = () => use(id)
```

---

## Rule 6 — Collections

`[MUST]` No module-scope array, `Map`, `Set`, or object may grow without a bound. Every such collection MUST have one of: a max size with eviction, a `clear()` on a lifecycle boundary, or `WeakMap`/`WeakSet` semantics.

```
// REQUIRED — bounded
MAX = 50
history.push(item)
IF history.length > MAX: history.splice(0, history.length - MAX)
```

`[MUST]` Keys derived from dynamic data (IDs, timestamps, event names) in a long-lived `Map` are forbidden without eviction — they grow unbounded over a session.

---

## Rule 7 — Block lifecycle (Gutenberg-specific)

`[MUST]` Every custom block's `edit` component that sets up frontend resources (Swiper, observers, listeners) MUST tear them down. In React blocks, use `useEffect` cleanup. In vanilla TS, hook into block removal.

`[MUST]` Core block extensions registered via `addFilter` MUST be registered once at module load, never inside a render or component body. Re-registering filters accumulates them.

```
// FORBIDDEN — filter registered on each render
FUNCTION MyComponent():
    addFilter('blocks.registerBlockType', 'my/ns', fn)   // accumulates
    RETURN <div/>

// REQUIRED — register once at module top level
addFilter('blocks.registerBlockType', 'my/ns', fn)
FUNCTION MyComponent():
    RETURN <div/>
```

`[SHOULD]` SlotFill providers (custom sidebar panels) should store any subscription/listener at component level and clean it in `useEffect` return.

---

## Rule 8 — TypeScript configuration

`[MUST]` `tsconfig.json` MUST enable:

```json
{
  "compilerOptions": {
    "strict": true,
    "strictNullChecks": true,
    "sourceMap": true
  }
}
```

`strictNullChecks` forces handling of the `null` states that cleanup code depends on. `sourceMap` makes heap snapshots point to real `.ts` lines.

`[SHOULD]` Type cleanup functions explicitly so the compiler catches missing returns.

```
type Cleanup = () => void
FUNCTION setupSlider(el: HTMLElement): Cleanup {
    swiper = new Swiper(el)
    RETURN () => swiper.destroy(true, true)   // compiler enforces a Cleanup is returned
}
```

---

## Rule 9 — Automated enforcement

`[MUST]` The following ESLint rules MUST be enabled in the plugin's lint config:

```
react-hooks/exhaustive-deps      → catches stale-closure dependency bugs
react-hooks/rules-of-hooks       → catches conditional hook usage
no-restricted-syntax              → custom rule to flag bare new Swiper / setInterval
```

`[SHOULD]` Add a custom ESLint rule or grep-based CI check that flags:

```
- "new Swiper" not followed by assignment
- "addEventListener" with an arrow function as 2nd arg
- "wp.data.subscribe" whose return value is discarded
- "setInterval" / "setTimeout" return value discarded
- "useEffect(async"
```

`[SHOULD]` Add a CI step that runs the editor under a headless browser, performs N edit cycles, and fails if heap retained-size grows beyond a threshold.

---

## Rule 10 — Pull request gate

`[MUST]` No PR touching frontend TS, blocks, or Swiper code merges until the author confirms the checklist below. Reviewers MUST verify each item.

```
PR CHECKLIST — memory safety
[ ] Every new instance / subscription / listener / timer added has a matching teardown
[ ] All teardowns are reachable on unmount (useEffect return, or block removal hook)
[ ] No inline arrow functions passed to addEventListener
[ ] All wp.data.subscribe callbacks have a change-guard
[ ] No DOM nodes stored in module-scope or non-Weak collections
[ ] No unbounded module-scope collections introduced
[ ] addFilter / registerBlockType calls are at module top level, not in render
[ ] No async useEffect callbacks
```

---

## Quick wrapper helpers (recommended infrastructure)

`[SHOULD]` Provide these small utilities so the safe path is the easy path. Devs and agents should reach for these instead of raw APIs.

```
// A disposer that collects teardowns and runs them all at once
FUNCTION createDisposer():
    teardowns = []
    RETURN {
        add: (fn) => teardowns.push(fn),
        dispose: () => {
            FOR each fn in teardowns REVERSED: fn()
            teardowns = []
        }
    }

// Usage — one dispose() cleans everything
FUNCTION setupBlock(container):
    d = createDisposer()

    swiper = new Swiper(container)
    d.add(() => swiper.destroy(true, true))

    unsub = wp.data.subscribe(onChange)
    d.add(() => unsub())

    handler = (e) => onKey(e)
    window.addEventListener('keydown', handler)
    d.add(() => window.removeEventListener('keydown', handler))

    RETURN d.dispose   // single function tears down all three
```

This pattern makes Rule 0 trivial to follow: every `d.add()` sits right next to its acquisition, and a single `dispose()` guarantees nothing is forgotten.

---

## Summary — the five habits

1. **Write teardown before setup** — stub the cleanup line first.
2. **Store every handle** — never a bare `new X()` or `setInterval()`.
3. **Name every listener** — no inline arrows in `addEventListener`.
4. **Guard every subscribe** — compare old vs new before reacting.
5. **Bound every collection** — max size, `clear()`, or `WeakMap`.

If every contributor (human or agent) follows these five, the leak classes in the debug guide essentially cannot occur.

---

*Companion to: gutenberg-plugin-memory-leak-guide.md*  
*Last updated: 2026-06-16. Scope: WordPress Gutenberg plugin, Swiper.js, TypeScript, React hooks, wp.data.*
