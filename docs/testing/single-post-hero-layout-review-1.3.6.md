# Single Post Hero Layout — UI Debug Review & Fix Handoff

**Target milestone:** V1 / 1.3.6 (Trục C — Single Post Fix)  
**Planning:** `.context/planning/026_VER_1_3_6_BLOCK_EDITOR_UX_AND_SLIDER_PARALLAX_PLANNING.md`  
**Status:** REVIEWED — root cause confirmed onsite; **implement plan: Claude Haiku**  
**Implement planning:** `.context/planning/030_VER_1_3_7_SINGLE-POST-HERO-LAYOUT.md` (authoritative for code)  
**Future brainstorm:** `docs/workflows/ideation-end-user-deep-customization.md` (chưa quyết định)  
**Collaboration method:** `docs/workflows/agent-ui-layout-collaboration-method.md` (handoff Haiku)  
**Decisions:** `docs/decisions/single-post-layout-and-mobile-surfaces.md`  
**Reviewer:** Grok agent (UI debug / State Delta)  
**Date:** 2026-06-19

---

## Onsite URLs verified

| URL | HTTP | Post ID | Notes |
|-----|------|---------|-------|
| https://minhhaifishery.com/iqf-technology-for-seafood-industry/ | 200 | 1 | Primary repro URL (slug đổi từ `hello-world`) |
| https://minhhaifishery.com/hello-world/ | 200 | 1 | Redirect/slug cũ — cùng markup |

**Featured image:** `featured_media: 0` — hero đang dùng `skvn-post-hero__placeholder`. Layout bug vẫn reproduce **không cần** featured image.

---

## Symptom (human report)

- Single post **render đúng markup SKVN** nhưng **sai UI**.
- Vùng hero/thumbnail **nằm bên trái** thay vì **full-width phía trên** article content.
- Trông như cột trái (~30–50% width) + content card bên phải.

**Không phải:**

- Template `single.php` thiếu.
- WooCommerce Coming Soon (chỉ ảnh hưởng product pages).
- Ảnh `wp-block-columns` trong bài (layout editor cố ý — text trái, diagram phải).

---

## State Delta summary

```
State A (ĐÚNG):  Hero full-width TRÊN → skvn-single-wrap (2-col grid) DƯỚI
State B (SAI):   skvn-post-hero cột TRÁI → skvn-single-wrap cột PHẢI
Delta:           GP .site-content flex row + 2 SKVN siblings không bọc wrapper
```

| Trục | Finding |
|------|---------|
| Environment | `body.single-post.right-sidebar` — GP sidebar mode active |
| Time | Static CSS — không phụ thuộc JS/scroll |
| Layer | **Primary:** GP `.site-content` flex. **Secondary:** `.skvn-post-hero { display:flex }` khi có `<img>` không absolute |
| Scope | Chỉ `single.php` custom template |
| Data | `featured_media: 0` → placeholder; khi set ảnh sau, cùng flex bug |

---

## Root cause (PROVEN — HTML onsite)

`single.php` output hero và body **trực tiếp** trong GP `.site-content`, bỏ qua `content-area` / `site-main`:

```html
<div class="site-content" id="content">
  <div class="skvn-post-hero">...</div>      <!-- flex child #1 -->
  <div class="skvn-single-wrap">...</div>    <!-- flex child #2 -->
</div>
```

GP inline CSS (onsite):

```css
.site-content .content-area { width: 70%; }
.is-right-sidebar { width: 30%; }
```

Khi `right-sidebar` active, `.site-content` là **flex row** → hai khối SKVN xếp **ngang**, không **dọc**.

**So sánh artifact Style C:** `docs/artifacts/single-post-style-C.html` — hero full-width **trước** `.page-wrap`; 2-col chỉ áp dụng cho body, không cho hero.

**Pattern đã có trong theme** (full-width canvas — reuse ý tưởng):

```css
.skvn-full-width-canvas .site-content {
  display: block;
}
```

File: `wp-content/themes/skvn-marine/style.css` (~line 960).

---

## Implement plan (Claude Haiku — authoritative)

**File:** `.context/planning/030_VER_1_3_7_SINGLE-POST-HERO-LAYOUT.md`

Haiku chọn **HTML restructure + CSS grid placement** thay vì override GP `.site-content` flex.

### Target layout (human-confirmed trong plan 030)

