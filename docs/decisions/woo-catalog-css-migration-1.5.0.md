---
name: woo-catalog-css-migration-1.5.0
description: CSS và PHP render cần move sang woo-catalog plugin khi 1.5.0 implement
metadata:
  type: project
---

# Woo-Catalog CSS Migration — 1.5.0

**Status:** Pending — CSS tạm giữ trong `skvn-marine-blocks` cho đến khi woo-catalog plugin được tạo.

**Why:** Các classes này render dữ liệu từ custom product meta (`_skvn_certifications`, `_skvn_moq`, `_skvn_lead_time`, `_skvn_spec_sheet_url`) được owned bởi woo-catalog plugin. Khi woo-catalog ra, CSS nên đi cùng plugin đó thay vì để dead-weight trong collection block.

---

## CSS cần move — `src/collection/style.css`

| Block | Lines | Classes |
|---|---|---|
| Cert dots | 417–432 | `.skvn-collection-card__certs`, `.skvn-collection-card__cert-dot`, `::before` |
| MOQ / Lead Time stats | 434–457 | `.skvn-collection-card__stats`, `__stat-label`, `__stat-value` |
| PDF link | 477–487 | `.skvn-collection-card__pdf`, `:hover` |

**Giữ lại trong skvn-marine-blocks:**
- `.skvn-collection-card__catalog` (wrapper div slot — dùng để mount point)

---

## PHP render cần move — `modules/collection-render/cards.php`

Trong `skvn_marine_blocks_render_collection_product_card()`:

```php
// MOVE sang woo-catalog:
// 1. Block cert dots (get_post_meta _skvn_certifications)
// 2. Block stats (get_post_meta _skvn_moq + _skvn_lead_time)
// 3. Block PDF link (get_post_meta _skvn_spec_sheet_url)
// Tất cả nằm bên trong <div class="skvn-collection-card__catalog">
```

**Boundary khi 1.5.0:** woo-catalog hook vào `skvn_collection_card_catalog_slot` filter
(hoặc render trực tiếp vào `div.skvn-collection-card__catalog` qua PHP output buffer).

---

## Meta key contract

| Meta key | Owner | Type |
|---|---|---|
| `_skvn_certifications` | woo-catalog | `array` of strings |
| `_skvn_moq` | woo-catalog | `string` |
| `_skvn_lead_time` | woo-catalog | `string` |
| `_skvn_spec_sheet_url` | woo-catalog | `string` (URL) |

Keys đã được hardcode trong cards.php — woo-catalog phải register đúng tên này.

---

## Slot trong card (hiện tại)

```html
<div class="skvn-collection-card__catalog">
  <!-- woo-catalog sẽ inject content vào đây -->
  <!-- Hiện tại: cert dots + stats + pdf link (conditional on post meta) -->
</div>
```

**How to apply:** Khi implement 1.5.0, tìm `skvn-collection-card__catalog` trong cards.php
và move toàn bộ nội dung block đó + CSS tương ứng sang woo-catalog plugin.
