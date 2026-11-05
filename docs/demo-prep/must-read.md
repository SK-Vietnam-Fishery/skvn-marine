# MUST READ — Demo Prep Index

> **Mục tiêu bộ docs này:** hoàn thiện slider/carousel kịp demo (deadline >2 tuần từ 2026-07-12).
> Tạo bởi Claude (Fable 5) theo yêu cầu human 2026-07-12, sau khi human confirm:
> demo show tất cả surfaces; **2 bug parallax vẫn còn sau 2 hotfix**.
>
> Đây là working docs cho giai đoạn demo — không phải decision docs. Quyết định chốt
> vẫn theo `docs/decisions/` + `.context/MILESTONES.md` sau khi human duyệt đề xuất.

---

## Đọc theo thứ tự

| # | File | Là gì | Khi nào đọc | Ai dùng |
|---|---|---|---|---|
| 1 | [01-progress-assessment.md](01-progress-assessment.md) | Trạng thái thật của từng surface + rủi ro xếp hạng + định nghĩa "demo-ready" | Đầu tiên, 5 phút để nắm toàn cảnh | Human + AI |
| 2 | [02-milestone-critique.md](02-milestone-critique.md) | Phản biện milestone: 1.3.8 DONE ảo, 1.3.11≈1.3.16 trùng, ledger drift, 6 đề xuất chờ human quyết | Trước khi quyết cấu trúc milestone cho 3 tuần tới | Human quyết, AI thi hành |
| 3 | [03-demo-plan.md](03-demo-plan.md) | Plan 3 tuần: tuần 1 parallax P0, tuần 2 Step Slider + content + QA slices, tuần 3 buffer + freeze. Kèm demo script, env readiness, rollback | Sau khi duyệt critique — đây là lịch làm việc | Human + AI + Dev |
| 4 | [04-parallax-debug-protocol.md](04-parallax-debug-protocol.md) | **P0 kỹ thuật.** Hypothesis tree có test cô lập (H1 inset layout jump, H2 transition desync), instrumentation, test matrix 7 dòng, fix directions xếp hạng, exit criteria | Trước khi đụng vào code parallax — KHÔNG đoán-vá tiếp | AI debug, Dev verify |
| 5 | [05-step-slider-mvp-cut.md](05-step-slider-mvp-cut.md) | Đường cắt MVP cho 1.3.10: 3 slice có gate riêng, danh sách OUT rõ ràng, bookkeeping phải sửa trước | Trước khi bắt đầu code Step Slider — cần human approve cut | Human approve, AI code |
| 6 | [06-expanded-perspective.md](06-expanded-perspective.md) | Góc nhìn mở rộng của AI (yêu cầu human #4): stability>features, QA discipline, rule verify library version, ledger hygiene, những nợ nên tiếp tục bỏ qua | Khi review 3 tension T-DEMO-1/2/3 trong TENSIONS_OPEN.md | Human |
| 7 | [07-demo-content-seeding.md](07-demo-content-seeding.md) | 30 sản phẩm B2B demo: ma trận Species×Cut×Form, SKU convention, quy trình import CSV idempotent, 8 ảnh dùng chung | Tuần 2, trước khi seed content | Dev thực hiện |
| 8 | [assets/demo-products.csv](assets/demo-products.csv) | CSV 30 products sẵn import (WooCommerce native importer, update theo SKU) | Cùng lúc với file 7 | Dev import |

## Tóm tắt 30 giây cho người bận

1. **Parallax là P0** — 2 lần fix không hết vì chẩn đoán "loop clones" sai với Swiper v11.
   Nghi phạm mới: `--skvn-parallax-inset` set sau first paint. Protocol ở file 4.
2. **Step Slider 1.3.10 làm bản MVP** (tabs + progress, bỏ motion polish) — file 5, cần bạn approve cut.
3. **30 sản phẩm demo đã sinh sẵn CSV** — Dev import 15 phút, file 7 + 8.
4. **3 việc chờ bạn quyết** (tension T-DEMO-1/2/3): mở 1.3.15 Parallax Stabilization;
   merge 1.3.11+1.3.16; demo chỉ dùng Preset parallax (ẩn Advanced).
5. **Code freeze T-3 ngày trước demo.** Rollback zip chuẩn bị ở tuần 3.

## Liên kết ngược (context hệ thống)

- Tension index: `.context/TENSIONS_OPEN.md` (3 entries T-DEMO-*)
- Milestone ledger: `.context/MILESTONES.md`
- Parallax decision: `docs/decisions/slider-parallax-both-1.3.8.md`, `docs/decisions/swiper-architecture.md` (cần lên Rev 1.3 sau fix)
- Step Slider planning: `.context/planning/018_VERSION_1_3_10_FULLSCREEN_STEP_SLIDER_PLANNING.md`
- QA checklists sẵn có: `docs/testing/onsite-*.md`

## Vòng đời bộ docs này

- Trong 3 tuần demo: cập nhật trạng thái trực tiếp vào các file này.
- Sau demo: nội dung còn giá trị dài hạn (protocol debug, MVP cut đã thành hiện thực,
  seeding matrix) → promote vào `docs/decisions/` hoặc `docs/testing/`; phần còn lại
  archive. Không để folder này thành ledger thứ hai.
