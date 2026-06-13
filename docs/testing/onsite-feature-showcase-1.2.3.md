# Onsite Feature Showcase Test — V1 / 1.3.2

Status: ready for human QA
Target milestone: V1 / 1.3.2

## Target URL/Page

Create or update:

```text
/feature-showcase-test/
```

## Setup / Preconditions

- Upload and activate the V1 / 1.3.2 plugin build.
- Insert one new `SKVN Feature Showcase`.
- Keep one previously saved Feature Showcase, if available, for migration testing.
- Assign real Media Library images to at least two panels.
- Add an internal link to one panel and an external new-tab link to another.
- Enable `SKVN Full Width Canvas` on the test page.
- Insert one normal-alignment Feature Showcase and one B2B Seafood Feature
  Showcase pattern.

## Test Steps

1. Confirm the new block contains panels only and no intro/meta text fields.
2. Add a fifth panel, remove another panel, and reorder two panels.
3. Edit every panel label, heading, copy, image, and one image alt value.
4. Test desktop direction `Horizontal panels`.
5. Test desktop direction `Vertical panels`.
6. Test initially open values `First`, `Last`, and `No panel`.
7. Save and reload the editor; confirm values and order persist.
8. Test Ocean, Deep navy, Marine teal, and Fresh sky gradient presets.
9. Confirm Fresh sky uses dark readable text in the editor and frontend.
10. Confirm images remain readable under each preset overlay.
11. On desktop frontend, hover every panel and confirm it becomes active without clicking.
12. Move directly between panels and confirm there is no blank or flashing frame.
13. Use keyboard Tab/Enter or Space and confirm the focused summary activates.
14. Open each panel and confirm the previously active panel closes.
15. Click the active panel again and confirm its image/content remains visible without a flash.
16. Repeat step 15 with a showcase containing only one panel.
17. At mobile width, confirm headers are horizontal and tap reveals content.
18. Select `Hide on mobile` and confirm only this block is hidden below 782px.
19. Enable reduced motion and confirm expansion does not force transitions.
20. Open the previously saved legacy block and confirm no invalid-block warning.
21. Check the legacy block before resaving and confirm its published layout is still styled.
22. Confirm the normal-alignment Feature Showcase is centered at the theme
    content width rather than filling the viewport.
23. Change that block to `Wide width` and confirm it uses the theme wide width.
24. Change it to `Full width` and confirm only then it fills the page canvas.
25. Confirm the B2B pattern background is full width while its text/panel grid
    is centered at the theme wide width.
26. Confirm a pattern inserted before this fix still uses the wide inner grid
    despite retaining the old constrained-layout class in saved content.
27. Select `Hover` interaction mode and confirm fine-pointer hover activates
    panels while touch/coarse-pointer interaction remains tap-based.
28. Select `Autoplay` and confirm the duration control exposes only `3s`, `5s`,
    `7s`, and `9s`.
29. Test each governed duration and confirm panels rotate in saved order without
    an all-closed frame.
30. Confirm autoplay does not run in the Gutenberg editor.
31. Hover anywhere inside the block and confirm autoplay pauses. Move out and
    confirm it resumes once without skipping panels or creating duplicate timers.
32. Tab into a summary and then into a panel CTA. Confirm autoplay remains paused
    until focus leaves the whole block.
33. While autoplay is active, hide the browser tab longer than the selected
    duration. Confirm panels do not continue rotating in the background.
34. Return to the tab and confirm autoplay resumes only when pointer and keyboard
    focus are outside the block.
35. Enable reduced motion, reload, and confirm automatic rotation is disabled.
36. Use LinkControl to search for and select a WordPress page, post, or product.
37. Save and reload the editor; confirm CTA text, URL, and new-tab setting persist.
38. Confirm same-tab CTA navigation works and new-tab CTA output uses
    `target="_blank"` with `rel="noopener noreferrer"`.
39. Confirm activating a CTA does not toggle or close its disclosure panel.
40. Disable JavaScript for one reload and confirm summaries, panel content, and
    CTA links remain usable.
41. Re-enable JavaScript and confirm no console error references Feature
    Showcase, Slider, or the shared autoplay runtime.
42. Recheck one autoplay Slider on the same page: pointer hover, keyboard focus,
    hidden-tab pause, reduced motion, and drag/swipe interaction must retain the
    approved V1 / 1.3.1 behavior.

## Expected Behavior

- The block is a reusable panel group, independent from the B2B intro pattern.
- Item count and order are editor-controlled.
- Desktop horizontal and vertical modes use the same content.
- Mobile disclosure works without hover and remains usable with JavaScript disabled.
- Enhanced mode keeps exactly one panel active and never loses the active image/content after a repeated click.
- Images cover a stable panel body rather than determining panel dimensions.
- Existing 1.2.3 content remains styled and migrates without recovery mode.
- Existing panel-only content defaults to Hover mode after migration.
- Autoplay uses only the governed delays and never runs in the editor or for
  reduced-motion users.
- Pointer, focus, and document visibility pause reasons compose without an early
  resume.
- CTA links remain separate from `summary` disclosure activation.
- Full Width Canvas preserves Gutenberg normal, wide, and full alignment
  semantics.
- The B2B pattern uses a full-width surface with a governed wide inner grid.

## Pass / Fail

PASS when all edits persist, both desktop modes work, mobile tap disclosure works,
legacy content remains valid, autoplay and CTA behavior match this contract,
Slider pause behavior does not regress, and no console or layout errors appear.

FAIL on invalid-block recovery, lost items, inaccessible summaries, overlapping
text, distorted panel sizing, duplicate/unstoppable timers, CTA/disclosure
conflicts, or legacy/Slider frontend regression.

## Evidence

- Editor screenshot showing five or more panels.
- Desktop horizontal and vertical screenshots.
- Mobile collapsed and expanded screenshots.
- Legacy block screenshot before and after resave.
- Editor screenshot showing Hover/Autoplay and the governed duration marks.
- Short recording of autoplay, hover pause/resume, and focus pause/resume.
- Screenshot or DOM note for the internal CTA and external new-tab CTA.
- Hidden-tab and reduced-motion result.
- Slider regression result from the same build.
- Browser, viewport, console notes, and any invalid-block message.
