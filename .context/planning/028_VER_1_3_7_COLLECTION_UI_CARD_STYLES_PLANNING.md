# 028 — V1.3.7 Collection Block UI & Card Styles Planning

**Milestone:** 1.3.7
**Status:** DECISIONS FINALIZED — READY TO IMPLEMENT
**Design artifact:** `.local/Seafood Carousels.html` (sanitized: `.local/Seafood Export Carousels Tailwind.debug.html`)
**Blocks in scope:** `skvn-marine/product-collection`, `skvn-marine/post-collection`
**Session finalized:** 2026-06-18

---

## 0. Baseline — Đã có, KHÔNG cần thay đổi

| Thành phần | File | Trạng thái |
|---|---|---|
| `eyebrow` / `showHeading` / `intro` — block.json + PHP render | cả 2 blocks | ✅ Done |
| `imageRatio` PHP emit class | cả 2 PHP renderers line 25 | ✅ Done |
| `archiveUrl` / `archiveLabel` — block.json + PHP | cả 2 blocks | ✅ Done |
| `4-2-1` preset CSS grid | style.css line 63 | ✅ Done |
| Carousel outer wrapper (BUG-02 fix) | cards.php line 220 | ✅ Done |
| `pagination.el` JS query từ `carousel-outer` | collection-view.ts line 55 | ✅ Works — không cần sửa JS |

---

## 1. Decisions Finalized

| # | Item | Quyết định |
|---|---|---|
| D-1 | Badge overlay source — product | `product_tag` taxonomy ("Wild Caught", "Farmed") |
| D-2 | Badge overlay source — post | `category` taxonomy ("Regulation", "Certification") |
| D-3 | Badge overlay behavior | Always overlay top-left trên ảnh — không conditional theo `cardStyle` |
| D-4 | Spec chips source | `$product->get_attributes()` filter `get_visible() === true` |
| D-5 | Spec chips visibility per attr | WooCommerce product edit → "Visible on product page" checkbox |
| D-6 | Spec chips block control | `showSpecChips: boolean` (default `true`) |
| D-7 | chipStyle options | `tag` / `hashtag` / `dot` / `plain` (default: `tag`) |
| D-8 | chipColorScheme | Từ theme.json palette via `useSettings('color.palette')` — auto-append khi add màu mới |
| D-9 | Read-more label | Attribute `readMoreLabel: string` (default "Đọc thêm →") — user-configurable |
| D-10 | `showAuthor` | Giữ attribute + render khi `true`, default `false` (B2B default, B2C bật được) |
| D-11 | `catalogPdfUrl` | Rename → `catalogCtaUrl` — no backward compat (still dev milestone) |
| D-12 | Catalog CTA attrs | `showCatalogCta: boolean` + `catalogCtaUrl: string` + `catalogCtaLabel: string` (default "Tải catalog") — cả 2 blocks |
| D-13 | Footer helper | Shared function `skvn_marine_blocks_render_collection_footer()` trong cards.php |
| D-14 | Pagination position | Trong `footer-left` bên trong `carousel-outer` — JS không cần thay đổi |
| D-15 | DRY principle | Footer render 1 function dùng chung cho product + post |

---

## 2. Attributes Changes

### 2.1 product-collection/block.json

**Rename:**
```
catalogPdfUrl (string, "") → catalogCtaUrl (string, "")
```

**Add:**
```json
"showCatalogCta":   { "type": "boolean", "default": false },
"catalogCtaLabel":  { "type": "string",  "default": "Tải catalog" },
"showSpecChips":    { "type": "boolean", "default": true },
"chipStyle":        { "type": "string",  "default": "tag" },
"chipColorScheme":  { "type": "string",  "default": "" }
```

**Không thêm:** `showHeading`, `eyebrow`, `imageRatio` (default `1:1`), `archiveUrl`, `archiveLabel` — đã có.

**Giữ nhưng không render trong card mới:** `showPrice`, `showSku`, `showStock`, `cardStyle` — backward compat.

### 2.2 post-collection/block.json

**Add:**
```json
"showCatalogCta":   { "type": "boolean", "default": false },
"catalogCtaUrl":    { "type": "string",  "default": "" },
"catalogCtaLabel":  { "type": "string",  "default": "Tải catalog" },
"readMoreLabel":    { "type": "string",  "default": "Đọc thêm →" }
```

