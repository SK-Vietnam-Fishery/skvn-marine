# Planning: woo-catalog Plugin — Milestone 1.5.0

> Tài liệu này là source of truth cho việc implement `woo-catalog` plugin.
> Đọc toàn bộ file này trước khi bắt đầu bất kỳ task nào liên quan đến milestone 1.5.0.
> Không cần tìm kiếm thêm context ở nơi khác — nếu thiếu, ghi vào phần Open Questions.

---

## 1. Plugin Identity

| Key | Value |
|-----|-------|
| Plugin slug | `woo-catalog` |
| Plugin name | `Woo Catalog` |
| PHP prefix | `woo_catalog_` |
| Text domain | `woo-catalog` |
| Option namespace | `woo_catalog_` |
| CSS variable namespace | `--woo-catalog-` / `--woo-cert-` |
| Min WooCommerce | 7.0 |
| Min WordPress | 6.4 |
| Plugin file | `woo-catalog/woo-catalog.php` |

> **Lý do prefix `woo_catalog_` thay vì `skvn_marine_`:** Plugin được thiết kế generic — reusable cho các site WooCommerce khác, không binding vào skvn-marine. Nếu cần swap sang catalog type khác (courses, parts, etc.) thì không cần sửa blocks plugin.

---

## 2. Architecture Overview

```
skvn-marine-blocks (block plugin)
├── Rendering engine — domain-agnostic
├── Layout CSS: card structure, flexbox, aspect-ratio
└── optional-reads woo-catalog via PHP functions + JS filters

        ↕ PHP API:  woo_catalog_get_fields($product_id)
        ↕ JS:       addFilter('editor.BlockEdit', ...) → inject sidebar

woo-catalog (this plugin)
├── Settings page → catalog type selector
├── Catalog type: seafood/
│   ├── Taxonomy: product_certification
│   ├── Meta: MOQ, Lead Time, Spec Sheet, Origin
│   ├── WC product panel: data entry UI
│   ├── style.css: semantic colors (cert badge, spec tag)
│   └── editor.js: inject InspectorControls vào collection block
├── Catalog type: courses/ (future)
│   ├── Taxonomy: product_level
│   ├── Meta: Duration, Certificate, Syllabus PDF
│   ├── style.css: course-specific colors
│   └── editor.js: inject InspectorControls
├── Global style settings → wp_options['woo_catalog_style'] → CSS vars on :root
└── PHP API: woo_catalog_get_fields(), woo_catalog_is_active(), etc.
```

**Rule:** `skvn-marine-blocks` KHÔNG bao giờ `require` hay `include` file của `woo-catalog`. Giao tiếp chỉ qua `function_exists()` guard + public PHP functions + WordPress hooks.

---

## 3. Catalog Type System

### 3.1 Type Registry

Mỗi catalog type là một "profile" độc lập. Catalog plugin đăng ký type qua internal registry:

```php
// woo-catalog.php bootstrap
woo_catalog_register_type('seafood', [
    'label'   => 'Seafood / Marine',
    'class'   => WooCatalog_Seafood_Type::class,
    'css'     => plugin_dir_url(__FILE__) . 'catalog-types/seafood/style.css',
    'editor'  => plugin_dir_url(__FILE__) . 'catalog-types/seafood/editor.js',
]);

woo_catalog_register_type('courses', [
    'label'   => 'Courses / Education',
    'class'   => WooCatalog_Courses_Type::class,
    'css'     => plugin_dir_url(__FILE__) . 'catalog-types/courses/style.css',
    'editor'  => plugin_dir_url(__FILE__) . 'catalog-types/courses/editor.js',
]);
```

### 3.2 Settings Page

**Location:** `wp-admin → Woo Catalog → Settings`

```
Catalog Type
  ○ Seafood / Marine     [Default for skvn-marine]
  ○ Courses / Education
  ○ Custom               [Deferred — grayed out, tooltip "Coming soon"]

[Save Changes]
```

Active type lưu vào `wp_options['woo_catalog_active_type']`. Khi thay đổi type:
- Deactivate CSS/JS của type cũ
- Activate CSS/JS của type mới
- Không xóa data của type cũ (non-destructive)

### 3.3 Style Settings Tab

