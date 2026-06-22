# CASE-007 — Core Button Hover: CSS Vars Built But Not Emitted

## Metadata

```text
ID: CASE-007
Category: core-control
Component: core/button + modules/core-control/features/button-hover.php
Milestone: V1 / 1.3.4
Observed: 2026-06-22
Environment: WordPress frontend (view source + DevTools)
Status: PROVEN · REGRESSED → RE-FIXED (2026-06-22, plan 033)
Onsite verification: PENDING
```

## Summary

`render_block_core/button` adapter build mảng `$css_vars` với
`--skvn-btn-hover-text` / `--skvn-btn-hover-bg` nhưng chỉ emit rule `:hover`
reference `var(--skvn-btn-hover-*, inherit)` — **không có rule define** custom
properties trên wrapper.

Hover rule chạy; giá trị luôn fallback `inherit` → màu không đổi dù attrs và
`<style>` đã có trên page.

## Symptoms

1. Sidebar đã set hover text/background; editor preview có thể đúng (inline style
   trên wrapper từ `button-hover/index.tsx`).
2. Frontend: hover không đổi màu.
3. View source: có `<style>` với `.skvn-btn-xxx .wp-block-button__link:hover`.
4. View source: **không** có `.skvn-btn-xxx { --skvn-btn-hover-text: … }`.
5. DevTools script trên wrapper:

```javascript
const btn = document.querySelector('.wp-block-button.skvn-btn-xxx');
getComputedStyle(btn).getPropertyValue('--skvn-btn-hover-text').trim(); // ""
```

## State Delta

```text
State A (editor OK):
- Wrapper có inline --skvn-btn-hover-* từ React wrapperStyle

State B (frontend broken):
- PHP emit :hover { color: var(--skvn-btn-hover-text, inherit) }
- Không emit block define vars trên .wp-block-button.skvn-btn-xxx
- var() → inherit → không đổi màu

Delta:
- $css_vars được populate nhưng không ghi vào $inline_style
```

## Root cause

`button-hover.php` dòng build `$css_vars` tách khỏi `sprintf()` tạo
`<style>` — dead data path; comment file nói "inject CSS custom properties"
nhưng implementation chỉ inject hover selectors.

## Fix contract (v1 — scoped `<style>`, superseded)

Scoped inline `<style>` phải có **hai** phần trên cùng class scope. Implementation
này đã fix CASE-007 ban đầu nhưng gây regression flex layout (`margin-left: 192px`)
khi `<style>` trở thành flex child trong `wp-block-buttons`.

## Fix contract (v2 — current, plan 033)

Pivot sang inline vars + class scoping, không prepend `<style>` vào block markup:

1. **Define** vars trên wrapper qua `style="--skvn-btn-hover-*:…"` + class
   `has-skvn-button-hover` (PHP `preg_replace_callback`).
2. **Consume** vars qua handle `skvn-marine-core-button-hover` dùng
   `wp_register_style` + `wp_add_inline_style`, dependency `skvn-marine-style`.
3. Selector specificity **0,3,1**:

```css
.wp-block-button.has-skvn-button-hover .wp-block-button__link:hover,
.wp-block-button.has-skvn-button-hover .wp-block-button__link:focus-visible {
  color: var(--skvn-btn-hover-text, inherit);
  background: var(--skvn-btn-hover-bg, inherit);
}
```

Dùng `background` (không `background-color`) để gradient hover hoạt động.
Editor preview dùng `editor.BlockListBlock` + `wrapperProps` (không `BlockEdit`).

## Verification

**View source / Elements:**

- [ ] Có block define `--skvn-btn-hover-*` trên wrapper class
- [ ] Có block `:hover` / `:focus-visible` reference cùng vars

**DevTools (hover link):**

```javascript
const link = document.querySelector('.wp-block-button.skvn-btn-xxx .wp-block-button__link');
// hover link, rồi:
getComputedStyle(link).color;
getComputedStyle(link).backgroundColor;
```

**Regression:** `tests/core-control-button-hover.test.mjs`

## General principle

Khi PHP/JS build mảng CSS custom properties cho inline/scoped style:

1. **Define** vars trên scope element (`selector { --x: value }`).
2. **Consume** vars trong rule downstream (`:hover { color: var(--x) }`).
3. Chỉ có (2) mà thiếu (1) → bug im lặng vì `var(..., inherit)` vẫn valid CSS.

Đừng coi "có `<style>` + có `var()`" là đủ bằng chứng feature hoạt động.

## Related

- `docs/debug-casebook/PITFALLS.md` — Pitfall 7
- `docs/decisions/core-control-core-button-hover.md` — Frontend contract
- `modules/core-control/features/button-hover.php`