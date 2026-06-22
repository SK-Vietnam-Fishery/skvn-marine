# Slider Bottom-Center Flank Controls

**Version:** 1.0  
**Target milestone:** V1 / 1.3.6 (layout only — separate from slide motion)  
**Date:** 2026-06-19  
**Status:** APPROVED DIRECTION — pending implementation  
**Decision amend:** `docs/decisions/slider-navigation-and-pagination-controls.md` §5.1  
**UX reference:** `docs/artifacts/slider-parallax-1.3.6-mockup.html` (controls row only)

---

## 1. Goal

When Slider arrows and pagination **both** use `bottom-center`, render controls as:

```text
‹  |  pagination  |  ›
```

instead of the default cluster:

```text
[ ‹ › ]  |  pagination
```

Pagination **style** does not matter (`dots`, `fraction`, `timed-fraction`, `timed-segments`).
Pagination **position** must still be `bottom-center` — this is not a global override.

---

## 2. Relationship to 1.3.1 cluster contract

| Rule | Scope |
|------|--------|
| **Default cluster** §5 | `bottom-left`, `bottom-right`, and `bottom-center` when flank does not apply → `arrows \| pagination` |
| **Flank exception** §5.1 | `bottom-center` + both controls visible + arrow style not `pill` → `prev \| pagination \| next` |
| **Pill** | Always follows default cluster order (grouped capsule + pagination) |

Flank is an **additional layout modifier**, not a replacement of the cluster system.

---

## 3. Trigger conditions

Enable `skvn-slider__controls--cluster-flank` when **all** are true:

1. `showArrows === true`
2. `showPagination === true`
3. `arrowPosition === 'bottom-center'`
4. `paginationPosition === 'bottom-center'`
5. `arrowStyle !== 'pill'`
6. `slideCount > 1` (same gate as existing arrow/pagination render)

When pagination is off but arrows are `bottom-center`, keep the existing paired-arrow layout (no flank — nothing to flank).

When positions differ (e.g. arrows `bottom-center`, pagination `bottom-left`), each control keeps its **independent** absolute position per §5.

---

## 4. Implementation sketch

### 4.1 Markup (PHP render + editor preview)

Inside `skvn-slider__controls--cluster.skvn-slider__controls--bottom-center`:

**Flank mode** — three siblings, no grouped `.skvn-slider__arrows` wrapper:

```html
<div class="skvn-slider__controls skvn-slider__controls--cluster skvn-slider__controls--bottom-center skvn-slider__controls--cluster-flank">
  <button class="skvn-slider__arrow skvn-slider__arrow--prev swiper-button-prev" …></button>
  <div class="skvn-slider__pagination …">…</div>
  <button class="skvn-slider__arrow skvn-slider__arrow--next swiper-button-next" …></button>
</div>
```

**Default cluster** — unchanged:

```html
<div class="skvn-slider__controls skvn-slider__controls--cluster …">
  <div class="skvn-slider__arrows">prev + next</div>
  <span class="skvn-slider__controls-separator" aria-hidden="true"></span>
  <div class="skvn-slider__pagination">…</div>
</div>
```

Swiper `navigation.prevEl` / `nextEl` selectors stay `.swiper-button-prev` / `.swiper-button-next`.

### 4.2 Files

| File | Change |
|------|--------|
| `modules/slider-render/slider-render.php` | Flank branch in controls render |
| `src/slider/save.tsx` | Static markup parity (if still used on any path) |
| `src/slider/edit.tsx` | Editor static controls preview |
| `src/slider/style.css` | `.skvn-slider__controls--cluster-flank` flex layout |
| `docs/decisions/slider-navigation-and-pagination-controls.md` | §5.1 amend |

### 4.3 CSS

- Flex row, `align-items: center`, `justify-content: center`, `gap: 0.75rem`
- Parent centered with existing `bottom-center` cluster rules (`left: 50%`, `translateX(-50%)`)
- Arrow circle `::after` fix (1.3.6) applies in both layouts
- **Mobile timed pagination width** — implement first; onsite shrink/tune deferred to 1.3.9 QA

---

## 5. Out of scope (separate milestone / Trục B)

Slide transition motion from artifact (bg slower than content) is **not** part of this plan.

Source of truth for motion: `docs/artifacts/slider-parallax-1.3.6-mockup.html` + `.context/planning/026_VER_1_3_6_BLOCK_EDITOR_UX_AND_SLIDER_PARALLAX_PLANNING.md` (Swiper Parallax Module / Trục B).

---

## 6. Acceptance draft

- [ ] `circle` + `minimal` + `bottom-center` arrows + `bottom-center` pagination → `‹ pagination ›`
- [ ] All four pagination styles render correctly in center flank row
- [ ] `pill` + `bottom-center` → default `arrows | pagination` cluster (no flank)
- [ ] `bottom-left` / `bottom-right` cluster unchanged
- [ ] `side-center` arrows unchanged
- [ ] Mismatched positions (arrow center, pagination left) → independent placement
- [ ] Swiper prev/next clicks and keyboard nav still work
- [ ] Editor preview matches frontend flank layout
- [ ] Mobile timed pagination overflow — **deferred**; note evidence in 1.3.9 QA

---

## 7. Human decisions locked (2026-06-19)

- Flank requires **same** `bottom-center` on arrows and pagination.
- Pagination **type** is irrelevant for flank eligibility.
- Default cluster order remains the general rule; flank is a bottom-center exception.
- Pill follows default cluster only.
- Layout milestone separate from artifact transition motion.
- Mobile width tuning after implementation + onsite test.