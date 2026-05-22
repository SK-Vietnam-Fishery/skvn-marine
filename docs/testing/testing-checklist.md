# Testing Checklist

## 0.2.0 Carried Test Debt

- [ ] WordPress runtime responds at `http://localhost:8080/wp-login.php`.
- [ ] `skvn-marine` child theme is active in runtime.
- [ ] `SKVN Homepage Test Layout` pattern can be inserted into a page.
- [ ] Test page uses full-width/no-sidebar setup where available.
- [ ] Default page title is hidden when testing a custom hero.
- [ ] Desktop screenshot reviewed.
- [ ] Mobile screenshot reviewed.
- [ ] Result recorded as PASS / FAIL / NEEDS REVISION.

## Theme

- [ ] GeneratePress parent installed.
- [ ] `skvn-marine` child theme active.
- [ ] Frontend loads.
- [ ] Admin loads.
- [ ] Block editor opens.
- [ ] Product page renders.
- [ ] Blog post renders.
- [ ] Homepage renders.
- [ ] No PHP fatal error.
- [ ] Console has no serious JS errors.

## Header/Footer Baseline

- [ ] Header nav is readable on desktop.
- [ ] Header does not overlap page content.
- [ ] Mobile header remains usable with GeneratePress behavior.
- [ ] Footer pattern renders.
- [ ] Footer columns stack cleanly on mobile.
- [ ] No footer text overflows.
- [ ] Footer links are keyboard reachable.

## Homepage Test Pattern

- [ ] Hero content and media do not overlap.
- [ ] Trust strip displays 4 items on desktop.
- [ ] Trust strip stacks cleanly on mobile.
- [ ] Why choose cards use soft mint support accent appropriately.
- [ ] Factory/process sections keep blue-first visual direction.
- [ ] Newsletter image is replaceable in editor.
- [ ] No custom newsletter form handler exists in theme.
- [ ] No image URL is hardcoded in CSS.
- [ ] CTA buttons remain visible on mobile.

## Encoding Guardrail

- [ ] Context/docs remain UTF-8.
- [ ] Vietnamese text does not show mojibake.
- [ ] `tests/context-encoding.test.mjs` passes before commit.

## Media ALT

- [ ] Upload image with SEO title.
- [ ] ALT is filled from title when empty.
- [ ] Existing ALT is not overwritten.
- [ ] Caption is not auto-generated in V1.

## Quote Flow

- [ ] Product CTA links to `/request-a-quote/?product_id=...`.
- [ ] CF7 form renders.
- [ ] CFDB7 stores submission.
- [ ] n8n receives lead.
- [ ] Thank-you page is reached.
- [ ] Admin email works.
- [ ] Customer confirmation works if enabled.

## Woo/Product Grid

- [ ] Product category renders.
- [ ] `SKVN Woo Category Strip` pattern inserts without invalid block warning.
- [ ] `SKVN Woo Product Grid` pattern inserts without invalid block warning.
- [ ] Product grid uses WooCommerce native Product Collection/Product Template.
- [ ] Product card CTA visible on mobile.
- [ ] Product card overlay works on desktop.
- [ ] Pagination works if enabled.
- [ ] No custom `skvn-marine/product-grid` block exists in V1.
- [ ] No custom `skvn-marine/product-list` block exists in V1.

## 0.4.0 Woo Product Sections

- [ ] `tests/woo-product-sections.test.mjs` passes.
- [ ] Woo native product patterns created/styled.
- [ ] Product card visual styling implemented.
- [ ] Mobile CTA remains visible without hover.
- [ ] Product category strip renders.
- [ ] Product grid renders with real WooCommerce products.
- [ ] Runtime smoke test passes.

### Small Test Plan

Objective:

```text
Verify SKVN Woo product sections work in the real WordPress runtime with WooCommerce-native data and mobile-visible CTAs.
```

Scope:

```text
In scope:
- SKVN Woo Category Strip pattern
- SKVN Woo Product Grid pattern
- Product card visual styling
- Mobile CTA visibility
- Save/reload editor stability

Out of scope:
- Custom product grid/list blocks
- Advanced filtering
- Quote flow submission
- Payment/cart/checkout
```

Test data:

```text
Create at least 4 WooCommerce products.
Each product needs:
- Product image
- Product title
- Price
- Category
```

Execution:

```text
1. Run source checks.
2. Open local WordPress runtime.
3. Create page: Woo Product Sections Test 0.4.0.
4. Insert SKVN Woo Category Strip.
5. Insert SKVN Woo Product Grid.
6. Publish or preview.
7. Check desktop layout.
8. Check mobile layout.
9. Reload editor and confirm no invalid block warning.
10. Record PASS / FAIL / NEEDS REVISION.
```

Pass criteria:

```text
[ ] Patterns insert successfully.
[ ] Product categories render.
[ ] Product grid renders real products.
[ ] Product card image/title/price/CTA are readable.
[ ] CTA is visible on mobile without hover.
[ ] No horizontal scroll on mobile.
[ ] No serious console errors.
[ ] No invalid block warning after save/reload.
```

