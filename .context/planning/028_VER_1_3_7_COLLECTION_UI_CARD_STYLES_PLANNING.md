# 028 — V1.3.7 Collection Block UI & Card Styles Planning

**Milestone:** 1.3.7  
**Status:** BRAINSTORM / PLANNING IN PROGRESS  
**Design artifact:** `.local/Seafood Carousels.html` (sanitized: `.local/Seafood Export Carousels Tailwind.debug.html`)  
**Blocks in scope:** `skvn-marine/product-collection`, `skvn-marine/post-collection`

---

## 1. Bugs to Fix (confirmed, implement first)

### BUG-01 — `imageRatio` không emit class

**File:** `modules/collection-render/product-collection.php`, `modules/collection-render/post-collection.php`  
**Symptom:** Attribute `imageRatio` tồn tại trong block.json (default `"4:3"`), CSS có rules `.skvn-collection--ratio-4-3` v.v., nhưng PHP render không thêm class → ratio setting vô nghĩa.  
**Fix:** Thêm `$ratio = 'skvn-collection--ratio-' . str_replace(':', '-', $attributes['imageRatio'])` vào `$classes[]` trong cả 2 PHP render files.  
**Note:** DP-02 đã ghi trong PITFALLS.md (fixed 2026-06-18 cho product-collection — verify post-collection chưa fix).

### BUG-02 — Carousel arrows bị clip bởi `overflow:hidden`

**Files:** `modules/collection-render/cards.php`, `src/collection/style.css`  
**Symptom:** `.skvn-collection__arrow--prev { left: -1.25rem }` / `--next { right: -1.25rem }` đặt arrows ra ngoài container, nhưng Swiper wrapper có `overflow:hidden` → arrows không hiển thị.  
**Fix:** Wrap carousel trong outer element `.skvn-collection__carousel-outer` (position: relative, không có overflow:hidden). Arrows gắn vào outer, Swiper init target inner `.skvn-collection__carousel`.  
**Note:** DP-04 đã ghi trong PITFALLS.md (fixed 2026-06-18).

---

## 2. New Attributes (cả product-collection và post-collection)

### 2.1 Heading Controls

| Attribute | Type | Default | Notes |
|---|---|---|---|
| `showHeading` | boolean | `true` | Toggle toàn bộ heading block (eyebrow + H2 + intro) |
| `eyebrow` | string | `""` | Text nhỏ phía trên H2. Vd: "EXPORT CATALOG", "INSIGHTS" |

**Render path:**
- `block.json` → thêm attribute definition
- `edit.tsx` → TextControl cho `eyebrow`, ToggleControl cho `showHeading`
- `save.tsx` (nếu static) hoặc PHP render (dynamic block) → emit `<span class="skvn-collection__eyebrow">` khi `eyebrow` có value và `showHeading` true
- CSS: `.skvn-collection__eyebrow` — teal accent bar + uppercase tracking (ref design: `width:22px height:2px bg-teal` + label)

### 2.2 Catalog / Section CTA

**Quyết định (2026-06-18):** Toggle on/off + 1 URL field. URL do user tự điền: có thể là PDF trực tiếp hoặc lead gen page. Áp dụng cho cả product và post collection.

| Attribute | Type | Default | Notes |
|---|---|---|---|
| `showCatalogCta` | boolean | `false` | Toggle hiện/ẩn nút CTA trong section footer |
| `catalogCtaUrl` | string | `""` | URL — PDF link hoặc lead gen page |
| `catalogCtaLabel` | string | `"Tải catalog"` | Label text |

**Render path:**
- Footer bar của section: dots/pagination trái + CTA phải
- Khi `showCatalogCta: true` và `catalogCtaUrl` không rỗng → render `<a href="{url}" class="skvn-collection__catalog-cta btn-ghost">` (ghost button style)
- `catalogCtaLabel` fallback về "Tải catalog" nếu rỗng
- CSS: ghost button — border + text, hover: fill navy (ref design: `.btn-ghost`)

### 2.3 Archive CTA (footer)

| Attribute | Type | Default | Notes |
|---|---|---|---|
| `archiveUrl` | string | `""` | URL trang archive / xem tất cả |
| `archiveLabel` | string | `"Xem tất cả"` | Label text |

Đã trong scope Phase 1 của milestone, đây là reminder.

### 2.4 Intro text

`intro` attribute đã tồn tại trong block.json — verify PHP render emit đúng và CSS style đúng (ref design: `text-slate-500, max-width:560px, font-size:15px`).

---

## 3. Card Style Variants

### 3.1 Product Card

**Reference:** Design artifact Section 1 — `.prod-card`

