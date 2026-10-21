# 038 — Collection Carousel Visual Lock (Path B, Path C = plan-B)

**Follow-up of:** 1.3.7 Collection UI & Card Styles (`028`)
**Does not replace** current milestone **V1 / 1.3.10** unless human explicitly starts this work.
**Status:** PLANNING — intake locked 2026-08-25; waiting human demo data + `làm B`
**Surface:** `skvn-marine/product-collection` layout `carousel` first; post carousel after product PASS
**Not this work:** Slider Hero / Product Showcase / Card Carousel (`019`, `skvn-marine/slider`)

**Design source of truth:**

```text
.local/Seafood Export Carousels Tailwind.html
```

`.local/Seafood Export Carousels Tailwind.debug.html` is the same layout with shorter class dumps — do not use as source of truth. Do not use `.local/Seafood Carousels.html` (other bundle).

**Human intake locked (2026-08-25):**

| ID | Answer |
|---|---|
| A1 | Làm B ngay khi human gõ `làm B` (xếp 1.3.10 trong phiên carousel) |
| A2 | Chỉ Product Carousel |
| A3 | PASS = chạy + khớp layout/card stack (mục 2). MOQ/cert hiện nếu data B1 đã nhập |
| B1 | Artifact = `Seafood Export Carousels Tailwind.html` |
| B2 | Cấu trúc + motion + card stack; không khóa Inter/32px |
| B3 | Viewport chấm: 1280 / 880 / 360 |
| C1 | Verify **local và onsite** (human gửi URL từng môi trường khi sẵn) |
| C2–C4 | Human tự chuẩn bị trang + ≥6 product |
| D | D1=1 meta+tag+attribute; D2 CTA **Yêu cầu báo giá**; D3 PDF ẩn khi không URL |

Demo field values: `docs/testing/collection-carousel-demo-data-038.md`.

**Existing architecture (keep):**

- Decision: `docs/decisions/skvn-dynamic-collections-1.3.3.md`
- Card/footer/header: `modules/collection-render/cards.php`
- CSS: `src/collection/style.css`
- Runtime: `src/collection-view.ts` (Swiper 11.1.x)

---

## 0. Why 1.3.7 shipped but still fails the design

| Layer | Artifact | Current code | Effect |
|---|---|---|---|
| Track | Native `overflow-x` + `scroll-snap-type: x mandatory` | Swiper, `slidesPerView` 3-2-1, breakpoints 640/1024, `spaceBetween: 16` | Motion ≠ mockup (no 4-up, no 80% peek, loop clones possible) |
| Card B2B | Always show chips, cert dots, MOQ, lead time, quote CTA, PDF | MOQ/cert/PDF only if `_skvn_*` meta; empty = hidden | Card looks generic |
| Visual | Body pad 18px / gap 13px; hover lift + navy shadow; CTA hover `#0E3A66` | Pad 1rem / gap 0.65rem; CTA hover teal `#0D9488`; weaker shadow | “Na ná” not locked |
| Nav | Arrows header, hide `<880px`; dots = page groups; no loop, no autoplay | Arrows always if `showArrows`; Swiper bullets per slide; autoplay optional | Extra chrome vs catalog |
| Data | Hardcoded demo products | Woo products + empty catalog meta | Cannot look like Export Catalog without demo data |

Opus delivered **engine + attributes**. This plan delivers **visual + motion lock against one HTML artifact**, with a gated engine fallback.

---

## 1. Locked choices (this planning session)

| # | Item | Decision |
|---|---|---|
| D-1 | Primary path | **B** — keep Product Collection + Swiper; lock CSS and Swiper geometry to the artifact |
| D-2 | Fallback | **C** — native scroll-snap track, **only** after B motion FAIL (gates in §5) |
| D-3 | Card markup | Shared. C must not redesign cards. Track/runtime only. |
| D-4 | Order | Product carousel → Post carousel. Do not start post until product B (or C) PASS. |
| D-5 | Catalog data | Demo via Woo + `_skvn_*` meta (or visible product attributes) for visual lock. Owner long-term remains **1.5.0 woo-catalog**. |
| D-6 | Loop / autoplay | Catalog default **off**, matching artifact. Keep attributes; do not force on. |
| D-7 | Width owner | Collection is a **component** inside Gutenberg/SKVN canvas. No `100vw` / `50vw`. See `docs/standards/css-layout-safety-contract.md`. |
| D-8 | Hex in plugin CSS | Artifact navy `#0A2540` / teal `#0D9488` map to existing SKVN tokens when a var exists; otherwise keep hex **only inside plugin collection CSS** (A13 debt). Do not put raw hex in saved block content. |

---

## 2. Artifact contract (acceptance numbers)

Source: `.local/Seafood Export Carousels Tailwind.html` — product array ~line 841; card CSS in the page `<style>` + inline card rules.

### 2.1 Track

```text
display: flex
gap: 20px                    (Tailwind gap-5)
overflow-x: auto
scroll-snap-type: x mandatory
scrollbar: hidden
```

Card flex-basis (class `.c-card`):

