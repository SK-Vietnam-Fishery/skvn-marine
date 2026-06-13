# Card Grid Layout Blocks 1.1.0 Test

Status: **DEFERRED**
Deferred on: **2026-06-07**
Target milestone: **V1 / 1.4.1 — Layout Blocks Validation & Quote Evaluation**

## Target

WordPress editor and frontend page containing the `SKVN Card Grid Test` pattern.

## Preconditions

- `skvn-marine` theme and `skvn-marine-blocks` plugin include the `1.1.0` layout-block implementation or a later compatible build.
- Current plugin build assets are deployed.

## Setup

1. Create a draft page using the `SKVN Full Width` layout.
2. Open the block inserter and switch to Patterns.
3. Insert `SKVN Card Grid Test`.
4. Save the draft and open Preview.

## Test Steps

1. Confirm both card grids appear without an invalid-block warning.
2. Select the first Card Grid and change desktop columns between 2 and 5.
3. Change mobile columns between 1 and 2.
4. Test gap, inset, content alignment, card style, and equal-height controls.
5. Select each Card and test default, featured, process-step, and pricing variants.
6. Test full-bleed and inset image treatments.
7. Edit headings, paragraphs, buttons, and process step numbers.
8. Check editor layout around 1200px, 768px, and 390px widths.
9. Check the frontend Preview at the same widths.

## Expected Behavior

- Cards wrap according to the selected desktop/mobile column presets.
- Equal-height mode keeps cards in the same row visually aligned.
- Full-bleed images reach the card edges; inset images retain inner spacing.
- Featured, bordered, elevated, pricing, and process-step states remain distinguishable.
- Decorative process numbers do not block text selection.
- Buttons follow the selected content alignment.
- Editor and frontend remain close enough to make layout decisions safely.
- No frontend JavaScript is required for the grids.

## Pass/Fail

Pass when:

- No invalid-block or recovery warning appears.
- All preset controls update stable layout classes.
- Content remains editable.
- No overflow, overlap, or unreadable text occurs at tested widths.
- Frontend and editor structures are materially consistent.

Fail when:

- A block reports unexpected or invalid content.
- Grid controls do not affect layout.
- Cards overlap, collapse, or lose editable content.
- Mobile columns or image treatments do not match the selected preset.

## Evidence To Report

- Editor screenshot at desktop and mobile widths.
- Frontend screenshot at desktop and mobile widths.
- Any invalid-block warning text.
- Browser console errors.
- Which preset combination failed, if applicable.
