# Typography Controls & GeneratePress Exit Plan
**Report date**: 2026-06-10
**Milestone**: V1 / 1.5.0 — Typography Settings
**Author**: Session log — AI-assisted planning
**Status**: Implemented (typography-settings.php, inc/typography.php). GP exit planning documented.

---

## 1. Mục tiêu của milestone 1.5.0

Cho phép marketing user thay đổi **brand palette** và **heading scale** (size + weight) thông qua admin UI trong WordPress, không cần chỉnh sửa code. Phạm vi:

- Color: 4 semantic slots (primary, accent, surface, text)
- Heading: h1–h4, mỗi level có size (rem/px) và weight (400–800)
- Font family: **defer** — chưa làm ở milestone này
- Custom palette builder UI: **defer** — sang 2.0.0 khi có Site Editor

---

## 2. Kiến trúc token — Semantic layer

### Vấn đề đã phát hiện trước khi implement

`theme.json` và `style.css` có **color drift** — cùng slug nhưng hex value khác nhau:

| Slug | theme.json (sai) | style.css (đúng) |
|---|---|---|
| `skvn-blue-950` | `#073b5a` | `#082f49` |
| `skvn-blue-700` | `#1e79be` | `#0369a1` |
| `skvn-slate-700` | `#0f172a` | `#334155` |

**Quyết định**: `style.css` là source of truth cho hex value. `theme.json` đã được align theo.

### Token cascade sau khi fix

```
theme.json palette
    ↓ WordPress generates
--wp--preset--color--skvn-blue-700: #0369a1
    ↓ style.css bridges
--skvn-color-blue-700: var(--wp--preset--color--skvn-blue-700)
    ↓ semantic layer (style.css default)
--skvn-color-primary: var(--skvn-color-blue-700)
    ↓ PHP override (inc/typography.php via wp_add_inline_style)
--skvn-color-primary: #custom  ← saved từ admin UI
```

**Rule quan trọng**: CSS của theme bám vào `--skvn-color-*`, không bám trực tiếp vào `--wp--preset--color--*`. Khi exit GP và rebuild `theme.json`, chỉ cần remap một chỗ.

### Semantic slots hiện tại

| Slot | Default | Dùng cho |
|---|---|---|
| `--skvn-color-primary` | `#0369a1` | heading, button, link, CTA |
| `--skvn-color-accent` | `#0d9488` | hover, badge, highlight, icon |
| `--skvn-color-surface` | `#eaf7ff` | card bg, section bg, panel |
| `--skvn-color-text` | `#334155` | body, label, caption |

### Heading CSS vars

```css
--skvn-h1-size: 3rem;     --skvn-h1-weight: 800;
--skvn-h2-size: 2.25rem;  --skvn-h2-weight: 700;
--skvn-h3-size: 1.875rem; --skvn-h3-weight: 600;
--skvn-h4-size: 1.5rem;   --skvn-h4-weight: 600;
```

CSS heading rules trong `style.css` dùng vars với fallback:

```css
h1 { font-size: var(--skvn-h1-size, 3rem); font-weight: var(--skvn-h1-weight, 800); }
```

Fallback đảm bảo headings render đúng kể cả khi PHP injection chưa chạy.

---

## 3. theme.json — Thay đổi ở 1.5.0

### fontSizes mở rộng

Thêm heading scale range (đã làm):

```json
{ "slug": "2xl", "size": "1.5rem",    "name": "2XL" },
{ "slug": "3xl", "size": "1.875rem",  "name": "3XL" },
{ "slug": "4xl", "size": "2.25rem",   "name": "4XL" },
{ "slug": "5xl", "size": "3rem",      "name": "5XL" }
```

### Tắt free input

```json
"typography": {
  "customFontSize": false
},
"color": {
  "custom": false,
  "customGradient": false
}
```

Marketing user chỉ chọn từ SKVN palette và preset sizes — không nhập hex hoặc px tùy tiện. Custom palette được quản lý qua admin UI riêng (typography settings), không phải free picker trong editor.

---

## 4. Implementation — File registry

### Files tạo mới ở 1.5.0

