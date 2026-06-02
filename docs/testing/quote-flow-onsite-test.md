# Quote Flow Onsite Test — V1 / 0.6.0

Purpose:

```text
Verify the V1 / 0.6.0 quote UI path in the real WordPress runtime.
This test proves product CTAs route buyers to the same-site Request a Quote page.
It does not test CF7, CFDB7, thank-you redirects, email delivery, or n8n automation.
```

## Scope

In scope:

```text
- WooCommerce catalog product CTA label and URL.
- Single product Request a Quote CTA.
- Request Quote page visual surface.
- Editor insertion of the SKVN Request Quote Page pattern.
- Desktop and mobile visual smoke.
- Browser console and PHP fatal smoke.
```

Out of scope:

```text
- Custom quote form handler.
- CF7 / CFDB7 submission storage.
- Quote thank-you page.
- n8n webhook automation.
- Multi-product quote cart.
- Popup/modal quote flow.
- WooCommerce checkout/order flow.
```

## Preconditions

Runtime:

```text
[ ] WordPress Admin is reachable.
[ ] GeneratePress parent theme is installed.
[ ] `skvn-marine` child theme is active.
[ ] WooCommerce is installed and active.
[ ] The current theme build is deployed to the onsite runtime.
[ ] Browser devtools can be opened for console checks.
```

Test data:

```text
[ ] At least 2 published WooCommerce products exist.
[ ] Each test product has a title.
[ ] Each test product has a featured image.
[ ] Each test product has a price or intentionally hidden price state.
[ ] Record one product ID for URL checks: PRODUCT_ID=____.
```

Recommended pages:

```text
[ ] A shop/product listing page is available.
[ ] A single product page is available.
[ ] A page with slug `/request-a-quote/` exists or can be created.
```

## Setup Request Quote Page

Use this only if `/request-a-quote/` does not already exist.

```text
1. Go to WP Admin > Pages > Add New.
2. Title: Request a Quote.
3. Slug/permalink: request-a-quote.
4. Insert pattern: SKVN Request Quote Page.
5. In the editor sidebar, set SKVN Page Display -> Page preset -> SKVN Request Quote Page.
6. Publish.
7. Open `/request-a-quote/`.
```

Pass criteria:

```text
[ ] Page loads without fatal error.
[ ] Pattern inserts without invalid block warning.
[ ] Page uses SKVN visual classes/tokens.
[ ] Page setup uses the SKVN Request Quote Page sidebar preset; no raw custom class input is required for page chrome/layout setup.
[ ] No real form submission is expected in 0.6.0.
[ ] Placeholder/contact path is clear enough for visual smoke.
```

## Test Case 1 — Product Listing CTA

Steps:

```text
1. Open the shop page or a page containing WooCommerce product listing/product grid.
2. Locate a published test product.
3. Confirm the product card CTA text.
4. Hover/focus the CTA if possible.
5. Copy the CTA URL.
6. Open the CTA in the browser.
```

Expected:

```text
[ ] CTA text is `Request a Quote`.
[ ] CTA is visible on desktop without relying on hover.
[ ] CTA is visible on mobile without relying on hover.
[ ] CTA URL is `/request-a-quote/?product_id=PRODUCT_ID`.
[ ] Clicking CTA opens the same-site Request Quote page.
[ ] The `product_id` query string remains in the browser URL.
[ ] No add-to-cart action is triggered by this CTA.
```

Fail if:

```text
[ ] CTA still says Add to cart.
[ ] CTA points to `/cart/`, `/checkout/`, `/contact/`, or a product page.
[ ] CTA omits `product_id`.
[ ] CTA is hidden on mobile.
[ ] Clicking CTA causes a PHP fatal, white screen, or 404.
```

## Test Case 2 — Single Product CTA

Steps:

```text
1. Open a published single product page.
2. Locate the main purchase/quote area.
3. Confirm the primary CTA text.
4. Copy the CTA URL.
5. Click the CTA.
```

Expected:

