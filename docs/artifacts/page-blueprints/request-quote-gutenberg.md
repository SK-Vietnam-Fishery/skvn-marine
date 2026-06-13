# Request Quote Page — Gutenberg Translation

## project_contract

- Theme root: `wp-content/themes/skvn-marine`
- Plugin root: `wp-content/plugins/skvn-marine-blocks`
- CSS prefix: `skvn-`
- Allowed output: WordPress core blocks plus existing SKVN theme classes.
- Ownership: visual contract belongs to the theme; future HTML-2-Gutenberg tooling belongs to `skvn-marine-blocks`.

## css_source_scan

- `style.css` implements SKVN tokens for blue, mint, gold, sky, slate, white, radius, shadow, border, content width, and wide width.
- `theme.json` exposes matching Gutenberg palette slugs: `skvn-blue-950`, `skvn-blue-900`, `skvn-blue-700`, `skvn-mint-100`, `skvn-gold-300`, `skvn-teal-600`, `skvn-sky-50`, `skvn-slate-700`, `skvn-white`.
- Registered block styles include `is-style-skvn-primary`, `is-style-skvn-section`, `is-style-skvn-card`, and `is-style-skvn-rounded-media`.
- Existing request quote pattern already defines `skvn-quote-page`, `skvn-quote-hero`, `skvn-quote-context-card`, `skvn-quote-surface`, `skvn-quote-surface__grid`, and `skvn-quote-surface__card`.
- Existing reusable card/grid classes include `skvn-card`, `skvn-card-grid skvn-card-grid--3`, `skvn-feature-card`, `skvn-process-card`, and `skvn-process-card__step`.

## brand_source_scan

- Artifact uses deep navy `#073b5a`, trust blue `#0f5c8c`, primary blue `#1e79be`, sky `#eaf7ff`, mint `#ddfaf4`, gold `#e9c766`, border `#dbe7ef`, and slate copy `#475569`.
- Visual direction is B2B export/procurement: structured hero, quote parameter card, process strip, inquiry surface, route cards, capability cards.
- Radius is small, card treatment is quiet, and CTA emphasis is primary blue.

## brand_mapping

- `#073b5a` -> `--skvn-color-blue-950` / `skvn-blue-950`
- `#0f5c8c` -> `--skvn-color-blue-900` / `skvn-blue-900`
- `#1e79be` -> `--skvn-color-blue-700` / `skvn-blue-700`
- `#eaf7ff` -> `--skvn-color-sky-50` / `skvn-sky-50`
- `#ddfaf4` -> `--skvn-color-mint-100` / `skvn-mint-100`
- `#e9c766` -> `--skvn-color-gold-300` / `skvn-gold-300`
- Card surfaces -> `skvn-card`, `skvn-quote-context-card`, `skvn-quote-surface__card`
- Primary CTA -> `skvn-button skvn-button--primary is-style-skvn-primary`

## brand_mismatch

- No major mismatch. Gold is used only as a premium/certification cue, which matches project rules.
- The disabled mock form is not production form behavior and should not become custom HTML form handling in V1.

## token_changes_needed