| File | Layer | Nhiệm vụ |
|---|---|---|
| `wp-content/plugins/skvn-marine-blocks/modules/typography-settings/typography-settings.php` | Plugin | Admin UI + register_setting + sanitize |
| `wp-content/themes/skvn-marine/inc/typography.php` | Theme | Đọc option → build CSS → wp_add_inline_style |

### Files sửa ở 1.5.0

| File | Thay đổi |
|---|---|
| `skvn-marine-blocks.php` | Thêm `require_once` cho typography-settings module |
| `functions.php` | Thêm `'inc/typography.php'` vào includes array |
| `theme.json` | Fix color drift, thêm fontSizes, tắt custom |
| `style.css` | Bridge `--skvn-color-*` → wp preset vars, thêm semantic tokens |

---

## 5. WordPress Settings API — Pattern chuẩn của project

**Rule**: Mọi admin settings page trong plugin đều dùng WordPress Settings API. Không dùng custom `admin-post.php` handler.

### Skeleton pattern (từ footer-settings + header-settings)

```php
// 1. Hooks
add_action('admin_menu', 'skvn_marine_blocks_xxx_menu');
add_action('admin_init', 'skvn_marine_blocks_register_xxx_settings');

// 2. Menu
function skvn_marine_blocks_xxx_menu() {
    add_submenu_page('skvn-marine', ...);
}

// 3. Register — trong admin_init
function skvn_marine_blocks_register_xxx_settings() {
    register_setting(
        'skvn_marine_blocks_xxx_settings',   // group — dùng trong settings_fields()
        'skvn_xxx_option',                    // option name
        ['type' => 'array', 'sanitize_callback' => '...', 'default' => ...]
    );
    add_settings_section('...', '...', 'callback', 'skvn-marine-xxx-settings');
    // skvn-marine-xxx-settings = page slug — dùng trong do_settings_sections()
    add_settings_field('field_id', 'Label', 'field_callback', 'skvn-marine-xxx-settings', 'section_id', $args);
}

// 4. Page render
function skvn_marine_blocks_render_xxx_page() {
    ?>
    <form action="<?php echo esc_url(admin_url('options.php')); ?>" method="post">
        <?php
        settings_fields('skvn_marine_blocks_xxx_settings');  // group name
        do_settings_sections('skvn-marine-xxx-settings');     // page slug
        submit_button();
        ?>
    </form>
    <?php
}

// 5. Getter — luôn chạy qua sanitize
function skvn_marine_blocks_get_xxx() {
    return skvn_marine_blocks_sanitize_xxx(
        get_option('skvn_xxx_option', skvn_marine_blocks_get_default_xxx())
    );
}
```

### Hai giá trị KHÔNG được nhầm

- `settings_fields()` nhận **group name** = tham số đầu của `register_setting()`
- `do_settings_sections()` nhận **page slug** = tham số `$page` của `add_settings_section()`

### Flat option vs Array option

| Footer | Header / Typography |
|---|---|
| `register_setting` × n — mỗi option 1 lần | `register_setting` × 1 — whole array |
| `type: 'integer'`, `type: 'string'` | `type: 'array'` |
| `name="skvn_footer_page_id"` | `name="skvn_typography[palette][primary]"` |

---

## 6. Plugin vs Theme boundary cho settings

```
Plugin skvn-marine-blocks
├── modules/footer-settings/   → admin UI + save logic cho footer
├── modules/header-settings/   → admin UI + save logic cho header actions
└── modules/typography-settings/ → admin UI + save logic cho typography

Theme skvn-marine
├── inc/footer.php    → đọc option, render footer
├── inc/header-actions.php → đọc option, render header
└── inc/typography.php → đọc option, inject CSS vars
```

**Rule**: Plugin owns admin UI + `update_option`. Theme owns render/inject. Theme đọc cùng option key bằng cách define lại constant riêng (không import từ plugin).

```php
// Plugin
const SKVN_MARINE_BLOCKS_TYPOGRAPHY_OPTION = 'skvn_typography';

// Theme (same key, different constant name)
const SKVN_MARINE_TYPOGRAPHY_OPTION = 'skvn_typography';
```

---

## 7. GP Coupling Audit — Kết quả

Chạy audit toàn bộ theme, tìm được **2 GP hooks**:

| File | Coupling | Fix cần làm |
|---|---|---|
| `inc/header-actions.php:12` | `generate_after_header_content` | Bridge hook đã thêm |
| `inc/page-display-controls.php:27` | `generate_sidebar_layout` | Bridge filter đã thêm |
| `inc/woocommerce.php` | Clean — không có GP coupling | Không cần làm gì |

### Bridge pattern (đã implement)

```php
// header-actions.php
add_action('skvn_marine_after_header', 'skvn_marine_render_header_actions', 20);

if (defined('GENERATE_VERSION')) {
    add_action('generate_after_header_content', function() {
        do_action('skvn_marine_after_header');
    }, 20);
}

// page-display-controls.php
add_filter('skvn_marine_sidebar_layout', 'skvn_marine_page_display_sidebar_layout');

if (defined('GENERATE_VERSION')) {
    add_filter('generate_sidebar_layout', function($layout) {
        return apply_filters('skvn_marine_sidebar_layout', $layout);
    });
}
```

Khi 2.0.0 custom theme: xóa `if (defined('GENERATE_VERSION'))` blocks. Custom theme gọi `do_action('skvn_marine_after_header')` và `apply_filters('skvn_marine_sidebar_layout', $default)` ở đúng vị trí trong template.

---

## 8. Roadmap exit GeneratePress → 2.0.0

### Rule từ bây giờ đến 2.0.0

- **Không thêm GP hook mới**. Nếu feature mới cần hook GP, ghi tension.
- **Không hardcode GP class names** (`.inside-header`, `.grid-container`, `.site-container`) vào CSS mới.
- **Không để user save arbitrary hex** vào block attributes — attribute lưu palette slug, CSS var xử lý màu. Content portability khi migrate.

### Milestone sequence

| Milestone | Việc cần làm cho GP exit |
|---|---|
| 1.5.0 | Typography settings ✓, token bridge ✓, GP coupling audit ✓ |
| 1.x.0 | Quyết định 2.0.0 là full block theme hay classic custom theme |
| 1.x.0 | Woo hook wiring tự quản lý (tách khỏi GP hooks) |
| 1.x.0 | `woocommerce/single-product/add-to-cart/simple.php` — quote CTA override |
| Pre-2.0.0 | Template parts: header, footer, page layout container |
| 2.0.0 | Custom theme, xóa GP bridge blocks |

### Quyết định quan trọng nhất cần có trước 1.6.0

**2.0.0 là full block theme hay classic custom theme?**

- **Full block theme**: `templates/*.html`, Site Editor, Global Styles Variations — `/styles/*.json` hoạt động, marketing user có Browse Styles panel. WordPress direction.
- **Classic custom theme**: PHP templates, không có Site Editor, kiểm soát tốt hơn nhưng không có Global Styles UI.

Stack hiện tại (TypeScript blocks, `@wordpress/scripts`, `theme.json`) thiên về full block theme. Nhưng quyết định này ảnh hưởng format của template parts cần build từ bây giờ.

---

## 9. WooCommerce layer — Cần làm trước 2.0.0

GP đang handle Woo bằng hook wiring, không phải template files. Khi thoát GP, Woo fallback về default wrapper — layout vỡ trên toàn bộ Woo pages.

### Priority theo SKVN B2B context

| Surface | Risk | Priority |
|---|---|---|
| Single product + quote CTA | Vỡ + mất business flow | Critical |
| Shop / archive-product | Layout vỡ | Critical |
| Product card loop | Grid misaligned | High |
| Cart / Checkout / My Account | B2B ít dùng trực tiếp | Low |

### Files cần tạo

```
skvn-marine/
  woocommerce/
    single-product/
      add-to-cart/
        simple.php    ← thay "Add to Cart" = "Request Quote" CTA
    loop/
      pagination.php
  assets/css/
    woocommerce.css   ← load conditional: is_woocommerce()
```

### Hook wiring cần tự quản lý

```php
// inc/woocommerce.php — thêm vào
remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper');
remove_action('woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end');
add_action('woocommerce_before_main_content', 'skvn_marine_woo_wrapper_start');
add_action('woocommerce_after_main_content', 'skvn_marine_woo_wrapper_end');
```

---

## 10. Block Deprecation — Điểm mù cần theo dõi

