# Loading Screen Settings (preloader/splash/brand-bar) — 1.3.13

```
Date:     2026-06-28
Status:   IMPLEMENTED (code xong 2 phase — chờ Dev onsite test)
Planning: .context/planning/archives/035_VER_1_3_13_LOADING_SCREEN_PLANNING.md
```

## Bối cảnh — vì sao có feature này

User báo trang chủ "flash" lúc vào: thấy **nền + text trước, ảnh hero vào sau**. Chẩn đoán (xem mục dưới) cho thấy **không phải lỗi code theme**, mà do **ThumbPress Lazy Load lazy-load ảnh hero LCP** — đã fix riêng bằng cách loại hero khỏi lazy. Tuy vậy user **vẫn muốn có preloader** như một feature thương hiệu, và yêu cầu nó **editable** (chọn loại + sửa nội dung) cho nhất quán với hướng settings-driven của dự án ([blog-sidebar](blog-sidebar-content-settings-1.3.13.md) cùng pattern).

## Chẩn đoán flash (đã xác nhận, lưu lại)

- **Phương pháp:** trace ngược pipeline render→hydrate, dựng trạng thái T0 (CSS tĩnh) vs T1 (sau JS) — không cần chạy browser.
- **Loại trừ:** ảnh nặng (35kb webp — không phải), FOUC stacked-slides (slide không nhảy), WP core lazy (core không tráo data-URI).
- **Xác nhận qua View Source:** ảnh hero có `class="...thumbpress-lazy"`, `src="data:image/gif..."` (placeholder 1×1), URL thật ở `data-src`. → **ThumbPress Lazy Load** rewrite server-side, hoãn ảnh LCP.
- **Fix gốc (Dev, config):** loại `skvn-slide__background-image` / ảnh above-the-fold khỏi ThumbPress. Đã xác nhận View Source sau fix: `src` = webp thật, hết `thumbpress-lazy`.
- **Nguyên tắc rút ra:** ảnh hero above-the-fold (LCP) **không bao giờ lazy-load**.

## Quyết định — Loading screen editable

### Kiến trúc (đúng pattern dự án)
- **Settings + getter ở plugin** `skvn-marine-blocks/modules/preloader-settings/`. Option `skvn_preloader`, getter `skvn_marine_blocks_get_preloader()`. Submenu **SKVN Marine → Loading screen** (`manage_options`).
- **Render ở theme** `inc/preloader.php` qua `function_exists()` guard → plugin off thì dùng default theme. Khử Tension theme→plugin như [typography.php].
- Màu resolve **slug → literal** trong PHP (không phụ thuộc thứ tự load global styles).

### Loại (dropdown)
| Loại | Hành vi |
|---|---|
| **Preloader** | overlay + spinner, ẩn theo `hero`/`window`/`time` + min-display |
| **Splash** | overlay brand, **1 lần/phiên** (`sessionStorage`, skip sớm trong head → không nháy) |
| **Skeleton** | overlay khung xám shimmer (5 thanh mô phỏng hero); nền = field bg (color/gradient), thanh tint theo `text_color`; dismiss như preloader |
| **Brand bar** | thanh mỏng ở đỉnh, **không che**, CSS animation tự chạy |
| **Off** | không render |

### Sửa được
- Phạm vi: trang chủ / toàn site (Splash luôn 1 lần/phiên).
- Nội dung: chữ (trống→tên site) + tagline + **logo ảnh** (media picker `wp.media`).
- Màu: nền solid/gradient + chữ + nhấn — whitelist theme palette/gradient.
- Dismiss: hero-load / window-load / time + **min-display ms**.

### An toàn (không lặp lỗi đã bàn)
- **Timeout cứng 3.5s** + `window load` fallback → không bao giờ treo màn (kể cả khi init chậm như memory [[slider-frontend-delay]] ghi).
- `prefers-reduced-motion`: tắt spin/animation.
- Critical CSS in sớm ở `wp_head` → overlay không tự nháy.

## Files
- Plugin: `modules/preloader-settings/preloader-settings.php` (mới), `skvn-marine-blocks.php` (require).
- Theme: `inc/preloader.php` (viết lại từ bản hardcode trước đó), `functions.php` (đã include).

## Defer / chưa làm
- Min-display mặc định = 0 (ẩn ngay khi hero xong). Chưa thêm preview trực tiếp trong admin.
- Artifact POC ([page-load-options-poc.html](../artifacts/page-load-options-poc.html)) chưa đồng bộ đúng 4 loại đang chạy thật.

## Tension
Giống blog-sidebar: theme render dùng config từ plugin → dependency theme→plugin. Khử bằng `function_exists()` + default theme. Tech debt có chủ ý.
