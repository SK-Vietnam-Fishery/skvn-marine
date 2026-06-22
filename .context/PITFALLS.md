# PITFALLS.md — SKVN Marine

> Bộ sưu tập lỗi thực tế đã xảy ra trong quá trình phát triển.
> Mục đích: agent và dev đều đọc trước khi làm việc liên quan để tránh lặp lại.
> Không xóa entry — chỉ thêm `Fixed:` khi đã có giải pháp kiến trúc lâu dài.

---

## Agent Pitfalls (lỗi quy trình của AI agent)

### AP-01 — Edit tool yêu cầu Read trước

**Xảy ra:** 2026-06-17, session sửa `.skvn-post-hero` trong `style.css`.
**Triệu chứng:** Agent gọi Edit tool mà chưa Read file → tool báo lỗi "file not read".
**Nguyên nhân:** Agent quên rằng Edit tool enforce điều kiện đọc trước khi viết.
**Tránh:** Với bất kỳ file nào chưa đọc trong session hiện tại, Read trước rồi mới Edit.

---

### AP-02 — Incomplete diff khi reorder JSX

**Xảy ra:** 2026-06-17, reorder `InspectorControls` và `<BlockEdit>` trong `button-hover/index.tsx`.
**Triệu chứng:** Edit đầu chỉ di chuyển opening tag của `InspectorControls` lên trước, nhưng quên di chuyển `<BlockEdit {...props} />` xuống sau → cấu trúc JSX sai, cần edit lần 2.
**Nguyên nhân:** Lên kế hoạch edit từng phần thay vì hình dung toàn bộ diff trước.
**Tránh:** Trước khi reorder code blocks, đọc lại toàn bộ hàm/component và viết mental diff hoàn chỉnh. Nếu cần 2+ edits trên cùng một block JSX, gộp lại thành 1 lần.

---

### AP-03 — Thêm property mà không kiểm tra existing display rule

**Xảy ra:** 2026-06-17, thêm `width: 100%` vào `.skvn-post-hero` trong `style.css`.
**Triệu chứng:** Agent sửa rule thành `display: block` (xóa mất `display: flex; align-items: flex-end`) khi chỉ cần thêm `width: 100%`. Phải sửa lại ngay.
**Nguyên nhân:** Khi thêm 1 property vào CSS rule, agent không giữ nguyên toàn bộ rule cũ mà vô tình overwrote.
**Tránh:** Khi thêm property vào CSS rule đã có, luôn copy toàn bộ rule cũ vào `old_string` và `new_string` — chỉ thêm dòng mới, không bao giờ xóa dòng cũ nếu không được yêu cầu.

---

## Dev / Code Pitfalls (lỗi kiến trúc và code)

### DP-01 — Block attribute thêm vào `block.json` + `edit.tsx` nhưng quên `save.tsx`

**File liên quan:** `src/slider/save.tsx`, `src/slider/block.json`, `src/slider/edit.tsx`
**Xảy ra:** 2026-06-17, attribute `heightPreset` tồn tại trong block.json và edit.tsx type/className, nhưng `save.tsx` có `SliderAttributes` type riêng (maintained manually) không bao gồm `heightPreset`.
**Hậu quả:**
- Frontend HTML không bao giờ nhận class `skvn-slider--height-viewport-below-header`
- CSS height rule không áp dụng
- `view.ts` đọc `heightPreset` từ `data-skvn-slider` JSON nhưng JSON cũng thiếu field này → luôn fallback về `'default'`
- `syncViewportHeight()` bị skip hoàn toàn

**Pattern gốc:** `save.tsx` dùng type riêng thay vì import từ shared types, nên diverge khỏi `edit.tsx`.

**Checklist khi thêm attribute mới vào block:**
1. `block.json` — thêm attribute definition
2. `edit.tsx` — thêm vào type + control UI
3. `save.tsx` — thêm vào type + className/markup/data-attribute (check từng output)
4. Nếu dynamic block → PHP render: thêm vào `$classes` hay attribute truyền xuống view
5. `view.ts` (nếu có) — verify attribute được đọc từ `data-*` JSON

---

### DP-02 — Block attribute maps to CSS class nhưng PHP render không thêm class

