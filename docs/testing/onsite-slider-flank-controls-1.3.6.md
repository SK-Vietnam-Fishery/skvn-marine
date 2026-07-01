# Onsite QA — Slider Bottom-Center Flank Controls (V1 / 1.3.6)

**Planning:** `.context/planning/archives/031_VER_1_3_6_SLIDER_BOTTOM_CENTER_FLANK_CONTROLS_PLANNING.md`  
**Decision:** `docs/decisions/slider-navigation-and-pagination-controls.md` §5.1  
**Milestone:** V1 / 1.3.6  
**Status:** READY — chạy sau khi implement + `npm run build`

---

## Preconditions

1. Plugin `skvn-marine-blocks` built và deploy sang runtime site.
2. Có ít nhất một trang test với `skvn-marine/slider` ≥ 2 slides.
3. Slider settings baseline cho case flank:
   - Show arrows: ON
   - Show pagination: ON
   - Arrow position: **Bottom center**
   - Pagination position: **Bottom center**
   - Arrow style: **Circle** hoặc **Minimal** (không dùng Pill cho case flank chính)

---

## Test 1 — Flank layout (circle + bottom-center)

### Steps

1. Mở trang slider frontend (hard refresh).
2. Quan sát hàng controls dưới slide: kỳ vọng `‹ ··· pagination ··· ›` trên một hàng giữa.
3. DevTools → Elements: tìm `.skvn-slider__controls--cluster-flank`.
4. Kiểm tra children **không** có `.skvn-slider__arrows` wrapper và **không** có `.skvn-slider__controls-separator`.
5. Chạy trong console:

```javascript
const row = document.querySelector('.skvn-slider__controls--cluster-flank');
console.table([...row.children].map((n, i) => ({
  i,
  tag: n.tagName,
  classes: n.className.split(/\s+/).slice(0, 4).join(' '),
})));
```

### Expected

- Class `skvn-slider__controls--cluster-flank` present.
- DOM order: `BUTTON prev` → pagination container → `BUTTON next`.
- Pagination visually centered between arrows.

### Pass criteria

- [ ] Layout matches mockup intent (`docs/artifacts/slider-parallax-1.3.6-mockup.html` controls row only).
- [ ] Console table shows 3 siblings, no separator.

### Evidence to report

- Screenshot full slider bottom controls.
- Console table output hoặc screenshot Elements tree.

---

## Test 2 — Pill exception (cluster cũ)

### Setup

- Arrow style: **Pill**
- Arrow position: Bottom center
- Pagination position: Bottom center

### Expected

- **No** `skvn-slider__controls--cluster-flank`.
- Layout vẫn: grouped pill arrows `|` pagination (cluster §5).

### Pass criteria

- [ ] Pill capsule gom prev+next, pagination bên cạnh — không flank.

### Evidence

- Screenshot.

---

## Test 3 — Pagination styles trong flank

Lặp lại Test 1 với từng pagination style:

- [ ] Dots
- [ ] Fraction
- [ ] Timed fraction
- [ ] Timed segments

### Expected

Mỗi style render đúng trong hàng flank; không overlap arrows; timed animation vẫn chạy.

### Deferred (ghi note, không fail 1.3.6)

- Mobile timed pagination overflow — tune tại 1.3.9 QA.

---

## Test 4 — Regression positions

| Case | Arrow pos | Pag pos | Expected |
|------|-----------|---------|----------|
| A | bottom-left | bottom-left | Cluster, no flank |
| B | bottom-right | bottom-right | Cluster, no flank |
| C | side-center | bottom-center | Independent |
| D | bottom-center | bottom-left | Independent |

### Pass criteria

- [ ] A, B: cluster without flank
- [ ] C, D: không có `--cluster-flank`

---

## Test 5 — Interaction

1. Click prev/next — slide đổi.
2. Click pagination (dots hoặc segment) — slide đổi.
3. Keyboard Left/Right khi focus slider — slide đổi.
4. Autoplay (nếu bật) — vẫn chạy; hover pause vẫn hoạt động.

### Pass criteria

- [ ] Không regression navigation sau đổi DOM flank.

---

## Test 6 — Editor preview parity

1. Mở cùng slider trong block editor.
2. Static controls preview (decorative bar dưới slide stack) phải mirror flank: prev | pag mock | next.
3. Không click được preview arrows (pointer-events none) — chỉ visual.

### Pass criteria

- [ ] Editor preview layout khớp frontend (không cần pixel-perfect Swiper).

### Evidence

- Screenshot editor + frontend side-by-side nếu có thể.

---

## Fail handling

Nếu fail:

1. Ghi lại attrs slider (arrow style/position, pagination style/position).
2. View source đoạn `.skvn-slider__controls`.
3. Báo agent — tham chiếu CASEbook nếu DOM đúng nhưng CSS sai (ui-debug State Delta).

---

## Sign-off

| Role | Date | Result |
|------|------|--------|
| Human onsite | | PASS / FAIL |
| Agent follow-up | | |