```text
[ ] Primary CTA text is `Request a Quote`.
[ ] CTA URL is `/request-a-quote/?product_id=PRODUCT_ID`.
[ ] Clicking CTA opens the same-site Request Quote page.
[ ] The `product_id` query string remains in the browser URL.
[ ] The page does not add an item to cart.
```

Fail if:

```text
[ ] Main CTA still submits add-to-cart.
[ ] Product page layout breaks around price/summary/CTA.
[ ] CTA is unreadable or clipped on mobile.
```

## Test Case 3 — Request Quote Page With Query String

Steps:

```text
1. Open `/request-a-quote/?product_id=PRODUCT_ID` directly.
2. Open `/request-a-quote/?product_id=999999` directly.
3. Open `/request-a-quote/` without query string.
4. Check desktop layout.
5. Check mobile layout.
```

Expected:

```text
[ ] All three URLs load without fatal error.
[ ] Invalid or missing product_id does not break the visual page.
[ ] Hero, quote context card, and detail cards are readable.
[ ] No horizontal scroll on mobile.
[ ] Text does not overlap or overflow cards/buttons.
[ ] Images/placeholders do not force layout overflow.
```

Note:

```text
0.6.0 does not require the page to read product_id into form fields.
That behavior returns when CF7/CFDB7 is in scope after 0.6.0.
```

## Test Case 4 — Editor Stability

Steps:

```text
1. Open the Request a Quote page in the block editor.
2. Confirm blocks are valid.
3. Edit normal text content in the hero and one card.
4. Save/update.
5. Reload the editor.
6. Preview frontend.
```

Expected:

```text
[ ] No invalid block warning appears.
[ ] Text edits persist after reload.
[ ] Marketing users do not need to enter raw custom classes for normal copy edits.
[ ] Marketing users can choose the SKVN Request Quote Page preset from the editor sidebar.
[ ] The frontend still matches the intended visual surface after save.
```

Known 0.6.0 boundary:

```text
Full visual style controls are not considered complete until the milestone's sidebar/editor controls acceptance is closed.
```

## Test Case 5 — Visual Smoke

Desktop:

```text
[ ] Test at approximately 1366px or wider.
[ ] Product card CTA is readable and aligned.
[ ] Single product CTA has adequate spacing.
[ ] Request Quote hero layout is balanced.
[ ] Quote context card does not look like a nested UI card inside another card.
```

Mobile:

```text
[ ] Test at approximately 390px wide.
[ ] Product listing CTA remains visible.
[ ] Single product CTA remains visible.
[ ] Request Quote sections stack cleanly.
[ ] No horizontal scroll.
[ ] Button text fits inside buttons.
```

Accessibility smoke:

```text
[ ] CTAs can be reached by keyboard tab.
[ ] Focus indicator is visible enough to locate CTA.
[ ] CTA accessible name includes quote intent.
[ ] Color contrast appears acceptable for primary CTA text.
```

## Evidence To Capture

Record:

```text
[ ] WordPress URL.
[ ] Theme version/build identifier if available.
[ ] Product ID used for testing.
[ ] Product listing page URL.
[ ] Single product page URL.
[ ] Request Quote page URL with product_id.
[ ] Desktop screenshots.
[ ] Mobile screenshots.
[ ] Browser console status.
[ ] PHP/error log status.
```

Suggested result format:

```text
Result: PASS / FAIL / NEEDS REVISION
Tester:
Date:
Environment:
Product ID:
Frontend URLs:
Console errors:
PHP/log errors:
Notes:
```

## Pass Rule

The 0.6.0 runtime visual smoke can be marked pass only when:

```text
[ ] Product listing CTA path passes.
[ ] Single product CTA path passes.
[ ] Request Quote page with product_id loads.
[ ] Desktop visual smoke passes.
[ ] Mobile visual smoke passes.
[ ] No serious console errors.
[ ] No PHP fatal errors.
[ ] Evidence is recorded.
```

Do not mark these as pass for 0.6.0:

```text
[ ] CF7 submission works.
[ ] CFDB7 stores lead.
[ ] n8n receives lead.
[ ] Thank-you redirect works.
```

Those belong to a later milestone after 0.6.0.
