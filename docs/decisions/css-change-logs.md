# SKVN Marine — style.css: Tổng hợp thay đổi CSS

**File:** `wp-content/themes/skvn-marine/style.css`
**Scope:** Debug session — responsive layout fixes từ html-2-gutenberg output
**Milestone:** V1 / 0.5.1 — Page Display & Sidebar Controls
**Decision source:** Human-approved exception outside normal translation workflow, based on generating test data from real Tailwind web artifacts.

---

## Milestone decision

Các CSS fix trong file này thuộc V1 / 0.5.1 vì chúng harden visual contract đã được dùng để render page-level Gutenberg output trong lúc test HTML-2-Gutenberg với Tailwind artifacts thật.

Decision này không mở scope tooling mới:

- Không build HTML-2-Gutenberg admin/publisher trong theme.
- Không thêm dependency hoặc Tailwind production contract mới.
- Không chuyển brand profile/token system vào 0.5.1.
- Chỉ cho phép CSS hardening nhỏ khi có evidence từ artifact test thật và class đã nằm trong theme contract.

---

## Tóm tắt nhanh

| Thay đổi | Loại | Dòng hiện tại | Action |
|---|---|---|---|
| `overflow-wrap` cho `.skvn-section__heading` | Thêm property | line 929 | Thêm vào rule đã có |
| `overflow-wrap` cho `.skvn-translated-hero__heading` | Thêm property | line 848 | Thêm vào rule đã có |
| Base rule cho `.skvn-newsletter-media--overhang` | Rule mới | Trước @media 768px | Thêm mới |
| ~~`skvn-newsletter-band` implement~~ | Không cần | line 759 | Đã có, không đụng |
| ~~`skvn-stat-grid`, `skvn-stat-card`~~ | Không cần | line 1268 | Đã có, không đụng |
| ~~`skvn-card-grid--3`~~ | Không cần | line 1241 | Đã có, không đụng |

**Tổng thay đổi thực:** 4 dòng CSS thêm vào 3 rule. Không có class mới, không có token mới, không có file mới.

---

## Thay đổi 1 — Overflow protection cho headings

### Vị trí trong file

Thêm vào hai rule đã có:

- `.skvn-section__heading` (line 929)
- `.skvn-translated-hero__heading` (line 848)

### CSS cần thêm

```css
/* Thêm vào .skvn-section__heading (line 929) */
.skvn-section__heading {
    color: var(--skvn-color-blue-950);
    font-size: 1.85rem;
    font-weight: 750;
    letter-spacing: 0;
    line-height: 1.12;
    margin-block: 0 1rem;
    /* THÊM: */
    overflow-wrap: break-word;
    word-break: break-word;
}

/* Thêm vào .skvn-translated-hero__heading (line 848) */
.skvn-translated-hero__heading {
    color: var(--skvn-color-white);
    font-size: 2.25rem;
    font-weight: 800;
    letter-spacing: 0;
    line-height: 1.05;
    margin-block: 0 1.25rem;
    max-width: 42rem;
    /* THÊM: */
    overflow-wrap: break-word;
    word-break: break-word;
}
```

### Làm gì

Browser mặc định không tự xuống dòng giữa từ khi heading text dài. Ở viewport tablet (~768px) hoặc khi sidebar đang active thu hẹp content area, heading tràn sang trái/phải ra ngoài container.

`overflow-wrap: break-word` buộc browser xuống dòng khi không còn chỗ. Chỉ kích hoạt khi viewport hẹp — không ảnh hưởng desktop.

`word-break: break-word` là fallback cho Safari cũ không hiểu `overflow-wrap`.

### Tại sao hai class này

Đây là heading class được dùng trong mọi section dịch từ artifact HTML-2-Gutenberg. Bất kỳ heading dài nào trên mọi trang dùng pattern này đều được bảo vệ bằng một rule duy nhất.

### Triệu chứng gốc

"Prepare product, packing, and shipping details" bị tràn ra ngoài viền trái ở mobile viewport.

---

## Thay đổi 2 — Base rule cho `.skvn-newsletter-media--overhang`

### Vị trí trong file

Thêm mới vào trước responsive block `@media (min-width: 768px)`. Hiện file có responsive rules cho modifier này ở line 1092 và 1208 nhưng không có base rule.

### CSS cần thêm

```css
/* Thêm trước @media (min-width: 768px) — khoảng line 1050 */
.skvn-newsletter-media--overhang {
    overflow: hidden;
    border-radius: var(--skvn-radius-card);
}
```

Hai responsive rules hiện có **giữ nguyên**, không sửa:

```css
/* Giữ nguyên — line 1092 */
@media (min-width: 768px) {
    .skvn-newsletter-media--overhang {
        margin-block: -2.5rem; /* hiệu ứng nhô ra có chủ ý ở desktop */
    }
}

/* Giữ nguyên — line 1208 */
@media (max-width: 767px) {
    .skvn-newsletter-media--overhang {
        margin-block: 0; /* reset về flat ở mobile */
    }
}
```

### Làm gì

Modifier `--overhang` tạo hiệu ứng image column cao hơn container 2.5rem ở desktop (`margin-block: -2.5rem`). Đây là thiết kế có chủ ý.

Vấn đề: khi image nhô ra khỏi container, nếu không có `overflow: hidden` trên wrapper, image tràn ra ngoài viewport ở breakpoint trung gian hoặc khi sidebar active. `overflow: hidden` clip phần nhô ra, giữ layout sạch.

`border-radius: var(--skvn-radius-card)` đồng bộ bo góc image column với token chuẩn của theme — tránh image vuông sắc cạnh khi clip.

### Triệu chứng gốc

Image "Quote Image" tràn ra ngoài viền phải section ở tablet viewport.

---

## Những gì KHÔNG thay đổi — và tại sao

