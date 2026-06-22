# CASE-006 — Slider Editor Arrow Preview CSS Cascade

## Metadata

```text
ID: CASE-006
Category: slider / gutenberg
Component: skvn-marine/slider editor preview arrows
Milestone: V1 / 1.3.6
Observed: 2026-06-20
Environment: Gutenberg block editor (iframe)
Status: PROVEN · FIXED · REGRESSION_GUARDED
Onsite verification: PENDING
Implementation: 2026-06-20 — see docs/decisions/slider-editor-arrow-preview-1.3.6.md
```

## Summary

Editor static arrow preview (thumbnail trên thanh navy dưới slide stack) trông
vỡ: vòng tròn “rỗng”, chevron xanh to lệch trái. SKVN `background` **đã apply**
— đây là **CSS cascade stack** + (trước fix) Swiper hook class trên preview,
không phải “thiếu file CSS”.

## Symptoms

- Circle appears hollow on navy preview bar `#073b5a`
- Chevron blue `#007aff`, oversized, left-shifted
- `aria-controls="swiper-wrapper-*"` on preview button (before fix)
- Human screenshot: `ArrowErrror.png`

## State Delta

```text
State A (spec):
  padding: 0
  color: #fff
  ::after font-size: 1rem
  fill navy on frontend slide

State B (broken):
  padding: 10px 20px
  color: rgb(0, 122, 255)
  width/height: 44px but inner content width ≈ 4px
  fill present but camouflaged on preview bar

Delta:
  .editor-styles-wrapper button > .skvn-slider__arrow (padding)
  Swiper theme color > SKVN white (color)
  preview bar ≈ frontend fill (camouflage)
  swiper-button-prev on preview → view.ts navigation bind
```

## Runtime Evidence (DevTools)

Element (before fix):

```html
<button class="skvn-slider__arrow skvn-slider__arrow--prev swiper-button-prev"
  type="button" aria-controls="swiper-wrapper-…"></button>
```

Computed highlights:

| Property | Value | Meaning |
|----------|-------|---------|
| `background-color` | `rgba(7, 59, 90, 0.92)` | SKVN fill loaded |
| `padding` | `10px 20px` | Editor global button wins |
| `color` | `rgb(0, 122, 255)` | Swiper `--swiper-theme-color` |
| `width` × inner box | `44px`, ~4px content | Glyph geometry broken |

## Root Cause Layers

1. **Padding leak** — specificity `(0,1,1)` editor button > `(0,1,0)` `.skvn-slider__arrow`.
2. **Color leak** — Swiper theme after/beside SKVN in cascade.
3. **Camouflage** — preview bar `#073b5a` ≈ circle fill; only border visible.
4. **Hook class** — `swiper-button-prev` on editor preview → `view.ts` `querySelector`.

## Fix Implemented

Chi tiết quyết định: `docs/decisions/slider-editor-arrow-preview-1.3.6.md`.

| # | Change | File |
|---|--------|------|
| 1 | Bỏ `swiper-button-*` khỏi editor preview buttons | `edit.tsx` |
| 2 | `initSlider` return nếu `.skvn-slider--editor` | `view.ts` |
| 3 | Editor iframe CSS: `padding:0`, `color:#fff`, preview contrast, `::after` | `style.css` |
| 4 | Frontend arrows: `color:#fff`, `padding:0` hardening | `style.css` |
| 5 | Regression tests | `tests/slider-block.test.mjs` |

## Prevention

- Contract: `docs/decisions/gutenberg-editor-chrome-contract.md` §2.5–§2.6
- Pitfall: `docs/debug-casebook/PITFALLS.md` Pitfall 6
- Verify: `docs/testing/slider-editor-arrow-preview-1.3.6.md`

## General Principle

> Editor decorative `button` phải thắng `.editor-styles-wrapper button` bằng
> **computed proof trong iframe**, không reuse **runtime hook class** chỉ vì
> “giống frontend”, và preview bar cần **contrast token riêng** — không copy
> frontend fill lên nền editor chrome.

## Related

- CASE-004 — editor chrome vs frontend overlay (hit-target)
- CASE-005 — editor performance (useSelect)
- `docs/decisions/slider-navigation-and-pagination-controls.md` — frontend arrow spec (unchanged)