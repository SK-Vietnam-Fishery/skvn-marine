# Planning — Slider Parallax 1.3.8

**Milestone:** V1 / 1.3.8  
**Status:** READY TO IMPLEMENT  
**Date:** 2026-06-22

---

## References

| Artifact | Path | Vai trò |
|----------|------|---------|
| Decision doc | `docs/decisions/slider-parallax-both-1.3.8.md` | Source of truth — engine, depth, intensity presets, markup contract, Inspector UX, governed internals |
| POC — Swiper translate | `docs/artifacts/slider-parallax-swiper-poc.html` | Visual pass — translate depth với Swiper 11 CDN, class SKVN |
| POC — Zoom lab | `docs/artifacts/slider-parallax-zoom-poc.html` | Lab so sánh translate / scale / both × wipe / fade / zoom-out; xác nhận both compound OK, zoom+scale không artifact |
| Foundation plan | `.context/planning/026_VER_1_3_6_BLOCK_EDITOR_UX_AND_SLIDER_PARALLAX_PLANNING.md` | Image layer parallax (§2), auto-disable guards |

---

## Scope

8 files thay đổi, tách 2 nhóm logic:

| Nhóm | Files | Mục tiêu |
|------|-------|----------|
| PR1 — Markup | `slider-render.php`, `slide/edit.tsx`, `slide/save.tsx`, `slider/style.css` | Thêm `.skvn-slide__bg` wrapper + CSS |
| PR2 — Wiring | `slider/block.json`, `slider/view.ts`, `slider/edit.tsx`, `slider/save.tsx` | Attributes + Inspector + Swiper Parallax module + serialize JSON |

---

## Inspector UX — Sidebar Structure

Xem decision doc §Inspector UX để biết đầy đủ help text và governed internals.

```
InspectorControls
│
├── PanelBody "Slider settings"          [open by default]  — không đổi
├── PanelBody "Navigation"               [closed]           — không đổi
├── PanelBody "Pagination"               [closed]           — không đổi
├── PanelBody "Presentation"             [closed]           — không đổi
│
└── PanelBody "Motion"                   [closed]  ← NEW 1.3.8
    ├── ToggleControl — Enable parallax
    │     help: "Background image moves at a different speed than the slide,
    │            creating a sense of depth. Has no effect in the editor."
    └── [if enableParallax]
        ├── label: "Intensity"
        │     help: "Controls how far the background travels and how much
        │            it scales during the transition."
        └── ButtonGroup — Subtle / Medium / Strong
              (không dùng dropdown — intensity là cảm giác so sánh bằng mắt,
               ButtonGroup = 1 click thấy ngay; xem decision doc §Editor Panel)

[Governed internally — ẩn khỏi UI]
├── Depth mechanism: BOTH (translate + scale) — luôn bật, không expose
│     Lý do: translate-only thiếu z-depth; scale-only chỉ rõ lúc đang chuyển;
│     both compound = hiệu ứng sâu nhất. Differentiation đến từ Transition,
│     không phải depth mechanism. (Xác nhận qua zoom-poc lab)
├── prefers-reduced-motion → parallax off (runtime guard, view.ts)
└── slidesPerView > 1 → parallax off (runtime guard, view.ts)

Editor canvas badge (toolbar area)
└── [if enableParallax] span "Parallax ON · {intensity}"
      Lý do: editor không chạy Swiper parallax runtime; badge xác nhận
      setting đang bật mà không gây nhầm lẫn "tại sao không thấy hiệu ứng"
```

---

## PR1 — Markup

### 1. `modules/slider-render/slider-render.php`

**Thay đổi — Slide render:** Wrap `<img class="skvn-slide__background-image">` trong `<div class="skvn-slide__bg">` bên trong `<div class="skvn-slide__media">`. `<span class="skvn-slide__overlay">` giữ nguyên là sibling của `__bg`, không vào trong wrapper.

**Thay đổi — Slider render:** Normalize `enableParallax` (bool, default false) và `parallaxIntensity` (`subtle|medium|strong`, default `medium`) từ `$attributes` và thêm vào `$config` array truyền vào `data-skvn-slider` JSON. Dùng hàm `skvn_marine_blocks_normalize_slider_choice()` đã có sẵn.

**Lý do:** Swiper Parallax Module cần element riêng để inject `data-swiper-parallax` + `data-swiper-parallax-scale`. Hiện tại `<img>` nằm trực tiếp trong `__media` — nếu inject attrs lên img, Swiper transform trực tiếp làm vỡ `object-fit: cover`. Cần wrapper có `position: absolute; inset` là chủ sở hữu transform duy nhất.

Tham chiếu markup contract: decision doc §Markup Contract.

---

### 2. `src/slide/edit.tsx`

