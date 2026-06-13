# Request Quote Page — Meta AI Gutenberg Translation

## project_contract

- Theme root: `wp-content/themes/skvn-marine`
- Plugin root: `wp-content/plugins/skvn-marine-blocks`
- CSS prefix: `skvn-`
- Output target: paste-ready Gutenberg core blocks using existing SKVN theme classes.
- Boundary: theme owns visual classes and tokens; future HTML-2-Gutenberg tooling belongs to `skvn-marine-blocks`.

## css_source_scan

- `style.css` implements SKVN blue, mint, gold, sky, slate, white, border, radius, shadow, content width, and wide width tokens.
- `theme.json` exposes matching palette slugs for SKVN colors.
- `inc/block-styles.php` registers `is-style-skvn-primary`, `is-style-skvn-section`, `is-style-skvn-card`, and `is-style-skvn-rounded-media`.
- Existing quote classes include `skvn-quote-page`, `skvn-quote-hero`, `skvn-quote-context-card`, `skvn-quote-surface`, `skvn-quote-surface__grid`, and `skvn-quote-surface__card`.
- Existing reusable layout classes include `skvn-translated-*`, `skvn-kpi-strip*`, `skvn-section__*`, `skvn-card`, `skvn-card-grid skvn-card-grid--3`, and `skvn-process-card*`.

## brand_source_scan

- Meta AI artifact uses the current SKVN palette: deep navy `#073b5a`, trust blue `#0f5c8c`, primary blue `#1e79be`, sky `#eaf7ff`, mint `#ddfaf4`, gold `#e9c766`, slate copy `#475569`, and border `#dbe7ef`.
- Layout intent: request quote hero, context card with image, quote form surface, business quote paths, 4-step process, support information, and CTA band.
- Prototype includes full document chrome, raw Tailwind classes, live form fields, and footer content.

## brand_mapping

- Deep navy -> `--skvn-color-blue-950` / `skvn-blue-950`
- Trust blue -> `--skvn-color-blue-900` / `skvn-blue-900`
- Primary CTA -> `skvn-button skvn-button--primary is-style-skvn-primary`
- Sky background -> `skvn-section--soft`
- Mint support surface -> `skvn-section--support` or `skvn-card` inside soft section
- Gold badge -> limited premium cue; keep as content text rather than raw color styling.

## brand_mismatch

- No major palette mismatch.
- Header/footer prototype is not translated into page content because site chrome belongs to the theme/GeneratePress layer.
- Raw active form fields are not translated into a working HTML form because custom quote handling is forbidden in the current milestone.

## token_changes_needed

- None for paste-ready Gutenberg using existing classes.
- Future CF7 field styling may need theme CSS when CF7 returns to scope.

## gutenberg_markup

Paste this into Gutenberg Code Editor:

```html
<!-- wp:group {"align":"full","className":"skvn-translated-page skvn-quote-page","layout":{"type":"default"}} -->
<div class="wp-block-group alignfull skvn-translated-page skvn-quote-page">
	<!-- wp:group {"align":"full","className":"skvn-translated-hero skvn-quote-hero","layout":{"type":"default"}} -->
	<div class="wp-block-group alignfull skvn-translated-hero skvn-quote-hero">
		<!-- wp:columns {"verticalAlignment":"center","className":"skvn-translated-hero__grid"} -->
		<div class="wp-block-columns are-vertically-aligned-center skvn-translated-hero__grid">
			<!-- wp:column {"verticalAlignment":"center","className":"skvn-translated-hero__content"} -->
			<div class="wp-block-column is-vertically-aligned-center skvn-translated-hero__content">
				<!-- wp:paragraph {"className":"skvn-translated-hero__eyebrow"} -->
				<p class="skvn-translated-hero__eyebrow">Request quotation</p>
				<!-- /wp:paragraph -->

				<!-- wp:heading {"level":1,"className":"skvn-translated-hero__heading"} -->
				<h1 class="wp-block-heading skvn-translated-hero__heading">Share your product, packing and delivery requirements for a clear export quote</h1>
				<!-- /wp:heading -->

				<!-- wp:paragraph {"className":"skvn-translated-hero__lead"} -->
				<p class="skvn-translated-hero__lead">Our export team reviews species, size grade, volume, destination port, and packing needs to prepare a container-scale quotation with lead time and documents.</p>
				<!-- /wp:paragraph -->

				<!-- wp:buttons {"className":"skvn-translated-hero__actions"} -->
				<div class="wp-block-buttons skvn-translated-hero__actions">
					<!-- wp:button {"className":"skvn-button skvn-button--primary is-style-skvn-primary"} -->
					<div class="wp-block-button skvn-button skvn-button--primary is-style-skvn-primary"><a class="wp-block-button__link wp-element-button" href="#quote-form">Start quote request</a></div>
					<!-- /wp:button -->

					<!-- wp:button {"className":"skvn-button skvn-button--secondary"} -->
					<div class="wp-block-button skvn-button skvn-button--secondary"><a class="wp-block-button__link wp-element-button" href="#categories">Review product categories</a></div>
					<!-- /wp:button -->
				</div>
				<!-- /wp:buttons -->

				<!-- wp:list {"className":"skvn-hashtag-list"} -->
				<ul class="skvn-hashtag-list">
					<li>MOQ from 1 FCL</li>
					<li>Cold chain -18 C</li>
					<li>Private label available</li>
				</ul>
				<!-- /wp:list -->
			</div>
			<!-- /wp:column -->

			<!-- wp:column {"verticalAlignment":"center","className":"skvn-translated-hero__media"} -->
			<div class="wp-block-column is-vertically-aligned-center skvn-translated-hero__media">
				<!-- wp:group {"className":"skvn-quote-context-card","layout":{"type":"default"}} -->
				<div class="wp-block-group skvn-quote-context-card">
					<!-- wp:image {"sizeSlug":"large","linkDestination":"none","className":"skvn-translated-hero__image"} -->
					<figure class="wp-block-image size-large skvn-translated-hero__image"><img src="https://placehold.co/800x320/eaf7ff/073b5a?text=Export+Ready+Containers" alt="Export-ready seafood containers placeholder"/></figure>
					<!-- /wp:image -->

					<!-- wp:heading {"level":2} -->
					<h2 class="wp-block-heading">Quote context</h2>
					<!-- /wp:heading -->

					<!-- wp:paragraph -->
					<p>Provide these details to speed up pricing.</p>
					<!-- /wp:paragraph -->

					<!-- wp:list {"className":"skvn-quote-context-list"} -->
					<ul class="skvn-quote-context-list">
						<li><strong>Product interest and size/specification:</strong> Species, size grade, glaze, treatment.</li>
						<li><strong>Estimated volume or container need:</strong> KG/MT, number of FCLs, frequency.</li>
						<li><strong>Destination country/port:</strong> Port of discharge, Incoterms preference.</li>
						<li><strong>Packing / private label requirement:</strong> Carton, inner bag, label artwork.</li>
						<li><strong>Certification or documentation needs:</strong> HACCP, BRCGS, catch certificate, health cert.</li>
					</ul>
					<!-- /wp:list -->
				</div>
				<!-- /wp:group -->
			</div>
			<!-- /wp:column -->
		</div>
		<!-- /wp:columns -->
	</div>
	<!-- /wp:group -->

	<!-- wp:group {"align":"full","metadata":{"name":"quote-form"},"className":"skvn-section skvn-section--support skvn-quote-surface","layout":{"type":"constrained"}} -->
	<div class="wp-block-group alignfull skvn-section skvn-section--support skvn-quote-surface" id="quote-form">
		<!-- wp:paragraph {"className":"skvn-section__eyebrow"} -->
		<p class="skvn-section__eyebrow">Export ready</p>
		<!-- /wp:paragraph -->

		<!-- wp:heading {"className":"skvn-section__heading"} -->
		<h2 class="wp-block-heading skvn-section__heading">Request a Quote</h2>
		<!-- /wp:heading -->

		<!-- wp:paragraph {"className":"skvn-section__lead"} -->
		<p class="skvn-section__lead">For importers, distributors, wholesalers and foodservice buyers. Typical reply within 1 business day.</p>
		<!-- /wp:paragraph -->

		<!-- wp:group {"className":"skvn-card skvn-quote-surface__card","layout":{"type":"default"}} -->
		<div class="wp-block-group skvn-card skvn-quote-surface__card">
			<!-- wp:heading {"level":3} -->
			<h3 class="wp-block-heading">Form surface placeholder</h3>
			<!-- /wp:heading -->

			<!-- wp:paragraph -->
			<p>When CF7 returns to scope, replace this card with the approved request quote form and hidden context fields.</p>
			<!-- /wp:paragraph -->

			<!-- wp:columns {"className":"skvn-quote-surface__grid"} -->
			<div class="wp-block-columns skvn-quote-surface__grid">
				<!-- wp:column -->
				<div class="wp-block-column"><!-- wp:group {"className":"skvn-card skvn-quote-surface__card","layout":{"type":"default"}} --><div class="wp-block-group skvn-card skvn-quote-surface__card"><!-- wp:heading {"level":3} --><h3 class="wp-block-heading">Buyer Details</h3><!-- /wp:heading --><!-- wp:paragraph --><p>Full name, company name, email, and country.</p><!-- /wp:paragraph --></div><!-- /wp:group --></div>
				<!-- /wp:column -->

				<!-- wp:column -->
				<div class="wp-block-column"><!-- wp:group {"className":"skvn-card skvn-quote-surface__card","layout":{"type":"default"}} --><div class="wp-block-group skvn-card skvn-quote-surface__card"><!-- wp:heading {"level":3} --><h3 class="wp-block-heading">Product Scope</h3><!-- /wp:heading --><!-- wp:paragraph --><p>Product interest, quantity, estimated volume, destination port, and packaging requirement.</p><!-- /wp:paragraph --></div><!-- /wp:group --></div>
				<!-- /wp:column -->

				<!-- wp:column -->
				<div class="wp-block-column"><!-- wp:group {"className":"skvn-card skvn-quote-surface__card","layout":{"type":"default"}} --><div class="wp-block-group skvn-card skvn-quote-surface__card"><!-- wp:heading {"level":3} --><h3 class="wp-block-heading">Certification</h3><!-- /wp:heading --><!-- wp:paragraph --><p>HACCP, BRCGS, EU catch certificate, target price, size breakdown, glaze, treatment, and delivery window.</p><!-- /wp:paragraph --></div><!-- /wp:group --></div>
				<!-- /wp:column -->
			</div>
			<!-- /wp:columns -->

			<!-- wp:paragraph -->
			<p><strong>Context fields:</strong> product_id: -, category: frozen-seafood, intent: b2b-quote, source: website.</p>
			<!-- /wp:paragraph -->

			<!-- wp:buttons -->
			<div class="wp-block-buttons">
				<!-- wp:button {"className":"skvn-button skvn-button--primary is-style-skvn-primary"} -->
				<div class="wp-block-button skvn-button skvn-button--primary is-style-skvn-primary"><a class="wp-block-button__link wp-element-button" href="/contact/">Submit request</a></div>
				<!-- /wp:button -->
			</div>
			<!-- /wp:buttons -->

			<!-- wp:paragraph -->
			<p>By submitting, buyers agree to be contacted regarding export quotation and documentation.</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->

	<!-- wp:group {"align":"full","metadata":{"name":"categories"},"className":"skvn-section skvn-section--soft","layout":{"type":"constrained"}} -->
	<div class="wp-block-group alignfull skvn-section skvn-section--soft" id="categories">
		<!-- wp:heading {"className":"skvn-section__heading"} -->
		<h2 class="wp-block-heading skvn-section__heading">Business quote paths</h2>
		<!-- /wp:heading -->

		<!-- wp:paragraph {"className":"skvn-section__lead"} -->
		<p class="skvn-section__lead">Choose the path that matches your inquiry. Each path routes to the same export team with tailored checklists.</p>
		<!-- /wp:paragraph -->

		<!-- wp:columns {"className":"skvn-card-grid skvn-card-grid--3"} -->
		<div class="wp-block-columns skvn-card-grid skvn-card-grid--3">
			<!-- wp:column {"className":"skvn-card"} -->
			<div class="wp-block-column skvn-card"><!-- wp:heading {"level":3} --><h3 class="wp-block-heading">Product-specific quote</h3><!-- /wp:heading --><!-- wp:paragraph --><p>Single SKU with size, glaze, and treatment defined. Ideal for repeat orders.</p><!-- /wp:paragraph --></div>
			<!-- /wp:column -->

			<!-- wp:column {"className":"skvn-card"} -->
			<div class="wp-block-column skvn-card"><!-- wp:heading {"level":3} --><h3 class="wp-block-heading">Category quote</h3><!-- /wp:heading --><!-- wp:paragraph --><p>Range pricing across shrimp, pangasius, squid, or mixed container.</p><!-- /wp:paragraph --></div>
			<!-- /wp:column -->

			<!-- wp:column {"className":"skvn-card"} -->
			<div class="wp-block-column skvn-card"><!-- wp:heading {"level":3} --><h3 class="wp-block-heading">Packing / private label quote</h3><!-- /wp:heading --><!-- wp:paragraph --><p>Retail or foodservice packs, carton design, barcode, and language variants.</p><!-- /wp:paragraph --></div>
			<!-- /wp:column -->

			<!-- wp:column {"className":"skvn-card"} -->
			<div class="wp-block-column skvn-card"><!-- wp:heading {"level":3} --><h3 class="wp-block-heading">Export capability inquiry</h3><!-- /wp:heading --><!-- wp:paragraph --><p>Monthly capacity, lead times, and origin documentation by market.</p><!-- /wp:paragraph --></div>
			<!-- /wp:column -->

			<!-- wp:column {"className":"skvn-card"} -->
			<div class="wp-block-column skvn-card"><!-- wp:heading {"level":3} --><h3 class="wp-block-heading">Sample request</h3><!-- /wp:heading --><!-- wp:paragraph --><p>Lab and shipment samples for quality, cooking yield, and packaging checks.</p><!-- /wp:paragraph --></div>
			<!-- /wp:column -->

			<!-- wp:column {"className":"skvn-card"} -->
			<div class="wp-block-column skvn-card"><!-- wp:paragraph {"className":"skvn-section__eyebrow"} --><p class="skvn-section__eyebrow">B2B</p><!-- /wp:paragraph --><!-- wp:heading {"level":3} --><h3 class="wp-block-heading">Campaign or landing-page inquiry</h3><!-- /wp:heading --><!-- wp:paragraph --><p>Seasonal promotions, tender supply, or distributor onboarding.</p><!-- /wp:paragraph --></div>
			<!-- /wp:column -->
		</div>
		<!-- /wp:columns -->
	</div>
	<!-- /wp:group -->

	<!-- wp:group {"align":"full","className":"skvn-kpi-strip","layout":{"type":"constrained"}} -->
	<div class="wp-block-group alignfull skvn-kpi-strip">
		<!-- wp:columns {"className":"skvn-kpi-strip__grid"} -->
		<div class="wp-block-columns skvn-kpi-strip__grid">
			<!-- wp:column {"className":"skvn-kpi-strip__item"} -->
			<div class="wp-block-column skvn-kpi-strip__item"><!-- wp:heading {"level":3} --><h3 class="wp-block-heading">1</h3><!-- /wp:heading --><!-- wp:paragraph --><p><strong>Share requirements</strong><br>Product, volume, destination, packing, and certs.</p><!-- /wp:paragraph --></div>
			<!-- /wp:column -->

			<!-- wp:column {"className":"skvn-kpi-strip__item"} -->
			<div class="wp-block-column skvn-kpi-strip__item"><!-- wp:heading {"level":3} --><h3 class="wp-block-heading">2</h3><!-- /wp:heading --><!-- wp:paragraph --><p><strong>Confirm specification</strong><br>Size grade, glaze, treatment, and label artwork.</p><!-- /wp:paragraph --></div>
			<!-- /wp:column -->

			<!-- wp:column {"className":"skvn-kpi-strip__item"} -->
			<div class="wp-block-column skvn-kpi-strip__item"><!-- wp:heading {"level":3} --><h3 class="wp-block-heading">3</h3><!-- /wp:heading --><!-- wp:paragraph --><p><strong>Receive quotation</strong><br>FOB/CFR/CIF pricing, lead time, and validity.</p><!-- /wp:paragraph --></div>
			<!-- /wp:column -->

			<!-- wp:column {"className":"skvn-kpi-strip__item"} -->
			<div class="wp-block-column skvn-kpi-strip__item"><!-- wp:heading {"level":3} --><h3 class="wp-block-heading">4</h3><!-- /wp:heading --><!-- wp:paragraph --><p><strong>Prepare export documents</strong><br>Health certificate, packing list, COO, and shipment plan.</p><!-- /wp:paragraph --></div>
			<!-- /wp:column -->
		</div>
		<!-- /wp:columns -->
	</div>
	<!-- /wp:group -->

	<!-- wp:group {"align":"full","className":"skvn-section skvn-section--soft","layout":{"type":"constrained"}} -->
	<div class="wp-block-group alignfull skvn-section skvn-section--soft">
		<!-- wp:heading {"className":"skvn-section__heading"} -->
		<h2 class="wp-block-heading skvn-section__heading">Support information</h2>
		<!-- /wp:heading -->

		<!-- wp:columns {"className":"skvn-card-grid skvn-card-grid--3"} -->
		<div class="wp-block-columns skvn-card-grid skvn-card-grid--3">
			<!-- wp:column {"className":"skvn-card skvn-process-card"} -->
			<div class="wp-block-column skvn-card skvn-process-card"><!-- wp:heading {"level":3,"className":"skvn-process-card__title"} --><h3 class="wp-block-heading skvn-process-card__title">Cold-chain handling</h3><!-- /wp:heading --><!-- wp:paragraph --><p>-18 C storage, pre-cooling, temperature loggers, and reefer container monitoring.</p><!-- /wp:paragraph --><!-- wp:paragraph {"className":"skvn-section__eyebrow"} --><p class="skvn-section__eyebrow">Hygiene controlled</p><!-- /wp:paragraph --></div>
			<!-- /wp:column -->

			<!-- wp:column {"className":"skvn-card skvn-process-card"} -->
			<div class="wp-block-column skvn-card skvn-process-card"><!-- wp:heading {"level":3,"className":"skvn-process-card__title"} --><h3 class="wp-block-heading skvn-process-card__title">Export packing</h3><!-- /wp:heading --><!-- wp:paragraph --><p>IQF bulk or retail, inner PE bag, master carton, palletization, and labeling.</p><!-- /wp:paragraph --><!-- wp:paragraph {"className":"skvn-section__eyebrow"} --><p class="skvn-section__eyebrow">Private label ready</p><!-- /wp:paragraph --></div>
			<!-- /wp:column -->

			<!-- wp:column {"className":"skvn-card skvn-process-card"} -->
			<div class="wp-block-column skvn-card skvn-process-card"><!-- wp:paragraph {"className":"skvn-section__eyebrow"} --><p class="skvn-section__eyebrow">Verified</p><!-- /wp:paragraph --><!-- wp:heading {"level":3,"className":"skvn-process-card__title"} --><h3 class="wp-block-heading skvn-process-card__title">Certifications</h3><!-- /wp:heading --><!-- wp:paragraph --><p>HACCP, BRCGS, ISO 22000, EU approval, and catch documentation support.</p><!-- /wp:paragraph --></div>
			<!-- /wp:column -->

			<!-- wp:column {"className":"skvn-card skvn-process-card"} -->
			<div class="wp-block-column skvn-card skvn-process-card"><!-- wp:heading {"level":3,"className":"skvn-process-card__title"} --><h3 class="wp-block-heading skvn-process-card__title">Container planning</h3><!-- /wp:heading --><!-- wp:paragraph --><p>Load optimization for 20 ft RF and 40 ft RF, mixed SKU consolidation, and Incoterms guidance.</p><!-- /wp:paragraph --></div>
			<!-- /wp:column -->
		</div>
		<!-- /wp:columns -->

		<!-- wp:group {"className":"skvn-map-contact__card","layout":{"type":"default"}} -->
		<div class="wp-block-group skvn-map-contact__card">
			<!-- wp:heading {"level":3} -->
			<h3 class="wp-block-heading">Need a fast preliminary price?</h3>
			<!-- /wp:heading -->

			<!-- wp:paragraph -->
			<p>Share destination port and target volume for an indicative FOB/CFR range.</p>
			<!-- /wp:paragraph -->

			<!-- wp:buttons -->
			<div class="wp-block-buttons">
				<!-- wp:button {"className":"skvn-button skvn-button--primary is-style-skvn-primary"} -->
				<div class="wp-block-button skvn-button skvn-button--primary is-style-skvn-primary"><a class="wp-block-button__link wp-element-button" href="#quote-form">Start quote request</a></div>
				<!-- /wp:button -->
			</div>
			<!-- /wp:buttons -->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
```

