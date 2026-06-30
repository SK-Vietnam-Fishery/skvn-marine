# Gutenberg Block Extension CSS Contract

Áp dụng cho mọi feature mở rộng `core/*` block qua PHP filter + Gutenberg JS filter
(không phải custom block `skvn-marine/*`).

**Case study gốc:** Core Button Hover Colors — 2 agent mắc lỗi, feature không hoạt động
mà không có error. Document này rút ra từ retrospective
`.context/planning/archives/033_VER_1_3_8_CORE_BUTTON_HOVER_FIX_PLAN.md`.

---

## Quy tắc 1 — Specificity trước, CSS sau

**Trước khi viết bất kỳ rule `:hover`, `:focus`, hay pseudo-class nào cho plugin:**

1. Mở DevTools → Elements → chọn element target
2. Hover element (hoặc force state) → Styles panel → liệt kê tất cả rules đang apply
3. Tìm rule theme/external có **hard-coded value** (không phải `var()`) với specificity cao nhất
4. Plugin rule **phải có specificity ≥ rule đó**

```
Theme rule: .wp-block-button.skvn-button--primary .wp-block-button__link:hover → 0,3,1
Plugin cần: ≥ 0,3,1
Dùng:       .wp-block-button.has-skvn-button-hover .wp-block-button__link:hover → 0,3,1 ✅
```

Nếu plugin rule thấp hơn → vars inject đúng nhưng màu không đổi → **silent failure**.

### Cách đạt specificity đủ mà không dùng `!important`

Thêm một scoped class vào wrapper element qua PHP filter, rồi dùng class đó trong selector:

```php
// PHP: thêm class vào <div class="wp-block-button">
if ( ! str_contains( $classes, 'has-skvn-button-hover' ) ) {
    $classes .= ' has-skvn-button-hover';
}
```

```css
/* CSS: class trên cùng element → tăng specificity mà không dùng !important */
.wp-block-button.has-skvn-button-hover .wp-block-button__link:hover { … }
```

---

## Quy tắc 2 — Editor DOM ≠ Frontend DOM

Đây là lỗi hay gặp nhất khi dùng `editor.BlockListBlock` filter.

### Frontend DOM (PHP render)

PHP filter `render_block_core/button` inject class trực tiếp vào `<div class="wp-block-button">`:

```html
<!-- Frontend: class và element CÙNG chỗ ✅ -->
<div class="wp-block-button has-skvn-button-hover" style="--skvn-btn-hover-text:#fff">
  <a class="wp-block-button__link">Button</a>
</div>
```

Selector `.wp-block-button.has-skvn-button-hover .wp-block-button__link:hover` **match** ✅

### Editor DOM (BlockListBlock wrapperProps)

`editor.BlockListBlock` inject `className` vào **outer wrapper** do Gutenberg tạo,
không phải vào `.wp-block-button`:

```html
<!-- Editor: class ở NGOÀI .wp-block-button ❌ cho selector frontend -->
<div class="block-editor-block-list__block has-skvn-button-hover" style="--skvn-btn-hover-text:#fff">
  <div class="wp-block-button">          <!-- ← không có class -->
    <a class="wp-block-button__link">Button</a>
  </div>
</div>
```

Selector `.wp-block-button.has-skvn-button-hover` **KHÔNG match** trong editor.

### Rule bắt buộc: hai selector cho hai context

| Context | Selector | Specificity |
|---|---|---|
| Frontend CSS (PHP `wp_add_inline_style`) | `.wp-block-button.has-skvn-button-hover .wp-block-button__link:hover` | 0,3,1 |
| Editor CSS (`src/*/style.css` import) | `.has-skvn-button-hover .wp-block-button .wp-block-button__link:hover` | 0,2,1 |

Editor CSS không cần specificity cao — không có theme rules xung đột trong editor iframe.

**CSS vars vẫn inherit** từ outer wrapper xuống tất cả descendants — đó là lý do
injecting vars qua `wrapperProps.style` vẫn hoạt động; chỉ consuming selector cần khác.

---

## Quy tắc 3 — Bundle boundary

### Không bao giờ enqueue plugin bundle cho feature CSS nhỏ

```php
// ❌ SAI: load toàn bộ plugin CSS (slider, collection, showcase…)
wp_enqueue_style( 'my-feature', plugins_url('build/style-index.ts.css', …) );

// ✅ ĐÚNG: handle riêng với wp_add_inline_style
wp_register_style( 'skvn-marine-my-feature', false, array('skvn-marine-style'), null );
wp_enqueue_style( 'skvn-marine-my-feature' );
wp_add_inline_style( 'skvn-marine-my-feature', $css );
```

