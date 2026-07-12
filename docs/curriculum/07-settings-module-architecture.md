# Module 07 — Settings page & kiến trúc module PHP

## Mục tiêu

- Viết settings page chuẩn Settings API: đăng ký option, sanitize, capability, nonce.
- Tổ chức plugin nhiều feature thành các module PHP độc lập + một feature registry.

## Khái niệm

**Settings API flow** (WordPress chính chủ, đừng tự chế form handler):

```
register_setting( group, option_name, [ 'sanitize_callback' => ... ] )
add_settings_section / add_settings_field
Trang settings: form POST tới options.php — WordPress lo nonce + capability + save
```

Sanitize callback là **chốt chặn duy nhất** trước khi vào DB: whitelist key, ép kiểu
từng giá trị. Xem cách dự án sanitize `skvn_core_controls` — "sanitizes only known
boolean keys": key lạ bị vứt, không lưu mù.

**Module layout** (pattern dự án dùng từ 0.9.0):

```
modules/
  footer-settings/     ← một feature = một folder
  core-control/
  collection-render/
```

Mỗi module tự chứa: đăng ký option/menu của nó, render callback của nó, asset của nó.
Plugin chính chỉ `require` các module. Lợi ích thật (đã kiểm chứng khi dự án lên kế
hoạch tách plugin GU Supercharger): module bốc đi được nguyên khối.

**Feature registry** (bài học 1.3.4 Core Control): khi có nhiều feature bật/tắt được,
đừng mỗi feature một settings page + option riêng — làm **một registry**: mảng khai báo
`[ id, label, description, default ]`, một option array `plugin_features`, một trang
settings render từ registry. Thêm feature mới = thêm một entry, không copy-paste
Settings API code.

**Getter công khai + guard**: module expose getter (`skvn_marine_blocks_get_header_actions()`)
cho theme/code khác đọc, và bên đọc phải bọc `function_exists()` — plugin tắt thì bên
đọc degrade nhẹ nhàng, không fatal. (Tension header-actions của dự án cho thấy nửa còn
lại của bài học: getter mà không ai render = orphan feature. Làm feature là làm đủ chuỗi
store → getter → render.)

## Đọc code thật

1. `modules/footer-settings/` — module settings hoàn chỉnh đầu tiên của dự án: menu,
   Settings API, option `skvn_footer_page_id`, getter.
2. `modules/core-control/` — registry pattern: đọc cách một feature mới đăng ký vào registry.
3. `wp-content/themes/skvn-marine/inc/footer.php` — phía consumer: `function_exists`
   guard + fallback.

## Bài tập (capstone bước 7)

1. Module `modules/showcase-settings/`: menu "Simple Showcase" trong admin,
   option `simple_showcase_settings` (array), sanitize whitelist.
2. Registry 2 feature: `enable_lightbox` (bool, default off), `brand_color_preset`
   (enum light/dark/brand — tái dùng preset module 06).
3. Getter `simple_showcase_get_settings()`; block render đọc qua getter, có default
   khi option chưa tồn tại.
4. Test độc lập: deactivate → activate lại → settings còn nguyên; xóa option trong DB
   (wp option delete) → mọi thứ chạy bằng defaults, không notice/fatal.
5. Capability check: user Editor (không phải Admin) không thấy trang settings.

## Checkpoint

- [ ] Sanitize callback vứt được key lạ (test bằng cách POST thêm field giả)
- [ ] Thêm feature thứ 3 vào registry chỉ mất ~5 dòng
- [ ] Consumer nào của getter cũng có guard + fallback
