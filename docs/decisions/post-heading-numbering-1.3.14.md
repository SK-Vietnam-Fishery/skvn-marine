# Post Heading Auto-Numbering — 1.3.14 (design)

```
Date:     2026-06-28
Status:   IMPLEMENTED (2026-06-28 — chờ Dev build + onsite test)
Home:     Core Control feature (modules/core-control)
Planning: .context/planning/036_VER_1_3_14_POST_HEADING_NUMBERING_PLANNING.md
```

## Goal

Tự đánh số heading h2–h5 trong **thân bài blog** (không đụng heading sidebar/CTA/related/sản phẩm), có **lựa chọn độ sâu** (tới h3/h4/h5) và **kiểu số** (decimal lồng `1 / 1.1 / 1.1.1` hoặc mixed `I / 1 / a`). Thuần CSS counter, không sửa nội dung, không JS.

## Quyết định đặt trong Core Control

Core Control hiện là registry **boolean-only** (admin-page chỉ render checkbox, option `skvn_core_controls` = id→bool, bật thì load `features/<id>.php`). Heading-numbering có sub-option (depth/style) nên mở rộng **tối thiểu, backward-compatible**:

- **Feature boolean** `post_heading_numbers` thêm vào registry (bật/tắt như các feature khác).
- **Sub-option** `skvn_heading_number` (`depth`, `style`) đăng ký **cùng group** `skvn_core_controls_group` → cùng form, `options.php` lưu cả hai. Không phá pattern boolean của các feature cũ.
- **Admin-page** render thêm 2 select (depth/style) sau bảng checkbox.
- **Adapter** `features/post-heading-numbers.php` load khi bật → `wp_head` in `<style>` scoped ở single post.

Lợi ích: **không sửa theme** — scope bằng class WP chuẩn `.single-post .entry-content` (ổn định, không phụ thuộc class skvn). Feature self-contained trong plugin, đúng tinh thần Core Control (giống button-hover tự enqueue CSS).

## Config schema (`skvn_heading_number`)
- `depth`: `h3` | `h4` | `h5` — cấp sâu nhất được đánh số (mặc định `h3`).
- `style`: `decimal` | `mixed` (mặc định `decimal`).

## Thiết kế CSS

### Scope + reset (chung cho cả 2 style)
```css
.single-post .entry-content { counter-reset: h2 h3 h4 h5; }
.single-post .entry-content h2 { counter-increment: h2; counter-reset: h3 h4 h5; }
.single-post .entry-content h3 { counter-increment: h3; counter-reset: h4 h5; }
.single-post .entry-content h4 { counter-increment: h4; counter-reset: h5; }
.single-post .entry-content h5 { counter-increment: h5; }
```
Quy tắc: mỗi heading reset **mọi cấp thấp hơn** → sub-số restart khi sang mục cha mới. Container reset = ranh giới mỗi bài.

### Style `decimal` (nối số cha)
```css
h2::before { content: counter(h2) ". "; }
h3::before { content: counter(h2) "." counter(h3) " "; }
h4::before { content: counter(h2) "." counter(h3) "." counter(h4) " "; }
h5::before { content: counter(h2) "." counter(h3) "." counter(h4) "." counter(h5) " "; }
```

### Style `mixed` (mỗi cấp một kiểu, không nối) — `I / 1 / a / i`
```css
h2::before { content: counter(h2, upper-roman) ". "; }
h3::before { content: counter(h3, decimal)     ". "; }
h4::before { content: counter(h4, lower-alpha)  ". "; }
h5::before { content: counter(h5, lower-roman)  ". "; }
```

### Depth gating
Chỉ in `::before` tới cấp được chọn: `depth=h3` → chỉ h2,h3; `h4` → thêm h4; `h5` → đủ. (PHP build phần `::before` theo depth.)

### Bỏ số 1 heading lẻ (`.no-number`) — toggle trong block
Tác giả bật/tắt ngay trong Inspector của `core/heading` (xem mục "Toggle block" dưới). CSS bỏ qua:
```css
.single-post .entry-content :is(h2,h3,h4,h5).no-number { counter-increment: none; }
.single-post .entry-content :is(h2,h3,h4,h5).no-number::before { content: none; }
```
`counter-increment: none` → heading không ăn số, dãy số kế tiếp không nhảy.

## Toggle block (editor extension)
ToggleControl "Bỏ đánh số mục này" thêm vào Inspector của `core/heading` (pattern button-hover):
- ON → thêm `no-number` vào `attributes.className` (= Advanced → Additional CSS classes).
- OFF → gỡ `no-number`.
- Gate bằng `isCoreControlEnabled('post_heading_numbers')` (đọc `window.skvnCoreControls`).
- File TS: `src/core-controls/heading-number/index.tsx`; cần `npm run build`.

## Cạm bẫy & a11y
- **Nhảy cấp** (h2→h4 thiếu h3): style `decimal` ra `1.0.1` (xấu) → khuyến nghị không nhảy cấp; style `mixed` miễn nhiễm.
- Số là `::before` generated content → **không nằm trong DOM text**: copy không dính số, vài screen reader không đọc, in ấn tùy trình duyệt. Chấp nhận cho mục đích hiển thị.
- TOC (island dán tay) **không** tự có số → sẽ lệch với thân bài. Đồng bộ TOC = ngoài scope bản này.

## Files (kế hoạch)
- `modules/core-control/registry.php` — thêm entry `post_heading_numbers`. [edit]
- `modules/core-control/core-control.php` — register `skvn_heading_number` cùng group + load adapter khi bật. [edit]
- `modules/core-control/admin-page.php` — render 2 select depth/style. [edit]
- `modules/core-control/features/post-heading-numbers.php` — sanitize + getter + `wp_head` CSS (gồm cả editor preview + `.no-number`). [new]
- `src/core-controls/heading-number/index.tsx` — ToggleControl `.no-number` cho core/heading. [new] → `npm run build`.

## Quyết định chốt
- **Mixed map tới h5** = `I → 1 → a → i`. ✅
- **`.no-number`** = toggle trong block (không defer nữa). ✅
- **Editor preview** = BẬT (số hiện cả lúc soạn) — CSS scoped thêm `.editor-styles-wrapper`. ✅
- Defaults: depth `h3`, style `decimal`.

## Open questions
- (none — chốt đủ để build)

## Tension
Adapter plugin in CSS nhắm class WP `.single-post .entry-content` (không phải class skvn riêng) → coupling thấp, không sửa theme. Chấp nhận.

## ⚠️ System design debt — nhảy cấp heading với style `decimal`
**Giới hạn bản chất của CSS counter:** style `decimal` (nối số cha `1.1.1`) khi tác giả nhảy cấp (vd h2 → h4, thiếu h3) sẽ ra `1.0.1` vì CSS không biết cấp giữa có tồn tại hay không → không thể ẩn segment `0` có điều kiện.

**Quyết định tạm (1.3.14):** **chấp nhận**, không xử bằng JS. Dev đã lường trước → trong blog sẽ **cố không nhảy cóc** để không phá giao diện. Style `mixed` miễn nhiễm (không nối) — dùng khi cần.

**Hướng giải quyết đàng hoàng (khi tách thành plugin độc lập):** đánh số bằng **JS đọc cây heading thật**, chỉ nối các cấp thực sự tồn tại (bỏ qua cấp trống) → loại hẳn artifact `1.0.1`, không cần ràng buộc tác giả. Đây là **debt có chủ ý**, không phải bug; giải khi feature lên plugin standalone (cùng đợt bỏ các module tạm khác).
