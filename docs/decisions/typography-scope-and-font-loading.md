# Typography Scope & Font Loading Contract

**Date:** 2026-06-19  
**Status:** APPROVED — planning in `.context/planning/archives/029_VER_1_3_6_TYPOGRAPHY_SCOPE_ISOLATION_PLANNING.md`  
**Milestone:** V1 / 1.3.6 hardening follow-up  
**Supersedes:** Partial guidance in `typography-and-gp-exit-report.md` §4 (selector scope only; palette/Settings API rules unchanged)

---

## 1. Tại sao cần document này

Typography SKVN Marine có **hai lớp độc lập** do hai entry point admin khác nhau. Không có contract scope, CSS inject qua `:root`/`body` sẽ đổi font cả wp-admin.

Document này là **source of truth** cho:

- Ai own UI vs ai own CSS output
- Selector scope cho từng WordPress surface
- Google Fonts Vietnamese loading contract

---

## 2. Hai lớp typography — không gộp

| Lớp | Tên | Admin UI | Option / setting | CSS output |
|---|---|---|---|---|
| **A** | Runtime tokens | SKVN Marine → **Typography** | `skvn_typography` (array) | `--skvn-color-primary/accent/surface/text`, `--skvn-h1..h4-size/weight` |
| **B** | Font preset | Appearance → Customize → **Typography (SKVN Marine)** | `theme_mod`: `skvn_font_preset`, `skvn_font_heading_scope` | `--skvn-font-heading`, `--skvn-font-body`, Google Fonts `<link>` |

### File ownership

| Concern | Plugin | Theme |
|---|---|---|
| Admin UI save | `modules/typography-settings/typography-settings.php` | `inc/customizer.php` |
| Sanitize at read | `skvn_marine_blocks_get_typography()` | `get_theme_mod()` + preset array |
| CSS delivery | — | `inc/typography.php` (lớp A), `inc/customizer.php` (lớp B) |
| Baseline defaults | `skvn_marine_blocks_get_default_typography()` | `style.css` `:root`, `theme.json` presets |

**Invariant:** Plugin không enqueue stylesheet. Theme không có Settings API form cho `skvn_typography`.

---

## 3. Surface scope contract

WordPress có ba surface liên quan typography:

```text
┌─────────────────────────────────────────────────────────────┐
│ wp-admin document (post editor screen)                      │
│  ├─ Admin chrome (#adminmenu, .wp-toolbar, .wrap)  → NO SKVN│
│  └─ Block editor canvas (.editor-styles-wrapper)   → SKVN   │
├─────────────────────────────────────────────────────────────┤
│ Public frontend (body:not(.wp-admin))                → SKVN   │
├─────────────────────────────────────────────────────────────┤
│ wp-admin list/settings (plugins, SKVN Typography)  → NO SKVN│
└─────────────────────────────────────────────────────────────┘
```

### Selector rules (bắt buộc)

| Surface | CSS scope selector | Hooks được phép |
|---|---|---|
| Frontend | `body:not(.wp-admin)` | `wp_enqueue_scripts` |
| Editor canvas | `.editor-styles-wrapper` | `enqueue_block_editor_assets`, `add_editor_style` |
| wp-admin chrome | *(không inject)* | — |

### Cấm

- `:root { --skvn-font-* }` từ PHP inline style trên `enqueue_block_editor_assets`
- `:root { --skvn-color-* }` từ PHP trên editor hook (dùng `.editor-styles-wrapper`)
- `body { font-family: ... }` không có `:not(.wp-admin)` trong stylesheet load vào admin
- `admin_enqueue_scripts` cho theme typography / Google Fonts

### Cho phép

- `style.css` `:root` chứa **static default** custom properties — miễn là `font-family` chỉ apply qua scoped selectors ở trên
- `theme.json` `fontFamilies` reference `var(--skvn-font-body)` — vars resolve trong scoped wrapper

---

## 4. CSS cascade (sau scope fix)

