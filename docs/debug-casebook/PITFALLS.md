# Agent & Layout Pitfalls — SKVN Marine

> Tổng hợp lỗi layout/UI đã gặp, cách tránh, và checklist bắt buộc trước khi
> agent sửa CSS/JS slider hoặc surface tương tự.
>
> Case chi tiết: xem `INDEX.md`. Phương pháp điều tra: `.agents/skills/ui-debug/SKILL.md`.

---

## Bảng nhanh

| Pitfall | Triệu chứng | Layer sở hữu | Case |
|---|---|---|---|
| Swiper JS không kèm core CSS | Slider trắng / slide xếp dọc sau init | WordPress enqueue | [CASE-001](slider/001_SWIPER_JS_WITHOUT_CORE_CSS.md) |
| Viewport height chỉ `min-height` trên slide | Container đủ cao, ảnh nền không full height | Plugin CSS + Swiper chain | [CASE-002](slider/002_VIEWPORT_BELOW_HEADER_IMAGE_NOT_FULL_HEIGHT.md) |
| Nhầm `text-align` với block center | Canh giữa trong editor, frontend vẫn lệch trái | Plugin CSS (hero preset) | [CASE-003](slider/003_HERO_TEXT_ALIGN_VS_BLOCK_CENTER.md) |
| Agent đọc code, không đo geometry | Fix “đúng spec” nhưng UI vẫn sai | Workflow / QA | § Agent anti-patterns |

---

## Pitfall 1 — Swiper runtime thiếu core CSS

**Triệu chứng:** Trước init thấy slide; sau init slider trắng hoặc slide xếp dọc.

**Nguyên nhân:** `view.ts.js` chạy nhưng `view.ts.css` (Swiper core) không được enqueue.

**Cách tránh:**

- Verify slider như **asset graph** (JS + Swiper CSS + SKVN CSS), không chỉ một file.
- Regression: `tests/slider-block.test.mjs` assert `build/view.ts.css` trong PHP bootstrap.

**Không làm:** Tăng opacity, retry Swiper init, hoặc che bằng `overflow: hidden` khi chưa có geometry đúng.

---

## Pitfall 2 — `Viewport below header` + ảnh không full height

**Triệu chứng:** Slider cao đúng viewport dưới header, nhưng `object-fit: cover` chỉ phủ một dải mỏng (theo chiều cao content).

**State Delta:**

```text
State A (ổn hơn):  Preset Medium/Tall — min-height cố định, gap nhỏ
State B (lỗi):      Viewport below header — min-height rất lớn (100dvh)
Delta:              Swiper .swiper-slide { height: 100% } cần parent có height xác định
```

**Nguyên nhân:** Chỉ set `min-height` trên `.skvn-slide`. Swiper wrapper/slide dùng `height: 100%` trong khi `.skvn-slider` không có `height` explicit → media absolute (`inset: 0`) fill theo box thực tế nhỏ hơn.

**Cách tránh:**

1. Viewport preset **own height** trên `.skvn-slider`, propagate `height: 100%` xuống wrapper + slide + `__media`.
2. Sau Swiper init, gọi lại `syncViewportHeight()` + `updateSize()` khi offset header/admin bar đổi.
3. **Đo geometry**, không chỉ đọc CSS:

```javascript
const slide = document.querySelector('.skvn-slide');
const media = slide?.querySelector('.skvn-slide__media');
const img = slide?.querySelector('.skvn-slide__background-image');
console.table({
  slide: slide?.getBoundingClientRect().height,
  media: media?.getBoundingClientRect().height,
  image: img?.getBoundingClientRect().height,
});
```

**PASS:** Ba giá trị gần bằng nhau và ≈ `innerHeight - headerOffset`.

**Acceptance tách biệt:**

- [ ] Container fill viewport below header
- [ ] Background media fill **100% computed slide box** (sau Swiper init)

**Không làm:** Chỉ thêm `min-height` lớn hơn mà không sửa height chain.

---

## Pitfall 3 — Hero “canh giữa” nhưng copy vẫn lệch trái

**Triệu chứng:** Editor set alignment Center; frontend copy block nằm lệch trái so với slide.

**Hai tầng alignment (không trộn):**

| Tầng | Cơ chế Gutenberg/CSS | Điều khiển gì |
|---|---|---|
| Glyph | `has-text-align-center` → `text-align: center` | Chữ trong block |
| Block | `margin-inline: auto`, `align-items: center` | Vị trí cột copy trong slide |

