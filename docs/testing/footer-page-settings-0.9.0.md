# Footer Page Settings Test — 0.9.0

Status:

```text
PASS_ONSITE_REPORTED_BY_HUMAN
```

Latest human feedback:

```text
2026-06-05: Footer onsite testing is complete. Treat the 0.9.0 footer selected-page render, default GeneratePress fallback, and invalid-ID fallback/mismatch documentation debt as closed for V1 / 0.10.0.
```

Purpose:

```text
Verify on the online site that the SKVN Marine Blocks plugin stores a selected published footer page in `skvn_footer_page_id`, and that the SKVN Marine theme renders that page through GeneratePress' `generate_footer` surface with the default GeneratePress footer as fallback.
```

Local runtime note:

```text
Human asked not to run local runtime smoke testing for 0.9.0 because local runtime is slow and token-expensive. Use this checklist for online/onsite evidence instead.
```

Deferred note:

```text
Human approved moving from 0.9.0 to 0.10.0 on 2026-06-03 while carrying this online footer settings test debt to V1 / 0.10.0.
When V1 / 0.10.0 becomes current, run this checklist before closing onsite test debt.
```

## Preconditions

```text
[x] `skvn-marine` child theme is active.
[x] `skvn-marine-blocks` plugin is active.
[x] GeneratePress parent theme is installed and untouched.
[x] A published WordPress page exists for testing the reusable footer.
```

## Test Setup

Create or use a published page:

```text
Title: SKVN Footer Test 0.9.0
Content marker: SKVN_FOOTER_TEST_090
```

Open:

```text
WP Admin → Settings → SKVN Footer
```

Record:

```text
[ ] Online site URL:
[ ] Tester:
[ ] Date/time:
[ ] Browser:
[ ] Deployed source includes 0.9.0 footer settings code.
```

## Test Case 1 — Selected Footer Page Renders

Steps:

```text
1. Select the published `SKVN Footer Test 0.9.0` page.
2. Save settings.
3. Open the frontend homepage or any public page.
4. Search the page source or visible footer for `SKVN_FOOTER_TEST_090`.
```

Expected:

```text
[ ] Option `skvn_footer_page_id` stores the selected page ID.
[ ] The selected page content appears in the footer.
[ ] Default GeneratePress footer widgets/site-info do not duplicate under the selected footer page.
[ ] No PHP warning or fatal error appears.
```

## Test Case 2 — Fallback Footer Works

Steps:

```text
1. Return to Settings → SKVN Footer.
2. Select `Use default GeneratePress footer`.
3. Save settings.
4. Reload the frontend page.
```

Expected:

```text
[ ] Option `skvn_footer_page_id` stores `0`.
[ ] `SKVN_FOOTER_TEST_090` no longer appears.
[ ] Default GeneratePress footer returns.
[ ] No PHP warning or fatal error appears.
```

## Test Case 3 — Invalid Page ID Fallback

Steps:

```text
1. Force `skvn_footer_page_id` to an invalid, trashed, draft, private, or non-page post ID.
2. Reload the frontend page.
```

Expected:

```text
[ ] Invalid value is sanitized back to `0` when saved through settings.
[ ] Theme does not render draft/private/non-page content.
[ ] Default GeneratePress footer remains the fallback.
```

## Evidence To Report

```text
Result: PASS / FAIL / BLOCKED
Tester:
Date/time:
Site URL:
Footer test page ID:
Selected footer renders: YES / NO
Fallback footer works: YES / NO
Invalid ID fallback works: YES / NO
Frontend screenshot:
Settings screenshot:
Console/log/PHP notes:
```
