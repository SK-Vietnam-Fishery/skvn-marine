# Typography Scope Isolation — Test Plan

**Target milestone:** V1 / 1.3.6  
**Planning:** `.context/planning/029_VER_1_3_6_TYPOGRAPHY_SCOPE_ISOLATION_PLANNING.md`  
**Decision:** `docs/decisions/typography-scope-and-font-loading.md`  
**Status:** READY — implement 029 done; chờ human verify onsite/local

---

## Preconditions

- Theme `skvn-marine` active
- Plugin `skvn-marine-blocks` active
- Customizer font preset: **Instrument Serif** (hoặc preset có Google Fonts)
- SKVN Marine → Typography: đổi Primary color sang giá trị dễ nhận biết (ví dụ `#ff0000`) để verify palette scope

---

## Test 1 — wp-admin chrome không đổi font

**URL:** `/wp-admin/` (Dashboard)

**Steps:**

1. Mở Dashboard sau khi đã save font preset + typography palette.
2. Quan sát sidebar trái, top admin bar, heading "Dashboard".

**Expected:**

- Font admin UI giữ system stack WordPress (không serif Instrument / không Barlow).
- Primary color đỏ **không** xuất hiện trên admin menu hoặc Dashboard widgets.

**Fail if:** Sidebar/menu đổi sang serif hoặc custom font preset.

**Evidence:** Screenshot Dashboard full page.

---

## Test 2 — SKVN Typography admin page không leak

**URL:** `/wp-admin/admin.php?page=skvn-marine-typography`

**Steps:**

1. Mở SKVN Marine → Typography.
2. Quan sát form labels, headings, buttons.

**Expected:**

- Standard wp-admin typography only.
- Form vẫn functional; save palette thành công.

**Evidence:** Screenshot settings page.

---

## Test 3 — Block editor canvas có SKVN font

**URL:** `/wp-admin/post.php?post={ID}&action=edit` (page hoặc post có heading)

**Steps:**

1. Mở block editor.
2. So sánh font **sidebar inspector** vs font **content canvas** (heading block H1).
3. Thêm/sửa text tiếng Việt: `Sản phẩm thủy sản xuất khẩu`.

**Expected:**

- Admin chrome (sidebar Gutenberg UI, top bar): WP default font.
- Canvas `.editor-styles-wrapper`: SKVN heading/body font per preset.
- Ký tự `ă â đ ê ô ơ ư` render đúng, không tofu/box.

**Evidence:** Screenshot editor — crop chrome vs canvas riêng.

---

## Test 4 — Frontend public

**URL:** Homepage hoặc bất kỳ public page có H1

**Steps:**

1. Mở page ở incognito/logged-out.
2. Verify heading font = preset.
3. Verify body text font.
4. Verify primary color từ Typography settings (nếu đã đổi test red → đổi lại sau test).

**Expected:**

- SKVN font preset applied.
- Palette tokens visible on buttons/links/headings.
- Vietnamese text renders correctly.

**Evidence:** Screenshot frontend hero/heading.

---

## Test 5 — Customizer font preset switch

**URL:** `/wp-admin/customize.php` → Typography (SKVN Marine)

**Steps:**

1. Switch preset: Instrument → Barlow → System.
2. Publish.
3. Re-check Test 1 (admin) and Test 4 (frontend).

**Expected:**

- Admin chrome unchanged across all presets.
- Frontend font changes per preset.
- System preset: no Google Fonts network request on frontend.

**Evidence:** Screenshot frontend per preset; optional DevTools Network filter `fonts.googleapis.com`.

---

## Pass / Fail summary

| Test | Pass | Fail | Notes |
|---|---|---|---|
| 1 — Admin Dashboard | | | |
| 2 — Typography settings | | | |
| 3 — Editor canvas | | | |
| 4 — Frontend | | | |
| 5 — Preset switch | | | |

**Overall pass:** All five tests pass.

---

## Deferred

- Login page (`/wp-login.php`) — out of scope; should remain WP default.
- WooCommerce admin product edit — same rule as Test 1 unless regression reported.