// Executes the actual transport/hook without a Gutenberg build or DOM renderer.
// Node >=22.13 is needed for native TypeScript type stripping (no emitted assets).
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { stripTypeScriptTypes } from 'node:module';
import vm from 'node:vm';
import test from 'node:test';

const source = readFileSync(new URL('../../wp-content/plugins/skvn-marine-blocks/src/design-sidebar/theme-design.tsx', import.meta.url), 'utf8');
const hook = source.slice(source.indexOf('type Group ='), source.indexOf('const labels:')).replace('export function useThemeDesign', 'function useThemeDesign');
const executable = stripTypeScriptTypes(hook) + '\nglobalThis.getModel = useThemeDesign;';
const value = (color) => ({ primary: color });
const snapshot = () => ({ available: true, incompatible: false, settingsUrl: '', groups: {
  palette: { value: value('#111111'), revision: 'palette-base', canEdit: true },
  heading: { value: { h1: { size: '3rem', weight: '800' } }, revision: 'heading-base', canEdit: true },
  fonts: { value: { preset: 'instrument', scope: 'h1-h3' }, revision: 'fonts-base', canEdit: true },
} });
const ok = (data) => ({ ok: true, status: 200, json: async () => ({ success: true, data }) });
const fail = (data, status = 403) => ({ ok: false, status, json: async () => ({ success: false, data }) });
const copy = (value) => JSON.parse(JSON.stringify(value));

function harness() {
  const cells = [], effects = [], queue = [], calls = [];
  const listeners = new Set();
  let cursor = 0, dirty = true, active = false, model;
  const useState = (initial) => {
    const i = cursor++;
    if (!(i in cells)) cells[i] = initial;
    return [cells[i], (next) => { cells[i] = typeof next === 'function' ? next(cells[i]) : next; dirty = true; }];
  };
  const context = vm.createContext({
    AbortController, URLSearchParams, __: (text) => text, useState,
    useRef: (initial) => { const i = cursor++; return cells[i] ||= { current: initial }; },
    useEffect: (run, dependencies) => {
      const i = cursor++;
      if (!cells[i] || dependencies.some((item, n) => item !== cells[i].dependencies[n])) {
        effects.push(() => { cells[i]?.cleanup?.(); cells[i] = { dependencies, cleanup: run() }; });
      }
    },
    window: { skvnMarineDesign: { url: '/ajax', nonce: 'old' }, addEventListener: (_, fn) => listeners.add(fn), removeEventListener: (_, fn) => listeners.delete(fn) },
    fetch: async (_, options) => {
      calls.push(Object.fromEntries(options.body));
      assert.ok(queue.length, 'unexpected request');
      const next = queue.shift();
      if (next instanceof Error) throw next;
      return typeof next === 'function' ? next(options) : next;
    },
  });
  vm.runInContext(executable, context);
  const flush = async () => {
    for (let i = 0; i < 25; i++) {
      if (dirty) { dirty = false; cursor = 0; model = context.getModel(active); while (effects.length) effects.shift()(); }
      await Promise.resolve();
    }
  };
  return { queue, calls, listeners, context, flush, get model() { return model; },
    activate: async (next) => { active = next; dirty = true; await flush(); },
    unmount: () => { for (const cell of cells) cell?.cleanup?.(); },
  };
}

test('lazy read, close/reopen preserves dirty draft and one unload listener', async () => {
  const h = harness(); await h.activate(false); assert.equal(h.calls.length, 0);
  h.queue.push(ok(snapshot())); await h.activate(true); assert.equal(h.calls.length, 1);
  h.model.change('palette', value('#222222')); await h.flush();
  for (let i = 0; i < 10; i++) { await h.activate(false); await h.activate(true); }
  assert.equal(h.calls.length, 1); assert.equal(h.model.drafts.palette.primary, '#222222');
  assert.equal(h.listeners.size, 1); h.unmount(); assert.equal(h.listeners.size, 0);
});

test('expired nonce keeps all drafts; refresh preserves revisions and requires explicit Save', async () => {
  const h = harness(); h.queue.push(ok(snapshot())); await h.activate(true);
  h.model.change('palette', value('#222222')); h.model.change('fonts', { preset: 'other', scope: 'all' }); await h.flush();
  h.queue.push(fail({ code: 'expired_session', message: 'expired' })); await h.model.save('palette'); await h.flush();
  assert.equal(h.model.snapshot.groups.palette.canEdit, true); assert.equal(h.model.sessionError, 'expired');
  const before = copy(h.model.drafts);
  const current = snapshot(); current.groups.palette.revision = 'external-change';
  h.queue.push(ok({ nonce: 'fresh' }), ok(current)); await h.model.refreshSession(); await h.flush();
  assert.deepEqual(copy(h.model.drafts), before);
  assert.equal(h.model.snapshot.groups.palette.revision, 'palette-base');
  assert.equal(h.calls.filter((call) => call.operation === 'save').length, 1);
  assert.equal(h.context.window.skvnMarineDesign.nonce, 'fresh');
  h.queue.push(fail({ code: 'conflict', message: 'stale', current: current.groups.palette, canEdit: true }, 409));
  await h.model.save('palette'); await h.flush();
  assert.equal(h.model.conflicts.palette.revision, 'external-change');
  assert.equal(h.calls.at(-1).revision, 'palette-base');
  assert.equal(h.model.drafts.fonts.preset, 'other');
});