```
Catalog Style
  Badge shape:   [● Pill]  [○ Rectangle]
  Tag background: [color picker]  Default: #003366
  Tag text:       [color picker]  Default: #ffffff

Certification Colors (per term slug — auto-populated from taxonomy)
  HACCP:       [color picker]  Default: #2d6a4f
  ASC:         [color picker]  Default: #1d4e89
  BAP:         [color picker]  Default: #5c4033
  MSC:         [color picker]  Default: #1565c0
  EU APPROVED: [color picker]  Default: #1b5e20
  TRACEABLE:   [color picker]  Default: #4a4a4a
  VSATTP:      [color picker]  Default: #880e4f

[Save Changes]
```

Lưu vào `wp_options['woo_catalog_style']`. Plugin inject inline CSS vào `<head>` (frontend + editor):

```css
:root {
  --woo-catalog-badge-radius: 999px;    /* pill */
  --woo-catalog-tag-bg: #003366;
  --woo-catalog-tag-color: #ffffff;
  --woo-cert-haccp: #2d6a4f;
  --woo-cert-asc: #1d4e89;
  /* ... */
}
```

---

## 4. CSS Ownership Split

### 4.1 `skvn-marine-blocks` sở hữu (layout — domain-agnostic)

```css
/* src/collection/style.css */
.skvn-collection-card { display: flex; flex-direction: column; }
.skvn-collection-card__image { aspect-ratio: 1/1; overflow: hidden; }
.skvn-collection-card__body { display: flex; flex: 1; flex-direction: column; gap: 0.65rem; }
.skvn-collection-card__certs { display: flex; flex-wrap: wrap; gap: 0.25rem; }
.skvn-cert { display: inline-flex; align-items: center; gap: 0.3rem;
             border-radius: var(--woo-catalog-badge-radius, 999px);
             font-size: 0.7rem; padding: 0.15rem 0.5rem; font-weight: 600; }
.skvn-collection-card__spec-tags { display: flex; flex-wrap: wrap; gap: 0.25rem; }
.skvn-spec-tag { background: var(--woo-catalog-tag-bg, #003366);
                  color: var(--woo-catalog-tag-color, #fff);
                  border-radius: var(--woo-catalog-badge-radius, 999px);
                  font-size: 0.7rem; padding: 0.15rem 0.5rem; }
```

Block biết slot nào để render (`.skvn-cert`, `.skvn-spec-tag`), nhưng không hardcode màu hay shape.

### 4.2 `woo-catalog` sở hữu (semantic colors — domain-specific)

```css
/* catalog-types/seafood/style.css */
.skvn-cert--haccp  { background: var(--woo-cert-haccp, #2d6a4f); color: #fff; }
.skvn-cert--asc    { background: var(--woo-cert-asc, #1d4e89); color: #fff; }
.skvn-cert--bap    { background: var(--woo-cert-bap, #5c4033); color: #fff; }
.skvn-cert--msc    { background: var(--woo-cert-msc, #1565c0); color: #fff; }
/* ... */
```

CSS variables trong `:root` do plugin settings inject. Style file của seafood type chỉ map slug → variable reference.

---

## 5. Style Controls Architecture

### 5.1 Global (Plugin Settings Page)

- Thay đổi áp dụng cho **toàn site** — không per-block
- Lưu: `wp_options['woo_catalog_style']`
- Output: inline CSS trên `:root` (cả frontend và editor iframe)
- Hook: `wp_head` + `admin_head` (cho editor)

### 5.2 Per-Block Override (via `addFilter`)

Catalog plugin inject `InspectorControls` vào `skvn-marine/collection` block **mà không sửa block plugin**:

```js
// catalog-types/seafood/editor.js
import { addFilter } from '@wordpress/hooks';
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, ColorPicker, SelectControl } from '@wordpress/components';

addFilter(
    'editor.BlockEdit',
    'woo-catalog/inject-collection-controls',
    (BlockEdit) => (props) => {
        if (props.name !== 'skvn-marine/collection') {
            return <BlockEdit {...props} />;
        }
        return (
            <>
                <BlockEdit {...props} />
                <InspectorControls>
                    <PanelBody title="Catalog Style (Override)" initialOpen={false}>
                        <p className="description">
                            Overrides global Woo Catalog style settings for this block only.
                        </p>
                        <SelectControl
                            label="Badge shape"
                            value={props.attributes.wcBadgeShape ?? 'global'}
                            options={[
                                { value: 'global',    label: 'Use global setting' },
                                { value: 'pill',      label: 'Pill' },
                                { value: 'rectangle', label: 'Rectangle' },
                            ]}
                            onChange={(val) => props.setAttributes({ wcBadgeShape: val })}
                        />
                    </PanelBody>
                </InspectorControls>
            </>
        );
    }
);
```

