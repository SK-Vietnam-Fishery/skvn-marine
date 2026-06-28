# Blog Sidebar Content Settings (bản tạm) — 1.3.12

```
Date:     2026-06-27
Status:   IMPLEMENTED (code xong 3 phase — chờ Dev onsite test)
Scope:    BẢN TẠM — milestone đúng (1.4–1.6.x) refactor thành block-config + drag-drop, bỏ module này
Planning: .context/planning/034_VER_1_3_12_BLOG_SIDEBAR_CONTENT_SETTINGS_PLANNING.md
Ideation: docs/ideations/blog-sidebar-content-settings.md
```

## Vấn đề

Sidebar single blog post hardcode 3 island, không có cách đổi nội dung. Cần trang admin tạm để dev/marketing đổi text/màu/thứ tự ngay, trước khi theme độc lập GP và làm block-config thật.

## Quyết định

### Kiến trúc
- **Module tạm:** `skvn-marine-blocks/modules/sidebar-blog-content/` (tên rõ "đồ tạm cho blog", sau bỏ không lẫn module thật). Option global `skvn_sidebar_content`, getter `skvn_marine_blocks_get_sidebar_content()`. Submenu **SKVN Marine → Blog sidebar** (`manage_options`).
- **Render:** theme `single.php` render qua getter, bọc `function_exists()` → **plugin off thì fallback markup default cũ** (pattern lấy từ `inc/typography.php`). Khử Tension theme→plugin: tắt plugin vẫn còn sidebar.
- **Màu:** whitelist theo slug `theme.json` palette/gradient (đọc bằng `wp_get_global_settings`), **không nhận hex tự do**. Frontend dùng preset vars `--wp--preset--color/gradient--{slug}`.

### Gradient (mới thêm vào theme.json)
3 preset reuse 100% hex palette + idiom `135deg`: `skvn-navy-deep` (blue-950→blue-900), `skvn-ocean` (blue-900→teal-600), `skvn-midnight` (blue-950→slate-900).

### Các island
| Island | Scope | Chốt |
|---|---|---|
| CTA | Global, **fixed top** (không vào order) | eyebrow/heading/label/note + url + bg solid/gradient + text color + button base/text; **hover auto-derive** (CSS swap base↔text, không picker rời) |
| TOC | **Per-post** | metabox `post`, meta `_skvn_toc_blocks` raw; render `do_shortcode(do_blocks())`; trống → không hiện |
| Category | Global | label + multi-select cat (no children) + enable + order; **bỏ count**; fallback primary category khi không match |
| Related | Global | label + enable + order; **Option C** random; markup copy category (link-only) |

### Related — Option C
Pool 30 bài mới nhất (trừ current) → giữ 2 mới nhất → `shuffle` phần còn lại lấy 4 → dedupe + backfill đủ 6 → **transient `skvn_related_{id}` TTL 8h** (ổn định khi F5, không query mỗi load). Đã **bỏ "most read"** (không có view-counter).

### Order
Number priority (số nhỏ trên), CTA luôn top. **Tie-break runtime, deterministic:** sort `order` asc, trùng số → giữ theo idx cố định (toc<category<related), không persist. Đúng milestone → drag-drop, bỏ logic này.

### Responsive
Mobile (`max-width:900px`): single sidebar ẩn hết island, **chừa mỗi CTA** (full-bleed).

## Defer (giải quyết ở 1.4–1.6.x)
- **KSES sanitization TOC** — hiện lưu/render raw (admin-entered), ghi rõ comment + phpcs ignore.
- i18n field tiếng Việt; permission chi tiết hơn `manage_options`/`edit_post`; view-counter cho "most read".
- Coupling plugin↔theme.json (đọc palette/gradient).

## Tension (chấp nhận có chủ ý)
Theme render content/màu từ plugin → dependency theme→plugin, ngược nguyên tắc "plugin sống độc lập". Khử bằng `function_exists()` guard + fallback default. Tech debt có chủ ý, giải quyết khi theme độc lập GP.

## Files
- Plugin: `modules/sidebar-blog-content/sidebar-blog-content.php` (settings + TOC metabox), `skvn-marine-blocks.php` (require).
- Theme: `theme.json` (gradients), `single.php` (render + related helper), `style.css` (`.skvn-island--cta` + mobile).
