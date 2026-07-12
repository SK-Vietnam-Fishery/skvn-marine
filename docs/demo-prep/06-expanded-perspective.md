# 06 — Góc nhìn mở rộng (AI perspective)

> Yêu cầu #4 của user 2026-07-12: quan điểm riêng của AI, vượt ngoài đầu bài.
> Mỗi mục P1–P3 có tension entry trong `.context/TENSIONS_OPEN.md` (T-DEMO-1/2/3) — human quyết.
> P4–P5 là quan sát, không cần quyết ngay.

---

## P1 — Demo trust = stability > feature count (→ T-DEMO-1)

Người xem demo B2B không đếm số block; họ nhớ khoảnh khắc giật/vỡ layout. Một bug
parallax hiện ra 3 giây trong demo xoá giá trị của cả tháng feature work.

Hệ quả thực tế trong project: 1.3.8 được đánh DONE khi acceptance chưa tick và bug còn.
Vấn đề không phải ai làm sai — mà là **quy trình không có chỗ đứng cho công việc
"stabilize"**: fix sau-DONE phải chui vào hotfix ngoài sổ (`ba03f08`, `406d42d` không
thuộc milestone nào). Đề xuất: mở **1.3.15 — Parallax Stabilization** để công việc fix
có milestone, có acceptance, có evidence — thay vì vá ngầm dưới nhãn đã đóng.

Nguyên tắc rút ra: *milestone có runtime output chỉ DONE khi có evidence onsite.*

## P2 — QA gate + freeze discipline (→ T-DEMO-2)

Hai milestone QA tổng hợp (1.3.11, 1.3.16) trùng scope là triệu chứng của QA-batching:
QA bị dồn thành "việc để sau", và khi danh sách quá dài thì tạo thêm danh sách mới thay
vì chạy danh sách cũ. Kết quả: chưa có mục QA nào của 1.3.x chạy cả.

Đề xuất: một **Pre-Demo QA Gate** duy nhất + QA slice 15–30 phút per surface (chạy được
ngay, không cần chờ "đủ lớn"). Kèm **code freeze T-3 ngày** — vì theo kinh nghiệm
WordPress: regression sát demo hầu hết đến từ "tiện tay sửa nốt".

## P3 — Diagnosis quality: verify library theo đúng version (→ T-DEMO-3 liên quan)

`swiper-architecture.md` Rev 1.2 viết invariant trên premise "Swiper tạo loop clones khi
init" — đúng với Swiper ≤8, **sai với v11 mà project đang dùng**. Fix xây trên premise
sai nên không hết bug, nhưng invariant sai thì còn nguy hiểm hơn: nó sẽ dẫn đường mọi
AI agent/dev sau này.

Đề xuất rule (thêm vào AGENTS.md nếu human duyệt):

> Trước khi viết invariant/decision về behavior của thư viện ngoài, phải verify against
> đúng major version trong `package.json` (docs/changelog của version đó). Kiến thức
> tổng quát về thư viện không đủ điều kiện làm premise cho invariant.

Áp dụng ngay: sau khi parallax fix xong, `swiper-architecture.md` phải lên Rev 1.3 với
premise đúng — kể cả khi fix cũ tình cờ vẫn được giữ.

## P3b — Advanced parallax controls: ẩn khỏi demo (→ T-DEMO-3)

Human đã reversal 2026-07-01: expose raw depth/direction/translate/scale/inset qua
Advanced toggle. Tôi không phản đối quyết định đó cho **product**, nhưng cho **demo**:

- Advanced controls nhân test matrix lên ~8× (mỗi combo là một đường bug riêng).
- Bug hiện tại chưa có root cause — surface control rộng = surface bug rộng.
- Người xem demo không cần thấy Advanced; Preset Subtle/Medium/Strong đủ kể chuyện.

Đề xuất phạm vi hẹp: demo content chỉ dùng Preset; không tắt code Advanced, chỉ **không
dùng** trong demo pages + không show trong demo script. Đây là mitigation rẻ nhất — zero
code change. Nếu muốn chắc hơn: một constant tạm ẩn Advanced panel, gỡ sau demo (cần
human approve vì đi ngược reversal).

## P4 — Version ledger là single source of truth chỉ khi có một ledger

1.3.12 double-booked, 1.3.13/14 ship ngoài sổ, planning header stale — từng cái vô hại,
cộng lại thì `MILESTONES.md` không còn tin được, và **AI agent load nó làm context mỗi
task**. Với dự án vận hành bằng AI agents, ledger sai = agent lạc có hệ thống.
Fix rẻ (30 phút), giá trị lâu dài. Checklist trong `02-milestone-critique.md` C4.

## P5 — Những thứ đúng đắn nên TIẾP TỤC bỏ qua đến sau demo

Để cân bằng — các nợ sau là thật nhưng **không được** để lọt vào 3 tuần tới:

- Plugin CSS independence (A13) — debt có chủ ý, giữ nguyên.
- 1.1.2 quote data-flow, 1.4.x, woo-catalog 1.5.0, GU Supercharger.
- REM layout debt, heading numbering skip-level.
- Header Actions orphan (tension HIGH sẵn có) — quan trọng nhưng không thuộc demo slider.

Kỷ luật "không làm" cũng là một quyết định kiến trúc.
