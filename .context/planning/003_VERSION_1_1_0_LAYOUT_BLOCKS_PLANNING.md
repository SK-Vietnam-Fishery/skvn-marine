# Version 1.1.0 Planning — Layout Blocks

> Planning candidate for repeated layout sections that are difficult to translate into safe Gutenberg-native markup.
> Load this file when planning card grids, reusable cards, quote/testimonial sections, pricing cards, process steps, or HTML-2-Gutenberg layout gaps.

---

## Status

Status: **PENDING**
Primary target: **V1 / 1.1.0**

This file is the planning source for the `1.1.0 — Layout Blocks` future checkpoint in `.context/MILESTONES.md`.

This is not current 0.8.0 scope and not V1 launch-required work unless the human explicitly changes milestone priority.

Source proposal:

- `.context/proposals/proposal-layout-blocks.md`

Carry-in admin UX note:

- Footer background preset dropdown should remain a native select for browser/OS stability and accessibility.
- Do not rely on styling individual native `<option>` rows for dark/light contrast because browser support is inconsistent.
- Add a stable swatch/preview treatment near the setting instead: dark presets preview with light text, light presets preview with dark text.
- Keep the setting preset-based; do not expose raw color input.

---

## Problem

Some repeated artifact sections are too fragile to translate into Gutenberg core blocks plus custom class names:

- Portfolio or image grids need responsive grid controls and hover overlays.
- Pricing cards need one highlighted card inside a repeated group.
- Founder quote and badge sections need decorative absolute-positioned elements.
- Process cards need a large decorative step number layered behind editable content.

These patterns appear repeatedly across layout artifacts, so they may justify editor-governed blocks after the launch-ready V1 baseline is stable.

---

## Boundary

Plugin `skvn-marine-blocks` owns:

- Custom Gutenberg block registration.
- Block attributes, editor UI, saved markup, and any frontend block runtime.
- HTML-2-Gutenberg tooling that recommends or emits these blocks in future.

Theme `skvn-marine` owns:

- The actual visual contract for `skvn-*` classes.
- CSS tokens, component styles, editor/frontend parity, and decorative presentation.
- Shared animation runtime.

Rules:

- Use namespace `skvn-marine`.
- Use TypeScript and `block.json`.
- Use InnerBlocks so content remains editable.
- Do not put block logic in the theme.
- Do not create per-block animation runtimes.
- If hover or motion is added, it must respect `prefers-reduced-motion`.
- Do not edit GeneratePress parent files.

---

## Candidate Blocks

### `skvn-marine/card-grid`

Goal:

- Provide a governed grid wrapper for portfolio grids, testimonial grids, process cards, and repeated visual cards.

Attributes:

- `columns`: desktop columns, range 2-5, default 3.
- `mobileColumns`: mobile columns, range 1-2, default 1.
- `gap`: `sm`, `md`, `lg`, default `md`.
- `cardStyle`: `default`, `elevated`, `bordered`, `featured`, default `default`.
- `inset`: `none`, `sm`, `md`, `lg`, default `md`; controls the horizontal space between outer cards and the grid edge.
- `contentAlign`: `left`, `center`, `right`, `justify`, default `left`; controls heading/copy/CTA alignment through preset classes.
- `equalHeights`: boolean, default `false`; when enabled, cards stretch to the same row height.

InnerBlocks:

- Prefer `skvn-marine/card` after that block exists.
- Allow `core/group` as a fallback for migration and manual editing.

Editor:

- Render the real CSS grid.
- Avoid hover-only previews.
- Controls live in the sidebar, not raw class input.

Frontend:

- CSS Grid only.
- No JavaScript required.

Benchmark-derived class contract:

- Current theme CSS supports product-card benchmark classes documented in `docs/decisions/product-card-grid-layout-contract.md`.
- Future `card-grid` / `card` block controls should map preset attributes to stable `skvn-*` classes instead of requiring raw class input.
- Dynamic WooCommerce product grid/list block controls remain deferred unless human explicitly changes product-block scope.

### `skvn-marine/card`

Goal:

- Provide a reusable governed card for testimonials, process steps, pricing cards, and simple content cards.

Attributes:

- `variant`: `default`, `featured`, `process-step`, `pricing`, default `default`.
- `stepNumber`: string, used only for `process-step`.
- `featured`: boolean, used for highlighted pricing or emphasis state.
- `imageTreatment`: `inset`, `full-bleed`, default `full-bleed` for product/card benchmark parity.

InnerBlocks:

- Heading, paragraph, buttons, image, and other safe core blocks depending on variant.

Editor:

- Featured and process variants should visibly differ in the editor.
- Decorative number must not block text selection or editing.

Frontend:

- CSS-only layout.
- Optional hover style may use theme CSS; avoid JS unless a real interaction is introduced.

### `skvn-marine/quote`

Goal:

- Provide an editable quote/testimonial section with author metadata, optional avatar, and theme-owned decorative treatment.

Attributes:

- `authorName`: string.
- `authorRole`: string.
- `authorImageId`: media ID.
- `showDecorativeIcon`: boolean, default true.

InnerBlocks:

- Quote body as editable text, preferably `core/paragraph` or `core/quote` depending on implementation review.

Editor:

- Show author and avatar preview.
- Hide or mute decorative elements that could cover editable content.

Frontend:

- Decorative icon should be theme CSS or a stable non-editable element controlled by the block attribute.

### `skvn-marine/hashtag-list`

Goal:

- Provide a governed horizontal hashtag/badge list for compact B2B facts such as MOQ, cold-chain requirements, private label availability, certification cues, or buyer-facing proof points.
- Replace fragile core List + raw class editing when this pattern repeats across quote, product, hero, and landing surfaces.

