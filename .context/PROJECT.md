# PROJECT — SKVN Marine

## [manual] Decisions

- V1 is a single B2B marine/fishery website.
- V3 may become a reusable base theme.
- Use GeneratePress child theme, not a fork, for V1.
- Use WooCommerce native product model.
- Page display/sidebar controls are current 0.5.1 scope; Quote UI/editor controls move to 0.6.0; CF7/CFDB7 are deferred until after 0.6.0; n8n is deferred until after 1.0.0.
- Use Rank Math for SEO and GEO/AEO-friendly content structure.
- Use Polylang as multilingual candidate.
- Use WindPress/Tailwind for design system implementation.
- Use TypeScript for custom Gutenberg block plugin.
- Use Swiper for slider.
- Product Grid/List V1: use WooCommerce native blocks/patterns. Custom Product Grid/List or related style blocks are deferred to V2.
- Plugin architecture: block-centric feature modules with minimal shared services. Blocks own feature logic; `src/shared/` is only for cross-block invariants/utilities such as motion, a11y, DOM/config parsing, and shared types.
- Version 0.1.0 planning decisions are recorded in `.context/planning/000_VERSION_0_1_0_PLANNING.md`: V1 uses Gutenberg patterns + theme CSS for hero, feature strips, stat/icon cards, about/factory, and map/contact; custom blocks are reserved for interaction-heavy logic or future editor governance.

## [manual] Invariants

- Do not edit GeneratePress parent theme.
- Do not put custom logic blocks in the theme.
- Do not overwrite existing image ALT text.
- Do not auto-generate image captions in V1.
- Do not add dependencies without rationale.
- AI must record tensions instead of silently breaking rules.

## [manual] Open Decisions

- Exact sidebar/admin control flow for page display options.
- Future CF7/n8n integration approach after 1.0.0.
- Polylang activation timing.
- SEO/GEO content template details.
