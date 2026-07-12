# Module 03 — Parent/Child với InnerBlocks (pattern Slider/Slide)

## Mục tiêu

- Xây cặp block cha/con: cha sở hữu behavior chung, con sở hữu nội dung từng item.
- Hiểu vì sao InnerBlocks thắng mọi kiểu "slides array" tự chế.

## Khái niệm

**InnerBlocks** = block chứa block. Cha khai báo `allowedBlocks` (chỉ nhận con hợp lệ),
con khai báo `parent` trong block.json (chỉ insert được trong cha). Editor tự cho
List View, duplicate, reorder, drag-drop — **miễn phí, không phải code**.

**Quyết định kiến trúc quan trọng nhất của pattern này** (dự án đã cân nhắc và bác bỏ
phương án ngược lại — đọc `.context/planning/018_...STEP_SLIDER_PLANNING.md` §3, §9):
lưu slides thành attribute mảng (`slides: [{title, image...}]`) + tự viết UI repeater
nghe có vẻ gọn, nhưng mất toàn bộ hệ sinh thái editor: List View, copy/paste từng
slide, nested blocks trong slide, undo granular. **Đừng bao giờ tự chế slide manager.**

**Phân chia attribute cha/con** (nhìn Slider/Slide của dự án):
- Cha: behavior toàn cục — autoplay, delay, loop, arrows, pagination, hiệu ứng.
- Con: nội dung per-item — background image, overlay, alignment.
- Nội dung editorial (heading, paragraph, button) = InnerBlocks của con, **không**
  thành attribute string.

**Template**: cha có thể khai báo template mặc định (insert cha là có sẵn N con mẫu) —
xem slider presets của dự án (1.2.1).

## Đọc code thật

1. `src/slider/block.json` + `src/slide/block.json` — để ý `parent` của slide,
   attribute nào ở tầng nào.
2. `src/slider/edit.tsx` — InnerBlocks với allowedBlocks; editor render **stacked**
   (các slide xếp dọc), không chạy carousel — quy tắc A6/editor của dự án.
3. `src/accordion/` — pattern cha/con thứ hai để đối chiếu.

## Bài tập (capstone bước 3 — Testimonial Slider)

1. `simple-showcase/testimonial-slider` (cha, dynamic render):
   attributes `autoplay` (bool), `delay` (enum 5/7/9 giây — governed, học kỹ ở module 06),
   `showArrows` (bool).
2. `simple-showcase/testimonial` (con): `parent` chỉ về cha; InnerBlocks bên trong
   cho phép quote-card (module 01) + heading + paragraph.
3. Template cha: 2 testimonial mẫu.
4. Editor: stacked preview, mỗi con có viền nhạt (xem `.skvn-slider--editor` CSS của dự án).
5. Render PHP cha: dựng shell `<div class="sshow-slider"><div class="sshow-slider__track">
   {InnerBlocks content}</div></div>` — chưa cần Swiper (module 05 sẽ gắn).
6. Test List View: thêm/xóa/duplicate/reorder con — mọi thao tác phải mượt, không lỗi console.

## Checkpoint

- [ ] Giải thích được vì sao không dùng slides-array + repeater (3 lý do)
- [ ] Con không insert được ngoài cha; cha chỉ nhận con hợp lệ
- [ ] Attribute đặt đúng tầng: thử đặt `autoplay` vào con và mô tả vì sao sai

## Hố đã đánh dấu

- Chạy carousel/autoplay thật trong editor → editing painful + memory leak (case study
  module 09). Editor luôn là preview tĩnh hoặc simplified.
