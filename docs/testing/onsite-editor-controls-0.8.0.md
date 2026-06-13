# Onsite Editor Controls Test — 0.8.0

Status:

```text
DEFERRED_TO_V1_0.10.0_BY_HUMAN
```

Purpose:

```text
Validate SKVN Editor Controls in the real onsite WordPress editor and frontend.
Do not run this on local unless the human explicitly asks.
```

Deferred note:

```text
Human approved moving from 0.8.0 to 0.9.0 on 2026-06-03 while carrying this onsite editor-controls test debt to V1 / 0.10.0.
When V1 / 0.10.0 becomes current, run this checklist before closing onsite test debt.
```

## Scope

In scope:

```text
- Gutenberg sidebar controls for SKVN-owned blocks or translated layout surfaces.
- Content, Style, Layout, and Advanced control grouping.
- Safe preset behavior.
- Save/reload stability.
- Frontend/editor visual parity.
- Desktop and mobile visual smoke.
```

Out of scope:

```text
- Local runtime testing.
- GeneratePress parent theme edits.
- Raw custom class workflow.
- Footer Page Settings 0.9.0.
- Quote flow runtime 0.7.1 / 0.10.0.
- Slider-specific controls until the slider editor UX tension is resolved.
```

## Related Files

Open before testing:

- `docs/decisions/skvn-editor-controls-0.8.0.md`
- `.context/modules/PLUGIN_SKVN_MARINE_BLOCKS.md`
- `.context/modules/THEME_SKVN_MARINE.md`
- `.context/MILESTONES.md`

## Preconditions

Record:

```text
[ ] Onsite domain:
[ ] Tester:
[ ] Date/time:
[ ] Browser:
[ ] WordPress version:
[ ] `skvn-marine` child theme is active.
[ ] `skvn-marine-blocks` plugin is active.
[ ] Current deployed build includes the 0.8.0 editor-control code.
[ ] Browser devtools can be opened.
```

## Test Page Setup

Use a draft or private onsite test page.

```text
Page title: SKVN Editor Controls Test 0.8.0
Slug: skvn-editor-controls-test-080
Visibility: Draft or Private unless human wants it public
```

Insert the first supported 0.8.0 target block/surface:

```text
SKVN Accordion
```

Record:

```text
[ ] Test page URL:
[ ] Tested block/surface name: SKVN Accordion
[ ] Initial inserted state screenshot:
```

## Editor UI Note

Gutenberg may show a native panel named `Styles` with options such as:

```text
Default
SKVN Section
SKVN Card
```

This native `Styles` panel is not the main 0.8.0 SKVN Accordion control surface.

For this test, select the `SKVN Accordion` block and test the accordion-specific sidebar panels:

```text
Content
Style
Layout
Advanced
```

Expected usage:

```text
Use `Content` to edit Heading.
Use `Style` to choose accordion Tone: Default, Fresh, Trust, Navy.
Use `Layout` to choose Spacing and Width presets.
Use `Advanced` only for the optional ARIA label.
Do not use raw class input, raw color values, arbitrary spacing, custom CSS, or custom JavaScript.
```

## Test Case 1 — Control Grouping

Steps:

```text
1. Open the test page in Gutenberg editor.
2. Select the supported SKVN block/surface.
3. Inspect the block sidebar.
4. Confirm control panels/groups.
```

Expected:

```text
[ ] Content group exists if the target has editable content fields.
[ ] Style group exists for tone/component variants.
[ ] Layout group exists for width/spacing/alignment where supported.
[ ] Advanced group exists only for controlled technical settings.
[ ] No primary workflow requires typing raw classes.
[ ] Unsupported theme-controlled layout areas show no misleading controls.
```

Evidence:

```text
[ ] Sidebar screenshot for each group.
[ ] Missing/unexpected controls:
```

## Test Case 2 — Content Controls

Steps:

```text
1. Change a normal content field from the sidebar or block UI.
2. Save/update the page.
3. Reload the editor.
4. Preview the frontend.
```

Expected:

```text
[ ] Content change persists after editor reload.
[ ] No invalid block warning appears.
[ ] Frontend shows the edited content.
[ ] The edit does not require raw class input.
```

Evidence:

```text
[ ] Before screenshot:
[ ] After editor screenshot:
[ ] Frontend screenshot:
[ ] Invalid block warning: YES / NO
```

## Test Case 3 — Style Presets

Steps:

```text
1. Change the tone/style preset.
2. Save/update.
3. Reload the editor.
4. Preview the frontend.
5. Inspect the block markup/classes if comfortable.
```

Expected:

```text
[ ] Style choices use preset labels, not raw colors.
[ ] No raw hex/rgb/hsl value is required.
[ ] Preset persists after reload.
[ ] Frontend visual output matches editor intent.
[ ] Output maps to approved `skvn-*` class or theme preset.
```

Evidence:

```text
[ ] Selected preset:
[ ] Editor screenshot:
[ ] Frontend screenshot:
[ ] Markup/class note if inspected:
```

## Test Case 4 — Layout Presets

Steps:

```text
1. Change width or spacing preset.
2. Save/update.
3. Reload the editor.
4. Preview desktop and mobile frontend.
```

Expected:

```text
[ ] Layout choices use preset labels.
[ ] No arbitrary pixel/rem spacing input is required.
[ ] Desktop layout is readable and aligned.
[ ] Mobile layout stacks cleanly.
[ ] No horizontal scroll appears on mobile.
[ ] Text does not overlap controls, cards, or buttons.
```

Evidence:

```text
[ ] Selected layout preset:
[ ] Desktop screenshot:
[ ] Mobile screenshot:
[ ] Any overflow/overlap:
```

## Test Case 5 — Advanced Controls

Steps:

```text
1. Set a safe advanced value such as HTML anchor or ARIA label if the target supports it.
2. Save/update.
3. Reload editor.
4. Preview frontend.
```

Expected:

```text
[ ] Advanced controls are limited and understandable.
[ ] No custom JavaScript or custom CSS input exists.
[ ] Safe value persists after reload.
[ ] Frontend remains valid and accessible.
```

Evidence:

```text
[ ] Advanced value tested:
[ ] Editor screenshot:
[ ] Frontend/source note:
```

## Test Case 6 — Save / Reload Stability

Steps:

```text
1. Apply one Content change, one Style preset, and one Layout preset.
2. Save/update the page.
3. Close the editor tab.
4. Reopen the page editor.
5. Confirm the block remains valid.
6. Make one additional text edit and save again.
```

Expected:

```text
[ ] No invalid block warning.
[ ] Controls show the saved selected values.
[ ] Frontend still matches the latest editor state.
[ ] No console error blocks editing.
```

Evidence:

```text
[ ] Reloaded editor screenshot:
[ ] Console errors:
[ ] Notes:
```

## Test Case 7 — Negative Checks

Check that these are not present as normal marketing workflows:

```text
[ ] Raw class input required for visual styling.
[ ] Raw hex/rgb/hsl input required for color.
[ ] Arbitrary margin/padding numeric input required for spacing.
[ ] Custom CSS text area.
[ ] Custom JavaScript input.
[ ] GeneratePress parent-theme edit requirement.
```

Pass condition:

```text
All items above are absent or clearly not required.
```

## Pass Criteria

0.8.0 onsite editor-control test passes only when:

```text
[ ] Supported control groups appear as expected.
[ ] Content controls persist.
[ ] Style presets persist and map to theme-owned output.
[ ] Layout presets persist and stay responsive.
[ ] Advanced controls are limited and safe.
[ ] Save/reload does not create invalid block warnings.
[ ] Frontend output matches editor intent.
[ ] Desktop visual smoke passes.
[ ] Mobile visual smoke passes.
[ ] No serious editor/frontend console errors.
[ ] No local-only assumption is needed to validate the result.
```

## Fail Criteria

Fail or block if:

```text
[ ] Block becomes invalid after save/reload.
[ ] Editor controls require raw classes for normal use.
[ ] Raw color or arbitrary spacing is required.
[ ] Frontend ignores saved controls.
[ ] Mobile layout breaks or has horizontal scroll.
[ ] GeneratePress parent theme must be edited.
[ ] Slider-specific controls are implemented before the slider editor UX tension is resolved.
```

## Result Template

Send this back after onsite testing:

```text
Result: PASS / FAIL / BLOCKED
Tester:
Date/time:
Onsite domain:
Test page URL:
Tested block/surface:
Content controls pass: YES / NO
Style controls pass: YES / NO
Layout controls pass: YES / NO
Advanced controls pass: YES / NO / N/A
Save/reload valid: YES / NO
Frontend matches editor: YES / NO / PARTIAL
Desktop screenshot attached: YES / NO
Mobile screenshot attached: YES / NO
Console errors:
Notes:
```