**Không thêm:** đã có `showHeading`, `eyebrow`, `imageRatio` (default `16:9`), `archiveUrl`, `archiveLabel`, `showAuthor`, `showDate`, `showExcerpt`.

---

## 3. PHP Changes

### 3.1 product-collection.php

Thêm/đổi biến:
```php
// Xóa:
$catalog_url = isset($attributes['catalogPdfUrl']) ...

// Thay bằng:
$show_catalog_cta  = skvn_marine_blocks_collection_bool($attributes, 'showCatalogCta', false);
$catalog_cta_url   = isset($attributes['catalogCtaUrl']) ? esc_url_raw($attributes['catalogCtaUrl']) : '';
$catalog_cta_label = isset($attributes['catalogCtaLabel']) ? sanitize_text_field($attributes['catalogCtaLabel']) : '';
$catalog_cta_label = '' !== $catalog_cta_label ? $catalog_cta_label : __('Tải catalog', 'skvn-marine-blocks');
```

Footer — tách theo mode:
- **Carousel mode:** `$footer_html = skvn_marine_blocks_render_collection_footer($attributes, 'carousel')` → truyền vào `render_collection_carousel()`
- **Grid mode:** `$footer_html = skvn_marine_blocks_render_collection_footer($attributes, 'grid')` → echo sau `.grid`

### 3.2 post-collection.php

Thêm biến catalog CTA (cùng pattern product-collection.php).

Footer — cùng pattern, truyền `$footer_html` vào carousel hoặc echo sau grid.

### 3.3 cards.php — Thay đổi lớn

#### A. `render_collection_carousel()` — thêm param + move pagination

```php
function skvn_marine_blocks_render_collection_carousel($items, $attributes, $content_type, $footer_html = '') {
    // Pagination div: KHÔNG render bên trong .swiper nữa
    // $footer_html (từ render_collection_footer) chứa pagination div trong footer-left
    // echo $footer_html bên dưới .swiper, bên trong .carousel-outer
}
```

DOM mới cho carousel mode:
```html
<div class="skvn-collection__carousel-outer" data-skvn-collection-carousel="...">
  <button.arrow--prev>
  <button.arrow--next>
  <div class="skvn-collection__carousel swiper">
    <div class="swiper-wrapper">...</div>
    <!-- KHÔNG có pagination ở đây nữa -->
  </div>
  <!-- footer_html inject ở đây: -->
  <div class="skvn-collection__footer">
    <div class="skvn-collection__footer-left">
      <div class="skvn-collection__pagination"> ← JS tìm thấy từ carousel-outer
    </div>
    <div class="skvn-collection__footer-right">
      [catalogCta?] [archiveLink?]
    </div>
  </div>
</div>
```

**JS không thay đổi** — `container.querySelector('.skvn-collection__pagination')` vẫn tìm được vì pagination nằm trong `carousel-outer`.

#### B. Thêm helper `skvn_marine_blocks_render_collection_footer()`

```php
function skvn_marine_blocks_render_collection_footer($attributes, $context = 'grid') {
    $show_pagination  = skvn_marine_blocks_collection_bool($attributes, 'showPagination', true);
    $show_catalog_cta = skvn_marine_blocks_collection_bool($attributes, 'showCatalogCta', false);
    $catalog_cta_url  = isset($attributes['catalogCtaUrl']) ? esc_url_raw($attributes['catalogCtaUrl']) : '';
    $catalog_cta_label = ...; // fallback "Tải catalog"
    $archive_url      = isset($attributes['archiveUrl']) ? esc_url_raw($attributes['archiveUrl']) : '';
    $archive_label    = ...; // fallback "Xem tất cả"

    $has_right = ($show_catalog_cta && '' !== $catalog_cta_url) || '' !== $archive_url;
    $has_left  = 'carousel' === $context && $show_pagination;

    if (!$has_left && !$has_right) return ''; // không render footer rỗng

    // render footer-left (pagination slot) + footer-right (CTA + archive)
}
```

Render condition `showPagination`:
- Khi `$context === 'carousel'` → footer-left có `<div class="skvn-collection__pagination">`
- Khi `$context === 'grid'` → footer-left rỗng

#### C. `skvn_marine_blocks_render_collection_product_card()` — Redesign

**Xóa:** `cardStyle` featured logic, `showPrice`, `showSku`, `showStock`, `skvn-collection-card__actions` wrapper, `skvn-collection-card__action`.