for (const body of [-1, 0, null]) test(`unstructured 403 body ${body} does not revoke permissions`, async () => {
  const h = harness(); h.queue.push(ok(snapshot())); await h.activate(true);
  h.model.change('palette', value('#222222')); await h.flush();
  h.queue.push({ ok: false, status: 403, json: async () => body }); await h.model.save('palette'); await h.flush();
  assert.equal(h.model.snapshot.groups.palette.canEdit, true); assert.ok(h.model.sessionError);
  assert.equal(h.model.drafts.palette.primary, '#222222');
});

test('structured denied permission locks only its group', async () => {
  const h = harness(); h.queue.push(ok(snapshot())); await h.activate(true);
  h.queue.push(fail({ code: 'unavailable', message: 'denied', canEdit: false })); await h.model.save('palette'); await h.flush();
  assert.equal(h.model.snapshot.groups.palette.canEdit, false);
  assert.equal(h.model.snapshot.groups.fonts.canEdit, true); assert.equal(h.model.sessionError, '');
});

test('initial read error has manual retry; successful retry clears session error', async () => {
  const h = harness(); h.queue.push({ ok: false, status: 403, json: async () => { throw Error('HTML'); } }); await h.activate(true);
  assert.ok(h.model.error); assert.equal(h.calls.length, 1);
  h.queue.push(ok(snapshot())); h.model.retryRead(); await h.flush();
  assert.ok(h.model.snapshot); assert.equal(h.model.error, ''); assert.equal(h.model.sessionError, '');
});

test('late initial read after unmount cannot attach draft unload listener', async () => {
  const h = harness(); let finish, signal;
  h.queue.push((options) => { signal = options.signal; return new Promise((resolve) => { finish = resolve; }); });
  await h.activate(true); h.unmount(); assert.equal(signal.aborted, true);
  finish(ok(snapshot())); await h.flush(); assert.equal(h.model.snapshot, null); assert.equal(h.listeners.size, 0);
});

test('closing panel during initial read keeps request alive and accepts result once', async () => {
  const h = harness(); let finish, signal;
  h.queue.push((options) => { signal = options.signal; return new Promise((resolve) => { finish = resolve; }); });
  await h.activate(true); await h.activate(false); assert.equal(signal.aborted, false);
  finish(ok(snapshot())); await h.flush(); await h.activate(true);
  assert.ok(h.model.snapshot); assert.equal(h.calls.length, 1);
});

test('failed session recovery preserves drafts, conflict and old revision', async () => {
  const h = harness(); h.queue.push(ok(snapshot())); await h.activate(true);
  h.model.change('palette', value('#222222')); await h.flush();
  const current = snapshot().groups.palette; current.revision = 'new';
  h.queue.push(fail({ code: 'conflict', message: 'conflict', current }, 409)); await h.model.save('palette'); await h.flush();
  h.queue.push({ ok: false, status: 400, json: async () => 0 }); await h.model.refreshSession(); await h.flush();
  assert.equal(h.model.drafts.palette.primary, '#222222');
  assert.equal(h.model.snapshot.groups.palette.revision, 'palette-base');
  assert.equal(h.model.conflicts.palette.revision, 'new');
  assert.equal(h.model.snapshot.groups.palette.canEdit, true);
  assert.ok(h.model.sessionError);
});

test('session refresh after initial expiry initializes once without another read', async () => {
  const h = harness(); h.queue.push(fail({ code: 'expired_session', message: 'expired' })); await h.activate(true);
  h.queue.push(ok({ nonce: 'fresh' }), ok(snapshot())); await h.model.refreshSession(); await h.flush();
  assert.equal(h.calls.length, 3); assert.equal(h.model.sessionError, '');
  assert.equal(h.model.readPending, false); assert.equal(h.model.hasDraft, false);
});

test('editor context gate never touches edit-post outside post editor', () => {
  const navigation = readFileSync(new URL('../../wp-content/plugins/skvn-marine-blocks/src/design-sidebar/navigation.tsx', import.meta.url), 'utf8');
  const guard = navigation.slice(navigation.indexOf('export function canUseDesignSidebar'), navigation.indexOf('export const designGroups')).replace('export function', 'function');
  let accesses = 0;
  const context = vm.createContext({ window: { skvnMarineDesign: { postEditor: false } }, select: () => { accesses++; return {}; }, dispatch: () => { accesses++; return {}; } });
  vm.runInContext(guard + '\nglobalThis.check = canUseDesignSidebar;', context);
  assert.equal(context.check(), false); assert.equal(accesses, 0);
  context.window.skvnMarineDesign.postEditor = true;
  assert.equal(context.check(), false);
  context.select = () => ({ getActiveGeneralSidebarName() {} });
  context.dispatch = () => ({ openGeneralSidebar() {} });
  assert.equal(context.check(), true);
});
