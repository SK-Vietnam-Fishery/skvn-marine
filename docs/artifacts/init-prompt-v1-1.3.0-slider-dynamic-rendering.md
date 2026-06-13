# Init Prompt — V1 / 1.3.0 Slider Dynamic Rendering

Use this prompt to implement the approved V1 / 1.3.0 Slider foundation.

```markdown
## Context

You are working in:

`D:\Github\skvn-marine`

Current milestone:

`V1 / 1.3.0 — Slider Dynamic Rendering Architecture`

Read first, in this exact order:

1. `AGENTS.md`
2. `.context/GLOBAL.md`
3. `.context/MILESTONES.md`
4. `.context/TENSIONS_OPEN.md`
5. `.context/TENSIONS_ACTIVE.md`
6. `.context/modules/PLUGIN_SKVN_MARINE_BLOCKS.md`
7. `docs/decisions/slider-completion-spec-1.3.0.md`
8. `docs/standards/css-layout-safety-contract.md`
9. `docs/workflows/deploy-artifact-workflow.md`
10. `docs/testing/onsite-slider-motion-1.3.1.md`

Archived Slider files linked from
`docs/decisions/slider-completion-spec-1.3.0.md` are historical evidence only.
Do not treat an archived file as active instruction when it conflicts with the
new completion spec.

Read `.local/ENVIRONMENT.md` before using WSL, WP-CLI, WordPress runtime, or
local server commands.

The worktree may already contain human changes. Inspect `git status` and
relevant diffs. Do not revert, overwrite, or clean unrelated changes.

## Mandatory Human Gate

Do not edit Slider source during the first pass.

First produce a concise implementation checkpoint containing:

1. Inventory of every saved Slider and Slide markup generation found in Git
   history.
2. Slider/Slide attribute ownership matrix with approved defaults, ranges, and
   enums.
3. Exact dynamic registration design:
   - PHP module path
   - bootstrap `require_once`
   - Slider render callback
   - Slide render callback
   - block metadata changes
4. Exact compatibility strategy for current `save()` and any required
   `deprecated` definitions.
5. Proposed PHP render signatures and escaping rules.
6. Files to change in Phase B, preferably no more than five source files unless
   the reason is stated.
7. Risks or unresolved questions.

Then stop and wait for human approval.

Do not interpret this gate as permission to reopen already approved product
scope. Ask only about a real technical choice that cannot be determined from
the repository or the approved completion spec.

## Goal

After human approves the Gate 1 checkpoint, complete V1 / 1.3.0:

- Convert `skvn-marine/slider` and `skvn-marine/slide` frontend output to
  dynamic PHP rendering.
- Preserve existing Gutenberg content without invalid-block recovery or bulk
  resave.
- Produce stable Hero media, overlay, and content layers.
- Preserve Product Showcase and Card Carousel flow layouts.
- Keep one Swiper runtime.
- Harden keyboard accessibility, navigation labels, hover/focus pause, reduced
  motion, malformed config handling, and duplicate initialization.
- Fix component geometry without hiding overflow defects.
- Include every new runtime PHP file in deploy artifacts and plugin zip.

## Approved Scope

### Hero

- Maximum five Slides for newly edited content.
- Existing content over the limit must remain valid.
- One Slide per view.
- Effects: `fade` or `slide`.
- Background image belongs to the Slide media layer.
- Overlay and content render above the image.

### Product Showcase

- Maximum five Slides for newly edited content.
- Existing content over the limit must remain valid.
- One Slide per view.
- Use its InnerBlocks Image block.
- Do not expose or render the Hero background image.

### Card Carousel

- Keep 3/2/1 Slides per view across desktop/tablet/mobile.
- Use flow-based card content.
- Do not expose or render the Hero background image.
- Keep current controls; improve geometry and runtime safety.

### Explicitly Out Of Scope

- 5x2 carousel editor grid.
- Ten-card editor cap.
- Marquee mode.
- Centered Carousel mode.
- Card LinkControl or shared new-tab setting.
- Visual card presets.
- Responsive token-spacing controls.
- Zoom, blobs, parallax, stagger, or arbitrary speed curves.
- Fullscreen Step Slider.
- A custom slide manager or `slides` array.
- A second Slider runtime.
- WooCommerce or post queries.
- Video.

Do not implement an out-of-scope item because it appears in an archived file.

## Architecture Requirements

Keep:

```text
skvn-marine/slider
└── skvn-marine/slide
```

Required ownership:

| Concern | Owner |
|---|---|
| Editable content and order | Gutenberg InnerBlocks |
| Slider shell, config, arrows, pagination | Slider PHP renderer |
| Slide frame, Hero media, overlay, content wrapper | Slide PHP renderer |
| Swipe, fade, loop, autoplay, keyboard, breakpoints | Swiper |
| Structural fallback styling | Plugin CSS |

Rules:

- Do not rename block names, namespace, plugin slug, or text domain.
- Do not depend on GeneratePress markup or the SKVN theme for block behavior.
- Do not create parallel static and dynamic frontend engines.
- Transitional static code may exist only for Gutenberg parsing compatibility.
- Product Showcase and Card Carousel may retain legacy background attributes in
  post content, but the PHP renderer must not output those backgrounds.
- PHP input must be normalized to approved values.
- PHP output must use WordPress escaping.
- JSON/data attributes must be encoded with WordPress-safe APIs.
- Button output must have meaningful accessible labels.
- Content must remain readable before Swiper initializes and when JavaScript is
  unavailable.

## Expected Render Contract

Slider:

```html
<div class="wp-block-skvn-marine-slider skvn-slider swiper"
     data-skvn-slider="...">
  <div class="skvn-slider__wrapper swiper-wrapper">
    <!-- rendered skvn-marine/slide children -->
  </div>
  <!-- optional accessible arrows -->
  <!-- optional pagination -->
