# Layout Translator Workflow

Purpose: translate AI-made HTML/CSS artifacts or screenshots into SKVN Marine Gutenberg patterns without putting raw CSS or JavaScript into page content.

CLI:

```bash
node tools/layout-translator/translate-layout.mjs --input docs/artifacts/example.html
```

## Scope

Use this workflow when a human provides:

- HTML/CSS artifact.
- Static screenshot.
- Visual section reference.
- AI-generated layout proposal.

Output should be a WordPress-native implementation plan first, then Gutenberg block markup or a theme pattern only after approval.

## Architecture Rules

- Source repo is `D:\Github\skvn-marine`, not the WordPress runtime root.
- GeneratePress parent is not edited.
- Theme `skvn-marine` owns visual system, theme CSS, design tokens, block styles, patterns, background/decorative animation, and shared animation runtime.
- Plugin `skvn-marine-blocks` owns custom Gutenberg blocks with logic or complex interaction state.
- Core Gutenberg blocks own editable text, headings, paragraphs, buttons, images, groups, and columns.
- Do not paste raw `<style>` or `<script>` into page content.
- Do not turn primary text or CTA content into images/canvas.
- Do not create a custom block when core blocks plus a pattern are enough.
- Do not add dependencies without rationale.

## Input Review

For each artifact, identify:

- Editable content: headings, paragraphs, buttons, images, product/category content.
- Theme-controlled presentation: spacing, colors, grids, cards, backgrounds, decorative layers.
- Motion: reveal, hover, loop, parallax, background animation.
- Assets: images, icons, SVGs, video, map, product media.
- Interactions: controls, state, carousel, accordion, filters, maps, forms.
- Risks: mobile behavior, accessibility, editor preview, performance, content ownership.

## Mapping Rules

| Artifact element | Gutenberg/SKVN target |
|---|---|
| `section` / wrapper `div` | `core/group` |
| Grid / two-column layout | `core/columns` |
| `h1`, `h2`, `h3` | `core/heading` |
| `p` | `core/paragraph` |
| CTA link/button | `core/buttons` + `core/button` |
| Content image | `core/image` |
| Repeated visual cards | Group/card pattern |
| Decorative layer, wave, particle, background | Theme CSS/background layer |
| Complex interaction with controls/state | Plugin block later, not forced into core blocks |

## Translation Output

Every translator result should use this structure:

```text
gutenberg_markup
required_classes
theme_css_contract
animation_contract
assets_needed
not_translated
risks
```

### `gutenberg_markup`

- Use full WordPress block comment syntax.
- Use stable `skvn-*` classes.
- Use core blocks where possible.
- Do not include raw CSS or JavaScript.
- Keep content editable in the block editor.
- Do not emit empty or self-closing image blocks. Paste-ready image blocks must include opening comment, `<figure>`, `<img src="">`, and closing comment.
- Replace missing, inline `data:` URI, or oversized image sources with a paste-safe placeholder URL and list the original image need in `assets_needed`.

### `required_classes`

List the `skvn-*` classes the theme must implement, grouped by section if useful.

Example:

```text
skvn-layout-translated-section
skvn-hero-panel
skvn-hero-panel__media
skvn-card-grid
skvn-card
skvn-motion-reveal
```

### `theme_css_contract`

Describe the CSS responsibility without writing full CSS unless requested:

- Layout model.
- Inner width and full-width behavior.
- Spacing rhythm.
- Color token intent.
- Card/border/shadow rules.
- Responsive stacking.
- Editor preview expectations.
- Asset behavior.

### `animation_contract`

For each motion element, define:

- Element.
- Trigger: load, scroll, hover, loop.
- Initial state.
- Final state.
- Duration.
- Easing.
- Stagger or delay.
- Reduced-motion fallback.
- Editor behavior: static or simplified.

All frontend motion must go through the shared animation runtime and respect `prefers-reduced-motion`.

Editor content must not be hidden with `opacity: 0` unless a safe fallback is present.

### `assets_needed`

List required assets:

- Images.
- SVG/icons.
- Map block or map provider.
- Video.
- Product/category media.
- Placeholder assets.

State whether each asset is editable via Gutenberg or controlled by theme CSS.

### `not_translated`

List artifact parts that should not go into Gutenberg content:

- Raw CSS resets.
- Inline `<style>`.
- Inline `<script>`.
- Decorative particles or waves.
- Hardcoded map screenshots.
- Static copies of WooCommerce product data.
- Prototype-only utility classes that are not part of SKVN/WindPress strategy.

### `risks`

Check:

- Mobile overflow.
- Text overlap.
- CTA visibility.
- Keyboard navigation.
- Reduced motion.
- Editor iframe differences.
- Performance cost of media and animations.
- Accessibility semantics.
- Whether a custom plugin block is truly required.

## Block Markup Standards

Use this pattern shape for simple sections:

```html
<!-- wp:group {"align":"full","className":"skvn-section skvn-section--example","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull skvn-section skvn-section--example">
	<!-- wp:heading {"level":2,"className":"skvn-section__heading"} -->
	<h2 class="wp-block-heading skvn-section__heading">Editable heading</h2>
	<!-- /wp:heading -->

	<!-- wp:paragraph {"className":"skvn-section__text"} -->
	<p class="skvn-section__text">Editable paragraph content.</p>
	<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
```

Use `core/columns` for editable column layout. Use theme CSS for exact responsive behavior when core controls are not enough.

## Custom Block Decision Gate

Do not create a custom block unless at least one condition is true:

- The section has interaction state that core blocks cannot represent safely.
- Editors need locked controls to prevent broken layouts.
- Data must come from WooCommerce or another structured source.
- Keyboard behavior, pagination, filtering, carousel, or accordion logic is required.

If custom block need is uncertain, record it in `not_translated` or `risks` first. Do not implement the block during translation.

## Review Checklist

```text
[ ] No raw <style> in content.
[ ] No raw <script> in content.
[ ] Uses core Gutenberg blocks first.
[ ] Uses stable skvn-* classes.
[ ] Editable text remains editable.
[ ] Images are core Image blocks unless decorative.
[ ] Image blocks are not empty/self-closing.
[ ] Inline data URI images are replaced with paste-safe placeholders.
[ ] Decorative layers are theme-controlled.
[ ] Motion is routed to shared runtime.
[ ] Reduced-motion fallback is specified.
[ ] Editor behavior is static or safe.
[ ] Mobile CTA remains visible.
[ ] No GeneratePress parent changes.
[ ] No new dependency without rationale.
```
