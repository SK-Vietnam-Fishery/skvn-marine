# CASE-003 — Hero Text Align vs Block Center

## Metadata

```text
ID: CASE-003
Category: slider
Component: skvn-marine/slider (preset: hero)
Milestone: V1 / 1.3.6
Observed: 2026-06-19
Environment: Frontend hero slider
Status: PROVEN · FIXED · REGRESSION_GUARDED
Onsite verification: PENDING
```

## Summary

Copy trong Hero slider hiển thị lệch trái so với slide, kể cả khi editor set
alignment **Center** cho heading/paragraph/buttons.

Gutenberg `has-text-align-center` chỉ canh **glyph** trong block; hero CSS ghim
**block column** (max-width 48rem) về trái bằng flex `align-items: flex-start`
và thiếu `margin-inline: auto`.

## Symptoms

- Hero slide: heading + paragraph + CTA nhóm lệch trái so với optical center.
- Đổi alignment Center trong block toolbar không sửa vị trí cột copy.
- Ảnh nền/overlay vẫn full width slide — chỉ foreground copy lệch.
- Chủ yếu trên `skvn-slider--hero`; card-carousel / product-showcase layout khác.

## State Delta

```text
State A (kỳ vọng user):
- Block alignment = Center
- Copy nằm giữa slide (optical center)

State B (thực tế):
- text-align: center hoạt động trong block
- Block column (max 48rem) dính trái slide

Delta:
- Glyph alignment ≠ block positioning
- align-items: flex-start trên .skvn-slider--hero .skvn-slide
- .skvn-slide__content > * có max-width 48rem, không margin-inline: auto
```

## Evidence

CSS trước fix:

```css
.skvn-slider--hero .skvn-slide {
  display: flex;
  flex-direction: column;
  align-items: flex-start; /* cross-axis = horizontal → children start left */
}

.skvn-slider--hero .skvn-slide__content > * {
  max-width: 48rem;
  width: 100%;
  /* no margin-inline: auto */
}
```

Editor output ví dụ:

```html
<h2 class="wp-block-heading has-text-align-center">...</h2>
```

→ Chữ canh giữa trong h2 full-width (trong cột 48rem đã ghim trái), không canh
cột trong slide.

## Root Cause

**Confusion of alignment layers:** hero preset thiết kế cột copy hẹp (48rem) nhưng
chỉ implement text-level alignment hooks, không implement block-level centering trong
flex slide frame.

## Fix

**Layer:** `wp-content/plugins/skvn-marine-blocks/src/slider/style.css`

1. `align-items: center` trên `.skvn-slider--hero .skvn-slide`
2. `margin-inline: auto` trên `.skvn-slider--hero .skvn-slide__content > *`
3. Override khi editor chọn left/right:
   - `has-text-align-left` / `has-text-align-right`
   - `.wp-block-buttons.is-content-justification-left/right`
4. `.skvn-slide__content { width: 100% }` — content wrapper full bleed trong slide
5. Zoom-out transition: reset active `.skvn-slide__content { transform: none }`

## Regression Guard

`tests/slider-block.test.mjs`:

- Hero slide `align-items: center`
- Hero content children `margin-inline: auto`

## Verification

**Human — DevTools:**

```javascript
const slide = document.querySelector('.skvn-slide');
const heading = slide?.querySelector('.wp-block-heading');
const slideCenter = slide.getBoundingClientRect().left + slide.offsetWidth / 2;
const blockCenter = heading.getBoundingClientRect().left + heading.offsetWidth / 2;
console.log('offset from slide center (px):', Math.round(blockCenter - slideCenter));
```

PASS khi `|offset| < 8px` với center alignment.

Test left/right alignment vẫn pin block đúng phía sau override.

## General Principle

> Trong layout marketing (hero, full-bleed surface), phân biệt rõ **glyph
> alignment** (`text-align`, `has-text-align-*`) và **block positioning**
> (`margin-inline: auto`, flex `align-items`, grid `justify-items`). Editor
> label “Center” thường chỉ sửa tầng glyph — agent phải verify optical center
> bằng `getBoundingClientRect()`, không suy từ class name.

## Related Files

- `wp-content/plugins/skvn-marine-blocks/src/slider/style.css`
- `tests/slider-block.test.mjs`
- `docs/debug-casebook/PITFALLS.md` — Pitfall 3