# Init Prompt — V1 / 1.3.8 Core Button Hover Fix

Paste block below into a **new agent session** after human approves implement.
Do not use this prompt inside the long debug thread — context is already spent there.

```markdown
## Context

You are working in:

`D:\Github\skvn-marine`

**Task type:** `CODE_NOW` — implement approved fix plan; do not re-debate architecture.

**Planning source of truth:**

`.context/planning/archives/033_VER_1_3_8_CORE_BUTTON_HOVER_FIX_PLAN.md`

**Decision baseline:**

`docs/decisions/core-control-core-button-hover.md`

**Retrospective / pitfalls:**

- `docs/debug-casebook/core-control/007_BUTTON_HOVER_CSS_VARS_BUILT_NOT_EMITTED.md`
- `docs/debug-casebook/PITFALLS.md` (Pitfall 7 — button hover define + consume)

### Startup protocol (AGENTS.md)

Read in order:

1. `AGENTS.md`
2. `.context/GLOBAL.md`
3. `.context/MILESTONES.md` — note current milestone; **do not self-transition** to 1.3.8 unless human already declared it. This fix may land inside current milestone 1.3.6 if human did not bump milestone yet.
4. `.context/TENSIONS_OPEN.md`
5. `.context/TENSIONS_ACTIVE.md` — tag filter: `blocks`, `editor-governance`
6. `.context/planning/archives/033_VER_1_3_8_CORE_BUTTON_HOVER_FIX_PLAN.md` — **read fully**
7. `docs/decisions/core-control-core-button-hover.md`

Read `.local/ENVIRONMENT.md` before WP-CLI / build. Inspect `git status` and relevant diffs; do not revert unrelated human work.

### What already exists (do not re-implement from scratch)

Core Control + Button Hover **foundation** shipped in 1.3.4:

- Option `skvn_core_controls`, key `button_hover` (not `button_hover_colors` in code)
- Attrs: `skvnHoverTextColor`, `skvnHoverBgColor` on `core/button`
- PHP filter `render_block_core/button` in `modules/core-control/features/button-hover.php`
- Editor: `PanelColorGradientSettings` in `src/core-controls/button-hover/index.tsx`
- Gradient bg sanitize in PHP (linear/radial/conic)

### What is broken (confirmed root causes)

1. **Specificity:** plugin rule `.wp-block-button .wp-block-button__link:hover` (0,2,1) loses to theme `.wp-block-button.skvn-button--primary .wp-block-button__link:hover` (0,3,1) and slider hero hover rules.
2. **No scoping class:** `has-skvn-button-hover` never added on frontend wrapper.
3. **Wrong enqueue boundary:** late-enqueues entire `build/style-index.ts.css` instead of handle `skvn-marine-core-button-hover` with ~15 lines hover CSS.
4. **No editor preview:** `editor.BlockEdit` cannot set wrapper DOM; need `editor.BlockListBlock` + `wrapperProps`.
5. **Tests are weak:** grep PHP source only; do not prove hover works.

### Gutenberg pipeline (invariant — do not confuse layers)

```
post_content (DB only)
  └── <!-- wp:button {"skvnHoverTextColor":"#fff",...} -->
        parse_blocks() → $block['attrs']
          render_block_core/button filter
            HTML: <div class="wp-block-button has-skvn-button-hover" style="--skvn-btn-hover-*">