### `skvn-newsletter-band` — đã có CSS đầy đủ

```css
/* line 759–790 — ĐÃ CÓ, không đụng */
.skvn-newsletter-band {
    background: var(--skvn-color-blue-900);
    color: var(--skvn-color-white);
    margin-block: 3rem;
    overflow: visible;
    padding-block: 2rem;
}
.skvn-newsletter-band__inner {
    align-items: center;
    gap: 1.5rem;
    margin-bottom: 0;
}
```

LLM trong session debug đề xuất implement lại class này — sai. Class đã tồn tại với đầy đủ background, color, padding.

**Lưu ý nhỏ:** `.skvn-newsletter-band__inner` không có `display` tường minh — layout phụ thuộc Gutenberg columns wrapper bên ngoài tạo flex context. Nếu markup dùng inner như container độc lập, sẽ cần thêm `display: flex`. Hiện tại không thay đổi vì chưa có evidence từ DOM thực.

### `skvn-stat-grid`, `skvn-stat-card` — đã có CSS

```css
/* line 1268–1305 — ĐÃ CÓ */
.skvn-stat-grid { display: grid !important; grid-template-columns: repeat(2, 1fr); ... }
.skvn-stat-card { background: var(--skvn-color-white); border: ...; ... }
.skvn-stat-card__number { font-size: clamp(2rem, 4vw, 2.75rem); ... }
.skvn-stat-card__label { font-size: 0.8125rem; ... }
```

### `skvn-card-grid--3` — đã có CSS với responsive đầy đủ

```css
/* line 1241–1263 — ĐÃ CÓ */
.skvn-card-grid.skvn-card-grid--3 { display: grid !important; grid-template-columns: repeat(3, 1fr); ... }
/* responsive: 3 col → 2 col (1024px) → 1 col (640px) */
```

---

## Tokens hiện có trong `:root` — để tham khảo khi viết CSS mới

```css
/* Màu sắc */
--skvn-color-blue-950: #082f49   /* navy — dùng cho text, background dark */
--skvn-color-blue-900: #0c4a6e   /* dark blue — newsletter-band, map-contact card */
--skvn-color-blue-700: #0369a1   /* primary blue — button, link, kpi-strip */
--skvn-color-mint-100: #ddfaf4   /* mint — support accent */
--skvn-color-gold-300: #e9c766   /* gold — premium accent, kpi heading */
--skvn-color-teal-600: #0d9488   /* teal */
--skvn-color-sky-50:   #f0f9ff   /* light blue — soft section bg */
--skvn-color-slate-700: #334155  /* body text */
--skvn-color-white:    #ffffff

/* Alias */
--skvn-accent-support: var(--skvn-color-mint-100)
--skvn-accent-premium: var(--skvn-color-gold-300)

/* Spacing/radius/shadow */
--skvn-radius-card: 8px
--skvn-shadow-card: 0 10px 30px rgba(8, 47, 73, 0.1)
--skvn-shadow-card-hover: 0 4px 8px rgba(8, 47, 73, 0.1), 0 12px 24px rgba(8, 47, 73, 0.14)
--skvn-border: rgba(51, 65, 85, 0.14)

/* Layout widths */
--skvn-content-width: 760px    /* column content — KHÔNG phải 1200px */
--skvn-wide-width:   1200px    /* full wide layout */
```

**Tokens KHÔNG tồn tại** (đã dùng sai trong session debug):
- ~~`--skvn-color-navy`~~ → dùng `--skvn-color-blue-950`
- ~~`--skvn-space-xl`~~ → dùng `4rem` trực tiếp
- ~~`--skvn-space-md`~~ → dùng `1.5rem` trực tiếp

---

## Lessons learned cho html-2-gutenberg skill

### L1 — Scan file thực trước khi kết luận class missing

LLM debug session báo nhiều class là "missing" trong khi chúng đã có trong file. Rule bắt buộc: chạy scan thực tế trước khi ghi vào `missing_theme_classes`.

```powershell
Select-String -Path wp-content\themes\skvn-marine\style.css -Pattern "\.skvn-newsletter|\.skvn-stat|\.skvn-card-grid" -Encoding UTF8
```

### L2 — Dùng đúng token name

Token navy trong SKVN là `--skvn-color-blue-950`, không phải `--skvn-color-navy`. Token width cho wide layout là `--skvn-wide-width: 1200px`, không phải `--skvn-content-width` (760px). Luôn đọc `:root` trong file trước khi viết CSS mới.

### L3 — Class có responsive rule nhưng thiếu base rule

`skvn-newsletter-media--overhang` có `@media (min-width: 768px)` và `@media (max-width: 767px)` nhưng không có base CSS. Khi chỉ có responsive rules mà không có base, behavior ở breakpoint trung gian hoặc khi layout bị thay đổi ngoài dự kiến (sidebar active) là undefined.

Pattern an toàn: **luôn viết base rule trước, responsive rule sau**.

### L4 — Overflow protection là prerequisite của mọi heading dài

Heading text dài là edge case xảy ra ở mọi section. `overflow-wrap: break-word` phải là property mặc định của tất cả heading class trong theme, không phải fix per-case.

---

## Checklist tự kiểm trước khi submit CSS

```
[ ] Đã scan file để xác nhận class chưa tồn tại trước khi tạo mới
[ ] Token name khớp với :root (không dùng alias tự tạo)
[ ] --skvn-content-width (760px) và --skvn-wide-width (1200px) dùng đúng ngữ cảnh
[ ] Base rule có trước responsive rule
[ ] Không thêm class family mới khi có class tương tự đã implement
[ ] overflow-wrap đã có trong heading class nếu text có thể dài
[ ] Không sửa themes/generatepress/
[ ] Không thay đổi hơn 5 files
```
