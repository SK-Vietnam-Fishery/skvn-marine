# Site Branding Guideline

Status: working standard.
Owner: `skvn-marine` theme for visual system; `skvn-marine-blocks` plugin for plugin-specific admin/editor assets only.

## Purpose

SKVN Marine can be customized, but customization must be managed through known files. Do not scatter brand values across Gutenberg content, inline CSS, custom HTML, or plugin logic.

This document defines where brand variables, visual assets, and editor-facing branding decisions live.

Before coding any branding change, update or verify this document first. If a new token, class family, screenshot/icon asset, editor style rule, or customization control is introduced, this document must state which file owns it.

## Brand Layers

| Layer | Owner | Editable by | Rule |
|---|---|---|---|
| Design tokens | Theme | Developer | Source of truth for colors, spacing, radius, shadow, layout widths |
| Gutenberg palette/layout presets | Theme | Developer | Must stay aligned with CSS tokens |
| Component and pattern styling | Theme | Developer | Use `skvn-*` classes |
| Page copy and page media | WordPress content | Editor | Editable Gutenberg content; no raw `<style>` or `<script>` |
| Plugin icon/banner | Plugin | Developer | Plugin identity asset only; not the site visual system |

## Identity Boundary

- `SKVN Marine` is the project/theme/plugin brand.
- `Minh Hai Fishery` is the current site/company content identity.
- Do not hardcode client/company names into theme logic unless it is a deliberate pattern or translatable string.
- Site title/logo should come from WordPress/GeneratePress site identity settings when possible.

## File Registry

| Concern | Primary file | Secondary file | Notes |
|---|---|---|---|
| CSS color tokens | `wp-content/themes/skvn-marine/style.css` `:root` | `wp-content/themes/skvn-marine/theme.json` palette | Keep names and values aligned |
| Layout widths | `wp-content/themes/skvn-marine/style.css` `--skvn-content-width`, `--skvn-wide-width` | `wp-content/themes/skvn-marine/theme.json` layout | Gutenberg editor and frontend must match |
| Typography base | `wp-content/themes/skvn-marine/style.css` body/headings | `wp-content/themes/skvn-marine/theme.json` typography | System font stack in V1 |
| Radius/shadow | `wp-content/themes/skvn-marine/style.css` `--skvn-radius-card`, `--skvn-shadow-card` | None | Use tokens before component overrides |
| Buttons | `wp-content/themes/skvn-marine/style.css` `.skvn-button*` and block button selectors | Patterns using `core/buttons` | Primary CTA remains blue |
| Sections/cards | `wp-content/themes/skvn-marine/style.css` `.skvn-section*`, `.skvn-card*`, translated classes | `wp-content/themes/skvn-marine/patterns/*.php` | Use `skvn-*` classes |
| Block styles | `wp-content/themes/skvn-marine/inc/block-styles.php` | `style.css` | Register editor-visible styles here |
| Editor parity | `wp-content/themes/skvn-marine/inc/setup.php` | `style.css`, future editor CSS if split | `add_editor_style()` must load required visual CSS |
| Theme screenshot | `wp-content/themes/skvn-marine/screenshot.png` | None | Theme marketplace/admin preview image |
| Shared motion | `wp-content/themes/skvn-marine/assets/js/animations.js` | `style.css` motion classes | Must respect `prefers-reduced-motion` |
| Page display controls UI | `wp-content/themes/skvn-marine/assets/js/page-display-controls.js` | `assets/css/page-display-controls-editor.css` | Editor/admin control surface |
| Page display meta | Theme PHP files that register/save `_skvn_*` meta | None | Keep page chrome controls in theme |
| Media ALT automation | `wp-content/themes/skvn-marine/inc/media.php` | None | Fill ALT only when empty |
| Woo visual override | `wp-content/themes/skvn-marine/inc/woocommerce.php` | `style.css` | Visual only; no product data ownership |
| WindPress integration | `wp-content/themes/skvn-marine/inc/windpress.php` | None | Tailwind is not the production brand source of truth |
| Plugin icon | `wp-content/plugins/skvn-marine-blocks/assets/icon-256x256.png` | None | Plugin identity only |
| Plugin banner | `wp-content/plugins/skvn-marine-blocks/assets/banner-772x250.png` | None | Plugin identity only |

## Customization Order

1. Update `theme.json` presets and `style.css` `:root` tokens together.
2. Update component CSS in `style.css`.
3. Update or add patterns in `wp-content/themes/skvn-marine/patterns/`.
4. Update editor parity if frontend and editor drift.
5. Update screenshots/icons only after the visual direction is approved.

## Visual Rules

- Blue-first system for V1.
- Primary CTA remains blue.
- Mint supports freshness, hygiene, cold-chain, factory/process, trust, and form support surfaces.
- Gold is limited to certification, premium species, buyer-value highlights, and top-grade labels.
- Do not use gold as a dominant background.
- Text content must stay editable Gutenberg content, not baked into images or canvas.
- Real seafood/factory/export imagery is preferred over decorative-only media when the user needs to inspect the subject.

## HTML-2-Gutenberg Impact

HTML-2-Gutenberg may accept Tailwind-ready artifacts, but Tailwind classes are input hints only. The final WordPress output must use Gutenberg block markup and `skvn-*` classes controlled by the theme.

When the importer outputs a new `skvn-*` class, record the class contract in the workflow output and implement its styling in the theme files listed above.

## Guardrails

- Update or verify this guideline before coding branding changes.
- Do not place raw `<style>` or `<script>` in Gutenberg content.
- Do not edit GeneratePress parent files.
- Do not move brand tokens into the plugin.
- Do not create dynamic Tailwind class contracts that the theme cannot audit.
- Do not rename `skvn-*`, `skvn_marine_`, `skvn_marine_blocks_`, or `skvn-marine`.
- Keep frontend and editor visual behavior close enough for safe editing.

## Review Checklist

- [ ] `style.css` tokens and `theme.json` presets match.
- [ ] New visual classes use `skvn-*`.
- [ ] Content remains editable in Gutenberg.
- [ ] Editor view does not hide content with `opacity: 0`.
- [ ] Mobile spacing and text wrapping checked.
- [ ] Contrast checked for CTA, text, and badges.
- [ ] Theme/plugin assets are updated only in their owned folders.