```
┌─────────────────────┬───────────┐
│   .skvn-post-hero   │  sidebar  │  row 1 — hero cột trái (2fr), sidebar span 2 rows
├─────────────────────┤           │
│  .skvn-single-main  │           │  row 2 — content dưới hero, cùng cột trái
└─────────────────────┴───────────┘
```

### Changes

1. **`single.php`** — move `.skvn-post-hero` vào trong `.skvn-single-layout` (trước `main`).
2. **`style.css`** — grid `2fr 1fr` + `grid-template-rows: auto 1fr`; placement rules cho hero/main/sidebar; responsive stack ≤900px.

### Deferred (plan 030)

- Full-width hero banner (Option A) → future Customizer ~1.6.0
- `aspect-ratio: 16/9` hero — không nằm trong plan 030; vẫn thuộc checklist 1.3.6 Trục C nếu Haiku gộp sau

### Grok review note (CSS-only alternative — không dùng)

Plan Grok trước đó (`.site-content { display: block }` + hero full-width trên cùng) khớp artifact `single-post-style-C.html` nhưng **khác** target layout đã confirm trong 030. Implementer follow **030**, không merge hai hướng.

---

## Files allowed

| File | Action |
|------|--------|
| `wp-content/themes/skvn-marine/style.css` | CSS fix primary + aspect-ratio |
| `wp-content/themes/skvn-marine/single.php` | Chỉ nếu cần wrapper `.skvn-single-page` |

**Forbidden:** `themes/generatepress/**`, plugin blocks (không liên quan).

---

## Acceptance checklist (post-fix)

### Automated / source

- [ ] PHP lint: `php -l wp-content/themes/skvn-marine/single.php` (nếu đổi PHP)
- [ ] Audit path: không thêm `100vw`, `50vw`, `overflow-x: hidden` trên hero path
- [ ] `.skvn-single-layout` grid `2fr 1fr` không đổi behavior

### Onsite visual — https://minhhaifishery.com/iqf-technology-for-seafood-industry/

Per plan 030 (không còn bug GP flex sibling):

- [x] Hero nằm **top-left** cột content (2fr), **không** bị kẹp như GP flex child ~30% — *Verify 2: `wrapW: 1200`*
- [ ] Sidebar **bên phải**, span chiều cao hero + main — *chưa có `sidebarRightOfHero` từ script*
- [x] Main article **dưới hero**, cùng cột trái — *Verify 2: `heroAboveMain: true`*
- [ ] Không horizontal scroll (desktop)
- [ ] Mobile ≤900px: hero → main → sidebar stack dọc

### With featured image (human setup sau fix)

- [ ] Set Featured Image cho post ID 1
- [ ] Hero ảnh crop `16:9` + `object-fit: cover`
- [ ] Title/meta vẫn overlay đáy hero, không bị ảnh đẩy sang trái

### DevTools proof

#### Verify 1 — GP `.site-content` không còn xếp hero/wrap ngang (optional)

```javascript
const sc = document.querySelector('.site-content');
const children = [...sc.children].map((el) => el.className);
console.log('site-content display:', getComputedStyle(sc).display);
console.log('direct children:', children);
// expect: 1 child — "skvn-single-wrap" (hero đã nằm trong layout grid)
```

#### Verify 2 — Wrap full canvas + hero **trên** main (bắt buộc) — **CHO HAIKU ĐỌC**

**Mục đích:** Đóng concern review Grok — sau fix 030, `.skvn-single-wrap` không bị kẹp ~70% GP sidebar flex; hero và main **stack dọc** trong cột trái, không còn hero “cột trái hẹp cạnh article”.

**URL test:** https://minhhaifishery.com/iqf-technology-for-seafood-industry/  
**Điều kiện:** Theme đã deploy bản có plan 030 (`single.php` + `style.css`).

**Script (paste DevTools Console):**

```javascript
const wrap = document.querySelector('.skvn-single-wrap');
const hero = document.querySelector('.skvn-post-hero');
const main = document.querySelector('.skvn-single-main');
const sidebar = document.querySelector('.skvn-single-sidebar');

const wrapR = wrap?.getBoundingClientRect();
const heroR = hero?.getBoundingClientRect();
const mainR = main?.getBoundingClientRect();
const sideR = sidebar?.getBoundingClientRect();

const result = {
  wrapW: wrapR?.width,
  heroTop: heroR?.top,
  mainTop: mainR?.top,
  heroAboveMain: heroR && mainR ? heroR.bottom <= mainR.top + 2 : null,
  heroLeft: heroR?.left,
  mainLeft: mainR?.left,
  sameColumn: heroR && mainR ? Math.abs(heroR.left - mainR.left) < 2 : null,
  sidebarRightOfHero: heroR && sideR ? sideR.left >= heroR.right - 2 : null,
};

console.log(result);
result;
```