**File liên quan:** `modules/collection-render/product-collection.php`, `modules/collection-render/post-collection.php`, `src/collection/style.css`
**Xảy ra:** 2026-06-17, attribute `imageRatio` định nghĩa trong block.json (`default: "4:3"`), CSS có đầy đủ rules `.skvn-collection--ratio-4-3`, `.skvn-collection--ratio-3-2`... nhưng PHP render không bao giờ thêm `skvn-collection--ratio-{value}` vào `$classes`.
**Hậu quả:** User thay đổi ratio trong editor → không có tác dụng gì trên frontend.

**Fixed:** 2026-06-18 — Thêm `$ratio = 'skvn-collection--ratio-' . str_replace( ':', '-', $attributes['imageRatio'] )` vào cả 2 PHP render files và include trong `array_filter( $classes )`.

**Pattern gốc:** CSS rules được viết sẵn cho feature chưa được wire đầy đủ.

**Checklist cho dynamic block (PHP render):**
- Mỗi attribute ảnh hưởng đến appearance phải có đường đi rõ ràng: `$attributes[key]` → `$classes[]` hoặc inline style hoặc data attribute
- Sau khi thêm CSS class rule mới, grep PHP render để confirm class đó được emit

---

### DP-03 — CSS scope sai: component property đặt ở `body` level

**File liên quan:** `wp-content/themes/skvn-marine/style.css`
**Xảy ra:** 2026-06-17, rule `body.skvn-has-footer-page { background: var(--skvn-footer-bg); }` paint toàn bộ `<body>` bằng màu footer.
**Hậu quả:** Mọi trang có footer page đều có nền navy — content area, sidebar, header background đều bị ảnh hưởng.
**Fix:** Xóa rule này. Footer element tự có `background` riêng qua `.skvn-footer-page` và `.skvn-site-footer`.

**Rule chung:** `body.skvn-*` class chỉ nên set CSS custom properties (variables) hoặc layout properties ảnh hưởng toàn trang (overflow, scroll behavior). Không set `background`, `color`, hay visual properties thuộc về component con.

---

### DP-04 — Carousel arrows đặt ngoài `overflow: hidden` container

**File liên quan:** `src/collection/style.css`, `modules/collection-render/cards.php`
**Xảy ra:** Phát hiện 2026-06-17 trong code review.
**Triệu chứng:** `.skvn-collection__arrow--prev { left: -1.25rem; }` và `--next { right: -1.25rem; }` đặt arrows ra ngoài container, nhưng `.skvn-collection__carousel` có `overflow: hidden` (Swiper yêu cầu) → arrows bị clip, không bao giờ hiển thị.

**Fixed:** 2026-06-18 — Thêm outer wrapper `.skvn-collection__carousel-outer` (position: relative, no overflow: hidden). Arrows và `data-skvn-collection-carousel` chuyển sang outer wrapper. Swiper init (`new Swiper(swiperEl, ...)`) chỉ target inner `.skvn-collection__carousel`. Arrows được outer wrapper contain → không bị clip.

**Rule chung:** Trước khi position element ra ngoài container (negative inset, `transform: translate` ra ngoài bounds), kiểm tra `overflow` của container và mọi ancestor. Swiper wrapper luôn cần `overflow: hidden` → arrows phải nằm ngoài wrapper div, hoặc dùng padding + negative margin trick trên parent.

---

## Patterns cần chú ý (không phải lỗi, nhưng dễ bỏ sót)

### PP-01 — Re-save block sau khi sửa `save.tsx`

Khi `save.tsx` thay đổi output (thêm class, thêm attribute), block đã lưu trong DB có markup cũ. WordPress sẽ báo "block validation failed" và offer recovery.
**Workflow:** Sau khi build plugin, vào editor của mọi trang có block đó → re-save để regenerate markup. Cần làm cho tất cả blocks bị ảnh hưởng.

---

### PP-02 — `view.ts` đọc config từ `data-skvn-*` JSON — attribute phải được thêm vào JSON trong `save.tsx`

Xem DP-01. Nếu `view.ts` cần đọc 1 attribute, attribute đó phải có trong `data-skvn-slider` (hoặc equivalent) JSON được emit bởi `save.tsx`. Chỉ thêm vào `SliderAttributes` type thôi chưa đủ — phải thêm vào object trong `JSON.stringify({...})`.
