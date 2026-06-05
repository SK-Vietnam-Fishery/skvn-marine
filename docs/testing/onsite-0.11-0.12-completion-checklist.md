# Onsite Completion Checklist — 0.11.0 + 0.12.0

Purpose:

- Confirm the deployed `0.11.0` admin/footer appearance work and `0.12.0` header/search work on the onsite WordPress site.
- Use this checklist before asking an agent to move the project to `V1 / 1.0.0 — V1 Launch Ready`.
- Local UI runtime is intentionally out of scope for this pass because local UI loading is slow; use source checks plus onsite evidence.

Target surfaces:

- WP Admin: `SKVN Marine -> Footer`
- WP Admin: `SKVN Marine -> Header`
- Frontend: homepage, at least one short page, product/category surfaces, search results page.

Deploy preconditions:

- Deploy current `build/skvn-marine.zip` and `build/skvn-marine-blocks.zip` or equivalent built theme/plugin folders.
- Confirm `skvn-marine` child theme is active.
- Confirm `skvn-marine-blocks` plugin is active.
- Confirm GeneratePress parent theme is installed and active as the parent.
- Confirm WooCommerce is active before testing product search.
- Do not activate Polylang for this pass.
- Do not expose or configure n8n.

## A. Admin Menu And Footer

1. Open `SKVN Marine -> Footer`.
2. Confirm `Settings -> SKVN Footer` is no longer the primary admin location.
3. Confirm the Footer page field still stores the existing `skvn_footer_page_id` selection.
4. Select a published footer page and save.
5. Select `Default`, save, and reload a frontend page.
6. Select `Deep navy`, save, and reload a frontend page.
7. Select `Trust blue`, save, and reload a frontend page.
8. Select `White`, save, and reload a frontend page.
9. Select `Fresh sky`, save, and reload a frontend page.
10. Confirm each preset affects `.skvn-footer-page`.
11. If the footer page starts with an outer `.skvn-site-footer` group, confirm that outer block also uses the selected preset.
12. On a short page, scroll to the bottom and confirm no mismatched white strip appears below the custom footer.
13. Clear the Footer page selection, save, and reload the frontend.
14. Confirm the default GeneratePress footer returns and is not affected by the selected preset.

Pass criteria:

- `SKVN Marine -> Footer` works.
- Existing footer page rendering still works.
- Background presets are safe preset choices only, not raw color input.
- GeneratePress fallback footer remains unaffected.

Evidence to report:

- Screenshot of `SKVN Marine -> Footer`.
- Screenshot of one dark footer preset.
- Screenshot of one light footer preset.
- Screenshot or note confirming default GeneratePress fallback after clearing footer page.
- Any console errors, PHP warnings, admin save failures, or visual mismatches.

## B. Header Settings

1. Open `SKVN Marine -> Header`.
2. Confirm the settings page exists under the same top-level `SKVN Marine` admin menu.
3. Confirm these controls exist:
   - Header actions enabled
   - Product search enabled
   - Post/site search enabled
   - Default search target
   - Contact button enabled
   - Contact button label
   - Contact button URL
   - Request Quote button enabled
   - Request Quote label
   - Request Quote URL
   - Header action layout
4. Save with `Header actions enabled` off.
5. Reload desktop and mobile frontend.
6. Confirm the frontend header remains unchanged while header actions are disabled.
7. Enable header actions.
8. Enable product search, post/site search, Contact, and Request Quote.
9. Set Contact URL to `/contact/`.
10. Set Request Quote URL to `/request-a-quote/`.
11. Save and reload desktop frontend.
12. Confirm actions render inside the existing GeneratePress header shell.
13. Reload mobile frontend.
14. Confirm the GeneratePress mobile menu button still works.
15. Confirm the header actions wrap safely and do not overlap logo, menu toggle, or navigation.

Pass criteria:

- Settings persist after save.
- Header actions render only when enabled.
- GeneratePress header shell and mobile navigation remain usable.
- Contact and Request Quote links are correct.

Evidence to report:

- Screenshot of `SKVN Marine -> Header`.
- Desktop header screenshot with actions enabled.
- Mobile header screenshot with menu closed.
- Mobile header screenshot with menu opened.
- Any console errors, PHP warnings, admin save failures, or broken links.

## C. Search Results

Setup:

- Pick one keyword that matches a product category/tag or product title.
- Pick one keyword that matches a post category/tag or post title.

Tests:

1. In `SKVN Marine -> Header`, enable product search and post/site search.
2. Search for the product keyword with target `Products`.
3. Confirm the results page shows a `Products` section.
4. Confirm product cards show product name, image when available, category/meta line, `View details`, and `Request Quote`.
5. Search for the post keyword with target `Articles`.
6. Confirm the results page shows a `Related articles` section.
7. Confirm article cards show title, category/meta, excerpt, and `Read more`.
8. Search for either keyword with target `All site`.
9. Confirm the results page separates `Products` and `Related articles`.
10. Disable product search and save.
11. Confirm product target/results are no longer exposed by the header UI.
12. Re-enable product search, disable post/site search, and save.
13. Confirm article target/results are no longer exposed by the header UI.

Pass criteria:

- Search target is explicit: `Products`, `Articles`, or `All site`.
- Product and article sections are not mixed into one default archive loop.
- Product search works through native taxonomy/title/content fallback behavior.
- Article search works through native taxonomy/title/content fallback behavior.
- No Elastic/OpenSearch or custom query cache is required.

Evidence to report:

- URL and screenshot for product-target search.
- URL and screenshot for article-target search.
- URL and screenshot for all-site search.
- Names of test keywords used.
- Any unexpected empty result, irrelevant result, console error, PHP warning, or broken CTA.

## D. Regression Sweep

1. Confirm no file from `wp-content/themes/generatepress/` was deployed from this repo.
2. Confirm external plugins were not copied into the source repo deploy package.
3. Confirm homepage still loads.
4. Confirm at least one WooCommerce product page still loads.
5. Confirm `/request-a-quote/` still loads.
6. Confirm `/quote-thank-you/` still loads if it exists onsite.
7. Confirm n8n remains unexposed.
8. Confirm no obvious layout overlap on mobile header/footer.

Pass criteria:

- No fatal errors.
- No broken header/footer on primary pages.
- Quote UI remains visually reachable.
- No out-of-scope quote data-flow, CFDB7, map, or n8n work is pulled into this pass.

Final report format:

```text
0.11/0.12 onsite pass result:
- Footer admin/settings:
- Footer frontend:
- Header admin/settings:
- Header desktop:
- Header mobile:
- Product search:
- Article search:
- All-site search:
- Regression sweep:
- Screenshots attached:
- Console/PHP warnings:
- Decision: approve closing 0.11/0.12? yes/no
```

## Final Onsite Result — 2026-06-05

Reported by human after launch:

```text
0.11/0.12 onsite pass result:
- Footer admin/settings: PASS
- Footer frontend: PASS
- Header admin/settings: PASS
- Header desktop: PASS
- Header mobile: PASS
- Product search: PASS
- Article search: PASS
- All-site search: PASS
- Regression sweep: PASS
- Console/PHP warnings: none reported
- Decision: approve closing 0.11/0.12? yes
```

V1 launch smoke:

```text
- Accessibility pass
- Mobile QA pass
- SEO/GEO structure pass
- Performance/asset loading review
- No GeneratePress parent-theme changes
- No external plugins committed to source repo
- Human approves V1 launch readiness
- n8n remains deferred/unexposed and is carried forward as a V1 -> V2 boundary review item.
```
