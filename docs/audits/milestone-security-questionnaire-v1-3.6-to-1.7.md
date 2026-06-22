# Milestone Security Questionnaire — V1.3.6 to 1.7.0

Status: **OPEN** — awaiting human answers  
Created: 2026-06-19  
Scope: Current milestone **V1 / 1.3.6** through **V1 / 1.7.0**, plus **1.x `skvn_element` CPT** planning; **2.0.0** boundary questions only (no full 2.0 scope).  
Sources: `.context/MILESTONES.md`, `docs/standards/security-guidelines.md`, theme/plugin PHP review.

Use this file as wiki source: code review findings, security blind spots, and fill-in answer tables per milestone.

---

## Milestone map (pre-2.0.0)

| Version | Name | Status |
|---|---|---|
| **1.3.6** | Block Editor UX, Slider Parallax & Single Post Fix | **IN PROGRESS** (current) |
| 1.1.2 | Product Quote Flow & Map Block Testing | PENDING (debt) |
| 1.3.4 | Core Control Foundation & Core Button Hover | PENDING |
| 1.3.5 | Post, Product & Archive Page Improvements | PLANNING |
| 1.3.7 | Collection Block UI & Card Styles | BRAINSTORM |
| 1.3.9 | Slider Dynamic Rendering & Controls Onsite QA | PENDING |
| 1.3.10 | SKVN Team Credits Easter Egg | PENDING |
| 1.4.0 | SKVN Theme Init Setup UI | PENDING |
| 1.4.1 | Layout Blocks Validation & Quote Evaluation | PENDING |
| 1.5.0 | `woo-catalog` + Fullscreen Step Slider | BRAINSTORM |
| 1.6.0 | SKVN Surface Presets | PENDING |
| 1.7.0 | Front page trang Chuyển đổi số | PENDING |
| 1.x | `skvn_element` CPT foundation | PLANNING |
| 2.0.0 | GeneratePress migration start | Boundary only |

---

## Part A — Code review (2026-06-19)

### A.1 Strengths

| ID | Finding | Reference |
|---|---|---|
| S-OK-1 | `source_url` uses WordPress routing, not `HTTP_HOST` | `wp-content/plugins/skvn-marine-blocks/modules/collection-render/cards.php` (audit T-2); `docs/standards/security-guidelines.md` |
| S-OK-2 | Typography: plugin inactive → theme defaults only (C1) | `wp-content/themes/skvn-marine/inc/typography.php` — `skvn_marine_get_typography()` |
| S-OK-3 | Feature Showcase: PHP `render_callback`, allowlists, `imageId` | `modules/feature-showcase-render/feature-showcase-render.php` |
| S-OK-4 | Slider shell: attribute normalization in PHP | `modules/slider-render/slider-render.php` |
| S-OK-5 | Settings API: `manage_options`, sanitize callbacks | typography / header / footer / core-control modules |
| S-OK-6 | Page display meta: `auth_callback` | `wp-content/themes/skvn-marine/inc/page-display-controls.php` |
| S-OK-7 | Core Button hover: `sanitize_hex_color` at render | `modules/core-control/features/button-hover.php` |
| S-OK-8 | Dev tools: path containment | `tools/build-deploy-artifact.mjs`, `tools/layout-translator/translate-layout.mjs` (audit D-1) |

### A.2 Risks and blind spots

