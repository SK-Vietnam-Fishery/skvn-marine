# SKVN Marine Architecture

## Project Goal

V1 is a single B2B marine/fishery website with WooCommerce catalog, blog content, and a Request a Quote flow.

V3 may evolve into a reusable base theme for multiple websites.

## Core Decisions

- Theme: `skvn-marine`
- Theme type: GeneratePress child theme, hybrid approach
- Plugin: `skvn-marine-blocks`
- Block namespace: `skvn-marine`
- Tailwind layer: WindPress
- Product model: WooCommerce products, categories, and attributes
- Quote form: Contact Form 7
- Lead storage: CFDB7
- Lead automation: n8n webhook
- SEO: Rank Math + GEO/AEO-oriented content structure
- Multilingual candidate: Polylang
- Map engine: OpenStreetMap iframe embed for V1
- Spam baseline: Antispam Bee for comments, separate protection for CF7 forms
- Image strategy: WebP, SEO filenames, auto ALT from attachment title if empty

## Hybrid Theme Direction Review

Status: **accepted for V1**

V1 keeps the hybrid architecture:

- GeneratePress remains the parent theme.
- `skvn-marine` remains a child theme for design system, templates, patterns, and visual overrides.
- `skvn-marine-blocks` remains the plugin for custom Gutenberg blocks with interaction logic.
- WooCommerce native products/blocks/patterns remain the product baseline for V1.

Rationale:

- V1 needs a stable WooCommerce site sooner than it needs a pure block theme architecture.
- GeneratePress reduces risk around header/footer, template hierarchy, WooCommerce compatibility, and classic-theme fallback.
- A future block/custom base theme can be evaluated after the design system and product information architecture stabilize.

### In Scope — Do Soon

- Keep improving `SKVN Full Width` as the reusable page canvas for homepage, product overview, campaign, and UI test pages.
- Verify whether GeneratePress wrappers such as `.inside-article` still add padding in runtime; add child-theme CSS only if the runtime test confirms it.
- Add `screenshot.png` to the child theme for WordPress admin theme preview.
- Keep deploy artifacts self-contained under `build/wp-content/themes/skvn-marine` and `build/wp-content/plugins/skvn-marine-blocks`.
- Keep onsite deploy requirements explicit: GeneratePress parent installed, WordPress/PHP version checked, WooCommerce memory limits set, backup/rollback ready, and error logs collected after activation.
- Gradually move new design tokens toward `theme.json` / WordPress variables so the editor and frontend stay aligned.
- Bump theme/plugin versions before a tagged onsite test build.

### Out of Scope — Proposal for Later

- Do not replace GeneratePress with a pure block theme in V1.
- Do not add a Tailwind/PostCSS build pipeline until there is a confirmed need beyond WindPress/current CSS.
- Do not move all patterns into a new registration architecture unless the current `patterns/*.php` approach becomes insufficient.
- Do not add Composer or PHP package management in V1.
- Do not build GitHub Actions release zip automation in V1.
- Consider a `skvn-block-experiment` branch in V2/V3 to evaluate a pure block theme using the stabilized `theme.json`, patterns, and tokens.
- Consider scoped block CSS and stricter performance budgets during the 1.0 performance pass.

## Boundary Rules

### Theme owns

- Visual system
- GeneratePress child theme customization
- `theme.json`
- Block styles for core blocks
- Patterns
- Frontend/editor CSS
- Animation runtime
- WooCommerce visual overrides
- Map/contact section wrapper, including overlay contact card styling
- Media helpers, including image ALT automation

### Plugin owns

- Custom Gutenberg blocks with logic
- Slider / Slide
- Accordion
- Product Grid / Product List
- Future Quote CTA / Quote Cart blocks if needed

### External plugins own

- WooCommerce product/catalog engine
- Contact Form 7 form handling
- CFDB7 lead table/storage
- n8n lead automation
- Rank Math SEO
- Antispam Bee comment spam protection
- Polylang multilingual if activated

## Map / Contact Section Direction

Use an OpenStreetMap iframe embed for V1. The SKVN theme owns the section composition and visual treatment.

Reason:

- Target shared host supports PHP 8.0 only.
- `Out of the Block: OpenStreetMap` requires PHP 8.1.
- The V1 map/contact requirement can be satisfied with an iframe, core content, and SKVN theme CSS.

Target layout:

- Full-width map panel.
- Large blue map pin emphasis on the left side.
- Floating white contact card over the map on desktop, aligned right.
- Contact card includes company name, address, phone, and email.
- Dark blue surrounding band or bottom frame can be used when it supports the page composition.
- On mobile, stack the contact card above or below the map instead of forcing an overlay.
- Do not add a replacement map plugin in V1 unless iframe embed proves insufficient.

### Recommended runtime plugins

- WooCommerce
- WindPress
- Rank Math SEO
- Contact Form 7
- Contact Form CFDB7
- Antispam Bee
