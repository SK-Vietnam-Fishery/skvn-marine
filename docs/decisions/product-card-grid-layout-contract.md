# Product Card Grid Layout Contract

Status: working contract.
Current use: theme CSS classes for benchmark/manual Gutenberg output.
Future enhancement target: V1 / 1.1.0 layout block controls.

## Current CSS Contract

Use `skvn-product-card-grid` on the grid wrapper and `skvn-product-card` on each card.

```html
<div class="wp-block-group skvn-product-card-grid skvn-product-card-grid--inset-md skvn-product-card-grid--content-left">
	<div class="wp-block-group skvn-product-card">...</div>
	<div class="wp-block-group skvn-product-card">...</div>
	<div class="wp-block-group skvn-product-card">...</div>
</div>
```

Grid inset presets:

```text
skvn-product-card-grid--inset-none
skvn-product-card-grid--inset-sm
skvn-product-card-grid--inset-md
skvn-product-card-grid--inset-lg
```

Default grid gutter comes from the same inner padding model used by section heading and lead text. This keeps the outer card edge aligned with the section text on desktop and prevents edge-sticking on mobile. Use inset presets only when cards need extra internal distance from the grid edge.

Content alignment presets:

```text
skvn-product-card-grid--content-left
skvn-product-card-grid--content-center
skvn-product-card-grid--content-right
skvn-product-card-grid--content-justify
```

`content-justify` keeps headings and CTA left-aligned while paragraph copy uses justified text. This avoids awkward stretched heading/button layouts.

Product card images use a full-bleed card media treatment by default:

```text
figure.wp-block-image spans through the card padding.
img uses 4:3 aspect ratio, object-fit: cover, and full card width.
```

## Future 1.1.0 Block Controls

When `skvn-marine/card-grid` and `skvn-marine/card` are implemented, expose equivalent preset controls instead of raw class input.

Recommended attributes:

```text
inset: none | sm | md | lg
contentAlign: left | center | right | justify
imageTreatment: inset | full-bleed
equalHeights: boolean
```

Control rules:

```text
- Controls live in the block sidebar.
- Do not expose raw class input for normal editors.
- Do not expose raw px/rem spacing values.
- Saved markup maps preset attributes to stable `skvn-*` classes.
- Frontend output stays CSS-only unless a real interaction is introduced.
```

Dynamic WooCommerce product grid/list controls remain deferred unless human explicitly changes the product-block milestone scope.

## 1.1.0 Layout Block Validation

Two existing layout artifacts justify the same governed grid/card model:

- `docs/artifacts/benchmark-templates/003-online-page-candidate.gutenberg.html` uses repeated product/category cards where grid inset, content alignment, and full-bleed image treatment need to stay consistent without raw class editing.
- `docs/artifacts/page-blueprints/request-quote-gutenberg.md` uses repeated route/support cards; the render notes already flag the core Columns plus base `.skvn-card` approach as visually underweighted and too easy to drift through manual class handling.

The core block plus theme-pattern alternative remains acceptable for one-off sections, but it is too fragile for repeated card artifacts because editors must remember a coupled wrapper/card class contract, column-count modifiers, CTA alignment classes, and image treatment rules. The `skvn-marine/card-grid` and `skvn-marine/card` blocks therefore map those choices to preset attributes and stable `skvn-*` classes while keeping content editable through InnerBlocks.
