# SKVN Editor Controls 0.8.0

Status:

```text
DRAFT_READY_FOR_HUMAN_REVIEW
```

Purpose:

```text
Define the 0.8.0 editor-control contract before any code changes.
The goal is to let marketing editors adjust SKVN-owned Gutenberg surfaces through safe presets, not raw classes or arbitrary CSS.
```

## Scope

In scope:

```text
- Token-governed sidebar controls for SKVN-owned blocks and translated layout surfaces.
- Controls grouped as Content, Style, Layout, and Advanced.
- Editor controls that map to theme-owned classes, theme.json presets, or fixed block attributes.
- Onsite-first validation through the block editor and frontend preview.
```

Out of scope:

```text
- Raw class input as the primary workflow.
- Raw hex/rgb/hsl color fields.
- Arbitrary inline margin/padding values.
- GeneratePress parent theme edits.
- Footer Page Settings 0.9.0.
- Quote flow runtime checks 0.7.1 / 1.1.2.
- New external dependencies.
```

## Ownership

Theme owns:

```text
- Visual tokens.
- Tone classes.
- Spacing presets.
- Width presets.
- Radius and shadow presets.
- Frontend CSS output.
- Editor/frontend visual parity styles.
```

Plugin owns:

```text
- Block sidebar UI.
- Block attributes.
- Saved markup.
- Interactive block behavior.
- Any reusable editor-control helper used by two or more SKVN-owned blocks.
```

## Control Groups

### Content

Use for values that change text/content meaning.

Allowed examples:

```text
- Eyebrow text.
- Heading text.
- Lead/body text.
- Button label.
- Button URL.
- Image alt text where the block owns an image field.
- Accordion item title/body.
```

Rules:

```text
- Content controls must not require raw class input.
- Links must be stored as normal block attributes and rendered through WordPress-safe output.
- Image alt rules must not overwrite manual Media Library ALT values.
```

### Style

Use for visual tone and component variants.

Allowed examples:

```text
- Tone: default, light, fresh, trust, navy.
- Button style: primary, secondary, text.
- Card style: plain, soft, bordered, elevated.
- Media treatment: plain, rounded, framed.
```

Rules:

```text
- Style controls map to approved `skvn-*` classes or theme.json presets.
- No raw color picker unless it is constrained to theme palette presets.
- No custom CSS text area.
```

### Layout

Use for section shape and responsive layout.

Allowed examples:

```text
- Content width: normal, wide, full.
- Section spacing: none, sm, md, lg, xl.
- Grid density: compact, balanced, spacious.
- Alignment: start, center, end where supported by the block.
- Responsive visibility: show all, hide on mobile, hide on desktop.
```

Rules:

```text
- Spacing controls use presets.
- Responsive controls must not make essential content inaccessible.
- Fixed theme layouts can show a read-only note instead of exposing unsupported controls.
```

### Advanced

Use only for controlled technical settings.

Allowed examples:

```text
- HTML anchor.
- ARIA label where needed.
- Tracking name from a safe text input.
- Experimental flag only if hidden from normal marketing flow.
```

Rules:

```text
- No raw class input.
- No raw inline style input.
- No JavaScript snippet input.
```

## Initial 0.8.0 Implementation Order

Recommended order:

```text
1. Document the control contract.
2. Add onsite test checklist.
3. Implement controls on the lowest-risk SKVN-owned surface first.
4. Verify editor save/reload stability onsite.
5. Verify frontend output matches editor preview.
6. Only then expand controls to more blocks/surfaces.
```

Preferred first target:

```text
SKVN Accordion, followed by translated layout / SKVN section surfaces that already map to theme-owned `skvn-*` classes.
```

Reason:

```text
Accordion is an existing SKVN-owned block with low runtime risk, and translated layout surfaces are visual and token-driven. This keeps 0.8.0 moving without touching slider runtime behavior.
```

## Slider Boundary

Slider-specific controls are blocked until the OPEN slider editor UX tension is resolved.

Current safe rule:

```text
- Keep slider editor preview stacked/simplified.
- Do not run Swiper autoplay in the editor.
- Do not add slider-specific UX controls until human confirms stacked, selected-slide-preview, or lightweight carousel preview.
```

## Acceptance Contract

0.8.0 can pass only when:

```text
[ ] Editor controls contract is documented before code.
[ ] Onsite test checklist exists before code.
[ ] Controls are grouped into Content, Style, Layout, and Advanced.
[ ] Controls map to theme-owned tokens/classes/presets.
[ ] No raw class input is required.
[ ] No raw color values are required.
[ ] No arbitrary inline spacing values are required.
[ ] Editor save/reload keeps blocks valid.
[ ] Frontend output matches editor intent.
[ ] GeneratePress parent remains untouched.
[ ] Human approves completion.
```

## Risks To Watch

```text
- Invalid block warnings after attribute or markup changes.
- Editor preview diverges from frontend CSS.
- Marketing users are forced back to raw class input.
- Style controls create one-off classes that are not theme-owned.
- Responsive visibility hides important content.
- Slider controls are added before the editor UX tension is resolved.
```
