# Module 02 — Dynamic render PHP

## Mục tiêu

- Chuyển block từ static save sang PHP render, hiểu khi nào nên chọn hướng nào.
- Nắm sanitize/escape thành phản xạ.

## Khái niệm

**Dynamic block**: `save()` trả `null` (hoặc chỉ InnerBlocks content), HTML frontend
do PHP `render_callback` sinh ra **mỗi lần load trang**. Attributes vẫn lưu trong bài
viết, nhưng markup thì không.

**Trade-off phải thuộc:**

| | Static save | Dynamic render |
|---|---|---|
| Đổi markup sau này | Gây invalid block, cần deprecation | Chỉ sửa PHP, nội dung cũ tự nhận markup mới |
| Data động (query sản phẩm...) | Không làm được | Làm được |
| Server cost | Zero | Render mỗi request (cần cẩn thận query) |
| SEO/no-JS | Như nhau — cả hai đều là HTML server-side | Như nhau |

**Bài học 1.3.0 của dự án (lý do module này tồn tại):** Slider ban đầu static save.
Mỗi lần đổi markup (thêm wrapper, đổi controls) là một đợt deprecation + rủi ro vỡ nội
dung cũ. Milestone 1.3.0 chuyển slider sang **dynamic rendering architecture** — từ đó
1.3.1 → 1.3.8 đổi markup thoải mái không vỡ bài cũ. Đọc
`docs/decisions/slider-completion-spec-1.3.0.md` để thấy quyết định này được viết thế nào.

**Security — không thương lượng (từ AGENTS.md §2):**

```php
// Input — sanitize tại chỗ đọc, kể cả attribute/option
$count = isset( $attributes['count'] ) ? absint( $attributes['count'] ) : 3;
$label = isset( $attributes['label'] ) ? sanitize_text_field( $attributes['label'] ) : '';

// Output — escape tại chỗ in, theo ngữ cảnh
echo esc_html( $title );          // text node
echo esc_attr( $value );          // trong attribute
echo esc_url( $url );             // href/src
echo wp_kses_post( $content );    // HTML có kiểm soát (RichText content)
```

Attribute của block **là user input** — người có quyền edit bài có thể sửa JSON comment
bằng Code editor. Không tin attribute chỉ vì "editor của mình nhập".

## Đọc code thật

1. `wp-content/plugins/skvn-marine-blocks/modules/collection-render/` — render PHP của
   collection blocks: nhận attributes, query WooCommerce/WP native, escape từng giá trị in ra.
2. `modules/collection-render/cards.php` → hàm `skvn_marine_blocks_get_current_source_url()`
   — case study T-2: vì sao không được dùng `$_SERVER['HTTP_HOST']` để build URL
   (host-header poisoning). Đọc `docs/standards/security-guidelines.md` phần này.
3. `src/slider/index.ts` + render PHP của slider — thấy pattern save trả InnerBlocks
   content còn shell do PHP dựng.

## Bài tập (capstone bước 2)

1. Chuyển `quote-card` sang dynamic: `save()` trả null; viết
   `render_callback` PHP sinh blockquote từ attributes; escape đầy đủ.
2. Thêm block mới `simple-showcase/latest-posts`: attribute `count` (1–6),
   render PHP dùng `WP_Query` in danh sách bài mới (title + link). KHÔNG SQL trực tiếp.
3. Tự audit: mở render PHP, đánh dấu từng chỗ `echo` — mỗi chỗ phải có một hàm escape.
   Thiếu một chỗ = chưa đạt.
4. Test: sửa markup trong render PHP (thêm class) → bài viết cũ nhận markup mới,
   không invalid block. So sánh cảm giác với bài kiểm tra module 01.

## Checkpoint

- [ ] Nói được 2 lý do chọn dynamic, 1 lý do chọn static cho một block cụ thể
- [ ] Mọi echo trong render đều qua esc_* đúng ngữ cảnh
- [ ] Giải thích được vì sao `$_SERVER['HTTP_HOST']` không đáng tin

## Hố đã đánh dấu

- Escape lúc save option nhưng quên escape lúc **đọc ra** — audit 2026 của dự án bắt
  đúng lỗi này (sanitize-at-read trong AGENTS.md self-check).

## Tài liệu nguồn (chính thức)

- Creating Dynamic Blocks (tutorial gốc): https://developer.wordpress.org/block-editor/how-to-guides/block-tutorial/creating-dynamic-blocks/
- Securing Input (sanitize): https://developer.wordpress.org/apis/security/sanitizing/
- Securing Output (escape, bảng hàm theo ngữ cảnh): https://developer.wordpress.org/apis/security/escaping/
- `WP_Query`: https://developer.wordpress.org/reference/classes/wp_query/
- `render_callback` trong register_block_type args: https://developer.wordpress.org/reference/classes/wp_block_type/
- WooCommerce dev docs (khi query product): https://developer.woocommerce.com/docs/
