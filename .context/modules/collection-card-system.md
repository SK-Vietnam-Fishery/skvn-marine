# Collection Card System — Technical Reference

**Milestone:** 1.3.7
**Plugin:** `skvn-marine-blocks`
**Last updated:** 2026-06-19

---

## 1. Files involved

| File | Role |
|---|---|
| `src/product-collection/block.json` | Attribute schema cho product block |
| `src/post-collection/block.json` | Attribute schema cho post block |
| `src/collection/types.ts` | TypeScript types — `CollectionAttributes`, union types |
| `src/collection/constants.ts` | Select/option arrays cho editor controls |
| `src/collection/controls.tsx` | Shared Gutenberg editor sidebar (`CollectionEdit`) |
| `src/collection/style.css` | Frontend + editor CSS — toàn bộ collection styles |
| `src/collection-view.ts` | Frontend JS — Swiper init, autoplay, pagination |
| `modules/collection-render/cards.php` | Card render functions + carousel + footer helper |
| `modules/collection-render/product-collection.php` | Block render callback cho product |
| `modules/collection-render/post-collection.php` | Block render callback cho post |

**JS không sửa:** `src/collection-view.ts` — Swiper init đọc `data-skvn-collection-carousel` JSON config và query `'.skvn-collection__pagination'` từ `.skvn-collection__carousel-outer`. Pagination đã move vào `footer-left` nhưng vẫn nằm trong `carousel-outer` → query vẫn tìm được.

---

## 2. Attribute reference

### 2.1 product-collection (block.json)

| Attribute | Type | Default | Notes |
|---|---|---|---|
| `layout` | string | `"grid"` | `"grid"` \| `"carousel"` |
| `eyebrow` | string | `""` | Section eyebrow label |
| `heading` | string | `"Featured products"` | |
| `showHeading` | boolean | `true` | |
| `intro` | string | `""` | wp_kses_post |
| `accessibleLabel` | string | `""` | `aria-label` fallback |
| `categories` | string[] | `[]` | `product_cat` slugs |
| `tags` | string[] | `[]` | `product_tag` slugs |
| `relation` | string | `"OR"` | `"OR"` \| `"AND"` |
| `orderMode` | string | `"newest"` | `"featured"` \| `"newest"` \| `"manual"` \| `"shuffle-balanced"` |
| `itemsToShow` | number | `3` | |
| `responsivePreset` | string | `"3-2-1"` | `"1-1-1"` \| `"2-1-1"` \| `"3-2-1"` \| `"4-2-1"` \| `"5-3-1"` |
| `showImage` | boolean | `true` | |
| `imageRatio` | string | `"1:1"` | `"1:1"` \| `"4:3"` \| `"3:2"` \| `"16:9"` \| `"auto"` |
| `cardStyle` | string | `"default"` | Backward compat only — không còn ảnh hưởng render card |
| `equalHeight` | boolean | `true` | |
| `badgeBehavior` | string | `"display"` | `"display"` \| `"archive-link"` |
| `showPrice` | boolean | `true` | Backward compat — không render trong card mới |
| `showSku` | boolean | `false` | Backward compat — không render trong card mới |
| `showStock` | boolean | `false` | Backward compat — không render trong card mới |
| `showProductCategories` | boolean | `true` | Không hiển thị trong card (category là query filter, không phải badge) |
| `showProductTags` | boolean | `false` | Điều khiển badge overlay từ `product_tag` |
| `productActionMode` | string | `"quote"` | `"view"` \| `"quote"` \| `"both"` \| `"custom"` |
| `customActionUrl` | string | `""` | |
| `appendQuoteContext` | boolean | `true` | Append `?product_id=...` vào quote URL |
| `showArrows` | boolean | `true` | Carousel chỉ |
| `showPagination` | boolean | `true` | Carousel chỉ |
| `autoplay` | boolean | `false` | |
| `autoplayDelay` | number | `5000` | ms, clamp 3000–10000 |
| `archiveUrl` | string | `""` | Footer right |
| `archiveLabel` | string | `""` | Fallback "View all" |
| `catalogCtaUrl` | string | `""` | Footer right ghost btn |
| `showCatalogCta` | boolean | `false` | Toggle hiện catalog CTA |
| `catalogCtaLabel` | string | `"Tải catalog"` | |
| `showSpecChips` | boolean | `true` | Hiện spec chips từ WC attributes |
| `chipStyle` | string | `"tag"` | `"tag"` \| `"hashtag"` \| `"dot"` \| `"plain"` |
| `chipColorScheme` | string | `""` | Slug từ theme.json palette (`"teal"`, `"navy"`, …) |

