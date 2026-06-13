# Init Prompt — V1 / 1.2.1 Slider Presets & Inserter

Use this prompt to start the implementation in a new chat.

---

## Context

Current milestone: **V1 / 1.2.1 — SKVN Slider Presets & Inserter**.

Read in mandatory order:

1. `AGENTS.md`
2. `.context/GLOBAL.md`
3. `.context/MILESTONES.md`
4. `.context/TENSIONS_OPEN.md`
5. `.context/TENSIONS_ACTIVE.md`
6. `.context/modules/PLUGIN_SKVN_MARINE_BLOCKS.md`
7. `.context/planning/015_VERSION_1_2_1_SKVN_SLIDER_PRESETS_AND_INSERTER_PLANNING.md`
8. `docs/decisions/slider-presets-and-inserter-1.2.1.md`
9. `docs/decisions/slider-block.md`
10. `docs/explain/slider-competitive-research-1.2.0.md`

Human UX direction:

- MVP must be add-and-see.
- Do not build a slide manager.
- Add one `SKVN Marine` category to the Block Inserter.
- Expose three useful Slider presets:
  - SKVN Hero Slider
  - SKVN Product Showcase
  - SKVN Card Carousel
- Hardcode approved experiences through variations/templates, not duplicated
  runtimes or new namespaces.
- Onsite QA is deferred to V1 / 1.2.9.

## Goal

Implement the `SKVN Marine` inserter category and the three Slider presets so a
marketing editor can add a useful Slider with editable sample content in one
action.

## Required Architecture

- Keep `skvn-marine/slider` and `skvn-marine/slide`.
- Keep one Swiper runtime.
- Prefer Gutenberg block variations with inserter scope.
- Use InnerBlocks templates for preset content.
- Keep the editor stacked and directly editable.
- Use native Gutenberg List View/actions for reorder, duplicate, and remove.
- Card Carousel uses responsive 3/2/1 slides per view.
- Do not create a custom slide manager or setup modal.

## Files Allowed To Change

- `wp-content/plugins/skvn-marine-blocks/src/**`
- `wp-content/plugins/skvn-marine-blocks/skvn-marine-blocks.php`
- `wp-content/plugins/skvn-marine-blocks/package.json`
- `wp-content/plugins/skvn-marine-blocks/package-lock.json`
- `wp-content/plugins/skvn-marine-blocks/build/**`
- `.context/MILESTONES.md`
- `.context/modules/PLUGIN_SKVN_MARINE_BLOCKS.md`
- `docs/decisions/slider-presets-and-inserter-1.2.1.md`

## Files Forbidden To Change

- `wp-content/themes/generatepress/**`
- external plugins
- quote form handling
- namespace, plugin slug, text domain, or existing option keys

## Acceptance Checklist

- [ ] Dedicated `SKVN Marine` category appears in the inserter.
- [ ] Existing SKVN blocks use the new category.
- [ ] Three Slider preset entries appear and insert complete sample content.
- [ ] Presets reuse the existing Slider/Slide blocks and Swiper runtime.
- [ ] Hero and Product Showcase use one slide per view.
- [ ] Card Carousel uses 3/2/1 responsive slides per view.
- [ ] No slide manager or setup modal is introduced.
- [ ] Existing Slider content remains valid.
- [ ] `npm run build` passes.
- [ ] PHP syntax passes if PHP changes.
- [ ] `git diff --check` passes.
- [ ] Do not run onsite QA; it belongs to V1 / 1.2.9.

## Tensions / Conflicts

The previous competitive research recommended a hybrid slide manager. Human
explicitly rejected that direction for MVP complexity. The active decision is
`docs/decisions/slider-presets-and-inserter-1.2.1.md`.

Do not reopen the slide-manager decision during implementation unless a concrete
Gutenberg API limitation makes the three preset workflow impossible.
