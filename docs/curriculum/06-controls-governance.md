# Module 06 — Controls governance: preset thay vì raw value

## Mục tiêu

- Thiết kế Inspector controls mà marketing user dùng được và không phá vỡ design system.
- Biết cách quyết định: attribute nào nên là enum preset, cái nào là số tự do.

## Khái niệm

**Triết lý dự án (0.8.0 SKVN Editor Controls):** người dùng editor không bao giờ phải
gõ raw class, raw hex color, raw px/ms. Mọi lựa chọn là **preset được đặt tên**
(governed): `Subtle/Medium/Strong`, `5/7/9/12s`, `flat/soft/glass`. Vì:

1. Preset giới hạn không gian bug: 3 giá trị = 3 đường test; số tự do = vô hạn đường test.
2. Preset là hợp đồng: đổi giá trị bên trong preset (subtle: 15%→12%) không cần
   migrate content — bài viết chỉ lưu chữ `"subtle"`.
3. Preset giữ brand: marketing không thể chọn màu lệch palette.

**Cấu trúc panel chuẩn của dự án**: Inspector chia 4 section **Content / Style /
Layout / Advanced** (quy ước 1.3.6). Advanced là chỗ duy nhất được phép lộ raw value,
và phải có lý do (xem tranh luận Advanced parallax — memory `parallax_advanced_toggle`:
dự án từng giấu hoàn toàn, human quyết định lộ qua toggle; trade-off là test matrix nở 8×).

**Kỹ thuật attribute cho preset**: dùng `enum` trong block.json — WordPress validate
hộ khi parse. Map preset → giá trị thật đặt **một chỗ duy nhất** (PHP hoặc JS, đừng cả
hai — nếu buộc cả hai như slider parallax của dự án thì phải có test giữ đồng bộ).

**Legacy value policy**: khi đổi enum (vd `dots` → pagination styles ở 1.3.1), phải
định nghĩa hành vi cho giá trị cũ còn trong bài viết — map sang giá trị mới hay fallback
default. Không định nghĩa = invalid block hoặc hành vi ngẫu nhiên.

## Đọc code thật

1. `src/slider/edit.tsx` — Inspector: SelectControl cho duration governed 5/7/9/12s,
   ToggleControl, panel Advanced parallax (đúng cả hai mặt của triết lý).
2. `docs/decisions/skvn-editor-controls-0.8.0.md` — quyết định gốc về governance.
3. `docs/decisions/slider-navigation-and-pagination-controls.md` — một contract controls
   hoàn chỉnh: visibility × style × position, kèm legacy `dots` migration. Đọc như mẫu
   spec khi bạn tự thiết kế controls.

## Bài tập (capstone bước 6)

1. Refactor Testimonial Slider: `delay` từ số tự do → enum `"5" | "7" | "9"` (SelectControl).
   Viết legacy policy: giá trị số cũ trong bài viết map về đâu?
2. Thêm `theme` preset cho quote-card: `light / dark / brand` — chỉ đổi class,
   CSS map class → màu. Không ColorPicker.
3. Sắp lại Inspector cả 2 block theo 4 section Content/Style/Layout/Advanced.
4. Viết một trang spec ngắn (bắt chước format decision doc dự án) cho controls của
   slider bạn: bảng control × giá trị × hành vi × fallback. Đây là kỹ năng thay thế
   AI agent quan trọng nhất: **viết spec trước khi code**.

## Checkpoint

- [ ] Không còn control nào nhận raw hex/px/class trong capstone
- [ ] Có legacy policy viết ra cho một lần đổi enum
- [ ] Spec một trang: người khác đọc là code được không cần hỏi lại

## Tài liệu nguồn (chính thức)

- `@wordpress/components` reference (SelectControl, ToggleControl, PanelBody...): https://developer.wordpress.org/block-editor/reference-guides/components/
- InspectorControls SlotFill: https://developer.wordpress.org/block-editor/reference-guides/slotfills/inspector-controls/
- `enum` trong attributes (validation): https://developer.wordpress.org/block-editor/reference-guides/block-api/block-attributes/#enum-validation
- Storybook chính thức của WP components (xem control nào có sẵn trước khi tự chế): https://wordpress.github.io/gutenberg/
