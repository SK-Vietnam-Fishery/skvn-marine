import { readFileSync } from 'node:fs';
import vm from 'node:vm';
import assert from 'node:assert/strict';
import test from 'node:test';

// Execute the actual inline lifecycle, with a deterministic clock and DOM events.
// This verifies recovery/state transitions; geometry/paint remain onsite checks.
const php = readFileSync(new URL('../../wp-content/themes/skvn-marine/inc/preloader.php', import.meta.url), 'utf8');
const source = php.split('<script id="skvn-curtain-bootstrap">')[1].split('</script>')[0];

function fixture({ once = false, seen = false, storageError = false, reduced = false, heroes = 1, ready = false, width = 100 } = {}) {
  let now = 0, sequence = 0, released = 0, removed = false, reads = 0;
  const timers = new Map(), attrs = new Map(), listeners = new Map(), warnings = [];
  const listen = (type, callback) => {
    if (!listeners.has(type)) listeners.set(type, new Set());
    listeners.get(type).add(callback);
  };
  const unlisten = (type, callback) => listeners.get(type)?.delete(callback);
  const emit = (type, data = {}) => {
    for (const callback of [...(listeners.get(type) ?? [])]) callback({ type, ...data });
  };
  const box = () => ({ width, height: 400 });
  const hero = { dataset: { skvnSliderReady: ready ? 'true' : 'false' }, getBoundingClientRect: box };
  const panel = { remove() { removed = true; }, getBoundingClientRect() { reads++; return box(); } };
  const motion = { matches: reduced, addEventListener: listen, removeEventListener: unlisten };
  const root = {
    setAttribute: (key, value) => attrs.set(key, value),
    removeAttribute: key => attrs.delete(key),
    hasAttribute: key => attrs.has(key),
  };
  vm.runInNewContext(source.replace(/<\?php[\s\S]*?\?>/g, JSON.stringify(once)), {
    document: {
      documentElement: root, getElementById: () => removed ? null : panel,
      querySelectorAll: () => Array.from({ length: heroes }, () => hero),
      addEventListener: listen, removeEventListener: unlisten,
    },
    window: {
      matchMedia: () => motion, addEventListener: listen,
      dispatchEvent(event) { if (event.type === 'skvn:loading-released') released++; emit(event.type); },
    },
    Event: class { constructor(type) { this.type = type; } },
    sessionStorage: {
      getItem() { if (storageError) throw Error('blocked'); return seen ? '1' : null; },
      setItem() { if (storageError) throw Error('blocked'); seen = true; },
    },
    Date: { now: () => now }, console: { warn: value => warnings.push(value) },
    setTimeout(callback, delay) { const id = ++sequence; timers.set(id, { at: now + delay, callback }); return id; },
    clearTimeout: id => timers.delete(id),
  });
  function tick(to) {
    while (true) {
      const next = [...timers].filter(([, timer]) => timer.at <= to).sort((a, b) => a[1].at - b[1].at)[0];
      if (!next) break;
      now = next[1].at; timers.delete(next[0]); next[1].callback();
    }
    now = to;
  }
  return { emit, tick, attrs, hero, panel, motion, warnings, timers,
    get released() { return released; }, get removed() { return removed; }, get reads() { return reads; } };
}

test('main readiness reveals once, ignores child/opacity transitions, removes only after panel transform', () => {
  const f = fixture();
  assert.equal(f.attrs.get('data-skvn-loading'), 'covering');
  f.hero.dataset.skvnSliderReady = 'true';
  f.emit('skvn:slider-ready');
  assert.equal(f.attrs.get('data-skvn-loading'), 'revealing');
  f.emit('transitionend', { target: {}, propertyName: 'transform' });
  f.emit('transitionend', { target: f.panel, propertyName: 'opacity' });
  assert.equal(f.released, 0);
  f.emit('transitionend', { target: f.panel, propertyName: 'transform' });
  f.emit('skvn:slider-ready'); f.tick(5000);
  assert.equal(f.released, 1); assert.equal(f.removed, true);
  assert.equal(f.attrs.size, 0); assert.equal(f.timers.size, 0);
});

test('already-ready main is read without requiring another event', () => {
  const f = fixture({ ready: true });
  assert.equal(f.attrs.get('data-skvn-loading'), 'revealing');
  assert.equal(f.reads, 1);
});

test('bundle absent: deadline begins reveal at 1500, missing transition event still cleans up', () => {
  const f = fixture(); f.tick(1499);
  assert.equal(f.attrs.get('data-skvn-loading'), 'covering');
  f.tick(1500); assert.equal(f.attrs.get('data-skvn-loading'), 'revealing');
  assert.equal(f.warnings.length, 1);
  f.tick(2350); assert.equal(f.released, 1);
  f.hero.dataset.skvnSliderReady = 'true'; f.emit('skvn:slider-ready');
  assert.equal(f.released, 1);
});

test('zero-width or ambiguous main never counts as ready', () => {
  for (const options of [{ ready: true, width: 0 }, { ready: true, heroes: 2 }, { heroes: 0 }]) {
    const f = fixture(options); assert.equal(f.attrs.get('data-skvn-loading'), 'covering');
    f.tick(1500); assert.equal(f.warnings.length, 1);
  }
});

test('once marks activation; seen session, blocked storage and reduced motion bypass cover', () => {
  const first = fixture({ once: true }); assert.equal(first.attrs.get('data-skvn-loading'), 'covering');
  for (const options of [{ once: true, seen: true }, { once: true, storageError: true }, { reduced: true }]) {
    const f = fixture(options); assert.equal(f.attrs.size, 0); assert.equal(f.released, 1);
  }
});

test('reduced-motion change, pagehide and BFCache restore release idempotently', () => {
  for (const type of ['change', 'pagehide', 'pageshow']) {
    const f = fixture(); f.motion.matches = true;
    f.emit(type, { persisted: true }); f.emit(type, { persisted: true }); f.tick(5000);
    assert.equal(f.released, 1); assert.equal(f.attrs.size, 0);
  }
});

test('active cover skips native transition; keyboard block is removed on release', () => {
  const f = fixture(); let skipped = 0, prevented = 0;
  f.emit('pagereveal', { viewTransition: { skipTransition() { skipped++; } } });
  f.emit('keydown', { key: 'Tab', preventDefault() { prevented++; } });
  f.emit('pagehide');
  f.emit('keydown', { key: 'Tab', preventDefault() { prevented++; } });
  assert.equal(skipped, 1); assert.equal(prevented, 1);
});

test('independent watchdog survives an exception in the reveal path', () => {
  const f = fixture(); f.panel.getBoundingClientRect = () => { throw Error('injected'); };
  assert.throws(() => f.tick(1500), /injected/);
  f.tick(2500); assert.equal(f.released, 1); assert.equal(f.attrs.size, 0);
});
