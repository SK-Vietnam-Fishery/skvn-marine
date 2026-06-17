# SKVN Marine — Kế hoạch cải thiện trang Post, Product & Archive

**Phiên bản:** 2.0  
**Milestone:** 1.3.5  
**Ngày cập nhật:** 2026-06-17  
**Context:** GeneratePress parent + skvn-marine child theme + skvn-marine-blocks plugin.

---

## 1. Quyết định đã chốt

| Vấn đề | Quyết định |
|--------|------------|
| Comment section (Single Post) | Ẩn hoàn toàn qua PHP filter |
| Reviews tab (Single Product) | Bỏ hoàn toàn qua PHP filter |
| Quote CTA button | Primary, full-width, nổi bật — nằm trong sidebar island |
| Product image placeholder | Navy background + text "Hình sắp cập nhật" |
| Related products | Giữ lại, restyle card grid |
| Archive layout | Style C Hybrid: featured post lớn + card grid bên dưới |
| Single Post layout | Style C: hero image + content + sidebar phải |
| Single Product layout | Gallery trái + details phải (WooCommerce), sidebar phải |
| Sidebar vị trí | Bên **phải**, dạng **island** (isolated zone) |
| Sidebar đóng | Full-width content (không JS toggle — Option A) |
| Tabs Product | Mô tả / Thông số kỹ thuật / Tài liệu & Chứng nhận (không có Reviews) |
| Font | Theo `--skvn-font-*` tokens, configurable qua Customizer (xem mục 3) |
| Layout sizing | Dùng `fr` / `%` theo Gutenberg content-size, không hardcode px/em |
| Labels UI | Tiếng Việt (B2B context thực tế của dự án) |
| Trust signals | VSATTP, HACCP, Cold Chain Certified, Bảo hành 24 tháng |

**Design artifacts tham khảo** (trong `docs/artifacts/`):
- `post-archive-style-C-hybrid.html` — archive layout đã duyệt
- `single-post-style-C.html` — single post layout đã duyệt
- `single-product-style-C.html` — single product layout đã duyệt
- `font-compare-b2b.html` — 4 font option để quyết định

---

## 2. Thứ tự thực hiện

```
Step 1: Font Customizer control          ← infrastructure, ~1 file
Step 2: CSS token bridge fix             ← --skvn-color-* → --wp--preset-- bridge
Step 3: Archive page (post grid)         ← Style C Hybrid
Step 4: Single Product page              ← conversion priority
Step 5: Single Post page                 ← content marketing
```

Step 1–2 là infrastructure foundation. Step 3–5 là visible output và có thể làm song song sau khi Step 1–2 xong.

---

## 3. Step 1 — Font Customizer Control

**Mục tiêu:** Cho phép thay đổi font qua Appearance → Customize mà không cần chạm code.

**Cơ chế:**
- WP Customizer control (radio/select) → lưu `get_option()`
- PHP output `<link>` Google Fonts (nếu cần) + `<style>` inline ghi đè `--skvn-font-heading` / `--skvn-font-body` trên `:root`
- Plugin và theme đọc token như bình thường — không cần đổi gì ở plugin

**4 preset:**

| Key | Heading | Body | Google Fonts |
|-----|---------|------|--------------|
| `instrument` **(default)** | `'Instrument Serif', Georgia, serif` | `system-ui, sans-serif` | Instrument Serif |
| `lora-inter` | `'Lora', Georgia, serif` | `'Inter', system-ui, sans-serif` | Lora + Inter |
| `barlow` | `'Barlow', system-ui, sans-serif` | same | Barlow |
| `system` | `system-ui, -apple-system, sans-serif` | same | Không cần |

**Files chạm:** `functions.php` (hoặc `inc/customizer.php` nếu tách file)  
**Files không chạm:** Plugin, GeneratePress parent, `theme.json`

---

## 4. Step 2 — CSS Token Bridge Fix

**Vấn đề hiện tại:**
- `style.css` `:root` định nghĩa `--skvn-color-*` là hex hardcoded — không kết nối với `--wp--preset--color--*`
- `theme.json` fontFamilies reference `var(--skvn-font-body)` — sai chiều (circular)

**Fix Option A (đã chọn):**
```css
/* style.css — bridge skvn tokens → WP preset vars */
:root {
  --skvn-color-primary:   var(--wp--preset--color--skvn-blue-600,   #0284C7);
  --skvn-color-navy-950:  var(--wp--preset--color--skvn-blue-950,   #082F49);
  /* v.v. cho từng màu */
}
```
Chiều đúng: WP preset (source of truth từ theme.json) → skvn tokens (consumed bởi plugin + theme CSS).

**Files chạm:** `style.css`

---

## 5. Step 3 — Archive Page (Style C Hybrid)

