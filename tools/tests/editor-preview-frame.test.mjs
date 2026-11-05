// Actual editor spacing hook, simulated rendered geometry/lifecycle.
// Node >=22.13; no Gutenberg build, React DOM or browser geometry claim.
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { stripTypeScriptTypes } from 'node:module';
import vm from 'node:vm';
import test from 'node:test';

const source = readFileSync(new URL('../../wp-content/plugins/skvn-marine-blocks/src/shared/editor-preview-frame.ts', import.meta.url), 'utf8');
const executable = stripTypeScriptTypes(source.replace(/^import .*;\r?\n/gm, '').replace('export function', 'function')) + '\nglobalThis.hook = useEditorPreviewFrame;';

function fixture({ gap = 0, previousMargin = 0, baseline = 0, collapsed = false, sideBySide = false, original = '' } = {}) {
  const state = { gap, sideBySide, frame: true };
  const pending = new Map(), listeners = new Set(), resizeObservers = [], mutationObservers = [];
  let frameId = 0, cleanup, effect;
  const style = { marginBlockStart: original };
  const previous = {
    classList: { contains: () => state.frame },
    getBoundingClientRect: () => ({ left: 0, right: 300, top: 0, bottom: 100 }),
  };
  const parent = {};
  const element = {
    style, parentElement: parent, previousElementSibling: previous,
    getBoundingClientRect: () => {
      const margin = style.marginBlockStart ? parseFloat(style.marginBlockStart) : baseline;
      const actualGap = state.gap + (collapsed ? Math.max(previousMargin, margin) : previousMargin + margin);
      return { left: state.sideBySide ? 320 : 0, right: state.sideBySide ? 620 : 300, top: state.sideBySide ? 0 : 100 + actualGap, bottom: 400 + actualGap };
    },
  };
  const view = {
    getComputedStyle: () => ({ marginBlockStart: `${baseline}px` }),
    requestAnimationFrame: (fn) => { pending.set(++frameId, fn); return frameId; },
    cancelAnimationFrame: (id) => pending.delete(id),
    addEventListener: (_, fn) => listeners.add(fn),
    removeEventListener: (_, fn) => listeners.delete(fn),
    ResizeObserver: class {
      targets = new Set();
      constructor(fn) { this.fn = fn; resizeObservers.push(this); }
      observe(target) { this.targets.add(target); }
      disconnect() { this.targets.clear(); }
    },
    MutationObserver: class {
      active = false;
      constructor(fn) { this.fn = fn; mutationObservers.push(this); }
      observe() { this.active = true; }
      disconnect() { this.active = false; }
    },
  };
  element.ownerDocument = { defaultView: view };
  const context = vm.createContext({ useRef: () => ({ current: element }), useEffect: (fn) => { effect = fn; } });
  vm.runInContext(executable, context);
  context.hook();
  cleanup = effect();
  const flush = () => {
    let passes = 0;
    while (pending.size) {
      assert.ok(++passes < 15, 'measurement must converge rather than spin');
      const batch = [...pending.values()]; pending.clear(); batch.forEach((fn) => fn());
    }
  };
  return {
    element, state, style, flush, cleanup, pending, listeners, resizeObservers, mutationObservers,
    trigger: () => resizeObservers[0].fn(),
    mutate: () => mutationObservers[0].fn(),
    actualGap: () => element.getBoundingClientRect().top - previous.getBoundingClientRect().bottom,
  };
}

for (const gap of [0, 8, 24]) {
  test(`existing parent gap ${gap}px receives only missing spacing`, () => {
    const f = fixture({ gap }); f.flush();
    assert.equal(f.actualGap(), Math.max(10, gap));
    assert.equal(parseFloat(f.style.marginBlockStart || '0'), Math.max(0, 10 - gap));
    f.cleanup();
  });
}

test('collapsed margins converge to a real 10px gap', () => {
  const f = fixture({ collapsed: true, previousMargin: 5 }); f.flush();
  assert.equal(f.actualGap(), 10); f.cleanup();
});

test('parent gap changes remove earlier correction instead of doubling it', () => {
  const f = fixture(); f.flush(); assert.equal(f.actualGap(), 10);
  f.state.gap = 24; f.trigger(); f.flush();
  assert.equal(f.style.marginBlockStart, ''); assert.equal(f.actualGap(), 24);
  f.state.gap = 8; f.trigger(); f.flush();
  assert.equal(f.actualGap(), 10); f.cleanup();
});

test('side-by-side frames are not vertically offset', () => {
  const f = fixture({ sideBySide: true }); f.flush();
  assert.equal(f.style.marginBlockStart, ''); f.cleanup();
});

test('reorder or removal restores original margin and observation target', () => {
  const f = fixture(); f.flush();
  f.element.previousElementSibling = null; f.mutate(); f.flush();
  assert.equal(f.style.marginBlockStart, '');
  assert.equal(f.resizeObservers[0].targets.size, 2);
  f.cleanup();
});

test('cleanup cancels pending work, observers and listener, restoring inline margin', () => {
  const f = fixture({ baseline: 3, original: '3px' }); f.flush();
  assert.equal(f.actualGap(), 10);
  f.trigger(); assert.equal(f.pending.size, 1);
  f.cleanup();
  assert.equal(f.style.marginBlockStart, '3px');
  assert.equal(f.pending.size, 0); assert.equal(f.listeners.size, 0);
  assert.equal(f.resizeObservers[0].targets.size, 0);
  assert.equal(f.mutationObservers[0].active, false);
});
