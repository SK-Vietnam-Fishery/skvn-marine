# PLUGIN — skvn-marine-blocks

## [manual] Role

Custom Gutenberg blocks with logic, and future plugin-owned editor/admin tooling.

## [manual] Architecture

Architecture: **Block-centric feature modules with minimal shared services**.

Each block owns feature-specific logic:

- `block.json` — block metadata and attributes
- `edit.tsx` — editor UI
- `save.tsx` — saved markup
- `view.ts` — frontend runtime only when needed
- `types.ts` — block-local TypeScript types when useful

Shared services are allowed only for cross-block invariants/utilities:

- `src/shared/motion.ts` — `prefers-reduced-motion` helpers
- `src/shared/a11y.ts` — keyboard/ARIA helpers
- `src/shared/dom.ts` — safe DOM/config parsing helpers
- `src/shared/types.ts` — truly shared TypeScript types

Do not move block-specific behavior into `shared/`.

Rule: put logic in `shared/` only when it has at least 2 consumers, or when it enforces a project invariant such as reduced motion, keyboard accessibility, or safe JSON parsing.

## [manual] Architecture Decision Rationale

Decision type: architecture/design-pattern decision.
Status: accepted.
Pattern: **Block-centric feature modules with minimal shared services**.

Why:

- Gutenberg block plugin naturally uses block as the primary unit, not MVC controller/view.
- V1 has interactive blocks (`slider`, `accordion`) that need consistent frontend runtime rules.
- Shared services prevent duplication for project invariants: reduced motion, keyboard accessibility, safe DOM/config parsing.
- Keeping feature logic inside each block avoids over-abstracting early.

Rejected patterns:

- **MVC**: rejected because Gutenberg blocks already define their own editor/save/frontend lifecycle. For this plugin, MVC would add artificial layers without clear benefit.
- **Service-heavy architecture**: rejected for V1 because the plugin is small and block-focused.
- **All logic inside each block with no shared layer**: rejected because accessibility, motion, and config parsing would be duplicated and drift between blocks.

Rules:

- Block-specific behavior stays in the block folder.
- Shared code must be cross-block utility or project invariant.
- Do not create shared abstractions for a single consumer unless it enforces a mandatory invariant.

## [manual] Folder Convention

```text
src/
  index.ts
  shared/
    a11y.ts
    dom.ts
    motion.ts
    types.ts
  slider/
    block.json
    edit.tsx
    save.tsx
    view.ts
    types.ts
  slide/
    block.json
    edit.tsx
    save.tsx
    types.ts
  accordion/
    block.json
    edit.tsx
    save.tsx
    view.ts
    types.ts
```

## [manual] Planned Blocks

- `skvn-marine/slider`
- `skvn-marine/slide`
- `skvn-marine/accordion`

## [manual] Deferred Blocks

- V1 KHÔNG build custom `skvn-marine/product-grid`.
- V1 KHÔNG build custom `skvn-marine/product-list`.
- V1 dùng WooCommerce native blocks/patterns cho product grid/list.
- V2 mới thêm Product Grid/List custom hoặc style blocks liên quan nếu cần.

## [manual] Future Layout Blocks

V1.x / 1.1.0 planned layout blocks:

- Planning file: `.context/planning/003_VERSION_1_1_0_LAYOUT_BLOCKS_PLANNING.md`.
- Source proposal: `.context/proposals/proposal-layout-blocks.md`.
- Candidate blocks: `skvn-marine/card-grid`, `skvn-marine/card`, and later `skvn-marine/quote` / `skvn-marine/hashtag-list`.
- Purpose: repeated artifact sections where core blocks plus raw class names are too fragile for editors.
- `card-grid` and `card` are the first implementation candidates; `quote` and `hashtag-list` are evaluation-only until card-grid/card are validated, unless repeated real artifacts prove earlier editor pain.
- `hashtag-list` must include style controls if implemented: preset-based style, tone, prefix, gap, and wrap. Do not expose raw classes, raw CSS, arbitrary colors, or require manual `#` typing from editors.
- Theme owns visual CSS, decorative presentation, editor/frontend parity, and `skvn-*` classes.
- Plugin owns block registration, attributes, editor UI, saved markup, and any real interaction runtime.
- Not current 0.8.0 scope unless human explicitly changes milestone priority.

## [manual] Future Admin Tooling

HTML-2-Gutenberg tooling belongs to this plugin, not the theme.

Plugin responsibilities for HTML-2-Gutenberg:

- HTML artifact intake.
- Translation workflow/tooling.
- Gutenberg markup validation.
- Brand scan/report output: `brand_source_scan`, `brand_mapping`, `brand_mismatch`, and `token_changes_needed`.
- Future admin publisher/create-page flow.
- Custom blocks only when core Gutenberg blocks plus theme patterns are insufficient.

Theme `skvn-marine` still owns the visual output contract:

- `skvn-*` classes.
- Theme CSS.
- Design tokens.
- Patterns.
- Editor/frontend CSS parity.
- Shared theme-level animation runtime for core blocks, page sections, and theme decoration.

Do not implement the HTML-2-Gutenberg admin tool in the theme.

Do not make this plugin own the primary SKVN visual system.

## [manual] Portable Plugin Animation Boundary

Decision doc: `docs/decisions/block-animation-strategy.md`.

This boundary is active for V1 / 1.2.0.

Future scope may rename or migrate this plugin toward `Gutenberg Supercharger`, so plugin-owned block behavior must remain portable.

Mandatory rule:

- If any animation behavior is exposed by a `skvn-marine-blocks` custom block, the plugin must ship the required behavior without depending on the `skvn-marine` theme.
- Required behavior includes attributes, editor controls, frontend JS, CSS classes, keyframes, transitions, reduced-motion fallback, device targeting, and no-JS visible fallback.
- Theme CSS/JS may skin or override plugin motion, but must not be required for plugin block animation to work.
- Theme animation remains acceptable for core blocks, page sections, and theme decoration that are not plugin-owned block behavior.
- Tailwind/WindPress is prototype-only and must not become the production animation contract.

Implemented V1 / 1.2.0:

- `src/shared/motion.ts` owns reduced-motion, device-targeting, and one-shot viewport observer invariants.
- `src/accordion/view.ts` progressively enhances saved Accordion markup with an accessible trigger, ARIA state, real-height animation, and keyboard navigation.
- `src/card/view.ts` is the first governed block motion surface.
- Card presets are limited to Fade up, Fade in, and Hover lift.
- Fade presets support On scroll or Always; Hover lift uses On hover automatically.
- Desktop, Tablet, and Mobile are independent toggles.
- `src/motion.css` and the motion frontend entry are plugin-owned and do not depend on the SKVN theme.
- Onsite verification contract deferred to V1 / 1.2.9:
  `docs/testing/onsite-slider-motion-1.2.9.md`.

## [manual] Footer Settings Module And Future Gutenberg Supercharger Boundary

0.9.0 footer page settings must be implemented as a migration-ready module inside the current `skvn-marine-blocks` plugin.

Allowed current structure:

```text
wp-content/plugins/skvn-marine-blocks/
  modules/
    footer-settings/
```

or:

```text
wp-content/plugins/skvn-marine-blocks/
  includes/
    modules/
      footer-settings/
```

Current 0.9.0 rules:

- Keep plugin slug `skvn-marine-blocks`.
- Keep plugin text domain `skvn-marine-blocks`.
- Keep option key `skvn_footer_page_id`.
- Keep bootstrap and build paths compatible with the current plugin.
- Do not create a `gutenberg-supercharger` or `gutenberg-turbo` plugin in V1.
- Do not rename namespaces, option keys, plugin headers, or activation path.
- Do not add a custom CPT or display-rules system for footer settings.

Implemented 0.9.0 source:

- Module path: `wp-content/plugins/skvn-marine-blocks/modules/footer-settings/footer-settings.php`.
- Bootstrap path: `wp-content/plugins/skvn-marine-blocks/skvn-marine-blocks.php`.
- Admin surface: `Settings → SKVN Footer`.
- The selector only stores a published WordPress page ID or `0`.
- Human chose online/onsite smoke testing instead of local runtime testing for 0.9.0.
- Runtime PHP modules under `modules/` are production files, not dev-only source.
- Deploy artifacts and plugin zips must include any plugin folder loaded by `require`, `require_once`, `include`, or `include_once`.
- When a future milestone adds a runtime folder such as `modules/`, `includes/`, or runtime `assets/`, update `tools/build-deploy-artifact.mjs` and `docs/workflows/deploy-artifact-workflow.md` in the same task.
- After packaging, verify the zip contains the required runtime module paths before onsite upload.

Planned 0.11.0 admin menu refinement:

- Move SKVN Footer out of `Settings`.
- Create a top-level `SKVN Marine` admin menu in the current `skvn-marine-blocks` plugin.
- Put footer settings under the `SKVN Marine` admin menu as a tab/page.
- Keep option key `skvn_footer_page_id` and existing render behavior unchanged.
- Add footer background preset option `skvn_footer_background_preset`.
- Sanitize footer background to approved presets only: `default`, `deep-navy`, `trust-blue`, `white`, `fresh-sky`.
- Do not expose raw hex/rgb/hsl color input for the footer background in 0.11.0.
- The background preset only applies when `skvn_footer_page_id` points to a valid published page.
- Do not hardcode advanced menu ordering in SKVN code; onsite menu repositioning belongs to the external ASE plugin.

0.11.0 decision contract:

- `docs/decisions/footer-appearance-settings-0.11.0.md`

Planned 0.12.0 header actions and B2B search:

- Decision contract: `docs/decisions/header-actions-search-0.12.0.md`.
- Add a future `SKVN Marine → Header` admin surface.
- Store and sanitize header action settings for product search, post/site search, contact CTA, and optional Request Quote CTA.
- Product search and post/site search may ship together but must keep explicit search targets: products, articles, or all site.
- Phase 1 search should use native WordPress/WooCommerce queries with taxonomy/title-first matching.
- Do not add Elastic/OpenSearch in phase 1.
- Do not add custom search query cache, custom SQL cache table, or transient registry in phase 1; rely on native queries and available object cache.

Implemented 0.12.0 source:

- Module path: `wp-content/plugins/skvn-marine-blocks/modules/header-settings/header-settings.php`.
- Bootstrap path: `wp-content/plugins/skvn-marine-blocks/skvn-marine-blocks.php`.
- Admin surface: `SKVN Marine -> Header`.
- Option key: `skvn_header_actions`.
- Stored settings are sanitized to booleans, approved search targets (`products`, `articles`, `all`), approved layouts (`compact`, `full`), text labels, and URLs.
- Header actions are disabled by default for safe deploy; human enables them onsite for testing.

Planned V1.x SKVN Element CPT foundation:

- Planning file: `.context/planning/014_VERSION_1_X_SKVN_ELEMENT_CPT_PLANNING.md`.
- Register plugin-owned CPT `skvn_element` for reusable site elements such as `site_header` and `site_footer`.
- The CPT must be admin/editable but not public: no public single URL, no archive, excluded from search, hidden from nav menus, and excluded from sitemap exposure.
- This is the SEO-safe successor to using normal WordPress Pages for reusable header/footer content.
- Theme owns rendering selected elements into header/footer surfaces and any GeneratePress adapter during the 2.0.0 migration window.
- Keep existing `skvn_footer_page_id` bridge working until an explicit migration step is approved.

Future direction:

- `Gutenberg Supercharger` is a possible V4 / 4.0.0 umbrella-plugin direction and standard/core product name.
- `Gutenberg Supercharger Stage 2` is the pro/commercial stage name.
- `Gutenberg Remap` is retained only as an alternate/redirect candidate.
- Tagline: `Remap Gutenberg. Turbocharge your site.`
- Community tagline: `Make your site feel more "Á đù VTEC".`
- Planning file: `.context/planning/008_FUTURE_CANDIDATE_GUTENBERG_TURBO_PLANNING.md`.
- When future migration is approved, modules such as `footer-settings/`, `article-templates/`, and `layout-controls/` may move under `gutenberg-supercharger/modules/`.
- Until that future milestone is explicitly approved, only use the migration-ready module shape inside the current plugin.

## [manual] SKVN Editor Controls

0.8.0 editor-control contract:

- Contract doc: `docs/decisions/skvn-editor-controls-0.8.0.md`
- Onsite test checklist: `docs/testing/onsite-editor-controls-0.8.0.md`

Plugin responsibilities:

- Sidebar UI for supported SKVN-owned blocks/surfaces.
- Block attributes.
- Saved markup.
- Interactive behavior.

Theme responsibilities remain visual:

- Tokens.
- `skvn-*` classes.
- Width, spacing, radius, shadow, and tone presets.
- Editor/frontend CSS parity.

Do not require raw class input, raw color values, arbitrary inline spacing, custom CSS, or custom JavaScript for normal marketing-editor use.

V1.x / 1.6.0 planned SKVN surface presets:

- Planning file: `.context/planning/009_VERSION_1_6_0_SKVN_SURFACE_PRESETS_PLANNING.md`.
- Plugin/editor controls may expose a safe `surfaceStyle` attribute after the theme owns the visual classes.
- Allowed values should stay preset-based: `flat`, `soft`, `glass`, `elevated`, `outlined`.
- Do not save Tailwind/WindPress utility classes as the production contract.
- Do not expose arbitrary blur, shadow, color, or raw CSS inputs for normal editors.

Slider editor UX decision for V1 / 1.2.0:

- Use a stacked editor preview; do not run Swiper/autoplay inside Gutenberg.
- Each Slide directly edits its background image, heading, lead, and CTA.
- Background image uses the WordPress Media Library with choose, replace, and remove actions.
- Overlay opacity is governed from 0–80%; raw CSS is not exposed.

V1 / 1.2.1 Slider preset and inserter decision:

- Register one `SKVN Marine` Block Inserter category for all SKVN-owned blocks.
- Expose Hero Slider, Product Showcase, and Card Carousel as add-and-see presets.
- Reuse the existing Slider/Slide block slugs and one Swiper runtime.
- Use Gutenberg variations/templates rather than separate duplicated Slider blocks.
- Do not build a custom slide manager, selected-slide canvas, or setup modal.
- Keep native Gutenberg List View and block actions for navigation, reorder, duplicate, and remove.
- Onsite QA for 1.2.0 and 1.2.1 is consolidated in V1 / 1.2.9.

Implemented V1 / 1.2.1:

- `block_categories_all` registers one plugin-owned `SKVN Marine` category.
- Slider, Slide, Accordion, Card Grid, and Card metadata use category slug `skvn-marine`.
- `src/slider/variations.ts` registers Hero Slider, Product Showcase, and Card Carousel with inserter scope and editable InnerBlocks templates.
- Hero is the default Slider variation so the inserter does not add a fourth generic Slider choice.
- Product Showcase uses flow-based Columns/Image/content blocks.
- Card Carousel saves one responsive preset flag and maps it to Swiper 3/2/1 breakpoints without changing the runtime dependency.
- Existing Slider serialization remains unchanged when the new preset attributes are absent/default.

Implemented V1 / 1.2.3 Feature Showcase:

- Planning file: `.context/planning/016_VERSION_1_2_3_FEATURE_SHOWCASE_PLANNING.md`.
- Decision doc: `docs/decisions/feature-showcase-1.2.3.md`.
- Human-approved name: `SKVN Feature Showcase`.
- Block slug: `skvn-marine/feature-showcase`.
- Source is active under `wp-content/plugins/skvn-marine-blocks/src/feature-showcase/`.
- Metadata is `block.json` and the block is registered from `src/index.ts`.
- Build output includes `build/feature-showcase/block.json`.
- Do not overload the existing `SKVN Accordion`; Feature Showcase is a separate editorial image-panel block.
- The block owns only the image-panel group. The B2B intro/copy/meta text stack belongs to a separate Gutenberg pattern.
- Editors can add, remove, reorder, and edit panels; choose horizontal or vertical desktop layout; choose the initially open panel; and hide or use disclosure on mobile.
- Saved markup uses native `details`/`summary` so mobile tap and keyboard interaction remain available without JavaScript.
- `src/feature-showcase/view.ts` progressively enhances that markup to keep exactly one panel active, close siblings when another panel opens, and prevent the active/only panel from collapsing into an empty surface.
- On fine-pointer devices, panel hover activates the corresponding `details`; on touch/coarse-pointer devices, summary tap remains the activation path.
- Enhanced summary clicks prevent the native details toggle before activating the target, avoiding a transient all-closed flash.
- Feature Showcase gradient choices are governed contrast packages: Ocean,
  Deep navy, Marine teal, and Fresh sky. A package controls the panel, rail,
  overlay, text, label, index, and border colors together.
- Fresh sky uses dark text and a light image overlay; editors do not enter raw
  colors or gradients.
- The first activated static markup is preserved through a Gutenberg deprecation and legacy CSS until old content is resaved.

0.5.1 brand-mapping contract:

- Treat artifact colors and Tailwind classes as source hints, not production contracts.
- Map visual cues to theme-owned SKVN tokens/classes when producing Gutenberg output.
- If a required token/class does not exist, report it in `token_changes_needed` or `missing_theme_classes`; do not inject raw color CSS into content.
- Leave brand profile/theme token implementation for V1 / 0.7.0.

## [manual] Rules

- Use TypeScript.
- Use `block.json`.
- Use `skvn-marine` block namespace.
- Do not depend on GeneratePress-specific markup.
