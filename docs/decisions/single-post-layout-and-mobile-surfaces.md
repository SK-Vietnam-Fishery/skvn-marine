# Single Post Layout & Mobile Surfaces — Decisions

Status: **DECIDED** (implemented in theme)  
Date: 2026-06-19  
Milestone context: V1 / 1.3.6 Trục C (Single Post Fix)  
Sources merged:

- `.context/planning/archives/030_VER_1_3_7_SINGLE-POST-HERO-LAYOUT.md` — hero grid fix (Haiku)
- `docs/workflows/ideation-end-user-deep-customization.md` — future customization (brainstorm only)
- Mobile surface pass — flatten islands + article + responsive gutter (Grok, hướng 3 A+B+C)

Related evidence:

- `docs/testing/single-post-hero-layout-review-1.3.6.md` — UI debug + Verify 2 DevTools
- `docs/decisions/skvn-1.3.x-architecture-and-decisions.md` — island pattern (desktop)

**Ý tưởng chưa quyết định / future customization:** đọc `docs/workflows/ideation-end-user-deep-customization.md` — file này **không** duplicate brainstorm.

**Phương pháp làm việc (Grok + human → handoff Haiku):** `docs/workflows/agent-ui-layout-collaboration-method.md`

---

## 1. Vấn đề ban đầu

| Issue | Root cause | Trạng thái |
|-------|------------|------------|
| Hero nằm cột trái hẹp cạnh article (desktop) | `.skvn-post-hero` và `.skvn-single-wrap` là **siblings** trong GP `.site-content` flex (`right-sidebar`) | **Đã đóng** — plan 030 |
| Content mobile lùi sâu (island + margin chồng) | Wrap `16px` + `.skvn-post-body` card `24px` + `.skvn-island` card `16px` — không có mobile override | **Đã đóng** — mobile surfaces (hướng 3) |

---

## 2. Quyết định đã chốt — Desktop hero layout (plan 030)

### Decision D1 — Magazine layout là default V1

Single post dùng **Option B — Magazine**: hero top-left (cột `2fr`), sidebar phải span 2 rows, main dưới hero cùng cột trái.

```
┌─────────────────────┬───────────┐
│   .skvn-post-hero   │  sidebar  │  row 1
├─────────────────────┤           │
│  .skvn-single-main  │           │  row 2
└─────────────────────┴───────────┘
```

**Cách implement:** HTML restructure + CSS grid placement — **không** override GP `.site-content { display: flex }` (fragile).

| File | Thay đổi |
|------|----------|
| `single.php` | Move `.skvn-post-hero` **vào trong** `.skvn-single-layout`, trước `<main>` |
| `style.css` | `grid-template-rows: auto 1fr`; placement hero/main/sidebar; responsive stack ≤900px |

### Decision D2 — Không full-bleed hero trong fix tức thời

Hero bounded trong SKVN page canvas (GP content width). Full-width hero banner = **future** (Customizer ~1.6.0, Option A trong ideation).

### Decision D3 — CSS layout safety

- Không `100vw`, không margin âm viewport, không `overflow-x` che geometry
- Layout owner: `.skvn-single-layout` (grid)
- `aspect-ratio: 16/9` hero — **chưa** gộp trong 030; vẫn debt checklist 1.3.6 nếu human yêu cầu sau

### Decision D4 — Không mở layout class contract trong 030

Plan 030 **chỉ** fix magazine grid; **không** thêm `.skvn-post-layout--banner|--magazine|--no-hero` trong lượt fix. Class contract = candidate khi implement customization (xem ideation).

### Verify 2 (onsite desktop geometry) — PASS

URL: `https://minhhaifishery.com/iqf-technology-for-seafood-industry/`

```text
wrapW: 1200, heroAboveMain: true
```

→ Wrap full canvas; hero trên main; bug GP flex sibling **đóng** về trục dọc.

---

## 3. Quyết định đã chốt — Mobile surfaces (hướng 3: A+B+C)

Human chọn **hướng 3** sau so sánh hướng 2 vs 3. DevTools baseline (360px): `bodyTextW: 278` (~77% viewport).

### Decision D5 — Mobile: flatten sidebar islands (A)

≤900px, `.skvn-island` (trừ navy) **không còn card chrome**:

- `background: transparent`, `border: none`, `border-radius: 0`, `padding: 0`
- Phân tách section bằng `border-bottom`
- `.skvn-island--navy` (Quote CTA): **full-bleed band** trong wrap — `margin-inline: calc(-1 * var(--skvn-page-gutter))`
- Sidebar `gap: 0` (tránh double spacing với divider)

**Scope:** shared component — áp **archive + single** sidebar (cùng `.skvn-island`).

### Decision D6 — Mobile: flatten article card (B)

≤900px, `.skvn-post-body`:

- Bỏ border, radius, white card background
- `padding-inline: 0` — gutter do wrap owner
- `padding-block: 1.5rem`
- Hero trong grid: `border-radius: 0` (mobile stack)

### Decision D7 — Mobile: responsive page gutter (C)

Token `--skvn-page-gutter` trên page wraps:

| Breakpoint | Gutter |
|------------|--------|
| Desktop | `1rem` |
| ≤900px | `0.75rem` |
| ≤600px | `0.5rem` |

**Owners dùng token:**

- `.skvn-archive-wrap`
- `.skvn-single-wrap`
- `.woocommerce div.product`, `.skvn-product-layout`

Navy island negative margin **luôn** sync với `--skvn-page-gutter` (một owner, không hardcode `-1rem`).

### Decision D8 — `bodyTextW` kỳ vọng sau fix

| Viewport | Trước | Sau (ước lượng) |
|----------|-------|-----------------|
| 360px, ≤900px | 278px | ~336px |
| 360px, ≤600px | 278px | ~344px |

### File thay đổi

Chỉ `wp-content/themes/skvn-marine/style.css` (mobile pass bổ sung sau 030).

---

## 4. Chưa quyết định — đọc ideation

Các hạng mục sau **không** có decision trong milestone fix tức thời. Brainstorm, câu hỏi mở, và roadmap nằm tại:

**`docs/workflows/ideation-end-user-deep-customization.md`**

| Topic | Tóm tắt một dòng | Trạng thái |
|-------|------------------|------------|
| Hero Option A (banner full-width) | `grid-column: 1 / -1` | Future ~1.6.0 Customizer |
| Hero Option C (no hero shell) | Ẩn hero PHP shell | Future |
| Layout class contract | `.skvn-post-layout--banner\|--magazine\|--no-hero` | Chưa implement |
| Per-post hero layout picker | Banner vs magazine per bài | Câu hỏi mở #1 |
| Sidebar on/off, island variants | Quote CTA, related, categories | Câu hỏi mở #3 |
| Surface presets (flat/glass/elevated) | `skvn-surface--*` | Planning 1.6.0 / 009 |
| Typography delivery | Governed fonts | Planning 1.6.0 |
| Hero `object-position` (A1) | top/center/bottom crop | Future 1.7.0 / V2 |
| Archive + product layout presets cùng lúc | Cross-template presets | Câu hỏi mở #4 |
| Elementor-style freeform | — | **Rejected** |

**Mô hình 3 tầng** (site defaults / template surfaces / in-content) — xem ideation § “Mô hình 3 tầng”; không duplicate ở đây.

---

## 5. Boundary & invariants (giữ nguyên)

```text
Theme owns:   single.php, style.css, .skvn-island, .skvn-post-hero, page gutter token
Plugin owns:  blocks — không liên quan fix này
Forbidden:    themes/generatepress/**, raw class input cho marketing
```

- `.skvn-island` vẫn là PHP-rendered sidebar (archive + single); không Gutenberg pattern V1
- Mobile flatten **không** xóa islands — chỉ đổi **surface treatment**
- Desktop: islands vẫn card style (border, radius, padding `1rem`)

---

## 6. Verification checklist

### Desktop (030)

- [x] Verify 2: `wrapW` full canvas, `heroAboveMain: true`
- [ ] `sameColumn`, `sidebarRightOfHero` — human DevTools tùy chọn
- [ ] Featured image post — hero image trong grid card
- [ ] Regression `/request-a-quote/`

### Mobile (surfaces)

- [ ] Hard refresh single post ≤900px và ≤600px
- [ ] Script gutter:

```javascript
const wrap = document.querySelector('.skvn-single-wrap');
const body = document.querySelector('.skvn-post-body');
const cs = (el) => el && getComputedStyle(el);
({
  viewport: innerWidth,
  wrapPad: cs(wrap)?.paddingInline,
  bodyPad: cs(body)?.paddingInline,
  bodyTextW: body?.querySelector('p')?.getBoundingClientRect().width,
});
```

**Pass:** `bodyPad` = `0px`; `wrapPad` = `12px` (≤900) hoặc `8px` (≤600); `bodyTextW` ≈ 336–344 trên 360px viewport.

- [ ] Archive sidebar mobile — islands flat, navy band không tràn ngang
- [ ] Không horizontal scroll

---

## 7. Changelog

| Date | Decision |
|------|----------|
| 2026-06-19 | D1–D4: Plan 030 magazine hero grid (Haiku) — `single.php` + `style.css` |
| 2026-06-19 | Verify 2 PASS onsite (`wrapW: 1200`, `heroAboveMain: true`) |
| 2026-06-19 | D5–D8: Mobile hướng 3 A+B+C — `--skvn-page-gutter`, flat islands + article |