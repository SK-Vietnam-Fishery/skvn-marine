# Brand Profile & Theme Tokens

Status: working decision for V1 / 0.7.0.

Purpose:

```text
Define the theme-owned brand profile contract before changing runtime tokens.
This lets HTML-2-Gutenberg, Gutenberg patterns, editor styles, and frontend CSS map visual intent to SKVN tokens instead of preserving prototype colors.
```

## Ownership

Theme `skvn-marine` owns the primary brand profile:

```text
wp-content/themes/skvn-marine/theme.json
wp-content/themes/skvn-marine/style.css
wp-content/themes/skvn-marine/inc/block-styles.php
wp-content/themes/skvn-marine/patterns/*.php
wp-content/themes/skvn-marine/inc/setup.php
```

Plugin `skvn-marine-blocks` owns tooling and reports:

```text
HTML-2-Gutenberg artifact intake
brand_source_scan
brand_mapping
brand_mismatch
token_changes_needed
validation that output does not rely on raw prototype colors
```

Gutenberg content owns editable page content:

```text
copy
links
button labels
media
normal block composition
```

Gutenberg content must not own:

```text
raw <style>
raw <script>
prototype Tailwind color classes as production contract
raw hex/rgb/hsl brand declarations
unrestricted inline spacing or shadow values
```

## Brand Profile Token Names

Canonical 0.7 profile names:

| Profile role | CSS variable | Current mapped value | Theme.json preset |
|---|---|---|---|
| Primary | `--skvn-color-primary` | `var(--skvn-color-blue-700)` | `skvn-blue-700` |
| Accent/support | `--skvn-color-accent` | `var(--skvn-color-mint-100)` | `skvn-mint-100` |
| Dark/navy | `--skvn-color-dark` | `var(--skvn-color-blue-950)` | `skvn-blue-950` |
| Surface | `--skvn-color-surface` | `var(--skvn-color-white)` | `skvn-white` |
| Soft surface | `--skvn-color-surface-soft` | `var(--skvn-color-sky-50)` | `skvn-sky-50` |
| Text | `--skvn-color-text` | `var(--skvn-color-slate-700)` | `skvn-slate-700` |
| Muted text | `--skvn-color-muted` | `rgba(15, 23, 42, 0.72)` | CSS only |
| Border | `--skvn-color-border` | `var(--skvn-border)` | CSS only |
| CTA background | `--skvn-color-cta` | `var(--skvn-color-blue-700)` | `skvn-blue-700` |
| CTA hover | `--skvn-color-cta-hover` | `var(--skvn-color-blue-900)` | `skvn-blue-900` |
| Premium cue | `--skvn-color-premium` | `var(--skvn-color-gold-300)` | `skvn-gold-300` |
| Body font | `--skvn-font-body` | system font stack | `skvn-system` |
| Heading font | `--skvn-font-heading` | `var(--skvn-font-body)` | `skvn-heading` |
| UI font | `--skvn-font-ui` | `var(--skvn-font-body)` | `skvn-ui` |
| Card radius | `--skvn-radius-card` | `8px` | CSS only |
| Card shadow | `--skvn-shadow-card` | existing card shadow | CSS only |
| Section spacing | `--skvn-section-spacing` | `4rem` | future spacing preset candidate |
| Content width | `--skvn-content-width` | `760px` | `settings.layout.contentSize` |
| Wide width | `--skvn-wide-width` | `1200px` | `settings.layout.wideSize` |

The existing descriptive tokens such as `--skvn-color-blue-700` remain valid. The canonical profile tokens act as semantic aliases for mapping artifact intent.

## Sync Rule

When a token exists in both `theme.json` and `style.css`, update both in the same task.

Required sync pairs:

