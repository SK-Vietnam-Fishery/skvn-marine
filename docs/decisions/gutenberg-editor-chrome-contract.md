# Gutenberg Editor Chrome Contract — SKVN Marine Blocks

Status:

```text
APPROVED — implemented 2026-06-19 (V1 / 1.3.6); onsite verify pending
```

Purpose:

```text
Ngăn agent tái hiện lỗi editor lớn: thay UI Gutenberg mặc định bằng custom chrome
dùng CSS frontend, hoặc subscribe store quá rộng khi user đang gõ.
```

Source cases:

- `docs/debug-casebook/slider/004_SLIDER_EDITOR_ADD_SLIDE_HIT_TEST_FAILURE.md`
- `docs/debug-casebook/slider/005_SLIDER_EDITOR_IME_TYPING_RERENDER_CASCADE.md`
- `docs/debug-casebook/slider/006_SLIDER_EDITOR_ARROW_PREVIEW_CSS_CASCADE.md`

Related decisions:

- `docs/decisions/slider-completion-spec-1.3.0.md` — giữ InnerBlocks + List View
- `docs/decisions/skvn-editor-controls-0.8.0.md` — inspector governance
- `docs/decisions/slider-editor-arrow-preview-1.3.6.md` — arrow preview cascade (CASE-006, chi tiết đa agent)
- `.context/modules/PLUGIN_SKVN_MARINE_BLOCKS.md` — Slider editor UX (no Swiper in editor)

---

## 1. Định nghĩa

| Thuật ngữ | Ý nghĩa |
|-----------|---------|
| **Editor chrome** | UI chỉ tồn tại trong block editor: thêm slide, toolbar, placeholder, skeleton preview |
| **Frontend overlay** | Lớp điều khiển trên slider runtime: arrows, pagination, `pointer-events` pass-through |
| **Hit-target proof** | DevTools picker (Ctrl+Shift+C) xác nhận pixel click thuộc đúng interactive node |
| **Appender** | UI Gutenberg để chèn block con (`InnerBlocks` appender / `ButtonBlockAppender`) |

**Rule:** Editor chrome và frontend overlay **không** dùng chung một class/CSS path
trừ khi đã có editor override **đầy đủ** và hit-target proof.

---

## 2. Quy tắc bắt buộc

### 2.1 Không tắt appender WordPress mà không có replacement đạt chuẩn

**Cấm:**

```tsx
<InnerBlocks renderAppender={ () => null } />
```

…mà không đồng thời cung cấp **ít nhất một** trong các đường sau, đã verify hit-target:

1. `InnerBlocks.ButtonBlockAppender` (hoặc appender mặc định)
2. Block toolbar control (`BlockControls`) gọi cùng `insertBlock` logic
3. Custom button trong **editor-only toolbar** (class riêng, xem §2.2)

**Cấm** coi “có `<Button onClick={…}>` trong JSX” là đủ — phải có hit-target proof.

### 2.2 Editor toolbar tách class khỏi frontend controls

Frontend:

```text
.skvn-slider__controls     → absolute overlay, pointer-events: none, z-index 4
.skvn-slider__arrows        → pointer-events: auto
.skvn-slider__pagination    → pointer-events: auto
```

Editor (bắt buộc dùng class riêng hoặc prefix editor):

```text
.skvn-slider__editor-toolbar   → position: relative; z-index: 10+;
                                 pointer-events: auto (cả bar);
                                 không inset: 0; không phủ slide stack
```

**Không** đặt nút thao tác editor (Add slide, v.v.) chỉ trong `.skvn-slider__controls`
frontend mà không tách layer.

### 2.3 Hit-target proof trước khi merge

Checklist bắt buộc khi PR/task chạm editor chrome:

```text
[ ] Ctrl+Shift+C click đúng label control → đúng node (tag + class)
[ ] Click thực hiện hành vi (insertBlock, toggle, v.v.)
[ ] Không picker trúng core/buttons / RichText slide khi click control
[ ] Frontend preview controls vẫn đúng (không regression overlay)
```

Nếu picker trúng block con trong slide → **FAIL** — chưa đạt contract.

### 2.4 `useSelect` trong block có InnerBlocks/RichText

**Cấm** return object/array mới mỗi store tick:

```tsx
// BAD — re-render mỗi keystroke trong child RichText
useSelect( ( select ) => ( {
  preset: select( … ).getBlockAttributes( … )?.preset,
} ), [ … ] );
```

**Ưu tiên:**

1. `usesContext` / `providesContext` cho parent attributes tĩnh (`preset`, v.v.)
2. Return **primitive** (`string`, `number`, `boolean`) từ `useSelect`
3. `React.memo()` + tách preview nặng (ảnh nền) khỏi phần cần store

**Typing path:** keystroke trong `core/heading` không được re-render toàn bộ
slide stack.

### 2.5 Không chạy frontend runtime trong editor

Giữ quyết định V1 / 1.2.0:

- Không Swiper init/autoplay/parallax trong Gutenberg.
- Editor preview static/stacked/grid theo spec từng preset.

**Cấm** gắn hook class của runtime lên editor preview nếu `view.ts` có thể query
theo selector đó:

```text
swiper-button-prev / swiper-button-next
swiper-pagination / swiper-wrapper
data-skvn-slider (trên editor shell)
```

