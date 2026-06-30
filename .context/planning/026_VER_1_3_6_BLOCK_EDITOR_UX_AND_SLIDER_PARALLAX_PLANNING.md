# SKVN Marine — Kế hoạch nâng cấp Editor UX, Slider Parallax & Single Post Fix

**Phiên bản:** 1.1  
**Milestone:** 1.3.6  
**Ngày tạo:** 2026-06-17  
**Context:** GeneratePress parent + skvn-marine child theme + skvn-marine-blocks plugin.

**Follow-up (hardening):** Typography scope isolation — `.context/planning/archives/029_VER_1_3_6_TYPOGRAPHY_SCOPE_ISOLATION_PLANNING.md` (wp-admin font leak fix + Vietnamese gfonts contract). Có thể implement song song hoặc ngay sau trục A/B.

**Trục D (layout slider):** Bottom-center flank controls — planning chi tiết
`.context/planning/archives/031_VER_1_3_6_SLIDER_BOTTOM_CENTER_FLANK_CONTROLS_PLANNING.md` (v2.0).
Onsite: `docs/testing/onsite-slider-flank-controls-1.3.6.md`.

**Trục B (parallax):** **Deferred → V1 / 1.3.8** (human lock 2026-06-22). Phần parallax
trong file này giữ làm reference; không implement trong 1.3.6.

**Artifact:** `docs/artifacts/slider-parallax-1.3.6-mockup.html` có controls row **và**
transition motion — 031/Trục D chỉ ship layout `‹ pagination ›`, không ship motion.

---

## 1. Mục tiêu

Milestone **1.3.6 đang active** có bốn trục (Trục B parallax chuyển sang 1.3.8):

**Trục A — Editor UX cho SKVN blocks:**
Nâng cấp Inspector panel, placeholder state, và control pattern cho tất cả các block
trong `skvn-marine-blocks` để editor UX nhất quán, dễ dùng cho marketing user mà
không cần chạm raw code.

**Trục B — Slider Parallax:** → **Deferred V1 / 1.3.8** (xem §4 và
`docs/decisions/slider-parallax-both-1.3.8.md`). Không nằm trong scope implement
1.3.6 hiện tại.

**Trục C — Single Post Fix:** Visual debt 1.3.5 (hero width, font, aspect-ratio).

**Trục D — Slider bottom-center flank controls:**
Khi arrows + pagination **cùng Bottom center** (và arrow không phải pill), đổi layout
controls từ `[‹›] | pagination` sang `‹ | pagination | ›`. Thuộc plugin slider;
**không** đổi attribute schema. Spec đầy đủ: planning **031 v2.0** — đọc file đó
trước khi code; file 026 này chỉ giải thích vị trí trong milestone.

---

## 2. Quyết định đã chốt

| Vấn đề | Quyết định |
|--------|------------|
| Parallax engine | Swiper built-in Parallax Module — không JS riêng |
| Parallax target | Image layer của từng Slide (`data-swiper-parallax`) |
| Parallax fallback | `prefers-reduced-motion: reduce` → disable parallax, set `data-swiper-parallax="0"` |
| Mobile parallax | Tắt khi `slidesPerView > 1` hoặc viewport < 768px (governed toggle) |
| Inspector panels | Dùng 4-section pattern: **Content / Style / Layout / Advanced** |
| Raw class input | Không cho phép raw class input từ editor — dùng governed presets |
| Empty state placeholder | Tất cả blocks phải có placeholder component khi không có content |
| Dynamic block preview | Post/Product Collection hiện skeleton grid trong editor khi không có data |

---

## 3. Trục A — Editor UX

### 3.1 Inspector Panel Consistency

Tất cả blocks phải dùng cấu trúc `PanelBody` 4-section:

```
Inspector
├── Content      — nội dung block: text, query, items
├── Style        — màu sắc, typography, preset visual
├── Layout       — grid columns, height preset, spacing
└── Advanced     — anchor, extra class, debug info
```

**Hiện trạng cần fix:**

| Block | Vấn đề |
|-------|--------|
| `slider` | Panel chưa phân nhóm rõ ràng giữa style và layout |
| `card-grid` | Panel chưa phân nhóm |
| `card` | Panel chưa phân nhóm |
| `accordion` | Panel chưa phân nhóm |
| `feature-showcase` | Panel chưa phân nhóm |
| `collection` (shared) | Panel lớn duy nhất — cần tách Content (query) vs Layout |

### 3.2 Placeholder / Empty State

Mỗi block cần có `BlockPlaceholder` hoặc custom empty state khi:
- Slider/Feature Showcase không có Slide con nào
- Accordion không có item nào
- Collection khi query trả về 0 kết quả trong editor

