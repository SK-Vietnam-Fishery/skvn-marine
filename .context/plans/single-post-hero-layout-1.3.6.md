# Plan: Single Post Hero Layout Fix — 1.3.6 Trục C

## Context

**Milestone:** V1 / 1.3.6 — Block Editor UX, Slider Parallax & Single Post Fix  
**Acceptance item:** `[ ] Hero .skvn-post-hero render đúng trong GP content area`

**Vấn đề:**  
Trên single post page, hero `.skvn-post-hero` render như một **column bên trái nhỏ** thay vì nằm đúng vị trí. Nguyên nhân: hero nằm NGOÀI `.skvn-single-layout` grid nhưng bên trong một ancestor GP đang apply flex/grid, khiến hero và `.skvn-single-wrap` xếp thành 2 columns.

**Target layout được confirm:**

```
┌─────────────────────┬───────────┐
│   .skvn-post-hero   │  sidebar  │  ← row 1: hero trái, sidebar phải (span 2 rows)
├─────────────────────┤           │
│  .skvn-single-main  │           │  ← row 2: content trái, sidebar tiếp tục
└─────────────────────┴───────────┘
```

Hero nằm TOP-LEFT của layout grid, sidebar span cả 2 rows.

**Future work (Customizer):** Full-width hero option — plan riêng, không implement 1.3.6.

---

## Approach: HTML Restructure + CSS Grid

Thay vì fix ancestor GP container (fragile), di chuyển `.skvn-post-hero` vào TRONG `.skvn-single-layout` grid và dùng CSS grid placement.

---

## Files Thay Đổi: 2 files

1. `wp-content/themes/skvn-marine/single.php`
2. `wp-content/themes/skvn-marine/style.css`

---

## Chi Tiết Thay Đổi

### File 1: `single.php`

**Trước** (hero nằm ngoài wrap):
```html
<!-- POST HERO -->
<div class="skvn-post-hero">...</div>

<!-- POST LAYOUT -->
<div class="skvn-single-wrap">
    <div class="skvn-single-layout">
        <main class="skvn-single-main">...</main>
        <aside class="skvn-single-sidebar">...</aside>
    </div>
</div>
```

**Sau** (hero được move vào trong grid, trước main):
```html
<!-- POST LAYOUT -->
<div class="skvn-single-wrap">
    <div class="skvn-single-layout">

        <!-- Hero nằm trong grid column 1, row 1 -->
        <div class="skvn-post-hero">...</div>

        <main class="skvn-single-main" id="main">...</main>

        <aside class="skvn-single-sidebar">...</aside>

    </div>
</div>
```

Xóa `<!-- POST HERO -->` block ở ngoài, đặt toàn bộ content của `.skvn-post-hero` vào vị trí mới trong grid.

### File 2: `style.css`

**Thay đổi `.skvn-single-layout` grid** để layout 3 items (hero + main + sidebar):

```css
/* Trước */
.skvn-single-layout {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 2.5rem;
    align-items: start;
}

/* Sau */
.skvn-single-layout {
    display: grid;
    grid-template-columns: 2fr 1fr;
    grid-template-rows: auto 1fr;
    gap: 2.5rem;
    align-items: start;
}
```

**Thêm grid placement rules:**

```css
/* Hero: column 1, row 1 */
.skvn-single-layout > .skvn-post-hero {
    grid-column: 1;
    grid-row: 1;
    min-height: 20rem;        /* giảm từ 24rem vì không full-width */
    border-radius: var(--skvn-radius-card);
    overflow: hidden;
}

/* Main: column 1, row 2 */
.skvn-single-layout > .skvn-single-main {
    grid-column: 1;
    grid-row: 2;
}

/* Sidebar: column 2, span cả 2 rows */
.skvn-single-sidebar {
    grid-column: 2;
    grid-row: 1 / 3;
}
```

**Responsive (≤900px):** hero, main, sidebar stack thành 1 column:
```css
@media (max-width: 900px) {
    .skvn-single-layout {
        grid-template-columns: 1fr;
        grid-template-rows: auto;
    }
    .skvn-single-layout > .skvn-post-hero,
    .skvn-single-layout > .skvn-single-main,
    .skvn-single-sidebar {
        grid-column: 1;
        grid-row: auto;
    }
}
```

---

## Không Thay Đổi

- `inc/page-display-controls.php` — không extend sang post type (defer)
- Không dùng `100vw`, không `overflow-x`, không `!important`
- CSS Layout Safety Contract: tuân thủ hoàn toàn
- Nội dung bên trong `.skvn-post-hero` không thay đổi (cùng template variables)

---

## Future Work (ghi nhận, không implement 1.3.6)

**Customizer global hero layout** (target 1.6.0 — brainstorm khi đến milestone):

Option A — Full-width hero (cần brainstorm thêm):
```
┌──────────────────────────────────┐
│         .skvn-post-hero          │  ← span cả 2 columns (grid-column: 1 / -1)
├──────────────────────┬───────────┤
│  .skvn-single-main   │  sidebar  │
└──────────────────────┴───────────┘
```

Option B — Content-width hero (layout hiện tại sau fix 1.3.6 — default):
```
┌─────────────────────┬───────────┐
│   .skvn-post-hero   │  sidebar  │
├─────────────────────┤           │
│  .skvn-single-main  │           │
└─────────────────────┴───────────┘
```

Implement qua WP Customizer → inject body class → CSS handles layout switch. Chi tiết brainstorm tại milestone 1.6.0.

> **Trigger 1.6.0:** Khi bắt đầu milestone 1.6.0, đọc plan này và brainstorm Customizer hero layout option trước khi code.

---

## Verification

1. **PHP lint:** `php -l wp-content/themes/skvn-marine/single.php`
2. **Onsite desktop:** load single post page
   - Hero nằm TOP-LEFT, sidebar nằm bên phải và span đủ chiều cao
   - Content nằm bên dưới hero trong cùng cột
   - Không horizontal scroll
3. **Onsite mobile (≤900px):** hero → main → sidebar stack dọc
4. **Regression:** load 1 Page (vd: /request-a-quote/) → layout không thay đổi

---

## Self-Check

- [x] Không sửa `themes/generatepress/`
- [x] Không rename namespace/prefix
- [x] Không dùng `100vw` / viewport units mới
- [x] Không dùng `overflow-x` để che geometry
- [x] Files thay đổi: 2 (≤ 5)
- [x] PHP sanitize/escape: không thay đổi output variables trong hero
- [x] Responsive mobile handled