### 2.2 post-collection (block.json)

| Attribute | Type | Default | Notes |
|---|---|---|---|
| `layout` | string | `"grid"` | |
| `eyebrow` | string | `""` | |
| `heading` | string | `"Latest articles"` | |
| `showHeading` | boolean | `true` | |
| `intro` | string | `""` | |
| `accessibleLabel` | string | `""` | |
| `categories` | string[] | `[]` | `category` taxonomy slugs |
| `tags` | string[] | `[]` | `post_tag` slugs |
| `relation` | string | `"OR"` | |
| `orderMode` | string | `"newest"` | |
| `itemsToShow` | number | `3` | |
| `responsivePreset` | string | `"3-2-1"` | |
| `showImage` | boolean | `true` | |
| `imageRatio` | string | `"16:9"` | |
| `cardStyle` | string | `"default"` | Backward compat only |
| `equalHeight` | boolean | `true` | |
| `badgeBehavior` | string | `"display"` | |
| `showDate` | boolean | `true` | Hiện trước title |
| `showAuthor` | boolean | `false` | B2B default off; B2C bật được |
| `showPostCategories` | boolean | `true` | Badge overlay trên ảnh |
| `showPostTags` | boolean | `false` | Không render trong card (removed từ 1.3.7) |
| `showExcerpt` | boolean | `true` | |
| `postActionMode` | string | `"read"` | `"read"` \| `"custom"` |
| `customActionUrl` | string | `""` | |
| `showArrows` | boolean | `true` | |
| `showPagination` | boolean | `true` | |
| `autoplay` | boolean | `false` | |
| `autoplayDelay` | number | `5000` | |
| `archiveUrl` | string | `""` | |
| `archiveLabel` | string | `""` | |
| `showCatalogCta` | boolean | `false` | |
| `catalogCtaUrl` | string | `""` | |
| `catalogCtaLabel` | string | `"Tải catalog"` | |
| `readMoreLabel` | string | `"Đọc thêm →"` | Text của read-more link |

---

## 3. PHP functions (cards.php)

### `skvn_marine_blocks_render_collection_footer( $attributes, $context )`

```
@param array  $attributes  Block attributes
@param string $context     'carousel' | 'grid'
@return string             HTML hoặc '' nếu không có gì cần render
```

**Logic:**
- `$has_left = 'carousel' === $context && $show_pagination` → slot pagination
- `$has_right = ($show_catalog_cta && $catalog_cta_url !== '') || $archive_url !== ''`
- Nếu cả hai đều false → return `''` (không render footer rỗng)
- Pagination `<div class="skvn-collection__pagination">` chỉ render trong `footer-left` khi context là `carousel`

**DOM output:**
```html
<div class="skvn-collection__footer">
  <div class="skvn-collection__footer-left">
    <!-- [pagination div khi carousel] -->
  </div>
  <div class="skvn-collection__footer-right">
    <!-- [catalog CTA ghost btn] [archive link] -->
  </div>
</div>
```

---

### `skvn_marine_blocks_render_collection_carousel( $items, $attributes, $content_type, $footer_html = '' )`

```
@param array  $items        WP_Post[] | WC_Product[]
@param array  $attributes   Block attributes
@param string $content_type 'post' | 'product'
@param string $footer_html  Pre-rendered từ render_collection_footer() — inject sau .swiper div
@return string
```

**DOM structure:**
```html
<div class="skvn-collection__carousel-outer" data-skvn-collection-carousel="{...}">
  [arrow prev] [arrow next]
  <div class="skvn-collection__carousel swiper">
    <div class="swiper-wrapper">
      <div class="swiper-slide"><!-- card --></div>
      ...
    </div>
    [pause button nếu autoplay]
  </div>
  <!-- $footer_html inject ở đây — bên trong carousel-outer -->
</div>
```

> **Quan trọng:** Pagination div đã được move từ bên trong `.swiper` ra `footer-left` (bên trong `carousel-outer`). JS `container.querySelector('.skvn-collection__pagination')` vẫn tìm được vì `container` = `.carousel-outer`.

---

### `skvn_marine_blocks_render_collection_product_card( $product, $attributes )`

**Card classes:** `skvn-collection-card skvn-collection-card--product [skvn-collection-card--chip-{style}] [skvn-chips--{color}]`

**Chip style class** chỉ thêm khi `chipStyle !== 'tag'` (tag là default, không cần modifier).
**Chip color class** chỉ thêm khi `chipColorScheme !== ''`.