**Cấu trúc mới:**
```html
<article class="skvn-collection-card skvn-collection-card--product">
  [image block — badge overlay từ product_tag, luôn overlay]
  <div class="skvn-collection-card__body">
    <h3.title>
    [spec chips — từ get_attributes() filter get_visible(), nếu showSpecChips]
    [cert dots — từ _skvn_certifications post meta, ẩn nếu rỗng]
    [stats row — MOQ + Lead Time từ _skvn_moq / _skvn_lead_time post meta, ẩn nếu rỗng]
    [quote CTA button — full width, nếu action_mode quote/both]
    [spec sheet PDF link — từ _skvn_spec_sheet_url post meta, ẩn nếu rỗng]
  </div>
</article>
```

Badge overlay:
```php
// Luôn overlay — không check cardStyle
$tag_terms = get_the_terms($product_id, 'product_tag');
if ($tag_terms && !is_wp_error($tag_terms) && $show_image):
  // render .skvn-collection-card__badges overlay trên ảnh
```

Spec chips:
```php
if (skvn_marine_blocks_collection_bool($attributes, 'showSpecChips', true)):
    $chip_style  = isset($attributes['chipStyle']) ? sanitize_key($attributes['chipStyle']) : 'tag';
    $chip_color  = isset($attributes['chipColorScheme']) ? sanitize_key($attributes['chipColorScheme']) : '';
    $product_attrs = $product->get_attributes();
    foreach ($product_attrs as $attr):
        if (!$attr->get_visible()) continue;
        $values = $attr->is_taxonomy()
            ? ($attr->get_terms() ? wp_list_pluck($attr->get_terms(), 'name') : [])
            : $attr->get_options();
        foreach ($values as $value):
            // render chip theo $chip_style
        endforeach;
    endforeach;
```

Placeholder meta (render nếu có, ẩn nếu rỗng):
```php
$certs     = get_post_meta($product_id, '_skvn_certifications', true); // array|''
$moq       = get_post_meta($product_id, '_skvn_moq', true);            // string|''
$lead_time = get_post_meta($product_id, '_skvn_lead_time', true);      // string|''
$pdf_url   = get_post_meta($product_id, '_skvn_spec_sheet_url', true); // string|''
```

#### D. `skvn_marine_blocks_render_collection_post_card()` — Redesign

**Xóa:** `cardStyle` featured logic, body badge render (badges move to overlay).

**Thay đổi chính:**

| Element | Trước | Sau |
|---|---|---|
| Category badge | Trong body bên dưới title | Overlay top-left trên ảnh |
| Date | Trong `card__meta` div, sau title | `card__date` span, TRƯỚC title |
| Author | Trong `card__meta` cùng date | Render khi `showAuthor === true` (giữ attr, default false) |
| Post tags | Trong body | Bỏ khỏi card (không trong design) |
| Action class | `skvn-collection-card__action` | `skvn-collection-card__read-more` |
| Action text | "Read more" (hardcode) | `readMoreLabel` attribute (default "Đọc thêm →") |

**Cấu trúc mới:**
```html
<article class="skvn-collection-card skvn-collection-card--post">
  [image block — category overlay, luôn overlay]
  <div class="skvn-collection-card__body">
    [date — nếu showDate, TRƯỚC title]
    [author — nếu showAuthor]
    <h3.title>
    [excerpt — nếu showExcerpt]
    <a.read-more>[readMoreLabel]</a>
  </div>
</article>
```

---

## 4. CSS Changes — `src/collection/style.css`

### 4.1 Eyebrow — teal accent bar (CSS-only, không sửa PHP)

```css
/* Thay thế block .skvn-collection__eyebrow hiện tại: */
.skvn-collection__eyebrow {
    align-items: center;
    color: #0D9488;
    display: flex;
    font-size: 0.8rem;
    font-weight: 600;
    gap: 0.5rem;
    letter-spacing: 0.08em;
    margin: 0 0 0.5rem;
    text-transform: uppercase;
}
.skvn-collection__eyebrow::before {
    background: #0D9488;
    content: '';
    display: block;
    flex-shrink: 0;
    height: 2px;
    width: 22px;
}
```

### 4.2 Footer — redesign layout

