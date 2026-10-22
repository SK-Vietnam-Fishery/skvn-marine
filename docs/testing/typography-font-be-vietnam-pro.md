# Typography — Font preset "Be Vietnam Pro" — Test Plan

**Target milestone:** V1 / 1.3.18 (source change tại `wp-content/themes/skvn-marine/inc/customizer.php`)  
**Decision:** `docs/decisions/typography-scope-and-font-loading.md`  
**Status:** READY — chờ human verify onsite

---

## Preconditions

- Theme `skvn-marine` active, plugin `skvn-marine-blocks` active
- Customizer → Appearance → Customize → Typography (SKVN Marine)
- Trang test có heading H1 + đoạn body có ký hiệu tiếng Việt (ví dụ: `Sản phẩm thủy sản xuất khẩu`, `Ánh sáng`). Có thể dùng trang `/test-product-carousel/` hoặc trang front-end bất kỳ.
- Truy cập được DevTools (Network lọc `fonts.googleapis.com`, Elements/Computed).

---

## Test 1 — Dropdown và lưu preset

**URL:** `/wp-admin/customize.php` → section "Typography (SKVN Marine)"

**Steps:**

1. Mở Customizer, vào section Typography (SKVN Marine).
2. Control "Font preset" (radio): xác nhận có lựa chọn **Be Vietnam Pro** cùng các preset cũ (Instrument Serif, Lora + Inter, Barlow, System).
3. Chọn **Be Vietnam Pro**, sau đó **Publish**.
4. Reload trang Customizer (hoặc vào lại sau khi publish).
5. Mở front-end trang test, xem font.

**Expected / PASS:**

- Radio "Be Vietnam Pro" xuất hiện trong danh sách preset.
- Sau Publish + reload, Customizer vẫn đánh dấu Be Vietnam Pro là đang chọn (`skvn_font_preset` lưu được).
- Frontend hiển thị font Be Vietnam Pro theo preset.

**Fail if:** Mất lựa chọn sau reload, hoặc dropdown không có mục mới.

**Evidence:** Screenshot Customizer control + trang front-end sau khi chọn.

---

## Test 2 — Frontend và editor canvas, chữ Việt có dấu

**URL:** trang front-end có H1 + body tiếng Việt; `/wp-admin/post.php?post={ID}&action=edit` (block editor)

**Steps:**

1. Front-end (logged-out/incognito): quan sát heading + body.
2. Thêm/sửa text tiếng Việt có dấu đầy đủ: `Á ÁÀ Ả Ã Ạ Â Ấ Ầ Ẩ Ẫ Ậ Đ Ê ÊẀ Êẁ ÊẂ Êẃ ÊỀ Êề ÊỂ ÊỄ ÊỆ Ô ÔÒ Ó Ỏ Õ Ọ Ơ Ờ Ỡ Ở Ợ Ư Ừ ƯỰ ƯỬ ƯỮ ƯỰ`.
3. Block editor: mở canvas `.editor-styles-wrapper`, so sánh font heading/body vs sidebar inspector (admin chrome).

**Expected / PASS:**

- Front-end: heading và body dùng `'Be Vietnam Pro', system-ui, sans-serif`; chữ Việt có dấu render đúng, không tofu/box.
- Editor canvas (`.editor-styles-wrapper`): font Be Vietnam Pro áp cho heading/body per preset.
- Admin chrome (sidebar Gutenberg, top bar): giữ font hệ thống WordPress, **không** đổi sang Be Vietnam Pro.

**Evidence:** Screenshot front-end heading; screenshot editor crop chrome vs canvas; câu chữ Việt có dấu hiển thị rõ.

---

## Test 3 — Network chỉ tải font theo lựa chọn, không tải trùng

**URL:** front-end trang test (logged-out/incognito)

**Steps:**

1. Mở DevTools → Network, lọc `fonts.googleapis.com`.
2. Reload trang với preset **Be Vietnam Pro**.
3. Chuyển preset sang **Barlow** (hoặc System) trong Customizer, Publish, reload lại front-end.
4. Quan sát request fonts.googleapis.com trước/sau khi đổi preset.

**Expected / PASS:**

- Với Be Vietnam Pro: có ít nhất một request `css2?family=Be+Vietnam+Pro...`; không có request Barlow/Lora/Instrument.
- Với Barlow/System: không có request Be Vietnam Pro.
- Không có request trùng lặp cùng URL trên cùng một lần load.
- System preset: không có request Google Fonts nào.

**Evidence:** Screenshot DevTools Network (filtered `fonts.googleapis.com`) trước/sau khi đổi preset.

---

## Test 4 — Chuyển về font cũ; wp-admin chrome không đổi

**URL:** Customizer Typography; front-end; `/wp-admin/` Dashboard

**Steps:**

1. Trong Customizer, chuyển preset về **Instrument Serif** (hoặc preset đã dùng trước), Publish.
2. Reload front-end: font phải quay về Instrument Serif; không còn request Be Vietnam Pro.
3. Mở `/wp-admin/` Dashboard: quan sát sidebar, admin bar, heading "Dashboard".

**Expected / PASS:**

- Font cũ恢復 bình thường trên front-end; không lỗi invalid block.
- Dashboard admin UI giữ font hệ thống WordPress (không bị Be Vietnam Pro rò rỉ).
- Không có fatal error / invalid-block trong editor.

**Fail if:** Admin chrome đổi font, hoặc front-end không quay về font cũ.

**Evidence:** Screenshot Dashboard + front-end sau khi chuyển lại font cũ.

---

## Pass / Fail summary

| Test | Mục tiêu | Pass | Fail | Notes |
|---|---|---|---|---|
| 1 — Dropdown + lưu | Be Vietnam Pro có trong radio, lưu qua reload | | | |
| 2 — Font đúng FE/Editor, chữ Việt | Frontend + editor canvas đúng font, dấu render đúng | | | |
| 3 — Network theo lựa chọn | Chỉ tải Be Vietnam Pro khi chọn; không trùng; không tải khi đổi font cũ | | | |
| 4 — Quay lại font cũ + admin không đổi | Font cũ hoạt lại; wp-admin chrome không đổi | | | |

**Overall pass:** Tất cả 4 test pass.

---

## Deferred

- Đổi heading scope (`skvn_font_heading_scope`) kết hợp Be Vietnam Pro — cùng hành vi như preset cũ, không cần test riêng trừ khi báo lỗi.
- Thêm weight mới (ví dụ 800/900) — yêu cầu sửa URL Google Fonts trong preset, out of scope lượt này.