| Viewport | Visible | Basis | Notes |
|---|---|---|---|
| > 1180px | 4 | `calc((100% - 60px) / 4)` | 3 gaps × 20px = 60px |
| ≤ 1180px | 3 | `calc((100% - 40px) / 3)` | |
| ≤ 880px | 2 | `calc((100% - 20px) / 2)` | Arrows **hidden** (`.hide-md`) |
| ≤ 600px | peek | `80%` | Next card visible — **signature motion** |

Hover: `translateY(-6px)`, shadow `0 22px 44px -18px rgba(10,37,64,.34)`, image scale `1.045`.

No loop. No autoplay. Drag/scroll native. Arrows disabled at start/end.

### 2.2 Product card stack (top → bottom)

```text
media 1:1 + badge overlay (product_tag)
title
spec chips (visible Woo attributes)
cert dots (HACCP / EU Approved / …)
stats row: MOQ | divider | Lead time
CTA full width — Yêu cầu báo giá
PDF link — Tải spec sheet PDF (if URL)
```

Footer: dots (left) + ghost “Tải catalog (PDF)” + “Xem toàn bộ danh mục”.

### 2.3 Post card (after product PASS)

```text
media 16:9 + category overlay
date then title then excerpt
read-more + arrow
```

Same track breakpoints as product.

---

## 3. Path B — Visual-lock on current Swiper (DO THIS)

Keep `data-skvn-collection-carousel` + Swiper. Change **geometry and CSS**, not the block family.

### B0 — Smoke: “chạy được”

Preconditions (human):

- WooCommerce on, **≥ 6 products** with images or placeholder
- One test page with **SKVN Product Carousel** only
- Plugin build current (`npm run build` after source change)

PASS:

- Swiper inits, no console error
- Arrow prev/next move slides
- Keyboard / a11y still on
- Grid layout of the same block is untouched

FAIL → fix `collection-view.ts` / markup, **do not** start C, **do not** restyle cards yet.

### B1 — Demo catalog data (human in wp-admin, agent documents keys)

Existing readers in `cards.php`:

```text
_skvn_certifications   array of strings
_skvn_moq              string   e.g. "5 MT"
_skvn_lead_time        string   e.g. "15–20 ngày"
_skvn_spec_sheet_url   URL
product_tag            Wild Caught / Farmed
visible product attributes → spec chips
```

Fill **4–6 products**. Without this, B2 cannot PASS “looks like Export Catalog”.

Do not invent a settings UI. Do not start `woo-catalog`.

### B2 — Card visual lock (CSS + small PHP only if DOM mismatch)

Files: `src/collection/style.css`; `cards.php` only if a **required** node is missing (stats divider, cert bullet as 5px dot, PDF icon slot).

Delta vs artifact (must close):

| Token | Artifact | Current plugin CSS |
|---|---|---|
| Card body padding / gap | 18px / 13px | 1rem / 0.65rem |
| Image ratio product | **1:1 on media box** | ratio class on `img`; media not squared |
| Hover shadow | navy 22/44 | `0 8px 24px rgba(0,0,0,.1)` |
| CTA hover | `#0E3A66` | `#0D9488` |
| Stats | 2 col + 1px divider | grid, no divider |
| Cert | 5px teal disc + label | `• ` via `::before` on text |
| Footer dots | compact group | Swiper default bullets |

Editor: no `opacity: 0`. Reduced-motion: no lift/scale.

### B3 — Swiper geometry lock (still Path B)

Change `collection-view.ts` + CSS so the **track matches §2.1** as far as Swiper 11 allows.

Target config (catalog carousel):

```text
spaceBetween: 20
loop: false unless human enables and slideCount requires it
slidesPerView: 'auto'  OR  4 / 3 / 2 with matching CSS basis
breakpoints aligned to 600 / 880 / 1180 — not 640 / 1024
watchOverflow: true
arrows hidden at ≤880px (CSS), disabled at ends (Swiper already)
```

Mobile peek: `slidesPerView: 1.2` **or** CSS `80%` + `slidesPerView: 'auto'`. Prefer CSS basis so 4-up math matches artifact (`100% - gaps`).

Do **not** use `100vw`. Overflow stays on `.skvn-collection__carousel` (component owner). Do not `overflow-x: hidden` on page canvas to hide mistakes.

Responsive preset `3-2-1` vs artifact `4-3-2-peek`: B3 should add or map a catalog preset **`4-3-2`** (mobile = peek, not 1). Existing `3-2-1` remains for editors who want it. Default for **new** Product Carousel variation: `4-3-2` if human agrees at implement start; otherwise keep `3-2-1` and only lock CSS for that preset.

### B4 — Last Swiper attempt before C (`cssMode`)

If after B3 human still says “không trượt như HTML”:

Enable Swiper 11 `cssMode: true` on collection carousel only (native scroll under Swiper API). Cite: Swiper 11 docs for the version in `package.json` (`^11.1.15`), not generic knowledge.

PASS `cssMode` → **stay on B. Do not implement C.**

Document in a short decision note when implement lands.

### B5 — Post carousel

Same track as product. Card CSS from artifact Insights section. Only after product B0–B3 PASS (B4 optional).

---