Editor preview chỉ mô phỏng **hình dạng** control; không được trở thành
`nextEl` / `prevEl` của Swiper.

### 2.6 Editor decorative control — CSS proof bắt buộc

Preview arrow/pagination trong editor là **decorative chrome**, không phải nút
WordPress/GP thông thường và không phải Swiper nav runtime.

**Trước merge**, inspect computed trên `button.skvn-slider__*` trong
`.editor-styles-wrapper` (hoặc iframe editor):

```text
[ ] padding === 0 (không 10px 20px từ .editor-styles-wrapper button)
[ ] color === #fff (không rgb(0, 122, 255) / --swiper-theme-color)
[ ] width/height === var(--skvn-slider-arrow-size) (2.75rem)
[ ] ::after font-size === var(--skvn-slider-arrow-glyph-size) (1rem)
[ ] không có aria-controls="swiper-wrapper-*" trên editor preview button
```

**Specificity rule:** selector editor control phải thắng
`.editor-styles-wrapper button` — tối thiểu:

```css
.editor-styles-wrapper .skvn-slider__arrow { padding: 0; color: #fff; }
```

hoặc scope editor-only:

```css
.skvn-slider--editor .skvn-slider__controls--editor-preview .skvn-slider__arrow { … }
```

**Contrast rule:** preview bar background (`#073b5a`) không được trùng tông với
fill circle mà không có viền/glyph đủ contrast. Decorative preview phải đọc được
trên nền preview — không chỉ đúng trên slide ảnh frontend.

**Không làm:**

- Chỉ nhìn screenshot rồi đoán “thiếu CSS” khi computed đã chứng minh padding/color leak.
- Dùng `swiper-button-*` trên editor preview chỉ để “ăn font icon” mà không guard
  Swiper init.
- Fix bằng `!important` trước khi có computed DIFF giữa spec và editor iframe.

---

## 3. Slider — implement đã chốt (V1 / 1.3.6)

### 3.1 Add slide (CASE-004)

| Hạng mục | Trạng thái |
|----------|------------|
| Appender | `ButtonBlockAppender` / default — không `renderAppender={() => null}` một mình |
| Custom nút | Shortcut trong `.skvn-slider__editor-toolbar` |
| CSS | Toolbar tách `.skvn-slider__controls` frontend overlay |

### 3.2 Slide edit performance (CASE-005)

| Hạng mục | Trạng thái |
|----------|------------|
| Preset | `context['skvn-marine/sliderPreset']` |
| Re-render | `memo()` + không `useSelect` object literal |

### 3.3 Arrow preview (CASE-006)

| Hạng mục | Trạng thái |
|----------|------------|
| Markup preview | Không `swiper-button-*` trên `--editor-preview` |
| Runtime | `view.ts` skip `.skvn-slider--editor` |
| CSS | `.editor-styles-wrapper … padding:0`, contrast preview bar |
| Chi tiết | `docs/decisions/slider-editor-arrow-preview-1.3.6.md` |

---

## 4. Agent checklist — trước khi submit task editor slider

```text
[ ] Không renderAppender={() => null} trừ khi có appender/toolbar thay thế đã verify
[ ] Editor toolbar class ≠ frontend overlay class (hoặc override đầy đủ)
[ ] Hit-target proof ghi trong PR / test note
[ ] useSelect không return object literal cho preset/parent attrs
[ ] usesContext đã dùng nếu block.json đã khai báo
[ ] Không Swiper/view.ts trong editor
[ ] Editor preview không dùng swiper-button-* / hook runtime selector
[ ] Computed CSS proof: padding 0, color #fff, ::after 1rem trên editor iframe
[ ] Preview bar có contrast đủ (fill ≠ nền preview hoặc viền/glyph rõ)
[ ] Casebook cập nhật nếu phát hiện failure mode mới
```

---

## 5. Anti-pattern: “Codex path” (không lặp lại)

Chuỗi sai lầm đã xảy ra:

```text
“Appender WP khó thấy”
  → renderAppender = null
  → thêm Button đẹp trong .skvn-slider__controls
  → onClick có trong source
  → không picker test
  → user click trúng core/buttons trong slide
  → báo “không handle event”
```

**Đúng:**

```text
Giữ hoặc cải thiện appender Gutenberg
  + optional toolbar editor riêng
  + picker proof
  + context thay useSelect cho preset
```

---

## 6. Regression guards (khi implement)

| Guard | File (dự kiến) |
|-------|----------------|
| Không tắt appender một mình | `tests/slider-block.test.mjs` |
| Editor toolbar class | `tests/slider-block.test.mjs` → `style.css` / `edit.tsx` |
| Context preset | `tests/slider-block.test.mjs` → `slide/edit.tsx` |
| Manual IME + picker | `docs/testing/slider-editor-chrome-1.3.6.md` (deferred / partial) |
| Arrow preview computed | `docs/testing/slider-editor-arrow-preview-1.3.6.md` |
| CASE-006 guards | `tests/slider-block.test.mjs` |

---

## 7. Out of scope

- Đổi frontend `.skvn-slider__controls` overlay model (1.3.1 đã chốt).
- Parallax editor preview (1.3.6 trục B: badge only).
- GeneratePress / theme editor CSS.