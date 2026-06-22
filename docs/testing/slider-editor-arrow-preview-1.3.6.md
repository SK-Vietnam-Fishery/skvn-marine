# Slider Editor Arrow Preview — Test Checklist (V1 / 1.3.6)

Status: `READY — onsite/local verify pending`

Decision: `docs/decisions/slider-editor-arrow-preview-1.3.6.md`

Case: `docs/debug-casebook/slider/006_SLIDER_EDITOR_ARROW_PREVIEW_CSS_CASCADE.md`

---

## Preconditions

- Plugin built after CASE-006 fix:

```bash
source /home/shinkuro/.nvm/nvm.sh && nvm use 20 && cd /mnt/d/Github/skvn-marine/wp-content/plugins/skvn-marine-blocks && npm run build
```

- Hard refresh block editor (Ctrl+Shift+R) or reload editor tab.
- Test block: `skvn-marine/slider` with **Show arrows** on, **Arrow style: Circle**.

---

## Target

- Editor canvas: static controls preview bar below slide stack
- Class path: `.skvn-slider--editor .skvn-slider__controls--editor-preview`

---

## Steps

1. Insert or open a page with SKVN Slider in block editor.
2. Sidebar → Navigation → Show arrows ON, Arrow style **Circle**.
3. Scroll to navy preview bar under slides.
4. DevTools → inspect **Previous slide** preview button.
5. Repeat for **Next** if needed.

---

## Expected UX / visual

- Circle readable on navy bar `#073b5a` (light frosted fill or clear border — not “empty ring only”).
- Chevron **white**, centered, size proportional to circle (~1rem glyph).
- No oversized blue chevron shifted left.

---

## DevTools computed — PASS criteria

On `button.skvn-slider__arrow.skvn-slider__arrow--prev` inside editor preview:

| Property | PASS |
|----------|------|
| `padding-top/bottom/left/right` | `0px` |
| `color` | `rgb(255, 255, 255)` |
| `width` / `height` | `44px` (2.75rem at 16px root) |
| `background-color` (circle) | `rgba(255, 255, 255, 0.16)` or equivalent preview token |
| `aria-controls` | **absent** (no `swiper-wrapper-*`) |

On `button::after`:

| Property | PASS |
|----------|------|
| `font-family` | contains `swiper-icons` |
| `font-size` | `16px` (1rem) |
| `color` | white / inherited white |

---

## FAIL signals

- `padding: 10px 20px` → editor global button still winning (CASE-006 regression).
- `color: rgb(0, 122, 255)` → Swiper theme leak.
- `aria-controls="swiper-wrapper-…"` → `view.ts` bound preview (hook class or guard regression).
- Glyph clipped / flush left with 44px box → padding leak not fixed.

---

## Frontend regression (same session)

1. View page on frontend (not editor).
2. Circle arrows on slide: filled navy `rgba(7,59,90,0.92)`, white glyph, Swiper nav works.
3. Editor preview look **may differ** from frontend on preview bar — that is intentional (D4).

---

## Evidence to report back

- Screenshot editor preview bar (before/after if available).
- Screenshot computed styles: button + `::after` (padding, color, font-size).
- Confirm `aria-controls` absent on preview button.
- Frontend arrow screenshot optional if regression suspected.

---

## Pass / fail

- **PASS:** All computed criteria + visual readable circle/glyph on preview bar.
- **FAIL:** Any FAIL signal above — file issue against CASE-006 / decision doc, do not patch with `!important` without computed DIFF.