```css
.skvn-collection__footer {
    align-items: center;
    display: flex;
    justify-content: space-between;
    margin-top: 1.25rem;
}
.skvn-collection__footer-left  { align-items: center; display: flex; }
.skvn-collection__footer-right { align-items: center; display: flex; gap: 1rem; }

/* Pagination trong footer-left: reset margin */
.skvn-collection__footer-left .skvn-collection__pagination {
    margin-top: 0;
    justify-content: flex-start;
}
```

### 4.3 Catalog CTA — ghost button

```css
.skvn-collection__catalog-cta {
    border: 1.5px solid #0D9488;
    border-radius: 4px;
    color: #0D9488;
    font-size: 0.9rem;
    font-weight: 600;
    padding: 0.35rem 0.9rem;
    text-decoration: none;
    transition: background 0.15s, color 0.15s;
}
.skvn-collection__catalog-cta:hover {
    background: #0D9488;
    color: #fff;
}
```

### 4.4 Archive link

```css
.skvn-collection__archive-link {
    align-items: center;
    color: #25404d;
    display: inline-flex;
    font-weight: 600;
    gap: 0.3rem;
    text-decoration: none;
}
.skvn-collection__archive-link:hover { text-decoration: underline; }
```

### 4.5 Badge overlay — universal (không chỉ --featured)

```css
/* Thêm position: relative vào media (hiện chưa có) */
.skvn-collection-card__media {
    background: #edf4f7;
    display: block;
    position: relative; /* thêm */
}

/* Badges luôn overlay — replace rule --featured cũ */
.skvn-collection-card__media .skvn-collection-card__badges {
    left: 0.75rem;
    position: absolute;
    top: 0.75rem;
}
```

### 4.6 Product card — new classes

```css
/* Spec chips container */
.skvn-collection-card__specs {
    display: flex;
    flex-wrap: wrap;
    gap: 0.3rem;
}

/* chipStyle: tag (default) */
.skvn-collection-card__spec-tag {
    background: #f0f6fa;
    border: 1px solid #c6d9e2;
    border-radius: 3px;
    color: #25404d;
    font-size: 0.78rem;
    padding: 0.1rem 0.4rem;
}

/* chipStyle: hashtag */
.skvn-collection-card--chip-hashtag .skvn-collection-card__spec-tag {
    background: none;
    border: none;
    color: #0D9488;
    padding: 0;
}
.skvn-collection-card--chip-hashtag .skvn-collection-card__spec-tag::before { content: '#'; }

/* chipStyle: dot */
.skvn-collection-card--chip-dot .skvn-collection-card__spec-tag {
    background: none;
    border: none;
    padding: 0;
}
.skvn-collection-card--chip-dot .skvn-collection-card__spec-tag::before { content: '• '; }

/* chipStyle: plain */
.skvn-collection-card--chip-plain .skvn-collection-card__spec-tag {
    background: none;
    border: none;
    padding: 0;
}
.skvn-collection-card--chip-plain .skvn-collection-card__spec-tag + .skvn-collection-card__spec-tag::before {
    content: ' / ';
    color: #c6d9e2;
}

/* chipColorScheme — per WP palette slug */
.skvn-chips--teal .skvn-collection-card__spec-tag {
    border-color: var(--wp--preset--color--teal);
    color: var(--wp--preset--color--teal);
}
.skvn-chips--navy .skvn-collection-card__spec-tag {
    border-color: var(--wp--preset--color--navy);
    color: var(--wp--preset--color--navy);
}

/* Cert dots */
.skvn-collection-card__certs {
    display: flex;
    flex-wrap: wrap;
    gap: 0.4rem;
}
.skvn-collection-card__cert-dot {
    color: #0D9488;
    font-size: 0.78rem;
    font-weight: 600;
}
.skvn-collection-card__cert-dot::before { content: '• '; }

/* MOQ / Lead Time stats */
.skvn-collection-card__stats {
    border-top: 1px solid #e8f0f4;
    display: grid;
    gap: 0.5rem;
    grid-template-columns: 1fr 1fr;
    padding-top: 0.6rem;
}
.skvn-collection-card__stat-label {
    color: #7b9aaa;
    display: block;
    font-size: 0.7rem;
    font-weight: 600;
    letter-spacing: 0.06em;
    text-transform: uppercase;
}
.skvn-collection-card__stat-value {
    color: #25404d;
    display: block;
    font-size: 0.95rem;
    font-weight: 700;
}

/* Quote CTA — full width */
.skvn-collection-card__cta {
    background: #0A2540;
    border-radius: 4px;
    color: #fff;
    display: block;
    font-weight: 600;
    margin-top: auto;
    padding: 0.55rem 1rem;
    text-align: center;
    text-decoration: none;
    transition: background 0.15s;
}
.skvn-collection-card__cta:hover { background: #0D9488; }

/* Spec sheet PDF link */
.skvn-collection-card__pdf {
    color: #0D9488;
    font-size: 0.85rem;
    text-align: center;
    text-decoration: none;
}
.skvn-collection-card__pdf:hover { text-decoration: underline; }
```

