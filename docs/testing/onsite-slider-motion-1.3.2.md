# Onsite Slider Dynamic Rendering, Controls & Motion 1.3.2 Test

Status: **DEFERRED**
Deferred on: **2026-06-11**
Target milestone: **V1 / 1.3.2 — Slider Dynamic Rendering & Controls Onsite QA**

The V1 / 1.3.0 development build failed onsite review on 2026-06-11. This
checklist includes repair evidence for inconsistent Hero images, narrow Slider
geometry, pagination outside the Slide frame, and reported idle RAM growth. It
also covers the V1 / 1.3.1 navigation/pagination controls UX.

Previously passed Slider image editing and Full Width Canvas checks must be
repeated because the dynamic rendering migration changed those surfaces.

## Target

- Onsite WordPress editor and frontend.
- Target draft/private page URL: record the editor URL and frontend preview URL
  in the evidence report before testing.
- A draft test page containing at least two SKVN Accordion blocks and one SKVN Card Grid with four SKVN Card blocks.
- All three 1.2.1 Slider presets: Hero Slider, Product Showcase, and Card Carousel.

## Preconditions

- Deploy the completed `1.3.1` development build containing the corrected
  dynamic renderer and navigation/pagination controls.
- Clear page/cache layers that can retain old plugin JavaScript or CSS.
- Keep the browser console open during frontend checks.
- Use a draft or private test page instead of changing production content directly.

## Setup

1. Create a draft page using the normal onsite SKVN editing workflow.
2. Insert `SKVN Hero Slider`, `SKVN Product Showcase`, and `SKVN Card Carousel` from the `SKVN Marine` category.
3. Add one base `SKVN Slider` if it remains intentionally exposed, and keep its two default `SKVN Slide` children.
4. Add two `SKVN Accordion` blocks with different content lengths.
5. Add one `SKVN Card Grid` containing four `SKVN Card` blocks.
6. Configure the cards:
   - Card 1: `Fade up`, `On scroll`, Desktop and Tablet enabled, Mobile disabled.
   - Card 2: `Fade in`, `Always`, all devices enabled.
   - Card 3: `Hover lift`, Desktop enabled, Tablet and Mobile disabled.
   - Card 4: `None`.
7. Save the draft and open its frontend preview.

## Slider Editor Checks

1. Confirm `SKVN Marine` appears as a dedicated Block Inserter category.
2. Confirm all three Slider presets appear without requiring search.
3. Confirm each preset inserts useful sample content immediately with no setup modal.
4. Confirm Hero Slider, Product Showcase, and Card Carousel have visibly different intended structures.
5. Open at least one Slider saved before 1.3.0.
6. Confirm existing Slider content opens without invalid-block or recovery warnings.
7. Use native Gutenberg List View/actions to reorder, duplicate, and remove a Slide.
8. Save and reload the editor.
9. Confirm preset structures and order persist without invalid-block or recovery warnings.
10. Confirm the old `Dots` control is replaced by `Show pagination`, pagination
    style, and pagination position.
11. Confirm Autoplay duration offers only `5`, `7`, `9`, and `12` seconds for
    new choices.
12. Open a Slider saved with an arbitrary legacy delay such as `3500ms`.
    Confirm the editor labels it as preserved, does not change it on load, and
    keeps it after an unrelated content edit/save.
13. Select a governed duration for that legacy Slider, save, and confirm the
    selected governed value replaces the legacy value.
14. Confirm Pill is disabled while Side center is selected, and Side center is
    disabled while Pill is selected.
15. Confirm the editor shows a static preview of the selected arrow and
    pagination styles without running Swiper, autoplay, or live timer progress.
16. Confirm the centered `Add slide` button inserts and selects a new Slide,
    remains editor-only, and becomes unavailable at the five-Slide Hero/Product
    Showcase limit.
17. Confirm Hero and Product Showcase hide Slides Per View and remain fixed at
    one; Card Carousel hides it and remains 3/2/1; an unclassified legacy
    Slider retains the compatible 1–4 control.
18. Confirm Hero and Product Showcase offer Directional wipe, Fade, and Zoom
    out with only `600/700/800/900/1000ms`; Card Carousel explains its governed
    responsive movement instead of exposing incompatible transition controls.
19. Confirm `Slider height` offers Default and `Viewport below header`.

## Slider Frontend Checks