**Nguyên nhân:** Hero preset có `max-width: 48rem` + `align-items: flex-start` trên flex slide, **không** có `margin-inline: auto` trên children → cột 48rem dính trái. `text-align: center` chỉ canh chữ trong cột đó.

**Cách tránh:**

1. Hero copy column: `margin-inline: auto` trên `.skvn-slide__content > *`.
2. Slide flex: `align-items: center` (không `flex-start`) khi layout mặc định là hero center.
3. Tôn trọng override editor: `has-text-align-left/right`, `is-content-justification-left/right` trên `.wp-block-buttons`.
4. Verify optical center:

```javascript
const slide = document.querySelector('.skvn-slide');
const heading = slide?.querySelector('.wp-block-heading');
const slideCenter = slide.getBoundingClientRect().left + slide.offsetWidth / 2;
const blockCenter = heading.getBoundingClientRect().left + heading.offsetWidth / 2;
console.log('offset from slide center (px):', Math.round(blockCenter - slideCenter));
```

**PASS:** `|offset| < 8px` khi user chọn center.

**Không làm:** Chỉ thêm `text-align: center` khi bug là **block position**, không phải glyph alignment.

---

## Agent anti-patterns (vì sao lỗi “ngớ ngẩn” lặp lại)

### 1. Đọc rule local, bỏ qua integration contract

Markup layer (`__media`, `__content`) đúng spec 1.3.0 **không đủ** nếu thiếu contract với Swiper (`height: 100%` chain) hoặc hero flex alignment.

**Tránh:** Trước khi close task layout, liệt kê **owners**: Gutenberg → PHP render → plugin CSS → Swiper JS → theme canvas.

### 2. Pattern-match từ preset khác

`min-height` hoạt động “ổn” với Medium/Tall **không chứng minh** hoạt động với `100dvh` + Swiper init.

**Tránh:** State Delta phải ghi **preset / mode** gây lỗi, không chỉ “slider sai”.

### 3. Nhầm editor state với visual state

`has-text-align-center` trong block attributes ≠ block nằm giữa hero frame.

**Tránh:** Hỏi “center **chữ** hay center **cột**?” khi task liên quan hero/marketing copy.

### 4. Không có measurement loop

Agent không có retina; code “trông đúng” dễ được coi là done.

**Tránh:** Mọi layout fix phải kèm **một lệnh DevTools** hoặc checklist onsite trong `docs/testing/`; human gửi evidence trước khi archive case.

### 5. Acceptance mơ hồ

“Slider fills viewport” dễ pass khi chỉ container cao đúng, ảnh vẫn hở.

**Tránh:** Tách criteria: container geometry vs media fill vs copy alignment.

### 6. Sửa sớm trước State Delta

Nhảy vào `!important`, `overflow: hidden`, hoặc tăng `min-height` khi chưa isolate layer.

**Tránh:** Bắt buộc ISOLATE → DIFF → PROVE (ui-debug skill) cho mọi UI bug.

---

## Checklist agent — trước khi submit slider layout fix

```text
[ ] Đã xác định State A (đúng) và State B (sai) + Delta
[ ] Đã kiểm tra Swiper asset graph (CASE-001) nếu chạm view.ts / enqueue
[ ] Viewport preset: height chain trên .skvn-slider, không chỉ min-height slide
[ ] Hero alignment: block center (margin/flex) tách khỏi text-align
[ ] Đã đưa lệnh getBoundingClientRect() cho human verify
[ ] Regression guard trong tests/slider-block.test.mjs nếu thêm rule mới
[ ] Không dùng overflow-x/100vw để che geometry (css-layout-safety-contract)
```

---

## Regression guards hiện có

| Guard | File |
|---|---|
| Swiper core CSS enqueue | `tests/slider-block.test.mjs`, `skvn-marine-blocks.php` |
| Viewport height chain | `tests/slider-block.test.mjs` → `style.css`, `view.ts` |
| Hero block centering | `tests/slider-block.test.mjs` → `style.css` |

Sau `npm run build`, human onsite verify để promote case sang `ONSITE_VERIFIED`.

---

## Related

- `.agents/skills/ui-debug/SKILL.md` — State Delta framework
- `docs/standards/css-layout-safety-contract.md` — width/overflow owners
- `docs/decisions/slider-completion-spec-1.3.0.md` — layer contract markup
- `docs/testing/onsite-slider-motion-1.3.2.md` — viewport below header QA steps