## required_classes

- `skvn-translated-page`
- `skvn-quote-page`
- `skvn-translated-hero`
- `skvn-quote-hero`
- `skvn-translated-hero__grid`
- `skvn-translated-hero__content`
- `skvn-translated-hero__media`
- `skvn-translated-hero__eyebrow`
- `skvn-translated-hero__heading`
- `skvn-translated-hero__lead`
- `skvn-translated-hero__actions`
- `skvn-translated-hero__image`
- `skvn-hashtag-list`
- `skvn-button`
- `skvn-button--primary`
- `skvn-button--secondary`
- `skvn-quote-context-card`
- `skvn-quote-context-list`
- `skvn-section`
- `skvn-section--soft`
- `skvn-section--support`
- `skvn-section__eyebrow`
- `skvn-section__heading`
- `skvn-section__lead`
- `skvn-card`
- `skvn-card-grid`
- `skvn-card-grid--3`
- `skvn-quote-surface`
- `skvn-quote-surface__grid`
- `skvn-quote-surface__card`
- `skvn-kpi-strip`
- `skvn-kpi-strip__grid`
- `skvn-kpi-strip__item`
- `skvn-process-card`
- `skvn-process-card__title`
- `skvn-map-contact__card`

## missing_theme_classes

- None for layout-critical behavior in this paste-ready markup.

