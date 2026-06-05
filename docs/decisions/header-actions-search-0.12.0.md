# Header Actions And B2B Search — 0.12.0 Planning Contract

Date: 2026-06-04
Status: Implemented for onsite testing in V1 / 0.12.0.

## Scope

This document records the agreed direction, implementation research, and source contract for the V1 / 0.12.0 header enhancement and B2B search experience.

Do not implement this in V1 / 0.10.0 or 0.11.0. 0.11.0 remains focused on the SKVN Marine admin menu and footer appearance settings.

Current milestone:

```text
0.12.0 — SKVN Header Actions And B2B Search
```

## Research Notes

Research date: 2026-06-05.

Primary sources reviewed:

- GeneratePress hook docs: `https://docs.generatepress.com/article/generate_after_header_content/`
- GeneratePress hooks index: `https://docs.generatepress.com/collection/hooks/`
- WordPress admin menu API: `https://developer.wordpress.org/reference/functions/add_menu_page/`
- WordPress submenu API: `https://developer.wordpress.org/reference/functions/add_submenu_page/`
- WordPress query preflight hook: `https://developer.wordpress.org/reference/hooks/pre_get_posts/`
- WordPress search parsing internals: `https://developer.wordpress.org/reference/classes/wp_query/parse_search/`

Findings:

- GeneratePress exposes `generate_after_header_content` before the closing `.inside-header` element, so SKVN can add a governed action group inside the existing header shell without replacing GeneratePress header/navigation code.
- GeneratePress documents a stable hook collection, supporting the V1.x decision to use hooks instead of editing the parent theme or building a full header template.
- WordPress admin settings can live under a top-level admin menu using `add_menu_page()` and `add_submenu_page()`, matching the 0.11.0 `SKVN Marine` admin surface and the 0.12.0 `SKVN Marine -> Header` submenu.
- WordPress search parsing supports title-oriented search behavior through query search columns. That supports the 0.12.0 strategy of taxonomy-first and title-first native queries before optional content fallback.
- `pre_get_posts` is available for main-query changes, but 0.12.0 uses a governed `search.php` template with explicit `WP_Query` sections instead, because the product/article split is a display contract and should not force the default search archive into one mixed loop.

Implementation decisions from research:

- Header actions render through the child theme at `generate_after_header_content`; no GeneratePress parent file is edited.
- Plugin-owned settings are stored in one option, `skvn_header_actions`, and sanitized by the plugin settings module.
- Theme defensively sanitizes the same option before frontend render so the frontend does not depend on plugin helper functions being loaded in a specific order.
- Header actions are disabled by default for safe deploy; onsite testing enables them through `SKVN Marine -> Header`.
- Search result rendering is governed by `search.php` and separates Products from Related articles.
- Phase 1 search uses native WordPress/WooCommerce taxonomy/title/content queries, no Elastic/OpenSearch, no custom SQL table, and no custom transient/cache registry.

## Header Direction

Use **Header Actions**, not a full Header Builder.

The GeneratePress header remains the shell for V1.x. SKVN adds governed action controls and rendering around the existing GeneratePress header/navigation surface.

Target action group:

```text
[Logo] [Menu] [Product Search] [Post/Site Search] [Contact] [Request Quote]
```

The exact visual layout may adapt by breakpoint. On mobile, search/actions may collapse into a compact panel or icon-triggered area.

## Why Not Full Header Builder Yet

A full Gutenberg header template is deferred because header replacement is riskier than footer replacement:

- Header owns primary navigation and site identity.
- Mobile menu behavior and focus management are fragile.
- Search forms require keyboard/focus accessibility.
- A broken header affects every page and every buyer flow.
- GeneratePress already provides stable navigation behavior.

Future full header template/page rendering may be reconsidered after V1 launch or a later base-theme decision.

## Ownership

Theme `skvn-marine` owns:

