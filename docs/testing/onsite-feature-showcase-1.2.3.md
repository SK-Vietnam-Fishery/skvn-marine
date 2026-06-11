# Onsite Feature Showcase Test — V1 / 1.2.3

Status: partially passed; B2B pattern layout check carried forward
Target milestone: V1 / 1.3.2

## Target URL/Page

Create or update:

```text
/feature-showcase-test/
```

## Setup / Preconditions

- Upload and activate the V1 / 1.2.3 plugin build.
- Insert one new `SKVN Feature Showcase`.
- Keep one previously saved Feature Showcase, if available, for migration testing.
- Assign real Media Library images to at least two panels.
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

## Expected Behavior

- The block is a reusable panel group, independent from the B2B intro pattern.
- Item count and order are editor-controlled.
- Desktop horizontal and vertical modes use the same content.
- Mobile disclosure works without hover and remains usable with JavaScript disabled.
- Enhanced mode keeps exactly one panel active and never loses the active image/content after a repeated click.
- Images cover a stable panel body rather than determining panel dimensions.
- Existing 1.2.3 content remains styled and migrates without recovery mode.
- Full Width Canvas preserves Gutenberg normal, wide, and full alignment
  semantics.
- The B2B pattern uses a full-width surface with a governed wide inner grid.

## Pass / Fail

PASS when all edits persist, both desktop modes work, mobile tap disclosure works,
legacy content remains valid, and no console or layout errors appear.

FAIL on invalid-block recovery, lost items, inaccessible summaries, overlapping
text, distorted panel sizing, or legacy frontend regression.

## Evidence

- Editor screenshot showing five or more panels.
- Desktop horizontal and vertical screenshots.
- Mobile collapsed and expanded screenshots.
- Legacy block screenshot before and after resave.
- Browser, viewport, console notes, and any invalid-block message.