Cấu trúc card (từ design, translate sang SKVN classes):

```
.skvn-product-card
├── .skvn-product-card__image          (aspect-ratio: 1/1, overflow:hidden)
│   ├── img (object-fit: cover)
│   └── .skvn-product-card__badge      (overlay top-left: "Wild Caught" / "Farmed")
├── .skvn-product-card__body           (flex-col, gap)
│   ├── h3 .skvn-product-card__title
│   ├── .skvn-product-card__specs      (flex-wrap, pill tags)
│   ├── .skvn-product-card__certs      (dot teal + label, flex-wrap)
│   ├── .skvn-product-card__stats      (MOQ | Lead Time — 2 col, divider)  ← placeholder, real data từ 1.5.0
│   ├── button .skvn-product-card__cta (full-width, "Yêu cầu báo giá")
│   └── a .skvn-product-card__pdf      (text link, "Tải spec sheet PDF")    ← placeholder, real data từ 1.5.0
```

**V1.3.7 scope:** Render structure + CSS. MOQ/Lead Time/certs/PDF = **placeholder display** dùng WooCommerce meta nếu có, fallback ẩn nếu không có. Real data model → 1.5.0.

**Category badge:** Overlay top-left trên ảnh (ref: `position:absolute; top:12px; left:12px`), thay vì bên dưới title.

### 3.2 Post Card

**Reference:** Design artifact Section 2 — `.art-card`

```
.skvn-post-card
├── .skvn-post-card__image             (aspect-ratio: 16/9, overflow:hidden)
│   ├── img (object-fit: cover)
│   └── .skvn-post-card__badge         (overlay top-left: category name)
├── .skvn-post-card__body              (flex-col, gap)
│   ├── span .skvn-post-card__date     (slate-400, 12px)
│   ├── h3 .skvn-post-card__title
│   ├── p .skvn-post-card__excerpt
│   └── a .skvn-post-card__read-more   ("Đọc thêm →", teal, hover gap animation)
```

---

## 4. Layout

### 4.1 4-column preset `4-2-1`

Thêm vào SelectControl options trong `edit.tsx` và CSS breakpoint rules cho cả product và post collection.

Ref design CSS:
```css
.c-card { flex: 0 0 calc((100% - 60px) / 4); }
@media (max-width: 1180px) { .c-card { flex-basis: calc((100% - 40px) / 3); } }
@media (max-width: 880px)  { .c-card { flex-basis: calc((100% - 20px) / 2); } }
@media (max-width: 600px)  { .c-card { flex-basis: 80%; } }
```

### 4.2 Section footer bar layout

```
[dots / pagination]         [catalogCta?]  [archiveUrl →]
```

- Dots trái: existing pagination dots (carousel mode) hoặc không hiện (grid mode)
- CTA phải: `catalogCta` (ghost button) + `archiveUrl` (arrow link)
- `justify-content: space-between`

---

## 5. Implementation Order

1. **BUG-01** — imageRatio PHP fix (verify cả 2 files)
2. **BUG-02** — Carousel arrows outer wrapper (PHP + CSS)
3. **Attribute additions** — block.json + edit.tsx + PHP render: `showHeading`, `eyebrow`, `showCatalogCta`, `catalogCtaUrl`, `catalogCtaLabel`, `archiveUrl`, `archiveLabel`
4. **Intro render fix** — verify `intro` attr render + style
5. **Card CSS** — product card structure + post card structure (CSS only, markup align với PHP render)
6. **Badge overlay** — move category badge từ bên dưới title lên overlay top-left trên ảnh
7. **4-col preset** — SelectControl + CSS
8. **Footer bar** — layout section footer với dots + catalog CTA + archive link
9. **Build + PHP lint**
10. **Onsite QA** — deferred to 1.3.9

---

## 6. Brainstorm Còn Mở (chưa chốt, cần session riêng)

- **Card style system:** Bao nhiêu variant? Attribute `cardStyle: 'default' | 'compact' | ...`? Hay chỉ 1 style trong 1.3.7?
- **Prefetch:** Hover-prefetch cho product card links? Đánh giá performance impact trước.
- **Image ratio per type:** Product default `1:1`, post default `16:9` — hardcode per block type hay user-selectable?
- **`badgeBehavior: 'overlay'`** — cần thêm enum value mới hay dùng CSS class trực tiếp?

---

## 7. Out of Scope (deferred)

- MOQ, Lead Time, certifications, spec sheet PDF — real data → **1.5.0 (`woo-catalog`)**
- Product Taxonomy Collections admin
- Faceted / AJAX filtering
- Per-block attribute injection từ catalog plugin
