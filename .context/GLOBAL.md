# SKVN Marine â€” Global Context Map

> AI working memory. KhÃ´ng pháº£i docs. KhÃ´ng pháº£i changelog.
> Load file nÃ y Ä‘áº§u tiÃªn trÆ°á»›c má»i task.

---

<!-- AUTO_START -->
[auto] Stack Overview

- WordPress + GeneratePress (parent) + `skvn-marine` (child theme)
- WooCommerce â€” native products, categories, attributes
- WindPress (Tailwind integration) â€” utility classes, animations, responsive
- Plugin: `skvn-marine-blocks` â€” custom Gutenberg blocks (TypeScript, @wordpress/scripts)
- CF7 + CFDB7 + n8n â€” quote form â†’ submission storage â†’ lead automation
- Rank Math â€” SEO, schema
- Polylang â€” multilingual (standby V1, activate later)
- Antispam Bee â€” comment spam. CF7 honeypot + optional Turnstile â€” form spam
- Out of the Block: OpenStreetMap â€” map/contact section block engine
- Swiper â€” slider block frontend runtime
- IntersectionObserver â€” scroll reveal
- Shared animation runtime (`assets/js/animations.js`) â€” fade/parallax/magnetic

[auto] Module Index

| Module | Path | Layer | Ghi chÃº |
|---|---|---|---|
| Theme root | `skvn-marine/` | Theme | style.css, functions.php, theme.json |
| Theme setup | `inc/setup.php` | Theme | register_nav_menus, add_theme_support |
| Theme enqueue | `inc/enqueue.php` | Theme | wp_enqueue + filemtime versioning |
| Block styles | `inc/block-styles.php` | Theme | register_block_style() |
| Media helpers | `inc/media.php` | Theme | ALT auto-fill tá»« attachment title |
| WooCommerce overrides | `inc/woocommerce.php` | Theme | Visual override, KHÃ”NG logic |
| WindPress config | `inc/windpress.php` | Theme | WindPress integration hooks |
| Animation runtime | `assets/js/animations.js` | Theme | Shared â€” KHÃ”NG tÃ¡ch per-block |
| Patterns | `patterns/*.php` | Theme | Block patterns |
| Plugin root | `skvn-marine-blocks/` | Plugin | skvn-marine-blocks.php |
| Slider block | `src/slider/` + `src/slide/` | Plugin | Swiper frontend, stacked editor preview |
| Accordion block | `src/accordion/` | Plugin | Keyboard nav báº¯t buá»™c |
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

---

[manual] Architecture Decisions â€” Project-Wide

**A1. Child theme, khÃ´ng fork GeneratePress**
LÃ½ do: giáº£m PHP maintenance, trÃ¡nh AI sá»­a parent theme, dá»… upstream update.
Invariant: KHÃ”NG BAO GIá»œ sá»­a file trong `themes/generatepress/`.

**A2. Theme vs Plugin boundary**
Rule: náº¿u thay theme mÃ  feature bá»‹ máº¥t â†’ feature thuá»™c plugin, khÃ´ng thuá»™c theme.
Visual/layout â†’ theme. Logic/data â†’ plugin.
Invariant: custom blocks KHÃ”NG Ä‘Æ°á»£c Ä‘áº·t trong theme.

**A3. KhÃ´ng custom CPT cho product á»Ÿ V1**
DÃ¹ng WooCommerce native products. Custom fields (ACF/Meta Box) chá»‰ thÃªm khi WC attributes khÃ´ng Ä‘á»§.

**A4. Quote form stack cá»‘ Ä‘á»‹nh á»Ÿ V1**
CF7 â†’ CFDB7 â†’ n8n. KHÃ”NG custom-code form handler. KHÃ”NG popup/modal lÃ m primary flow.
URL: `/request-a-quote/?product_id=123` â†’ `/quote-thank-you/`

**A5. Animation runtime dÃ¹ng chung**
`assets/js/animations.js` lÃ  single runtime. KHÃ”NG táº¡o animation logic riÃªng per block trá»« khi báº¯t buá»™c.
Invariant: táº¥t cáº£ animation pháº£i respect `prefers-reduced-motion`.

