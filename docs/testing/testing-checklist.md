# Testing Checklist

## 0.2.0 Carried Test Debt

- [x] WordPress runtime responds at `http://localhost:8080/wp-login.php`.
- [x] `skvn-marine` child theme is active in runtime.
- [x] `SKVN Homepage Test Layout` pattern can be inserted into a page.
- [x] Test page uses full-width/no-sidebar setup where available.
- [x] Default page title is hidden when testing a custom hero.
- [x] Desktop screenshot reviewed.
- [x] Mobile screenshot reviewed.
- [x] Result recorded as PASS / FAIL / NEEDS REVISION.

## Theme

- [x] GeneratePress parent installed.
- [x] `skvn-marine` child theme active.
- [x] Theme `screenshot.png` exists for admin preview.
- [x] Frontend loads.
- [x] Admin loads.
- [x] Block editor opens.
- [x] Product page renders.
- [x] Blog post renders.
- [x] Homepage renders.
- [x] No PHP fatal error.
- [x] Console has no serious JS errors.

## Header/Footer Baseline

- [ ] Header nav is readable on desktop.
- [ ] Header does not overlap page content.
- [ ] Mobile header remains usable with GeneratePress behavior.
- [ ] Footer pattern renders.
- [ ] Footer columns stack cleanly on mobile.
- [ ] No footer text overflows.
- [ ] Footer links are keyboard reachable.

## Homepage Test Pattern

- [x] Hero content and media do not overlap.
- [x] Trust strip displays 4 items on desktop.
- [ ] Trust strip stacks cleanly on mobile.
- [ ] Why choose cards use soft mint support accent appropriately.
- [ ] Factory/process sections keep blue-first visual direction.
- [ ] Newsletter image is replaceable in editor.
- [ ] No custom newsletter form handler exists in theme.
- [ ] No image URL is hardcoded in CSS.
- [ ] CTA buttons remain visible on mobile.

### Open Layout Issue — Full-Width Container Not Escaping

Status:

```text
OPEN
```

Observed in local runtime:

```text
An `alignfull` group inside the pattern test page still appears constrained by the page/content container.
The visual result looks like a large page wrapper with the content box trapped inside instead of the hero band reaching full viewport width.
Screenshot evidence is now available from 2026-05-21 local runtime.
The light-blue hero background reaches the viewport, but the hero grid/content is still visually trapped in a narrow centered column.
The headline wraps too aggressively because the text column is too narrow for the intended desktop hero layout.
The media column renders, but the composition does not match the intended wide ecommerce landing-page hero.
```

Reference note:

```text
See user-provided diagram: page wrapper, header/menu inside, and content area still boxed instead of full-width.
See user-provided screenshot: C:/Users/VPF-Champion/Downloads/Screenshot 2026-05-21 at 14-31-52 Minh Hai Fish Local.png
```

Test snippet used:

```html
<!-- wp:group {"className":"skvn-pattern-test-page","layout":{"type":"default"}} -->
<div class="wp-block-group skvn-pattern-test-page">
	<!-- wp:group {"align":"full","className":"skvn-pattern-hero","layout":{"type":"constrained"}} -->
	<div class="wp-block-group alignfull skvn-pattern-hero">
		<!-- wp:columns {"className":"skvn-pattern-hero__grid","verticalAlignment":"center"} -->
		<div class="wp-block-columns are-vertically-aligned-center skvn-pattern-hero__grid">
			<!-- wp:column {"verticalAlignment":"center"} -->
			<div class="wp-block-column is-vertically-aligned-center">
				<!-- wp:paragraph {"className":"skvn-pattern-hero__eyebrow"} -->
				<p class="skvn-pattern-hero__eyebrow">Pattern UI Test 0.2.0</p>
				<!-- /wp:paragraph -->

				<!-- wp:heading {"level":1} -->
				<h1>Trusted Seafood Exporter From Viet Nam</h1>
				<!-- /wp:heading -->

				<!-- wp:paragraph -->
				<p>Testing the current SKVN theme pattern baseline with reusable blocks, marine colors, card surfaces, and a replaceable media band.</p>
				<!-- /wp:paragraph -->

				<!-- wp:buttons -->
				<div class="wp-block-buttons">
					<!-- wp:button {"className":"is-style-skvn-primary"} -->
					<div class="wp-block-button is-style-skvn-primary"><a class="wp-block-button__link wp-element-button" href="/contact/">Request a Quote</a></div>
					<!-- /wp:button -->
				</div>
				<!-- /wp:buttons -->
			</div>
			<!-- /wp:column -->

			<!-- wp:column {"verticalAlignment":"center","className":"skvn-pattern-hero__media"} -->
			<div class="wp-block-column is-vertically-aligned-center skvn-pattern-hero__media">
				<!-- wp:image {"sizeSlug":"large","linkDestination":"none","className":"is-style-skvn-rounded-media"} -->
				<figure class="wp-block-image size-large is-style-skvn-rounded-media"><img src="https://images.unsplash.com/photo-1615141982883-c7ad0e69fd62?auto=format&amp;fit=crop&amp;w=1200&amp;q=80" alt="Fresh seafood selection on ice"/></figure>
				<!-- /wp:image -->
			</div>
			<!-- /wp:column -->
		</div>
		<!-- /wp:columns -->
	</div>
	<!-- /wp:group -->
```

