# SKVN Marine — Typography Scope Isolation & Vietnamese Font Loading

**Phiên bản:** 1.0  
**Milestone target:** V1 / 1.3.6 (hardening follow-up)  
**Ngày tạo:** 2026-06-19  
**Status:** IMPLEMENTED — 2026-06-19  
**Decision doc:** `docs/decisions/typography-scope-and-font-loading.md`  
**Liên quan:** `docs/decisions/typography-and-gp-exit-report.md`, `docs/decisions/skvn-1.3.x-architecture-and-decisions.md` §5

---

## 1. Vấn đề

Typography settings (palette + heading scale + font preset) đang ảnh hưởng **wp-admin chrome** (sidebar, menu, toolbar), không chỉ frontend và block editor canvas.

Triệu chứng quan sát được:

- Đổi font preset trong Customizer → font wp-admin đổi theo.
- Đổi palette/heading trong SKVN Marine → Typography → màu/size token có thể leak qua `:root` trong editor admin page.

**Mục tiêu sau fix:**

| Surface | Typography áp dụng? |
|---|---|
| Public frontend (`body:not(.wp-admin)`) | Có — full tokens + Google Fonts |
| Block editor canvas (`.editor-styles-wrapper`) | Có — preview khớp frontend |
| wp-admin chrome (menu, list tables, plugin settings pages) | **Không** — giữ WP admin UI mặc định |

Đồng thời: Google Fonts preset phải **luôn load glyph tiếng Việt** qua CSS2 API (bỏ `subset=vietnamese` legacy).

---

## 2. Root cause (đã audit)

### 2.1 Selector quá rộng

| File | Hook | Selector hiện tại | Vấn đề |
|---|---|---|---|
| `inc/customizer.php` | `enqueue_block_editor_assets` | `:root`, `h1,h2,h3` | Inject lên document wp-admin cha |
| `inc/typography.php` | `enqueue_block_editor_assets` | `:root` | Palette vars leak admin document |
| `style.css` | `add_editor_style('style.css')` | `body, .editor-styles-wrapper` | `body` bare → toàn trang admin |

### 2.2 Hai lớp typography khác nhau (dễ nhầm)

| Lớp | Owner | UI | Output |
|---|---|---|---|
| **A — Runtime tokens** | Plugin admin + theme inject | SKVN Marine → Typography | `--skvn-color-*`, `--skvn-h*-size/weight` |
| **B — Font preset** | Theme Customizer | Appearance → Customize → Typography (SKVN) | `--skvn-font-heading/body`, Google Fonts |

Plugin **không** đổi font family. Font leak chủ yếu từ lớp B + selector `body`/`:root`.

### 2.3 Google Fonts URL sai API

Preset trong `inc/customizer.php` dùng `&subset=vietnamese` — param **API v1**, CSS2 **bỏ qua**. Cần chuẩn hóa URL và ghi contract trong decision doc.

### 2.4 Editor thiếu Google Fonts enqueue

`skvn_marine_enqueue_font_preset_editor()` chỉ inject CSS variables, **không** `wp_enqueue_style` Google Fonts → editor preview có thể fallback system font dù frontend đúng.

---

## 3. Quyết định đã chốt

| # | Quyết định |
|---|---|
| D1 | **Không** dùng `:root` hoặc bare `body` cho theme typography trên bất kỳ hook nào chạy trong wp-admin context |
| D2 | Frontend scope: `body:not(.wp-admin)` — đủ vì public site không có class `wp-admin` |
| D3 | Editor canvas scope: `.editor-styles-wrapper` — token vars + heading `font-family` chỉ trong wrapper này |
| D4 | wp-admin chrome: **zero** theme font/color token injection — không hook `admin_enqueue_scripts` cho typography |
| D5 | Google Fonts: chỉ enqueue trên frontend (`wp_enqueue_scripts`) và editor (`enqueue_block_editor_assets`), **không** trên general admin pages |
| D6 | Vietnamese: dùng CSS2 URL với font đã support Vietnamese; bỏ `subset=vietnamese`; optional `text=` param cho tối ưu payload (document, không bắt buộc V1) |
| D7 | Tách helper `skvn_marine_build_typography_scope_css( $context )` — `$context` = `frontend` \| `editor` — một nguồn truth cho CSS builder |
| D8 | Plugin `typography-settings.php` **không đổi** admin UI; chỉ theme `inc/typography.php` đổi scope inject |
| D9 | `theme.json` giữ nguyên preset slugs; vars resolve trong scoped wrapper, không cần đổi slug |