**Thay đổi:** Sync full structure với PHP render:
```
<div class="skvn-slide__media">
  <div class="skvn-slide__bg"><img /></div>
  <span class="skvn-slide__overlay" />
</div>
<div class="skvn-slide__content">…InnerBlocks…</div>
```
Hiện tại `edit.tsx` chưa có `__media` wrapper — cần thêm đồng thời `__bg` bên trong.

**Lý do:** Editor dùng JSX riêng, không dùng PHP render. Nếu không sync markup, editor render khác frontend → block validation mismatch khi user lưu. Thiếu `__media` → CSS transition rules (clip-path, scale) không áp dụng được trong editor preview.

---

### 3. `src/slide/save.tsx`

**Thay đổi:** Sync full structure (cùng cấu trúc PHP và edit.tsx):
```
<div class="skvn-slide__media">
  <div class="skvn-slide__bg"><img /></div>
  <span class="skvn-slide__overlay" />
</div>
<div class="skvn-slide__content">…InnerBlocks.Content…</div>
```
Hiện tại `save.tsx` không có `__media` hoặc `__content` wrapper.

**Lý do:** `save.tsx` là shape Gutenberg dùng để validate block khi mở editor. Slide block có `render_callback` nên frontend không dùng `save.tsx`, nhưng editor vẫn dùng để kiểm tra markup khớp. Nếu không cập nhật → "invalid block / attempt recovery" mỗi lần mở.

**Deprecation note:** Không cần `deprecated.tsx` cho onsite dev site (single customer). Nếu plugin ship multi-customer: thêm `deprecated.tsx` với old `save()`. Xem decision doc §Commercialization Note và pattern `src/feature-showcase/deprecated.tsx`.

---

### 4. `src/slider/style.css`

**Thay đổi:** Thêm rules cho `__bg` wrapper và cập nhật `__media`.

Chi tiết rules — xem decision doc §CSS changes.

**Lý do từng rule:**
- **`overflow: hidden` trên `__media` — verify, không duplicate:** rule đã có tại `.skvn-slide__media` (line 38–44 trong `style.css`: `inset: 0; overflow: hidden; pointer-events: none; position: absolute; z-index: 0`). Confirm rule hiện tại đủ contain parallax scale overspill trên `__bg`; **không** thêm declaration trùng.
- `inset: var(--skvn-parallax-inset, 0%)` — default `0%` không ảnh hưởng layout cũ (no parallax). Khi bật, `view.ts` inject giá trị âm để image overscroll buffer đủ cho translate không lộ edge. Inset coupling với intensity: subtle `-20%`, medium `-35%`, strong `-50%`.
- `object-fit: cover` trên `__bg img` — `__bg` giờ là positioned container mới, img cần fill 100% container.
- **Exclusion selector** (line ~74): `.skvn-slide--has-background > :not(.skvn-slide__media, .skvn-slide__background-image, .skvn-slide__overlay)` — thêm `.skvn-slide__bg` vào danh sách exclusion để tránh selector này áp `z-index: 2` lên wrapper parallax.

---

## PR2 — Wiring

### 5. `src/slider/block.json`

**Thay đổi:** Thêm `enableParallax` (boolean, default false) và `parallaxIntensity` (enum subtle/medium/strong, default medium) vào `attributes`.

**Lý do:** Gutenberg block attributes là source of truth. Không có trong `block.json` → `setAttributes` không lưu được, không truyền vào PHP render, không truyền vào `data-skvn-slider` JSON.

---

### 6. `src/slider/view.ts`

**Thay đổi:**

1. Import thêm `Parallax` từ `swiper/modules` — cùng pattern với `A11y`, `Autoplay`, etc. trong block import hiện tại. **Không** thêm `import 'swiper/css/parallax'` trừ khi build warn missing module — Swiper 11 Parallax dùng inline transform, không có CSS entry bắt buộc như `effect-fade` / `navigation` / `pagination`.
2. Thêm `enableParallax?: boolean` và `parallaxIntensity?: string` vào `SliderConfig` type (raw JSON parse)
3. Thêm `enableParallax: boolean` và `parallaxIntensity: 'subtle' | 'medium' | 'strong'` vào `NormalizedSliderConfig` type — **bắt buộc cả hai tầng type**. `initSlider` làm việc với output đã normalize của `parseSliderConfig`; chỉ update `SliderConfig` mà bỏ `NormalizedSliderConfig` → TypeScript fail khi truy cập `config.enableParallax`. Pattern hiện tại: `slidesPerView?: number` (raw) → `slidesPerView: number` (normalized).
4. Thêm normalize cho 2 fields mới trong hàm `parseSliderConfig` — `enableParallax` boolean normalize, `parallaxIntensity` dùng `normalizeChoice(['subtle','medium','strong'], 'medium')`; return object phải include cả hai field trên `NormalizedSliderConfig`
5. Trước `new Swiper()`: tính `shouldParallax = config.enableParallax && !reducedMotion && config.slidesPerView === 1`; nếu true, inject `data-swiper-parallax` + `data-swiper-parallax-scale` lên tất cả `.skvn-slide__bg`; set `--skvn-parallax-inset` trên **slider root element** (không per-slide — intensity uniform toàn slider)
6. Thêm `Parallax` vào `modules: []` và `parallax: shouldParallax` vào Swiper config
7. Trong hàm `cleanup`: remove `--skvn-parallax-inset` CSS var khỏi slider root

