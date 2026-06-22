# SKVN Marine — Global Context Map

> AI working memory. Không phải docs. Không phải changelog.
> Load file này đầu tiên trước mọi task.

---

<!-- AUTO_START -->
[auto] Stack Overview

- WordPress + GeneratePress (parent) + `skvn-marine` (child theme)
- WooCommerce — native products, categories, attributes
- WindPress (Tailwind integration) — optional/prototyping aid; not the production visual-system source of truth
- Plugin: `skvn-marine-blocks` — custom Gutenberg blocks (TypeScript, @wordpress/scripts)
- Page display/sidebar controls completed in 0.5.1; Quote UI completed in 0.6.0; basic CF7/CFDB7 source/docs contract in 0.7.0; quote-flow runtime verification/handoff in 0.7.1; SKVN Editor Controls in 0.8.0; Footer Page Settings in 0.9.0; onsite UI/test debt closed in 0.10.0; CF7 data-flow and map block/display testing deferred to 1.1.2; n8n after 1.0.0
- Rank Math — SEO, schema
- Polylang — multilingual (standby V1, activate later)
- Antispam Bee — comment spam. CF7 honeypot + optional Turnstile — form spam
- OpenStreetMap iframe embed — map/contact section, wrapped by theme CSS
- Swiper — slider block frontend runtime
- IntersectionObserver — scroll reveal
- Shared animation runtime (`assets/js/animations.js`) — fade/parallax/magnetic

[auto] Module Index

| Module | Path | Layer | Ghi chú |
|---|---|---|---|
| Theme root | `skvn-marine/` | Theme | style.css, functions.php, theme.json |
| Theme setup | `inc/setup.php` | Theme | register_nav_menus, add_theme_support |
| Theme enqueue | `inc/enqueue.php` | Theme | wp_enqueue + filemtime versioning |
| Block styles | `inc/block-styles.php` | Theme | register_block_style() |
| Media helpers | `inc/media.php` | Theme | ALT auto-fill từ attachment title |
| WooCommerce overrides | `inc/woocommerce.php` | Theme | Visual override, KHÔNG logic |
| WindPress config | `inc/windpress.php` | Theme | WindPress integration hooks |
| Animation runtime | `assets/js/animations.js` | Theme | Shared — KHÔNG tách per-block |
| Patterns | `patterns/*.php` | Theme | Block patterns |
| Plugin root | `skvn-marine-blocks/` | Plugin | skvn-marine-blocks.php |
| Slider block | `src/slider/` + `src/slide/` | Plugin | Swiper frontend, stacked editor preview |
| Accordion block | `src/accordion/` | Plugin | Keyboard nav bắt buộc |
| Product Collection block | `src/product-collection/` | Plugin | Planned 1.3.3 dynamic WooCommerce grid/carousel |
| Post Collection block | `src/post-collection/` | Plugin | Planned 1.3.3 dynamic post grid/carousel |
<!-- AUTO_END -->

---

<!-- MANUAL_START -->
[manual] Context Map Layout

Active context module files live in `.context/modules/`.

Current module context files:

- `.context/modules/THEME_SKVN_MARINE.md`
- `.context/modules/PLUGIN_SKVN_MARINE_BLOCKS.md`
- `.context/modules/QUOTE_FLOW.md`

Planning snapshots live in `.context/planning/` and use a three-digit ordering prefix, starting at `000_`.

Current planning file:

- `.context/planning/000_VERSION_1_1_0_PLANNING.md`
- `.context/planning/003_VERSION_1_1_0_LAYOUT_BLOCKS_PLANNING.md` — checkpoint plan for layout blocks promoted from `.context/proposals/proposal-layout-blocks.md`. Covers `skvn-marine/card-grid`, `skvn-marine/card`, and quote/testimonial block evaluation for repeated artifact sections that are too fragile for core blocks plus raw class names.
- `.context/planning/010_VERSION_1_7_0_FRONT_PAGE_TRANG_CHUYEN_DOI_SO_PLANNING.md` — future front-page planning for the external `ChuyenDoiSo.html` benchmark: document/resource list, taxonomy/category counts from real data, whole-site search styling/hook boundary, and layout patterns discovered during the 1.1.0 brainstorm trigger.
- Historical Slider planning from 1.2.0, 1.2.1, and the superseded 1.3.0 draft now lives under `.context/planning/archives/`.
- `.context/planning/016_VERSION_1_2_3_FEATURE_SHOWCASE_PLANNING.md` — active `1.2.3` Feature Showcase plan. Source is activated under `src/feature-showcase/` as `skvn-marine/feature-showcase`.
- `.context/planning/020_VERSION_1_3_2_FEATURE_SHOWCASE_AUTOPLAY_AND_LINKS_PLANNING.md` — completed `1.3.2` plan for governed Feature Showcase hover/autoplay modes, snapping `3/5/7/9s` delay, per-panel Gutenberg LinkControl CTA, and shared autoplay invariants with Slider after `1.3.1` approval.
- `.context/planning/024_VERSION_1_3_3_DYNAMIC_COLLECTIONS_PLANNING.md` — active `1.3.3` plan for dynamic Product/Post Collection blocks: Product Grid, Product Carousel, Post Grid, Post Carousel, native WP/Woo query adapters, governed cards/actions, shuffle-balanced ordering, and onsite QA.
- `.context/planning/021_VERSION_1_3_1_SLIDER_EDITOR_AND_CORE_TRANSITIONS_PLANNING.md` — approved expansion of the current `1.3.1` scope: centered editor Add Slide action, governed/hidden Slides Per View controls, Directional Wipe/Fade/Zoom Out, `600–1000ms` transition duration, and reduced-motion fallback.
- `.context/planning/022_FUTURE_CANDIDATE_SLIDER_EXTENSION_API_PLANNING.md` — Future Candidate for the `2.x.x` phase: WordPress-native Transition/Pagination Extension API, namespaced registries, fallback/governance, and Portal Zoom plus Timed Tabs as reference integrations outside the lightweight core.
- `docs/decisions/slider-completion-spec-1.3.0.md` — active Slider source of truth. V1 / 1.3.0 completes dynamic rendering, compatibility, accessibility, and geometry; expanded carousel/showcase UX remains a later human-approved milestone.
- `docs/decisions/slider-navigation-and-pagination-controls.md` — approved V1 / 1.3.1 direction for independent arrow/pagination style and position controls, conditional bottom clustering, Swiper-owned timed pagination, governed `5/7/9/12s` duration, and responsive/accessibility fallbacks after the failed 1.3.0 media, width, pagination, and memory behavior is corrected.
- `.context/planning/013_VERSION_2_0_0_TO_3_0_0_STANDALONE_BLOCK_THEME_PLANNING.md` — medium/long-term plan: 2.0.0 starts migration away from GeneratePress; 3.0.0 completes standalone SKVN theme direction. Gutenberg remains the WP content/editor model; custom blocks become additive governed site enhancements.
- `.context/planning/014_VERSION_1_X_SKVN_ELEMENT_CPT_PLANNING.md` — 1.x foundation plan for SEO-safe reusable site elements (`skvn_element`) before 2.0.0 migration. Header/footer reusable content should move away from normal Pages toward non-public CPT elements.

Future plugin architecture planning:

- `.context/planning/008_FUTURE_CANDIDATE_GUTENBERG_TURBO_PLANNING.md` — split small Gutenberg enhancement plugins first, then possibly consolidate into an umbrella plugin named `Gutenberg Supercharger` around a future V4 / 4.0.0 candidate. Standard/core edition name: `Gutenberg Supercharger`; pro/commercial edition name: `Gutenberg Supercharger Stage 2`; `Gutenberg Remap` is retained only as an alternate/redirect candidate; community tagline: "Make your site feel more \"Á đù VTEC\"." Current V1 work may use migration-ready module structure, but must not create/rename to `gutenberg-supercharger` or `gutenberg-turbo`.
- `.context/planning/009_VERSION_1_6_0_SKVN_SURFACE_PRESETS_PLANNING.md` — SKVN-local hardening plan for approved flat/soft/glass/elevated/outlined surface presets. Theme owns `skvn-surface--*` classes and tokens; plugin/editor controls may select presets later. Production output must not depend on WindPress/Tailwind utilities.

