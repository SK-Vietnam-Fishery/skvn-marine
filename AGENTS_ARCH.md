# AGENTS_ARCH.md — SKVN Marine
# Architecture, Naming, PHP Security
# Đọc khi task liên quan đến architecture decision, thêm file mới, hoặc PHP security review.

---

## 2. Architecture — Biết rõ trước khi code

### Boundary rules

| Thuộc về | Layer | Ví dụ |
|---|---|---|
| `skvn-marine/` | Theme | Visual system, design tokens, block styles, patterns, WooCommerce visual override, animation runtime, media helpers |
| `skvn-marine-blocks/` | Plugin | Custom Gutenberg blocks có logic: slider, accordion, product-grid, product-list |
| External plugins | Không touch | WooCommerce, CF7, CFDB7, Rank Math, Polylang, n8n |
| GeneratePress | Không touch tuyệt đối | `themes/generatepress/**` |

**Rule quyết định nhanh**: nếu thay theme mà feature bị mất → feature thuộc plugin.

### Marine Blocks — human decision 2026-11-04

- Định vị: bộ block chuyên dụng và patterns giúp dựng, chỉnh sửa, bàn giao website nhanh; không hướng tới supercharge toàn bộ FSE. Ưu tiên core blocks/patterns, custom block khi cần dữ liệu/tương tác/UX biên tập chuyên dụng. Không tự mở lại query ownership của Collection.
- Plugin có styling/runtime riêng, hoạt động độc lập với WindPress và theme SKVN. Website được dùng WindPress/Tailwind enhance core blocks/patterns; không coi đây là hai lựa chọn loại trừ nhau. Layout/style trên mỗi surface phải có owner rõ ràng.
- Identity đích: Marine Blocks; slug/text domain `marine-blocks`, block namespace `marine-blocks/*`, PHP functions `marine_blocks_*`, constants `MARINE_BLOCKS_*`, plugin CSS `marine-blocks-*`, variables `--marine-blocks-*`.
- Theme giữ identity `skvn-marine`, PHP `skvn_marine_`, CSS/tokens theme. Có thể cập nhật điểm gọi API plugin khi migrate, không đổi thương hiệu theme.
- Human xác nhận 2026-11-04: **1.4.0 — Marine Blocks Migration**; Setup UI cũ 1.4.0 → 1.4.1, Layout/Quote Validation cũ 1.4.1 → 1.4.2, giữ acceptance. Version đã chốt, chưa start; current vẫn 1.3.18. Trước migration giữ naming source hiện hành.
- Replace hàng loạt chỉ theo inventory plugin/theme/shared. Hàm nội bộ có thể đổi đồng bộ; wrapper chỉ cần cho consumer chưa chuyển. Saved blocks/markup, option/meta keys phải có compatibility hoặc migration riêng; không global replace `skvn` trong repo/DB.
- Identity GU Supercharger và mục tiêu full-FSE bị thay thế. Migration sớm hơn không tự thay public-launch gate sau 1.5.x, reset version về 0.0.1, hoặc hủy acceptance/milestone cũ.
- Source of truth: `.context/planning/047_VER_1_4_0_MARINE_BLOCKS_MIGRATION_PLANNING.md`.

### Naming — source hiện hành trước migration 1.4.0

```
Theme slug:         skvn-marine
Plugin slug:        skvn-marine-blocks
Block namespace:    skvn-marine
Theme text domain:  skvn-marine
Plugin text domain: skvn-marine-blocks
Theme PHP prefix:   skvn_marine_
Plugin PHP prefix:  skvn_marine_blocks_
CSS prefix:         skvn-
```

### PHP security — Không thương lượng

```php
// Input — luôn sanitize
$product_id = isset($_GET['product_id']) ? absint($_GET['product_id']) : 0;
$sku        = isset($_GET['sku']) ? sanitize_text_field(wp_unslash($_GET['sku'])) : '';

// Output — luôn escape
echo esc_html($title);
echo esc_attr($value);
echo esc_url($url);
echo wp_kses_post($content);
```
