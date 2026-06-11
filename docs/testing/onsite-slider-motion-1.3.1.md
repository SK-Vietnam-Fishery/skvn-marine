# Onsite Slider Dynamic Rendering & Motion 1.3.1 Test

Status: **DEFERRED**
Deferred on: **2026-06-11**
Target milestone: **V1 / 1.3.1 — Slider Dynamic Rendering Onsite QA**

This checklist carries only the checks that were not completed before V1 /
1.3.0. Previously passed Slider image editing, Slider controls, Feature
Showcase interaction, and Full Width Canvas checks do not need to be repeated
unless the dynamic rendering migration changes those surfaces.

## Target

- Onsite WordPress editor and frontend.
- A draft test page containing at least two SKVN Accordion blocks and one SKVN Card Grid with four SKVN Card blocks.
- All three 1.2.1 Slider presets: Hero Slider, Product Showcase, and Card Carousel.

## Preconditions

- Deploy the completed `1.3.0` dynamic-rendering development build.
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

## Slider Frontend Checks

1. Confirm Hero Slider and Product Showcase show one slide at a time.
2. Confirm Card Carousel shows 3 cards on desktop, 2 on tablet, and 1 on mobile.
3. Confirm Hero background images cover their slides without stretching.
4. Confirm Product Showcase and Card Carousel render only their Image block media, including Slides that previously stored an unused background image.
5. Confirm Hero heading, lead, and CTA remain readable above the overlay.
6. Confirm arrows, dots, keyboard navigation, loop, autoplay, and pause-on-hover follow preset/settings behavior.
7. Confirm Swiper layout/navigation CSS loads without relying on the SKVN theme.
8. While an autoplay Slider is moving, switch to another browser tab long
   enough to exceed its configured delay. Confirm the hidden Slider does not
   advance continuously in the background.
9. Return to the Slider tab and confirm autoplay resumes only when the Slider
   is not hovered and keyboard focus is not inside it.
10. Repeat the tab switch while the pointer remains over the Slider, then once
    while keyboard focus is on an arrow or pagination control. Confirm returning
    to the tab does not resume autoplay until hover/focus leaves the Slider.
11. Enable Reduce motion and confirm autoplay is disabled and does not resume
    after a tab visibility change.

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

## Fail Evidence To Report

- Target page URL.
- Browser/device and viewport width.
- Screenshot or short screen recording.
- Exact block preset, trigger, and device-toggle combination.
- Whether the failure occurred before hiding the tab, while hidden, or after
  returning, and whether pointer hover or keyboard focus remained inside.
- Whether Reduce motion or JavaScript-disable mode was active.
- Console error text and stack trace, if present.
- Whether the issue occurs in editor, frontend, or both.