Proposal files under `.context/proposals/` are not active protocol and are ignored by git. Do not load them unless the human explicitly asks to review a proposal.

Project docs under `docs/` are grouped by reading purpose:

- `docs/decisions/` — approved or working decisions that should be easy to audit.
- `docs/standards/` — coding, AI, and security standards for human-readable reference.
- `docs/workflows/` — development and context-management workflows.
- `docs/testing/` — test methods, test prompts, manual verification notes.
- `docs/explain/` — simplified explanations for human review.
- `docs/artifacts/` — visual/static artifacts such as HTML mockups; useful for review, not source of truth.

Current active docs:

- `docs/decisions/architecture.md`
- `docs/decisions/caching-strategy.md`
- `docs/decisions/design-direction.md`
- `docs/decisions/product-data-model.md`
- `docs/decisions/quote-flow.md`
- `docs/decisions/footer-page-settings-0.9.0.md`
- `docs/decisions/footer-appearance-settings-0.11.0.md`
- `docs/decisions/header-actions-search-0.12.0.md`
- `docs/decisions/core-control-core-button-hover.md`
- `docs/decisions/skvn-dynamic-collections-1.3.3.md`
- `docs/decisions/product-card-grid-layout-contract.md`
- `docs/decisions/block-animation-strategy.md`
- `docs/decisions/skvn-editor-controls-0.8.0.md`
- `docs/decisions/slider-completion-spec-1.3.0.md`
- `docs/decisions/feature-showcase-1.2.3.md`
- `docs/decisions/typography-and-gp-exit-report.md` — typography settings 1.5.0: token architecture, Settings API pattern, GP coupling audit, WooCommerce layer, 2.0.0 exit plan.
- `docs/standards/ai-rules.md`
- `docs/standards/security-guidelines.md`
- `docs/standards/memory-leak-prevention-rules.md` — quy tắc phòng chống memory leak cho Gutenberg plugin (event listener cleanup, observer teardown, block edit lifecycle).
- `docs/testing/frontpage-testing.md`
- `docs/testing/onsite-test-debt-checklist.md`
- `docs/testing/onsite-map-block-1.1.2.md`
- `docs/testing/footer-page-settings-0.9.0.md`
- `docs/testing/footer-appearance-settings-0.11.0.md`
- `docs/testing/header-actions-search-0.12.0.md`
- `docs/testing/onsite-0.11-0.12-completion-checklist.md`
- `docs/testing/onsite-editor-controls-0.8.0.md`
- `docs/testing/onsite-dynamic-collections-1.3.3.md`
- `docs/testing/onsite-slider-motion-1.3.2.md`
- `docs/testing/testing-checklist.md`
- `docs/workflows/context-map-workflow.md`
- `docs/workflows/ideation-chom-revslider-theme-tra-phi-to-gutenberg.md`
- `docs/workflows/deploy-artifact-workflow.md`
- `docs/workflows/layout-translator-workflow.md`
- `docs/workflows/onsite-qa-checklist.md`
- `docs/workflows/theme-development-workflow.md`
- `docs/workflows/versioning-release-workflow.md`
- `docs/explain/explain-for-5-years-old.md`
- `docs/artifacts/init-prompt-v1-1.0.0-launch-ready.md`
- `docs/artifacts/init-prompt-v1-1.3.0-slider-dynamic-rendering.md`
- `docs/artifacts/init-prompt-v1-1.3.1-slider-controls.md`
- `docs/artifacts/init-prompt-v1-1.3.3-dynamic-collections.md`
- `docs/artifacts/init-prompt-v1-1.3.4-core-control.md`
- `docs/workflows/start-v1-1.3.0-slider-repair.md` — reusable repair prompt for the failed 1.3.0 build, including State Delta diagnostics, target code architecture, geometry/RAM gates, and the targeted human smoke-test boundary before 1.3.1.
- `docs/artifacts/brand-palette-options.html`
- `docs/debug-casebook/gutenberg-plugin-memory-leak-guide.md` — hướng dẫn debug và reproduce memory leak trong Gutenberg plugin blocks.

---

[manual] Architecture Decisions — Project-Wide