### Khi nào dùng `wp_add_inline_style` vs file riêng

| CSS | Cách |
|---|---|
| < 50 dòng, không có media queries phức tạp | `wp_add_inline_style` |
| > 50 dòng hoặc nhiều breakpoints | File `.css` riêng, handle riêng |
| Luôn | Không bao giờ dùng bundle chung |

### Dependency đảm bảo load order

```php
// Dependency vào theme stylesheet → plugin CSS load SAU theme → cascade đúng
wp_register_style( 'skvn-marine-my-feature', false, array( 'skvn-marine-style' ), null );
```

Không hardcode dependency nếu theme có thể không active — dùng `wp_style_is()` check trước:

```php
$deps = array();
if ( wp_style_is( 'skvn-marine-style', 'registered' ) || wp_style_is( 'skvn-marine-style', 'enqueued' ) ) {
    $deps[] = 'skvn-marine-style';
}
```

---

## Quy tắc 4 — `editor.BlockEdit` vs `editor.BlockListBlock`

| Filter | Dùng để | KHÔNG dùng để |
|---|---|---|
| `editor.BlockEdit` | Inject Inspector panel (sidebar) | Gắn style/class vào block wrapper |
| `editor.BlockListBlock` | Gắn `wrapperProps` (style, class) vào editor wrapper | Inject Inspector controls |

`wrapperStyle` build trong `editor.BlockEdit` là **dead code** — không được gắn vào DOM.
Không có error, không có warning.

```tsx
// ❌ KHÔNG LÀM — wrapperStyle không đi đâu cả
addFilter('editor.BlockEdit', …, (BlockEdit) => (props) => {
  const wrapperStyle = { '--my-var': value }; // dead code
  return <BlockEdit {...props} />;
});

// ✅ ĐÚNG — wrapperProps được gắn vào outer wrapper
addFilter('editor.BlockListBlock', …, (BlockListBlock) => (props) => {
  return <BlockListBlock {...props} wrapperProps={{ style: { '--my-var': value } }} />;
});
```

---

## Quy tắc 5 — Test phải verify behavior, không verify source

```javascript
// ❌ FALSE POSITIVE — pass kể cả khi feature không hoạt động
assert.match(phpSource, /--skvn-btn-hover-text:/);

// ✅ ĐÚNG — test transform thực sự
const output = mockRenderButtonHover(mockHtml, { skvnHoverTextColor: '#fff' });
assert.match(output, /has-skvn-button-hover/);
assert.match(output, /--skvn-btn-hover-text:#fff/);
assert.equal(mockRenderButtonHover(mockHtml, {}), mockHtml); // unchanged when no attrs
```

Mock function phải mirror đúng PHP transform logic. Nếu PHP đổi → mock phải đổi theo.

---

## Quy tắc 6 — Sanitize biết kiểu giá trị trước khi chọn hàm

| Giá trị | Hàm PHP | Ghi chú |
|---|---|---|
| Hex color `#rgb` / `#rrggbb` | `sanitize_hex_color()` | Drop 8-digit hex và rgba |
| Hex + 8-digit hex | Custom regex | WP không có hàm native |
| CSS gradient | Strip tags + character allowlist | Không dùng `sanitize_hex_color` |
| Màu từ Gutenberg palette | `sanitize_hex_color()` | Palette chỉ emit hex 6-digit |

`ColorPicker` với `enableAlpha` → emit `rgba(...)` → `sanitize_hex_color()` drop → silent failure.
Dùng `PanelColorGradientSettings` (palette-based) để tránh alpha tự do.

---

## Checklist trước khi submit block extension CSS

```
[ ] Đã check specificity của theme rule mạnh nhất cho element → plugin rule ≥ đó
[ ] PHP filter thêm scoped class vào wrapper element (cùng element với vars)
[ ] Frontend CSS selector khớp với DOM structure của PHP render
[ ] Editor CSS selector khớp với DOM structure của BlockListBlock (outer wrapper)
[ ] Không enqueue plugin bundle — dùng handle riêng + wp_add_inline_style hoặc file riêng
[ ] Dependency vào theme handle để đảm bảo load order
[ ] editor.BlockListBlock (không phải BlockEdit) cho wrapperProps
[ ] Test gọi transform function với mock HTML, assert output — không chỉ grep source
[ ] Sanitize PHP chọn đúng hàm cho kiểu giá trị (hex / gradient / palette)
[ ] Pivot cơ chế specificity phải atomic — bỏ cái cũ và thêm cái mới trong cùng commit
```
