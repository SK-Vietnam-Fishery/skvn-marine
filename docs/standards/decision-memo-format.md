# Decision Memo Format — Chuẩn trình quyết định cho human

> Sinh từ quyết định human 2026-07-12: "Agent phải cung cấp đủ thông tin, có cấu trúc,
> để human vừa research vừa ra quyết định đúng."
> Áp dụng cho MỌI lần agent xin human quyết định: tension, ASK_ARCHITECTURE,
> milestone approval, scope change, dependency mới.
> Bản tóm tắt nằm trong `AGENTS.md` — file này là bản đầy đủ kèm ví dụ.

---

## Nguyên tắc gốc

1. **Không hỏi trần.** "Anh muốn A hay B?" mà không kèm trade-off + bằng chứng = chuyển
   gánh nặng research sang human. Agent làm research; human cung cấp thứ agent không có:
   khẩu vị rủi ro và ưu tiên kinh doanh.
2. **Bằng chứng ≠ lời khai.** "Đã fix", "chắc là do X" là lời khai. Link commit, số liệu,
   screenshot, đoạn changelog — là bằng chứng. Memo không có mục Bằng chứng điền được
   nghĩa là agent **chưa đủ tư cách hỏi** — quay lại research tiếp.
3. **Human phải kiểm chứng độc lập được trong ≤10 phút.** Mục "Tự kiểm chứng" tồn tại
   để human không phải tin agent — chỉ cần theo con trỏ là tự thấy.
4. **Khẳng định về thư viện ngoài phải cite nguồn đúng major version** đang cài trong
   `package.json` (bài học premise "loop clones" — `docs/curriculum/09-case-studies.md` case 2).

## Format — 7 mục, đúng thứ tự

```markdown
### Decision Memo — [tiêu đề ngắn]

1. Vấn đề:        [1 câu. Nếu cần hơn 1 câu → tách thành nhiều memo]
2. Phương án:
   - A: [1–2 dòng] — trade-off chính: [...]
   - B: [1–2 dòng] — trade-off chính: [...]
   (2–3 phương án; nếu chỉ có 1 phương án hợp lệ thì đây không phải quyết định — cứ làm)
3. Khuyến nghị:   [A/B] vì [lý do 1–2 dòng]
4. Bằng chứng:    [link commit/file:line/screenshot/số đo — thứ nhìn được]
5. Tự kiểm chứng: [nguồn gốc đúng version + file repo + xem gì, để human research độc lập ≤10 phút]
6. Đảo ngược:     [2 chiều → quyết nhanh được | 1 chiều → cần evidence dày, quyết chậm]
7. Default:       [nếu human không quyết trước [mốc], agent làm phương án conservative nào]
```

## Quy tắc cho mục 7 (Default)

- Default luôn là phương án **conservative** — ít thay đổi trạng thái nhất, dễ đảo nhất.
- Default chỉ áp dụng cho quyết định **không chặn** (tension severity `low`, lựa chọn
  scope/UX). Tension `high` và invariant: default luôn là **DỪNG chờ human** — không
  được dùng Default để lách protocol.
- Mốc thời gian phải cụ thể (ngày hoặc sự kiện: "trước khi merge fix X"), không ghi "sớm".

## Ví dụ thật (T-DEMO-1, 2026-07-12)

```markdown
### Decision Memo — Chỗ chứa công việc fix parallax

1. Vấn đề:        1.3.8 đã DONE nhưng 2 bug parallax còn; công việc fix không có milestone chứa.
2. Phương án:
   - A: Mở 1.3.15 — Parallax Stabilization — trade-off: thêm 1 entry ledger, minh bạch.
   - B: Tiếp tục hotfix ngoài sổ dưới nhãn 1.3.8 — trade-off: nhanh, nhưng ledger tiếp tục nói dối.
3. Khuyến nghị:   A — 2 hotfix ngoài sổ vừa rồi chính là hệ quả của B.
4. Bằng chứng:    MILESTONES.md 1.3.8 acceptance toàn [ ] nhưng Status DONE; commit ba03f08,
                  406d42d không thuộc milestone nào; human confirm bug còn 2026-07-12.
5. Tự kiểm chứng: mở .context/MILESTONES.md §1.3.8 (60 giây); git log --oneline -5 (60 giây);
                  swiper.js migration guide v9 (https://swiperjs.com/migration-guide-v9) mục
                  Loop mode — đối chiếu premise "clones" trong docs/decisions/swiper-architecture.md.
6. Đảo ngược:     2 chiều (milestone entry sửa/gộp lại được).
7. Default:       Nếu chưa quyết trước khi bắt đầu fix: track công việc trong docs/demo-prep/
                  (không đổi MILESTONES.md), fix vẫn tiến hành — không chờ.
```

## Checklist tự chấm trước khi nộp memo

- [ ] Vấn đề đúng 1 câu
- [ ] Mọi phương án có trade-off (phương án không có nhược điểm = chưa nghĩ đủ)
- [ ] Bằng chứng là artifact nhìn được, không phải "theo phân tích của tôi"
- [ ] Tự kiểm chứng có ước lượng thời gian, tổng ≤10 phút
- [ ] Khẳng định thư viện ngoài có cite nguồn đúng version
- [ ] Default là conservative + có mốc cụ thể (hoặc ghi rõ "DỪNG" nếu là quyết định chặn)
