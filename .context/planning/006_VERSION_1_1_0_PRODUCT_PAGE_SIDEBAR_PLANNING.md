# Version 1.1.0 Planning — Product Page & Product Sidebar

> Planning candidate for making WooCommerce single product pages feel like professional B2B quote-first product pages.
> Load this file when planning single product page layout, Woo product sidebar, same-tag related products, product metadata display, or product quote UX after V1 launch readiness.

---

## Status

Status: **FUTURE CANDIDATE**
Primary target: **V1 / 1.1.0**

This file does not change the current milestone. Current milestone remains managed by `.context/MILESTONES.md`.

---

## Problem

The default WooCommerce single product page can look like a retail/blog page when it shows:

- Generic blog sidebar widgets such as Search, Recent Posts, and Recent Comments.
- Retail review tabs before the B2B product data is mature.
- `SKU: N/A` or `Uncategorized` metadata.
- Small default Add to Cart / Request Quote placement.
- Missing or unbranded product imagery.

For SKVN Marine, product pages should support B2B seafood buyers who need product context, export specs, packing notes, and a clear quote path.

---

## Goal

Create a quote-first single product page system that keeps WooCommerce as the product data source while replacing the default retail/blog feel with a professional B2B product detail experience.

Expected buyer-facing structure:

```text
Product media / gallery
Product title
Buyer-facing short description
Key specs from WooCommerce attributes
Request a Quote CTA
Export / packing context
Product sidebar with related products from same product tag/category
```

---

## Boundary

Theme `skvn-marine` owns:

- Single product visual layout.
- WooCommerce visual overrides.
- Product sidebar presentation.
- `skvn-*` CSS classes and editor/frontend visual parity.

WooCommerce owns:

- Product records.
- Product categories.
- Product tags.
- Product attributes.
- Product image/gallery.
- SKU and product metadata.

Plugin `skvn-marine-blocks` owns only future custom blocks if native WooCommerce/product templates are proven insufficient.

Rules:

- Do not edit GeneratePress parent files.
- Do not create a custom product CPT.
- Do not replace WooCommerce native product data with theme-owned data.
- Do not custom-code quote form handling.
- Do not expose cart/checkout as the primary flow.
- Do not rely on hover-only product CTAs on mobile.
- Use native WooCommerce taxonomy queries before adding new dependencies.

---

## Product Data Requirements

Before implementation, product data should be cleaned enough that the template has real content to present.

Required for a credible product page:

- Product title.
- Featured image or branded placeholder.
- Product category other than `Uncategorized`.
- At least one useful product tag or category for related products.
- Short description.
- Core attributes such as Origin, Size / Grade, Packaging, Certification, Processing Type, Freezing Type, Storage Temperature, or Shelf Life.

Hide or de-emphasize:

- `SKU: N/A`.
- Empty attributes.
- Empty review state.
- Blog widgets unrelated to products.

---

## Product Sidebar Direction

The sidebar should be product-specific, not the default WordPress blog sidebar.

Candidate sidebar blocks:

```text
Request a Quote card
Related products from same product_tag
Fallback related products from same product_cat
Packing/export support note
Certification/trust note
```

Related products logic:

1. Read current product's `product_tag`.
2. Query published products sharing at least one tag.
3. Exclude current product.
4. If no tag match exists, fallback to `product_cat`.
5. If no related products exist, hide the related products section rather than showing filler.

Implementation should use `WP_Query` or WooCommerce APIs with sanitized IDs and escaped output. Do not use custom SQL.

---

## Single Product Layout Direction

Recommended desktop layout:

```text
Main column:
  Breadcrumb
  Product hero: media + summary
  Specs / attributes panel
  Packing and export notes

Sidebar:
  Sticky quote card
  Same-tag related products
  Support/trust note
```

Recommended mobile layout:

```text
Product image
Title
Short description
Request a Quote CTA
Key specs
Related products
Support note
```

Mobile rules:

- CTA must remain visible without hover.
- Sidebar content stacks below core product info.
- No horizontal scroll.
- Product cards and button text must wrap cleanly.

---

## Visual Contract

Candidate class families:

```text
skvn-product-detail
skvn-product-detail__hero
skvn-product-detail__media
skvn-product-detail__summary
skvn-product-detail__meta
skvn-product-detail__specs
skvn-product-sidebar
skvn-product-sidebar__quote
skvn-product-sidebar__related
skvn-product-sidebar__card
```

These classes are not active until implemented in theme CSS. If implementation adds them, update `docs/standards/site-branding-guideline.md` and theme CSS in the same task.

Reuse existing classes where practical:

- `skvn-button`
- `skvn-button--primary`
- `skvn-card`
- `skvn-section__eyebrow`
- `skvn-section__heading`
- `skvn-section__lead`

---

## Implementation Phases

### Phase 0 — Data Audit

Acceptance:

- [ ] Product categories are not `Uncategorized`.
- [ ] Product tags exist for same-tag related sidebar behavior.
- [ ] Product attributes needed for B2B specs are defined.
- [ ] Featured images or branded placeholders exist.
- [ ] Products with missing SKU are identified.

### Phase 1 — Remove Retail/Blog Friction

Acceptance:

- [ ] Blog sidebar widgets are not shown on single product pages.
- [ ] Reviews are hidden or moved out of the primary product flow unless human explicitly wants reviews.
- [ ] `SKU: N/A` is not displayed.
- [ ] Quote CTA remains the primary product action.
- [ ] GeneratePress parent remains untouched.

### Phase 2 — Product Sidebar

Acceptance:

- [ ] Product sidebar renders a Request a Quote card using the current product ID.
- [ ] Related products are selected by same `product_tag`.
- [ ] Sidebar falls back to same `product_cat` if no tag-related products exist.
- [ ] Empty related state is hidden cleanly.
- [ ] Output is escaped and product IDs are sanitized.
- [ ] No custom SQL is used.

### Phase 3 — Product Detail Visual System

Acceptance:

- [ ] Product hero layout looks professional on desktop and mobile.
- [ ] Product attributes render as a readable specs panel.
- [ ] Product image placeholder is branded if real media is missing.
- [ ] Product CTA uses SKVN visual classes/tokens.
- [ ] Product page does not look like a blog post or retail cart page.
- [ ] CSS uses theme-owned `skvn-*` classes.

### Phase 4 — Runtime QA

Acceptance:

- [ ] Test with a product that has tags.
- [ ] Test with a product that has only categories.
- [ ] Test with missing SKU.
- [ ] Test with missing featured image.
- [ ] Test desktop around 1366px.
- [ ] Test mobile around 390px.
- [ ] Confirm CTA URL remains `/request-a-quote/?product_id=PRODUCT_ID`.
- [ ] Confirm no PHP fatal errors.

---

## Decision Gates

Human confirmation needed before implementation:

- Keep sidebar on product pages or move to full-width no-sidebar product detail.
- Use same-tag related products as primary relation, with category fallback.
- Hide reviews in V1-style B2B product pages.
- Hide `SKU: N/A`.
- Whether to create a branded placeholder image treatment when product image is missing.

Default recommendation:

- Keep a product-specific sidebar only if it contains product/quote content, not blog widgets.
- Use same `product_tag` first, fallback to same `product_cat`.
- Hide reviews until a real B2B review/testimonial strategy exists.
- Hide empty Woo metadata instead of showing placeholders like `N/A`.

---

## Out Of Scope

- Quote cart or multi-product quote table.
- Advanced filtering.
- Custom product CPT.
- Technical Product Card with full specs table before its approved milestone.
- CF7/CFDB7 quote submission handling.
- n8n automation.
- Payment/cart/checkout flow.
- Editing GeneratePress parent files.

---

## Risks

- Theme-side product queries can become business logic if they grow too complex.
- Sidebar can look cluttered if product data is incomplete.
- Related products can feel random if tags/categories are not curated.
- Hiding reviews may surprise stakeholders if they expect ecommerce behavior.

Mitigation:

- Keep the first implementation visual and query-light.
- Audit product taxonomy data first.
- Use native WooCommerce product tags/categories.
- Document any new `skvn-*` class family before implementation.
- Add runtime QA cases for missing data.
