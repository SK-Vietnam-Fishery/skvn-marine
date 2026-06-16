# Debug Casebook Index

Registry chính của các án lệ kỹ thuật trong SKVN Marine.

## Cases

| ID | Category | Failure mode | Status | General principle |
|---|---|---|---|---|
| `CASE-001` | Slider | [Swiper JS Without Core CSS](slider/001_SWIPER_JS_WITHOUT_CORE_CSS.md) | `PROVEN · FIXED · REGRESSION_GUARDED` | Một runtime UI cần được verify như một asset graph, không chỉ như một file JavaScript |

## Guides

| Guide | Mô tả |
|---|---|
| [gutenberg-plugin-memory-leak-guide.md](gutenberg-plugin-memory-leak-guide.md) | Hướng dẫn debug và reproduce memory leak trong Gutenberg plugin blocks |

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