**Badge overlay:** Lấy từ `product_tag` taxonomy. Controlled by `showProductTags` attribute. KHÔNG conditional theo `cardStyle`.

**Spec chips:** `$product->get_attributes()` → filter `get_visible() === true` → taxonomy dùng `get_terms()`, local attribute dùng `get_options()`.

**Placeholder meta fields** (render nếu có, ẩn nếu rỗng — data thật sẽ vào ở 1.5.0).
Human 2026-10-07: MOQ/lead time/certs đều là text tự do, nhiều giá trị cách nhau bằng dấu phẩy:
- `_skvn_certifications` — `array|CSV string|''` → render `.skvn-collection-card__certs` (tách phẩy, trim, bỏ rỗng)
- `_skvn_moq` — `string|''` → render `.skvn-collection-card__stats` (vd `"5 MT, 10 MT"` → mỗi giá trị một dòng, cap 3, không ghép cặp với lead time)
- `_skvn_lead_time` — `string|''` → render `.skvn-collection-card__stats` (như MOQ)
- `_skvn_spec_sheet_url` — `string|''` → render `.skvn-collection-card__pdf` (sanitize-at-read `esc_url_raw`)

---

### `skvn_marine_blocks_render_collection_post_card( $post, $attributes )`

**Card classes:** `skvn-collection-card skvn-collection-card--post`

**Thứ tự elements trong body:**
1. `.skvn-collection-card__date` — nếu `showDate`
2. `.skvn-collection-card__author` — nếu `showAuthor`
3. `.skvn-collection-card__title`
4. `.skvn-collection-card__excerpt` — nếu `showExcerpt`
5. `.skvn-collection-card__read-more` — luôn có (text từ `readMoreLabel`, fallback "Đọc thêm →")

**Badge overlay:** Từ `category` taxonomy, controlled by `showPostCategories`. Luôn overlay trên ảnh.

> `showPostTags` vẫn tồn tại trong block.json (backward compat) nhưng không render trong card từ 1.3.7.

---

### `skvn_marine_blocks_render_collection_term_badges( $object_id, $taxonomy, $attributes, $visibility_key )`

Reusable helper — trả về `.skvn-collection-card__badges` container với các `<span>` hoặc `<a>` badge.
`$visibility_key` → check boolean attribute trước khi query terms. Return `''` nếu false.

---

## 4. CSS class inventory

### Section-level

| Class | Mô tả |
|---|---|
| `.skvn-collection` | Root wrapper |
| `.skvn-collection--product` / `--post` | Block type |
| `.skvn-collection--grid` / `--carousel` | Layout mode |
| `.skvn-collection--preset-{X-Y-Z}` | Responsive grid preset |
| `.skvn-collection--ratio-{X-Y}` | Image ratio (e.g. `--ratio-1-1`) |
| `.skvn-collection--equal-height` | Equal height cards |
| `.skvn-collection__eyebrow` | Teal bar + uppercase text (::before = teal bar) |
| `.skvn-collection__heading` | Section H2 |
| `.skvn-collection__intro` | Optional intro paragraph |
| `.skvn-collection__footer` | flex, justify-content: space-between |
| `.skvn-collection__footer-left` | Pagination slot |
| `.skvn-collection__footer-right` | Catalog CTA + archive link |
| `.skvn-collection__archive-link` | "View all" link |
| `.skvn-collection__catalog-cta` | Ghost button (teal border) |
| `.skvn-collection__pagination` | Swiper pagination dots container |
| `.skvn-collection__carousel-outer` | Position relative wrapper — JS data attribute host |
| `.skvn-collection__carousel.swiper` | Swiper root |
| `.skvn-collection__arrow--prev/next` | Arrow buttons |
| `.skvn-collection__pause-btn` | Autoplay pause button |

### Card-level

