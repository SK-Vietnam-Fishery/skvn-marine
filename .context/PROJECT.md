# PROJECT — SKVN Marine

## [manual] Decisions

- V1 is a single B2B marine/fishery website.
- V3 may become a reusable base theme.
- Use GeneratePress child theme, not a fork, for V1.
- Use WooCommerce native product model.
- Use CF7 + CFDB7 + n8n for quote lead flow.
- Use Rank Math for SEO and GEO/AEO-friendly content structure.
- Use Polylang as multilingual candidate.
- Use WindPress/Tailwind for design system implementation.
- Use TypeScript for custom Gutenberg block plugin.
- Use Swiper for slider.
- Product Grid/List V1: use WooCommerce native blocks/patterns. Custom Product Grid/List or related style blocks are deferred to V2.

## [manual] Invariants

- Do not edit GeneratePress parent theme.
- Do not put custom logic blocks in the theme.
- Do not overwrite existing image ALT text.
- Do not auto-generate image captions in V1.
- Do not add dependencies without rationale.
- AI must record tensions instead of silently breaking rules.

## [manual] Open Decisions

- Exact CF7 webhook approach for n8n.
- Polylang activation timing.
- SEO/GEO content template details.