| `theme.json` | `style.css` |
|---|---|
| `settings.color.palette[].slug = skvn-blue-950` | `--skvn-color-blue-950` |
| `settings.color.palette[].slug = skvn-blue-900` | `--skvn-color-blue-900` |
| `settings.color.palette[].slug = skvn-blue-700` | `--skvn-color-blue-700` |
| `settings.color.palette[].slug = skvn-mint-100` | `--skvn-color-mint-100` |
| `settings.color.palette[].slug = skvn-gold-300` | `--skvn-color-gold-300` |
| `settings.color.palette[].slug = skvn-teal-600` | `--skvn-color-teal-600` |
| `settings.color.palette[].slug = skvn-sky-50` | `--skvn-color-sky-50` |
| `settings.color.palette[].slug = skvn-slate-700` | `--skvn-color-slate-700` |
| `settings.color.palette[].slug = skvn-white` | `--skvn-color-white` |
| `settings.typography.fontFamilies[].slug = skvn-system` | `--skvn-font-body` |
| `settings.typography.fontFamilies[].slug = skvn-heading` | `--skvn-font-heading` |
| `settings.typography.fontFamilies[].slug = skvn-ui` | `--skvn-font-ui` |
| `settings.layout.contentSize` | `--skvn-content-width` |
| `settings.layout.wideSize` | `--skvn-wide-width` |

CSS-only tokens such as shadows, borders, semantic aliases, and component-specific states may live only in `style.css` until WordPress has a useful native preset surface for them.

## Editor / Frontend Contract

Editor and frontend must consume the same visual contract:

```text
theme.json exposes palette and layout presets to Gutenberg.
style.css defines semantic aliases and component/pattern CSS.
inc/setup.php loads style.css in the editor via add_editor_style().
patterns/*.php use Gutenberg blocks plus skvn-* classes, not raw style payloads.
inc/block-styles.php registers editor-visible style choices when a choice should be selectable.
```

If a frontend-only CSS class affects layout, spacing, color, or visibility, the editor must remain readable and close enough for safe editing. Editor content must not be hidden by `opacity: 0`.

## HTML-2-Gutenberg Brand Mapping

Artifact intake must report:

```text
brand_source_scan
brand_mapping
brand_mismatch
token_changes_needed
```

Mapping rules:

```text
Prototype blue/primary CTA intent -> --skvn-color-primary / skvn-button--primary.
Freshness, hygiene, cold-chain, factory support surfaces -> --skvn-color-accent or --skvn-color-surface-soft.
Dark trust/nav/footer surfaces -> --skvn-color-dark.
Certification or premium product cue -> --skvn-color-premium, used sparingly.
Card radius/shadow cues -> --skvn-radius-card and --skvn-shadow-card.
Large section spacing cues -> --skvn-section-spacing or existing skvn-section spacing.
```

If an artifact requires a brand cue not represented above, record it in `token_changes_needed`. Do not preserve raw prototype colors in Gutenberg markup.

## External References

Implementation references for manual development:

- WordPress Block Editor Handbook — Global Settings & Styles: `https://developer.wordpress.org/block-editor/how-to-guides/themes/global-settings-and-styles/`
- WordPress Block Editor Handbook — Styles in the Editor: `https://developer.wordpress.org/block-editor/explanations/architecture/styles/`
- WordPress Block Editor Handbook — Block Supports: `https://developer.wordpress.org/block-editor/reference-guides/block-api/block-supports/`
- WordPress Theme Handbook — Styles: `https://developer.wordpress.org/themes/global-settings-and-styles/styles/`
- WordPress Block Editor Handbook — Block Styles: `https://developer.wordpress.org/block-editor/reference-guides/block-api/block-styles/`
- WordPress Settings API: `https://developer.wordpress.org/apis/settings/`
- WordPress Customize API: `https://developer.wordpress.org/themes/customize-api/`
- GeneratePress Documentation — Using a Child Theme: `https://docs.generatepress.com/article/using-child-theme/`
- Tailwind CSS — Customizing Colors: `https://tailwindcss.com/docs/customizing-colors/`
- Tailwind CSS — Theme Variables: `https://tailwindcss.com/docs/configuration`

## Implementation Gate

Before changing runtime tokens in 0.7:

```text
[ ] Confirm 0.6 quote UI test result.
[ ] Update theme.json and style.css together for synced tokens.
[ ] Keep GeneratePress parent untouched.
[ ] Keep plugin as tooling/report owner only.
[ ] Run editor and frontend visual smoke after token changes.
```