**Tension — Per-block attribute registration:**
Block attributes như `wcBadgeShape` cần được registered. `block.json` của blocks plugin không biết về catalog. Giải pháp cần brainstorm:
- Option A: Catalog plugin dùng `register_block_type_args` filter để inject attributes dynamically
- Option B: Block.json có sẵn placeholder attributes (`wcOverride: {}`) — catalog plugin populate
- Option C: Lưu override vào `wp_options` keyed by block client ID — nhưng client ID không stable

**Recommended (chưa confirmed):** Option A — `register_block_type_args` filter.

---

## 6. PHP API Contract (Public Interface)

Blocks plugin sử dụng **chỉ** các functions này. Không gọi internal class hay method trực tiếp.

```php
/**
 * Check if woo-catalog plugin is active and initialized.
 * Use this before calling any other woo_catalog_* functions.
 */
function woo_catalog_is_active(): bool {}

/**
 * Get all catalog fields for a product.
 * Returns empty array if plugin inactive or product has no catalog data.
 *
 * @param int $product_id WooCommerce product ID
 * @return array {
 *   certifications: array<{slug: string, name: string, color: string}>,
 *   moq: string,           // e.g. "5 MT" — empty string if not set
 *   lead_time: string,     // e.g. "15–20 ngày"
 *   spec_sheet_pdf: string, // URL or empty string
 *   origin: string,        // e.g. "Vietnam"
 *   spec_tags: array<{label: string, type: string}>,
 * }
 */
function woo_catalog_get_fields(int $product_id): array {}

/**
 * Get active catalog type slug.
 * Returns 'seafood' | 'courses' | '' (if not configured)
 */
function woo_catalog_get_type(): string {}

/**
 * Get CSS variable map for inline injection (used by plugin itself, not blocks).
 * Returns [ '--woo-cert-haccp' => '#2d6a4f', ... ]
 */
function woo_catalog_get_style_vars(): array {}
```

---

## 7. Optional-Read Pattern trong `cards.php`

```php
// modules/collection-render/cards.php (sau khi catalog plugin có)

// --- Catalog fields ---
$catalog_fields = [];
if ( function_exists( 'woo_catalog_get_fields' ) && woo_catalog_is_active() ) {
    $catalog_fields = woo_catalog_get_fields( $product->get_id() );
}

$certifications = $catalog_fields['certifications'] ?? [];
$moq            = $catalog_fields['moq']            ?? '';
$lead_time      = $catalog_fields['lead_time']      ?? '';
$spec_sheet     = $catalog_fields['spec_sheet_pdf'] ?? '';
$spec_tags      = $catalog_fields['spec_tags']      ?? [];

// Render certifications
if ( ! empty( $certifications ) ) : ?>
    <div class="skvn-collection-card__certs">
        <?php foreach ( $certifications as $cert ) : ?>
            <span class="skvn-cert skvn-cert--<?= esc_attr( $cert['slug'] ) ?>">
                <?= esc_html( $cert['name'] ) ?>
            </span>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if ( $moq || $lead_time ) : ?>
    <dl class="skvn-collection-card__logistics">
        <?php if ( $moq ) : ?>
            <div><dt>MOQ</dt><dd><?= esc_html( $moq ) ?></dd></div>
        <?php endif; ?>
        <?php if ( $lead_time ) : ?>
            <div><dt>Lead Time</dt><dd><?= esc_html( $lead_time ) ?></dd></div>
        <?php endif; ?>
    </dl>
<?php endif; ?>

<?php if ( $spec_sheet ) : ?>
    <a class="skvn-collection-card__spec-link"
       href="<?= esc_url( $spec_sheet ) ?>" target="_blank" rel="noopener">
        Tải spec sheet PDF
    </a>
<?php endif; ?>
```

**Rule:** Mọi render block liên quan đến catalog đều phải wrapped trong `if (!empty(...))` hoặc `if ($field)`. Catalog inactive = field trống = block không render = layout không vỡ.

---

## 8. Data Model: Seafood Catalog Type

### 8.1 Taxonomy: `product_certification`

```php
register_taxonomy('product_certification', 'product', [
    'hierarchical'      => false,
    'label'             => 'Certifications',
    'show_in_rest'      => true,
    'show_admin_column' => true,
    'rewrite'           => ['slug' => 'certification'],
]);
```

**Default terms + slugs:**