**Lý do:**
- Pattern inject attrs runtime giống pattern arrow/pagination classes hiện tại — markup sạch, logic tập trung một chỗ.
- `shouldParallax = enableParallax && !reducedMotion && slidesPerView === 1` — 3 guards trong 1 biểu thức, không scatter.
- `prefersReducedMotion()` đã có sẵn (line 16 import) — dùng lại, không import thêm.
- Cleanup cần remove CSS var để tránh stale state khi Swiper bị destroy (MutationObserver cycle trong editor iframe).

Tham chiếu intensity map + inset values: decision doc §Intensity Presets table.

---

### 7. `src/slider/save.tsx`

**Thay đổi:** Thêm `enableParallax` và `parallaxIntensity` vào `SliderAttributes` type và vào object serialize trong `data-skvn-slider` JSON (trong `blockProps`).

**Lý do:** Slider block có `render_callback` → **PHP là frontend source of truth** cho `data-skvn-slider`. `save.tsx` serialize JSON này cho editor validation và legacy path — không phải nguồn chính cho `view.ts` ở frontend. Tuy nhiên vẫn cần cập nhật để:
1. Editor validation khớp (tránh "invalid block")
2. Legacy/static path (nếu `render_callback` bị tắt) vẫn hoạt động

**PHP (PR1) là bắt buộc cho parallax hoạt động onsite. `save.tsx` (PR2) là bắt buộc cho editor consistency.**

---

### 8. `src/slider/edit.tsx`

**Thay đổi:**

1. Thêm `enableParallax: boolean` và `parallaxIntensity: 'subtle' | 'medium' | 'strong'` vào `SliderAttributes` type
2. Import thêm `ButtonGroup` từ `@wordpress/components` — chưa có trong destructure hiện tại, thiếu sẽ lỗi compile
3. Thêm `PanelBody "Motion"` sau `PanelBody "Presentation"` (panel cuối cùng trong `InspectorControls`)
4. Thêm badge `"Parallax ON · {intensity}"` trong `skvn-slider__editor-toolbar` div

**Lý do:**
- `ButtonGroup` chưa import — thiếu sẽ lỗi compile.
- Panel "Motion" tách khỏi "Presentation" vì parallax là motion effect, không phải layout config — đúng mental model người dùng.
- Badge giải quyết UX gap: editor không chạy Swiper runtime nên user không thấy hiệu ứng; badge xác nhận setting đang bật.

---

## Test Steps (Onsite QA)

1. **Build:** `npm run build` trong `wp-content/plugins/skvn-marine-blocks/`
2. **Build gate — Parallax CSS:** sau build, verify **không** có warning/error về missing `swiper/css/parallax` (hoặc module CSS parallax tương đương). Parallax module không yêu cầu CSS import riêng theo Swiper 11 docs; chỉ thêm import nếu build output báo thiếu.
3. **PHP lint:** `find modules/ -name "*.php" -exec php -l {} \;`
4. Wipe transition + parallax medium — depth có, không edge-reveal
5. Fade transition + parallax medium — depth có, fade OK
6. Zoom-out transition + parallax medium — compound scale looks intentional
7. Zoom-out + parallax strong — QA xem có artifact không
8. `prefers-reduced-motion: reduce` (DevTools) → parallax tắt hoàn toàn, không có inline transform
9. Card carousel (`slidesPerView > 1`) → parallax tắt tự động
10. Slider cũ (trước parallax, `enableParallax` chưa set): frontend render OK, không fatal; editor có thể show "attempt recovery" warning — acceptable (D6)
11. Loop + autoplay + keyboard → no regression
12. Inspector badge hiển thị đúng khi enable/disable
13. Intensity switch Subtle → Medium → Strong → depth thay đổi rõ ràng

---

## Build + Lint Command

```
cd wp-content/plugins/skvn-marine-blocks && npm run build && find modules/ -name "*.php" -exec php -l {} \;
```

---

## Deferred

- `deprecated.tsx` cho slide block — chỉ cần khi ship multi-customer (xem decision doc §Commercialization Note)
- Parallax trên `.skvn-slide__content` — quá rủi ro với zoom-out transition (xem decision doc §Deferred)
- Step slider parallax fine-tuning — 1.3.10 inherits foundation
- Mobile `< 768px` disable guard — không làm (D4); parallax chạy trên mobile khi điều kiện `shouldParallax` thỏa
