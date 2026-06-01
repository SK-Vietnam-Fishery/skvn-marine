# Version 0.8.0 Planning — SKVN Editor Controls

> Planning reference for Elementor-inspired sidebar controls inside Gutenberg.
> Load this file when planning or implementing SKVN block controls, section presets, spacing controls, responsive visibility, or editor governance for marketing-owned pages.

---

## Status

Status: **PENDING**
Target: **V1 / 0.8.0**

This file does not change the current milestone. Current milestone remains managed by `.context/MILESTONES.md`.

---

## Problem

Gutenberg is safe and source-controlled, but marketing editors still need a faster visual editing flow for layout, spacing, tone, and interactive blocks. Elementor's sidebar UX is familiar and productive, but unrestricted freeform controls can scatter raw colors, spacing, and classes across content.

SKVN needs an editor experience that feels direct without turning Gutenberg content into the brand source of truth.

---

## Goal

Create SKVN-owned editor/sidebar controls for Gutenberg that expose safe presets instead of raw design decisions.

The controls should cover:

- Content fields where a block owns structured content.
- Style presets for tone, button style, media treatment, radius, and shadow.
- Layout presets for width, alignment, columns, spacing, margin, and padding.
- Responsive visibility and limited responsive spacing overrides.
- Block-specific behavior such as slider playback, transition, navigation, and motion.

Design debt carried from 0.5.1:

- Page display controls can hide the frontend page title, but the Gutenberg editor still needs a visible title/identity surface so editors can name, find, and edit the page safely.
- In 0.8.0, resolve the editor UX so `Hide page title` and presets such as `SKVN Landing Canvas` hide the rendered frontend title without making the editor title field disappear or feel lost.

---

## Ownership

Theme `skvn-marine` owns:

- Color tokens and `theme.json` palette.
- Spacing tokens and section rhythm.
- Tone classes such as `skvn-section--fresh`, `skvn-section--trust`, `skvn-section--premium`, and `skvn-section--neutral`.
- Width classes such as `skvn-width--content`, `skvn-width--wide`, and `skvn-width--full`.
- Spacing classes such as `skvn-spacing--compact`, `skvn-spacing--normal`, `skvn-spacing--spacious`, and `skvn-spacing--hero`.
- Card, media, button, radius, shadow, and editor/frontend CSS parity.

Plugin `skvn-marine-blocks` owns:

- Block sidebar UI.
- Block attributes.
- Block saved markup and class composition.
- Block-specific frontend behavior.
- Shared editor-control utilities only when at least two blocks consume them or they enforce a project invariant.

Gutenberg content owns:

- Editable copy.
- Editable links.
- Editable media.
- Selected preset values.

Gutenberg content must not own:

- Raw `<style>` or `<script>`.
- Raw hex/rgb/hsl brand colors.
- Unbounded arbitrary spacing values.
- Raw class entry as the primary marketing workflow.

---

## UX Model

Use Elementor as a mental model, not as an unrestricted clone.

Primary sidebar groups:

```text
Content
- Structured text, CTA, media, and block-owned content fields.

Style
- Tone: Fresh / Trust / Premium / Neutral / Dark CTA when supported.
- Button style: Primary / Secondary / Text / White when supported.
- Media style: Flat / Card / Soft shadow when supported.
- Radius and shadow presets.

Layout
- Width: Content / Wide / Full.
- Alignment: Left / Center.
- Spacing: Compact / Normal / Spacious / Hero.
- Padding: token preset controls, responsive only where needed.
- Margin: None / Normal / Large / Pull up, with no arbitrary negative values.
- Responsive visibility: hide desktop/tablet/mobile.

Advanced
- Anchor ID.
- Optional developer-only diagnostic output.
```

Page-level editor parity rule:

```text
Frontend visibility != editor identity.
```

If a page display preset hides the frontend title, the editor should still show a safe title editing/identity surface, possibly with a small state label such as `Hidden on frontend`.

---

## Spacing Contract

Spacing controls must prefer presets.

Initial spacing presets:

```text
Compact  -> tighter section rhythm for dense content.
Normal   -> default section rhythm.
Spacious -> large narrative sections.
Hero     -> first-viewport hero rhythm with mobile reduction.
```

Initial margin presets:

```text
Top:    None / Normal / Large / Pull up
Bottom: None / Normal / Large
```

Rules:

- Do not expose freeform pixel inputs in the first implementation.
- Do not expose unrestricted negative margin.
- Left/right padding is theme/container-owned by default; expose only when a block has a proven layout need.
- Responsive controls inherit desktop values unless explicitly overridden.

---

## Saved Markup Contract

Controls should save compact attributes and output classes.

Example attribute intent:

```json
{
  "tone": "fresh",
  "spacing": "hero",
  "width": "wide",
  "marginTop": "none",
  "marginBottom": "normal",
  "hideOnMobile": false
}
```

Example saved class contract:

```html
<section class="skvn-section skvn-section--fresh skvn-spacing--hero skvn-width--wide skvn-margin-top--none skvn-margin-bottom--normal">
```

Avoid:

```html
<section style="padding-top:73px;background:#eaf7ff">
```

---

## First Implementation Candidate

Start with one narrow implementation path instead of a global builder clone.

Recommended first target:

1. Resolve slider editor UX tension: use stacked editor preview.
2. Implement shared control definitions for tone/spacing/width/margin where reusable.
3. Apply block-specific controls to `skvn-marine/slider` and `skvn-marine/slide`.
4. Add or map the required theme CSS classes in `skvn-marine`.

Do not implement a full generic page-builder container in the first pass unless core Gutenberg blocks plus existing `skvn-*` layout classes cannot support the required homepage/landing workflows.

---

## External Plugin Reference

Reference only. Do not add a dependency without a separate dependency decision.

- GenerateBlocks: closest fit with GeneratePress and a lightweight block philosophy.
- Kadence Blocks and Spectra: useful references for responsive sidebar controls, but broader than SKVN's governance needs.
- Stackable and GreenShift: useful references for richer page-builder UX, but higher risk of visual and motion scope drift.
- Advanced Block Controls: useful reference for adding controls to existing/core blocks.

---

## Acceptance

- [ ] Editor controls contract is documented before code.
- [ ] Theme owns visual tokens/classes; plugin owns sidebar UI and block behavior.
- [ ] Content stores selected presets, not raw brand styles.
- [ ] Controls are grouped into Content, Style, Layout, and Advanced.
- [ ] Margin and padding controls are token/preset based.
- [ ] Responsive overrides inherit from desktop by default.
- [ ] Hiding the frontend page title does not remove the editor title/identity surface.
- [ ] Slider-specific controls follow the resolved editor UX.
- [ ] No unrestricted raw class, raw hex, raw inline spacing, or custom CSS workflow is required for marketing users.
- [ ] Editor does not hide content with `opacity: 0`.
- [ ] Frontend respects `prefers-reduced-motion` where controls affect motion.
- [ ] GeneratePress parent remains untouched.