| Slug | Label | Default Color |
|------|-------|---------------|
| `haccp` | HACCP | #2d6a4f |
| `asc` | ASC | #1d4e89 |
| `bap` | BAP | #5c4033 |
| `msc` | MSC | #1565c0 |
| `eu-approved` | EU Approved | #1b5e20 |
| `traceable` | Traceable | #4a4a4a |
| `vsattp` | VSATTP | #880e4f |

Term color lưu vào term meta `_woo_cert_color`. Editable trong WP admin taxonomy term edit screen.

### 8.2 Custom Product Meta

```php
// prefix: _woo_catalog_ (không dùng _skvn_ để giữ plugin independence)
register_meta('post', '_woo_catalog_moq', [
    'object_subtype' => 'product',
    'type'           => 'string',
    'single'         => true,
    'show_in_rest'   => false,  // cần brainstorm — public hay private?
]);

register_meta('post', '_woo_catalog_lead_time', [...]);
register_meta('post', '_woo_catalog_spec_sheet_pdf', [...]);
register_meta('post', '_woo_catalog_origin', [...]);
```

**MOQ format — chưa quyết định:**
- Option A (current): free text `"5 MT"` — simple, flexible, no sorting
- Option B: split `_woo_catalog_moq_value` (float) + `_woo_catalog_moq_unit` (MT/KG/carton) — enable sorting/filtering
- **Tạm thời dùng Option A** cho 1.5.0. Upgrade sang Option B khi cần filter.

### 8.3 Spec Tags: WooCommerce Product Attributes (Recommended)

Sử dụng built-in WC product attributes thay vì custom taxonomy:

- `pa_processing`: Whole Frozen, IQF, Fillet, Headless Shelled...
- `pa_size`: 200–300g, 300–500g, 500g+...
- `pa_origin`: Vietnam, Pacific, Atlantic...

**Lý do:** Admin UI đã có sẵn trong WC Product → Attributes tab. Không cần UI mới. Display trong card bằng cách query `$product->get_attributes()`.

### 8.4 WooCommerce Product Panel

Thêm custom tab vào WC product data metabox:

```php
add_filter('woocommerce_product_data_tabs', function($tabs) {
    $tabs['woo_catalog'] = [
        'label'  => 'Catalog Data',
        'target' => 'woo_catalog_product_data',
        'class'  => ['show_if_simple', 'show_if_variable'],
    ];
    return $tabs;
});

add_action('woocommerce_product_data_panels', 'woo_catalog_render_product_panel');
```

Panel có fields: MOQ, Lead Time, Spec Sheet PDF (media upload button), Origin.

---

## 9. Data Model: Courses Catalog Type (Future)

Đưa vào planning để không phải rethink sau:

| Meta key | Label | Type |
|----------|-------|------|
| `_woo_catalog_duration` | Duration | string, e.g. "8 tuần" |
| `_woo_catalog_level` | Level | taxonomy slug (`product_level`: beginner/intermediate/advanced) |
| `_woo_catalog_certificate` | Certificate | string, e.g. "CPD Certified" |
| `_woo_catalog_syllabus_pdf` | Syllabus PDF | attachment URL |

Taxonomy `product_level` là flat, màu per term: beginner=#2d7d2d, intermediate=#e65c00, advanced=#cc3300.

---

## 10. File Structure

```
woo-catalog/
├── woo-catalog.php                      # Plugin header, bootstrap, check WC active
├── includes/
│   ├── class-catalog-registry.php       # Type registry + activation
│   ├── class-catalog-api.php            # Public PHP functions (woo_catalog_*)
│   ├── class-catalog-settings.php       # Settings page + style vars injection
│   └── class-catalog-admin.php          # Admin menu registration
├── catalog-types/
│   ├── seafood/
│   │   ├── class-seafood-type.php       # Implements CatalogType interface
│   │   ├── class-seafood-taxonomy.php   # register_taxonomy + default terms
│   │   ├── class-seafood-meta.php       # register_meta + WC product panel
│   │   ├── style.css                    # Cert badge semantic colors
│   │   └── editor.js                   # addFilter → InspectorControls
│   └── courses/
│       ├── class-courses-type.php
│       ├── class-courses-taxonomy.php
│       ├── class-courses-meta.php
│       ├── style.css
│       └── editor.js
└── admin/
    └── views/
        └── settings-page.php
```

---

## 11. WordPress Hook Integration Points

