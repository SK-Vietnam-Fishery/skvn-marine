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
- Spam baseline: Antispam Bee for comments, separate protection for CF7 forms
- Image strategy: WebP, SEO filenames, auto ALT from attachment title if empty

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
- Polylang multilingual if activated