**A1. Child theme, không fork GeneratePress**
Lý do: giảm PHP maintenance, tránh AI sửa parent theme, dễ upstream update.
Invariant: KHÔNG BAO GIỜ sửa file trong `themes/generatepress/`.

**A2. Theme vs Plugin boundary**
Rule: nếu thay theme mà feature bị mất → feature thuộc plugin, không thuộc theme.
Visual/layout → theme. Logic/data → plugin.
Invariant: custom blocks KHÔNG được đặt trong theme.

**A3. Không custom CPT cho product ở V1**
Dùng WooCommerce native products. Custom fields (ACF/Meta Box) chỉ thêm khi WC attributes không đủ.

**A4. Quote path phased by milestone**
0.5.1 completed page-level display/sidebar controls. 0.6.0 completed Quote UI, same-site request quote page surface, and CTA polish. 0.7.0 prepares the basic CF7/CFDB7 source/docs contract. 0.7.1 verifies runtime/admin setup and closes the immediate handoff gap. 0.10.0 closed onsite UI evidence for footer and CF7 interface. Remaining CF7 data-flow evidence is deferred to V1 / 1.1.2 because it depends on product/page block origin, hidden/context fields, CFDB7 storage, and thank-you behavior. n8n automation dời sau version 1.0.0. KHÔNG custom-code form handler. KHÔNG popup/modal làm primary flow.
URL pattern giữ: `/request-a-quote/?product_id=123`

**A10. Page display controls**
Page-level controls such as Hide site header and Hide site footer belong to the `skvn-marine` child theme. Use safe editor/admin controls and page meta; do not require marketing users to type raw classes. Do not add a header/footer builder plugin by default.

**A11. SKVN Editor Controls**
0.8.0 adds token-governed sidebar controls for SKVN-owned Gutenberg blocks and translated layout surfaces. Theme owns tone, spacing, width, radius, shadow, and visual classes. Plugin owns block sidebar UI, attributes, saved markup, and interactive behavior. Editors should not need raw class input, raw colors, or arbitrary inline spacing values.

**A12. Footer Page Settings**
0.9.0 adds a plugin settings page for `skvn_footer_page_id`. The theme renders the selected footer page through GeneratePress' `generate_footer` surface, with GeneratePress default footer as fallback. No custom CPT, no display rules system, and no GeneratePress replacement. Implement footer settings as a migration-ready module inside the current `skvn-marine-blocks` plugin; do not create or rename to `gutenberg-supercharger` or `gutenberg-turbo` in V1.

**A13. Plugin CSS independence — token cascade**
Plugin `skvn-marine-blocks` phải có khả năng hoạt động độc lập, không phụ thuộc hoàn toàn vào theme.
Sự phụ thuộc hiện tại (plugin dùng `--skvn-*` tokens từ theme) là **technical debt được chấp nhận có chủ ý** để phát triển nhanh.
CSS token cascade đúng: Plugin CSS (define defaults/fallback) → Theme style.css (override `--skvn-*`) → GP Customizer (future).
Invariant: KHÔNG tạo thêm dependency ngầm từ plugin → theme mà không ghi nhận. Nếu buộc dùng theme token, ghi chú technical debt rõ ràng tại chỗ.
Roadmap: Dần move tokens cần thiết vào plugin block CSS với fallback values, để sau này tách hoàn toàn.

**A5. Animation runtime dùng chung**
`assets/js/animations.js` là single runtime. KHÔNG tạo animation logic riêng per block trừ khi bắt buộc.
Invariant: tất cả animation phải respect `prefers-reduced-motion`.

**A6. Editor opacity rule**
Reveal animations KHÔNG được set `opacity: 0` trong editor nếu không có safe fallback.
Editor phải match frontend ~80%. Animations có thể chạy trong editor nhưng KHÔNG làm editing painful.

**A7. Image ALT automation**
Auto-fill từ attachment title khi ALT empty. KHÔNG overwrite manual ALT. KHÔNG auto-generate caption ở V1.
File: `inc/media.php`, prefix: `skvn_marine_auto_set_image_alt_from_title()`.

**A8. Dependency policy**
Mỗi dependency mới PHẢI document: purpose, alternative, bundle impact, load scope, removal plan.
KHÔNG add dependency không có rationale.