**Pattern chuẩn:**
```tsx
if (slideCount === 0) {
  return (
    <div {...blockProps}>
      <Placeholder icon={…} label="SKVN Slider">
        <Button variant="primary" onClick={addSlide}>
          {__('Thêm slide đầu tiên', 'skvn-marine-blocks')}
        </Button>
      </Placeholder>
    </div>
  );
}
```

### 3.3 Collection Preview Skeleton

`post-collection` và `product-collection` dùng server-side render → editor thấy
"Loading..." hoặc blank khi REST chậm. Cần thêm:
- `preview.tsx` skeleton grid (hiện có file nhưng chưa đủ đẹp)
- Số cột skeleton match `responsivePreset` desktop (cột đầu tiên của preset `X-Y-Z`)

### 3.4 Block Icons và Inserter Label

Kiểm tra và chuẩn hoá:
- Icon SVG cho mỗi block trong `block.json` / `registerBlockType`
- `description` rõ ràng bằng tiếng Anh (WP convention) — không để trống
- `keywords` tối thiểu 2 từ khoá để inserter search hoạt động tốt

---

## 4. Trục B — Slider Parallax (DEFERRED → 1.3.8)

> **Trạng thái:** Không implement trong milestone 1.3.6. Giữ §4 làm reference khi
> mở 1.3.8. Tránh nhầm với Trục D (flank layout) — flank là CSS/markup only.

### 4.1 Cơ chế

Swiper Parallax Module dùng `data-swiper-parallax` attribute trên element bên trong
slide. Khi slide chuyển, Swiper tính toán translate offset theo tốc độ cấu hình.

```html
<!-- Markup pattern trong slide render -->
<div class="swiper-slide skvn-slide">
  <div class="skvn-slide__bg" data-swiper-parallax="-30%">
    <img src="…" alt="" />
  </div>
  <div class="skvn-slide__content" data-swiper-parallax="-10%">
    <!-- InnerBlocks -->
  </div>
</div>
```

Swiper config cần thêm:
```js
modules: [...existingModules, Parallax],
parallax: true,
```

### 4.2 Attributes mới cho Slider

| Attribute | Type | Default | Mô tả |
|-----------|------|---------|-------|
| `enableParallax` | `boolean` | `false` | Bật parallax effect |
| `parallaxIntensity` | `'subtle' \| 'medium' \| 'strong'` | `'medium'` | Tốc độ parallax |

Intensity → actual percent offset:
- `subtle` → `-15%`
- `medium` → `-30%`
- `strong` → `-50%`

Không cho nhập số tùy ý — governed presets.

### 4.3 Reduced Motion và Mobile

```ts
// Trong frontend init
const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

swiperConfig.parallax = attributes.enableParallax && !prefersReduced;
```

Tắt parallax khi `slidesPerView > 1` (multi-slide view không có visual benefit và
gây artifact overlapping).

### 4.4 Editor Preview

Không chạy Swiper parallax trong editor (editor đã stacked preview). Thêm badge
text "Parallax ON" trong editor preview khi `enableParallax: true`.

### 4.5 Slide background và parallax

Slide hiện có `background-image` được set qua CSS hoặc `img` tag tùy preset.
Cần audit `skvn-slide__bg` wrapper tồn tại đúng trong cả 3 Slider presets
(Hero, Product Showcase, Card Carousel) trước khi gắn `data-swiper-parallax`.

---

## 4.6 Trục D — Slider bottom-center flank controls (ACTIVE — planning 031)

### Vì sao có Trục D

Milestone 1.3.1 đã ship **cluster** `arrows | pagination` khi hai control cùng bottom
position. Với **bottom-center**, marketing muốn pagination nằm giữa và prev/next hai
bên — giống mockup artifact. Đây là **exception layout**, không phải feature mới:
không thêm attribute, không đổi Swiper.

### Human đã chốt (2026-06-19)

| Câu hỏi | Trả lời |
|---------|---------|
| Khi nào flank? | Cùng `bottom-center` + arrows on + pagination on + không pill |
| Pill + bottom-center? | Giữ cluster cũ (capsule gom 2 nút) |
| Pagination style? | Không ảnh hưởng eligibility (dots/fraction/timed đều flank được) |
| Parallax motion? | Không — Trục B deferred 1.3.8 |

### Implementer đọc đâu

**Không implement từ section tóm tắt này.** Source of truth:

1. `.context/planning/archives/031_VER_1_3_6_SLIDER_BOTTOM_CENTER_FLANK_CONTROLS_PLANNING.md` (v2.0)
   — predicate, markup, từng file, CSS mirror, test matrix.