---

## 4. Ownership matrix (ai sửa gì)

```
┌─────────────────────────────────────────────────────────────────────────┐
│ TYPOGRAPHY OWNERSHIP — sau khi implement 029                            │
├──────────────────────────┬──────────────────────────────────────────────┤
│ Plugin typography-settings│ Admin UI, sanitize, get_option skvn_typography│
│ (modules/typography-...)  │ Palette + heading scale ONLY                  │
│                           │ KHÔNG enqueue CSS, KHÔNG đổi font family      │
├──────────────────────────┼──────────────────────────────────────────────┤
│ Theme inc/typography.php  │ Đọc option → build scoped CSS → inject        │
│                           │ Frontend: body:not(.wp-admin)                 │
│                           │ Editor: .editor-styles-wrapper                │
├──────────────────────────┼──────────────────────────────────────────────┤
│ Theme inc/customizer.php  │ Font preset theme_mod, Google Fonts URL       │
│                           │ Scoped --skvn-font-* + heading font-family    │
│                           │ Helper build gfonts URL (Vietnamese contract) │
├──────────────────────────┼──────────────────────────────────────────────┤
│ Theme style.css           │ Baseline tokens trong :root (static defaults) │
│                           │ Apply font/color chỉ qua scoped selectors     │
├──────────────────────────┼──────────────────────────────────────────────┤
│ Theme theme.json          │ Preset fontFamilies reference --skvn-font-*   │
│                           │ Global styles apply trong editor canvas only  │
└──────────────────────────┴──────────────────────────────────────────────┘
```

**Rule cho agent/human sau này:** Nếu task đụng font → sửa `customizer.php` + `style.css`. Nếu task đụng màu/heading scale admin → sửa plugin + `typography.php`. Không gộp hai lớp vào một file.

---

## 5. Implementation plan

### Phase 1 — Shared scope helper (theme)

**File:** `inc/typography.php`, `inc/customizer.php` (hoặc `inc/typography-scope.php` nếu muốn tách — ưu tiên inline helper trong từng file để giữ ≤5 files)

Thêm helper:

```php
/**
 * @param string $context 'frontend' | 'editor'
 * @return string CSS selector root for custom properties.
 */
function skvn_marine_typography_scope_selector( string $context ): string {
    return 'editor' === $context
        ? '.editor-styles-wrapper'
        : 'body:not(.wp-admin)';
}
```

Palette CSS builder đổi từ `:root {` → `{scope} {`.

Heading font rule đổi từ `h1,h2,h3` → `{scope} h1, {scope} h2, ...` (editor) hoặc `body:not(.wp-admin) h1, ...` (frontend).

### Phase 2 — `inc/customizer.php`

1. Sửa `skvn_marine_font_presets()` — URL Google Fonts CSS2 (xem decision doc §3).
2. `skvn_marine_enqueue_font_preset()` — scope `body:not(.wp-admin)`.
3. `skvn_marine_enqueue_font_preset_editor()` — scope `.editor-styles-wrapper` + **thêm** `wp_enqueue_style` Google Fonts (cùng URL helper).
4. Heading selector: prefix scope vào `skvn_marine_heading_selector()` output khi build CSS.

### Phase 3 — `inc/typography.php`

1. `skvn_marine_build_typography_css( $context = 'frontend' )` — nhận context, dùng scope selector.
2. `skvn_marine_inject_typography_css()` — gọi với `frontend`.
3. `skvn_marine_inject_typography_css_editor()` — gọi với `editor`.

### Phase 4 — `style.css`

1. Tách block:

```css
/* Trước — XÓA */
body,
.editor-styles-wrapper { font-family: ... }

/* Sau */
body:not(.wp-admin) { font-family: var(--skvn-font-body); }
.editor-styles-wrapper { font-family: var(--skvn-font-body); }
```