**A9. Map/contact V1 fallback**
Shared host chỉ hỗ trợ PHP 8.0. `Out of the Block: OpenStreetMap` yêu cầu PHP 8.1, nên V1 dùng OpenStreetMap iframe embed trong Gutenberg/HTML content, bọc bằng SKVN theme CSS. Không thêm map plugin mới trong V1 nếu iframe đủ dùng.

---

[manual] Invariants — Không bao giờ được vi phạm

- KHÔNG sửa `themes/generatepress/` (bất kỳ file nào)
- KHÔNG đặt custom block logic trong theme
- KHÔNG rename namespace `skvn-marine`, prefix `skvn_marine_` / `skvn_marine_blocks_`, CSS prefix `skvn-`
- KHÔNG overwrite manual image ALT
- KHÔNG auto-generate caption ở V1
- KHÔNG custom-code quote form handler — 0.7.0 dùng CF7/CFDB7
- KHÔNG expose n8n webhook unprotected; n8n deferred until after 1.0.0
- KHÔNG log credential dù debug
- PHP: input phải sanitize, output phải escape (`esc_html`, `esc_attr`, `esc_url`, `wp_kses_post`)
- KHÔNG cache: `/cart/`, `/checkout/`, `/my-account/`, `/request-a-quote/`, `/quote-thank-you/`
- KHÔNG modify hơn 3–5 files per AI task trừ khi có lý do rõ ràng

---

[manual] Phase Scope

**V1 (current)** — Một website B2B marine, local-first
- Theme child + design system + block styles + patterns
- Plugin blocks: Slider, Accordion, Product Collection, Post Collection
- Page display/sidebar controls in 0.5.1; Quote UI/editor controls in 0.6.0; basic CF7/CFDB7 source/docs contract in 0.7.0; quote-flow runtime verification/handoff in 0.7.1; SKVN Editor Controls in 0.8.0; Footer Page Settings in 0.9.0; onsite UI/test debt closed in 0.10.0; CF7 data-flow and map block/display testing in 1.1.2; n8n after 1.0.0
- English content, prepare cho multilingual nhưng KHÔNG activate Polylang

**V2 (future)**
- Start migration away from GeneratePress; `2.0.0` is the migration-start boundary, not the completion point.
- Staging + Git deploy workflow
- Redis object cache nếu cần
- CDN cho static assets
- Advanced filtering cho product
- Technical Product Card với specs table

**V3 (future)**
- Complete GeneratePress removal; `3.0.0` is the standalone/custom theme completion boundary.
- Child theme support
- Marketing governance layer
- GitHub Actions release zip

---

[manual] Open Decisions (chưa resolve — xem TENSIONS_OPEN.md)

1. V1 product grid/list: WooCommerce native blocks trước hay custom blocks ngay?
2. Exact sidebar/admin control flow for page display options
3. Polylang: activate V1 hay chỉ prepare?
4. Slider editor UX: stacked / selected-slide-preview / carousel preview?
5. CF7 spam layer for 0.7.0 basic quote form
6. V2 hosting/deployment approach cụ thể
7. V3: stay GeneratePress hay custom base theme?

---

[manual] AI Guardrails — Project-Wide

Mọi AI agent làm việc trong project này PHẢI đọc `AGENTS.md` trước.
Xem thêm: `.context/TENSIONS_OPEN.md` trước khi modify bất kỳ module nào.

When using external AI feedback such as MetaAI:

- Treat it as review input, not source of truth.
- Ask it for HTML artifacts, layout specs, QA checklists, copy variants, or critique.
- Do not rely on it as the primary image generation source.
- For image needs, use a dedicated image generation flow or create code/HTML artifacts that can be reviewed and exported.
- Validate all external AI suggestions against `AGENTS.md`, `.context/`, and current repo files before applying.

Những gì AI KHÔNG được làm (summary nhanh):
- Sửa GeneratePress parent
- Thêm dependency không có rationale
- Rename namespace/prefix
- Đặt block logic trong theme
- Overwrite manual image ALT
- Custom-code quote form handler
- Nếu thấy conflict → ghi vào TENSIONS_OPEN.md, DỪNG, hỏi lại
<!-- MANUAL_END -->


