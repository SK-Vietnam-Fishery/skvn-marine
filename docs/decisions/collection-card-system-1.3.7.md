# Collection Card System — Technical Reference (1.3.7)

**Milestone:** 1.3.7 — Collection Block UI & Card Styles
**Plugin:** `skvn-marine-blocks`
**Blocks:** `skvn-marine/product-collection`, `skvn-marine/post-collection`
**Date:** 2026-06-19
**Status:** Implemented

---

## 1. Tổng quan thay đổi

Milestone 1.3.7 redesign toàn bộ card render và footer layout của hai collection blocks. Thay đổi tập trung vào:

- **Badge overlay universal** — không còn conditional theo `cardStyle`
- **Spec chips** từ WooCommerce product attributes
- **Footer shared helper** — một function dùng chung cho cả grid và carousel mode
- **Pagination move** ra khỏi `.swiper` vào `footer-left` (JS không đổi)
- **Post card restructure** — date trước title, read-more label configurable
- **Catalog CTA ghost button** trong footer (cả 2 blocks)

Xem chi tiết từng quyết định tại: `.context/planning/028_VER_1_3_7_COLLECTION_UI_CARD_STYLES_PLANNING.md`

---

## 2. Files

| File | Role |
|---|---|
| `src/product-collection/block.json` | Attribute schema |
| `src/post-collection/block.json` | Attribute schema |
| `src/collection/types.ts` | TypeScript types shared |
| `src/collection/constants.ts` | Editor select options |
| `src/collection/controls.tsx` | Shared Gutenberg sidebar UI |
| `src/collection/style.css` | Toàn bộ frontend + editor CSS |
| `src/collection-view.ts` | Frontend JS — **không sửa trong 1.3.7** |
| `modules/collection-render/cards.php` | Tất cả card + carousel + footer render helpers |
| `modules/collection-render/product-collection.php` | Block render callback |
| `modules/collection-render/post-collection.php` | Block render callback |

---

## 3. Attribute reference

### product-collection

| Attribute | Type | Default | Ghi chú |
|---|---|---|---|
| `showProductTags` | boolean | `false` | Badge overlay từ `product_tag` (Wild Caught, Farmed) |
| `showSpecChips` | boolean | `true` | Show/hide toàn bộ spec chip block |
| `chipStyle` | string | `"tag"` | `"tag"` \| `"hashtag"` \| `"dot"` \| `"plain"` |
| `chipColorScheme` | string | `""` | Palette slug từ theme.json (e.g. `"teal"`, `"navy"`) |
| `showCatalogCta` | boolean | `false` | Hiện catalog CTA button ở footer |
| `catalogCtaUrl` | string | `""` | Renamed từ `catalogPdfUrl` (1.3.7 break) |
| `catalogCtaLabel` | string | `"Tải catalog"` | Text của ghost button |

> `cardStyle`, `showPrice`, `showSku`, `showStock`, `showProductCategories` — còn trong block.json (backward compat) nhưng không render trong card từ 1.3.7.

### post-collection

| Attribute | Type | Default | Ghi chú |
|---|---|---|---|
| `showDate` | boolean | `true` | Hiện trước title trong body |
| `showAuthor` | boolean | `false` | B2B default off; B2C tự bật |
| `showPostCategories` | boolean | `true` | Badge overlay trên ảnh |
| `readMoreLabel` | string | `"Đọc thêm →"` | Text của read-more link (configurable) |
| `showCatalogCta` | boolean | `false` | |
| `catalogCtaUrl` | string | `""` | |
| `catalogCtaLabel` | string | `"Tải catalog"` | |

> `showPostTags` — còn trong block.json nhưng post card từ 1.3.7 không render tags.

---

## 4. DOM structure

### Product card

