# Module 01 — Block tĩnh đầu tiên: edit, save, attributes

## Mục tiêu

- Viết block tĩnh hoàn chỉnh: `block.json` + `edit.tsx` + `save.tsx`.
- Hiểu attributes là gì, lưu ở đâu, và vì sao đổi `save()` gây "invalid block".

## Khái niệm

**Attributes = state của block, lưu ngay trong HTML bài viết** (dạng comment
`<!-- wp:simple-showcase/quote-card {"author":"..."} -->` hoặc parse ngược từ markup).
Không có database riêng cho block — bài viết chính là database.

**`edit()`** = React component chạy trong editor. **`save()`** = hàm sinh HTML tĩnh
lưu vào bài viết. Quy tắc sống còn: **save() phải deterministic** — cùng attributes
phải sinh ra cùng markup từng ký tự. Khi bạn sửa `save()` mà markup cũ trong bài viết
không khớp code mới → Gutenberg báo "This block contains unexpected or invalid content".

Đây là lý do tồn tại của **deprecations**: mảng các phiên bản `save()` cũ để Gutenberg
nhận diện và migrate markup cũ. Và cũng là lý do dự án SKVN cuối cùng bỏ static save
cho slider (học ở module 02).

**RichText** = component nhập text có format, dùng cả trong edit lẫn save
(`<RichText.Content>` phía save).

## Đọc code thật

1. `wp-content/plugins/skvn-marine-blocks/src/card/` — block card: block.json
   (attributes string/boolean), edit.tsx (useBlockProps, InspectorControls cơ bản),
   save.tsx. Đây là mẫu block tĩnh chuẩn của dự án.
2. Để ý cách đặt tên class: mọi class có prefix `skvn-` — capstone của bạn dùng prefix riêng.
3. Tìm trong repo một block có mảng `deprecated` (grep `deprecated`) — đọc để thấy
   hình dạng một migration.

## Bài tập (capstone bước 1 — Quote Card)

Block `simple-showcase/quote-card`:

1. Attributes: `quote` (string, source html từ RichText), `author` (string),
   `showAvatar` (boolean, default false).
2. `edit.tsx`: RichText cho quote, TextControl cho author (đặt trong InspectorControls),
   ToggleControl cho showAvatar.
3. `save.tsx`: blockquote + footer, class `sshow-quote-card`, thêm class
   `sshow-quote-card--avatar` khi showAvatar.
4. `style.css` load qua `style` trong block.json.
5. **Bài kiểm tra invalid block (quan trọng nhất module):** publish 1 bài có block →
   sửa markup trong `save.tsx` (thêm 1 div) → build → mở lại bài viết → quan sát lỗi
   invalid → viết deprecation để fix → xác nhận bài cũ mở lại bình thường.

## Checkpoint

- [ ] Giải thích attributes được serialize vào đâu (xem Code editor của bài viết)
- [ ] Tự gây ra và tự sửa được lỗi invalid block bằng deprecation
- [ ] Trace được 1 attribute đủ chuỗi: block.json → edit → save → CSS (quy tắc #7 của giáo trình)

## Hố đã đánh dấu

- Sửa save() "chỉ một tí" rồi ship — nguồn invalid block hàng loạt. Dự án SKVN có
  nguyên checklist "existing content opens without invalid-block recovery" trong mọi
  milestone slider vì bài học này.
