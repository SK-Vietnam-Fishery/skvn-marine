# Onsite Dynamic Collections Test — V1 / 1.3.3

Status: planned.
Target milestone: V1 / 1.3.3 — Dynamic Product And Post Collections.

## Target Pages

Create or update one onsite test page:

```text
Dynamic Collections Test 1.3.3
```

Optional second page for mobile-heavy review:

```text
Dynamic Collections Mobile Review 1.3.3
```

## Preconditions

- `skvn-marine-blocks` is active.
- WooCommerce is active for product collection tests.
- At least 6 products exist with product images or WooCommerce placeholder
  fallback available.
- At least 6 posts exist with a mix of featured images and missing images.
- Product categories/tags and post categories/tags exist.
- Request Quote page exists at the approved quote path.

## Test Blocks

Add these blocks to the test page:

```text
SKVN Product Grid
SKVN Product Carousel
SKVN Post Grid
SKVN Post Carousel
```

## Editor Tests

For each block:

- Confirm the block inserts without invalid-block recovery.
- Confirm controls are grouped as Content, Query, Layout, Card, Actions, and
  Advanced.
- Change category/tag filters and confirm preview updates or shows a clear
  loading/empty/error state.
- Switch Grid <-> Carousel inside the same content type if the UI exposes it.
- Confirm Product <-> Post switching is not offered as a destructive direct
  conversion.
- Confirm carousel autoplay does not run in the editor.

## Query Tests

Product Grid:

- Select one product category.
- Select one product tag if available.
- Test relation `OR`.
- Set columns to a normal preset such as `3-2-1`.
- Confirm no more than 3 rows and 5 columns are exposed.

Product Carousel:

- Limit items to 10 or fewer.
- Test `Shuffle balanced pool` if there are enough matching products.
- Confirm carousel item count does not exceed 10.

Post Grid:

- Select one post category.
- Toggle date, author, category, tags, and excerpt display.
- Confirm missing featured images use the fallback placeholder.

Post Carousel:

- Test newest and shuffle-balanced ordering.
- Confirm cards remain readable on desktop and mobile.

## Product Action Tests

For Product Grid and Product Carousel:

- Test View Product action.
- Test Request Quote action.
- Test Both action.
- If Custom URL is used, confirm all cards go to the configured destination.
- For quote actions, confirm generated URLs preserve:
  - `product_id`
  - `product_sku`
  - `product_name`
  - `product_url`
  - `source_url`

Evidence to report:

```text
Page URL:
Product clicked:
Generated quote URL:
Does quote URL include product context? yes/no
```

## Badge Tests

For product and post cards:

- Set badges to display-only.
- Set badges to archive-link if available.
- Confirm archive links navigate to the correct taxonomy archive.
- Confirm badge clicks do not attempt AJAX filtering in this milestone.

## Carousel Runtime Tests

For each carousel:

- Confirm arrows work.
- Confirm pagination works.
- Enable autoplay.
- Confirm visible Pause/Play control appears.
- Confirm hover pauses autoplay on desktop.
- Confirm keyboard focus pauses autoplay.
- Confirm tab hidden/document visibility pause works when practical.
- Confirm reduced-motion mode disables autoplay or avoids motion.
- Confirm when item count is less than or equal to visible slides, loop/autoplay
  do not create fake movement.

Evidence to report:

```text
Desktop screenshot:
Mobile screenshot:
Autoplay pause works on hover? yes/no
Autoplay pause works on focus? yes/no
Console errors? paste notes
```

## Hover / CTA Interaction Tests (new in 1.3.7 card styles + button hover compatibility)

See dedicated deep DevTools guide: `docs/testing/onsite-button-hover-1.3.7.md` (covers Core feature, specificity audit per gutenberg-block-extension-css-contract.md, slider nesting case, reduced-motion, editor preview).

Quick on-page checks for the new CTAs (quote CTA, catalog CTA, read-more, archive, pdf):

- Hover each CTA: background/color or underline changes as planned in 028 (navy→teal for CTAs, underline for links).
- Force `:hover` in DevTools on the CTA element → computed style matches 028 spec (e.g. `.skvn-collection-card__cta:hover { background: #0D9488; }`).
- Check transition animates (0.15s).
- Emulate reduced-motion → hover should be instant (no transition).
- `:focus-visible` (Tab or force) should show visible focus state.
- Inside Carousel: hover a CTA while auto-paused on container hover — CTA hover should still work independently.
- For any core/button placed inside a collection card or Slider slide on the page: if Core Button Hover feature enabled with custom colors, the custom hover must apply (vars + class) and win specificity over any slider hero or theme button rules. See DevTools Styles on forced hover: feature rule active, no strikethrough, computed color matches config. Compare to standalone button outside.

Evidence to report (add to main template):

```text
CTA hover matches 028 plan? (describe colors/transitions)
Reduced-motion on CTAs? yes/no
Core button inside slider/collection: custom hover applies? yes/no + winning rule note
DevTools screenshots for hover states attached?
```

## Responsive Tests

Desktop:

- Product Grid uses expected columns.
- Post Grid uses expected columns.
- Product Carousel visible cards match preset.
- Post Carousel visible cards match preset.

Tablet/mobile:

- Cards stack/read cleanly.
- CTA remains visible.
- Images keep the selected ratio.
- No horizontal overflow appears.
- Carousel controls remain tappable.

## Fallback Tests

- Product without image uses WooCommerce placeholder or SKVN fallback.
- Post without featured image uses SKVN fallback.
- Empty query shows a clear editor/admin-safe empty state.
- Frontend empty state is not confusing to visitors.

## Dependency Test

If safe on a staging or local environment only:

- Temporarily deactivate WooCommerce.
- Confirm Product Collection blocks do not fatal.
- Confirm Post Collection blocks still work.
- Reactivate WooCommerce.

Do not perform this test on production unless the human explicitly chooses a
safe maintenance window.

## Pass Criteria

- All four inserter choices insert cleanly.
- Editor controls are understandable and do not require raw class input.
- Frontend output matches configured query and card options.
- Product quote links preserve context.
- Carousel controls are keyboard/touch usable.
- Autoplay respects pause rules and reduced motion.
- No fatal errors.
- No invalid block recovery.
- No major console errors.
- Human approves the milestone.
