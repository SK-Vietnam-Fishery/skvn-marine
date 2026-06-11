# V1 / 1.3.3 — Feature Showcase Autoplay And Panel Links Planning

Status: PENDING
Created: 2026-06-11
Human direction: implement only after Slider dynamic rendering and onsite QA
are verified stable.

## 1. Goal

Extend `skvn-marine/feature-showcase` with:

- a governed interaction mode: hover activation or automatic panel rotation
- an autoplay delay control that snaps to `3`, `5`, `7`, or `9` seconds
- one optional destination link per panel, selected through WordPress'
  Gutenberg link UI
- shared autoplay invariants with Slider where the behavior is genuinely common

This milestone must preserve the existing `details`/`summary` saved interaction
contract and must not turn Feature Showcase into another Swiper carousel.

## 2. Dependency And Entry Gate

Do not begin V1 / 1.3.3 implementation until:

- V1 / 1.3.0 dynamic Slider rendering is complete
- V1 / 1.3.1 controls UX is complete and V1 / 1.3.2 onsite QA confirms Slider
  autoplay, hover/focus pause, keyboard
  behavior, reduced motion, no-JavaScript fallback, and compatibility are stable
- any Slider runtime defects found during QA are fixed and re-tested

The current milestone remains V1 / 1.3.0 until the human explicitly approves a
milestone transition.

## 3. Feature Showcase Interaction Contract

Proposed block-level attributes:

```text
interactionMode: hover | autoplay
autoplayDelay: 3000 | 5000 | 7000 | 9000
```

Defaults:

```text
interactionMode: hover
autoplayDelay: 5000
```

Behavior:

- `hover` keeps the current fine-pointer activation behavior.
- `autoplay` rotates through panels in saved order.
- Pointer hover anywhere within the showcase pauses autoplay.
- Keyboard focus anywhere within the showcase pauses autoplay.
- Autoplay resumes only after both pointer hover and internal keyboard focus
  have ended.
- Manual summary activation updates the active panel without allowing an
  all-closed state.
- Mobile remains tap/focus disclosure; autoplay must not make mobile content
  difficult to read or operate.
- `prefers-reduced-motion: reduce` disables automatic rotation and leaves a
  stable active panel.
- Autoplay must pause when the document is hidden and resume conservatively when
  it becomes visible.
- The editor must not run autoplay.

Use a block-local timer for Feature Showcase. Do not initialize Swiper and do
not run a timer beside Swiper for the Slider block.

## 4. Governed Delay Control

Use the WordPress `RangeControl` in the Feature Showcase sidebar.

The UI should:

- expose marks at `3s`, `5s`, `7s`, and `9s`
- snap to those values only
- appear only when `interactionMode` is `autoplay`
- store milliseconds in block attributes and frontend configuration
- normalize missing or malformed values to `5000`

The control may use a shared editor constant/helper with Slider after Slider QA
confirms that adopting the same delay presets does not regress existing content.

Do not expose arbitrary millisecond input.

## 5. Panel Link Contract

Each Feature Showcase item may add:

```text
linkUrl
linkText
linkTarget
```

Use Gutenberg's native LinkControl or its current supported equivalent so the
editor can search for:

- WordPress pages
- posts
- WooCommerce products
- other registered searchable content
- internal or external URLs

The link belongs inside `.skvn-feature-showcase__content` as an explicit CTA.

Do not:

- wrap `<details>` in an anchor
- turn `<summary>` into a navigation link
- make the full panel a link while it also owns disclosure interaction
- navigate when the user is only trying to activate a panel

Opening in a new tab must be explicit. Frontend output must add the appropriate
`rel` value when `target="_blank"` is used.

## 6. Shared Slider And Showcase Foundation

After Slider is verified stable, audit and share only behavior with at least two
real consumers or a project invariant.

Approved shared candidates:

- `prefersReducedMotion()` from `src/shared/motion.ts`
- autoplay delay constants: `3000`, `5000`, `7000`, `9000`
- delay normalization
- editor marks/options for the governed delay control
- pointer-hover and keyboard-focus pause/resume binding
- document visibility pause/resume policy

Slider remains responsible for:

- Swiper lifecycle
- slide navigation, loop, arrows, dots, and breakpoints
- Swiper autoplay pause/resume calls

Feature Showcase remains responsible for:

- synchronizing sibling `<details>` elements
- panel activation order
- its block-local autoplay timer
- preserving the native disclosure fallback

Do not create a generic carousel controller or make Feature Showcase depend on
Swiper.

## 7. Compatibility

