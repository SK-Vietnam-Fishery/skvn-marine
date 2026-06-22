# Decision — Slider Parallax (Both Depth) 1.3.8

**Version:** 1.0  
**Milestone:** V1 / 1.3.8 — Slider Parallax  
**Status:** DECIDED  
**Date:** 2026-06-22  

---

## Summary

Implement Slider Parallax using Swiper built-in Parallax Module with **both translate + scale depth**, applied to background image layer only. Governed presets (subtle/medium/strong) per intensity, no auto-disable on zoom-out transition. Default `enableParallax: false`.

---

## Decision

### Engine: Swiper 11 Parallax Module

```ts
// src/slider/view.ts
import { Parallax } from 'swiper/modules';

modules: [..., Parallax],
parallax: enableParallax && !reducedMotion && slidesPerView === 1,
```

- Runtime inject `data-swiper-parallax` + `data-swiper-parallax-scale` attrs on `.skvn-slide__bg`
- Swiper owns single `transform` per element — no competing transforms
- No custom animation engine; `prefers-reduced-motion` disables parallax entirely

### Depth Mechanism: Both (Translate + Scale)

Visual testing confirms both provide meaningful depth:
- **Translate** alone: background "slides" horizontally behind content  
- **Scale** alone: background "zooms" during transition  
- **Both compound**: layered depth effect (1.08 scale × 1.12 parallax = ~1.21 effective scale)

Apply both to `.skvn-slide__bg` simultaneously.

### Intensity Presets (Governed, Not Raw)

| Preset | Translate | Scale | Inset override |
|--------|-----------|-------|-----------------|
| subtle | `-15%` | `1.06` | `-20%` |
| medium | `-30%` | `1.12` | `-35%` |
| strong | `-50%` | `1.20` | `-50%` |

**Inset coupling rule**: `.skvn-slide__bg { inset: var(--skvn-parallax-inset, 0%) }` scales with intensity to prevent edge-reveal (image mismatch at viewport edge during translate). Default `0%` preserves existing layout when parallax is off.

Inspector default: `enableParallax: false`, `parallaxIntensity: medium`.

### No Auto-Disable on Zoom-out Transition

Initial proposal suggested auto-disable parallax when `transition: zoom-out`. **Reject** — code analysis shows clean separation:

- `.skvn-slide__media` (CSS-owned): `scale(1.08)` on non-active slide (zoom-out effect)
- `.skvn-slide__bg` (Swiper-owned): `transform: translate + scale` (parallax)
- `.skvn-slide__content` (CSS-owned): fade + translateY

Each element has one transform owner. Compounding scale is intentional depth layering, not conflict. All three transitions (wipe/fade/zoom-out) remain compatible with parallax.

### Background Layer Only (`__bg`)

Parallax attributes **only** on `.skvn-slide__bg`, not on `__content`.

Rationale:
- Content already has zoom-out transition (fade + translateY on `.skvn-slide__content`)
- Adding parallax translate to content would force Swiper inline transform to override CSS, breaking zoom-out animation
- Image layer parallax provides sufficient depth; content parallax adds risk without proportional UX gain

---

## Markup Contract

### Current PHP render (frontend)

```php
<div class="skvn-slide swiper-slide">
  <div class="skvn-slide__media">
    <img class="skvn-slide__background-image" … />
    <span class="skvn-slide__overlay" … />
  </div>
  <div class="skvn-slide__content">…InnerBlocks…</div>
</div>
```

### Required change: Add `.skvn-slide__bg` wrapper

```php
<div class="skvn-slide swiper-slide">
  <div class="skvn-slide__media">
    <div class="skvn-slide__bg">  <!-- ← NEW wrapper -->
      <img class="skvn-slide__background-image" … />
    </div>
    <span class="skvn-slide__overlay" … />
  </div>
  <div class="skvn-slide__content">…InnerBlocks…</div>
</div>
```

**File ownership:**
- `wp-content/plugins/skvn-marine-blocks/modules/slider-render/slider-render.php` — PHP render (frontend)
- `src/slide/edit.tsx` — sync editor markup
- `src/slide/save.tsx` — sync saved markup (editor validation)

**Editor deprecation:** Because this changes saved markup, `deprecated.tsx` would normally be required. **For 1.3.8 onsite dev site**: skip deprecation, accept that old content may show "invalid block / attempt recovery" on open. **Commercialization note**: when plugin ships to multiple customers with existing content, implement Gutenberg deprecation (ref: Feature Showcase pattern) to auto-migrate old markup on resave.

### CSS changes

```css
.skvn-slide__media {
  position: absolute;
  inset: 0;
  z-index: 0;
  overflow: hidden;  /* Contain parallax scale overspill */
  pointer-events: none;
}

.skvn-slide__bg {
  position: absolute;
  inset: var(--skvn-parallax-inset, 0%);  /* 0% = no-parallax default; view.ts injects negative value when enabled */
  will-change: transform;
}

.skvn-slide__bg img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: center;
  display: block;
}
```

CSS variable injected by `view.ts` on **slider root element** (`.skvn-slider`), not per-slide — intensity is uniform across all slides in one slider:
```ts
const insetMap = { subtle: '-20%', medium: '-35%', strong: '-50%' };
slider.style.setProperty('--skvn-parallax-inset', insetMap[parallaxIntensity]);
```

---

## Inspector UX

### Attributes (Slider `block.json`)

```json
{
  "enableParallax": { "type": "boolean", "default": false },
  "parallaxIntensity": { "type": "string", "enum": ["subtle", "medium", "strong"], "default": "medium" }
}
```

