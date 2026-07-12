# 01 — Đánh giá tiến độ (2026-07-12)

> Snapshot trước demo. Người viết: Claude (Fable 5), vai trò WordPress expert review.
> Nguồn: `.context/MILESTONES.md`, git log đến `406d42d`, human feedback 2026-07-12.

---

## 1. Trạng thái các surface demo

| Surface | Milestone | Code | Onsite QA | Đánh giá demo-readiness |
|---|---|---|---|---|
| Hero Slider (dynamic render, controls) | 1.3.0 + 1.3.1 | ✅ DONE | ✅ human approved 06-11/06-12 | **Sẵn sàng** |
| Slider Parallax | 1.3.8 + 2 hotfix | ⚠️ shipped, **bug còn** | ❌ human confirm 07-12: stutter + pre-scale vẫn còn | **P0 — chưa demo được** |
| Product/Post Carousel + Grid | 1.3.3 + 1.3.7 | ✅ DONE | ❌ deferred → 1.3.11 | **Cần QA + content thật** |
| Feature Showcase (tab/step, không Swiper) | 1.2.3 | ✅ DONE | ⚠️ một phần (1.2.9) | Gần sẵn sàng, QA nhẹ |
| Fullscreen Step Slider | 1.3.10 | ❌ **chưa bắt đầu** | — | Cần MVP cut (xem `05-step-slider-mvp-cut.md`) |

## 2. Parallax — timeline sự kiện

```
2026-07-01  1.3.8 marked DONE + human approved (acceptance boxes chưa tick)
2026-07-01  9ddcdde  Advanced controls (depth/direction/translate/scale/inset)
2026-07-02  ba03f08  Fix #1: defer attr injection sau new Swiper() + swiper.update()
2026-07-03  406d42d  Hotfix #2: version bump + block.json normalize + swiper-architecture.md
2026-07-12  Human confirm: CẢ 2 BUG VẪN CÒN (stutter slide 2/3, ảnh scale khi load)
```

**Kết luận:** chẩn đoán "loop clones thiếu attrs" trong `docs/decisions/swiper-architecture.md`
(Rev 1.2) nhiều khả năng **sai với Swiper v11** — v9+ đã bỏ DOM clone trong loop mode.
Fix dựa trên chẩn đoán đó nên không hết bug. Chẩn đoán lại: `04-parallax-debug-protocol.md`.

## 3. Nợ QA tích lũy

QA onsite bị dồn từ 4 milestone (1.3.3, 1.3.6, 1.3.7, 1.3.8) vào 1.3.11, sau đó
1.3.16 được tạo với scope gần trùng 1.3.11. Hệ quả thực tế: bug parallax lọt qua
"DONE" và chỉ được phát hiện khi human tự xem. Chi tiết + đề xuất: `02-milestone-critique.md`.

## 4. Nợ ngoài scope demo (không chặn demo, không làm bây giờ)

- 1.1.2 quote data-flow testing, 1.4.1 layout blocks validation + T-2 `source_url`
- Header Actions orphan (tension HIGH, milestone 1.4.0)
- REM layout debt, heading numbering skip-level, Tab 3 WP_Query static fallback
- Blog sidebar 1.3.12 / preloader 1.3.13 — code xong, chờ Dev test (không thuộc demo slider)

## 5. Rủi ro chính cho demo (xếp hạng)

1. **Parallax bug chưa có root cause đúng** — đã fix 2 lần không hết → cần protocol chẩn đoán có kỷ luật, không đoán-vá tiếp.
2. **Step Slider chưa bắt đầu** — block family mới; nếu làm full contract sẽ lấn giờ QA.
3. **Carousel chưa từng chạy QA onsite với data thật** — rủi ro ẩn (image fallback, Swiper init, responsive preset).
4. **Không có freeze rule** — thay đổi sát ngày demo là nguồn regression lớn nhất.

## 6. Điều kiện đủ để demo (định nghĩa "xong")

- [ ] Parallax: 2 bug hết trên site thật, verify bởi Dev theo exit criteria trong `04`
- [ ] Step Slider MVP: đủ tab nav + progress + keyboard + reduced-motion (cut line trong `05`)
- [ ] Carousel/Grid: pass demo-QA checklist với content thật
- [ ] Demo site: content seeded, cache rõ ràng, plugin zip rollback sẵn
- [ ] Code freeze trước demo ≥ 3 ngày (chỉ fix, không feature)
