# Footer Appearance Settings 0.11.0 — Onsite Test

Target URL/page:

- WP Admin: `SKVN Marine -> Footer`
- Frontend: any page where the selected custom footer page renders.

Setup/preconditions:

- `skvn-marine-blocks` is active.
- `skvn-marine` child theme is active.
- At least one published WordPress page exists for footer content.
- The footer page content may start with an outer Group block using class `skvn-site-footer`.

Test steps:

1. Open `SKVN Marine -> Footer` in wp-admin.
2. Confirm `Settings -> SKVN Footer` is no longer the primary settings location.
3. Select a published Footer page.
4. Select each Footer background preset once: `Default`, `Deep navy`, `Trust blue`, `White`, `Fresh sky`.
5. Save after each preset and reload a frontend page.
6. Confirm the custom footer page renders for the valid selected page.
7. Confirm `.skvn-footer-page` uses the selected footer background.
8. If the footer page starts with `.skvn-site-footer`, confirm the outermost `.skvn-site-footer` uses the same selected background.
9. Scroll to the bottom of a short page and confirm viewport space below the footer uses the same selected background.
10. Clear the Footer page selection, save, and reload the frontend.
11. Confirm the default GeneratePress footer appears and the selected footer background preset no longer affects it.

Expected UX/visual behavior:

- The admin menu shows `SKVN Marine -> Footer`.
- Editors choose a preset, not a raw color.
- Existing footer page selection behavior remains unchanged.
- Background presets apply only while a valid custom footer page is active.
- The normal page area stays white.

Pass/fail criteria:

- PASS if all expected behavior above is confirmed on desktop and mobile widths.
- FAIL if the old settings menu is still the primary location, invalid presets are saved, fallback footer is affected, the custom footer loses its selected background, or the page background changes outside the footer area.

Evidence human should report back:

- Screenshot of `SKVN Marine -> Footer`.
- Screenshot of frontend footer with one dark preset and one light preset.
- Screenshot or note confirming fallback GeneratePress footer after clearing the selected page.
- Any console errors, PHP warnings, or save failures.
