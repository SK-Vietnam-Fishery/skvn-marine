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

### Naming — Không bao giờ đổi

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
