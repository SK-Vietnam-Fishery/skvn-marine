# Slider Editor Arrow Preview — Decision & Reference (V1 / 1.3.6)

Status:

```text
APPROVED — implemented 2026-06-20 (source)
Onsite verification: PENDING
```

Audience:

```text
Mọi AI agent và human dev sửa Slider editor chrome, navigation preview,
hoặc CSS arrow/pagination trong skvn-marine-blocks.
```

Source case:

- `docs/debug-casebook/slider/006_SLIDER_EDITOR_ARROW_PREVIEW_CSS_CASCADE.md`

Parent contract:

- `docs/decisions/gutenberg-editor-chrome-contract.md` — §2.5 (runtime hook) + §2.6 (CSS proof)

Related:

- `docs/decisions/slider-navigation-and-pagination-controls.md` — arrow styles frontend
- `docs/decisions/slider-completion-spec-1.3.0.md` — editor stacked preview, no Swiper in editor
- `docs/debug-casebook/PITFALLS.md` — Pitfall 6

---

## 1. Vấn đề cần giải quyết

Trong block editor, thanh preview tĩnh dưới slider (`.skvn-slider__controls--editor-preview`)
hiển thị arrow **circle** sai:

- Vòng tròn trông “rỗng” (chỉ thấy viền mỏng)
- Chevron **xanh** `#007aff`, to, lệch trái
- Có thể thấy `aria-controls="swiper-wrapper-…"` trên preview button

Human ban đầu nghi “thiếu CSS” hoặc “chưa build”. DevTools chứng minh **SKVN CSS đã load**
nhưng bị **cascade stack** của editor iframe và (trước fix) Swiper runtime hook.

**Đây không phải một bug một lớp.** Agent sau này không được fix một property rồi đóng task.

---

## 2. Bối cảnh kiến trúc (đọc trước khi sửa)

### 2.1 Ba surface khác nhau

| Surface | DOM / class | Mục đích | Runtime |
|---------|-------------|---------|---------|
| **Editor toolbar** | `.skvn-slider__editor-toolbar` | Add slide, thao tác editor | React `edit.tsx` |
| **Editor controls preview** | `.skvn-slider__controls--editor-preview` | Mô phỏng arrows/pagination | **Decorative only** — `aria-hidden` parent |
| **Frontend overlay** | `.skvn-slider__controls` (không `--editor-preview`) | Arrows/pagination thật trên slide | Swiper `view.ts` |

**Quyết định cốt lõi:** Editor controls preview **không** là frontend overlay và **không**
là Swiper navigation target.

### 2.2 Asset graph trong editor iframe

WordPress enqueue cho slider block trong editor:

```text
skvn-marine-slider-core   → build/view.ts.css   (Swiper core + :root --swiper-theme-color)
skvn-marine-slider-view   → build/style-view.ts.css (SKVN slider + editor rules)
+ theme .editor-styles-wrapper button rules
+ WP block editor chrome
```

Agent phải verify UI trong **iframe editor**, không chỉ frontend hay “đọc source có rule”.

### 2.3 Glyph arrow không cần class Swiper trên editor preview

Frontend dùng `swiper-button-prev/next` vì Swiper `navigation.nextEl/prevEl` query theo
selector đó.

Editor preview chỉ cần glyph qua CSS:

```css
.skvn-slider__arrow--prev::after { content: "prev"; font-family: swiper-icons; }
```

→ **Không** gắn `swiper-button-*` lên preview button.

---

## 3. State Delta đã PROVEN (DevTools)

```text
State A — Spec SKVN (circle):
  padding: 0
  color: #fff
  ::after font-size: 1rem (var --skvn-slider-arrow-glyph-size)
  size: 2.75rem circle
  fill: rgba(7,59,90,0.92) trên slide ảnh / frontend

State B — Broken (editor iframe):
  padding: 10px 20px
  color: rgb(0, 122, 255)
  width/height: 44px (đúng token nhưng inner box ≈ 4px ngang)
  background SKVN có nhưng gần trùng nền preview #073b5a
  aria-controls trên preview button (Swiper bind)

Delta:
  Lớp 1 — .editor-styles-wrapper button thắng padding
  Lớp 2 — Swiper --swiper-theme-color thắng color #fff
  Lớp 3 — camouflage fill trên preview bar
  Lớp 4 — swiper-button-* hook → view.ts navigation
```

### 3.1 Vì sao padding 10px 20px phá geometry

Với `box-sizing: border-box`, `width: 44px` và `padding-left/right: 20px`:

```text
content width ≈ 44 - 40 = 4px
```

