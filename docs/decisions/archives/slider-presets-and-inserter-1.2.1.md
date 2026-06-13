# Slider Presets And Inserter — 1.2.1 Decision

Date: 2026-06-07
Status: implemented; onsite QA deferred to V1 / 1.2.9
Milestone: V1 / 1.2.1

## Decision

SKVN will optimize Slider UX for immediate use, not for maximum configurability.

The Block Inserter will expose three familiar Slider experiences:

- SKVN Hero Slider
- SKVN Product Showcase
- SKVN Card Carousel

They reuse the existing Slider/Slide blocks and Swiper runtime through Gutenberg
variations/templates. They are not separate duplicated Slider engines.

All SKVN-owned blocks will use one Block Inserter category named `SKVN Marine`.

## Rationale

The current generic Slider requires the editor to understand nested Slide blocks
and configure a blank structure. A custom slide manager would improve complex
Slider management but would add another interface to learn.

For the current site and marketing users, the shortest useful path is:

```text
Add block
→ SKVN Marine
→ choose Hero / Product Showcase / Card Carousel
→ replace sample image and copy
```

This reduces decision cost without creating proprietary editing behavior.

## Rejected Direction: Custom Slide Manager

Do not build a custom manager, tab strip, or selected-slide editing canvas in
1.2.1.

Reasons:

- It adds UI before the core Slider use cases are proven.
- Gutenberg already provides List View, reorder, duplicate, and remove.
- It increases state synchronization and invalid-block risk.
- It delays the add-and-see MVP.

The competitive research recommendation for a hybrid slide manager is superseded
for the MVP by this human decision. It may only be reconsidered after evidence
that native List View and stacked editing fail on real pages.

## Preset Versus Separate Block

Use a preset/variation when:

- runtime behavior is still Swiper
- the child type is still `skvn-marine/slide`
- differences are default attributes, templates, classes, and responsive presets

Create a separate block only when:

- the data source or interaction contract is materially different
- a preset would require brittle conditional logic
- the feature must survive theme changes but cannot be represented by the
  Slider/Slide schema

No separate Slider block is approved for 1.2.1.

## Inserter Category Contract

Category slug:

```text
skvn-marine
```

Category label:

```text
SKVN Marine
```

Rules:

- Register from the plugin layer.
- Use WordPress category hooks/APIs; do not patch Gutenberg core.
- All SKVN-owned custom blocks use this category.
- Keep `skvn-marine/slide` constrained to the Slider parent.
- Do not split categories by feature family in 1.2.1.

## Variation Contract

Each variation must define:

- clear user-facing title
- one-sentence use-case description
- approved icon
- Slider default attributes
- InnerBlocks template
- stable identifying attribute/class when required
- inserter scope

Variation registration must not:

- rename the base block
- duplicate Swiper runtime
- save raw utility classes
- require a setup modal
- depend on GeneratePress markup

## Default Content Principles

- Sample content should explain the visual structure through realistic labels.
- Avoid lorem ipsum.
- Images remain replaceable placeholders; do not bundle misleading production
  photography.
- CTA buttons must be directly editable.
- A newly inserted preset must look intentionally designed before any editing.
- Content remains English-first and translation-ready.

## Runtime Principles

- Hero: one slide per view.
- Product Showcase: one slide per view.
- Card Carousel: responsive 3 desktop / 2 tablet / 1 mobile.
- Fade must force one slide per view.
- Autoplay defaults:
  - Hero: on
  - Product Showcase: off
  - Card Carousel: off
- Existing reduced-motion and keyboard rules remain.
- No additional frontend dependency is introduced.

## Testing Boundary

Implementation/build checks belong to 1.2.1.

Onsite editor/frontend verification for 1.2.0 and 1.2.1 is consolidated under
V1 / 1.2.9.

## Implementation Record

Implemented on 2026-06-07:

- The plugin registers one `SKVN Marine` inserter category.
- All current SKVN-owned block metadata uses category slug `skvn-marine`.
- Three JavaScript block variations provide complete editable InnerBlocks
  templates.
- Hero is the default Slider variation, preventing a duplicate generic Slider
  entry while preserving the base block and existing content.
- Card Carousel uses one optional saved responsive flag that configures Swiper
  for 3/2/1 slides per view.
- Product Showcase and Card Carousel remain flow-based and do not introduce
  dynamic queries, a slide manager, or another runtime.
- `npm run build`, PHP syntax validation, and `git diff --check` pass.