- None required for paste-ready Gutenberg using existing classes.
- Visual parity would improve with future theme CSS for quote-form field previews, but that should remain theme CSS or CF7 styling, not inline Gutenberg content.

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
				<p class="skvn-translated-hero__eyebrow">Request Quotation</p>
				<!-- /wp:paragraph -->

				<!-- wp:heading {"level":1,"className":"skvn-translated-hero__heading"} -->
				<h1 class="wp-block-heading skvn-translated-hero__heading">Specify Seafood Products, Packing, &amp; Delivery Requirements</h1>
				<!-- /wp:heading -->

				<!-- wp:paragraph {"className":"skvn-translated-hero__lead"} -->
				<p class="skvn-translated-hero__lead">Our dedicated sales and export desk reviews product specifications, required volume metrics, final destination port criteria, and custom packaging logistics to deliver commercial, container-scale contract terms.</p>
				<!-- /wp:paragraph -->

				<!-- wp:buttons {"className":"skvn-translated-hero__actions"} -->
				<div class="wp-block-buttons skvn-translated-hero__actions">
					<!-- wp:button {"className":"skvn-button skvn-button--primary is-style-skvn-primary"} -->
					<div class="wp-block-button skvn-button skvn-button--primary is-style-skvn-primary"><a class="wp-block-button__link wp-element-button" href="#quote-form-surface">Start Quote Request</a></div>
					<!-- /wp:button -->

					<!-- wp:button {"className":"skvn-button skvn-button--secondary"} -->
					<div class="wp-block-button skvn-button skvn-button--secondary"><a class="wp-block-button__link wp-element-button" href="#paths-section">Review Product Categories</a></div>
					<!-- /wp:button -->
				</div>
				<!-- /wp:buttons -->
			</div>
			<!-- /wp:column -->

			<!-- wp:column {"verticalAlignment":"center","className":"skvn-translated-hero__media"} -->
			<div class="wp-block-column is-vertically-aligned-center skvn-translated-hero__media">
				<!-- wp:group {"className":"skvn-quote-context-card","layout":{"type":"default"}} -->
				<div class="wp-block-group skvn-quote-context-card">
					<!-- wp:heading {"level":2} -->
					<h2 class="wp-block-heading">Standard Export Parameters</h2>
					<!-- /wp:heading -->

					<!-- wp:paragraph {"className":"skvn-section__eyebrow"} -->
					<p class="skvn-section__eyebrow">Premium Quality</p>
					<!-- /wp:paragraph -->

					<!-- wp:list -->
					<ul>
						<li><strong>Product Interest &amp; Specification:</strong> Frozen HG, HGT, Whole Round, Fillets, or Steaks. Precision sizing gradings.</li>
						<li><strong>Estimated Volume / Container Need:</strong> FCL shipments, 20ft or 40ft reefer containers, single or multi-port allocations.</li>
						<li><strong>Destination Country &amp; Incoterms Port:</strong> CIF, CFR, or FOB pricing arrays tailored to international commercial maritime entry points.</li>
						<li><strong>Packing &amp; Private Label Schemes:</strong> Master cartons, IQF bags, shattered packs, or block frozen configurations with custom branding options.</li>
						<li><strong>Certification &amp; Documentation:</strong> HACCP, BRCGS, IFS, MSC, Halal, health certificates, and country-specific clearance protocols.</li>
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

	<!-- wp:group {"align":"full","className":"skvn-kpi-strip","layout":{"type":"constrained"}} -->
	<div class="wp-block-group alignfull skvn-kpi-strip">
		<!-- wp:columns {"className":"skvn-kpi-strip__grid"} -->
		<div class="wp-block-columns skvn-kpi-strip__grid">
			<!-- wp:column {"className":"skvn-kpi-strip__item"} -->
			<div class="wp-block-column skvn-kpi-strip__item"><!-- wp:heading {"level":3} --><h3 class="wp-block-heading">1</h3><!-- /wp:heading --><!-- wp:paragraph --><p><strong>Share Requirements</strong><br>Submit quantities, grading parameters, and port protocols via the quote form.</p><!-- /wp:paragraph --></div>
			<!-- /wp:column -->

			<!-- wp:column {"className":"skvn-kpi-strip__item"} -->
			<div class="wp-block-column skvn-kpi-strip__item"><!-- wp:heading {"level":3} --><h3 class="wp-block-heading">2</h3><!-- /wp:heading --><!-- wp:paragraph --><p><strong>Confirm Specification</strong><br>Export managers verify availability, sizing tiers, and processing compliance rules.</p><!-- /wp:paragraph --></div>
			<!-- /wp:column -->

			<!-- wp:column {"className":"skvn-kpi-strip__item"} -->
			<div class="wp-block-column skvn-kpi-strip__item"><!-- wp:heading {"level":3} --><h3 class="wp-block-heading">3</h3><!-- /wp:heading --><!-- wp:paragraph --><p><strong>Receive Quotation</strong><br>Access cost, packaging, and ocean freight quotation details.</p><!-- /wp:paragraph --></div>
			<!-- /wp:column -->

			<!-- wp:column {"className":"skvn-kpi-strip__item"} -->
			<div class="wp-block-column skvn-kpi-strip__item"><!-- wp:heading {"level":3} --><h3 class="wp-block-heading">4</h3><!-- /wp:heading --><!-- wp:paragraph --><p><strong>Prepare Logistics</strong><br>Authorize production schedules, cold-chain handling, and container manifest routing.</p><!-- /wp:paragraph --></div>
			<!-- /wp:column -->
		</div>
		<!-- /wp:columns -->
	</div>
	<!-- /wp:group -->

	<!-- wp:group {"align":"full","metadata":{"name":"quote-form-surface"},"className":"skvn-section skvn-section--soft skvn-quote-surface","layout":{"type":"constrained"}} -->
	<div class="wp-block-group alignfull skvn-section skvn-section--soft skvn-quote-surface" id="quote-form-surface">
		<!-- wp:paragraph {"className":"skvn-section__eyebrow"} -->
		<p class="skvn-section__eyebrow">Commercial Inquiry</p>
		<!-- /wp:paragraph -->

		<!-- wp:heading {"className":"skvn-section__heading"} -->
		<h2 class="wp-block-heading skvn-section__heading">Commercial Inquiry Formulation</h2>
		<!-- /wp:heading -->

		<!-- wp:paragraph {"className":"skvn-section__lead"} -->
		<p class="skvn-section__lead">Complete all core operational specifications to expedite processing by regional division directors. Standard turnaround: less than 24 working hours.</p>
		<!-- /wp:paragraph -->

		<!-- wp:group {"className":"skvn-card skvn-quote-surface__card","layout":{"type":"default"}} -->
		<div class="wp-block-group skvn-card skvn-quote-surface__card">
			<!-- wp:heading {"level":3} -->
			<h3 class="wp-block-heading">Form placeholder</h3>
			<!-- /wp:heading -->

			<!-- wp:paragraph -->
			<p><strong>System context metadata passed:</strong> product_id: SF-9042; category: frozen_pelagic; intent: wholesale_fcl; source: utm_direct_b2b.</p>
			<!-- /wp:paragraph -->

			<!-- wp:columns {"className":"skvn-quote-surface__grid"} -->
			<div class="wp-block-columns skvn-quote-surface__grid">
				<!-- wp:column -->
				<div class="wp-block-column"><!-- wp:group {"className":"skvn-card skvn-quote-surface__card","layout":{"type":"default"}} --><div class="wp-block-group skvn-card skvn-quote-surface__card"><!-- wp:heading {"level":3} --><h3 class="wp-block-heading">Buyer Details</h3><!-- /wp:heading --><!-- wp:paragraph --><p>Full name, registered company name, corporate email, and country of incorporation.</p><!-- /wp:paragraph --></div><!-- /wp:group --></div>
				<!-- /wp:column -->

				<!-- wp:column -->
				<div class="wp-block-column"><!-- wp:group {"className":"skvn-card skvn-quote-surface__card","layout":{"type":"default"}} --><div class="wp-block-group skvn-card skvn-quote-surface__card"><!-- wp:heading {"level":3} --><h3 class="wp-block-heading">Product Scope</h3><!-- /wp:heading --><!-- wp:paragraph --><p>Product classification, contract volume target, destination port, and Incoterms delivery details.</p><!-- /wp:paragraph --></div><!-- /wp:group --></div>
				<!-- /wp:column -->

				<!-- wp:column -->
				<div class="wp-block-column"><!-- wp:group {"className":"skvn-card skvn-quote-surface__card","layout":{"type":"default"}} --><div class="wp-block-group skvn-card skvn-quote-surface__card"><!-- wp:heading {"level":3} --><h3 class="wp-block-heading">Documentation</h3><!-- /wp:heading --><!-- wp:paragraph --><p>Packaging configuration, HACCP, EU approval, BRCGS, IFS, MSC, and customs documentation requirements.</p><!-- /wp:paragraph --></div><!-- /wp:group --></div>
				<!-- /wp:column -->
			</div>
			<!-- /wp:columns -->

			<!-- wp:paragraph -->
			<p><strong>Notice on Wholesale Operations:</strong> All price agreements are calculated on current ocean line indexes, harvest availability, and verified commercial funding authorization. We do not service individual retail residential supply orders.</p>
			<!-- /wp:paragraph -->

			<!-- wp:buttons -->
			<div class="wp-block-buttons">
				<!-- wp:button {"className":"skvn-button skvn-button--primary is-style-skvn-primary"} -->
				<div class="wp-block-button skvn-button skvn-button--primary is-style-skvn-primary"><a class="wp-block-button__link wp-element-button" href="/contact/">Transmit Secure Quote Request Array</a></div>
				<!-- /wp:button -->
			</div>
			<!-- /wp:buttons -->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->

	<!-- wp:group {"align":"full","metadata":{"name":"paths-section"},"className":"skvn-section","layout":{"type":"constrained"}} -->
	<div class="wp-block-group alignfull skvn-section" id="paths-section">
		<!-- wp:group {"className":"skvn-section__header--centered","layout":{"type":"default"}} -->
		<div class="wp-block-group skvn-section__header--centered">
			<!-- wp:heading {"className":"skvn-section__heading"} -->
			<h2 class="wp-block-heading skvn-section__heading">Structured Procurement Route Options</h2>
			<!-- /wp:heading -->

			<!-- wp:paragraph {"className":"skvn-section__lead"} -->
			<p class="skvn-section__lead">Select the processing vector that aligns directly with your corporate import workflow mechanisms.</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:group -->

		<!-- wp:columns {"className":"skvn-card-grid skvn-card-grid--3"} -->
		<div class="wp-block-columns skvn-card-grid skvn-card-grid--3">
			<!-- wp:column {"className":"skvn-card"} -->
			<div class="wp-block-column skvn-card"><!-- wp:paragraph {"className":"skvn-section__eyebrow"} --><p class="skvn-section__eyebrow">SKU Direct</p><!-- /wp:paragraph --><!-- wp:heading {"level":3} --><h3 class="wp-block-heading">Product-Specific Quote</h3><!-- /wp:heading --><!-- wp:paragraph --><p>Direct inquiry for defined individual species variants like Vannamei Shrimp, Atlantic Salmon, or Skipjack Tuna with set metrics.</p><!-- /wp:paragraph --><!-- wp:paragraph --><p><a href="/request-a-quote/">Initiate SKU Pipeline</a></p><!-- /wp:paragraph --></div>
			<!-- /wp:column -->

			<!-- wp:column {"className":"skvn-card"} -->
			<div class="wp-block-column skvn-card"><!-- wp:paragraph {"className":"skvn-section__eyebrow"} --><p class="skvn-section__eyebrow">Bulk Category</p><!-- /wp:paragraph --><!-- wp:heading {"level":3} --><h3 class="wp-block-heading">Category Quote</h3><!-- /wp:heading --><!-- wp:paragraph --><p>Multi-product request matrices designed for mixed container optimization or annual category supply programs.</p><!-- /wp:paragraph --><!-- wp:paragraph --><p><a href="/request-a-quote/">Initiate Mixed Matrix</a></p><!-- /wp:paragraph --></div>
			<!-- /wp:column -->

			<!-- wp:column {"className":"skvn-card"} -->
			<div class="wp-block-column skvn-card"><!-- wp:paragraph {"className":"skvn-section__eyebrow"} --><p class="skvn-section__eyebrow">Private Label</p><!-- /wp:paragraph --><!-- wp:heading {"level":3} --><h3 class="wp-block-heading">Packing / Private Label</h3><!-- /wp:heading --><!-- wp:paragraph --><p>Custom packaging parameters including custom consumer bags, weight grading strategies, and private corporate branding integration.</p><!-- /wp:paragraph --><!-- wp:paragraph --><p><a href="/request-a-quote/">Initiate Label Scope</a></p><!-- /wp:paragraph --></div>
			<!-- /wp:column -->

			<!-- wp:column {"className":"skvn-card"} -->
			<div class="wp-block-column skvn-card"><!-- wp:paragraph {"className":"skvn-section__eyebrow"} --><p class="skvn-section__eyebrow">Compliance</p><!-- /wp:paragraph --><!-- wp:heading {"level":3} --><h3 class="wp-block-heading">Export Capability Inquiry</h3><!-- /wp:heading --><!-- wp:paragraph --><p>Technical assessment requests mapping facility approvals to national hygiene, customs regulations, and border controls.</p><!-- /wp:paragraph --><!-- wp:paragraph --><p><a href="/request-a-quote/">Request Audit Dossier</a></p><!-- /wp:paragraph --></div>
			<!-- /wp:column -->

			<!-- wp:column {"className":"skvn-card"} -->
			<div class="wp-block-column skvn-card"><!-- wp:paragraph {"className":"skvn-section__eyebrow"} --><p class="skvn-section__eyebrow">Evaluation</p><!-- /wp:paragraph --><!-- wp:heading {"level":3} --><h3 class="wp-block-heading">Sample Request Path</h3><!-- /wp:heading --><!-- wp:paragraph --><p>Formal authorization tracks for physical air freight sample evaluation lots before full container commitment signing.</p><!-- /wp:paragraph --><!-- wp:paragraph --><p><a href="/request-a-quote/">Request Evaluation Lot</a></p><!-- /wp:paragraph --></div>
			<!-- /wp:column -->

			<!-- wp:column {"className":"skvn-card"} -->
			<div class="wp-block-column skvn-card"><!-- wp:paragraph {"className":"skvn-section__eyebrow"} --><p class="skvn-section__eyebrow">Campaign</p><!-- /wp:paragraph --><!-- wp:heading {"level":3} --><h3 class="wp-block-heading">Campaign / Landing Request</h3><!-- /wp:heading --><!-- wp:paragraph --><p>Streamlined routing for prospective buyers migrating from trade exhibitions or promotional events.</p><!-- /wp:paragraph --><!-- wp:paragraph --><p><a href="/request-a-quote/">Connect Campaign Desk</a></p><!-- /wp:paragraph --></div>
			<!-- /wp:column -->
		</div>
		<!-- /wp:columns -->
	</div>
	<!-- /wp:group -->

	<!-- wp:group {"align":"full","className":"skvn-section skvn-section--soft","layout":{"type":"constrained"}} -->
	<div class="wp-block-group alignfull skvn-section skvn-section--soft">
		<!-- wp:group {"className":"skvn-section__header--centered","layout":{"type":"default"}} -->
		<div class="wp-block-group skvn-section__header--centered">
			<!-- wp:heading {"className":"skvn-section__heading"} -->
			<h2 class="wp-block-heading skvn-section__heading">Infrastructure Standards &amp; Capabilities</h2>
			<!-- /wp:heading -->

			<!-- wp:paragraph {"className":"skvn-section__lead"} -->
			<p class="skvn-section__lead">Validating institutional control parameters across integrated cold chain supply links.</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:group -->

		<!-- wp:columns {"className":"skvn-card-grid skvn-card-grid--3"} -->
		<div class="wp-block-columns skvn-card-grid skvn-card-grid--3">
			<!-- wp:column {"className":"skvn-card skvn-process-card"} -->
			<div class="wp-block-column skvn-card skvn-process-card"><!-- wp:paragraph {"className":"skvn-process-card__step"} --><p class="skvn-process-card__step">01</p><!-- /wp:paragraph --><!-- wp:heading {"level":3,"className":"skvn-process-card__title"} --><h3 class="wp-block-heading skvn-process-card__title">Cold-Chain Handling</h3><!-- /wp:heading --><!-- wp:paragraph --><p>Unbroken refrigeration chains at continuous -18°C or lower from harvest processing to point of discharge.</p><!-- /wp:paragraph --></div>
			<!-- /wp:column -->

			<!-- wp:column {"className":"skvn-card skvn-process-card"} -->
			<div class="wp-block-column skvn-card skvn-process-card"><!-- wp:paragraph {"className":"skvn-process-card__step"} --><p class="skvn-process-card__step">02</p><!-- /wp:paragraph --><!-- wp:heading {"level":3,"className":"skvn-process-card__title"} --><h3 class="wp-block-heading skvn-process-card__title">Export Packing</h3><!-- /wp:heading --><!-- wp:paragraph --><p>Reinforced corrugated outer master containers with water-resistant inner lining for international ocean freight handling.</p><!-- /wp:paragraph --></div>
			<!-- /wp:column -->

			<!-- wp:column {"className":"skvn-card skvn-process-card"} -->
			<div class="wp-block-column skvn-card skvn-process-card"><!-- wp:paragraph {"className":"skvn-process-card__step"} --><p class="skvn-process-card__step">03</p><!-- /wp:paragraph --><!-- wp:heading {"level":3,"className":"skvn-process-card__title"} --><h3 class="wp-block-heading skvn-process-card__title">Certifications</h3><!-- /wp:heading --><!-- wp:paragraph --><p>Institutional verification alignment including inspection tracking and customs tariff compliance clearance tags.</p><!-- /wp:paragraph --></div>
			<!-- /wp:column -->

			<!-- wp:column {"className":"skvn-card skvn-process-card"} -->
			<div class="wp-block-column skvn-card skvn-process-card"><!-- wp:paragraph {"className":"skvn-process-card__step"} --><p class="skvn-process-card__step">04</p><!-- /wp:paragraph --><!-- wp:heading {"level":3,"className":"skvn-process-card__title"} --><h3 class="wp-block-heading skvn-process-card__title">Container Planning</h3><!-- /wp:heading --><!-- wp:paragraph --><p>Precise weight distribution for high-cube reefer equipment while maintaining maximum payload density.</p><!-- /wp:paragraph --></div>
			<!-- /wp:column -->
		</div>
		<!-- /wp:columns -->
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
- `skvn-button`
- `skvn-button--primary`
- `skvn-button--secondary`
- `skvn-quote-context-card`
- `skvn-kpi-strip`
- `skvn-kpi-strip__grid`
- `skvn-kpi-strip__item`
- `skvn-section`
- `skvn-section--soft`
- `skvn-section__eyebrow`
- `skvn-section__heading`
- `skvn-section__lead`
- `skvn-section__header--centered`
- `skvn-card`
- `skvn-card-grid`
- `skvn-card-grid--3`
- `skvn-quote-surface`
- `skvn-quote-surface__grid`
- `skvn-quote-surface__card`
- `skvn-process-card`
- `skvn-process-card__step`
- `skvn-process-card__title`

## missing_theme_classes

- None for layout-critical behavior in the paste-ready markup.

## theme_css_contract

- Uses existing full-width section and constrained inner width behavior.
- Uses SKVN token-backed blue-first palette, mint support surfaces, and limited gold premium cue.
- Uses existing card/grid classes instead of raw Tailwind utility classes.
- The quote form remains a visual placeholder until approved form implementation returns to scope.

## animation_contract

- No artifact motion translated into content.
- Future reveal/hover animation, if added, must use shared theme runtime and respect `prefers-reduced-motion`.
- Editor behavior must stay visible/static.

## assets_needed

- No images required for this conversion.
- Future real form embed or CF7 shortcode required when form handling returns to scope.

## not_translated

- Raw Tailwind classes.
- Inline prototype colors.
- Disabled mock form inputs/selects/checkboxes/textarea.
- Any custom form submission handler.

## risks

- The form surface is visual-only. Production lead capture still needs the approved CF7/CFDB7 flow.
- The page uses anchor IDs via Gutenberg group `id` attributes; verify WordPress preserves them in the editor.
- Four capability cards use the existing 3-column grid, so desktop layout becomes 3 + 1 instead of the artifact's 4-column row unless theme CSS is extended later.
