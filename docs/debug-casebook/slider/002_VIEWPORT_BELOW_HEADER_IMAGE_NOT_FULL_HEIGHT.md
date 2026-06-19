# CASE-002 — Viewport Below Header Image Not Full Height

## Metadata

```text
ID: CASE-002
Category: slider
Component: skvn-marine/slider (heightPreset: viewport-below-header)
Milestone: V1 / 1.3.6
Observed: 2026-06-19
Environment: Frontend with Swiper initialized
Status: PROVEN · FIXED · REGRESSION_GUARDED
Onsite verification: PENDING
```

## Summary

Slider ở chế độ `Viewport below header` có container đủ cao (fill vùng dưới
header), nhưng ảnh nền `object-fit: cover` không phủ full chiều cao slide — chỉ
phủ vùng tương ứng content height.

Markup layer (`skvn-slide__media`, `skvn-slide__background-image`) đúng contract
1.3.0; lỗi nằm ở **height ownership** giữa SKVN preset và Swiper sizing chain.

## Symptoms

- Preset height khác (Default, Medium, Tall, Hero) hiển thị ảnh ổn hơn hoặc ổn.
- `Viewport below header`: slide cao ~ viewport, ảnh/overlay chỉ cao một phần.
- Khoảng trống phía dưới ảnh trong slide (hoặc ảnh như dải mỏng phía trên).
- Không phải lỗi resolution ảnh hay thiếu `__media` wrapper (PHP render đúng).

## State Delta

```text
State A (đúng hơn):
- heightPreset: default | medium | tall
- min-height cố định (rem), gap với content height nhỏ

State B (sai):
- heightPreset: viewport-below-header
- min-height: calc(100dvh - var(--skvn-slider-viewport-offset))
- Sau skvn-slider--initialized + Swiper effect

Delta:
- Chỉ viewport preset dùng 100dvh trên slide
- Swiper .swiper-wrapper / .swiper-slide dùng height: 100%
- .skvn-slider không có height explicit → % height chain gãy
- .skvn-slide__media (absolute inset:0) fill theo computed box nhỏ
```

## Evidence

CSS trước fix:

```css
.skvn-slider--height-viewport-below-header:not(.skvn-slider--editor) .skvn-slide {
  min-height: calc(100dvh - var(--skvn-slider-viewport-offset, 5rem));
}
```

Swiper core (`node_modules/swiper/swiper.css`):

```css
.swiper-wrapper { height: 100%; }
.swiper-slide { height: 100%; }
```

→ Parent `.skvn-slider` không set `height` → slide `height: 100%` resolve thấp;
`min-height` tạo visual frame lớn nhưng absolute media không stretch theo min-height
đủ trong chain Swiper.

## Root Cause

**Split height ownership:** viewport offset sync set CSS variable trên slider,
nhưng chỉ `min-height` trên slide — không establish explicit height trên
`.skvn-slider` → `.skvn-slider__wrapper` → `.skvn-slide` → `.skvn-slide__media`.

## Fix

**Layer:** `wp-content/plugins/skvn-marine-blocks/src/slider/style.css`,
`src/slider/view.ts`

1. CSS: `--skvn-slider-viewport-height` + `height`/`min-height` trên `.skvn-slider`;
   `height: 100%` trên wrapper, slide, `__media`.
2. JS: `syncViewportHeight(swiper)` sau init; `updateSize()` + `updateSlides()` on resize.

## Regression Guard

`tests/slider-block.test.mjs`:

- Assert `--skvn-slider-viewport-height` trên slider root
- Assert wrapper `height: 100%`
- Assert `__media` `height: 100%`
- Assert `syncViewportHeight(swiper)` và `activeSwiper.updateSize()`

## Verification

**Human — DevTools:**

```javascript
const slide = document.querySelector('.skvn-slide');
const media = slide?.querySelector('.skvn-slide__media');
const img = slide?.querySelector('.skvn-slide__background-image');
console.table({
  slide: slide?.getBoundingClientRect().height,
  media: media?.getBoundingClientRect().height,
  image: img?.getBoundingClientRect().height,
});
```

PASS khi ba giá trị gần bằng nhau (sau hard refresh + build mới).

**Onsite:** Step 29 `docs/testing/onsite-slider-motion-1.3.2.md` (viewport fill).

## General Principle

> Viewport-sized surfaces phải own **explicit height** trên container root,
> không chỉ `min-height` trên child — đặc biệt khi thư viện bên thứ ba (Swiper)
> propagate `height: 100%`. Luôn verify bằng geometry measurement, không chỉ
> đọc `object-fit: cover` và `inset: 0`.

## Related Files

- `wp-content/plugins/skvn-marine-blocks/src/slider/style.css`
- `wp-content/plugins/skvn-marine-blocks/src/slider/view.ts`
- `tests/slider-block.test.mjs`
- `docs/debug-casebook/PITFALLS.md` — Pitfall 2