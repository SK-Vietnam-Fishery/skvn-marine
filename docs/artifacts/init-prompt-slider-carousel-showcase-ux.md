# Init Prompt - Slider Carousel Showcase UX

Use this prompt in a new chat to continue from the approved brainstorm. The
target version is not assigned. Do not change milestones unless the human
explicitly approves the transition.

```markdown
## Context

Read first, in order:

1. `AGENTS.md`
2. `.context/GLOBAL.md`
3. `.context/MILESTONES.md`
4. `.context/TENSIONS_OPEN.md`
5. `.context/TENSIONS_ACTIVE.md`
6. `.context/modules/PLUGIN_SKVN_MARINE_BLOCKS.md`
7. `docs/standards/css-layout-safety-contract.md`
8. `docs/decisions/slider-block.md`
9. `docs/decisions/slider-presets-and-inserter-1.2.1.md`
10. `docs/decisions/slider-carousel-showcase-ux.md`
11. `.context/planning/017_VERSION_1_3_0_SLIDER_DYNAMIC_RENDERING_ARCHITECTURE_PLANNING.md`
12. `.context/planning/019_FUTURE_CANDIDATE_SLIDER_CAROUSEL_SHOWCASE_UX_PLANNING.md`
13. `docs/testing/onsite-slider-motion-1.2.9.md`
14. `docs/testing/slider-frontend-media-content-layer-bug-1.2.1.md`

Important workspace state:

- A previous turn already modified `src/slide/edit.tsx`, generated
  `build/index.ts.js`, `build/index.ts.asset.php`, and
  `docs/testing/onsite-slider-motion-1.2.9.md` to hide the background-image
  chooser for Product Showcase/Card Carousel and provide cleanup for old
  unused backgrounds.
- Do not revert those changes.
- Inspect and work with the current dirty worktree.
- The full carousel/showcase implementation has not started.
- `AGENTS.md` and `.context/MILESTONES.md` currently disagree about the current
  milestone. Do not silently resolve or change that metadata without explicit
  human approval.

## Goal

Start with architecture/schema planning for the approved Slider Carousel
Showcase UX. Do not immediately implement the entire feature.

Produce the smallest safe first implementation phase after confirming how it
fits the V1 / 1.3.0 dynamic Slider rendering architecture.

Approved UX:

- Carousel/showcase editor preview is a non-moving grid, maximum 5 columns by
  2 rows.
- Maximum 10 Slides.
- Visible Add, Duplicate, Remove, Move earlier/later actions.
- At 10, Add is disabled, gray, and explains the limit.
- Image and Heading always show.
- Optional general content: Subheading, Body text, Meta, Button.
- Desktop layout: Side by side or Stacked.
- Mobile is always Stacked.
- Slider-level mobile toggles control Subheading, Body, Meta, and Button.
- Motion modes: Custom Carousel, Product Showcase Marquee, Centered Carousel.
- Preset-controlled settings remain visible but gray/disabled with helper text.
- Marquee pauses on hover and keyboard focus.
- Each Slide uses native Gutenberg LinkControl in the sidebar.
- Slider owns one Open links in new tab setting.
- Card style/background use governed presets with preview thumbnails.
- Spacing uses tokens, responsive inheritance, and linked sides.
- Reuse Slider/Slide, InnerBlocks, and one Swiper runtime.

## Required First Step

Before editing source, provide an attribute and ownership matrix:

- Existing attributes to retain.
- New Slider attributes.
- New Slide attributes.
- Which content remains InnerBlocks.
- Preset defaults and locked dependencies.
- Compatibility behavior for existing Hero, Product Showcase, and Card
  Carousel posts.
- Which work must wait for dynamic PHP rendering.

Then implement only the first approved phase that can be completed safely
without creating another static saved-markup migration. Prefer the editor grid
and 10-card management phase if the architecture review confirms it is
serialization-safe.

## Files Allowed To Change

Keep each implementation phase to 3-5 source/docs files where possible.

Likely first-phase files:

- `wp-content/plugins/skvn-marine-blocks/src/slider/edit.tsx`
- `wp-content/plugins/skvn-marine-blocks/src/slider/style.css`
- `wp-content/plugins/skvn-marine-blocks/src/slide/edit.tsx`
- `wp-content/plugins/skvn-marine-blocks/src/slider/block.json` only if approved
  attributes are required
- `docs/testing/onsite-slider-motion-1.2.9.md`
- related generated plugin build files after `npm run build`

Broader frontend/dynamic phases may also touch:

- `src/slider/view.ts`
- `src/slider/variations.ts`
- `src/slide/block.json`
- `modules/slider-render/`
- plugin bootstrap and deploy artifact tooling when PHP runtime files are added

## Files Forbidden To Change

- `wp-content/themes/generatepress/**`
- external plugins
- block namespace `skvn-marine`
- plugin slug/text domain `skvn-marine-blocks`
- existing option keys and prefixes

Do not:

- replace InnerBlocks with a `slides` array
- create a second Slider runtime
- add raw CSS/class/gradient controls
- add arbitrary pixel or negative-margin controls
- add WooCommerce automatic queries
- silently change the current milestone/version

## Acceptance Checklist For The First Phase

- [ ] Current dirty changes are preserved.
- [ ] Attribute/ownership matrix is documented or reported before code.
- [ ] No new unresolved HIGH tension exists.
- [ ] Editor does not run Swiper/autoplay.
- [ ] Existing Slider content does not enter invalid-block recovery.
- [ ] Hero background-image behavior is preserved.
- [ ] Product Showcase/Card Carousel do not expose a duplicate background image.
- [ ] Any implemented grid reads left-to-right then top-to-bottom.
- [ ] Narrow editor widths reduce columns safely.
- [ ] Any implemented 10-card limit is enforced in state, not only visually.
- [ ] Disabled controls use real disabled semantics and visible helper text.
- [ ] No new viewport-width or overflow masking rule is introduced without
      geometry proof.
- [ ] Plugin build passes.
- [ ] `git diff --check` passes.
- [ ] Onsite test contract is updated for implemented behavior.

## Verification

Build with the configured WSL Node toolchain:

```bash
wsl -d Debian -- bash -lc "source /home/shinkuro/.nvm/nvm.sh && nvm use 20 >/dev/null && cd /mnt/d/Github/skvn-marine/wp-content/plugins/skvn-marine-blocks && npm run build"
git diff --check
```

If PHP render modules are added, also follow:

- `docs/workflows/deploy-artifact-workflow.md`
- PHP lint for every new runtime PHP file
- deploy artifact and plugin zip runtime-path audit

## Tensions / Conflicts

If the requested phase requires:

- another static Slider markup migration before the dynamic architecture
- replacing InnerBlocks
- a second carousel library/controller
- arbitrary editor CSS/layout controls
- more than 10 Slides without changing the approved UX
- editing GeneratePress parent

record the tension according to `AGENTS.md` and stop if severity is HIGH.
```