Attributes:

- `items`: editable list items.
- `stylePreset`: `pill`, `plain`, `compact`, `outlined`, default `pill`.
- `tone`: `light-on-dark`, `dark-on-light`, `accent`, default `light-on-dark`.
- `prefix`: `hash`, `none`, `check`, default `hash`.
- `gap`: `sm`, `md`, `lg`, default `md`.
- `wrap`: boolean, default true.

Style controls:

- Expose preset controls in the block sidebar.
- Do not expose raw class input, raw CSS, or arbitrary colors.
- Theme owns the actual CSS for each style/tone preset.
- Saved markup should remain stable and simple, for example `ul.skvn-hashtag-list`.

InnerBlocks / editing model:

- Prefer a list-item editing model that lets editors add/remove/reorder items without editing HTML.
- Do not require editors to manually add `#`; the prefix control owns it.

Frontend:

- CSS-only.
- No frontend JavaScript required.

---

## Recommended Direction

Start with `card-grid` and `card`.

Reason:

- They cover three of the four proposal pain points: image/card grids, pricing highlight state, and process cards.
- They reduce raw class reliance for editors.
- They create a useful target for future HTML-2-Gutenberg recommendations without forcing admin publisher work.

Defer `quote` and `hashtag-list` until after `card-grid` and `card` are validated, unless repeated quote/product-page artifacts prove the hashtag list is becoming editor-fragile earlier.

Reason:

- Quote/testimonial content can often be represented with core blocks plus theme pattern CSS.
- The custom block becomes justified only if author/avatar/decorative controls need repeated editor governance.
- Hashtag list can currently be represented with core List + `skvn-hashtag-list`; the custom block becomes justified when style presets and add/remove/reorder controls matter.

---

## Decision Gates

Human confirmation needed before implementation:

- Strategy: custom blocks vs theme CSS patterns with class names.
- Card highlight model: `featured` attribute on `skvn-marine/card` vs separate `skvn-marine/featured-card`.
- Quote decorative model: theme CSS pseudo-element vs saved inline SVG/element.

Default recommendation:

- Use custom blocks only for repeated sections where class-based core block editing is fragile.
- Use a single `skvn-marine/card` block with variant and `featured` attributes.
- Use theme-owned decorative CSS for quote visuals.

---

## Implementation Phases

### Phase 0 — Validate Need

Acceptance:

- [ ] Footer background preset admin preview is evaluated as a small 1.1.0 UX hardening task before or alongside layout-block implementation.
- [ ] At least two real layout artifacts require the same grid/card governance.
- [ ] A core-block plus theme-pattern attempt is documented as too fragile or too slow for editors.
- [ ] Missing theme CSS classes are listed before block implementation.

### Phase 1 — Card Grid + Card

Acceptance:

- [ ] `skvn-marine/card-grid` block exists in plugin.
- [ ] `skvn-marine/card` block exists in plugin.
- [ ] Both blocks use `block.json` and TypeScript.
- [ ] InnerBlocks keep heading, copy, buttons, and images editable.
- [ ] Sidebar controls include preset-based grid inset, content alignment, image treatment, and equal-height options.
- [ ] Editor preview matches frontend closely enough for layout decisions.
- [ ] No JavaScript frontend runtime is added unless a real interaction requires it.
- [ ] Theme CSS implements all layout-critical `skvn-*` classes used by saved markup.
- [ ] Build passes for `skvn-marine-blocks`.

### Phase 2 — Quote Block Evaluation

Acceptance:

- [ ] A theme pattern alternative is tested first.
- [ ] Custom block is approved only if author/avatar/decorative controls need repeatable governance.
- [ ] Decorative elements do not obscure editor content.
- [ ] Avatar output is escaped and rendered through WordPress media-safe APIs if server-rendered later.

### Phase 2.5 — Hashtag List Block Evaluation

Acceptance:

- [ ] At least two real pages use `skvn-hashtag-list` or equivalent compact fact chips.
- [ ] A core List + theme class approach is documented as too fragile for editors or insufficient for style variation.
- [ ] Style controls are preset-based: `stylePreset`, `tone`, `prefix`, `gap`, and `wrap`.
- [ ] No raw class, raw CSS, arbitrary color, or manual `#` prefix is required from editors.
- [ ] Saved markup remains stable and readable.
- [ ] Theme CSS implements every style/tone preset before the block exposes it.

### Phase 3 — HTML-2-Gutenberg Integration

Acceptance:

- [ ] Translator can recommend `card-grid` and `card` when artifact structure matches.
- [ ] Translator can recommend `hashtag-list` when artifact structure matches repeated short facts/badges.
- [ ] Translation report records why a custom block is needed instead of core blocks.
- [ ] Output still reports `missing_theme_classes` and `token_changes_needed`.
- [ ] Admin publisher flow remains out of scope unless separately approved.

---

## Out Of Scope

- Product Grid / Product List blocks.
- Payment or ecommerce pricing integration.
- Admin publisher/create-page flow.
- AI API or screenshot parser integration.
- New animation choreography per block.
- Raw inline `<style>`, `<script>`, or artifact Tailwind classes in saved production content.

---

## Risks

- Custom blocks can increase maintenance if created before actual editor pain is proven.
- Layout blocks may duplicate what theme patterns can solve cheaply.
- Too many variants can make the inserter and sidebar confusing.
- Saved markup must stay stable enough for future theme CSS changes.

Mitigation:

- Validate with real artifacts first.
- Implement the smallest useful block pair first.
- Keep visual styling theme-owned.
- Keep block attributes limited to editor-safe layout decisions.
