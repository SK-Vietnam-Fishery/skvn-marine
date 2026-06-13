# Init Prompt — V1 / 1.3.1 Slider Controls

Use this prompt to implement the approved Slider navigation and pagination
controls after the V1 / 1.3.0 dynamic rendering foundation passed onsite review.

```markdown
## Context

You are working in:

`D:\Github\skvn-marine`

Current milestone:

`V1 / 1.3.1 — Slider Navigation & Pagination Controls UX`

Read first, in this exact order:

1. `AGENTS.md`
2. `.context/GLOBAL.md`
3. `.context/MILESTONES.md`
4. `.context/TENSIONS_OPEN.md`
5. `.context/TENSIONS_ACTIVE.md`
6. `.context/modules/PLUGIN_SKVN_MARINE_BLOCKS.md`
7. `docs/decisions/slider-completion-spec-1.3.0.md`
8. `docs/decisions/slider-navigation-and-pagination-controls.md`
9. `docs/standards/css-layout-safety-contract.md`
10. `docs/testing/onsite-slider-motion-1.3.2.md`
11. `docs/workflows/deploy-artifact-workflow.md`

Read `.local/ENVIRONMENT.md` before using WSL, WP-CLI, the WordPress runtime,
or the local server.

Inspect `git status` and relevant diffs before editing. Do not revert or clean
human changes.

V1 / 1.3.0 is complete and human-approved. Preserve its dynamic PHP renderer,
canonical media resolution, full-width ownership, stable media/content layers,
one-Swiper runtime, listener cleanup, and compatibility behavior.

## Goal

Implement the approved V1 / 1.3.1 controls contract:

- Replace the editor's dots-only concept with governed pagination controls.
- Add independent arrow and pagination visibility, style, and position.
- Add conditional bottom clustering in the order `arrows | pagination`.
- Add Swiper-owned static and timed pagination without a second timer.
- Govern new editor delay choices to `5/7/9/12s` while preserving legacy saved
  delay values.
- Keep editor previews static.
- Preserve existing Slider content without invalid-block recovery or bulk
  resave.

## Mandatory Compatibility Checkpoint

Before source edits, report:

1. Current Slider attributes and saved markup.
2. Exact `dots` migration/deprecation policy.
3. Exact legacy arbitrary-delay display and persistence policy.
4. Zero/one real-Slide behavior.
5. Proposed PHP classes/markup for independent and clustered controls.
6. Swiper events used for timed progress and real-slide numbering.
7. Mobile and reduced-motion fallback behavior.
8. Files proposed for implementation, preferably no more than five source
   files per logical phase.

Continue implementation when repository evidence and the approved decision
identify a narrow path. Ask the human only for a genuine product decision not
already answered by the decision document.

## Required Behavior

- Arrow styles: `minimal`, `circle`, `pill`.
- Arrow positions: `side-center`, `bottom-left`, `bottom-center`,
  `bottom-right`.
- Pill is unavailable with Side center.
- Pagination styles: `dots`, `fraction`, `timed-fraction`, `timed-segments`.
- Pagination positions: `bottom-left`, `bottom-center`, `bottom-right`.
- Matching bottom positions cluster as `arrows | pagination`.
- Different positions remain independent.
- Zero/one real Slide hides both control families and disables autoplay.
- Timed progress reads Swiper autoplay state; do not add `setInterval`, another
  autoplay controller, or Gutenberg runtime state writes.
- Current/total values exclude loop clones.
- Hover, focus, visibility, and interaction pause reasons must compose.
- Reduced motion disables autoplay/timer animation but keeps manual controls.
- Mobile uses the approved static fallbacks when timed layouts do not fit.
- Editor does not run Swiper, autoplay, or live progress.

## Boundaries

- Keep `skvn-marine/slider` and `skvn-marine/slide`.
- Keep Gutenberg InnerBlocks and native List View.
- Keep the dynamic PHP renderer and one Swiper runtime.
- Do not implement Fullscreen Step Slider, Feature Showcase autoplay/links,
  marquee, centered carousel, 5x2 editor grid, or arbitrary styling controls.
- Do not add viewport-width hacks, negative viewport margins, or overflow
  masking.
- Do not edit `themes/generatepress/**`.

## Primary Files

- `wp-content/plugins/skvn-marine-blocks/src/slider/block.json`
- `wp-content/plugins/skvn-marine-blocks/src/slider/edit.tsx`
- `wp-content/plugins/skvn-marine-blocks/src/slider/view.ts`
- `wp-content/plugins/skvn-marine-blocks/src/slider/style.css`
- `wp-content/plugins/skvn-marine-blocks/modules/slider-render/slider-render.php`

Compatibility evidence may justify Slider deprecations, fixtures, or narrowly
scoped registration changes.

## Verification

- Build plugin assets.
- Run Slider compatibility fixtures.
- Run PHP lint for the bootstrap and Slider renderer.
- Run the layout audit from the CSS safety contract.
- Confirm generated frontend CSS uses the actual emitted asset filename.
- Build the deploy artifact and plugin zip when runtime PHP/build output changes.
- Update `docs/testing/onsite-slider-motion-1.3.2.md` with targeted controls
  smoke checks.
- Do not mark 1.3.1 complete without human onsite evidence.

At completion report:

- compatibility policy
- files changed
- control architecture
- checks and exact results
- remaining onsite test evidence
- whether V1 / 1.3.1 is ready for human approval
```