| Hook | Direction | Purpose |
|------|-----------|---------|
| `plugins_loaded` (priority 20) | woo-catalog fires | Bootstrap sau WC loaded |
| `init` | woo-catalog | Register taxonomies, meta |
| `woocommerce_product_data_tabs` | woo-catalog | Add Catalog Data tab |
| `woocommerce_product_data_panels` | woo-catalog | Render WC product panel |
| `woocommerce_process_product_meta` | woo-catalog | Save product panel fields |
| `wp_head` | woo-catalog | Inject CSS vars `:root { }` |
| `admin_enqueue_scripts` | woo-catalog | Enqueue editor.js (catalog type) |
| `editor.BlockEdit` (JS filter) | woo-catalog editor.js | Inject InspectorControls |
| `woo_catalog_loaded` (custom action) | woo-catalog fires | Signal for blocks plugin |

---

## 12. Implementation Order

1. **Plugin skeleton**: `woo-catalog.php` header, WC dependency check, autoloader, bootstrap
2. **Settings page**: admin menu, type selector, save handler, CSS vars injection
3. **Catalog type registry**: `CatalogType` interface, `CatalogRegistry` class, `woo_catalog_register_type()`
4. **Seafood type — data layer**: taxonomy registration, default terms, meta registration, WC product panel
5. **PHP API**: `woo_catalog_get_fields()`, `woo_catalog_is_active()`, `woo_catalog_get_type()`
6. **Global style settings**: color pickers per cert slug, inject inline CSS vars
7. **Seafood type — CSS**: `catalog-types/seafood/style.css` — cert badge colors using CSS vars
8. **blocks plugin integration**: optional-read pattern trong `cards.php` — certifications, MOQ, Lead Time, spec sheet
9. **Seafood type — editor.js**: `addFilter` → InspectorControls per-block override (sau khi brainstorm attribute registration approach)
10. **Courses type**: sau khi seafood validated onsite
11. **Onsite QA**: hết seafood product cards trên frontend với dữ liệu thực

---

## 13. Open Questions (cần brainstorm trước khi implement bước liên quan)

| # | Question | Ảnh hưởng bước |
|---|----------|---------------|
| OQ-01 | Per-block attribute registration: Option A (`register_block_type_args` filter) hay Option B (placeholder trong block.json)? | Bước 9 |
| OQ-02 | MOQ: free text "5 MT" hay split value + unit? | Bước 4 |
| OQ-03 | `show_in_rest`: product meta public hay private? Auth nào? | Bước 4 |
| OQ-04 | Site hiện có ACF Free active không? Có nên dùng ACF cho meta fields? | Bước 4 |
| OQ-05 | Spec sheet PDF: WP attachment upload hay external URL input? Fallback khi attachment deleted? | Bước 4 |
| OQ-06 | Certification badge: CSS color bullet (current plan) hay SVG icon? | Bước 7, 8 |
| OQ-07 | Prefetch on card hover: có làm không? Performance threshold? | Bước 8 |
| OQ-08 | Data migration plan nếu có free-text product data trước milestone này? | Trước bước 4 |

---

## 14. Acceptance Criteria (đầy đủ — update khi brainstorm xong)

- [ ] Architecture contract và data model schema approved trước implementation
- [ ] `woo-catalog` plugin active/deactivate không ảnh hưởng đến `skvn-marine-blocks`
- [ ] Catalog type selector hoạt động — seafood profile activates đúng taxonomy + meta + CSS + editor.js
- [ ] `product_certification` taxonomy registered, có WP admin UI, default terms có đủ
- [ ] MOQ, Lead Time, Spec Sheet, Origin meta registered với WC product panel
- [ ] Global style settings page: badge shape, cert colors save và inject CSS vars
- [ ] CSS vars đúng ở cả frontend (`wp_head`) và editor iframe (`admin_head`)
- [ ] Catalog plugin inject InspectorControls vào `skvn-marine/collection` qua `addFilter`
- [ ] `woo_catalog_get_fields()` trả về đúng cấu trúc array
- [ ] `skvn-marine-blocks` optional-reads: khi catalog inactive → no fatal, empty fields → no render
- [ ] Product card hiển thị certifications, MOQ, Lead Time, spec sheet link từ real product data
- [ ] Layout không vỡ khi fields trống (khóa học không có MOQ)
- [ ] WC product data panel save/load đúng giá trị
- [ ] PHP lint pass, JS build pass
- [ ] Onsite QA với dữ liệu thực
- [ ] Human approves data model schema trước khi code
- [ ] Human approves milestone completion
