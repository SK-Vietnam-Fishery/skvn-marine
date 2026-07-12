# Module 00 — Foundations: Plugin anatomy & build pipeline

## Mục tiêu

- Hiểu một plugin blocks gồm những tầng nào và file nào chạy ở đâu (PHP server / JS editor / JS frontend).
- Tự tạo skeleton plugin + build pipeline chạy được.

## Khái niệm

**Một plugin blocks có 3 "thế giới" code, chạy ở 3 chỗ khác nhau:**

| Thế giới | Ngôn ngữ | Chạy khi nào | Trong dự án |
|---|---|---|---|
| Server | PHP | Mỗi request: đăng ký block, render HTML, settings | `skvn-marine-blocks.php`, `modules/**/*.php` |
| Editor | JS/TS (React) | Chỉ trong wp-admin Gutenberg | `src/*/edit.tsx`, `index.ts` |
| Frontend runtime | JS/TS | Trên trang public, sau khi HTML render | `src/*/view.ts` |

Nhầm thế giới là lỗi phổ biến nhất của người mới: code editor không chạy trên frontend,
PHP không biết gì về state của React.

**`block.json` là hợp đồng trung tâm** — khai báo name, attributes, script/style nào
load ở thế giới nào (`editorScript`, `viewScript`, `style`, `editorStyle`). WordPress
đọc file này để tự enqueue đúng chỗ, đúng lúc (chỉ load khi block có mặt trên trang).

**`@wordpress/scripts`** là bộ build chính chủ: webpack + TypeScript + JSX cấu hình sẵn.
`npm run build` đọc `src/`, xuất `build/`. PHP đăng ký block từ **`build/`**, không phải `src/`.

## Đọc code thật (theo thứ tự)

1. `wp-content/plugins/skvn-marine-blocks/skvn-marine-blocks.php` — entry: header comment,
   các `require` module, hook `init` đăng ký blocks. Để ý: mọi function có prefix
   `skvn_marine_blocks_` — tránh đụng tên với plugin khác trong cùng process PHP.
2. `wp-content/plugins/skvn-marine-blocks/package.json` — scripts `build`/`start`,
   dependencies (để ý `swiper` là dependency có rationale — quy tắc A8 của dự án).
3. `wp-content/plugins/skvn-marine-blocks/src/slide/block.json` — block đơn giản nhất
   để đọc trước: name, category, attributes, supports.
4. So sánh `src/slide/` với `build/slide/` sau khi build — hiểu build xuất ra gì.

## Bài tập (capstone bước 0)

1. Tạo folder `wp-content/plugins/simple-showcase/` trong site local với:
   - `simple-showcase.php` — plugin header + hook `init` (tạm để trống body).
   - `package.json` — dùng `@wordpress/scripts` (xem package.json dự án làm mẫu, bỏ swiper).
   - `src/` trống.
2. `npm install` + `npm run build` chạy không lỗi (build trống cũng được).
3. Activate plugin trong wp-admin — không fatal.
4. Trả lời viết ra giấy: `editorScript` vs `viewScript` khác nhau chỗ nào?
   Vì sao đăng ký block từ `build/` mà không phải `src/`?

## Checkpoint

- [ ] Kể tên 3 thế giới code và cho ví dụ 1 file mỗi loại trong dự án SKVN
- [ ] Giải thích được block.json giúp conditional loading thế nào
- [ ] Plugin skeleton activate được, `php -l` pass

## Đọc thêm trong repo

- `AGENTS.md` §2 (Architecture) — boundary theme/plugin.
- `docs/decisions/architecture.md` — vì sao dự án chọn child theme + plugin tách riêng.
