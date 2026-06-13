# Onsite Map Block / Map Surface Test — 1.1.2

Status:

```text
DEFERRED_TO_V1_1.1.2
```

Latest human feedback:

```text
2026-06-05: Current map surface is not viewable. Add this to V1 / 1.1.2 testing together with block/page/product related checks.
```

## Purpose

Verify the onsite map block or map surface renders visibly and does not fail because of embed markup, responsive container sizing, blocked iframe/content, or theme CSS.

This is a testing and source-hardening checklist. Do not add a new map plugin or dependency unless the human explicitly changes scope.

## Target URL / Page

Record before testing:

```text
Target page URL:
Map block/surface location:
Tester:
Date/time:
Browser:
Device/viewport:
```

## Preconditions

```text
[ ] `skvn-marine` child theme is active.
[ ] The page containing the map has been published or previewed onsite.
[ ] The map markup/block is present in the editor or page source.
[ ] No production credentials or private map API keys are exposed in page source.
```

## Test Steps

```text
1. Open the target onsite page on desktop.
2. Confirm the map area is visible without a blank/zero-height container.
3. Open the same page on mobile.
4. Confirm the map remains visible and responsive.
5. Inspect browser console for iframe/embed/CSP/mixed-content/layout errors.
6. If the map is blank, capture the map container markup/classes and computed size.
```

## Expected UX / Visual Behavior

```text
[ ] Map is visible on desktop.
[ ] Map is visible on mobile.
[ ] Map container has stable height and does not collapse.
[ ] Map does not overflow horizontally.
[ ] Nearby text/buttons are not overlapped by the map.
[ ] Console has no serious map/embed/theme errors.
```

## Pass Criteria

```text
[ ] Desktop map visible.
[ ] Mobile map visible.
[ ] No horizontal overflow.
[ ] No zero-height or blank map container.
[ ] Console/log issues are clean or documented as non-blocking.
```

## Fail Criteria

```text
[ ] Map is not visible.
[ ] Map container collapses to zero or near-zero height.
[ ] Map iframe/embed is blocked by browser, CSP, mixed content, or markup issue.
[ ] Map causes horizontal scroll or overlaps adjacent content.
[ ] Serious console errors appear.
```

## Evidence To Report Back

```text
Result: PASS / FAIL / BLOCKED
Target URL:
Desktop screenshot:
Mobile screenshot:
Editor/block markup screenshot:
Map container computed width/height:
Console/log notes:
Page source notes:
```