| ID | Severity | Finding | Reference |
|---|---|---|---|
| S-RISK-1 | **High** | CF7 hidden fields use `default:get` — client can forge `product_id`, `source_url`, UTM before submit; no documented server-side validation | `tools/setup-quote-flow-070.php`; milestone 1.1.2 debt |
| S-RISK-2 | **Medium** | Slide block still **static save** with raw `src={backgroundImageUrl}` — no PHP gate like Feature Showcase | `wp-content/plugins/skvn-marine-blocks/src/slide/save.tsx` |
| S-RISK-3 | **Medium** | Accordion / Card / Card-grid: classes from attributes in `save.tsx`, no PHP `render_callback` allowlist | `src/accordion/save.tsx`, `src/card/save.tsx`, `src/card-grid/save.tsx` |
| S-RISK-4 | **Medium** | Collection: `imageRatio`, `responsivePreset`, `chipColorScheme` not fully allowlisted in PHP render | `modules/collection-render/product-collection.php`, `cards.php` |
| S-RISK-5 | **Medium** | `_skvn_spec_sheet_url` read from meta; `esc_url` on output only — no sanitize-at-read | `modules/collection-render/cards.php` |
| S-RISK-6 | **Medium** | Footer page: `apply_filters('the_content')` — trust model = who can edit the selected page? | `wp-content/themes/skvn-marine/inc/footer.php` |
| S-RISK-7 | **Low** | Header settings duplicated in theme + plugin (same option key, two sanitize paths) | `inc/header-actions.php` + `modules/header-settings/header-settings.php` |
| S-RISK-8 | **Low** | OPEN tension “header orphan” may be **stale** — theme already renders via `generate_after_header_content` | `.context/TENSIONS_OPEN.md` vs `inc/header-actions.php` |
| S-RISK-9 | **Info** | T-2 source fix landed; full quote chain **not verified onsite** | `.context/MILESTONES.md` 1.4.1; `security-guidelines.md` |
| S-RISK-10 | **Future High** | `woo-catalog` `show_in_rest` not decided — MOQ/spec exposure via REST? | `.context/planning/027_VERSION_1_5_0_WOO_CATALOG_PLUGIN_PLANNING.md` |

---

## Part B — Cross-cutting questions (answer once)

| ID | Question | Why it matters | Evidence | Your answer |
|---|---|---|---|---|
| X-01 | Who has `edit_pages`, `edit_products`, `manage_options` on production? Can marketing edit footer page / product meta? | Footer + product meta = compromise surface | `footer.php`, `cards.php` | |
| X-02 | Will CF7 **validate server-side** that `product_id` exists and is published before mail/CFDB7? | Forged lead data (S-RISK-1) | CF7 `default:get` in setup script | |
| X-03 | CFDB7 retention, access, export — GDPR/consent for email, company, message? | PII in DB; 1.1.2 not closed | `docs/decisions/quote-flow.md` | |
| X-04 | Spam: honeypot only, or **Turnstile** before launch? CF7 rate limit? | `security-guidelines.md` spam table | | |
| X-05 | Full-page cache excludes `/request-a-quote/` when URL has `?product_id=`? | Stale quote context across users | `docs/decisions/caching-strategy.md` | |
| X-06 | Spec sheet / PDF URLs: same-site attachments only, or external HTTPS allowed? | Open redirect / malicious links | `cards.php` PDF link | |
| X-07 | Google Fonts (1.3.5 / 1.6.0): acceptable on production (EU/VN privacy)? When is self-host mandatory? | Third-party requests, CSP | `inc/customizer.php` | |
| X-08 | Polylang: prepare-only V1 or activate before 2.0.0? Quote URLs per locale? | OPEN multilingual tension | `.context/TENSIONS_OPEN.md` | |
| X-09 | n8n: stay deferred to V2, or pilot after 1.1.2? Webhook auth model? | `security-guidelines.md` n8n section | | |
| X-10 | OSM iframe map: CSP `frame-src` / sandbox policy? | Clickjacking if embed URL is editor-controlled | `GLOBAL.md` A9 | |
| X-11 | Production: who can upload plugins/themes? Deploy zip integrity? | Supply chain | `docs/workflows/deploy-artifact-workflow.md` | |
| X-12 | Editor role: block `unfiltered_html` / custom HTML on marketing pages? | Stored XSS | WP capabilities | |
| X-13 | B2B search (`skvn_search_target`): stricter rate limit or lower `posts_per_page` cap? | Query cost / enumeration | `inc/header-actions.php`, `search.php` | |
| X-14 | When plugin inactive, should theme **always** use defaults (C1) for all options — including footer? | Inconsistent getter pattern | `footer.php` vs `typography.php` | |

---

## Part C — Per-milestone questions

### C1. V1 / 1.3.6 — Block Editor UX, Parallax, Single Post

