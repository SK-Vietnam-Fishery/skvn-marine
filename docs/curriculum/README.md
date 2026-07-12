# Giáo trình: Tự xây plugin Gutenberg blocks (từ dự án SKVN Marine)

> Mục tiêu: sau khi học xong, bạn tự xây được một plugin blocks tương tự
> `skvn-marine-blocks` — không cần AI agent.
> Phương pháp: **dự án này là sách giáo khoa**. Mỗi module chỉ cho bạn đọc đúng file
> nào trong repo, giải thích tại sao code viết như vậy, và giao bài tập xây dần
> plugin riêng của bạn (capstone).

---

## Đầu vào giả định

- PHP + JavaScript cơ bản (viết được function, hiểu object/array, callback).
- Dùng WordPress thành thạo mức admin (cài plugin, soạn Gutenberg).
- Môi trường dev như `.local/ENVIRONMENT.md`: WSL + PHP 8.x + Node 20 + WP-CLI + site local.

## Capstone: plugin `simple-showcase`

Xuyên suốt giáo trình bạn xây một plugin riêng tên `simple-showcase` (prefix
`simple_showcase_`, namespace `simple-showcase/*`) gồm:

1. Block tĩnh **Quote Card** (module 01)
2. Chuyển thành **dynamic render** (module 02)
3. **Testimonial Slider** parent/child InnerBlocks (module 03)
4. Sidebar controls governed (module 04, 06)
5. Frontend runtime Swiper + cleanup đúng (module 05)
6. **Settings page** dạng module registry (module 07)
7. Đóng gói release zip + versioning (module 08)

Làm hết bài tập = bạn đã đi lại đúng con đường của `skvn-marine-blocks` từ 0.1.0 → 1.3.x,
nhưng tránh được các hố mà dự án này đã rơi (module 09).

## Lộ trình

| # | Module | Nội dung | Thời lượng gợi ý |
|---|---|---|---|
| 00 | [Foundations](00-foundations.md) | Plugin anatomy, block.json, build pipeline @wordpress/scripts | 1–2 ngày |
| 01 | [Block tĩnh đầu tiên](01-first-static-block.md) | edit/save, attributes, RichText, deprecation là gì | 2–3 ngày |
| 02 | [Dynamic render PHP](02-dynamic-render-php.md) | render_callback, vì sao dự án bỏ static (bài học 1.3.0), sanitize/escape | 2–3 ngày |
| 03 | [Parent/Child InnerBlocks](03-innerblocks-parent-child.md) | Slider/Slide pattern, allowedBlocks, List View, template | 3–4 ngày |
| 04 | [Editor ≠ Frontend](04-editor-vs-frontend.md) | Editor preview rules, BlockEdit vs BlockListBlock, CSS specificity contract | 2–3 ngày |
| 05 | [Frontend runtime](05-frontend-runtime.md) | viewScript, Swiper, lifecycle + cleanup, memory leak, reduced-motion | 3–5 ngày |
| 06 | [Controls governance](06-controls-governance.md) | InspectorControls, preset vs raw value, thiết kế attribute | 2 ngày |
| 07 | [Settings & module architecture](07-settings-module-architecture.md) | Settings API, feature registry, PHP module layout | 3–4 ngày |
| 08 | [Chất lượng & release](08-quality-and-release.md) | Security, a11y, versioning, build zip, deploy audit | 2–3 ngày |
| 09 | [Case studies — học từ tai nạn thật](09-case-studies.md) | 6 bug thật của dự án: nguyên nhân, cách phát hiện, bài học | 1–2 ngày, đọc rải |

Tổng: ~4–6 tuần part-time. Module 09 nên đọc song song — mỗi khi học module nào,
đọc case study tương ứng.

## Cách dùng giáo trình

1. Mỗi module: đọc phần **Khái niệm** → mở các file trong **Đọc code thật** →
   làm **Bài tập** → tự kiểm bằng **Checkpoint**.
2. Không copy code dự án vào capstone — gõ lại và đổi tên theo namespace của bạn.
   Hiểu đến đâu gõ đến đó; chỗ nào gõ mà không giải thích được thì quay lại đọc.
3. Lệnh build/lint dùng chung cho mọi module (chạy trong WSL):
   ```bash
   npm run build && find . -name "*.php" -not -path "./node_modules/*" -exec php -l {} \;
   ```
4. Tài liệu chính thức đọc kèm: Block Editor Handbook (developer.wordpress.org/block-editor)
   — giáo trình này không thay thế, nó chỉ cho bạn thứ tự đọc và bản đồ tránh hố.

## Nguyên tắc rút từ dự án (thuộc lòng trước khi code)

1. **Visual → theme, logic/data → plugin.** Thay theme mà feature mất = feature đặt sai chỗ.
2. **Sanitize input, escape output — luôn luôn**, kể cả giá trị đọc từ option/attribute.
3. **Editor không chạy runtime thật** (không autoplay, không Swiper trong editor).
4. **Mọi animation phải check `prefers-reduced-motion`.**
5. **Mọi listener/observer phải có đường tháo** (cleanup) — xem case study memory leak.
6. **Verify behavior thư viện theo đúng version đang dùng** — xem case study parallax.
7. **Attribute mới phải trace đủ chuỗi**: block.json → edit → save/render → CSS.
