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

- Navy: `#082f49`
- Primary blue: `#0369a1`
- Support mint: `#ddfaf4`
- Premium gold: `#e9c766`

## [manual] Layout Direction

- 0.5.0 introduces a reusable page layout named `SKVN Full Width`.
- The layout is not landing-page specific. It is the general canvas for homepage, product overview, campaign, and UI test pages that need full-width Gutenberg sections.
- Keep GeneratePress header/footer intact.
- Remove or bypass the narrow default content wrapper only for pages explicitly using this template/layout.
- `alignfull` sections should reach viewport width.
- Inner content must stay constrained by SKVN design widths.
- Do not fix this by editing GeneratePress parent files.
- Do not create a custom block for this layout in V1.

## [manual] Forbidden

- Do not edit parent GeneratePress.
- Do not register complex custom Gutenberg blocks here.
- Do not handle quote form submissions manually in V1.
