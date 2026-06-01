# THEME — skvn-marine

## [manual] Role

GeneratePress child theme for SKVN Marine visual system.

## [manual] Responsibilities

- Theme styles and tokens.
- GeneratePress child customization.
- Page templates and full-width layout surfaces.
- Block styles.
- Patterns.
- Woo visual overrides.
- Media ALT helper.
- Frontend animation runtime.

## [manual] Design Direction

- Use a blue-first system for V1 visual direction.
- Primary CTA remains blue, not mint or gold.
- Soft mint is the main support accent for freshness, hygiene, cold-chain, factory/process, trust strip icons, support cards, and form support surfaces.
- Bright gold is only a limited premium cue for certification, premium species badges, buyer-value highlights, or top-grade labels.
- Do not use gold as a dominant background.

Current token intent:

- Deep navy: `#073b5a`
- Trust blue: `#0f5c8c`
- Primary blue: `#1e79be`
- Fresh sky: `#eaf7ff`
- Support mint: `#ddfaf4`
- Premium gold: `#e9c766`
- Text slate: `#0f172a`

0.5.1 visual hotfix:

- Translated artifact heroes should use fresh sky/white surfaces instead of full navy backgrounds by default.
- Deep navy remains available for text, footer, and trust surfaces.
- Brand profile/sidebar color controls remain deferred to V1 / 0.7.0; do not add a new token UI in 0.5.1.

## [manual] Layout Direction

- 0.5.0 introduces a reusable page layout named `SKVN Full Width`.
- The layout is not landing-page specific. It is the general canvas for homepage, product overview, campaign, and UI test pages that need full-width Gutenberg sections.
- Keep GeneratePress header/footer intact.
- Remove or bypass the narrow default content wrapper only for pages explicitly using this template/layout.
- `alignfull` sections should reach viewport width.
- Inner content must stay constrained by SKVN design widths.
- Do not fix this by editing GeneratePress parent files.
- Do not create a custom block for this layout in V1.

## [manual] Page Display Controls

- 0.5.1 introduces page-level controls for marketing-owned pages that need alternate chrome.
- Required controls: Hide site header, Hide site footer.
- Store per-page values in post meta using `_skvn_` meta keys.
- The frontend must apply controls without editing GeneratePress parent files.
- Prefer editor/admin UI controls over raw class names.
- Do not add a header/footer builder plugin by default.
- Page preset `SKVN Landing Canvas` groups the standard marketing-page setup into one editor action: hide page title and enable the full-width/no-sidebar SKVN canvas.
- The preset is stored in `_skvn_page_display_preset`; direct toggles remain available for overrides and debugging.
- Do not blindly write GeneratePress layout metabox meta. Use SKVN meta/classes and safe GeneratePress filters where audited.
- Design debt for V1 / 0.8.0: frontend title visibility must be separated from editor title identity. A hidden frontend title should not make the Gutenberg title field/identity surface disappear while editing.

## [manual] HTML-2-Gutenberg Boundary

Theme owns the visual output contract for HTML-2-Gutenberg, not the translator tool.

Theme responsibilities:

- `skvn-*` class styling.
- Design tokens.
- Block styles.
- Patterns.
- Editor/frontend CSS parity.
- Background/decorative animation and shared animation runtime.

Plugin `skvn-marine-blocks` owns the HTML-2-Gutenberg tool itself:

- HTML artifact intake.
- Translation workflow/tooling.
- Validation.
- Future admin publisher/create-page flow.

Do not implement HTML-2-Gutenberg admin/tooling logic in the theme.

## [manual] HTML-2-Gutenberg CSS Families

HTML-2-Gutenberg output must reuse implemented theme CSS families before inventing new `skvn-*` classes.

Implemented translated-layout families in `wp-content/themes/skvn-marine/style.css`:

- `skvn-translated-page`
- `skvn-translated-hero`
- `skvn-translated-hero__grid`
- `skvn-translated-hero__content`
- `skvn-translated-hero__media`
- `skvn-translated-hero__eyebrow`
- `skvn-translated-hero__heading`
- `skvn-translated-hero__lead`
- `skvn-translated-hero__actions`
- `skvn-translated-hero__image`
- `skvn-kpi-strip`
- `skvn-kpi-strip__grid`
- `skvn-kpi-strip__item`
- `skvn-translated-split`
- `skvn-translated-split__grid`
- `skvn-placeholder-media`
- `skvn-section__eyebrow`
- `skvn-section__heading`
- `skvn-section__lead`
- `skvn-card`
- `skvn-button`
- `skvn-button--primary`
- `skvn-button--secondary`
- registered button style `is-style-skvn-primary`

Reference pattern:

- `wp-content/themes/skvn-marine/patterns/artifact-exporter-test.php`

Translator rule:

- Reuse `skvn-translated-*`, `skvn-kpi-strip*`, `skvn-section__*`, `skvn-placeholder-media`, `skvn-card`, and `is-style-skvn-*` when translating generic HTML artifacts.
- Do not create new layout-critical families like `skvn-hero__*`, `skvn-card-grid`, `skvn-logo-grid`, or `skvn-testimonial-grid` unless their theme CSS is implemented in the same task or they are explicitly listed in `missing_theme_classes`.
- If exact artifact layout requires classes not yet implemented, output native Gutenberg structure first and record the CSS contract separately.

## [manual] Site Branding Governance

Brand customization is allowed, but brand variables and assets must be managed through the registry in `docs/standards/site-branding-guideline.md`.

Before coding any branding, visual token, theme screenshot, pattern identity, or editor visual parity change, update or verify `docs/standards/site-branding-guideline.md` first. Do not implement scattered branding changes until the docs state which file owns the variable, class, or asset.

Theme-owned brand sources:

- CSS tokens and component styles: `wp-content/themes/skvn-marine/style.css`.
- Gutenberg presets: `wp-content/themes/skvn-marine/theme.json`.
- Editor style loading: `wp-content/themes/skvn-marine/inc/setup.php`.
- Patterns: `wp-content/themes/skvn-marine/patterns/*.php`.
- Theme screenshot: `wp-content/themes/skvn-marine/screenshot.png`.

Do not move the primary brand system into the plugin. Plugin assets may follow the brand, but do not define it.

HTML-2-Gutenberg brand-mapping contract for 0.5.1:

- Translator output must report `brand_source_scan`, `brand_mapping`, `brand_mismatch`, and `token_changes_needed`.
- Artifact colors, Tailwind color utilities, fonts, radius, shadows, and spacing cues are input hints only.
- Production output must map those cues to SKVN theme tokens, existing `skvn-*` classes, Gutenberg presets, or block styles.
- If the current theme token set cannot represent an artifact cue, record it in `token_changes_needed`; do not put raw hex/rgb/hsl values or Tailwind color utilities into Gutenberg content as the production contract.
- Theme token implementation and brand profile work remain parked until V1 / 0.7.0. Do not code a brand profile UI or new token system during 0.5.1.

0.5.1 Tailwind artifact CSS hardening decision:

- `docs/decisions/css-change-logs.md` records human-approved CSS hardening discovered while generating test data from real Tailwind web artifacts.
- This is allowed inside 0.5.1 only as visual contract hardening for existing theme CSS/classes; it does not move HTML-2-Gutenberg tooling, publisher UI, brand profile, or token-system implementation into the theme.
- Before marking a class as missing or adding CSS, scan `wp-content/themes/skvn-marine/style.css` first and reuse existing `skvn-*` families.

## [manual] Forbidden

- Do not edit parent GeneratePress.
- Do not register complex custom Gutenberg blocks here.
- Do not handle quote form submissions manually in V1.
