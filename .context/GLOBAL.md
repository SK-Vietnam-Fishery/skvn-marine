# SKVN Marine — Global Context Map

> AI working memory. Không phải docs. Không phải changelog.
> Load file này đầu tiên trước mọi task.

---

<!-- AUTO_START -->
[auto] Stack Overview

- WordPress + GeneratePress (parent) + `skvn-marine` (child theme)
- WooCommerce — native products, categories, attributes
- WindPress (Tailwind integration) — utility classes, animations, responsive
- Plugin: `skvn-marine-blocks` — custom Gutenberg blocks (TypeScript, @wordpress/scripts)
- CF7 + CFDB7 + n8n — quote form → submission storage → lead automation
- Rank Math — SEO, schema
- Polylang — multilingual (standby V1, activate later)
- Antispam Bee — comment spam. CF7 honeypot + optional Turnstile — form spam
- Out of the Block: OpenStreetMap — map/contact section block engine
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
| Layout Translator CLI | `tools/layout-translator/translate-layout.mjs` | Dev tool | HTML/CSS artifact → Gutenberg markup contract |
| Plugin root | `skvn-marine-blocks/` | Plugin | skvn-marine-blocks.php |
| Slider block | `src/slider/` + `src/slide/` | Plugin | Swiper frontend, stacked editor preview |
| Accordion block | `src/accordion/` | Plugin | Keyboard nav bắt buộc |
| Product Grid block | `src/product-grid/` | Plugin | WooCommerce query |
| Product List block | `src/product-list/` | Plugin | WooCommerce query + pagination |
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

- `.context/planning/000_VERSION_0_1_0_PLANNING.md`

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
- `docs/decisions/slider-block.md`
- `docs/standards/ai-rules.md`
- `docs/standards/security-guidelines.md`
- `docs/testing/frontpage-testing.md`
- `docs/testing/testing-checklist.md`
- `docs/workflows/context-map-workflow.md`
- `docs/workflows/layout-translator-workflow.md`
- `docs/workflows/theme-development-workflow.md`
- `docs/explain/explain-for-5-years-old.md`
- `docs/artifacts/brand-palette-options.html`

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

**A4. Quote form stack cố định ở V1**
CF7 → CFDB7 → n8n. KHÔNG custom-code form handler. KHÔNG popup/modal làm primary flow.
URL: `/request-a-quote/?product_id=123` → `/quote-thank-you/`

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

---

[manual] Invariants — Không bao giờ được vi phạm

- KHÔNG sửa `themes/generatepress/` (bất kỳ file nào)
- KHÔNG đặt custom block logic trong theme
- KHÔNG rename namespace `skvn-marine`, prefix `skvn_marine_` / `skvn_marine_blocks_`, CSS prefix `skvn-`
- KHÔNG overwrite manual image ALT
- KHÔNG auto-generate caption ở V1
- KHÔNG custom-code quote form handler — dùng CF7 + CFDB7 + n8n
- KHÔNG expose n8n webhook unprotected (cần hard-to-guess URL + optional shared secret)
- KHÔNG log credential dù debug
- PHP: input phải sanitize, output phải escape (`esc_html`, `esc_attr`, `esc_url`, `wp_kses_post`)
- KHÔNG cache: `/cart/`, `/checkout/`, `/my-account/`, `/request-a-quote/`, `/quote-thank-you/`
- KHÔNG modify hơn 3–5 files per AI task trừ khi có lý do rõ ràng

---

[manual] Phase Scope

**V1 (current)** — Một website B2B marine, local-first
- Theme child + design system + block styles + patterns
- Plugin blocks: Slider, Accordion, Product Grid, Product List
- Quote flow: CF7 + CFDB7 + n8n
- English content, prepare cho multilingual nhưng KHÔNG activate Polylang

**V2 (future)**
- Staging + Git deploy workflow
- Redis object cache nếu cần
- CDN cho static assets
- Advanced filtering cho product
- Technical Product Card với specs table

**V3 (future)**
- Đánh giá lại: tiếp tục GeneratePress base hay custom base theme
- Child theme support
- Marketing governance layer
- GitHub Actions release zip

---

[manual] Open Decisions (chưa resolve — xem TENSIONS_OPEN.md)

1. V1 product grid/list: WooCommerce native blocks trước hay custom blocks ngay?
2. CF7 ↔ n8n integration method chính xác
3. Polylang: activate V1 hay chỉ prepare?
4. Slider editor UX: stacked / selected-slide-preview / carousel preview?
5. CF7 spam layer: Turnstile ngay V1 hay delay?
6. V2 hosting/deployment approach cụ thể
7. V3: stay GeneratePress hay custom base theme?

---

[manual] AI Guardrails — Project-Wide

Mọi AI agent làm việc trong project này PHẢI đọc `AGENTS.md` trước.
Xem thêm: `.context/TENSIONS_OPEN.md` trước khi modify bất kỳ module nào.

Những gì AI KHÔNG được làm (summary nhanh):
- Sửa GeneratePress parent
- Thêm dependency không có rationale
- Rename namespace/prefix
- Đặt block logic trong theme
- Overwrite manual image ALT
- Custom-code quote form handler
- Nếu thấy conflict → ghi vào TENSIONS_OPEN.md, DỪNG, hỏi lại
<!-- MANUAL_END -->