2. `docs/decisions/slider-navigation-and-pagination-controls.md` §5.1 — contract ngắn.
3. `docs/testing/onsite-slider-flank-controls-1.3.6.md` — đóng checklist milestone.

### Files chạm (tóm tắt — chi tiết trong 031 §7)

| File | Việc |
|------|------|
| `modules/slider-render/slider-render.php` | Flank markup branch (canonical) |
| `src/slider/view.ts` | Không inject separator khi flank |
| `src/slider/edit.tsx` | Editor preview branch |
| `src/slider/style.css` | `--cluster-flank` + `--arrows-{style}` mirror |
| `tests/slider-block.test.mjs` | Regression flank + pill exception |

**Không chạm:** `save.tsx`, theme, GeneratePress, Swiper module list.

### Trạng thái code (2026-06-22)

**0% implemented** — grep `cluster-flank` trong plugin = 0. Milestone bullets flank
trong `MILESTONES.md` vẫn `[ ]`.

---

## 5. Trục C — Single Post Fix (1.3.5 visual debt)

### 5.1 Vấn đề hiện tại (từ onsite screenshot)

| Vấn đề | Triệu chứng | Root cause |
|--------|-------------|------------|
| Hero width sai | Hero render như cột hẹp ~30% bên cạnh content, không full-width | Thiếu CSS hoặc GP container tạo flex row |
| Font sai | Heading dùng system-ui thay vì Instrument Serif | `.skvn-post-hero__title` thiếu `font-family: var(--skvn-font-heading)` |
| Thumbnail ratio | Card và hero dùng ảnh gốc không có visual crop | Cần `aspect-ratio` + `object-fit: cover` |

### 5.2 Ràng buộc ThumbPress

Site đang dùng ThumbPress cấu hình **không sinh thêm thumbnail size** — chỉ giữ ảnh gốc. Do đó:
- **Không** đăng ký `add_image_size()` cho hero hay card.
- **Dùng CSS** `aspect-ratio` + `object-fit: cover` để visual-crop cùng ảnh gốc ở các context khác nhau.
- PHP gọi `'full'` hoặc `'large'` cho tất cả `the_post_thumbnail()` call.

### 5.3 Aspect ratio mục tiêu

| Context | Ratio | CSS |
|---------|-------|-----|
| Hero single post | 16:9 | `aspect-ratio: 16/9` trên wrapper |
| Card thumbnail (archive, related) | 3:2 | `aspect-ratio: 3/2` trên wrapper |
| Sidebar related post thumb | 4:3 | `aspect-ratio: 4/3` trên wrapper |

```css
/* Tất cả thumbnail wrappers */
.skvn-post-hero,
.skvn-post-card__thumb,
.skvn-post-related__thumb,
.skvn-island__post-thumb { overflow: hidden; }

.skvn-post-hero__img,
.skvn-post-card__thumb img,
.skvn-post-related__thumb img,
.skvn-island__post-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
}
```

### 5.4 Font fix

Thêm `font-family: var(--skvn-font-heading)` vào:
- `.skvn-post-hero__title`
- `.skvn-post-body h1, h2, h3, h4` (content headings)
- `.skvn-post-related__title a`
- `.skvn-island__post-title a`

### 5.5 Hero width fix

Audit tại sao `.skvn-post-hero` không full-width. Candidates:
- Thiếu `display: block; width: 100%` trên `.skvn-post-hero`
- GP container có `display: flex` làm hero thành flex item
- GP sidebar active cho single post type

Fix: Thêm `display: block; width: 100%` explicit. Nếu GP flex container vẫn can thiệp, dùng `align-self: stretch` hoặc audit GP sidebar settings.

### 5.6 Files chạm (Trục C)

- `wp-content/themes/skvn-marine/style.css` — hero CSS fix, aspect-ratio, font-family
- `wp-content/themes/skvn-marine/single.php` — thumbnail size argument nếu cần

---

## 6. Thứ tự thực hiện (cập nhật 2026-06-22)

```
Step 1–2:  [C] Single Post — DONE (human verified 2026-06-19)
Step 3–9:  [A] Editor UX — IN PROGRESS / pending
Step 10:     [D] Flank controls — theo 031 §9 (PHP → CSS → view → edit → test)
Step 11:     [D] Onsite QA — docs/testing/onsite-slider-flank-controls-1.3.6.md
Step 12:     Build + PHP lint + tick MILESTONES flank bullets
```

**Trục B (Parallax):** không nằm trong sequence 1.3.6 — chuyển sang milestone **1.3.8**.