| Class | Mô tả |
|---|---|
| `.skvn-collection-card` | Card root |
| `.skvn-collection-card--product` / `--post` | Card type |
| `.skvn-collection-card--chip-hashtag` | Chip style modifier (thêm khi chipStyle !== 'tag') |
| `.skvn-collection-card--chip-dot` | |
| `.skvn-collection-card--chip-plain` | |
| `.skvn-chips--{slug}` | Chip color scheme (e.g. `skvn-chips--teal`) |
| `.skvn-collection-card__media` | Image `<a>` wrapper — `position: relative` |
| `.skvn-collection-card__image` | `<img>` |
| `.skvn-collection-card__fallback` | No-image placeholder span |
| `.skvn-collection-card__badges` | Badge container (absolute overlay trên ảnh) |
| `.skvn-collection-card__badge` | Individual badge span/a |
| `.skvn-collection-card__body` | flex column, gap 0.65rem |
| `.skvn-collection-card__title` | H3 |
| `.skvn-collection-card__specs` | Spec chips flex container |
| `.skvn-collection-card__spec-tag` | Individual chip |
| `.skvn-collection-card__certs` | Cert dots flex container |
| `.skvn-collection-card__cert-dot` | Individual cert (::before = •) |
| `.skvn-collection-card__stats` | MOQ + Lead Time grid (2 columns) |
| `.skvn-collection-card__stat-label` | "MOQ", "LEAD TIME" uppercase small |
| `.skvn-collection-card__stat-value` | Giá trị bold |
| `.skvn-collection-card__cta` | Quote/action CTA button (dark navy, full width) |
| `.skvn-collection-card__pdf` | Spec sheet PDF link (teal, center) |
| `.skvn-collection-card__date` | Post date (trước title) |
| `.skvn-collection-card__author` | Post author |
| `.skvn-collection-card__excerpt` | Post excerpt |
| `.skvn-collection-card__read-more` | Post read-more link (teal) |

---

## 5. Spec chips — cơ chế WooCommerce

Spec chips đọc từ `$product->get_attributes()` (trả về `WC_Product_Attribute[]`):

```php
foreach ( $product->get_attributes() as $attr ) {
    if ( ! $attr->get_visible() ) continue; // "Visible on product page" checkbox

    $values = $attr->is_taxonomy()
        ? wp_list_pluck( $attr->get_terms(), 'name' )  // global attr (pa_*)
        : $attr->get_options();                          // local attr (string[])
}
```

**Điều khiển per-attribute:** WooCommerce product edit → Attributes tab → bỏ tích "Visible on product page" → chip ẩn.

**Điều khiển block-level:** `showSpecChips` boolean toggle trong sidebar.

---

## 6. chipColorScheme — cơ chế extend

`chipColorScheme` lưu slug của một color trong theme.json palette. Controls.tsx đọc palette runtime qua `useSettings('color.palette')`.

**Thêm màu mới:**
1. Thêm vào `theme.json` → `settings.color.palette`
2. Màu tự xuất hiện trong "Chip color scheme" dropdown
3. Thêm CSS rule trong `style.css`:
```css
.skvn-chips--{new-slug} .skvn-collection-card__spec-tag {
    border-color: var(--wp--preset--color--{new-slug});
    color: var(--wp--preset--color--{new-slug});
}
```

---

## 7. Footer logic — carousel vs grid

```
product-collection.php / post-collection.php:
├── layout === 'carousel'
│   ├── $footer_html = render_collection_footer($attributes, 'carousel')
│   │   └── footer-left: pagination div, footer-right: CTA + archive
│   └── render_collection_carousel($items, $attributes, $type, $footer_html)
│       └── $footer_html inject bên trong .carousel-outer, sau .swiper
└── layout === 'grid'
    ├── render cards
    └── echo render_collection_footer($attributes, 'grid')
        └── footer-left: rỗng (no pagination slot), footer-right: CTA + archive
```

---

## 8. Deferred to 1.5.0 (woo-catalog)

- `_skvn_certifications` — real data (custom meta UI)
- `_skvn_moq` — real data
- `_skvn_lead_time` — real data
- `_skvn_spec_sheet_url` — real data
- Product Taxonomy Collections admin
- Per-attribute spec chip filter (beyond "Visible on product page")
- Faceted / AJAX filtering

---

## 9. Breaking changes từ pre-1.3.7

| Thay đổi | Impact |
|---|---|
| `catalogPdfUrl` rename → `catalogCtaUrl` | Block instances cũ mất giá trị — update thủ công trong editor |
| `showAuthor` default `true` → `false` | Block instances cũ (giá trị explicit) không ảnh hưởng; new block instances default off |
| Pagination div move ra khỏi `.swiper` | JS không thay đổi; CSS rule cũ nhắm vào `.swiper .skvn-collection__pagination` sẽ không match — đã update CSS |
| Badge overlay không còn conditional theo `cardStyle` | Tag/category badge luôn hiện trên ảnh nếu `showProductTags` / `showPostCategories` |
| `cardStyle` attribute | Còn trong block.json (backward compat) nhưng không ảnh hưởng render card từ 1.3.7 |
