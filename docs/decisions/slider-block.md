# Slider Block Decision

## Status

Working decision for V1 / 0.3.0.

## Scope

Build `skvn-marine/slider` and `skvn-marine/slide` in the custom block plugin.

The slider is plugin-owned because removing the theme must not remove interactive block behavior.

## Dependency

Use `swiper`.

Rationale:

- It provides mature slider behavior without hand-rolling touch, keyboard, pagination, loop, and autoplay state.
- It supports modular imports, so the block can use only Autoplay, EffectFade, Keyboard, Navigation, and Pagination.
- It is scoped to the slider frontend view script.
- It matches the V1 need for a production-safe slider faster than a custom implementation.

Alternatives rejected:

- Custom slider runtime: higher accessibility and browser-risk for V1.
- CSS-only carousel: insufficient for keyboard, pagination, loop, and autoplay controls.
- Builder/plugin slider: violates source-control and block ownership direction.

Bundle/load rule:

- Swiper must load only through the slider block frontend view script.
- Editor uses stacked/simplified preview and must not run Swiper autoplay.

Removal plan:

- If V2 replaces the slider with a simpler custom runtime, remove `swiper` from `package.json`, remove Swiper imports from `src/slider/view.ts`, and rebuild block assets.

## Required Behavior

- Slider config comes from block attributes.
- Slide block is allowed only inside Slider.
- Keyboard navigation is enabled.
- Autoplay pauses on hover.
- `prefers-reduced-motion` disables autoplay.
- Frontend JSON config parsing is guarded.
- Editor preview remains stacked/simplified.