**Layout:**
- Full: featured post lớn (2-col: image + content) + card grid 3-col bên dưới
- Sidebar open: content `2fr` + sidebar `1fr`
- Sidebar closed: full-width, card grid 3-col

**Files chạm:**
- `archive.php` (tạo mới trong child theme)
- `style.css` (archive component styles)

**Không chạm:** `archive-product.php` (WooCommerce shop page — separate concern)

**PHP hooks cần:**
- `comments_open` filter — ẩn comment cho `post` type

---

## 6. Step 4 — Single Product Page (Style C)

**Layout:**
- Gallery trái `1fr` + Product details phải `1fr` (tỉ lệ, không px cứng)
- Sidebar phải `1fr` (khi open): Hotline island + Certifications + Hỗ trợ
- Quote CTA: primary button full-width, dưới giá/SKU
- Trust signals row: VSATTP / HACCP / Cold Chain / Bảo hành
- Tabs: Mô tả / Thông số kỹ thuật / Tài liệu & Chứng nhận
- Related products: `2fr` khi sidebar open, 4-col khi closed

**Sidebar implementation: Gutenberg Pattern (Option 2)**
- Đăng ký patterns PHP trong `inc/patterns.php` child theme
- Pattern category: `skvn-marine/sidebar`
- 3 patterns: Hotline Island, Chứng nhận sản phẩm, Hỗ trợ mua hàng
- Hotline number color: `var(--skvn-color-accent)` — không hardcode vàng/gold như trong artifact
- Client insert vào sidebar widget area trong Gutenberg editor, edit text trực tiếp
- Không hardcode — hotline/certifications/support info khác nhau theo từng client/site

**Files chạm:**
- `inc/woocommerce.php` (hoặc `functions.php`) — filter bỏ Reviews tab, placeholder hook
- `inc/patterns.php` — sidebar patterns
- `style.css` — product page component styles + sidebar pattern styles
- `woocommerce/single-product/related.php` — nếu cần thay markup card

**PHP hooks cần:**
```php
// Bỏ Reviews tab
add_filter('woocommerce_product_tabs', fn($tabs) => array_diff_key($tabs, ['reviews' => '']), 98);

// Branded placeholder
add_filter('woocommerce_placeholder_img', fn() => '<div class="skvn-product-placeholder">Hình sắp cập nhật</div>');
```

---

## 7. Step 5 — Single Post Page (Style C)

**Layout:**
- Hero image full-width với navy gradient overlay + title
- Content area + Sidebar phải (Quote CTA island + Related articles + Categories)
- Sidebar closed: CTA banner ngang cuối bài + related posts 3-col
- Author row: avatar + tên + role + read time
- Breadcrumb, tags, callout box

**Files chạm:**
- `functions.php` — ẩn comment
- `style.css` — single post component styles
- `single.php` (tạo/sửa trong child theme nếu cần thay DOM structure)

---

## 8. Open Points còn lại

1. ~~**Font choice**~~ — **Chốt: Option A (Instrument Serif)**. Client là xuất nhập khẩu thủy sản cao cấp (grouper, mahi-mahi), positioning gần luxury food brand hơn industrial equipment.
2. **Archive header description** — generic text hay per-category? Cần copy từ marketing
3. **Product archive (shop page)** — có áp style C không hay để sau?
4. **JSON-LD schema** — Product page SEO, khuyến nghị có nhưng chưa scope
5. **Trust signals** — V1 dùng CSS static hay taxonomy động (V2)?

---

## 9. Kiến trúc & Invariants

- CSS token cascade đúng: **Plugin CSS (fallback)** → **Theme style.css (override)** → **GP Customizer (future)**
- Plugin phải có khả năng hoạt động độc lập — xem A13 trong `.context/GLOBAL.md`
- Dependency plugin → theme token là technical debt chấp nhận có chủ ý, phải ghi chú tại chỗ
- Không hardcode px/em cho layout sizing — dùng `fr`, `%`, hoặc WP global content-size vars
- Không sửa GeneratePress parent
- Sidebar là "island" pattern — isolated, self-contained zone
- `prefers-reduced-motion` bắt buộc cho mọi animation

---

## 10. Deliverables V1

- `inc/customizer.php` — font Customizer control (hoặc trong `functions.php`)
- `style.css` — token bridge + component styles (archive, post, product)
- `functions.php` — PHP hooks (hide comments, remove reviews tab)
- `archive.php` — custom archive template
- `woocommerce/single-product/related.php` — nếu cần override markup
- Visual QA: screenshot desktop + mobile cho 3 page types

---

## 11. Defer V2+

- Filter / sort bar dynamic (Post Collection block trong plugin)
- Sidebar content dynamic (custom blocks từ plugin)
- Trust signals taxonomy + badges động
- Full i18n + Polylang
- Product archive (shop page) style
- JSON-LD schema markup
- A/B test Quote button position