</div>
```

Slide:

```html
<div class="wp-block-skvn-marine-slide skvn-slide swiper-slide">
  <div class="skvn-slide__media">
    <!-- optional Hero image and overlay -->
  </div>
  <div class="skvn-slide__content">
    <!-- rendered InnerBlocks -->
  </div>
</div>
```

Exact wrappers may be refined during Gate 1, but media, overlay, content, and
Swiper ownership must remain unambiguous.

## Delivery Phases

### Phase A — Inventory And Approval

- Inspect current source and Git history.
- Capture saved-markup fixtures for:
  - initial Slider/Slide
  - image-enabled Slide
  - preset-enabled Slider
- Produce the Mandatory Human Gate deliverable.
- Stop for approval.

### Phase B — Dynamic Rendering

After approval:

- Add a plugin-owned runtime module such as
  `wp-content/plugins/skvn-marine-blocks/modules/slider-render/`.
- Register Slider and Slide render callbacks.
- Preserve InnerBlocks content.
- Add only the minimum compatibility definitions proved necessary in Phase A.
- Implement preset-specific rendering and complete escaping.
- Update deploy artifact assertions if needed for the new runtime module.

Before editing, tell the human exactly which files will change.

### Phase C — Runtime And Geometry

- Add Swiper A11y support.
- Keep keyboard navigation enabled.
- Pause autoplay on hover and keyboard focus within.
- Disable autoplay under `prefers-reduced-motion`.
- Guard malformed config values.
- Prevent duplicate initialization.
- Implement stable Hero media/content layering.
- Preserve Product Showcase flow and Card Carousel 3/2/1 behavior.
- Ensure arrows and pagination do not overlap content or footer.
- Replace the current Hero `100vw` padding calculation unless geometry proves
  viewport ownership is intentional.
- Do not use `overflow-x: hidden` or `clip` to conceal bad geometry.

Before CSS edits, confirm the affected layout owner according to
`docs/standards/css-layout-safety-contract.md`.

### Phase D — Verification And Handoff

- Run source build and PHP syntax checks.
- Verify compatibility fixtures.
- Build deploy artifact.
- Package plugin zip.
- Confirm the Slider render module is present in the zip.
- Update context/docs only for decisions actually made.
- Prepare the onsite handoff for V1 / 1.3.1.

Do not mark onsite QA passed without human evidence.
Do not close or transition the milestone without explicit human approval.

## Files Allowed To Change

Expected source areas:

- `wp-content/plugins/skvn-marine-blocks/skvn-marine-blocks.php`
- `wp-content/plugins/skvn-marine-blocks/modules/slider-render/**`
- `wp-content/plugins/skvn-marine-blocks/src/index.ts`
- `wp-content/plugins/skvn-marine-blocks/src/slider/block.json`
- `wp-content/plugins/skvn-marine-blocks/src/slider/edit.tsx`
- `wp-content/plugins/skvn-marine-blocks/src/slider/save.tsx`
- `wp-content/plugins/skvn-marine-blocks/src/slider/deprecated.tsx`
- `wp-content/plugins/skvn-marine-blocks/src/slider/view.ts`
- `wp-content/plugins/skvn-marine-blocks/src/slider/style.css`
- `wp-content/plugins/skvn-marine-blocks/src/slide/block.json`
- `wp-content/plugins/skvn-marine-blocks/src/slide/edit.tsx`
- `wp-content/plugins/skvn-marine-blocks/src/slide/save.tsx`
- `wp-content/plugins/skvn-marine-blocks/src/slide/deprecated.tsx`
- `tools/build-deploy-artifact.mjs`
- `docs/testing/onsite-slider-motion-1.3.1.md`
- `.context/MILESTONES.md`
- `.context/modules/PLUGIN_SKVN_MARINE_BLOCKS.md`
- `.context/TENSIONS_OPEN.md` only when a real conflict is found

Generated plugin `build/` files may change after the approved source work is
complete.

This milestone may require more than five files because it crosses block
metadata, editor compatibility, PHP runtime, frontend runtime, CSS, generated
assets, and packaging. Keep each implementation phase narrowly scoped and state
the reason before exceeding five files.

## Files Forbidden To Change

- `wp-content/themes/generatepress/**`
- External plugin source.
- Theme files unless the approved plugin-portable contract proves a missing
  theme-owned visual token is required and human approves that separate change.
- Archived Slider files under any `/archives/` folder.
- Fullscreen Step Slider planning or implementation.

Do not rename:

- `skvn-marine/slider`
- `skvn-marine/slide`
- plugin slug `skvn-marine-blocks`
- block namespace `skvn-marine`
- text domain `skvn-marine-blocks`
- PHP prefix `skvn_marine_blocks_`
- CSS prefix `skvn-`

## Acceptance Checklist

- [ ] Human approved the Phase A inventory and compatibility design before source edits.
- [ ] Every known saved Slider/Slide generation has a fixture or documented evidence.
- [ ] Slider and Slide frontend markup is rendered by PHP.
- [ ] Only one active frontend render engine exists.
- [ ] Existing Slider content opens without invalid-block recovery.
- [ ] Existing content renders without bulk resave.
- [ ] Hero uses stable media, overlay, and content layers.
- [ ] Product Showcase does not render a legacy Hero background.
- [ ] Card Carousel does not render a legacy Hero background.
- [ ] Card Carousel remains 3/2/1 responsive.
- [ ] Hero and Product Showcase enforce the approved limit for new editing without invalidating legacy content.
- [ ] Swiper A11y and meaningful navigation labels are present.
- [ ] Keyboard navigation works.
- [ ] Autoplay pauses on hover and focus within.
- [ ] Reduced motion disables autoplay.
- [ ] Malformed config does not break the page.
- [ ] Duplicate initialization is prevented.
- [ ] No-JavaScript content remains readable.
- [ ] No horizontal page overflow is introduced.
- [ ] No `overflow-x` masking is used as a geometry fix.
- [ ] PHP input normalization and output escaping pass review.
- [ ] Plugin build passes.
- [ ] New PHP files pass syntax checks.
- [ ] Deploy artifact includes runtime modules.
- [ ] Plugin zip includes `modules/slider-render/`.
- [ ] Onsite QA remains assigned to V1 / 1.3.1 until human evidence is supplied.
- [ ] Human explicitly approves milestone completion.

## Verification Commands

Run only after the relevant phase is implemented.

Plugin build:

```bash
wsl -d Debian -- bash -lc "source /home/shinkuro/.nvm/nvm.sh && nvm use 20 && cd /mnt/d/Github/skvn-marine/wp-content/plugins/skvn-marine-blocks && npm run build"
```

PHP syntax:

```bash
wsl -d Debian -- php -l /mnt/d/Github/skvn-marine/wp-content/plugins/skvn-marine-blocks/skvn-marine-blocks.php
wsl -d Debian -- php -l /mnt/d/Github/skvn-marine/wp-content/plugins/skvn-marine-blocks/modules/slider-render/slider-render.php
```

Layout audit:

```bash
rg -n "100vw|50vw|100dvw|100svw|100lvw|overflow-x" wp-content/themes/skvn-marine wp-content/plugins/skvn-marine-blocks
```

Deploy artifact and zip:

```bash
wsl -d Debian -- bash -lc "source /home/shinkuro/.nvm/nvm.sh && nvm use 20 && cd /mnt/d/Github/skvn-marine && node tools/build-deploy-artifact.mjs"
wsl -d Debian -- bash -lc "cd /mnt/d/Github/skvn-marine && bash tools/package-plugin-zip.sh"
wsl -d Debian -- bash -lc "cd /mnt/d/Github/skvn-marine && unzip -l build/skvn-marine-blocks.zip | grep 'skvn-marine-blocks/modules/slider-render/'"
```

Final source checks:

```bash
git diff --check
git status --short
```

Apply the project Command Responsiveness rule strictly:

- If WSL, build, packaging, WP-CLI, server, or another command returns no output,
  hangs, or times out once, stop immediately.
- Do not retry, poll, or run a second version of the command.
- Give the human the exact command to run manually and state what output is
  needed before continuing.

## Tensions / Conflicts

Record a HIGH tension and stop if implementation would require:

- changing the approved 1.3.0 product scope
- editing GeneratePress parent files
- renaming blocks, namespace, slug, text domain, or prefix
- replacing InnerBlocks with a `slides` array or custom manager
- adding a second Slider engine
- invalidating existing Slider content
- requiring bulk resave for existing pages
- hiding geometry errors with page-level overflow clipping
- implementing an archived future carousel feature during 1.3.0

Record a LOW tension and proceed conservatively only when the conflict fits the
project's LOW routing rules.

At the end of each phase, report:

- files changed
- behavior completed
- checks run and exact result
- remaining human evidence
- next phase that will begin after approval, when approval is required
```