**Trục D** có thể làm song song Trục A sau khi có bandwidth; không phụ thuộc parallax.
Ưu tiên D nếu onsite đang chờ layout bottom-center hero slider.

---

## 6. Files sẽ chạm

**Plugin (skvn-marine-blocks):**
- `src/slider/edit.tsx` — Inspector panel refactor + parallax controls
- `src/slider/index.ts` hoặc `block.json` — parallax attributes
- `src/slide/edit.tsx` — (nếu cần thêm parallax wrapper markup)
- `src/feature-showcase/edit.tsx` — Inspector panel refactor
- `src/collection/controls.tsx` — tách Content / Layout panels
- `src/post-collection/edit.tsx`, `src/product-collection/edit.tsx`
- `src/card-grid/edit.tsx`, `src/card/edit.tsx`, `src/accordion/edit.tsx`
- `src/collection/preview.tsx` — skeleton grid
- `src/slider/frontend.ts` hoặc shared runtime — parallax Swiper config

**Theme (skvn-marine):**
Không có thay đổi theme cho milestone này.

**Files không chạm:** `themes/generatepress/**`, theme PHP, `style.css`

---

## 7. Kiến trúc & Invariants

- Swiper Parallax là module built-in — không add dependency mới.
- Parallax chỉ active khi `parallax: true` trong Swiper config.
- `data-swiper-parallax` là string attribute chuẩn Swiper — không phải custom hack.
- Editor không chạy Swiper autoplay/parallax — stacked preview vẫn giữ nguyên.
- `prefers-reduced-motion` bắt buộc — tắt parallax hoàn toàn, không chỉ slow down.
- Inspector panel refactor không được đổi attribute names hiện có → không invalidate content cũ.
- Governed presets thay raw input — không để textbox nhập % tùy ý.

---

## 8. Open Points

1. **Slide background wrapper** — cần audit slide markup của 3 presets: Hero, Product Showcase, Card Carousel. Nếu chưa có `skvn-slide__bg` wrapper thống nhất, phải add trước khi gắn parallax attribute.
2. **Panel refactor scope** — có refactor tất cả blocks trong một milestone hay chia nhỏ? Nếu chia, ưu tiên slider + collection (phức tạp nhất, dùng nhiều nhất).
3. **Placeholder design** — dùng WP native `<Placeholder>` hay custom SKVN empty state component?
4. **Collection skeleton** — skeleton đơn giản (gray boxes) hay có SKVN branded style?

---

## 9. Acceptance Draft

**Trục C — Single Post Fix:**
- [ ] Hero `.skvn-post-hero` render full-width trong GP content area
- [ ] Heading trong hero dùng `font-family: var(--skvn-font-heading)` — Instrument Serif load đúng
- [ ] Thumbnail hero dùng `aspect-ratio: 16/9` + `object-fit: cover` — không cần WP image size
- [ ] Card thumbnail (archive, related) dùng `aspect-ratio: 3/2` + `object-fit: cover`
- [ ] Không có `add_image_size()` mới — ThumbPress-compatible

**Trục A — Editor UX:**
- [ ] Tất cả Inspector panels dùng 4-section Content/Style/Layout/Advanced
- [ ] Không block nào còn dùng raw class text input cho marketing user
- [ ] Slider và Accordion có empty state placeholder với action button
- [ ] Collection skeleton grid match responsive preset khi editor đang load
- [ ] Block icons và descriptions không còn blank trong inserter
- [ ] Inspector panel refactor không invalidate content hiện có

**Trục B — Slider Parallax:** → moved to **V1 / 1.3.8** (không tick trong 1.3.6).

**Trục D — Flank controls (planning 031 + onsite doc):**
- [ ] `bottom-center` + `bottom-center` + circle/minimal → `‹ pagination ›`
- [ ] Bốn pagination styles OK trong flank row
- [ ] `pill` + bottom-center → cluster cũ, không flank
- [ ] `bottom-left` / `bottom-right` / `side-center` / mismatched positions — no regression
- [ ] Editor preview khớp frontend
- [ ] `tests/slider-block.test.mjs` pass
- [ ] Human onsite PASS (`docs/testing/onsite-slider-flank-controls-1.3.6.md`)
- [ ] Mobile timed pagination width — deferred 1.3.9 (ghi note evidence)

**Chung:**
- [ ] Plugin build pass, PHP lint pass
- [ ] Human approves milestone completion

---

## 10. Deferred

- Parallax cho `step-slider` (1.5.0 scope)
- Full SKVN Editor Controls system với sidebar token pickers (0.8.0 scope)
- Sidebar content dynamic (V2)