### Editor Panel

- **Toggle**: "Enable Parallax" (boolean)
  - help: *"Background image moves at a different speed than the slide, creating a sense of depth. Has no effect in the editor."*
- **When enabled**, show **Intensity** (3 preset buttons: Subtle / Medium / Strong)
  - help: *"Controls how far the background travels and how much it scales during the transition. Higher intensity = stronger depth effect."*
  - **ButtonGroup not dropdown**: intensity is a tactile feel comparison, not a numeric value — user needs to pick by eye, not from a list.
- **When disabled**, hide intensity control
- **Badge** when `enableParallax: true`: `"Parallax ON · {intensity}"`
  - Reason: editor does not run Swiper parallax runtime; badge confirms the setting is active without misleading user into thinking the effect should be visible in editor.
- No parallax runtime in editor — stacked slide preview remains static

### Governed Internally — Hidden from UI

**Depth mechanism: Both (translate + scale)** is always applied when parallax is enabled. It is **not exposed** as a user control.

Rationale:
- Translate-only lacks z-depth (background "slides" but doesn't recede)
- Scale-only is only visible mid-transition (active slide = `scale(1)`, effect collapses)
- Both compound = strongest perceivable depth layer on `__bg`
- Differentiation between slider personalities comes from **Transition** (wipe / fade / zoom-out), not from depth mechanism — exposing translate vs scale vs both adds complexity without proportional UX value
- Decision confirmed by POC lab (`slider-parallax-zoom-poc.html`) visual testing

Values injected at runtime in `view.ts` per intensity preset (user never sees these):

| Preset | `data-swiper-parallax` | `data-swiper-parallax-scale` |
|--------|------------------------|-------------------------------|
| subtle | `-15%` | `1.06` |
| medium | `-30%` | `1.12` |
| strong | `-50%` | `1.20` |

### Auto-disable (no UI)

Parallax disabled when:
- `prefers-reduced-motion: reduce`
- `slidesPerView > 1` (card carousel)

These are automatic guards in `view.ts`, not user-facing controls.

---

## Acceptance Checklist

- [ ] Swiper Parallax Module imported in `view.ts`
- [ ] `.skvn-slide__bg` wrapper added to PHP render, editor markup, saved markup
- [ ] Intensity attributes registered in `block.json`
- [ ] Inspector toggle + 3 intensity buttons render correctly
- [ ] `data-swiper-parallax` + `data-swiper-parallax-scale` injected at runtime per intensity
- [ ] Inset CSS var coupled to intensity (subtle -20%, medium -35%, strong -50%)
- [ ] `prefers-reduced-motion` disables parallax (no inline transform applied)
- [ ] `slidesPerView > 1` disables parallax
- [ ] Editor preview shows badge, no parallax motion
- [ ] Desktop hero slides with parallax work: wipe transition, fade transition, zoom-out transition
- [ ] No edge-reveal (image mismatch) at viewport edges during translate
- [ ] Compounded scale on zoom-out + parallax scale looks intentional, not artifact-y
- [ ] Loop + autoplay + keyboard pairing no regression
- [ ] Old slider content (before parallax added): frontend renders OK, no fatal; editor may show "attempt recovery" warning — acceptable per D6
- [ ] Plugin build pass, PHP syntax check pass
- [ ] Human approves milestone completion

---

## Deferred / Out of Scope 1.3.8

- Per-block `__content` parallax translate (too risky with zoom-out transition)
- Scale-only mode (translate gives better visual results; scale-only deferred)
- User-facing "depth mechanism" dropdown (governed both is the only exposed option)
- Parallax on step slider frames (1.3.10 inherits foundation; fine-tuning deferred to 1.3.10)
- Content-layer intensity coupling (background layer is the primary depth mechanism)
- Mobile `< 768px` disable guard — parallax runs on mobile when `slidesPerView === 1` and reduced-motion is off. This supersedes 026 §mobile which suggested a breakpoint guard.

---

## Supersedes 026

`026_VER_1_3_6_BLOCK_EDITOR_UX_AND_SLIDER_PARALLAX_PLANNING.md` is the foundation plan. The following 026 items are overridden by 1.3.8 decisions:

| 026 item | 1.3.8 decision |
|----------|----------------|
| Content layer parallax (`__content -10%`) | **Rejected** — `__content` parallax conflicts with zoom-out CSS transition owner (D5) |
| Mobile `< 768px` auto-disable | **Rejected** — no breakpoint guard; only `prefers-reduced-motion` + `slidesPerView > 1` (D4) |
| `--parallax-inset` CSS variable name | **Changed** to `--skvn-parallax-inset` for namespace consistency (D2) |
| Per-slide inset injection | **Changed** to slider root injection — uniform intensity per slider (D3) |

---

## Future: Commercialization Note

If plugin is packaged for sale with multiple customers having pre-1.3.8 content:

1. Implement `src/slide/deprecated.tsx` with old `save()` function
2. Register as `deprecated: [{ attributes, save: oldSave }]` in slide block
3. Gutenberg auto-migrates old markup to new on user resave
4. No "invalid block" warning appears during open/edit

See: `wp-content/plugins/skvn-marine-blocks/src/feature-showcase/deprecated.tsx` pattern for reference.

---

## Related

- `.context/planning/026_VER_1_3_6_BLOCK_EDITOR_UX_AND_SLIDER_PARALLAX_PLANNING.md` — foundation plan
- `docs/artifacts/slider-parallax-swiper-poc.html` — visual pass (translate POC)
- `docs/artifacts/slider-parallax-zoom-poc.html` — lab (both translate + scale, various transitions)