## 4. Path C — Native scroll-snap (PLAN-B only)

### 4.1 When C is allowed

All of:

1. B0 PASS (it runs).
2. B2 PASS (cards look right) **or** human explicitly says skip remaining CSS because motion is the blocker.
3. B3 done.
4. B4 (`cssMode`) tried **or** human waives B4 in writing.
5. Human FAIL on **motion gates** in §5 (not taste, not color).

If B0 FAIL, fix Swiper. C is not a debugger.

### 4.2 What C changes

Replace **track + small controller** only.

Keep:

- `skvn-marine/product-collection` / `post-collection`
- Card PHP, footer CTAs, query, editor attributes (arrows, pagination, autoplay)
- Plugin-owned CSS for cards

Change:

- `render_collection_carousel()`: wrapper is a scroller, **not** `.swiper` / `.swiper-slide`
- `collection-view.ts`: if C is active, do **not** construct `new Swiper` for that container
- CSS: flex + snap + hidden scrollbar + card basis from §2.1
- JS (~80 lines): `scrollBy` one card, enable/disable arrows, update dots, `prefers-reduced-motion` → instant `scrollTo`, no loop, autoplay remains off unless already specified then **skip autoplay on C** (artifact has none; do not write a second timer)

Grid layout must not load this runtime (already true).

### 4.3 Constraint / tension

`PLUGIN_SKVN_MARINE_BLOCKS.md`: “Carousel may reuse the Slider/Swiper adapter”. C is a **second collection track**, not a second Slider engine, and only after B fails.

When C is actually started: write a **LOW** tension in `TENSIONS_OPEN.md` (tag `blocks`, `slider`), conservative = C collection-only, Swiper remains for `skvn-marine/slider`. Do not open that tension while still on B.

### 4.4 Reversibility

C must be one commit-sized swap of wrapper + view. Cards unchanged so reverting C restores B without card regression.

Do not add a user-facing “engine” Inspector control in V1.

---

## 5. Motion FAIL gates (B → C)

Human compares **debug HTML vs onsite** at the same viewport. FAIL if **any** remain after B3+B4:

| ID | Gate | Measure |
|---|---|---|
| M1 | Mobile peek | ≤600px: next card visible (~80% current card width) |
| M2 | Finger/trackpad | Drag feels like native scroll, not a rubber slide with clone flash |
| M3 | No clones | DOM has no loop duplicate slides |
| M4 | Ends | First/last: prev or next disabled; cannot wrap |
| M5 | Arrows ≤880 | Header arrows not shown (touch scroll only) |

Color/spacing FAIL → stay on B2, not C.

---

## 6. Implementation order

```text
1. Human: test page + ≥6 products + B1 meta
2. B0 smoke
3. B2 card CSS (and tiny PHP if DOM)
4. B3 Swiper breakpoints / auto slides / 20px gap / no loop default
5. Human visual compare (desktop 1280, tablet 880, mobile 360)
6. If motion FAIL → B4 cssMode
7. If still FAIL → human says "làm C" → LOW tension + C track
8. B5 post carousel
9. Test md + decision snippet
```

Agent does not run `npm run build` (AGENTS.md). After plugin edits, print the combined build+lint command for human.

---

## 7. Files

**Allowed (B):**

```text
wp-content/plugins/skvn-marine-blocks/src/collection/style.css
wp-content/plugins/skvn-marine-blocks/src/collection-view.ts
wp-content/plugins/skvn-marine-blocks/src/product-collection/block.json   (preset 4-3-2 for new carousel)
wp-content/plugins/skvn-marine-blocks/modules/collection-render/cards.php
  (read _skvn_certifications as array or comma-separated string)
docs/testing/onsite-collection-carousel-visual-lock.md   (create at implement)
```

**Additional if C starts:**

```text
same PHP wrapper in cards.php
same collection-view.ts (branch: snap controller vs Swiper)
.context/TENSIONS_OPEN.md
```

**Forbidden:**

```text
themes/generatepress/**
src/slider/** , src/slide/**          (wrong carousel)
modules/core-control/**               (out of scope)
woo-catalog plugin creation
new block namespace
```

Keep ≤5 files per implement slice (B2 then B3 then optional B4/C).

---

## 8. Out of scope

- 1.5.0 `woo-catalog` data model / cert taxonomy
- Faceted filter, AJAX, taxonomy collections
- Step Slider 1.3.10
- Slider Card Carousel showcase (`019`)
- Changing current milestone without human
- Pixel-perfect Inter/font-size on heading (theme typography 1.6.0) — match **structure + motion + card stack**, not a new type system

---

## 9. Test (create when implementing)

`docs/testing/onsite-collection-carousel-visual-lock.md`

Must include: artifact path, test URL, viewport list (1280 / 880 / 360), B0–B3 checklist, M1–M5, evidence = screenshots side-by-side with debug HTML + console notes.

---

## 10. Human start signal

This file is planning only.

- `làm B` / `implement B` → CODE_NOW from B0/B2
- `làm C` → only valid after §5; otherwise agent reminds gates
- No signal → do not touch collection source; 1.3.10 stays current milestone