1. Confirm every Hero Slide renders its own selected image.
2. Confirm image, overlay, and content use the intended media/content layers
   and a valid image is not concealed by the fallback background.
3. Confirm the Slider occupies the intended SKVN full-width canvas instead of a
   narrow content-width box.
4. Confirm pagination remains inside the visible Slider frame.
5. Confirm Hero Slider and Product Showcase show one slide at a time.
6. Confirm Card Carousel shows 3 cards on desktop, 2 on tablet, and 1 on mobile.
7. Confirm Hero background images cover their slides without stretching.
8. Confirm Product Showcase and Card Carousel render only their Image block media, including Slides that previously stored an unused background image.
9. Confirm Hero heading, lead, and CTA remain readable above the overlay.
10. Confirm arrows, pagination, keyboard navigation, loop, autoplay, and pause-on-hover follow settings behavior.
11. Confirm all approved arrow styles and positions work without overlap.
12. Confirm Pill is unavailable while Side center is selected.
13. Confirm all approved pagination styles and positions work without overlap.
14. Confirm matching bottom positions cluster as `arrows | pagination`.
15. Confirm different arrow and pagination positions remain independent.
16. Confirm zero/one real Slide hides arrows and pagination and disables autoplay.
17. Confirm timed fraction and timed segments follow Swiper autoplay without a second timer/controller.
18. Confirm current/total numbering excludes loop clones.
19. Confirm mobile timed-fraction and timed-segments fallbacks follow the decision contract.
20. Confirm Swiper layout/navigation CSS loads without relying on the SKVN theme.
21. Confirm clicking the already-current pagination item does not restart its
    progress.
22. With loop disabled, confirm autoplay/progress stops on the final Slide.
    Navigate to an earlier Slide and confirm autoplay can resume under the
    normal pause policy.
23. While an autoplay Slider is moving, switch to another browser tab long
   enough to exceed its configured delay. Confirm the hidden Slider does not
   advance continuously in the background.
24. Return to the Slider tab and confirm autoplay resumes only when the Slider
   is not hovered and keyboard focus is not inside it.
25. Repeat the tab switch while the pointer remains over the Slider, then once
    while keyboard focus is on an arrow or pagination control. Confirm returning
    to the tab does not resume autoplay until hover/focus leaves the Slider.
26. During a drag/swipe, confirm timer progress pauses and resumes only after
    interaction ends and no hover/focus/visibility reason remains.
27. Enable Reduce motion and confirm autoplay is disabled and does not resume
    after a tab visibility change.
28. Confirm Slider text uses Inter when the font request succeeds, falls back
    readably when it does not, and the Hero heading is visibly smaller/lighter
    than the previous oversized treatment.
29. Select `Viewport below header` and confirm the Slider fills the visible
    area from the bottom edge of the site header to the bottom of the viewport.
    Repeat while logged in so the WordPress admin bar is also accounted for.
30. Test Directional wipe, Fade, and Zoom out using arrows, pagination,
    autoplay, keyboard, and swipe. Confirm direction remains correct when loop
    wraps from the last real Slide to the first and back.
31. Confirm Zoom out remains clipped inside the Slider frame and none of the
    transitions increases document width or creates horizontal scrolling.
32. During Directional wipe, confirm the incoming Slide edge is feathered
    instead of showing a hard vertical seam, then becomes fully sharp when the
    transition finishes.
33. During Directional wipe, confirm the incoming Hero content staggers in this
    order: heading, paragraph after `100ms`, and Buttons after another `100ms`.
    Confirm both next and previous navigation use the same semantic order.

## Memory Stability Check

1. Open browser task manager or an equivalent per-tab memory view.
2. Record memory after the page becomes idle.
3. Leave the page idle for five minutes without editor interaction.
4. Navigate repeatedly through every Slide using arrows, pagination, keyboard,
   and swipe where available.
5. In the editor, select different Slides and sidebar controls without saving
   runtime progress into block attributes.
6. Confirm memory settles instead of increasing continuously.
7. Confirm console evidence does not show duplicate Swiper initialization,
   repeated subscriptions, repeated observers, or render/update loops.

## Editor Checks

1. Confirm Accordion content remains visible and editable in the editor.
2. Confirm Card content remains visible when any motion preset is selected.
3. Confirm the Motion panel offers only `None`, `Fade up`, `Fade in`, and `Hover lift`.
4. Confirm Fade up/Fade in offer only `On scroll` and `Always`.
5. Confirm Hover lift does not show an irrelevant trigger selector.
6. Confirm Desktop, Tablet, and Mobile are independent toggles.
7. Save and reload the editor.
8. Confirm no invalid-block or block-recovery warning appears.