```html
<article class="skvn-collection-card skvn-collection-card--product
                [skvn-collection-card--chip-{style}] [skvn-chips--{colorSlug}]">

  <!-- Image block — badge overlay LUÔN trên ảnh khi showProductTags -->
  <a class="skvn-collection-card__media" href="...">
    <img class="skvn-collection-card__image" />
    <div class="skvn-collection-card__badges">  <!-- position: absolute, top-left -->
      <span class="skvn-collection-card__badge">Wild Caught</span>
    </div>
  </a>

  <div class="skvn-collection-card__body">
    <h3 class="skvn-collection-card__title"><a>...</a></h3>

    <!-- Spec chips (nếu showSpecChips + có visible attributes) -->
    <div class="skvn-collection-card__specs">
      <span class="skvn-collection-card__spec-tag">Block Frozen</span>
      <span class="skvn-collection-card__spec-tag">IQF</span>
    </div>

    <!-- Cert dots (nếu _skvn_certifications meta không rỗng) -->
    <div class="skvn-collection-card__certs">
      <span class="skvn-collection-card__cert-dot">ASC</span>
    </div>

    <!-- MOQ + Lead Time (nếu _skvn_moq hoặc _skvn_lead_time meta không rỗng) -->
    <div class="skvn-collection-card__stats">
      <div>
        <span class="skvn-collection-card__stat-label">MOQ</span>
        <span class="skvn-collection-card__stat-value">500kg</span>
      </div>
      <div>
        <span class="skvn-collection-card__stat-label">Lead time</span>
        <span class="skvn-collection-card__stat-value">2–4 weeks</span>
      </div>
    </div>

    <!-- CTA button (quote / view / custom) -->
    <a class="skvn-collection-card__cta" href="...">Request quote</a>

    <!-- Spec sheet PDF (nếu _skvn_spec_sheet_url meta không rỗng) -->
    <a class="skvn-collection-card__pdf" href="..." target="_blank">Download spec sheet (PDF)</a>
  </div>
</article>
```

### Post card

```html
<article class="skvn-collection-card skvn-collection-card--post">

  <a class="skvn-collection-card__media" href="...">
    <img class="skvn-collection-card__image" />
    <div class="skvn-collection-card__badges">  <!-- category overlay khi showPostCategories -->
      <span class="skvn-collection-card__badge">Regulation</span>
    </div>
  </a>

  <div class="skvn-collection-card__body">
    <span class="skvn-collection-card__date">19 June 2026</span>   <!-- nếu showDate -->
    <span class="skvn-collection-card__author">Admin</span>         <!-- nếu showAuthor -->
    <h3 class="skvn-collection-card__title"><a>...</a></h3>
    <div class="skvn-collection-card__excerpt">...</div>            <!-- nếu showExcerpt -->
    <a class="skvn-collection-card__read-more" href="...">Đọc thêm →</a>
  </div>
</article>
```

### Carousel + footer DOM

```html
<div class="skvn-collection__carousel-outer" data-skvn-collection-carousel="{...}">
  <button class="skvn-collection__arrow skvn-collection__arrow--prev">‹</button>
  <button class="skvn-collection__arrow skvn-collection__arrow--next">›</button>

  <div class="skvn-collection__carousel swiper">
    <div class="swiper-wrapper">
      <div class="swiper-slide"><!-- card --></div>
    </div>
    <!-- Pause button nếu autoplay -->
  </div>

  <!-- footer inject bên trong carousel-outer, SAU .swiper -->
  <div class="skvn-collection__footer">
    <div class="skvn-collection__footer-left">
      <div class="skvn-collection__pagination"></div>  <!-- JS inject dots vào đây -->
    </div>
    <div class="skvn-collection__footer-right">
      <a class="skvn-collection__catalog-cta">Tải catalog</a>
      <a class="skvn-collection__archive-link">View all</a>
    </div>
  </div>
</div>
```

### Grid + footer DOM