| ID | Question | Blind spot | Evidence | Your answer |
|---|---|---|---|---|
| 1.3.6-01 | Parallax: GPU/memory limits on low-end mobile? Disable below 768px? | Availability; 1.3.0 memory QA debt | `MILESTONES.md` 1.3.9 | |
| 1.3.6-02 | Inspector refactor: migration for existing attributes without invalidating blocks? | Data loss | 1.3.6 acceptance | |
| 1.3.6-03 | Single post hero full-width: audited for `100vw` / `overflow-x` per layout contract? | Layout safety | `css-layout-safety-contract.md` | |
| 1.3.6-04 | Slider-only Inter vs site typography — acceptable font request leak? | OPEN slider/Inter tension | `TENSIONS_OPEN.md` | |
| 1.3.6-05 | Migrate Slide to dynamic render (`imageId` only) in 1.3.6 or defer to 1.3.9? | S-RISK-2 | `src/slide/save.tsx` | |

### C2. V1 / 1.1.2 — Quote Flow & Map (debt)

| ID | Question | Blind spot | Evidence | Your answer |
|---|---|---|---|---|
| 1.1.2-01 | Which CFDB7 hidden fields are **required** non-empty? Can `product_id` be empty for generic quote? | Business vs integrity | `quote-flow.md` | |
| 1.1.2-02 | Thank-you redirect: leak PII in URL query or Referer? | Privacy | CF7 redirect config (onsite) | |
| 1.1.2-03 | Draft/private products: should quote CTA still append `product_id`? | IDOR / disclosure | `cards.php` quote URL | |
| 1.1.2-04 | Map not viewable onsite — CSP, mixed content, or GP wrapper? Who owns fix? | Launch blocker | `docs/testing/onsite-map-block-1.1.2.md` | |
| 1.1.2-05 | CF7 mail: SMTP rotation; do mail logs store full submissions? | Credential / PII leak | WP mail config (onsite) | |

### C3. V1 / 1.3.4 — Core Control

| ID | Question | Blind spot | Evidence | Your answer |
|---|---|---|---|---|
| 1.3.4-01 | Block Copy/Paste: allow paste from external sites / hostile clipboard? Same-site only? | Arbitrary block tree import | OPEN editor-governance tension | |
| 1.3.4-02 | `window.skvnCoreControls` inline inject — sufficient hardening? | Flag tampering in editor | `core-control.php` | |
| 1.3.4-03 | Button hover attrs: can contributors set attrs via REST without cap check? | Capability model | `button-hover.php` | |
| 1.3.4-04 | Core Control menu: `manage_options` only, or also `edit_theme_options`? | Role separation | `security-guidelines.md` Roles | |

### C4. V1 / 1.3.5 — Post / Product / Archive

| ID | Question | Blind spot | Evidence | Your answer |
|---|---|---|---|---|
| 1.3.5-01 | Default Instrument Serif from Google CDN — fallback if Google blocked? | B2B networks | `inc/customizer.php` | |
| 1.3.5-02 | Hide reviews/comments: CSS only or remove hooks/schema too? | SEO spam surface | Woo templates | |
| 1.3.5-03 | Trust signals (HACCP, VSATTP): hardcoded — legal accuracy ownership? | Compliance | Style C artifacts | |
| 1.3.5-04 | Customizer font preset: sufficient allowlist if `theme_mod` tampered in DB? | Poisoned DB | `customizer.php` | |

### C5. V1 / 1.3.7 — Collection UI

| ID | Question | Blind spot | Evidence | Your answer |
|---|---|---|---|---|
| 1.3.7-01 | `catalogCtaUrl` external PDF — virus scan / WP attachment only? | Malware distribution | 1.3.7 planning | |
| 1.3.7-02 | `archiveUrl`: same-site only or off-site allowed? | Open redirect / branding | `product-collection.php` | |
| 1.3.7-03 | Hover prefetch on product cards — referrer/privacy impact? | Perf + privacy | 1.3.7 planning | |
| 1.3.7-04 | Fixing `imageRatio` bug — PHP allowlist `1-1`, `3-2`, `16-9`? | S-RISK-4 | `MILESTONES.md` 1.3.7 bugs | |
| 1.3.7-05 | Card style variants: max user-controlled classes via attributes? | CSS injection surface | `card/save.tsx` | |

### C6. V1 / 1.3.9 — Onsite QA