```text
theme.json presets
    ↓
style.css :root (static defaults only)
    ↓
body:not(.wp-admin) / .editor-styles-wrapper (runtime vars)
    ├─ inc/customizer.php priority 15 → --skvn-font-*
    └─ inc/typography.php priority 20   → --skvn-color-*, --skvn-h*-*
    ↓
Component rules (h1, .skvn-button, cards, …) consume vars inside scope
```

Priority giữ nguyên: font preset (15) trước palette override (20) nếu cùng property — hiện không overlap.

---

## 5. Google Fonts — Vietnamese contract

### 5.1 API version

Project dùng **Google Fonts CSS2 API** (`/css2?family=...`).

Param `subset=vietnamese` thuộc **API v1** — **không dùng**, bị ignore.

### 5.2 Preset URLs (governed)

| Preset key | CSS2 URL |
|---|---|
| `instrument` | `https://fonts.googleapis.com/css2?family=Instrument+Serif&display=swap` |
| `lora-inter` | `https://fonts.googleapis.com/css2?family=Lora:wght@400;600&family=Inter:wght@400;500&display=swap` |
| `barlow` | `https://fonts.googleapis.com/css2?family=Barlow:wght@400;600;700&display=swap` |
| `system` | `null` — không enqueue external font |

Tất cả font trên đều support Vietnamese (Latin Extended). Google trả `@font-face` với `unicode-range` phù hợp.

### 5.3 Enqueue rules

| Context | Enqueue Google Fonts? | Handle pattern |
|---|---|---|
| Frontend | Có, khi preset `gfonts !== null` | `skvn-gfonts-{preset_key}` via `wp_enqueue_scripts` |
| Block editor canvas | Có, cùng URL | `skvn-gfonts-{preset_key}-editor` via `enqueue_block_editor_assets` |
| wp-admin elsewhere | **Không** | — |

### 5.4 Optional payload optimization (Future Candidate)

Dynamic subset qua `&text=` với bảng ký tự Việt — chỉ khi cần giảm bandwidth; không bắt buộc V1.

Self-hosted WOFF2 subset — deferred V2+.

---

## 6. Heading scope (Customizer)

`skvn_font_heading_scope` giới hạn element nhận `font-family: var(--skvn-font-heading)`:

| Value | Elements |
|---|---|
| `h1` | `h1` only |
| `h1-h3` *(default)* | `h1, h2, h3` |
| `all` | `h1` – `h6` |

Sau scope fix, selector phải prefix:

- Frontend: `body:not(.wp-admin) h1, ...`
- Editor: `.editor-styles-wrapper h1, ...`

---

## 7. Khi nào sửa file nào

| Task | Sửa |
|---|---|
| Thêm màu palette slot | Plugin sanitize + admin field + theme `typography.php` builder + `style.css` semantic alias |
| Đổi heading scale default | Plugin defaults + theme defaults (sync) + optional `theme.json` fontSizes |
| Thêm font preset mới | `inc/customizer.php` preset array + gfonts URL + test Vietnamese |
| Font leak vào admin | Check scope selectors — **không** thêm `!important` trên admin |
| Editor preview khác frontend | Check editor hook có enqueue gfonts + scoped vars |

---

## 8. Verification

Onsite/local test: `docs/testing/typography-scope-isolation-1.3.6.md`

Pass criteria tóm tắt:

1. wp-admin Dashboard font = system admin UI
2. Post editor canvas = SKVN preset font
3. Frontend = SKVN preset font
4. Vietnamese diacritics render correctly

---

## 9. Related documents

| Document | Role |
|---|---|
| `typography-and-gp-exit-report.md` | Palette + Settings API + GP exit — **không** duplicate scope rules |
| `skvn-1.3.x-architecture-and-decisions.md` §5 | Font preset flow diagram |
| `brand-profile-theme-tokens.md` | Token naming `--skvn-font-*` |
| `.context/planning/archives/029_VER_1_3_6_TYPOGRAPHY_SCOPE_ISOLATION_PLANNING.md` | Implementation checklist |