# Version 1.2.1 — SKVN Slider Presets & Inserter Planning

Status: active
Created: 2026-06-07
Approved direction: 2026-06-07

## Purpose

Make SKVN blocks discoverable and make Slider insertion immediately useful.

The editor should not need to search for a generic Slider, configure it from an
empty state, or learn a slide-management interface. The user chooses a familiar
slider type from the Block Inserter and receives editable sample content at once.

## Human Direction

- MVP means add and immediately see useful output.
- Do not build a slide manager.
- Hardcode three to five common slider experiences when the set is stable.
- Give SKVN blocks their own Block Inserter category.
- Prefer simple preset choices over a large generic configuration surface.

## Approved MVP Slider Presets

Implement exactly three inserter-facing presets in 1.2.1:

1. `SKVN Hero Slider`
2. `SKVN Product Showcase`
3. `SKVN Card Carousel`

Do not add Testimonial Slider or Logo Carousel until a real site section requires
them.

## Architecture

### One runtime, three entry experiences

Use Gutenberg block variations or an equivalent native registration mechanism:

- Base block remains `skvn-marine/slider`.
- Child block remains `skvn-marine/slide`.
- Swiper runtime remains shared.
- Each preset supplies approved Slider attributes, classes, and InnerBlocks
  templates.
- Do not create three duplicated Slider implementations.
- Do not rename existing block slugs.

### No slide manager

Do not add:

- a custom slide list
- a selected-slide canvas
- custom drag-and-drop ordering
- custom duplicate/delete controls
- a setup wizard

Native Gutenberg behavior remains available:

- List View for navigation and ordering
- block toolbar/options for duplicate and remove
- direct stacked Slide editing in the canvas

### Inserter category

Register one category:

```text
SKVN Marine
```

Move all SKVN-owned blocks into it:

- Slider presets
- Slider / Slide where technically exposed
- Accordion
- Card Grid
- Card
- future SKVN-owned blocks

Do not create separate categories such as SKVN Slider, SKVN Layout, or SKVN
Motion in this milestone.

## Preset Contracts

### Hero Slider

Use case:

- Homepage or campaign first viewport.

Default experience:

- one slide visible
- two editable sample slides
- background image treatment
- heading, lead, and primary CTA
- overlay enabled
- arrows and dots enabled
- autoplay enabled with reduced-motion fallback
- fade transition

Editor expectation:

- immediately shows two complete editable hero surfaces
- each Slide exposes Choose/Replace/Remove background image
- no empty bare heading/copy skeleton

### Product Showcase

Use case:

- Featured product, processing capability, or service highlight.

Default experience:

- one slide visible
- two editable sample slides
- media and content split layout
- product/service heading
- concise description
- CTA
- arrows enabled
- dots enabled
- autoplay off by default
- slide transition

Implementation note:

- Prefer flow-based InnerBlocks such as Columns/Group/Image/Heading/Paragraph/
  Buttons.
- Do not use free absolute layers.
- The preset may use an inline Image block instead of the Slide background-image
  control when that produces the intended split layout more naturally.

### Card Carousel

Use case:

- Product cards, service cards, articles, or repeated highlights.

Default experience:

- three cards visible on desktop
- two on tablet
- one on mobile
- at least four editable sample slides/cards
- image, heading, short copy, and optional CTA
- arrows enabled
- dots optional but enabled for MVP discoverability
- autoplay off by default
- slide transition

Implementation note:

- Responsive slides-per-view is part of this preset contract.
- Keep content flow-based and equal enough in structure to avoid unstable card
  heights.
- Do not query WooCommerce or posts dynamically in 1.2.1.

## Editor UX Rules

- Selecting a preset inserts real editable sample content immediately.
- No modal or intermediate setup screen.
- No custom slide manager.
- No autoplay in the editor.
- All Slide content remains visible in the stacked editor.
- Variation names and descriptions must explain their use case.
- Use distinct icons only when WordPress/Dashicons provide an appropriate icon.
- Avoid duplicate generic Slider entries that confuse normal editors.
- Preserve an advanced/base Slider path only if needed for backward
  compatibility or development.

## Files Expected To Change During Implementation

Primary:

- `wp-content/plugins/skvn-marine-blocks/src/index.ts`
- `wp-content/plugins/skvn-marine-blocks/src/slider/block.json`
- `wp-content/plugins/skvn-marine-blocks/src/slider/edit.tsx`
- `wp-content/plugins/skvn-marine-blocks/src/slider/save.tsx`
- `wp-content/plugins/skvn-marine-blocks/src/slider/view.ts`

Likely supporting files:

- `wp-content/plugins/skvn-marine-blocks/skvn-marine-blocks.php`
- block metadata files whose category changes to `skvn-marine`
- Slider structural CSS
- generated `build/` assets

More than five files is acceptable because category metadata and generated build
artifacts must remain synchronized.

## Non-Scope

- Custom slide manager.
- Absolute-position layer canvas.
- Timeline/keyframe editor.
- Background video.
- Dynamic WooCommerce/post query slides.
- Testimonial Slider.
- Logo Carousel.
- Thumbnail navigation.
- Per-slide scheduling.
- Raw CSS, raw classes, arbitrary transitions, or arbitrary breakpoints.

## Acceptance Draft

- [ ] `SKVN Marine` appears as a dedicated Block Inserter category.
- [ ] Existing SKVN-owned blocks appear under `SKVN Marine`.
- [ ] `SKVN Hero Slider` appears as an inserter choice.
- [ ] `SKVN Product Showcase` appears as an inserter choice.
- [ ] `SKVN Card Carousel` appears as an inserter choice.
- [ ] Each preset inserts useful editable sample content immediately.
- [ ] Presets reuse `skvn-marine/slider`, `skvn-marine/slide`, and one Swiper
      runtime.
- [ ] No slide manager or setup modal is introduced.
- [ ] Hero Slider defaults to one visible slide and a fade-oriented hero setup.
- [ ] Product Showcase defaults to a flow-based media/content split.
- [ ] Card Carousel defaults to 3/2/1 responsive slides per view.
- [ ] Native Gutenberg List View and block actions remain usable.
- [ ] Existing Slider content remains valid.
- [ ] Plugin build passes.
- [ ] Onsite QA is deferred to V1 / 1.2.9.
- [ ] Human approves milestone completion.