## Accordion Frontend Checks

1. Confirm both Accordion panels load collapsed after JavaScript enhancement.
2. Open each panel and confirm its real content height is shown without clipping.
3. Close each panel and confirm height animates smoothly to zero.
4. Rapidly open and close one panel; confirm it does not remain stuck or clipped.
5. Use `Tab`, then `Enter` and `Space`, to focus and toggle a heading.
6. Use `Arrow Down` / `Arrow Up` to move between headings.
7. Use `Home` / `End` to move to the first/last heading.
8. Confirm focus is visible.
9. Confirm the accessibility tree reports a button with `aria-expanded` and `aria-controls`.

## Card Motion Frontend Checks

1. At desktop width, scroll Card 1 into view. It should reveal once with Fade up; resizing must not replay it.
2. Reload and confirm Card 2 runs Fade in on page load.
3. On a mouse/trackpad desktop device, hover Card 3 and confirm it lifts slightly.
4. Confirm Card 4 remains static.
5. At tablet width, Card 1 remains enabled and Card 3 remains static.
6. At mobile width, Card 1 and Card 3 remain static; Card 2 remains enabled.
7. On a touch-only device, confirm Hover lift does not activate.

## Fallback And Accessibility Checks

1. Enable operating-system/browser `Reduce motion`, reload, and confirm:
   - Accordion opens and closes without animated transition.
   - Card content is immediately visible.
   - No card entrance or hover animation runs.
2. Disable JavaScript for one reload and confirm:
   - Accordion content remains visible instead of being trapped collapsed.
   - All Card content remains visible.
3. Re-enable JavaScript after this check.

## Pass Criteria

- No invalid-block warning appears.
- Slider images can be chosen, replaced, removed, saved, and rendered.
- Every Hero Slide renders its selected image.
- Slider width and pagination geometry match the approved frame ownership.
- Idle and repeated-interaction memory use settles without continuous growth.
- All three Slider presets are discoverable under `SKVN Marine` and insert useful content immediately.
- Card Carousel responds 3/2/1 across desktop/tablet/mobile.
- Slider editor remains stacked and editable; frontend initializes as Swiper.
- Autoplay pauses while the document is hidden and resumes conservatively only
  when autoplay is enabled, Reduce motion is off, and pointer/focus is outside.
- Accordion interaction, ARIA state, focus, and keyboard navigation work.
- Accordion content is never clipped after open/close or rapid toggling.
- Card controls save and reload correctly.
- Device targeting matches all three independent toggles.
- Motion runs only for the matching preset, trigger, and device combination.
- Reduced-motion and no-JavaScript fallbacks leave all content visible.
- No frontend console error references `skvn-marine-blocks`, motion runtime, or Accordion.
- Arrow and pagination controls match
  `docs/decisions/slider-navigation-and-pagination-controls.md`.
- Legacy `dots`, `arrows`, and arbitrary delay content migrates without
  invalid-block recovery or silent timing changes.
- Legacy `effect: fade` remains Fade until the editor explicitly changes it.
- Inter remains scoped to Slider output and has a readable system fallback.
- `Viewport below header` accounts for the site header and logged-in admin bar.
- Directional wipe, Fade, and Zoom out respect the governed duration and
  reduced-motion fallback.

## Fail Evidence To Report

- Target page URL.
- Editor URL and frontend preview URL.
- Browser/device and viewport width.
- Screenshot or short screen recording.
- Screenshot of the Slider sidebar for one clustered configuration, one
  independent configuration, and one preserved legacy delay.
- Desktop and mobile screenshots for Default and `Viewport below header`,
  including one logged-in screenshot with the admin bar visible.
- Short recording of all three transitions, including one loop wrap.
- Network/console note confirming whether the Inder font request loaded or
  fell back.
- Exact block preset, trigger, and device-toggle combination.
- Whether the failure occurred before hiding the tab, while hidden, or after
  returning, and whether pointer hover or keyboard focus remained inside.
- Whether Reduce motion or JavaScript-disable mode was active.
- Console error text and stack trace, if present.
- Whether the issue occurs in editor, frontend, or both.
- Memory reading at initial idle, after five minutes, and after repeated
  navigation/editor interaction.