- Hooking into GeneratePress header/navigation surfaces.
- Header action visual layout and responsive CSS.
- `skvn-*` classes, tokens, spacing, button styles, and editor/frontend visual contract.

Plugin `skvn-marine-blocks` owns:

- Future `SKVN Marine → Header` admin settings UI.
- Storing header action options.
- Sanitizing labels, URLs, and enabled states.

Search logic uses native WordPress/WooCommerce query behavior in phase 1. Do not add a custom search engine, Elastic/OpenSearch, or custom SQL cache table in phase 1.

## Header Settings Draft

Future admin surface:

```text
SKVN Marine → Header
```

Candidate settings:

```text
Header actions enabled
Product search enabled
Post/site search enabled
Default search target: products / posts / all site
Contact button enabled
Contact button label
Contact button URL
Request Quote button enabled
Request Quote label
Request Quote URL
Header action layout: compact / full
```

Request Quote button should be optional. Initial URL can stay simple:

```text
/request-a-quote/
```

Do not append product context in the first header action phase unless explicitly approved.

## Search UI Direction

Product search and post/site search may ship together, but they should be separate intents.

Preferred UI:

```text
[ Target: Products | Articles | All site ] [ Search keyword ] [ Search icon ]
```

Submit behavior:

```text
Products → product-focused query
Articles → post/resource-focused query
All site → broader site query
```

The header may expose one compact search UI with a target selector instead of two separate search boxes, because header space is limited.

## B2B Search Results Experience

Do not rely on the default WordPress or WooCommerce archive layout as the primary B2B search result experience.

Future search results page should be governed:

```text
"[Search term]" results

Products
[Product grid/cards]

Related articles
[Post/resource cards]
```

Product cards prioritize buyer actions:

- Product name
- Image
- Category/spec short line
- View details
- Request Quote CTA

Related article cards prioritize education/trust:

- Title
- Excerpt
- Category/tag
- Read more

## Query Strategy

Phase 1 search should be governed and lightweight. Do not default to full-content search.

Priority order:

```text
1. Product category / product tag match
2. Product title match
3. Related post tag / category match
4. Post title match
5. Full content search only as optional fallback
```

Product tags/categories are treated as a search governance layer. If a product should be discoverable by a keyword, that keyword should be intentionally represented in product tags/categories or title.

This avoids generic full-content matches that can pollute B2B buyer search results.

## Search Engine And Caching

Do not integrate Elastic/OpenSearch in phase 1.

Do not implement custom search query caching in phase 1:

- No custom SQL cache table.
- No custom transient registry.
- No pre-warm cache workflow.

Rely on:

- Native WordPress/WooCommerce queries.
- Search query shape constrained to taxonomy/title.
- Available persistent object cache if the server provides one, such as SQLite Object Cache or Redis.

Object cache is an optimization layer, not a dependency. Search must still return correct results if object cache is flushed or disabled.

Revisit custom cache or search engine only if measured search performance becomes a real issue.

## Acceptance Draft

- [ ] Human confirms exact target milestone before implementation.
- [ ] GeneratePress header remains the shell.
- [ ] No GeneratePress parent files are edited.
- [ ] `SKVN Marine → Header` settings are documented before code.
- [ ] Product search can be enabled/disabled.
- [ ] Post/site search can be enabled/disabled.
- [ ] Request Quote header button can be enabled/disabled.
- [ ] Contact header button can be enabled/disabled.
- [ ] Search target is explicit: products, articles, or all site.
- [ ] B2B search results page separates Products from Related articles.
- [ ] Product matching uses product tags/categories/title before content fallback.
- [ ] Related article matching uses post tags/categories/title before content fallback.
- [ ] No Elastic/OpenSearch dependency is added in phase 1.
- [ ] No custom query cache or SQL cache table is added in phase 1.
- [ ] Header mobile behavior does not break GeneratePress navigation.
- [ ] Keyboard/focus behavior is reviewed for search and buttons.