Likely investigation targets:

```text
[ ] GeneratePress page layout setting: full-width/no-sidebar/content container.
[ ] Whether page title/content wrapper is still constrained.
[ ] Whether `.entry-content` or `.site-content` limits `alignfull`.
[ ] Whether `.alignfull` needs child-theme CSS escape rule.
[ ] Whether pattern wrapper `.skvn-pattern-test-page` is preventing full-width behavior.
```

Do not close until:

```text
[x] Runtime screenshot captured.
[ ] Desktop full-width hero band reaches viewport width.
[ ] Inner hero content remains constrained to SKVN wide width.
[ ] Mobile has no horizontal scroll.
[ ] Desktop headline keeps intended line breaks and does not collapse into a narrow text column.
[ ] Desktop image column aligns with the hero content and supports the intended wide composition.
```

## 0.5.0 SKVN Full Width Layout

Objective:

```text
Create a reusable theme-owned page layout for pages that need full-width Gutenberg sections without losing GeneratePress header/footer.
```

Scope:

```text
In scope:
- SKVN Full Width page template/layout.
- Full-width content surface for selected pages.
- alignfull escape behavior.
- Inner SKVN max-width constraints.
- Pattern UI test page validation.

Out of scope:
- Quote flow integration.
- Custom product grid/list blocks.
- Custom hero block.
- Editing GeneratePress parent theme.
```

Acceptance:

```text
[x] Page can select/use SKVN Full Width at source/template level.
[x] Header/footer calls remain in the template.
[x] Default narrow page content wrapper is bypassed by the template source.
[x] alignfull section backgrounds can reach viewport width at CSS contract level.
[x] Inner content remains aligned to SKVN wide container at CSS contract level.
[ ] Desktop hero headline no longer collapses into an unintended narrow column.
[ ] Image/media column aligns with the intended wide composition.
[ ] Mobile has no horizontal scroll.
[x] PHP syntax check passes.
[ ] Runtime screenshot reviewed.
```

Test method:

```text
1. Apply SKVN Full Width to Pattern UI Test 0.2.0 or a new layout test page.
2. Insert the existing homepage/pattern hero test content.
3. Preview desktop at wide viewport.
4. Confirm hero background reaches viewport width.
5. Confirm hero inner grid uses SKVN wide width, not the old narrow content column.
6. Preview mobile.
7. Confirm no horizontal scroll and no overlap.
8. Record PASS / FAIL / NEEDS REVISION.
```

## V1 Near-Term Technical Debt

In scope for upcoming V1 work:

```text
[ ] Add theme screenshot.png.
[ ] Runtime-check SKVN Full Width against GeneratePress wrappers.
[ ] Add child-theme CSS for `.inside-article` padding only if runtime confirms the issue.
[ ] Keep onsite deploy checklist updated with memory, backup, rollback, and log checks.
[ ] Bump theme/plugin versions before tagged onsite test builds.
[ ] Move new design tokens toward theme.json / WordPress variables.
```

Deferred proposals:

```text
[ ] Pure block theme / custom base theme experiment.
[ ] Tailwind/PostCSS build pipeline.
[ ] Pattern registration restructure.
[ ] Composer/PHP package workflow.
[ ] GitHub Actions release zip automation.
[ ] Scoped block CSS/performance budget pass.
```

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
- [ ] Single product CTA links to `/request-a-quote/?product_id=...`.
- [ ] Request Quote page visual surface loads.
- [ ] Request Quote page accepts missing or invalid `product_id` without fatal error.
- [ ] CTA remains visible on mobile.
- [ ] No custom quote form handler exists in theme/plugin code.
- [ ] CF7/CFDB7/n8n behavior is not tested in 0.6.0.
- [ ] See `docs/testing/quote-flow-onsite-test.md` for onsite execution.

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
