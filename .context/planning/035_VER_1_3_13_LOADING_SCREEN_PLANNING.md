# 035 — Loading Screen Settings (preloader/splash/brand-bar)

```
Version:   1.3.13
Date:      2026-06-28
Status:    IMPLEMENTED (2 phase — chờ Dev onsite test)
Decision:  docs/decisions/loading-screen-settings-1.3.13.md
```

---

## Context

User muốn preloader trang chủ, **editable** (chọn loại + sửa text/màu/logo), nhất quán pattern settings-driven của dự án. Bắt nguồn từ chẩn đoán flash hero (thực ra do ThumbPress lazy-load ảnh LCP — đã fix config riêng; chi tiết trong decision doc).

## Goal

Admin chọn loại loading screen + sửa nội dung; theme render qua getter, fallback an toàn khi plugin off; không bao giờ treo màn.

## Phase 1 — Plugin: settings + getter + media picker

**Files:** `modules/preloader-settings/preloader-settings.php` (NEW), `skvn-marine-blocks.php` (require).

- Option `skvn_preloader`, submenu "Loading screen" (`manage_options`).
- Fields: type (preloader/splash/brandbar/off), scope, dismiss, min_display, mark_text, tagline, use_logo, logo_id, bg_type, bg_solid, bg_gradient, text_color, accent_color.
- Sanitize whitelist màu theo slug palette/gradient. Media picker logo qua `wp_enqueue_media()` + inline `wp.media`.
- Getter `skvn_marine_blocks_get_preloader()`.

**AC:** lưu được, media picker chọn logo OK, `php -l` sạch. Chưa render.

## Phase 2 — Theme: render

**Files:** `inc/preloader.php` (viết lại). `functions.php` đã include sẵn.

- Config qua `function_exists` getter, else default theme.
- Eligibility: off → bỏ; scope front → `is_front_page()`.
- Resolve màu slug → literal (PHP) cho inline CSS bền vững.
- 4 nhánh render: preloader (overlay+spinner+dismiss), splash (sessionStorage 1 lần/phiên), brandbar (thanh CSS), off.
- An toàn: timeout 3.5s + window-load fallback + min-display + reduced-motion.

**AC:** đổi setting → frontend đổi; off → trống; splash 1 lần/phiên; brandbar không che; logo/min-display/reduced-motion đúng; `php -l` sạch.

## Tension

Theme render config từ plugin → dependency theme→plugin. Khử bằng `function_exists()` + default theme (pattern `inc/typography.php`).
