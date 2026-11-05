# 03 — Demo Plan 3 tuần (2026-07-12)

> Mục tiêu: demo toàn bộ surface slider/carousel với độ ổn định cao nhất.
> Deadline: >2 tuần (plan tính 3 tuần, tuần 3 là buffer).
> Phân vai theo rule dự án: **AI viết code + sinh lệnh; Dev chạy + verify + cung cấp evidence.**

---

## Nguyên tắc xuyên suốt

1. **Stability > feature count.** Một surface mượt tạo niềm tin hơn năm surface lắc.
2. **Không tick DONE khi chưa có evidence onsite** (bài học 1.3.8).
3. **Code freeze T-3 ngày** trước demo: chỉ fix bug, không thêm feature/control mới.
4. Việc gì Dev làm nhanh hơn (nhìn UI, chạy import, quay video) → bàn giao sớm kèm checklist.

---

## Tuần 1 — Ổn định nền (P0)

### 1.1 Parallax debug + fix (P0, chặn mọi thứ khác về slider)

- **AI:** chạy protocol `04-parallax-debug-protocol.md` → xác định root cause → viết fix.
- **Dev:** chạy build, deploy local, verify theo exit criteria trong file 04, báo evidence
  (video trước/sau, DevTools computed transform, console).
- Nếu human approve C1: công việc này mang nhãn **1.3.15 — Parallax Stabilization**.

### 1.2 Bookkeeping fix (song song, ~30 phút AI)

- Sửa 4 mục C4 trong `02-milestone-critique.md` (ledger, planning header, decision rename).

### 1.3 Step Slider MVP contract

- **Human:** đọc + approve `05-step-slider-mvp-cut.md` (10 phút).
- **AI:** sau approve → scaffold blocks (`step-slider`, `step-slide`), PHP render shell.

### 1.4 Pre-Demo QA Gate doc

- **AI:** merge checklist 1.3.11 + 1.3.16 → một file QA gate duy nhất, tách demo-scope
  vs post-demo (chờ human approve T-DEMO-2 trước khi sửa MILESTONES.md).

**Gate cuối tuần 1:** parallax hết bug trên local + Step Slider skeleton build pass.

## Tuần 2 — Step Slider MVP + Content + QA slices

### 2.1 Step Slider MVP (AI code, 3 slice, mỗi slice một build gate)

| Slice | Nội dung | Gate |
|---|---|---|
| S1 | Block registration, InnerBlocks, PHP dynamic render, fullscreen shell | build + php -l + editor insert OK |
| S2 | Bottom tabs (number+label), Swiper wiring, tab click đổi slide | Dev verify local |
| S3 | Progress bar sync autoplay, pause hover/focus, keyboard, aria-current, reduced-motion, no-JS fallback | Dev verify local + a11y check |

### 2.2 Content seeding (Dev ~1 giờ, theo `07-demo-content-seeding.md`)

- Import `assets/demo-products.csv` (30 sản phẩm B2B) vào local trước → check Product
  Grid/Carousel render → import lên demo site.
- Seed 6–10 posts thật cho Post Collection (tiêu đề + featured image + category).
- Upload 6–8 ảnh cut-type dùng chung; ALT tự fill từ title.

### 2.3 QA slices (Dev, mỗi slice 15–30 phút, checklist trong QA gate doc)

- Slice A: Hero Slider + Parallax (sau fix 1.1) trên demo site.
- Slice B: Product/Post Grid + Carousel với 30 sản phẩm thật (responsive presets, arrows,
  image fallback, quote URL per product).
- Slice C: Feature Showcase.

**Gate cuối tuần 2:** mọi surface trừ Step Slider đã pass QA slice trên demo site.

## Tuần 3 — Buffer + polish + freeze

- **3.1** Step Slider QA slice trên demo site (Dev).
- **3.2** Motion polish (chỉ khi 3.1 xong sớm): text cascade preset đơn giản cho Step Slider.
- **3.3** Regression pass cuối: chạy toàn bộ QA gate doc một lượt (Dev, ~2 giờ).
- **3.4** **Code freeze (T-3 ngày):** tag git + build zip last-known-good →
  `bash tools/package-plugin-zip.sh` → giữ zip làm rollback.
- **3.5** Demo dry-run: chạy demo script (bên dưới) một lượt, đo thời gian, ghi chú câu chuyện.

---

## Demo environment readiness (bổ sung — user chưa yêu cầu nhưng cần)

- [ ] Xác định demo trên site nào: local (localhost:8080) hay onsite/staging? URL chốt trước tuần 2.
- [ ] Cache: xác nhận trạng thái cache/CDN của demo site; purge trước dry-run.
- [ ] Plugin versions trên demo site khớp build freeze (check version header + `filemtime` assets).
- [ ] Browser demo: chốt 1 browser chính (Chrome), test thêm 1 mobile viewport.
- [ ] Mạng: nếu demo offline risk → chuẩn bị local fallback.

## Rollback plan

- Zip last-known-good từ bước 3.4 + ghi lại commit hash freeze.
- Nếu fix sát ngày gây regression: deactivate → cài lại zip freeze → không debug trên demo site.

## Demo script (thứ tự show — kể chuyện B2B funnel)

1. **Hero Slider + Parallax** (ấn tượng đầu) — wipe transition, depth subtle.
2. **Product Grid** 30 sản phẩm — filter câu chuyện "một loài cá, nhiều cách bán".
3. **Product Carousel** — responsive, arrows, autoplay pause.
4. **Product card → Request a Quote** — URL mang product context (điểm B2B mạnh nhất).
5. **Step Slider** — quy trình sản xuất/xuất khẩu (storytelling close).
6. (Nếu hỏi thêm) Feature Showcase, Post Collection, editor experience.

## Việc KHÔNG làm trước demo

- Không refactor kiến trúc, không thêm control mới ngoài MVP cut.
- Không đụng Advanced parallax controls (đề xuất demo chỉ dùng Preset — xem T-DEMO-3).
- Không làm 1.1.2 / 1.4.x / woo-catalog / GU Supercharger.
