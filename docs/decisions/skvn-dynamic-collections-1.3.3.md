# SKVN Dynamic Collections 1.3.3

Status: working decision.
Milestone: V1 / 1.3.3.
Owner layer: `skvn-marine-blocks` plugin.

## Goal

Add governed dynamic collection blocks for product and post surfaces:

- Product Grid
- Product Carousel
- Post Grid
- Post Carousel

The editor-facing UX exposes four clear inserter choices, but the runtime
architecture uses two content-type owners:

```text
skvn-marine/product-collection
  layout: grid | carousel

skvn-marine/post-collection
  layout: grid | carousel
```

## Core Decision

Use custom SKVN dynamic blocks, but use native WordPress and WooCommerce data
APIs under the hood.

Product collections query WooCommerce products through WooCommerce/native WordPress
APIs. Post collections query posts through WordPress APIs. The plugin owns the
block attributes, editor controls, dynamic PHP render callbacks, card markup,
baseline CSS, carousel runtime, accessibility, and quote/action behavior.

Do not depend directly on the experimental WooCommerce Product Collection
extension API as the source of truth for SKVN block behavior in V1. The SKVN
contract may learn from the native Woo/Core model, but it must stay stable if a
Woo experimental API changes.

## Why This Approach

Custom SKVN dynamic blocks give the project control over B2B card markup,
Request Quote context, governed controls, carousel accessibility, and portable
baseline styling.

Native APIs keep the data model correct:

- Products remain WooCommerce products.
- Product categories and tags remain WooCommerce taxonomies.
- Posts remain WordPress posts.
- No direct custom SQL is introduced.
- No custom product CPT is introduced.

This balances portability and ecosystem alignment. SKVN owns the presentation
and editor contract; WooCommerce and WordPress own the source data.

## Saved Content Contract

Blocks store query and presentation attributes only. They must not store a
snapshot of product or post cards in saved markup.

Saved attributes may include:

```json
{
  "layout": "grid",
  "categories": ["grouper"],
  "tags": ["featured"],
  "relation": "OR",
  "orderMode": "newest",
  "itemsToShow": 10,
  "responsivePreset": "3-2-1",
  "showImage": true,
  "showPrice": false,
  "actionMode": "quote"
}
```

Frontend output is rendered dynamically from current WordPress/WooCommerce data.
This avoids stale product names, old images, invalid product links, wrong stock
state, and broken quote context.

## Inserter UX

Expose four inserter variations:

```text
SKVN Product Grid
SKVN Product Carousel
SKVN Post Grid
SKVN Post Carousel
```

Under the hood:

```text
SKVN Product Grid
  -> skvn-marine/product-collection, layout=grid

SKVN Product Carousel
  -> skvn-marine/product-collection, layout=carousel

SKVN Post Grid
  -> skvn-marine/post-collection, layout=grid

SKVN Post Carousel
  -> skvn-marine/post-collection, layout=carousel
```

Allow switching Grid <-> Carousel inside the same content type. Do not allow
direct Product <-> Post switching inside one block because query schema, card
fields, taxonomy controls, and actions differ.

## Editor Panels

Inspector controls are grouped as:

```text
Content | Query | Layout | Card | Actions | Advanced
```

- Content: heading, intro text, accessible label.
- Query: taxonomy filters, relation, item count, order mode.
- Layout: grid/carousel mode, responsive preset, rows, equal height, image ratio.
- Card: image, price/date, taxonomy badges, SKU, stock, excerpt.
- Actions: view, quote, read more, archive/custom URL.
- Advanced: anchor, class name, technical fallback options.

Do not build a global editor `Query` tab in V1 / 1.3.3. Keep the UI local to
the block Inspector so the feature can later move into a mature Core Control
surface without coupling the first implementation to editor-wide UI.

## Query MVP

Product and post collections support:

- Multi-select category.
- Multi-select tag.
- Taxonomy relation: `AND` or `OR`.
- Order modes:
  - Featured
  - Newest
  - Manual order
  - Shuffle balanced pool
- Product Grid and Post Grid: maximum 5 columns and maximum 3 rows.
- Product Carousel and Post Carousel: maximum 10 items.

Deferred from 1.3.3:

- Include/exclude specific product or post IDs.
- Product attribute query.
- Faceted/AJAX filtering.
- Group by taxonomy.
- Taxonomy Collections admin.
- Attribute/tag thumbnail manager.
- Universal custom post type support.

## Shuffle Balanced Pool

Do not use database-level `ORDER BY RAND()` in V1.

When `Shuffle balanced pool` is selected:

```text
If total matching items <= 30:
  Query up to 30 IDs for the current filters.
  Shuffle in PHP.
  Slice to the block item limit.

If total matching items > 30:
  Build a pool of up to 60 IDs:
    - 15 title ASC
    - 15 title DESC
    - 15 newest
    - 15 oldest
  Deduplicate by ID.
  Shuffle the pool in PHP.
  Slice to the block item limit.
```

The result is not true whole-database random. It is a host-friendlier balanced
shuffle intended to make small marketing collections feel less static.

