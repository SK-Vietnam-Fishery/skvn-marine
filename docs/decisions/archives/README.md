# Decision Archives — SKVN Marine

> Quy ước chung: [`../README.md`](../README.md).
> Thư mục này **không được load mặc định**. Chỉ đọc khi audit lịch sử quyết định, truy vết vì sao một milestone có quyết định đó, hoặc khi human yêu cầu.

## Vì sao ở đây

Một decision bị chuyển vào `archives/` khi thỏa **một** trong ba điều kiện:

1. **Superseded** — có decision mới thay thế (bắt buộc ghi `Superseded by:`).
2. **Implemented và milestone đã đóng** — onsite QA đã được carry sang milestone sau, không còn là contract sống.
3. **Living doc đã đóng băng** — doc tổng hợp còn sống cho một nhóm milestone đã qua.

`IMPLEMENTED` **không** tự động đủ điều kiện archive. Nếu còn contract đang hiệu lực hoặc còn source/context phụ thuộc → giữ ở root.

## Danh mục

| File | Status | Vì sao archive | Thay bằng / hướng dẫn |
|---|---|---|---|
| `gutenberg-supercharger-launch-1.3.11.md` | SUPERSEDED | Human đổi launch timing 2026-06-23 | [`../gu-supercharger-launch-post-1.5x.md`](../gu-supercharger-launch-post-1.5x.md) |
| `skvn-1.3.x-architecture-and-decisions.md` | ARCHIVED | Living doc đóng băng ở 1.3.5; milestone đã tới 1.3.10+ | Decision doc riêng theo từng milestone 1.3.6+ |
| `slider-static-markup-migration-1.2.1.md` | SUPERSEDED | Human từ chối hướng static saved markup trước khi implement | [`../slider-completion-spec-1.3.0.md`](../slider-completion-spec-1.3.0.md) |
| `slider-presets-and-inserter-1.2.1.md` | IMPLEMENTED | Onsite QA carried sang V1 / 1.2.9 | Contract slider hiện hành ở root |
| `slider-block.md` | ARCHIVED | Decision log gốc 0.3.0 + editor 1.2.0 | [`../slider-completion-spec-1.3.0.md`](../slider-completion-spec-1.3.0.md), [`../swiper-architecture.md`](../swiper-architecture.md) |
| `slider-carousel-showcase-ux.md` | ARCHIVED | Direction approved nhưng implementation chưa bắt đầu; vẫn là nguồn cho `.context/planning/archives/019_FUTURE_CANDIDATE_SLIDER_CAROUSEL_SHOWCASE_UX_PLANNING.md` | Không implement nếu chưa có milestone mới |
| `blog-sidebar-content-settings.md` | IMPLEMENTED | Code xong 3 phase, onsite test pending; bản tạm, milestone đúng sẽ refactor (1.4–1.6.x) | Không có decision thay thế; giữ module đến khi refactor |
| `loading-screen-settings-1.3.13.md` | IMPLEMENTED | Code xong 2 phase, onsite test pending; hậu tố version chưa được xác nhận | — |
| `post-heading-numbering-1.3.14.md` | IMPLEMENTED | Onsite test pending; hậu tố version chưa được xác nhận | — |

## Quy tắc khi đọc file trong này

- Nội dung giữ nguyên lịch sử, gồm cả câu chữ đã lỗi thời về runtime.
- Chỉ sửa **path** khi reference trỏ tới file đã đổi vị trí; giữ nguyên câu chữ lịch sử.
- Không dùng file ở đây làm source of truth cho code mới. Nếu phát hiện contract còn hiệu lực nhưng file đang nằm ở `archives/` → báo human để đưa về root.