2. Audit các rule `body` bare khác có ảnh hưởng admin (color, padding editor-only giữ nguyên nếu đã scoped).

3. Giữ `:root` cho **static baseline defaults** — acceptable vì vars chỉ được *consume* bởi scoped selectors; không set `font-family` trực tiếp trên `:root` cho admin.

### Phase 5 — Docs sync (cùng PR)

- `docs/decisions/typography-scope-and-font-loading.md` — source of truth mới
- Update `typography-and-gp-exit-report.md` §4 registry + link
- Update `skvn-1.3.x-architecture-and-decisions.md` §5.1 diagram
- Update `.context/modules/THEME_SKVN_MARINE.md` [manual] Typography Scope

### Phase 6 — Test

Tạo `docs/testing/typography-scope-isolation-1.3.6.md` với checklist onsite/local.

---

## 6. Files allowed to change

| File | Change |
|---|---|
| `wp-content/themes/skvn-marine/inc/customizer.php` | Scope + gfonts URL + editor font enqueue |
| `wp-content/themes/skvn-marine/inc/typography.php` | Scoped palette/heading inject |
| `wp-content/themes/skvn-marine/style.css` | Tách `body` vs `.editor-styles-wrapper` |
| `docs/decisions/typography-scope-and-font-loading.md` | **NEW** |
| `docs/decisions/typography-and-gp-exit-report.md` | Appendix link |
| `docs/decisions/skvn-1.3.x-architecture-and-decisions.md` | §5 update |
| `docs/testing/typography-scope-isolation-1.3.6.md` | **NEW** |
| `.context/modules/THEME_SKVN_MARINE.md` | [manual] invariant |
| `.context/planning/029_VER_1_3_6_TYPOGRAPHY_SCOPE_ISOLATION_PLANNING.md` | This file — mark IMPLEMENTED when done |

**Forbidden:** `themes/generatepress/**`, plugin `typography-settings.php` (unless bug found — hiện không cần).

---

## 7. Acceptance checklist

- [ ] `/wp-admin/` Dashboard — font WP admin mặc định, không Instrument Serif / Barlow
- [ ] `/wp-admin/post.php?post=…&action=edit` — admin chrome default; **canvas** dùng SKVN font
- [ ] SKVN Marine → Typography settings page — admin UI không đổi font
- [ ] Public homepage + single post — SKVN font preset vẫn đúng
- [ ] Tiếng Việt có dấu render đúng trên frontend (ă â đ ê ô ơ ư)
- [ ] Block editor preview khớp frontend font (Google Fonts load trong editor)
- [ ] Palette override từ plugin vẫn apply frontend + editor canvas
- [x] `php -l` theme PHP ok
- [x] Không có `:root` font-family injection từ PHP hooks
- [x] Decision doc + module [manual] updated

---

## 8. Google Fonts URL — target state

| Preset | URL (CSS2, Vietnamese-capable) |
|---|---|
| instrument | `https://fonts.googleapis.com/css2?family=Instrument+Serif&display=swap` |
| lora-inter | `https://fonts.googleapis.com/css2?family=Lora:wght@400;600&family=Inter:wght@400;500&display=swap` |
| barlow | `https://fonts.googleapis.com/css2?family=Barlow:wght@400;600;700&display=swap` |

Chi tiết rationale và fallback `text=` param: xem decision doc.

---

## 9. Risk & non-goals

**Risk thấp:**

- `body:not(.wp-admin)` không match login page (`body.login`) — login giữ WP default, đúng intent.
- Site Editor / FSE: ngoài scope V1 GeneratePress child; ghi Future Candidate.

**Non-goals (không làm trong 029):**

- Di chuyển font preset từ Customizer sang plugin admin
- Self-host WOFF2 subset
- Đổi font preset list
- Sửa `theme.json` font family slugs

---

## 10. Implement trigger

Human nói **"làm 029"** hoặc **"implement typography scope"** → agent route `CODE_NOW`, implement Phase 1–6, mark planning Status → IMPLEMENTED.