| ID | Question | Blind spot | Evidence | Your answer |
|---|---|---|---|---|
| 1.3.9-01 | Memory leak threshold after N slider interactions? | Tab crash / DoS local | `memory-leak-prevention-rules.md` | |
| 1.3.9-02 | Woo inactive message visible to guests — acceptable? | Info disclosure | `product-collection.php` | |
| 1.3.9-03 | QA includes T-2 quote `source_url` regression, or only 1.4.1? | Test ownership | 1.4.1 T-2 | |

### C7. V1 / 1.3.10 — Easter Egg

| ID | Question | Blind spot | Evidence | Your answer |
|---|---|---|---|---|
| 1.3.10-01 | `sessionStorage` click counter — abuse to spam dialog (admin-only)? | Low; a11y | `skvn-team-credits-easter-egg` decision | |
| 1.3.10-02 | Employee names in credits — OK in public plugin zip / repo? | HR privacy | 1.3.10 constraints | |

### C8. V1 / 1.4.0 — Theme Init Setup UI

| ID | Question | Blind spot | Evidence | Your answer |
|---|---|---|---|---|
| 1.4.0-01 | “Load quote workflow”: preview/diff before overwriting onsite CF7? | Highest admin blast radius | 1.4.0 constraints | |
| 1.4.0-02 | Idempotent: second run creates duplicate pages/forms? | SEO dup / orphans | 1.4.0 acceptance | |
| 1.4.0-03 | Capability: `manage_options` only? | Privilege path | | |
| 1.4.0-04 | Audit log (who/when ran setup)? | Incident forensics | Not in scope yet | |
| 1.4.0-05 | Disable `wp eval-file tools/setup-quote-flow-070.php` on production? | CLI full DB write | `tools/setup-quote-flow-070.php` | |

### C9. V1 / 1.4.1 — Layout Blocks & T-2

| ID | Question | Blind spot | Evidence | Your answer |
|---|---|---|---|---|
| 1.4.1-01 | After card-grid/card validation, is new `skvn-marine/quote` block justified? | Scope creep | layout blocks planning | |
| 1.4.1-02 | T-2 pass: `source_url` host must equal `home_url()` only, or allow staging subdomain? | Multi-env | `security-guidelines.md` | |
| 1.4.1-03 | Card `data-skvn-motion-*` — meaningful no-JS security concern? | Low | `card/save.tsx` | |

### C10. V1 / 1.5.0 — `woo-catalog` & Fullscreen Step Slider

| ID | Question | Blind spot | Evidence | Your answer |
|---|---|---|---|---|
| 1.5.0-01 | Who may activate/deactivate `woo-catalog` on production? | Second attack surface | planning 027 | |
| 1.5.0-02 | `product_certification` public rewrite `/certification/` — intentional indexed URLs? | SEO spam | planning taxonomy | |
| 1.5.0-03 | Product meta `show_in_rest`: public read, authenticated, or off? | REST enumeration | S-RISK-10 | |
| 1.5.0-04 | `woo_catalog_get_fields()` — documented filter hook security contract? | Extension abuse | planning PHP API | |
| 1.5.0-05 | Catalog `addFilter` Inspector — conflict with other editor plugins? | XSS via rogue plugin | architecture split | |
| 1.5.0-06 | Step slider: if editor embeds YouTube iframe — oEmbed/CSP policy? | Third-party embed | fullscreen-step-slider decision | |
| 1.5.0-07 | Migration free-text → structured meta — mandatory DB backup step? | Data loss | planning | |

### C11. V1 / 1.6.0 — Surface Presets

| ID | Question | Blind spot | Evidence | Your answer |
|---|---|---|---|---|
| 1.6.0-01 | Glass `backdrop-filter` fallback — content beneath readable when blur unsupported? | Readability / leakage | planning 009 | |
| 1.6.0-02 | Self-hosted fonts: license files in repo; subsetting for privacy? | Legal + fingerprinting | 1.6.0 planning | |
| 1.6.0-03 | Font delivery admin UI — ban arbitrary URLs (preset CDN/local paths only)? | SSRF if server fetches URL | 1.6.0 planning | |

### C12. V1 / 1.7.0 — Front page Chuyển đổi số