**A6. Editor opacity rule**
Reveal animations KHÃ”NG Ä‘Æ°á»£c set `opacity: 0` trong editor náº¿u khÃ´ng cÃ³ safe fallback.
Editor pháº£i match frontend ~80%. Animations cÃ³ thá»ƒ cháº¡y trong editor nhÆ°ng KHÃ”NG lÃ m editing painful.

**A7. Image ALT automation**
Auto-fill tá»« attachment title khi ALT empty. KHÃ”NG overwrite manual ALT. KHÃ”NG auto-generate caption á»Ÿ V1.
File: `inc/media.php`, prefix: `skvn_marine_auto_set_image_alt_from_title()`.

**A8. Dependency policy**
Má»—i dependency má»›i PHáº¢I document: purpose, alternative, bundle impact, load scope, removal plan.
KHÃ”NG add dependency khÃ´ng cÃ³ rationale.

---

[manual] Invariants â€” KhÃ´ng bao giá» Ä‘Æ°á»£c vi pháº¡m

- KHÃ”NG sá»­a `themes/generatepress/` (báº¥t ká»³ file nÃ o)
- KHÃ”NG Ä‘áº·t custom block logic trong theme
- KHÃ”NG rename namespace `skvn-marine`, prefix `skvn_marine_` / `skvn_marine_blocks_`, CSS prefix `skvn-`
- KHÃ”NG overwrite manual image ALT
- KHÃ”NG auto-generate caption á»Ÿ V1
- KHÃ”NG custom-code quote form handler â€” dÃ¹ng CF7 + CFDB7 + n8n
- KHÃ”NG expose n8n webhook unprotected (cáº§n hard-to-guess URL + optional shared secret)
- KHÃ”NG log credential dÃ¹ debug
- PHP: input pháº£i sanitize, output pháº£i escape (`esc_html`, `esc_attr`, `esc_url`, `wp_kses_post`)
- KHÃ”NG cache: `/cart/`, `/checkout/`, `/my-account/`, `/request-a-quote/`, `/quote-thank-you/`
- KHÃ”NG modify hÆ¡n 3â€“5 files per AI task trá»« khi cÃ³ lÃ½ do rÃµ rÃ ng

---

[manual] Phase Scope

**V1 (current)** â€” Má»™t website B2B marine, local-first
- Theme child + design system + block styles + patterns
- Plugin blocks: Slider, Accordion, Product Grid, Product List
- Quote flow: CF7 + CFDB7 + n8n
- English content, prepare cho multilingual nhÆ°ng KHÃ”NG activate Polylang

**V2 (future)**
- Staging + Git deploy workflow
- Redis object cache náº¿u cáº§n
- CDN cho static assets
- Advanced filtering cho product
- Technical Product Card vá»›i specs table

**V3 (future)**
- ÄÃ¡nh giÃ¡ láº¡i: tiáº¿p tá»¥c GeneratePress base hay custom base theme
- Child theme support
- Marketing governance layer
- GitHub Actions release zip

---

[manual] Open Decisions (chÆ°a resolve â€” xem TENSIONS_OPEN.md)

1. V1 product grid/list: WooCommerce native blocks trÆ°á»›c hay custom blocks ngay?
2. CF7 â†” n8n integration method chÃ­nh xÃ¡c
3. Polylang: activate V1 hay chá»‰ prepare?
4. Slider editor UX: stacked / selected-slide-preview / carousel preview?
5. CF7 spam layer: Turnstile ngay V1 hay delay?
6. V2 hosting/deployment approach cá»¥ thá»ƒ
7. V3: stay GeneratePress hay custom base theme?

---

[manual] AI Guardrails â€” Project-Wide

Má»i AI agent lÃ m viá»‡c trong project nÃ y PHáº¢I Ä‘á»c `AGENTS.md` trÆ°á»›c.
Xem thÃªm: `.context/TENSIONS_OPEN.md` trÆ°á»›c khi modify báº¥t ká»³ module nÃ o.

Nhá»¯ng gÃ¬ AI KHÃ”NG Ä‘Æ°á»£c lÃ m (summary nhanh):
- Sá»­a GeneratePress parent
- ThÃªm dependency khÃ´ng cÃ³ rationale
- Rename namespace/prefix
- Äáº·t block logic trong theme
- Overwrite manual image ALT
- Custom-code quote form handler
- Nếu thấy conflict → ghi vào TENSIONS_OPEN.md, DỪNG, hỏi lại
<!-- MANUAL_END -->


