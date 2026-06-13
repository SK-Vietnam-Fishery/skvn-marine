# Editor Block Clipboard 1.3.2

Status: READY FOR ONSITE TEST

## Target

- WordPress editor on `http://localhost:8080/wp-admin/`
- One source page containing an SKVN Slider with images and text
- One separate destination page

## Preconditions

- `skvn-marine-blocks` is active and built from the current source.
- Browser clipboard permission is available for the WordPress admin origin.
- The editor is in Visual mode.

## Steps

1. Open the source page and select the Slider parent in List View.
2. Open the editor options menu and choose `Copy selected block(s)`.
3. Open the destination page without switching to Code editor.
4. Choose `Paste block(s)` from the editor options menu.
5. Confirm the pasted item is one editable Slider with its Slide children.
6. Repeat with multiple selected top-level blocks.
7. Select a nested paragraph, copy it, place focus inside a compatible
   container, and paste it.
8. Copy ordinary text outside WordPress and choose `Paste block(s)`.
9. Deny clipboard permission temporarily and try both actions.

## Expected

- Slider attributes, images, text, InnerBlocks, and block hierarchy survive.
- The pasted blocks appear after the current selection when allowed.
- An incompatible nested insertion falls back to the end of the page canvas.
- Ordinary text is rejected without changing post content.
- Browser permission failures show a notice and do not change post content.
- Native keyboard copy/paste and WordPress core clipboard behavior are
  unchanged.

## Pass Or Fail

Pass when all expected behaviors occur with no invalid-block recovery warning
and no browser console error.

Fail when block hierarchy is flattened, content is inserted on an invalid
surface, clipboard failure changes content, or core copy/paste behavior changes.

## Evidence

- Screenshot of the copied Slider in the source page List View.
- Screenshot of the pasted Slider and Slide hierarchy in the destination page.
- Note the browser name/version and clipboard permission behavior.
- Record any invalid-block notice or console error.
