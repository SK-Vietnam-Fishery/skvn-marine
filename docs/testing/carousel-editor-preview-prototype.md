# Carousel editor prototype — protocol và evidence

Ngày: 2026-10-19. Scope: artifact Editor UX; current 1.3.17, target production 1.3.18 chưa bắt đầu.
Block UI: N/A, không thay frontend. Technical: fixture/state/geometry của artifact, không kiểm query WordPress.

## Target và setup

- Mở [artifact](../artifacts/editor-ui/carousel-editor-preview-prototype.html) trực tiếp trong browser; không cần server/login/API.
- Fixture 0/1/5/20 sản phẩm, tên thường/dài; canvas 1600/1200/800/480/320px.
- Sidebar cạnh nội dung 280px hỗ trợ từ canvas 480px; 320px đóng và khóa rõ ràng.
- Panel ngoài khung là công cụ lab, không phải thiết kế Inspector production.

## Kiểm tự động đã chạy

`node docs/artifacts/editor-ui/verify-carousel-prototype.mjs`

Runner dùng Chrome headless **155.0.8059.84**, viewport 2200×1600, virtual time 60000ms; mỗi process có timeout 20s và dừng khi lỗi, không retry.
Có thể đặt `CAROUSEL_TEST_BROWSER` trỏ tới Chrome/Chromium phù hợp. Browser profile lab riêng nằm trong evidence và bị gitignore.

Kết quả hiện hành: **172 PASS / 0 FAIL**, [log](../artifacts/editor-ui/evidence/selftest.txt).
Agent đã xem cả ba ảnh Wide/Full/Narrow sau chạy. `.canvas-scroll` dùng `overflow-x:auto` có chủ đích để chứa canvas lab cố định lớn hơn màn hình; không dùng overflow hidden/clip để che tràn component. Không có viewport-width rule mới.
Không gộp vào PASS của WordPress onsite. Headless kiểm DOM/focus lập trình không thay kiểm bàn phím thật hoặc screen reader.

| ID | Nhóm | Bước | Expected / tiêu chí PASS | Evidence / trạng thái |
|---|---|---|---|---|
| UX-09 | Editor UX | 20 tên, mở rộng/thu gọn; toggle ảnh | Card không stretch; ảnh đổi; equal-height chỉ là thông tin mẫu | Automated PASS; ảnh Wide |
| UX-10 | Editor UX | 800 không sidebar, 800 có sidebar, 320; chọn tên dài | 3/2/1 cột, hiện 9/6/3; đúng thứ tự theo hàng, tên đầy đủ | Automated PASS; `grid-*` |
| UX-11 | Editor UX | Canvas 1600; lần lượt Normal/Wide/Full; đóng/mở sidebar | 760/1200/1566px khi đóng, 760/1200/1286px khi mở | Automated PASS; log và ảnh Wide/Full |
| UX-11b | Editor UX | Canvas 320, URL `?canvas=320&sidebar=1&count=20&names=long` | Sidebar đóng, control khóa có lời giải thích; không tràn tên | Automated PASS; ảnh narrow |
| TECH-12 | Technical | Chọn loading/empty/error; bấm thử lại; chọn count 0 | State phân biệt, retry về results, count 0 về empty | Automated PASS; `state-*`/`retry-*` |

## Kiểm human còn chờ

1. Mở artifact, Tab qua các control và tới “Xem thêm”; dùng Enter/Space mở rồi thu gọn. PASS khi focus nhìn rõ, thứ tự hợp lý, không mất focus và trạng thái mở được đọc đúng. Gửi browser/version và kết quả; screenshot focus nếu FAIL.
2. Dùng screen reader kiểm tên checkbox ảnh/sidebar và trạng thái danh sách/error. PASS khi tên có nghĩa và state được thông báo; báo tên screen reader/browser và lời đọc bất thường.
3. So ảnh Wide/Full và canvas hẹp: đánh giá card 320px, cỡ chữ, khoảng cách, độ dài block. Đây là human visual approval, chưa được selftest xác nhận.
4. Khi bắt đầu 1.3.18: test lại editor Gutenberg có sidebar, Group/Columns, save/reopen và dữ liệu thật; không suy ra từ prototype. URL onsite sẽ ghi lúc được giao triển khai.

Ảnh: [Wide](../artifacts/editor-ui/evidence/wide-1600.png), [Full](../artifacts/editor-ui/evidence/full-1600.png), [Narrow](../artifacts/editor-ui/evidence/narrow-320.png).