Existing Feature Showcase content must:

- continue to parse without invalid-block recovery
- retain hover behavior when the new attributes are absent
- retain existing panel content and order
- render without requiring a bulk resave

Adding per-item link fields changes serialized item data and saved markup.
Before implementation:

1. Capture current Feature Showcase saved-markup fixtures.
2. Define defaults for old item objects without link fields.
3. Decide whether the current `save()` output remains valid through optional
   markup or requires a Gutenberg deprecation.
4. Verify old content opens, renders, edits, saves, and reloads.

## 8. Accessibility And Fallback

- Keyboard users can activate summaries and reach panel CTAs.
- Focus on a CTA must not unexpectedly rotate the panel.
- Autoplay pauses while any descendant owns focus.
- Reduced-motion users do not receive automatic panel changes.
- No-JavaScript output retains native `details`/`summary` disclosure and usable
  CTA links.
- Link text must describe the destination; do not rely on an unlabeled icon.
- External/new-tab behavior must be communicated accessibly if enabled.

## 9. Slider Follow-Up

V1 / 1.3.3 should update Slider only where sharing is justified:

- replace duplicate reduced-motion detection with the shared helper
- normalize Slider delay against the approved delay presets if compatibility
  review permits
- use the shared pause/focus/visibility policy while keeping Swiper as the
  Slider controller
- keep existing Slider frontend config compatible with saved values

If existing Slider delay values outside `3/5/7/9s` exist, do not silently change
their frontend timing. Define a compatibility path before restricting the
editor control.

## 10. Expected Files

Likely implementation surface:

```text
wp-content/plugins/skvn-marine-blocks/src/shared/autoplay.ts
wp-content/plugins/skvn-marine-blocks/src/slider/edit.tsx
wp-content/plugins/skvn-marine-blocks/src/slider/view.ts
wp-content/plugins/skvn-marine-blocks/src/feature-showcase/block.json
wp-content/plugins/skvn-marine-blocks/src/feature-showcase/types.ts
wp-content/plugins/skvn-marine-blocks/src/feature-showcase/edit.tsx
wp-content/plugins/skvn-marine-blocks/src/feature-showcase/save.tsx
wp-content/plugins/skvn-marine-blocks/src/feature-showcase/view.ts
wp-content/plugins/skvn-marine-blocks/src/feature-showcase/style.css
```

This list is a planning inventory, not permission to modify all files in one
unreviewed task. Split implementation into focused changes if needed.

## 11. Testing

Editor:

- Switch between Hover and Autoplay.
- Confirm delay control appears only for Autoplay.
- Confirm the range thumb snaps to `3s`, `5s`, `7s`, and `9s`.
- Add, edit, remove, and search for an internal product/page/post link.
- Confirm autoplay does not run in Gutenberg.
- Open legacy Feature Showcase content without recovery.

Frontend:

- Verify Hover mode on fine pointers and tap activation on coarse pointers.
- Verify autoplay order and each governed delay.
- Verify pointer hover, keyboard focus, CTA focus, and hidden document pause.
- Verify resume does not create duplicate timers or skip panels unexpectedly.
- Verify reduced motion disables automatic rotation.
- Verify internal, external, same-tab, and new-tab CTA behavior.
- Verify no-JavaScript disclosure and links remain usable.
- Verify horizontal and vertical layouts at desktop, tablet, and mobile widths.

Slider regression:

- Re-run autoplay, hover/focus pause, reduced motion, keyboard, and
  no-JavaScript checks after adopting shared helpers.
- Confirm existing Slider delay/config values remain compatible.

## 12. Acceptance Draft

- [ ] V1 / 1.3.0 and V1 / 1.3.1 stability gates are complete
- [ ] Human approves the final attribute and saved-markup contract
- [ ] Feature Showcase supports Hover and Autoplay modes
- [ ] Delay control snaps only to `3s`, `5s`, `7s`, and `9s`
- [ ] Hover, focus, document visibility, and reduced motion govern autoplay
- [ ] Autoplay does not run in the editor
- [ ] Each panel supports an optional Gutenberg LinkControl destination
- [ ] Panel CTA does not conflict with `summary` disclosure
- [ ] Existing Feature Showcase content remains valid and editable
- [ ] Slider and Feature Showcase share only approved autoplay invariants
- [ ] Slider retains Swiper as its only movement controller
- [ ] No-JavaScript output remains readable and operable
- [ ] Plugin build and relevant TypeScript checks pass
- [ ] Onsite editor/frontend QA passes
- [ ] Human approves milestone completion
