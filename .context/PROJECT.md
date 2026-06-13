# PROJECT — SKVN Marine

## [manual] Decisions

- V1 is a single B2B marine/fishery website.
- V3 may become a reusable base theme.
- Use GeneratePress child theme, not a fork, for V1.
- Use WooCommerce native product model.
- Page display/sidebar controls completed in 0.5.1; Quote UI/editor controls completed in 0.6.0; basic CF7/CFDB7 quote form source/docs completed in 0.7.0; 0.7.1 verifies quote-flow runtime handoff; 0.8.0 adds SKVN Editor Controls; 0.9.0 adds Footer Page Settings; 0.10.0 closes onsite UI/test debt for footer and CF7 interface; remaining CF7 data-flow and map block/display testing moves to 1.1.2; n8n is deferred until after 1.0.0.
- Use Rank Math for SEO and GEO/AEO-friendly content structure.
- Use Polylang as multilingual candidate.
- WindPress/Tailwind may be used as a prototyping aid, but production visual-system implementation should be SKVN theme tokens/classes and must not require WindPress for future plugin portability.
- Use TypeScript for custom Gutenberg block plugin.
- Use Swiper for slider.
- Product sections started with WooCommerce native blocks/patterns. V1 / 1.3.3 now plans custom dynamic SKVN Product/Post Collection blocks for Product Grid, Product Carousel, Post Grid, and Post Carousel; Product Taxonomy Collections admin and advanced filtering remain deferred to 2.x.x or later.
- Plugin architecture: block-centric feature modules with minimal shared services. Blocks own feature logic; `src/shared/` is only for cross-block invariants/utilities such as motion, a11y, DOM/config parsing, and shared types.
- Version planning decisions are recorded in `.context/planning/000_VERSION_1_1_0_PLANNING.md`: V1 uses Gutenberg patterns + theme CSS for hero, feature strips, stat/icon cards, about/factory, and map/contact; custom blocks are reserved for interaction-heavy logic or future editor governance.

## [manual] Invariants

- Do not edit GeneratePress parent theme.
- Do not put custom logic blocks in the theme.
- Do not overwrite existing image ALT text.
- Do not auto-generate image captions in V1.
- Do not add dependencies without rationale.
- AI must record tensions instead of silently breaking rules.

## [manual] Open Decisions

- Exact sidebar/admin control flow for page display options.
- Future n8n integration approach after 1.0.0.
- Polylang activation timing.
- SEO/GEO content template details.
