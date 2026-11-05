# AGENT FIRST RUN — Demo Prep Init (tạm thời)

> **File tạm ở root.** Human sẽ tự xóa sau khi agent đã init xong.
> Mục đích: agent mới (phiên mới, model mới) cold-start đúng vào giai đoạn demo-prep
> mà không lạc đường. Đây KHÔNG phải protocol thường trực — protocol thường trực là `AGENTS.md`.

---

## Bước 0 — Bạn là ai, đang ở đâu

- Repo: `D:\Github\skvn-marine` — source repo dev (KHÔNG phải WordPress runtime root).
- Giai đoạn hiện tại: **Demo prep slider/carousel**, deadline >2 tuần từ 2026-07-12.
- Human đang bận việc gia đình → làm việc tự chủ tối đa, chỉ hỏi khi quyết định thuộc về human.

## Bước 1 — Load context theo thứ tự (bắt buộc, không bỏ bước)

```
1. AGENTS.md                          → protocol bắt buộc, routing CODE_NOW/ASK_ARCHITECTURE/EXPLAIN
2. .context/GLOBAL.md                 → stack, module index, invariants
3. .context/MILESTONES.md             → milestone ledger (lưu ý: có drift, xem critique)
4. .context/TENSIONS_OPEN.md          → đọc HẾT; chú ý 3 entries T-DEMO-1/2/3 (2026-07-12)
5. docs/demo-prep/must-read.md        → SOURCE OF TRUTH giai đoạn demo; index 8 files
6. .local/ENVIRONMENT.md              → WSL/WP-CLI/build commands (local-only, không commit)
```

## Bước 2 — Trạng thái chốt tại 2026-07-12 (đỡ phải suy lại)

- **P0 = parallax**: 2 bug (stutter slide 2/3; ảnh scale khi load) **vẫn còn** sau 2 fix
  (`ba03f08`, `406d42d`). Premise "loop clones" trong `docs/decisions/swiper-architecture.md`
  **sai với Swiper v11**. Debug theo `docs/demo-prep/04-parallax-debug-protocol.md` — cấm đoán-vá.
- **Step Slider 1.3.10**: chưa code. Chỉ làm theo MVP cut `docs/demo-prep/05-step-slider-mvp-cut.md`
  và chỉ sau khi human approve cut + sửa bookkeeping (rename decision file 1.5.0→1.3.10).
- **30 sản phẩm demo**: CSV sẵn `docs/demo-prep/assets/demo-products.csv` — Dev import, agent không tự chạy.
- **Freeze T-3 ngày trước demo**: sau freeze chỉ fix, không feature.

## Bước 3 — Việc init cần làm ngay trong phiên đầu

1. [ ] Xác nhận đã đọc đủ 6 mục Bước 1 (báo lại human 1 dòng).
2. [ ] Check T-DEMO-1/2/3 trong `TENSIONS_OPEN.md`: human đã fill Decision chưa?
   - Đã resolve → move entry sang `TENSIONS_ACTIVE.md` theo AGENTS.md §4, rồi thi hành.
   - Chưa → hỏi human 3 câu ngắn (mở 1.3.15? merge 1.3.11+1.3.16? demo Preset-only?).
3. [ ] Hỏi human ngày demo chính xác (plan đang tính "T" trôi nổi — cần mốc thật để đặt freeze).
4. [ ] Nếu human nói "làm parallax" → route `CODE_NOW`, vào thẳng protocol 04, hypothesis H1 trước.
5. [ ] Sau mỗi lượt sửa plugin → output lệnh build+lint dạng text cho Dev (không tự chạy):
   `source /home/shinkuro/.nvm/nvm.sh && nvm use 20 && cd /mnt/d/Github/skvn-marine/wp-content/plugins/skvn-marine-blocks && npm run build && find modules/ -name "*.php" -exec php -l {} \;`

## Bước 4 — Ranh giới không được vượt (tóm tắt, đầy đủ trong AGENTS.md)

- KHÔNG sửa `themes/generatepress/`; KHÔNG rename namespace/prefix; KHÔNG custom quote handler.
- Test/deploy/QA là việc **Dev** — agent viết code + sinh lệnh + checklist, không tự khẳng định "pass".
- Command WSL không phản hồi/timeout → DỪNG, đưa lệnh cho human, không retry/poll.
- Không làm trước demo: 1.1.2, 1.4.x, woo-catalog 1.5.0, refactor kiến trúc, control mới ngoài MVP cut.
- Milestone có runtime output: không tick DONE khi chưa có evidence onsite (bài học 1.3.8).

## Khi file này hết nhiệm vụ

Human xóa file. Mọi nội dung ở đây đều có bản gốc trong `AGENTS.md`, `.context/`,
`docs/demo-prep/` — không có thông tin nào chỉ tồn tại ở file này.