### 4.7 Post card — new classes

```css
/* Date */
.skvn-collection-card__date {
    color: #7b9aaa;
    font-size: 0.78rem;
}

/* Read more */
.skvn-collection-card__read-more {
    color: #0D9488;
    font-size: 0.9rem;
    font-weight: 600;
    margin-top: auto;
    text-decoration: none;
}
.skvn-collection-card__read-more:hover { text-decoration: underline; }
```

---

## 5. JS — collection-view.ts

**Không thay đổi.** `paginationEl = container.querySelector('.skvn-collection__pagination')` tìm từ `.carousel-outer` — pagination div nằm trong `footer-left` bên trong `carousel-outer` → vẫn found.

---

## 6. Sidebar Editor (edit.tsx) — Các control mới

**product-collection:**
- `showSpecChips` ToggleControl
- `chipStyle` SelectControl: `[tag, hashtag, dot, plain]`
- `chipColorScheme` SelectControl: populate từ `useSettings('color.palette')`, option đầu = "Default"
- `showCatalogCta` ToggleControl
- `catalogCtaUrl` URLInput (hiện khi `showCatalogCta === true`)
- `catalogCtaLabel` TextControl (hiện khi `showCatalogCta === true`)

**post-collection:**
- `showCatalogCta` ToggleControl
- `catalogCtaUrl` URLInput
- `catalogCtaLabel` TextControl
- `readMoreLabel` TextControl

---

## 7. File List — Tất cả file cần sửa

| File | Thay đổi |
|---|---|
| `src/product-collection/block.json` | Rename `catalogPdfUrl`→`catalogCtaUrl`; thêm `showCatalogCta`, `catalogCtaLabel`, `showSpecChips`, `chipStyle`, `chipColorScheme` |
| `src/post-collection/block.json` | Thêm `showCatalogCta`, `catalogCtaUrl`, `catalogCtaLabel`, `readMoreLabel` |
| `modules/collection-render/product-collection.php` | Rename attr; footer → `render_collection_footer()`; truyền `$footer_html` vào carousel |
| `modules/collection-render/post-collection.php` | Thêm catalog CTA vars; footer → `render_collection_footer()`; truyền `$footer_html` vào carousel |
| `modules/collection-render/cards.php` | Redesign product card + post card; thêm `$footer_html` param vào carousel; thêm `render_collection_footer()` helper; move pagination ra khỏi swiper |
| `src/collection/style.css` | Eyebrow teal bar; footer layout; catalog CTA ghost btn; badge overlay universal; product card classes; post card classes; chip style variants; chip color scheme |
| `src/product-collection/edit.tsx` | Thêm controls: showSpecChips, chipStyle, chipColorScheme, showCatalogCta, catalogCtaUrl, catalogCtaLabel |
| `src/post-collection/edit.tsx` | Thêm controls: showCatalogCta, catalogCtaUrl, catalogCtaLabel, readMoreLabel |

**JS không sửa:** `src/collection-view.ts`

---

## 8. Implementation Order

1. `block.json` cả 2 blocks — attribute changes
2. `cards.php` — `render_collection_footer()` helper + `render_collection_carousel()` signature + move pagination
3. `product-collection.php` + `post-collection.php` — footer wiring
4. `cards.php` — product card redesign
5. `cards.php` — post card redesign
6. `style.css` — tất cả CSS mới
7. `edit.tsx` cả 2 blocks — sidebar controls
8. Build + PHP lint
9. Onsite QA

---

## 9. Out of Scope — Deferred

- MOQ, Lead Time, certifications, spec sheet PDF real data → **1.5.0 woo-catalog**
- Product Taxonomy Collections admin
- Faceted / AJAX filtering
- Per-block attribute injection từ catalog plugin
- `chipColorScheme` CSS per new palette slug → add khi user request màu mới (pattern: copy `.skvn-chips--teal` rule)
