# Module 04 — Editor ≠ Frontend: DOM, CSS specificity, block extension

## Mục tiêu

- Hiểu editor render DOM khác frontend thế nào và viết CSS đúng cho cả hai.
- Extend block core (thêm control vào block có sẵn) mà không vỡ.

## Khái niệm

**Editor DOM ≠ Frontend DOM.** Trong editor, block của bạn bị Gutenberg bọc thêm
wrapper, đặt trong iframe, và một số class chỉ tồn tại một phía. CSS viết cho frontend
selector có thể trượt hoàn toàn trong editor và ngược lại. Dự án có nguyên một contract
về chuyện này: `docs/standards/gutenberg-block-extension-css-contract.md` — **đọc toàn
bộ file đó như một phần của module này.**

Tóm tắt các luật (không thay việc đọc contract):

1. **Specificity trước, CSS sau**: mở DevTools xem rule mạnh nhất của theme đang thắng
   element đích, rồi mới viết rule của bạn với specificity ≥ nó (dùng scoped class trên
   wrapper). Viết CSS trước khi đo = ăn may.
2. **Extend block core** có 2 filter khác nhau, đừng nhầm vai:
   - `editor.BlockEdit` — chỉ để inject panel vào Inspector (sidebar).
   - `editor.BlockListBlock` — để gắn class/style vào **wrapper của block trong editor**
     (muốn preview thấy được thì phải dùng cái này, style build trong BlockEdit là dead code).
   - PHP `render_block_{name}` filter — gắn class/vars vào output frontend.
3. Selector editor phải theo cấu trúc `.has-scoped-class .wp-block-button .link:hover`,
   **không phải** `.wp-block-button.has-scoped-class` — vì class inject vào outer wrapper.
4. **Bundle boundary**: feature CSS nhỏ dùng `wp_add_inline_style` với handle riêng,
   không enqueue cả bundle plugin.
5. **Editor không giấu nội dung**: không `opacity: 0` chờ animation trong editor
   (quy tắc A6 — reveal animation phải có safe fallback).

**Vì sao dự án trả giá cho contract này:** feature Core Button Hover (1.3.4/1.3.7) —
thêm hover color cho `core/button` — chết nhiều vòng vì: theme rule mạnh hơn đè mất,
editor preview không ăn vì class gắn nhầm element, wrapperStyle viết trong BlockEdit
không có tác dụng. Toàn bộ được đúc lại thành contract.

## Đọc code thật

1. `docs/standards/gutenberg-block-extension-css-contract.md` — bắt buộc, đọc hết.
2. `src/core-controls/` — code extend `core/button`: addFilter BlockEdit (panel) +
   BlockListBlock (wrapper class/vars) + PHP render_block filter. Đối chiếu từng luật
   contract với từng đoạn code.
3. `docs/standards/css-layout-safety-contract.md` — contract chị em về layout width
   (một owner cho width, cấm 100vw bừa, cấm overflow-x che lỗi).

## Bài tập (capstone bước 4)

1. Extend `core/quote`: thêm toggle "Accent border" trong Inspector
   (`editor.BlockEdit`), khi bật gắn class `sshow-accent` vào frontend (PHP
   `render_block_core/quote`) **và** editor preview (`editor.BlockListBlock`).
2. CSS: border trái màu accent. Trước khi viết, mở DevTools đo specificity rule
   của theme cho blockquote — ghi lại con số, rồi viết rule thắng nó không dùng `!important`.
3. Test hành vi cả 2 phía: editor thấy border ngay khi toggle; frontend đúng;
   tắt plugin → bài viết không invalid (vì chỉ thêm class qua filter, không đổi markup lưu).

## Checkpoint

- [ ] Chỉ ra đúng filter nào cho việc nào: Inspector panel / editor wrapper / frontend output
- [ ] Đo được specificity bằng DevTools trước khi viết CSS
- [ ] Giải thích vì sao `!important` là cấm mặc định trong contract dự án
