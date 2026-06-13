# Header Actions And B2B Search 0.12.0 — Onsite Test

Target URL/page:

- WP Admin: `SKVN Marine -> Header`
- Frontend: homepage, product/category pages, and `/?s=<keyword>&skvn_search_target=<target>`

Setup/preconditions:

- Deploy updated `skvn-marine` theme and `skvn-marine-blocks` plugin to the onsite WordPress site.
- Do not rely on local UI runtime for this pass.
- WooCommerce should be active before testing product search.
- At least one product has a matching product category, product tag, or title keyword.
- At least one post has a matching category, post tag, or title keyword.

Test steps:

1. Open `SKVN Marine -> Header`.
2. Confirm settings exist for header actions, product search, post/site search, default target, Contact button, Request Quote button, and layout.
3. Save with `Header actions enabled` off and confirm the frontend header remains unchanged.
4. Enable header actions, product search, post/site search, Contact, and Request Quote.
5. Set Contact URL to `/contact/` and Request Quote URL to `/request-a-quote/`.
6. Reload desktop frontend and confirm actions render inside the existing GeneratePress header shell.
7. Reload mobile frontend and confirm the GeneratePress mobile menu/toggle still works.
8. Search with target `Products` using a product category/tag/title keyword.
9. Confirm the search results page shows a `Products` section with product cards and quote CTAs.
10. Search with target `Articles` using a post category/tag/title keyword.
11. Confirm the search results page shows a `Related articles` section.
12. Search with target `All site`.
13. Confirm the page separates `Products` and `Related articles`.
14. Disable product search, save, and confirm product target/results are no longer exposed by the header UI.
15. Disable post/site search, save, and confirm article target/results are no longer exposed by the header UI.

Expected UX/visual behavior:

- GeneratePress remains the header shell.
- No GeneratePress parent theme files are edited.
- Header actions are governed by preset settings, not raw classes.
- Search target is explicit: `Products`, `Articles`, or `All site`.
- Product results prioritize taxonomy/title matches before content fallback.
- Article results prioritize taxonomy/title matches before content fallback.
- No Elastic/OpenSearch, custom SQL cache table, or custom query cache is required.

Pass/fail criteria:

- PASS if admin settings save, header actions render only when enabled, mobile menu remains usable, and search results separate product/article sections by target.
- FAIL if the header breaks navigation, settings do not persist, search target is ambiguous, fallback footer/header settings disappear, or products/articles are mixed into one default archive.

Evidence human should report back:

- Screenshot of `SKVN Marine -> Header`.
- Desktop header screenshot with actions enabled.
- Mobile header screenshot with menu opened and actions visible or wrapped safely.
- Search results screenshots for `Products`, `Articles`, and `All site`.
- Any console errors, PHP warnings, admin save failures, or broken links.
