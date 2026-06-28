# 034 — Blog Sidebar Content Settings (bản tạm)

```
Version:   1.3.12
Date:      2026-06-26
Status:    IMPLEMENTED (2026-06-27, 3 phase — chờ Dev onsite test)
Decision:  docs/decisions/blog-sidebar-content-settings-1.3.12.md
Feature:   Single post sidebar — content editable qua plugin setting page
Ideation:  docs/ideations/blog-sidebar-content-settings.md
Scope:     BẢN TẠM — milestone đúng (1.4–1.6.x) refactor thành block-config + drag-drop
```

---

## Context

Sidebar single blog post ([single.php:151-226](../../wp-content/themes/skvn-marine/single.php#L151)) đang **hardcode** 3 island (CTA / related / categories), không có cách đổi nội dung. Cần một trang admin tạm để dev + marketing đổi text/màu/thứ tự ngay, trước khi theme độc lập GP và làm block-config thật.

Verify codebase:
- Convention chuẩn = [header-settings.php](../../wp-content/plugins/skvn-marine-blocks/modules/header-settings/header-settings.php) (Settings API + option array + `sanitize_callback` + getter).
- Parent menu `skvn-marine` ở [footer-settings.php:22](../../wp-content/plugins/skvn-marine-blocks/modules/footer-settings/footer-settings.php#L22).
- Tension **đã có lời giải sẵn** = pattern `function_exists('skvn_marine_blocks_get_*')` ở [typography.php:79](../../wp-content/themes/skvn-marine/inc/typography.php#L79) → plugin tắt thì theme fallback default, KHÔNG mất content.
- theme.json có `palette`, `customGradient:false`, **chưa có `gradients`**.

## Goal

Trang "Single post sidebar content" trong admin menu SKVN Marine: sửa được CTA (text/url/màu), Category (label/chọn cat/order), Related (label/order), TOC per-post (paste block HTML). Theme render từ getter, fallback an toàn khi plugin off.

---

## Quyết định đã chốt

**Gradient CTA** — thêm `settings.color.gradients` vào theme.json, 3 preset **reuse 100% hex palette** + idiom `135deg` đang dùng khắp theme ([style.css:2963](../../wp-content/themes/skvn-marine/style.css#L2963)):

| slug | stops | vai trò |
|---|---|---|
| `skvn-navy-deep` | blue-950 → blue-900 | mặc định CTA |
| `skvn-ocean` | blue-900 → teal-600 | biến thể tươi |
| `skvn-midnight` | blue-950 → slate-900 | biến thể tối |

**Tên module:** `modules/sidebar-blog-content/` (tên rõ "đồ tạm cho blog", sau bỏ không lẫn module thật). Option `skvn_sidebar_content`, getter `skvn_marine_blocks_get_sidebar_content()`.

**3 gap chốt:**
1. Gradient làm ngay (không defer).
2. TOC metabox **gộp** vào `sidebar-blog-content.php` (không tách file).
3. Tie-break order tính **runtime mỗi load**, không persist.

---

## Phase 1 — Plugin: global settings + getter

**Files allowed:**
- `wp-content/plugins/skvn-marine-blocks/modules/sidebar-blog-content/sidebar-blog-content.php` (NEW)
- `wp-content/plugins/skvn-marine-blocks/skvn-marine-blocks.php` (thêm `require_once`)
- `wp-content/themes/skvn-marine/theme.json` (thêm `settings.color.gradients`)

**Files forbidden:** single.php, style.css, header/footer/typography modules.

**Nội dung:**
- Submenu "Single post sidebar content" dưới `skvn-marine`, cap `manage_options`.
- Option array `skvn_sidebar_content`: CTA (eyebrow/heading/label/note/url/bg-solid/bg-gradient/text-color/btn-base/btn-text), Category (label/cat-ids[]/enable/order), Related (label/enable/order).
- Sanitize chặt: text=`sanitize_text_field`, url=`esc_url_raw`, color=whitelist slug palette+gradient (không nhận hex tự do), order=`absint`, enable=bool. Field trống → default.
- Getter `skvn_marine_blocks_get_sidebar_content()` merge default.

**Acceptance:**
- [ ] Trang admin load + lưu được, `get_option('skvn_sidebar_content')` đúng schema.
- [ ] 3 gradient slug đọc được từ theme.json.
- [ ] `php -l` sạch.
- [ ] CHƯA render gì lên frontend.

---

## Phase 2 — Plugin: TOC per-post (gộp cùng module)

**Files allowed:** `sidebar-blog-content.php` (chỉ file Phase 1, +0 file mới).
**Files forbidden:** như Phase 1.

**Nội dung:**
- Metabox màn soạn `post`, cap `edit_post`, meta `_skvn_toc_blocks` (raw HTML/shortcode), nonce.
- Getter `skvn_marine_blocks_get_post_toc($post_id)`.
- ⚠️ KSES sanitization **DEFER** (ghi rõ comment) → fix đúng 1.4–1.6.x.

**Acceptance:**
- [ ] Meta save/load đúng, nonce + cap check.
- [ ] `php -l` sạch.

---

## Phase 3 — Theme: render từ getter

**Files allowed:**
- `wp-content/themes/skvn-marine/single.php`
- `wp-content/themes/skvn-marine/style.css` (class màu CTA + ẩn mobile, nếu cần)

**Files forbidden:** plugin modules, theme.json (đã xong Phase 1).

**Nội dung:**
- Thay hardcode [151-226]: `function_exists('skvn_marine_blocks_get_sidebar_content')` → config; else fallback markup default hiện tại.
- CTA fixed top (KHÔNG vào order), màu = class/CSS var từ slug, button hover auto-derive bằng CSS.
- 3 island (TOC/Category/Related) render theo order number + skip disabled + tie-break runtime (trùng → đẩy số trống/random).
- TOC: `do_blocks() . do_shortcode()` từ meta.
- Category: list link, **bỏ count**, fallback primary category.
- Related: Option C — pool 20–30 bài mới nhất (trừ current) → 2 mới nhất + `shuffle` 4 → transient `skvn_related_{post_id}` TTL 6–12h, dedupe + backfill.
- Mobile: ẩn hết island chừa CTA.

**Acceptance:**
- [ ] Đổi setting → sidebar đổi đúng.
- [ ] Tắt plugin → fallback default, không lỗi.
- [ ] F5 nhiều lần → related không giật (transient).
- [ ] Mobile chỉ hiện CTA.

---

## Tension / conflict

Theme render content/màu lấy từ plugin → **dependency theme → plugin**, ngược nguyên tắc "plugin sống độc lập". **Khử bằng pattern có sẵn** ([typography.php:79](../../wp-content/themes/skvn-marine/inc/typography.php#L79)): `function_exists` guard + theme default fallback → plugin off vẫn có sidebar. Chấp nhận như tech debt có chủ ý, giải quyết khi theme độc lập GP (1.4–1.6.x → block-config + drag-drop, bỏ trang tạm này).

Defer: KSES sanitization TOC, i18n field, view-counter (most-read), permission chi tiết hơn `manage_options`/`edit_post`.