**Pass criteria:**

| Field | Pass khi |
|-------|----------|
| `wrapW` | ≈ content canvas (~1100–1200px desktop; khớp `--skvn-wide-width` / GP container) — **không** ~30–40% viewport |
| `heroAboveMain` | `true` — main bắt đầu **dưới** đáy hero (gap grid OK) |
| `sameColumn` | `true` — hero và main cùng cột trái |
| `sidebarRightOfHero` | `true` desktop — sidebar nằm phải hero (magazine layout) |

**Evidence human (2026-06-19, sau deploy 030):**

Human chạy script trên Firefox DevTools Console tại URL trên. Console trả về **đúng object** sau (copy nguyên từ DevTools):

```
Object { wrapW: 1200, heroTop: 220.1999969482422, mainTop: 580.2000122070312, heroAboveMain: true }
  heroAboveMain: true
  heroTop: 220.1999969482422
  mainTop: 580.2000122070312
  wrapW: 1200
```

> Lưu ý: Human chỉ expand 4 field trên trong Console. Script còn tính `sameColumn`, `sidebarRightOfHero`, `heroLeft`, `mainLeft` — chưa được human paste lại; xem mục “Chưa verify” bên dưới.

**Bảng đọc kết quả (Haiku):**

| Field | Giá trị onsite | Pass? | Ý nghĩa |
|-------|----------------|-------|---------|
| `wrapW` | `1200` | **PASS** | `.skvn-single-wrap` full canvas (~`--skvn-wide-width`). Trước fix: ~30–40% viewport vì GP flex sibling. |
| `heroAboveMain` | `true` | **PASS** | `hero.bottom <= main.top + 2` — main bắt đầu **dưới** hero. Bug “hero/thumbnail cột trái cạnh article” **đóng** về trục dọc. |
| `heroTop` | `220.2` | OK (context) | Offset từ viewport top: GP header + `padding-block` `.skvn-single-wrap`. Không phải regression layout. |
| `mainTop` | `580.2` | OK (context) | Main nằm dưới hero; khoảng `mainTop - heroTop ≈ 360px` khớp `min-height: 20rem` hero + grid `gap` + padding. |

**Verdict Verify 2:** **PASS** — hai tiêu chí bắt buộc (`wrapW` full canvas, `heroAboveMain: true`) đều đạt sau implement plan 030.

**Haiku — không cần sửa thêm cho Verify 2** trừ khi human báo `heroAboveMain: false`, `wrapW` hẹp bất thường (~300–400px desktop), hoặc visual onsite vẫn sai dù script pass.

**Chưa verify bằng script này (human làm tiếp):** `sameColumn`, `sidebarRightOfHero`, mobile ≤900px stack, featured image (`featured_media` vẫn 0).

---

## Out of scope (this review)

- Product page `/product/grouper/` — blocked by WooCommerce Coming Soon; separate task
- Font preset Customizer value onsite (`system-ui` fallback) — typography milestone 029
- In-content `wp-block-columns` layout — editor content, không sửa
- Mixed content `http://vipafood.com/...` images trong bài — content debt, không block hero fix

---

## Handoff note for Claude Haiku

1. **Source of truth:** `.context/planning/030_VER_1_3_7_SINGLE-POST-HERO-LAYOUT.md`
2. **Verify 2 PASS** — đọc section **DevTools proof → Verify 2** ở trên; human đã trả `wrapW: 1200`, `heroAboveMain: true`. Không reopen bug GP flex sibling trừ khi evidence mới fail.
3. Đọc `docs/standards/css-layout-safety-contract.md` trước khi sửa CSS layout thêm.
4. Files đã đổi: `single.php` + `style.css` only (2 files).
5. Debt còn lại (không block 030): `aspect-ratio 16/9`, layout class contract — xem `docs/workflows/ideation-end-user-deep-customization.md`.
6. Filename plan ghi `1.3.7` nhưng scope ghi `1.3.6 Trục C` — align với human trước khi bump milestone metadata.