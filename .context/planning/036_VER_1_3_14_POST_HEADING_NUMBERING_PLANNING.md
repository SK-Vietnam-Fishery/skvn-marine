# 036 — Post Heading Auto-Numbering (Core Control feature)

```
Version:   1.3.14
Date:      2026-06-28
Status:    PLANNED (chưa code)
Decision:  docs/decisions/post-heading-numbering-1.3.14.md
Home:      modules/core-control (feature post_heading_numbers + sub-option)
```

---

## Context

Tự đánh số heading h2–h5 trong thân bài blog, có lựa chọn depth (h3/h4/h5) + style (decimal `1/1.1/1.1.1` | mixed `I/1/a`). Thuần CSS counter. Đặt trong **Core Control** (user chốt) — mở rộng registry boolean tối thiểu để mang thêm sub-option, không sửa theme (scope `.single-post .entry-content`).

## Goal

Bật/tắt + chọn depth/style trong Core Control; frontend single post tự in CSS đánh số đúng kiểu.

## Phase 1 — Registry + sub-option + admin UI

**Files:**
- `modules/core-control/registry.php` — thêm entry `post_heading_numbers` (label/description/default false). [edit]
- `modules/core-control/core-control.php` — `register_setting('skvn_core_controls_group', 'skvn_heading_number', …)` + load `features/post-heading-numbers.php` khi enabled. [edit]
- `modules/core-control/admin-page.php` — sau bảng checkbox, render 2 select: depth (h3/h4/h5), style (decimal/mixed), name `skvn_heading_number[...]`. [edit]

**AC:** trang Core Control hiện checkbox + 2 select; lưu được; `get_option('skvn_heading_number')` đúng; feature cũ không đổi; `php -l` sạch. Chưa in CSS.

## Phase 2 — Adapter render CSS (frontend + editor preview + no-number)

**Files:**
- `modules/core-control/features/post-heading-numbers.php` (NEW) — sanitize (`depth` whitelist h3/h4/h5, `style` whitelist decimal/mixed), getter; build chuỗi CSS dùng chung rồi in cho:
  - **Frontend** `wp_head` khi `is_singular('post')` → scoped `.single-post .entry-content`.
  - **Editor preview** `enqueue_block_editor_assets` → scoped `.editor-styles-wrapper` (số hiện lúc soạn).
  - counter-reset/increment chung; `::before` theo style (decimal nối | mixed per-level) + gate theo depth.
  - **`.no-number`** skip rule (`:is(h2,h3,h4,h5).no-number` → `counter-increment:none` + `content:none`).

**AC:** bật feature → bài blog đánh số đúng kiểu + depth (cả frontend lẫn editor); heading có `.no-number` bị bỏ số, dãy không nhảy; trang/sản phẩm khác không bị; tắt feature → hết số; `php -l` sạch.

## Phase 3 — Block toggle (editor extension)

**Files:**
- `src/core-controls/heading-number/index.tsx` (NEW) — `addFilter('editor.BlockEdit')` thêm ToggleControl "Bỏ đánh số mục này" cho `core/heading`; ON/OFF thêm-gỡ `no-number` trong `attributes.className`; gate `isCoreControlEnabled('post_heading_numbers')`.
- Import vào `src/core-controls` (entry hiện có) + `npm run build`.

**AC:** bật feature → soạn heading thấy toggle; bật toggle → số mục đó biến mất (editor preview) + frontend cũng bỏ; tắt → số trở lại; build sạch.

## Defer
- Đồng bộ số sang TOC island.

## Tension
Adapter plugin in CSS nhắm class WP chuẩn (`.single-post .entry-content`), không phải class skvn → coupling thấp, không sửa theme.
