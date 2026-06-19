# Debug Casebook Index

Registry chính của các án lệ kỹ thuật trong SKVN Marine.

## Cases

| ID | Category | Failure mode | Status | General principle |
|---|---|---|---|---|
| `CASE-001` | Slider | [Swiper JS Without Core CSS](slider/001_SWIPER_JS_WITHOUT_CORE_CSS.md) | `PROVEN · FIXED · REGRESSION_GUARDED` | Một runtime UI cần được verify như một asset graph, không chỉ như một file JavaScript |
| `CASE-002` | Slider | [Viewport Below Header Image Not Full Height](slider/002_VIEWPORT_BELOW_HEADER_IMAGE_NOT_FULL_HEIGHT.md) | `PROVEN · FIXED · REGRESSION_GUARDED` | Viewport preset phải own explicit height trên container root, không chỉ min-height trên slide khi Swiper dùng height: 100% |
| `CASE-003` | Slider | [Hero Text Align vs Block Center](slider/003_HERO_TEXT_ALIGN_VS_BLOCK_CENTER.md) | `PROVEN · FIXED · REGRESSION_GUARDED` | text-align / has-text-align-* ≠ block optical center — verify bằng geometry |

## Guides

| Guide | Mô tả |
|---|---|
| [PITFALLS.md](PITFALLS.md) | Tổng hợp pitfalls layout/slider và agent anti-patterns — đọc trước khi sửa UI |
| [gutenberg-plugin-memory-leak-guide.md](gutenberg/gutenberg-plugin-memory-leak-guide.md) | Hướng dẫn debug và reproduce memory leak trong Gutenberg plugin blocks |

## Tìm Case

Tra cứu theo thứ tự:

1. Failure mode tương tự.
2. Layer gây lỗi: HTML, CSS, JavaScript, WordPress registration hoặc deploy.
3. State Delta tương tự.
4. General principle có thể áp dụng lại.

## Quy Tắc Registry

- Mỗi case chính thức phải có đúng một dòng trong bảng `Cases`.
- ID không được tái sử dụng sau khi case bị supersede.
- Chỉ ghi `ONSITE_VERIFIED` sau khi có human evidence.
- Khi một case bị thay thế, giữ nguyên dòng cũ và thêm liên kết tới case mới.

