# VERSION 1.3.3 — Dynamic Product And Post Collections Planning

Status: future planning.
Decision source: `docs/decisions/skvn-dynamic-collections-1.3.3.md`.
Testing source: `docs/testing/onsite-dynamic-collections-1.3.3.md`.

## Goal

Implement two dynamic collection block foundations with four editor-facing
inserter choices:

```text
skvn-marine/product-collection
  variation: SKVN Product Grid
  variation: SKVN Product Carousel

skvn-marine/post-collection
  variation: SKVN Post Grid
  variation: SKVN Post Carousel
```

The blocks render live WordPress/WooCommerce data and do not save product or
post card snapshots into page content.

## Why This Milestone Exists

The earlier V1 product surface used WooCommerce native blocks and SKVN patterns.
That was enough for the first product sections, but the next B2B funnel needs
governed, reusable collection surfaces that can:

- show product and post collections with consistent SKVN cards;
- support product quote CTAs with product context;
- support grid and carousel layouts from one content-type foundation;
- avoid Elementor-like flat product lists that hide buyer navigation intent;
- remain portable if the theme changes.

## Scope

In scope:

- Custom dynamic SKVN blocks.
- Native WooCommerce/WordPress query APIs under the hood.
- Four inserter choices backed by two block types.
- Product and post query controls for category/tag, relation, item count, and
  order mode.
- `Shuffle balanced pool` order mode.
- Grid max 5 columns and max 3 rows.
- Carousel max 10 items.
- Responsive presets: `1-1-1`, `2-1-1`, `3-2-1`, `4-2-1`, `5-3-1`.
- Product card options for image, price, category/tag badges, SKU, stock, and
  actions.
- Post card options for image, date, author, category/tag badges, excerpt, and
  action.
- Product quote URL context.
- Baseline plugin CSS so blocks remain usable outside the SKVN theme.
- Carousel runtime reuse of Swiper adapter and shared pause/reduced-motion
  invariants.
- Onsite testing handoff with human visual/runtime evidence.

Out of scope:

- Product attribute query.
- Include/exclude specific IDs.
- Product Taxonomy Collections admin.
- Attribute/tag thumbnails.
- Group by taxonomy.
- Faceted filtering.
- AJAX load more.
- Archive builder.
- Technical product specs table/card.
- Universal CPT collection.
- Theme pattern implementation before plugin testing passes.

## Implementation Order

### Phase 0 — Contract And Structure

- Confirm final block names and attributes.
- Create shared collection type definitions and constants.
- Define shared responsive presets, image ratios, card field defaults, action
  modes, and order modes.
- Add PHP render scaffolding for product and post collections.
- Add WooCommerce inactive guards for product collection.

### Phase 1 — Post Collection Grid

- Implement `skvn-marine/post-collection`.
- Register `SKVN Post Grid` variation.
- Add Query, Layout, Card, Actions, and Advanced panels.
- Render dynamic PHP post cards with live post data.
- Add post category/tag filters, relation, order modes, and item limits.
- Add plugin baseline CSS for grid/cards.

Reason: posts are available in any WordPress install and are the simplest
foundation test before WooCommerce-specific behavior.

### Phase 2 — Product Collection Grid

- Implement `skvn-marine/product-collection`.
- Register `SKVN Product Grid` variation.
- Query products through WooCommerce/native APIs, no direct custom SQL.
- Render product cards with image, optional price, SKU, stock, taxonomy badges,
  and product actions.
- Add Request Quote action mode with product context query params.
- Add WooCommerce placeholder image fallback and plugin fallback path.

Reason: product cards carry the main B2B funnel behavior and should be validated
before carousel runtime is layered on top.

### Phase 3 — Shared Carousel Runtime

- Extract or reuse the carousel-only Swiper adapter and pause policy without
  making grid load carousel runtime.
- Register `SKVN Post Carousel` and `SKVN Product Carousel` variations.
- Add carousel layout controls, responsive slides-per-view presets, arrows,
  pagination, optional autoplay, delay `3-10s`, and visible Pause/Play when
  autoplay is enabled.
- Disable loop/autoplay when item count is less than or equal to visible slides.
- Do not run autoplay in the editor.

Reason: carousel should reuse proven Slider runtime rules but keep dynamic
collection card markup separate from `skvn-marine/slide`.

### Phase 4 — Performance, Compatibility, And Fallbacks

- Implement `Shuffle balanced pool` for product and post queries.
- Use ID-only pool queries where possible.
- Add loading, empty, and error preview states.
- Add WooCommerce inactive behavior.
- Ensure blocks do not fatal when terms/products/posts are removed.
- Verify output escaping and input sanitization.

### Phase 5 — Human Onsite QA And Fix Pass

- Build plugin assets.
- Human creates onsite test pages from `docs/testing/onsite-dynamic-collections-1.3.3.md`.
- Human reports screenshots, URLs, console notes, quote URL evidence, and
  mobile/desktop observations.
- Agent fixes source issues found during onsite QA.
- Human approves milestone completion.

## Attributes Draft

Common:

```text
layout: grid | carousel
heading
intro
accessibleLabel
categories: string[]
tags: string[]
relation: AND | OR
orderMode: featured | newest | manual | shuffle-balanced
itemsToShow
responsivePreset
showImage
imageRatio
equalHeight
badgeBehavior: display | archive-link
```

Product-specific:

```text
showPrice
showSku
showStock
showProductCategories
showProductTags
productActionMode: view | quote | both | custom
customActionUrl
appendQuoteContext
```

Post-specific:

```text
showDate
showAuthor
showPostCategories
showPostTags
showExcerpt
postActionMode: read | custom
customActionUrl
```

Carousel-specific:

```text
showArrows
showPagination
autoplay
autoplayDelay: 3000-10000
```

## Shuffle Balanced Pool

Use the decision contract:

```text
If total matching items <= 30:
  Query up to 30 IDs.
  Shuffle in PHP.
  Slice to the item limit.

If total matching items > 30:
  Query up to 60 IDs:
    15 title ASC
    15 title DESC
    15 newest
    15 oldest
  Deduplicate.
  Shuffle in PHP.
  Slice to the item limit.
```

Do not use SQL random ordering.

## Testing Responsibilities

Agent:

- Implement source.
- Run fast local syntax/build checks where available.
- Prepare exact onsite test checklist.
- Fix source defects from human evidence.

Human:

- Build/deploy to onsite WordPress if needed.
- Create or update test pages.
- Verify editor controls, frontend layout, quote URLs, responsive behavior, and
  console state.
- Provide screenshots, URLs, console notes, and pass/fail observations.

## Theme Follow-Up

After plugin implementation and onsite testing pass, the theme may add SKVN
patterns that preconfigure the blocks as best-practice recipes.

Theme patterns are not required for the blocks to work. Plugin baseline styling
must keep the blocks readable and functional without the SKVN theme.