Fail criteria:

```text
[ ] Pattern cannot be inserted.
[ ] Product grid does not render real products.
[ ] CTA is hidden on mobile.
[ ] Layout overlaps or overflows.
[ ] Editor reports invalid block after reload.
```

### How To Test 0.4.0 In Runtime

Preconditions:

```text
[ ] WordPress runtime responds at http://localhost:8080/wp-login.php.
[ ] WooCommerce is installed and active in runtime.
[ ] skvn-marine child theme is active.
[ ] Theme source changes are deployed or symlinked into runtime.
[ ] At least 4 test products exist.
[ ] Products have image, title, price, and category.
```

Create test page:

```text
1. Go to WP Admin > Pages > Add New.
2. Title: Woo Product Sections Test 0.4.0.
3. Insert pattern: SKVN Woo Category Strip.
4. Insert pattern: SKVN Woo Product Grid.
5. Set page layout full-width/no-sidebar if available.
6. Publish or preview.
```

Desktop checks:

```text
[ ] Category strip renders using WooCommerce product categories.
[ ] Product grid renders real WooCommerce products.
[ ] Product cards use SKVN visual styling.
[ ] Product images keep stable aspect ratio.
[ ] Product title/price are readable.
[ ] Product CTA is visible.
[ ] Hover enhancement does not hide the CTA as the only access path.
[ ] No invalid block warning appears after save/reload.
```

Mobile checks:

```text
[ ] Product cards stack cleanly.
[ ] CTA remains visible without hover.
[ ] Text does not overflow cards.
[ ] Images do not create horizontal scroll.
[ ] Category strip remains readable.
```

Pass evidence:

```text
[ ] Frontend URL recorded.
[ ] Desktop screenshot reviewed.
[ ] Mobile screenshot reviewed.
[ ] Browser console checked.
[ ] Result recorded as PASS / FAIL / NEEDS REVISION.
```

Pass rule:

```text
Source tests passing means source-level pass only.
0.4.0 runtime smoke is not PASS until the WordPress page above is inspected.
```

## Custom Blocks

- [ ] Insert block.
- [ ] Save post.
- [ ] Reload editor.
- [ ] No invalid block warning.
- [ ] Frontend behavior works.

## 0.3.0 Slider/Slide Block

- [ ] TypeScript build pipeline passes.
- [ ] Slider block registers.
- [ ] Slide block registers.
- [ ] Slider can contain Slide blocks.
- [ ] Swiper dependency rationale is documented.
- [ ] Swiper assets load only on frontend pages containing slider block.
- [ ] Slider config comes from block attributes.
- [ ] Keyboard navigation is enabled.
- [ ] Autoplay pauses on hover.
- [ ] `prefers-reduced-motion` disables autoplay.
- [ ] Editor uses stacked or simplified preview.
- [ ] Editor does not run Swiper autoplay.
- [ ] Editor does not hide slide content with `opacity: 0`.
- [ ] Saved page reloads without invalid block warning.
- [ ] Runtime smoke test passes.

## 0.3.0 Technical Debt

Debt status:

```text
OPEN
```

Source/build checks for the Slider/Slide block have passed. Runtime validation and human approval are intentionally still pending.

### Debt 1 — Slider Runtime Smoke Test

Purpose:

```text
Prove the built slider works inside the real local WordPress runtime, not only by source-level tests.
```

How to test:

```text
1. Confirm local WP runtime responds at http://localhost:8080/wp-login.php.
2. Confirm runtime uses the custom plugin from source or current deployed copy.
3. Activate SKVN Marine Blocks if needed.
4. Create a test page named: Slider Runtime Test 0.3.0.
5. Insert SKVN Slider block.
6. Add at least 3 SKVN Slide child blocks.
7. Set autoplay on, delay 5000, loop on, arrows on, dots on, effect slide.
8. Publish or preview page.
9. Confirm slider initializes on frontend.
10. Confirm arrows work.
11. Confirm dots work.
12. Confirm keyboard navigation works.
13. Confirm autoplay pauses on hover.
14. Confirm no serious browser console errors.
15. Test with reduced motion enabled; autoplay must not run.
```

Pass evidence:

```text
[ ] Frontend URL recorded.
[ ] Desktop screenshot reviewed.
[ ] Mobile screenshot reviewed.
[ ] Console checked.
[ ] Result recorded as PASS / FAIL / NEEDS REVISION.
```

### Debt 2 — Human Milestone Approval

Purpose:

```text
Human confirms 0.3.0 is acceptable after runtime smoke test evidence exists.
```

How to close:

```text
1. Runtime smoke test must be PASS.
2. Human reviews the slider test page.
3. Human explicitly approves 0.3.0 completion.
4. Only then tick "Human approves milestone completion" in .context/MILESTONES.md.
5. Do not auto-transition to 0.4.0 unless human explicitly approves the milestone move.
```
