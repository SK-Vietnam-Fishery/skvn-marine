# Onsite bổ sung — Carousel engine behavior, 1.3.17

Ngày: 2026-10-14. Status: **PREPARED / NOT RUN**. Target milestone: **V1 / 1.3.17**.
Nguồn: [review](../ideations/skvn-slider-engine-strategy-review.md), [planning 040](../../.context/planning/040_VER_1_3_17_CAROUSEL_UI_UX_TECHNICAL_PLANNING.md), TECH-02/04/05/06/09.

## Target/setup

URL/page onsite: **chưa được cung cấp; human ghi trước test**. Fixture Product + Post Carousel, thêm Hero để kiểm coexistence, dữ liệu Woo mặc định đủ vượt desktop visible count; case riêng 0/1/ít item. Human chuẩn bị trên môi trường test được phép. Không tự sửa production. Ghi browser/viewport/preset/count/delay và source/build/deploy version. DevTools console/network sẵn. Autoplay off baseline rồi bật cho test; reduced motion bình thường trước.

## Exact steps và expected/pass-fail

| ID | Steps | Expected |
|---|---|---|
| ER-01 | Autoplay on, focus ngoài; chờ đổi slide; hover card >2 delay rồi rời mouse | Hover giữ slide, resume khi hết reasons. Ghi countdown reset/resume; chưa coi reset là FAIL khi policy chưa chốt |
| ER-02 | Pause, rời mouse/focus, đổi tab/quay lại, chờ >2 delay; Play rồi rời mouse/focus | Explicit Pause không tự phát; Play cho phép chạy khi hết reasons khác. Label/aria-pressed khớp runtime |
| ER-03 | Focus link và rời mouse >2 delay, rồi rời focus; lặp vừa hover vừa focus rồi bỏ từng reason | Không phát khi còn một reason; không xóa explicit Pause |
| ER-04 | Reload tab nền, chờ >2 delay rồi quay lại; fixture có focus trước init kiểm initial focus | Không khởi động trái hidden/focus policy. Thiếu fixture hoặc browser không tải nền: NOT TESTED. Throttling có thể che lỗi, cần trace initial state |
| ER-05 | Hover/focus arrows/header/footer CTA mỗi vùng >2 delay | Pause scope khớp TECH-04; ghi vùng còn phát để đối chiếu |
| ER-06 | Off; on với 0/1/ít item; bật OS reduce và reload | Off/reduce không autoplay; controls không gây hiểu nhầm khi runtime disabled; count theo 039/040 |
| ER-07 | Hai carousel: Tab/Shift+Tab/Enter/Space/←/→; focus input ngoài, kiểm offscreen | Không navigation chéo/chặn input. Keyboard kiểm riêng theo scope được executor định nghĩa |
| ER-08 | Counts ít/đủ/nhiều tại 639/640/641 và 1023/1024/1025px; resize/orientation; arrows/dots/swipe 10 vòng | Không slide trống/index sai/clip/tràn; ghi count/preset/viewport khi FAIL |
| ER-09 | Hai Collection + Hero thao tác riêng; fixture hook init→cleanup→init 10 vòng, heap sau GC nếu hỗ trợ; removal owner gọi cleanup trước remove | Không init trùng/callback sau destroy; heap/listeners không tăng liên tục. Thiếu hook: NOT TESTED. Reload/root biến mất không chứng minh teardown PASS |

## Evidence và tiếp tục

Gửi URL/browser/viewport/count/preset/delay/deploy version; ER-01…09 PASS/FAIL/NOT TESTED. FAIL kèm steps tối thiểu, video focus/control/index, label/DOM screenshot, console. Lifecycle kèm số vòng, init/destroy counts, cleanup owner, heap/listeners. Không gửi credential.

Agent đối chiếu evidence/source rồi chuẩn bị fix/handoff trong scope được duyệt. Đây là supplement, không thay full visual/milestone QA TECH-11 hoặc human approval. Chưa chạy onsite. Nếu human dời test: ghi DEFERRED + ngày/lý do và target milestone vào MILESTONES.md; hiện chưa có quyết định dời.