Mỗi lần `save.tsx` thay đổi output HTML, tất cả posts có block đó trở thành invalid. WordPress hiện "unexpected or invalid content".

**Check ngay**: slider và accordion đang dùng static save hay dynamic render?

- **Dynamic render** (PHP `render_callback`): không có vấn đề, markup không lưu vào DB.
- **Static save**: cần thêm `deprecated` array trong block registration trước khi thay đổi markup.

```js
// Trong block registration
export default {
    ...settings,
    deprecated: [
        {
            attributes: oldAttributes,
            save: OldSaveComponent,
        }
    ]
};
```

---

## 11. CSS isolation — Plugin vs Theme

### Rule đã thống nhất

| Scope | Nằm ở đâu |
|---|---|
| Animation runtime | `src/shared/motion.ts` trong plugin |
| Block-specific CSS | `block.json style` của từng block |
| Shared CSS patterns | `src/shared/tokens.css` trong plugin |
| Visual token values | Theme feed vào qua CSS variable bridge |
| `--wp--preset--*` fallback | Luôn có — đảm bảo plugin chạy standalone |

**Debt hiện tại**: `assets/js/animations.js` vẫn đang ở theme. Cần move về `src/shared/motion.ts` trong plugin trước khi animation logic phức tạp hơn.

### Plugin CSS variable contract

```css
/* Plugin CSS — dùng wp preset với skvn fallback */
.skvn-slider__dot--active {
    background: var(--skvn-block-accent, var(--wp--preset--color--skvn-teal-600));
}

/* Theme bridge — feed brand token vào plugin var */
:root {
    --skvn-block-accent: var(--skvn-color-accent);
}
```

Plugin chạy standalone với `--wp--preset--*` fallback. Theme override qua `--skvn-block-*` vars.

---

## 12. Swiper version lock

Pin exact version trong `package.json`. Không dùng `^` range:

```json
"swiper": "11.x.x"
```

Swiper v8→v9→v10 đều có breaking API changes. Một `npm update` có thể silently break slider.

---

## 13. GP Customizer settings migration risk

Nếu site đang dùng GP Customizer settings (container width, header layout, font), các giá trị đó lưu trong `theme_mods` gắn với theme slug `generatepress`. Switch sang custom theme → mất toàn bộ, không auto migrate.

**Audit trước 2.0.0**:
```bash
wp option get theme_mods_generatepress --path=/mnt/d/Github/minhhaifish
```

Nếu kết quả có giá trị custom (không phải mặc định), cần plan migration trước khi switch theme.

---

## 14. Content portability — Rule cho block attributes

Marketing user **không được** save arbitrary hex vào block attributes:

```js
// Sai — lưu hex thô
setAttributes({ dotColor: '#0369a1' });

// Đúng — lưu palette slug
setAttributes({ dotColor: 'skvn-blue-700' });

// CSS render bằng preset var
style={{ '--dot-color': `var(--wp--preset--color--${dotColor})` }}
```

Khi exit GP và rebuild `theme.json`, chỉ cần đảm bảo slug còn tồn tại. Không phải đuổi theo từng block instance trong content.

---

## 15. Verify checklist cho 1.5.0

```bash
# PHP syntax
wsl -d Debian -- php -l /mnt/d/Github/skvn-marine/wp-content/plugins/skvn-marine-blocks/modules/typography-settings/typography-settings.php
wsl -d Debian -- php -l /mnt/d/Github/skvn-marine/wp-content/themes/skvn-marine/inc/typography.php

# Không còn GP coupling ngoài 2 bridge đã biết
grep -rn "generate_" /mnt/d/Github/skvn-marine/wp-content/themes/skvn-marine/inc/

# Token không bám wp preset trực tiếp trong component CSS
grep -n "var(--wp--preset--color" /mnt/d/Github/skvn-marine/wp-content/themes/skvn-marine/style.css
```

### Smoke test sau deploy

- Vào `SKVN Marine > Typography` trong WP admin
- Đổi Primary color → Save → Frontend reload: màu heading/button thay đổi
- Đổi H1 size → Save → Frontend reload: h1 size thay đổi
- Mở block editor: heading block color picker chỉ hiện SKVN palette, không có free hex
- Heading size dropdown chỉ hiện preset sizes, không có custom input