If page caching is active, the visible order may remain stable until cache
refresh. Editor preview should use deterministic shuffle behavior so preview
does not jump on every control change.

## Layout Contract

Responsive presets use the format:

```text
desktop-tablet-mobile
```

Examples:

```text
1-1-1
2-1-1
3-2-1
4-2-1
5-3-1
```

For grids, the preset controls columns per breakpoint. For carousels, it
controls slides per view per breakpoint.

Recommended defaults:

- Product Grid: `3-2-1`
- Product Carousel: `4-2-1`
- Post Grid: `3-2-1`
- Post Carousel: `3-2-1`

Card options:

- Equal height: on/off, default on.
- Image ratio: `1:1`, `4:3`, `3:2`, `16:9`, `auto`; default `4:3`.
- CTA alignment: left, center, right, stretch; default left.

If item count is less than or equal to visible slides, carousel loop and autoplay
are disabled and the carousel behaves as a static row.

## Actions

Product action modes:

- View Product
- Request Quote
- Both
- Custom URL

Post action modes:

- Read More
- Custom URL

When Product action mode is Request Quote or Both, the generated quote URL must
preserve product context:

- `product_id`
- `product_sku`
- `product_name`
- `product_url`
- `source_url`

Custom URL may point all product cards to one destination. If quote context is
enabled, product context should still be appended safely to the configured URL.

## Card Fields

Product cards may show:

- Image
- Name
- Price
- Category
- Tags
- SKU
- Stock
- Primary action
- Secondary action

Post cards may show:

- Featured image
- Title
- Date
- Author
- Category
- Tags
- Excerpt
- Read More action

Taxonomy badges may be:

- Display only
- Links to the matching archive

Do not implement in-place filtering when a badge is clicked in 1.3.3.

## Image Fallbacks

Product fallback order:

1. Product image.
2. WooCommerce placeholder image.
3. SKVN collection fallback placeholder.

Post fallback order:

1. Featured image.
2. SKVN collection fallback placeholder.

Alt text is not an image fallback. It remains text alternative for an image.

The first implementation may ship a default plugin fallback. The code should
leave a clean option/filter path for a later branded fallback image setting,
for example `skvn_collection_fallback_image_id`.

## Carousel Runtime

Carousels reuse only the relevant Slider foundation:

- Swiper adapter.
- Pointer/focus/document visibility pause policy.
- Reduced-motion behavior.
- Accessible arrows/pagination patterns where compatible.

Do not render Product/Post cards as `skvn-marine/slide` InnerBlocks. Dynamic
collections own their own card markup. Grid layouts do not load carousel runtime.

Autoplay:

- Default off.
- Optional on/off control.
- Delay choices from 3 to 10 seconds, step 1 second.
- Reduced motion disables autoplay.
- If item count is not enough to move, autoplay is disabled.

If autoplay is enabled, render a visible Pause/Play control.

## Accessibility

Each carousel needs an accessible name. Prefer the visible heading. If there is
no visible heading, require an editor-provided carousel label.

Best practices:

- Use real buttons for arrows and pagination.
- Pagination buttons include labels such as `Go to slide 2`.
- Pause autoplay on hover, keyboard focus, user interaction, and document hidden.
- Do not trap focus inside the carousel.
- Do not announce every automatic slide change to screen readers.
- Announce user-triggered slide changes only when useful and not noisy.
- Respect `prefers-reduced-motion`.
- Do not run carousel autoplay in the editor.

## Performance And Compatibility

Query:

- Hard-limit item counts.
- Never query unlimited items.
- Do not use `ORDER BY RAND()`.
- Use ID-only pool queries where possible, then fetch/render final selected IDs.
- Use `no_found_rows` when pagination is not needed.
- Rely on WordPress/WooCommerce object cache first. Do not add custom transient
  caching in 1.3.3 unless onsite evidence proves it is needed.

Dependencies:

- Product collections must not fatal when WooCommerce is inactive.
- Product inserter variations may be hidden or show a clear admin/editor notice
  when WooCommerce is inactive.
- Post collections do not depend on WooCommerce.

Editor preview:

- Debounce query changes.
- Show loading, empty, and error states.
- Use placeholder preview data if a live preview is not available.
- Do not run carousel autoplay in Gutenberg.
- Keep preview item count modest.

## Theme Pattern Boundary

The plugin ships the engine and baseline UI. The theme may later ship patterns
as recommended recipes after the plugin has been implemented and tested.

Examples:

```text
Homepage Featured Products
  Product Collection
  layout: carousel
  action: Request Quote
  responsive: 4-2-1

Marine Knowledge Articles
  Post Collection
  layout: grid
  show date + excerpt
```

Theme patterns must not be required for the blocks to work. If the theme is
changed, the plugin blocks should remain readable, accessible, and functional.

## Deferred To 2.x.x Or Later

- Product Taxonomy Collections admin.
- `Products -> Taxonomy Collections`.
- Thumbnail metadata for attribute/tag terms.
- Grouped taxonomy card navigation.
- Faceted filters.
- AJAX load more.
- Archive builder.
- Technical product/specification card.
- Universal custom post type collection block.