```

Block comments **never** appear in frontend HTML. Verify attrs via Code editor / WP-CLI, not view-source comment search.

---

## Goal

Implement **atomic fix** from plan 033 so Core Button Hover works on frontend (including `skvn-button--primary`) and editor preview, without regressing the flex-layout fix (no `<style>` inside `wp-block-buttons`).

Deliver:

1. PHP: add `has-skvn-button-hover` + inline CSS vars on `.wp-block-button` wrapper
2. PHP: `wp_register_style` (no file URL) + `wp_add_inline_style` for scoped hover rules; dependency on theme stylesheet loaded **after** theme button rules
3. CSS source: editor-only consume rules in `src/core-controls/button-hover/style.css` (frontend hover CSS lives in PHP inline style per plan)
4. TSX: `editor.BlockListBlock` filter for editor preview vars on wrapper
5. Tests: assert PHP render output from mock `$block_content` + `$block` (class + vars), not grep-only
6. Docs: update CASE-007 status + decision doc status per plan §5

Do **not** change theme or slider CSS files for this task.

---

## Files allowed to change

- `wp-content/plugins/skvn-marine-blocks/modules/core-control/features/button-hover.php`
- `wp-content/plugins/skvn-marine-blocks/src/core-controls/button-hover/index.tsx`
- `wp-content/plugins/skvn-marine-blocks/src/core-controls/button-hover/style.css`
- `tests/core-control-button-hover.test.mjs`
- `docs/debug-casebook/core-control/007_BUTTON_HOVER_CSS_VARS_BUILT_NOT_EMITTED.md`
- `docs/decisions/core-control-core-button-hover.md`

Optional if human approves milestone doc sync:

- `.context/planning/archives/033_VER_1_3_8_CORE_BUTTON_HOVER_FIX_PLAN.md` (status → IMPLEMENTED)

## Files forbidden

- `wp-content/themes/generatepress/**`
- `wp-content/themes/skvn-marine/style.css`
- `wp-content/plugins/skvn-marine-blocks/src/slider/style.css`
- Renaming namespaces, option keys, or attr names

---

## Implementation order (from plan 033 §6)

```
Fix 1 — PHP: has-skvn-button-hover + inline vars (same preg_replace_callback)
Fix 3 — PHP: wp_add_inline_style handle skvn-marine-core-button-hover (same file)
Fix 2 — style.css: editor-only rules; frontend rules in PHP inline
Fix 4 — index.tsx: editor.BlockListBlock wrapperProps
Fix 5 — docs + tests
npm run build (human runs per AGENTS.md — output command text)
```

---

## Technical notes agent must verify (not assume)

### Theme style handle

Plan 033 mentions dependency `skvn-marine-styles`. **Actual theme handle** in `inc/enqueue.php` is:

`skvn-marine-style`

Use the real registered handle so cascade order is correct. If handle missing at late enqueue time, register dependency safely or document fallback.

### Specificity target

Frontend inline CSS must be at least **0,3,1**:

```css
.wp-block-button.has-skvn-button-hover .wp-block-button__link:hover,
.wp-block-button.has-skvn-button-hover .wp-block-button__link:focus-visible {
  color: var(--skvn-btn-hover-text, inherit);
  background: var(--skvn-btn-hover-bg, inherit);
  transition: color 0.15s ease, background 0.15s ease;
}
```

Use `background` not `background-color` (gradient support already in PHP).

### No `<style>` in button markup

Do **not** prepend `<style>` to `render_block_core/button` output — it becomes flex child inside `wp-block-buttons` and caused `margin-left: 192px`.

### Gradient + hex sanitize (keep existing PHP)

- Text: `sanitize_hex_color` only
- Background: hex OR sanitized gradient string (already implemented)

### Editor preview filter

```tsx
addFilter('editor.BlockListBlock', 'skvn-marine/button-hover-wrapper-props', ...)
```

Inject `--skvn-btn-hover-text` / `--skvn-btn-hover-bg` via `wrapperProps.style` when attrs set.
Gate preview with `isCoreControlEnabled('button_hover')` same as Inspector panel.

---

## Tests required

Update `tests/core-control-button-hover.test.mjs`:

**Keep:** no `<style>` prepended to block content string.

**Add (minimum):**

- Assert output HTML contains `has-skvn-button-hover`
- Assert output contains `--skvn-btn-hover-text:` and/or `--skvn-btn-hover-bg:`
- Assert uses `wp_add_inline_style` or equivalent — not `build/style-index.ts.css` URL enqueue
- Prefer extracting render logic testable via mock OR inline PHP test strings matching `preg_replace_callback` behavior

Run:

```bash
node /mnt/d/Github/skvn-marine/tests/core-control-button-hover.test.mjs
```

---

## Acceptance checklist (human onsite)

### Frontend

- [ ] Elements: `<div class="wp-block-button has-skvn-button-hover" style="--skvn-btn-hover-*">`
- [ ] Hover `skvn-button--primary` button — color actually changes
- [ ] DevTools Styles on hovered link — plugin rule **not** struck through
- [ ] Network/DOM: `skvn-marine-core-button-hover` inline block present; **not** loading full `style-index.ts.css` for hover alone
- [ ] Gradient hover background works when set

### Editor

- [ ] Styles tab → "Hover Colors" panel visible when toggle on
- [ ] DevTools in editor iframe: wrapper has inline `--skvn-btn-hover-*` after picking colors

### Toggle

- [ ] Toggle off → no PHP inject, no panel
- [ ] Toggle on → saved values restored
- [ ] Block remains valid when plugin off

### DevTools script (frontend)

```javascript
const w = document.querySelector('.wp-block-button.has-skvn-button-hover');
console.table({
  class: w?.className,
  style: w?.getAttribute('style'),
  textVar: w && getComputedStyle(w).getPropertyValue('--skvn-btn-hover-text').trim(),
  bgVar: w && getComputedStyle(w).getPropertyValue('--skvn-btn-hover-bg').trim(),
});
// Then hover link and confirm winning :hover rule in Styles panel
```

---

## Post-implement commands (output to human — do not auto-run build per AGENTS.md)

```bash
php -l wp-content/plugins/skvn-marine-blocks/modules/core-control/features/button-hover.php

source /home/shinkuro/.nvm/nvm.sh && nvm use 20 && \
cd /mnt/d/Github/skvn-marine/wp-content/plugins/skvn-marine-blocks && \
npm run build && find modules/ -name "*.php" -exec php -l {} \;

node /mnt/d/Github/skvn-marine/tests/core-control-button-hover.test.mjs
```

---

## Agent response routing

- **CODE_NOW** — human pasted this init prompt = approval to implement plan 033.
- Do not write a long plan and wait again.
- Do not fix theme/slider CSS in this session.
- If `skvn-marine-style` dependency fails at runtime, stop and report — do not guess handle name.

## Tensions

None expected. If task expands to edit theme `style.css` for specificity → **STOP**, record LOW tension, ask human.

## Self-check (AGENTS.md §8)

- [ ] No generatepress edits
- [ ] Prefix unchanged
- [ ] Sanitize input / escape output on PHP inline CSS and attrs
- [ ] prefers-reduced-motion in hover CSS
- [ ] No `!important`
- [ ] Files changed ≤ 6 (or justify)
- [ ] CASE-007 + decision doc updated
```

---

## Human handoff one-liner

> Implement Core Button Hover fix per `033_VER_1_3_8_CORE_BUTTON_HOVER_FIX_PLAN.md` using init prompt `docs/artifacts/init-prompt-v1-1.3.8-core-button-hover-fix.md`. CODE_NOW. Do not touch theme/slider CSS.