| ID | Question | Blind spot | Evidence | Your answer |
|---|---|---|---|---|
| 1.7.0-01 | External taxonomy plugin inactive — empty state leak plugin name? | Reconnaissance | planning 010 | |
| 1.7.0-02 | Resource list: `publish` only, or `private` for logged-in users? | Access control | front-page IA | |
| 1.7.0-03 | Badges `locked` / `internal` / `free` — who assigns; spoof risk? | Trust UX | planning badges | |
| 1.7.0-04 | Whole-site search hook boundary — SKVN never owns SQL/query? | Injection boundary | planning 010 | |

### C13. V1.x — `skvn_element` CPT (pre-2.0.0)

| ID | Question | Blind spot | Evidence | Your answer |
|---|---|---|---|---|
| 1.x-01 | `show_in_rest: true` on non-public CPT — REST exposes draft header/footer? | Editor vs public leak | planning 014 | |
| 1.x-02 | Rank Math sitemap: exclude `skvn_element` + current footer **Page** bridge? | Duplicate indexed URLs | planning SEO section | |
| 1.x-03 | Future `_skvn_element_display_rules` — sandboxed eval, no arbitrary PHP? | RCE if rules are code | planning 014 | |
| 1.x-04 | Migrate footer Page → Element: 301 old footer page URLs? | Orphan URLs | planning 014 | |

### C14. 2.0.0 boundary (prep only)

| ID | Question | Blind spot | Evidence | Your answer |
|---|---|---|---|---|
| 2.0-01 | GP exit: staging + rollback before removing `generate_*` hooks? | Downtime | planning 013 | |
| 2.0-02 | Git deploy V2: where do CI/secrets live; never in artifact zip? | Credential leak | `GLOBAL.md` V2 | |

---

## Part D — Priority 15 (answer first if time-limited)

| Priority | ID | Topic |
|---|---|---|
| 1 | X-01 | Production role model |
| 2 | X-02 | CF7 server-side validation |
| 3 | X-03 | CFDB7 / PII retention |
| 4 | X-05 | Cache + quote query params |
| 5 | 1.1.2-02 | Thank-you redirect PII |
| 6 | 1.3.6-05 | Slide dynamic render timeline |
| 7 | 1.4.0-01 | Setup UI overwrite guard |
| 8 | 1.4.1-02 | T-2 pass criteria (multi-env) |
| 9 | 1.5.0-03 | REST exposure for catalog meta |
| 10 | 1.5.0-02 | Certification taxonomy public URLs |
| 11 | X-07 | Google Fonts policy |
| 12 | X-09 | n8n timing + auth |
| 13 | 1.x-02 | Sitemap footer page leak |
| 14 | S-RISK-1 | Acceptable forged `product_id`? |
| 15 | 1.3.7-01 | External PDF policy |

---

## Part E — Suggested hardening (can start before all answers)

| Priority | Action | Suggested milestone |
|---|---|---|
| P0 | Run 1.1.2 onsite + verify T-2 quote chain end-to-end | 1.1.2 / 1.4.1 |
| P1 | CF7 server-side validate `product_id` (hook or documented CF7 extension) | 1.1.2 |
| P1 | Slide → `backgroundImageId` + PHP render (Feature Showcase pattern) | 1.3.6 or 1.3.9 |
| P2 | PHP allowlist `imageRatio`, `responsivePreset`, `chipColorScheme` | 1.3.7 |
| P2 | `_skvn_spec_sheet_url`: `esc_url_raw` at read | 1.3.7 / 1.5.0 |
| P2 | Close or update stale header orphan tension | context/docs |
| P3 | Theme footer: delegate to plugin getters when active | 1.4.x |

---

## Answer format (for wiki / agent handoff)

```text
X-01: [roles on production]
X-02: [yes/no + validation approach]
1.5.0-03: [public REST | auth only | off]
...
```

When complete, move summary decisions to `docs/decisions/` and set this file Status to **CLOSED** with date.

---

## Related documents

- `docs/standards/security-guidelines.md`
- `docs/decisions/quote-flow.md`
- `docs/decisions/caching-strategy.md`
- `.context/MILESTONES.md`
- `.context/TENSIONS_OPEN.md`