Glyph `::after` (~16px+) bị ép → tràn, lệch trái — khớp screenshot human.

**Lesson:** Với decorative `button` trong editor, **padding computed** quan trọng hơn
`font-size` trong source.

### 3.2 Vì sao circle trông “rỗng”

| Layer | Màu |
|-------|-----|
| Preview bar | `#073b5a` |
| Circle fill (frontend token) | `rgba(7, 59, 90, 0.92)` |

Hai màu gần nhau → mắt chỉ thấy `border: rgba(255,255,255,0.32)` như vòng xám.

**Lesson:** Editor preview cần **contrast token riêng**, không copy y nguyên frontend fill
lên nền preview bar.

---

## 4. Quyết định đã chốt (và lý do)

### D1 — Tách runtime hook khỏi editor preview markup

**Quyết định:** Preview buttons trong `edit.tsx` chỉ dùng:

```text
skvn-slider__arrow skvn-slider__arrow--prev|next
```

**Không** dùng `swiper-button-prev|next` trên `--editor-preview`.

**Lý do:**

- `view.ts` bind `navigation.prevEl/nextEl` bằng `querySelector('.swiper-button-prev')`.
- Class hook + `view.ts` trong editor iframe → Swiper gắn `aria-controls`, theme color, nav geometry.
- Trái contract V1 / 1.2.0: không Swiper carousel trong editor.

**Đã reject:**

| Phương án | Vì sao reject |
|-----------|----------------|
| Giữ `swiper-button-*` “để ăn font” | Glyph đã có qua `--prev::after` + `swiper-icons` |
| Đổi `view.ts` query sang class khác chỉ trên frontend | Phá save markup + PHP render đang dùng `swiper-button-*` |
| `pointer-events: none` trên preview | Không fix cascade; chỉ che symptom |

**File:** `src/slider/edit.tsx` (preview buttons only). Frontend `save.tsx` / PHP render **giữ** `swiper-button-*`.

---

### D2 — Guard `view.ts` không init trên editor shell

**Quyết định:** Đầu `initSlider()`:

```ts
if ( slider.classList.contains( 'skvn-slider--editor' ) ) {
  return;
}
```

**Lý do:**

- `view_script` vẫn load trong editor iframe (block registration).
- `MutationObserver` trên `document.body` có thể gặp DOM có `data-skvn-slider` từ preview khác hoặc tương lai.
- `.skvn-slider--editor` là invariant editor shell từ `useBlockProps` — guard rẻ, rõ.

**Đã reject:**

| Phương án | Vì sao reject |
|-----------|----------------|
| Chỉ dựa vào không có `data-skvn-slider` trên editor | Dễ regress nếu ai thêm attr sau này |
| Tắt `view_script` trong editor | Phá pattern block.json; khó maintain |

**File:** `src/slider/view.ts`

---

### D3 — CSS specificity cho editor iframe (padding + color)

**Quyết định:** Scope tối thiểu:

```css
.editor-styles-wrapper .skvn-slider--editor .skvn-slider__controls--editor-preview .skvn-slider__arrow
```

Set bắt buộc:

```text
padding: 0
color: #fff
appearance: none
margin: 0
```

**Lý do specificity:**

```text
.editor-styles-wrapper button     → (0, 1, 1)  thắng .skvn-slider__arrow (0, 1, 0) về padding
.editor-styles-wrapper … .skvn-slider__arrow → (0, 3, 1) hoặc cao hơn → thắng editor global button
```

**Đã reject:**

| Phương án | Vì sao reject |
|-----------|----------------|
| `!important` trên padding | Che cascade; khó debug layer sau |
| Sửa theme reset toàn bộ `.editor-styles-wrapper button` | Ảnh hưởng mọi block; scope quá rộng |
| Chỉ tăng `font-size` glyph | Không sửa content box 4px |

**File:** `src/slider/style.css` (block CASE-006)

---

### D4 — Contrast token riêng cho circle trên preview bar

**Quyết định:** Chỉ trong editor preview + style `circle`:

```css
background: rgba(255, 255, 255, 0.16);
border-color: rgba(255, 255, 255, 0.5);
```

**Lý do:**

- Preview bar `#073b5a` là **editor chrome**, không phải slide ảnh.
- Frontend circle vẫn dùng `rgba(7, 59, 90, 0.92)` — không đổi spec 1.3.1.
- Marketing editor phải **đọc được** preview mà không cần frontend.

**Đã reject:**

| Phương án | Vì sao reject |
|-----------|----------------|
| Đổi frontend fill cho giống editor | Regression onsite slider trên ảnh tối |
| Bỏ preview bar navy | Mất visual link với brand bar; scope UX |

