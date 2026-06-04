# Footer Appearance Settings — 0.11.0 Contract

Date: 2026-06-04
Status: Planned for V1 / 0.11.0.

## Scope

0.11.0 extends the existing Footer Page Settings admin surface with a safe footer background setting.

The setting only applies when `skvn_footer_page_id` points to a valid published footer page. It must not affect the default GeneratePress fallback footer.

## Ownership

Plugin `skvn-marine-blocks` owns:

- Admin UI under the planned `SKVN Marine` admin menu.
- Storing the selected footer background value.
- Sanitizing the value to an approved preset.

Theme `skvn-marine` owns:

- Mapping the approved preset to CSS variables/tokens.
- Applying the footer background to the rendered footer page wrapper.
- Ensuring the outermost footer block inherits the selected background.

## Option Contract

Existing option remains unchanged:

```text
skvn_footer_page_id
```

New option:

```text
skvn_footer_background_preset
```

Allowed values:

```text
default
deep-navy
trust-blue
white
fresh-sky
```

Default:

```text
default
```

Meaning:

- `default` maps to the current footer background token.
- Invalid or empty values sanitize to `default`.
- Do not store raw hex/rgb/hsl values in 0.11.0.

## Rendering Contract

When a valid footer page is active, the theme should expose a footer background preset class on the body because the body background must match the footer background when viewport space appears below the footer:

```html
<body class="skvn-has-footer-page skvn-footer-bg-deep-navy">
```

The preset class defines one shared variable:

```css
body.skvn-footer-bg-deep-navy {
	--skvn-footer-bg: var(--skvn-color-blue-950);
}
```

The theme CSS should make the rendered footer page and outermost footer block use that same variable:

```css
.skvn-footer-page,
.skvn-footer-page > .skvn-site-footer,
.skvn-footer-page > .wp-block-group.skvn-site-footer {
	background: var(--skvn-footer-bg, var(--skvn-color-blue-950));
}
```

This specifically supports Gutenberg content like:

```html
<!-- wp:group {"className":"skvn-site-footer","layout":{"type":"default"}} -->
<div class="wp-block-group skvn-site-footer">
```

The setting must affect this outermost `.skvn-site-footer` block without requiring editors to manually edit block classes or inline styles.

## Body Background Contract

If the custom footer page is active, the theme may keep using a body class such as:

```text
skvn-has-footer-page
```

The body background should also use the selected footer variable so viewport space below the footer does not show a mismatched white strip.

The main site wrapper must keep the normal page background:

```css
body.skvn-has-footer-page {
	background: var(--skvn-footer-bg, var(--skvn-color-blue-950));
}

body.skvn-has-footer-page .site {
	background: var(--skvn-color-white);
}
```

## Admin UI Contract

Under `SKVN Marine → Footer`, show:

```text
Footer page
Footer background
```

Footer background should be a preset select, not a freeform color input in 0.11.0.

Recommended labels:

```text
Default
Deep navy
Trust blue
White
Fresh sky
```

## Acceptance

- [ ] `Settings → SKVN Footer` is replaced by `SKVN Marine → Footer`.
- [ ] Existing option `skvn_footer_page_id` continues to work.
- [ ] New option `skvn_footer_background_preset` is stored and sanitized to allowed presets.
- [ ] Background preset applies only when a valid custom footer page is active.
- [ ] Background preset affects `.skvn-footer-page`.
- [ ] Background preset affects the outermost `.skvn-site-footer` Gutenberg block.
- [ ] Viewport space below the footer uses the same selected footer background.
- [ ] Default GeneratePress fallback footer is unaffected.
- [ ] No raw hex/rgb/hsl value is required from marketing editors in 0.11.0.
- [ ] Capability checks and nonce protection remain in place.