## theme_css_contract

- Use existing full-width SKVN section handling and constrained inner width.
- Use existing quote and translated layout classes for hero/context/form surface.
- Use `skvn-card-grid skvn-card-grid--3` for six route cards and four support cards; this intentionally accepts 3+3 and 3+1 desktop layouts instead of adding new CSS.
- Keep form fields as future CF7 responsibility.

## animation_contract

- No motion translated into content.
- Future reveal or CTA motion must use the shared theme animation runtime and respect `prefers-reduced-motion`.
- Editor behavior should remain static and visible.

## assets_needed

- Placeholder image: `https://placehold.co/800x320/eaf7ff/073b5a?text=Export+Ready+Containers`.
- Future real export/cold-chain image can replace the placeholder through a core Image block.
- Future CF7 shortcode/form block required when form handling returns to scope.

## not_translated

- `<!DOCTYPE>`, `<html>`, `<head>`, `<body>`, prototype header, nav, and footer.
- Raw Tailwind utility classes and raw hex color declarations.
- Active HTML inputs, textarea, button submit behavior, and any custom form handling.
- Decorative badge layout that depends only on utility classes.

## risks

- This is a visual/request-surface translation, not a working lead-capture implementation.
- Anchor IDs should be verified after paste because Gutenberg can strip unsupported attributes in some editing flows.
- Placeholder image should be replaced with a real export/cold-chain image before production.