**Invariant:** Frontend arrow look ≠ editor preview bar look — **cố ý**.

---

### D5 — Glyph `::after` explicit trong editor preview

**Quyết định:** Rule riêng cho preview `::after`:

```text
font-family: swiper-icons
font-size: var(--skvn-slider-arrow-glyph-size)
color: #fff
```

**Lý do:** Swiper `navigation.css` set `font-size: var(--swiper-navigation-size)` trên
`.swiper-button-prev:after` — khi color/padding leak, glyph layer cũng lệch.

**File:** `src/slider/style.css`

---

### D6 — Hardening frontend arrow base (phụ, cùng file)

**Quyết định:** Thêm vào block selector frontend arrows:

```text
color: #fff
padding: 0
```

trên `.skvn-slider__arrows .skvn-slider__arrow.swiper-button-prev|next|--prev|--next`.

**Lý do:** Cùng conflict Swiper theme color có thể xảy ra frontend khi load order thay đổi.
Phòng thủ nhẹ, không đổi geometry frontend đã verify.

---

## 5. Implementation map (source of truth)

| Hạng mục | File | Thay đổi |
|----------|------|----------|
| Markup preview | `src/slider/edit.tsx` | Bỏ `swiper-button-*` trên preview buttons |
| Runtime guard | `src/slider/view.ts` | Early return nếu `.skvn-slider--editor` |
| Editor + frontend CSS | `src/slider/style.css` | CASE-006 block + frontend `color`/`padding` |
| Regression | `tests/slider-block.test.mjs` | Không swiper hook trong preview; editor padding rule; view guard |

**Không đổi:**

- `save.tsx` / PHP render — vẫn `swiper-button-prev/next` cho Swiper runtime
- `docs/decisions/slider-navigation-and-pagination-controls.md` — frontend arrow spec
- Theme `generatepress/**`

---

## 6. Quy tắc cho agent (bắt buộc)

### 6.1 Trước khi sửa

```text
1. Đọc gutenberg-editor-chrome-contract.md §2.5–§2.6
2. Đọc file này nếu task chạm editor preview arrows/pagination
3. Xác định surface: toolbar vs editor-preview vs frontend overlay
```

### 6.2 Khi thêm/sửa editor decorative control

```text
[ ] Không dùng swiper-button-* / swiper-pagination / data-skvn-slider hook trên editor shell
[ ] Selector thắng .editor-styles-wrapper button (computed padding === 0)
[ ] color #fff trên button và ::after (không rgb(0,122,255))
[ ] Preview contrast đủ trên #073b5a (không copy frontend fill mù)
[ ] Không !important trước computed DIFF
```

### 6.3 Verify (human hoặc agent hướng dẫn human)

Checklist: `docs/testing/slider-editor-arrow-preview-1.3.6.md`

Hard refresh editor iframe sau plugin build.

### 6.4 Anti-pattern “một lần nữa”

```text
“Preview cần giống frontend 100%”
  → gắn swiper-button-prev vào edit.tsx
  → view.ts bind Swiper
  → theme/editor padding leak
  → blue 44px glyph
  → agent chỉ sửa font-size
  → vẫn lỗi
```

---

## 7. Regression guards

| Guard | Location |
|-------|----------|
| Preview không có `swiper-button-prev` trong editor-preview | `tests/slider-block.test.mjs` |
| CSS `.editor-styles-wrapper … padding: 0` | `tests/slider-block.test.mjs` |
| `view.ts` guard `skvn-slider--editor` | `tests/slider-block.test.mjs` |
| Pitfall index | `docs/debug-casebook/PITFALLS.md` Pitfall 6 |
| Manual onsite | `docs/testing/slider-editor-arrow-preview-1.3.6.md` |

---

## 8. Open / deferred

- **Onsite verify** editor iframe sau deploy — status PENDING.
- **Minimal / pill** preview trên bar navy: chưa có contrast token riêng từng style;
  nếu human báo lỗi tương tự → mở rộng D4 theo `arrowStyle`.
- **Theme-level** `.editor-styles-wrapper button { padding: 0 }` cho mọi SKVN decorative
  control: deferred — fix từng component scope trước.

---

## 9. Đọc theo thứ tự (onboarding agent mới)

```text
1. docs/decisions/gutenberg-editor-chrome-contract.md
2. docs/decisions/slider-editor-arrow-preview-1.3.6.md   ← file này
3. docs/debug-casebook/slider/006_*.md
4. docs/debug-casebook/PITFALLS.md — Pitfall 4, 5, 6
5. src/slider/edit.tsx + style.css (editor-preview section)
```