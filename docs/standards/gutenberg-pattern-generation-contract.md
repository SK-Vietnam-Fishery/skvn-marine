# Gutenberg Pattern Generation Contract

> Mandatory guardrail for agents creating SKVN Marine block patterns.

## Core Rule

Pattern markup must pass Gutenberg block validation in the editor.

If the editor shows **Attempt recovery** or **Block contains unexpected or invalid content**, the pattern source is wrong. Do not patch individual class names until the generation method is corrected.

## Generation Methods

### Method A — Theme PHP pattern (default)

Use for:

- `core/*` blocks only
- SKVN plugin blocks with **static** `save()` output and **no** PHP `render_callback`

Reference files:

```text
wp-content/themes/skvn-marine/patterns/homepage-test.php
wp-content/themes/skvn-marine/patterns/card-grid-test.php
wp-content/themes/skvn-marine/patterns/request-quote-page.php
```

Rules:

1. Mirror an existing working pattern before inventing structure.
2. Every wrapper must have a matching `<!-- wp:* -->` block comment.
3. Hand-authored HTML must match the block `save()` output byte-for-byte.
4. Do not put PHP inside block-comment JSON attributes.
5. Do not generate `data-skvn-*` JSON with `wp_json_encode()` in theme PHP.
6. Use `esc_html__()`, `esc_attr__()`, and `esc_url()` only in visible content, not inside block JSON comments.

### Method B — Plugin `serialize()` pattern (required for dynamic blocks)

Use for:

- `skvn-marine/slider`
- `skvn-marine/slide`
- `skvn-marine/post-collection`
- `skvn-marine/product-collection`

Reason:

Since V1 / 1.3.0 these blocks use PHP `render_callback` and `skip_inner_blocks`. Frontend HTML is rendered by PHP in `modules/slider-render/` and `modules/collection-render/`. Editor validation still uses the JavaScript `save()` contract.

Hand-written theme PHP cannot reliably reproduce that saved shape. Use:

```typescript
import { createBlock, registerBlockPattern, serialize } from '@wordpress/blocks';

registerBlockPattern( 'skvn-marine/example', {
  content: serialize(
    createBlock( 'skvn-marine/slider', { preset: 'hero' }, [
      createBlock( 'skvn-marine/slide', {}, [
        createBlock( 'core/heading', { content: 'Heading' } ),
      ] ),
    ] )
  ),
} );
```

Register these patterns from `skvn-marine-blocks/src/patterns/`, not from theme `patterns/*.php`.

Reference:

```text
wp-content/plugins/skvn-marine-blocks/src/patterns/page-under-construction.ts
```

### Method C — Editor export (fallback)

When Method B is not available:

1. Insert the block/variation manually in the block editor.
2. Switch to Code editor.
3. Copy the serialized markup.
4. Paste into theme PHP or plugin pattern registration.
5. Replace only editable copy, links, and image URLs.

## Block Ownership Matrix

| Block | Pattern location | Generation method |
|---|---|---|
| `core/group`, `core/heading`, `core/columns`, `core/image`, `core/button` | Theme PHP | Method A |
| `skvn-marine/card-grid`, `skvn-marine/card` | Theme PHP | Method A only with reference markup |
| `skvn-marine/feature-showcase` | Theme PHP | Method A only if copied from verified `save()` output |
| `skvn-marine/slider`, `skvn-marine/slide` | Plugin patterns | Method B |
| `skvn-marine/post-collection`, `skvn-marine/product-collection` | Plugin patterns | Method B |

## Forbidden Shortcuts

Do not:

- Hand-author slider shell HTML in theme PHP.
- Copy frontend PHP render output into pattern source.
- Assume hero slider variation markup can be guessed from `variations.ts` templates alone.
- Use self-closing tags or attribute JSON that were not produced by `serialize()`.
- Register the same pattern slug in both theme PHP and plugin JS.

## Validation Checklist

Before submitting a new pattern:

```text
[ ] Generation method matches block ownership matrix.
[ ] Pattern inserts in editor without Attempt recovery.
[ ] Slider blocks render stacked in editor, not as broken placeholders.
[ ] Frontend page loads without invalid-block notices.
[ ] Only editable copy/links/images were changed after serialization.
[ ] PHP syntax ok for theme patterns.
[ ] Plugin build completed after plugin pattern changes.
```

## Quick Debug

If a pattern fails validation:

1. Check whether the block has `render_callback` in `skvn-marine-blocks.php`.
2. If yes, regenerate with Method B or Method C.
3. Compare against `card-grid-test.php` only for static-save blocks.
4. Read `docs/decisions/slider-completion-spec-1.3.0.md` for slider render vs save ownership.