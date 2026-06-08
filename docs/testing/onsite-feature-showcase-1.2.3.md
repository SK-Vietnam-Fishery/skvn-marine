# Onsite Feature Showcase Test — V1 / 1.2.3

Status: ready for onsite verification
Target milestone: V1 / 1.2.3

## Target URL/Page

Create or update a test page onsite, for example:

```text
/feature-showcase-test/
```

## Setup / Preconditions

- The `skvn-marine-blocks` plugin build for V1 / 1.2.3 is uploaded/active.
- `SKVN Feature Showcase` appears under the `SKVN Marine` inserter category.
- Insert one `SKVN Feature Showcase` block.
- Replace at least two panel images with real Media Library images.

## Test Steps

1. Insert the block from `SKVN Marine -> SKVN Feature Showcase`.
2. Edit eyebrow, heading parts, intro, meta label, and meta text.
3. Edit all four panel kickers, headings, and copy.
4. Replace at least two panel images.
5. Edit image alt text for at least one panel.
6. Remove one panel image and confirm the block remains usable.
7. Save the page and reload the editor.
8. Open the frontend page on desktop.
9. Hover each panel and confirm the focused/hovered panel expands.
10. Use keyboard Tab to focus each panel and confirm the panel content appears.
11. Open mobile width and confirm intro appears first, then the compact panel rail.
12. Tap/focus panels on mobile and confirm the panel content can be revealed.
13. Enable reduced motion at OS/browser level if available and re-check that forced transitions do not run.

## Expected UX / Visual Behavior

- The block inserts with complete sample content.
- The intro/copy surface is readable.
- Panel images cover their panel frame without distortion.
- Desktop defaults to the last panel open.
- Hover and keyboard focus can expand another panel.
- Mobile keeps the intentional split layout: intro first, compact panel rail second.
- Mobile panel labels remain visible, and focused/tapped panels can reveal their body content.
- Reduced-motion users do not receive forced panel animation.

## Pass / Fail Criteria

PASS when:

- No invalid block warning appears after save/reload.
- All editable text and image values persist.
- Desktop hover and keyboard focus both reveal panel content.
- Mobile layout is readable and does not depend on hover.
- No console errors are reported.

FAIL when:

- The block is missing from the inserter.
- Any editor field fails to persist.
- Keyboard focus cannot reveal panel content.
- Mobile rail hides all meaningful content with no focus/tap path.
- Layout overflows or text overlaps incoherently.

## Evidence Human Should Report Back

- Desktop editor screenshot after insertion.
- Desktop frontend screenshot with the default open panel.
- Desktop frontend screenshot with a different panel focused or hovered.
- Mobile frontend screenshot of the split intro + panel rail.
- Browser and viewport.
- Console/log notes.
- Any invalid-block message text, if present.
