# Version 1.2.0 — Slider Editor & Motion Planning

Status: implemented — onsite QA deferred to V1 / 1.2.9
Created: 2026-06-04
Promoted: 2026-06-07

## Purpose

Complete the practical Slider/Slide editing experience and implement portable animation/motion support for `skvn-marine-blocks`.

Slider editor scope:

- Stacked editor preview; no Swiper/autoplay inside Gutenberg.
- Per-slide WordPress Media Library image selection, replacement, and removal.
- Directly editable heading, lead, and CTA.
- Governed overlay opacity.
- Plugin-owned structural and Swiper CSS.

## Source Decision

- `docs/decisions/block-animation-strategy.md`

## Version Placement

Implemented milestone: **1.2.0 — Slider Editor & Motion**.

Human approved promotion and milestone start on **2026-06-07**.

## Completed Deferral Boundaries

### 0.10.0

- Do not implement animation.
- Resolve onsite test debt only.

### 0.11.0

- Do not implement animation.
- Move `SKVN Footer` admin surface from `Settings` to `SKVN Marine → Footer`.

### 1.0.0

- Do not implement animation.
- Focus on V1 launch readiness: accessibility, mobile QA, SEO/GEO, performance, and onsite stability.

### 1.1.0

- Do not build a motion engine.
- Layout blocks may prepare markup that can accept future motion safely.
- Do not expose motion controls.
- Acceptance should confirm layout blocks do not depend on theme-only animation behavior.

## Mandatory Architecture Boundary

If animation behavior belongs to any `skvn-marine-blocks` custom block, the plugin must ship everything required for that behavior without depending on the `skvn-marine` theme:

- block attributes
- editor controls
- frontend JS
- CSS classes
- keyframes
- transitions
- reduced-motion fallback
- device targeting
- no-JS visible fallback

Theme CSS/JS may skin or override plugin motion, but must not be required for plugin block animation to work.

Theme-owned animation remains acceptable only for:

- core blocks
- page sections
- theme decoration
- non-plugin content

## Planned Build Order

1. Harden accordion height animation in `src/accordion/view.ts`.
2. Add slider first-slide entrance only if a real UX case justifies it.
3. Add shared plugin motion helper such as `src/shared/motion.ts`.
4. Add device targeting model with independent checkboxes: Desktop, Tablet, Mobile.
5. Add stat counter or KPI motion block only when there is a real content use case.
6. Add theme `assets/js/animations.js` only for core blocks/page sections, separate from plugin motion.

## Editor Control Model

Motion controls must be preset-based and non-dev friendly.

Suggested controls:

```text
Motion preset
- None
- Fade up
- Fade in
- Wave float
- Hover lift

Trigger
- Fade up / Fade in: On scroll or Always
- Hover lift: On hover automatically

Devices
[x] Desktop
[x] Tablet
[ ] Mobile
```

Do not expose:

- raw CSS
- raw classes
- raw Tailwind/WindPress utilities
- arbitrary milliseconds
- arbitrary transforms
- custom easing text input

## Fallback Rules

Content must remain visible when JS fails.

Entrance animation should use a visible default and only apply hidden pre-animation state when the owner runtime has added a `.js` or equivalent active marker.

Editor output must remain visible and editable. Do not set editor output to `opacity: 0`.

All animation must respect `prefers-reduced-motion`.

## Device Targeting

Device targeting uses independent checkboxes:

```text
[x] Desktop
[x] Tablet
[ ] Mobile
```

Do not use grouped dropdowns such as:

```text
Desktop only
Tablet + desktop
All devices
```

Hover animation must account for touch devices:

```css
@media (hover: hover) and (pointer: fine) {
  /* hover animation only on devices that support hover */
}
```

## Theme Runtime Enqueue

`_skvn_disable_animations` is a kill switch, not the primary loading condition.

For theme-level animation:

1. If `_skvn_disable_animations` is true, do not enqueue theme `animations.js` and add `skvn-no-animations` body class.
2. If false, enqueue theme `animations.js` only when the page likely contains theme-level animation targets.
3. Detection can use page preset/template, registered pattern markers, or content containing SKVN theme motion classes.
4. If detection becomes brittle, a small global runtime may be accepted only after measuring payload and documenting the tradeoff.

Plugin block animation assets are separate and should use block-owned `viewScript`/styles through `block.json`.

## Acceptance Draft

- [x] Human approves exact milestone version before implementation.
- [x] Motion implementation remains deferred until after 1.0.0.
- [x] Plugin-owned animation works without `skvn-marine` theme active by source architecture; onsite verification pending.
- [x] Plugin-owned animation ships required CSS/JS/keyframes/reduced-motion fallback inside the plugin.
- [x] No frontend content remains invisible when JS fails by fallback design; onsite verification pending.
- [x] Editor content is not hidden by animation fallback; onsite verification pending.
- [x] Device targeting uses independent Desktop/Tablet/Mobile checkboxes.
- [x] Hover motion is guarded to hover-capable pointer devices; onsite touch verification pending.
- [x] Theme `animations.js` remains reserved for core/page/theme-level animation and was not added without a use case.
- [x] Tailwind/WindPress is prototype-only and not the production animation contract.
- [x] Build passes for `skvn-marine-blocks`.
- [ ] Human runs `docs/testing/onsite-slider-motion-1.2.9.md` during V1 / 1.2.9.
- [x] Human approved moving forward with onsite QA deferred to V1 / 1.2.9.
