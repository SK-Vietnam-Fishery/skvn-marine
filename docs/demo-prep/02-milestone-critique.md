# 02 — Phản biện milestone (2026-07-12)

> Critique cấu trúc milestone dưới góc nhìn demo deadline. Mỗi mục có đề xuất hành động.
> Các đề xuất C1, C2 có tension entry tương ứng trong `.context/TENSIONS_OPEN.md` — human quyết.

---

## C1 — 1.3.8 "DONE" nhưng chưa done

**Sự thật:**
- `.context/MILESTONES.md` ghi 1.3.8 `Status: DONE`, approved 2026-07-01.
- Toàn bộ acceptance checklist của 1.3.8 vẫn là `[ ]` (chưa tick một ô nào).
- Sau ngày approve có thêm 2 hotfix (`ba03f08` 07-02, `406d42d` 07-03).
- Human confirm 2026-07-12: 2 bug vẫn còn.

**Vấn đề:** DONE + unchecked boxes + bug còn = trạng thái milestone không phản ánh thực tế.
Người đọc MILESTONES.md (hoặc AI agent load context) sẽ tin parallax ổn.

**Đề xuất:** Mở milestone **1.3.15 — Parallax Stabilization** (patch scope) để chứa
debug + fix + Dev verify. Không sửa ngầm dưới nhãn 1.3.8 nữa — 2 hotfix "ngoài sổ"
vừa rồi chính là hệ quả của việc thiếu chỗ chứa công việc này.
→ Tension **T-DEMO-1**.

## C2 — 1.3.11 và 1.3.16 trùng scope

Hai milestone QA tổng hợp cùng cover: 1.3.7 collection UI, 1.3.8 parallax, 1.3.10 step
slider, button hover, console/memory checks. Khác biệt thực tế không đáng kể.

**Vấn đề:** hai danh sách gần giống nhau = không danh sách nào là source of truth;
checklist sẽ drift; công QA bị đếm hai lần trong planning.

**Đề xuất:** Merge thành một **Pre-Demo QA Gate** (giữ số 1.3.11, absorb nội dung 1.3.16;
1.3.16 đánh dấu ARCHIVED/merged). Item nào không thuộc demo scope → tách sang checklist
post-demo trong cùng file. → Tension **T-DEMO-2**.

## C3 — Anti-pattern: QA batching

QA onsite bị dồn từ 1.3.3 → 1.3.6 → 1.3.7 → 1.3.8 vào một mega-milestone. Hệ quả đã
thấy: 1.3.8 được approve khi chưa có evidence onsite, bug chỉ lộ khi human tình cờ xem.

**Đề xuất cho giai đoạn demo:** mỗi surface phải pass **QA slice riêng** (15–30 phút
Dev test theo checklist ngắn) trước khi tick DONE. Mega-QA chỉ còn là regression pass
cuối cùng trước freeze. Quy tắc: *"Không có evidence onsite → không tick DONE cho
milestone có runtime output."*

## C4 — Version ledger drift (bookkeeping)

| Vấn đề | Chi tiết | Fix |
|---|---|---|
| 1.3.12 double-booked | MILESTONES.md: Team Credits Easter Egg. Thực tế code: module sidebar-blog-content cũng nhận nhãn 1.3.12 | Human chọn: sidebar-blog-content nhận số mới (1.3.13-series?) hoặc Team Credits dời số |
| 1.3.13 / 1.3.14 không có trong MILESTONES.md | Preloader settings (1.3.13) + heading numbering (1.3.14) đã ship trong code nhưng ledger không ghi | Thêm entry retro vào MILESTONES.md với status thực |
| Planning 018 header sai | File ghi "V1 / 1.5.0 — Fullscreen Step Slider" nhưng milestone là 1.3.10 | Sửa header + dòng "Current milestone remains: 1.2.1" (stale) |
| Decision file tên sai | MILESTONES.md reference `fullscreen-step-slider-1.3.10.md` (không tồn tại); file thật là `fullscreen-step-slider-1.5.0.md` | ~~Rename file → update reference~~ — **xong 2026-10-13** |

Việc này nhỏ nhưng nên làm **trước** khi bắt đầu 1.3.10, vì AI agent load các file này
làm context — ledger sai thì agent lạc đường.

## C5 — 1.3.10 full contract quá lớn cho demo

Planning 018 gồm: tabs, progress, wipe transition, text cascade, Ken Burns, governed
motion presets, video policy, đầy đủ a11y. Làm hết trước demo sẽ ăn vào thời gian QA
và parallax fix. **Đề xuất:** MVP cut trong `05-step-slider-mvp-cut.md` — giữ phần
demo nhìn thấy (tabs + progress + chuyển slide mượt), dời motion polish ra sau demo.

## C6 — Chất lượng chẩn đoán kỹ thuật

`docs/decisions/swiper-architecture.md` Rev 1.2 viết invariant dựa trên premise
"Swiper tạo loop clones bên trong `new Swiper()`". **Swiper v11 (project đang dùng
`^11.1.15`) không tạo DOM clones trong loop mode** — cơ chế clone-based bị bỏ từ v9,
thay bằng slide rearrangement (`loopFix`). Fix xây trên premise này không hết bug —
đúng như human quan sát.

**Đề xuất rule mới cho AGENTS.md (human duyệt):** *"Trước khi viết invariant về
behavior của thư viện ngoài, verify against đúng major version đang dùng trong
package.json (đọc docs/changelog của version đó), không dùng kiến thức tổng quát."*
→ chi tiết trong `06-expanded-perspective.md` §P3.

## Tóm tắt hành động chờ human quyết

1. [ ] Mở 1.3.15 — Parallax Stabilization (T-DEMO-1)
2. [ ] Merge 1.3.11 + 1.3.16 → Pre-Demo QA Gate (T-DEMO-2)
3. [ ] Fix 4 mục version ledger (C4) — AI làm được ngay khi approve
4. [ ] Approve MVP cut 1.3.10 (file 05)
5. [ ] Duyệt rule "verify library version trước khi viết invariant" (C6)