```html
<section class="skvn-collection skvn-collection--grid ...">
  <!-- eyebrow, heading, intro -->
  <div class="skvn-collection__grid">
    <!-- cards -->
  </div>
  <div class="skvn-collection__footer">
    <div class="skvn-collection__footer-left"></div>  <!-- rỗng trong grid mode -->
    <div class="skvn-collection__footer-right">
      <a class="skvn-collection__catalog-cta">Tải catalog</a>
      <a class="skvn-collection__archive-link">View all</a>
    </div>
  </div>
</section>
```

---

## 5. PHP function signatures (cards.php)

```php
// Shared footer — dùng cho cả carousel và grid
skvn_marine_blocks_render_collection_footer( array $attributes, string $context = 'grid' ): string

// $context = 'carousel' → footer-left có pagination div
// $context = 'grid'     → footer-left rỗng
// Return '' nếu không có gì cần render (không tạo div rỗng)

// Carousel wrapper — $footer_html inject sau .swiper
skvn_marine_blocks_render_collection_carousel(
    array  $items,
    array  $attributes,
    string $content_type,   // 'post' | 'product'
    string $footer_html = ''
): string

// Card renders
skvn_marine_blocks_render_collection_product_card( WC_Product $product, array $attributes ): string
skvn_marine_blocks_render_collection_post_card( WP_Post $post, array $attributes ): string

// Badge helper (reused)
skvn_marine_blocks_render_collection_term_badges(
    int    $object_id,
    string $taxonomy,
    array  $attributes,
    string $visibility_key  // attribute key điều khiển hiện/ẩn
): string
```

---

## 6. Spec chips — WooCommerce data flow

```
WC product edit → Attributes tab
    └── [x] Visible on product page   ← $attr->get_visible()

PHP render:
    $product->get_attributes()         // WC_Product_Attribute[]
    ├── $attr->is_taxonomy() === true  → global attr (pa_*)
    │   └── $attr->get_terms()         // WP_Term[] → use term->name
    └── $attr->is_taxonomy() === false → local attr
        └── $attr->get_options()       // string[]

Block-level toggle: showSpecChips attribute
```

---

## 7. chipColorScheme — cơ chế extend

Slider dropdown trong sidebar đọc `useSettings('color.palette')` — tự append khi thêm màu vào `theme.json`.

**Thêm màu mới cần thêm CSS rule:**

```css
.skvn-chips--{new-slug} .skvn-collection-card__spec-tag {
    border-color: var(--wp--preset--color--{new-slug});
    color: var(--wp--preset--color--{new-slug});
}
```

Hiện có sẵn: `skvn-chips--teal`, `skvn-chips--navy`.

---

## 8. JS — tại sao không đổi

`src/collection-view.ts` line 55:

```ts
const paginationEl = container.querySelector<HTMLElement>('.skvn-collection__pagination')
```

`container` = `.skvn-collection__carousel-outer` (host của `data-skvn-collection-carousel`).

Trước 1.3.7: pagination div nằm bên trong `.swiper` (cũng bên trong `carousel-outer`).
Từ 1.3.7: pagination div nằm trong `.footer-left` (cũng bên trong `carousel-outer`).

Query `container.querySelector(...)` tìm đệ quy bên trong → vẫn tìm được → zero JS changes.

---

## 9. Breaking changes

| Breaking change | Cách xử lý |
|---|---|
| `catalogPdfUrl` rename → `catalogCtaUrl` | Update thủ công block instances cũ trong Gutenberg editor |
| `showAuthor` default `true` → `false` | Block instances có explicit value không bị ảnh hưởng |
| Pagination DOM move | Không ảnh hưởng JS; CSS rules cũ nhắm `.swiper .pagination` không còn match — CSS đã update |

---

## 10. Deferred sang 1.5.0 (woo-catalog)

Các meta fields placeholder hiện chỉ render nếu data có sẵn trong database. Real data + admin UI sẽ vào 1.5.0:

- `_skvn_certifications` (array)
- `_skvn_moq` (string)
- `_skvn_lead_time` (string)
- `_skvn_spec_sheet_url` (string)
