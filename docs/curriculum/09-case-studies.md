# Module 09 — Case studies: học từ tai nạn thật của dự án

> Mỗi case: chuyện gì xảy ra → vì sao → phát hiện bằng cách nào → luật rút ra.
> Đọc case tương ứng khi học module liên quan (ghi trong ngoặc).

---

## Case 1 — Editor memory leak: Swiper chồng Swiper (đọc cùng module 05)

**Chuyện:** Editor có slider càng dùng càng ì, RAM tăng không hạ.
**Vì sao:** Editor là SPA — block re-render nhiều lần; mỗi lần init Swiper mới mà không
destroy instance cũ. Instance + listeners tích lũy trong iframe editor.
**Phát hiện:** DevTools Memory heap snapshot trước/sau thao tác duplicate/xóa block —
đếm instance còn giữ reference.
**Fix thật (2026-06-16):** MutationObserver phát hiện node slider bị gỡ khỏi DOM →
`swiper.destroy()` + tháo listeners.
**Luật:** mọi `new X()` gắn vào DOM node phải có đường hủy khi node biến mất. Viết init
là viết luôn cleanup, không "để sau". Chi tiết: `docs/standards/memory-leak-prevention-rules.md`.

## Case 2 — Parallax fix 2 lần không hết: premise sai về thư viện (module 05)

**Chuyện:** 2 bug (giật slide 2/3; ảnh scale khi load). Fix lần 1 đổi timing inject
attrs dựa trên chẩn đoán "Swiper tạo loop clones khi init nên clones thiếu attrs".
Fix lần 2 hotfix tiếp. 2026-07-12: bug vẫn còn.
**Vì sao:** Chẩn đoán viết bằng kiến thức Swiper cũ. Dự án dùng Swiper v11 — loop mode
từ v9 **không còn tạo DOM clones**. Fix xây trên premise sai thì đổi gì cũng không trúng.
Tệ hơn: premise sai được ghi thành *invariant* trong decision doc → dẫn đường sai cho
mọi người đọc sau.
**Phát hiện:** so hành vi thật với changelog/docs đúng major version trong package.json.
**Luật:** trước khi kết luận về behavior thư viện, mở docs/changelog **đúng version
đang cài**. Kiến thức tổng quát không đủ điều kiện làm premise. Và: fix phải có test
cô lập chứng minh cơ chế (tắt loop còn giật không? hardcode biến CSS còn nhảy không?)
trước khi viết code fix. Protocol mẫu: `docs/demo-prep/04-parallax-debug-protocol.md`.

## Case 3 — Attribute có mà class không ra: đứt chuỗi trace (module 01, 06)

**Chuyện:** `imageRatio` có control trong Inspector, user chọn thoải mái — nhưng
frontend không đổi gì. Attribute được lưu mà không ai tiêu thụ: class
`skvn-collection--ratio-*` chưa từng được render ra.
**Phát hiện:** review theo checklist chuỗi attribute; hoặc đơn giản: bật control lên
và nhìn DOM frontend — không thấy class nào đổi.
**Luật:** attribute mới phải trace đủ: block.json → edit control → save/render output
(class/style) → CSS rule tồn tại. Thiếu một mắt xích = control giả. Test bằng
**hành vi** (DOM đổi thật) chứ không phải grep thấy tên attribute trong code.

## Case 4 — Arrows biến mất: absolute positioning vs overflow (module 04)

**Chuyện:** Nav arrows của carousel đặt `left/right: -1.25rem` (thò ra ngoài) — bị
`overflow: hidden` của Swiper container cắt cụt, không nhìn thấy, không bấm được.
**Vì sao:** element absolute thò ra ngoài một ancestor có overflow hidden thì bị clip —
CSS cơ bản, nhưng chỉ lộ khi ghép hai mảnh (arrows + Swiper) vốn test riêng lẻ đều "pass".
**Fix thật (1.3.7):** dời arrows lên section header, ngoài vùng overflow.
**Luật:** khi element absolute "biến mất", nghi phạm đầu tiên là overflow của ancestor.
Và không dùng `overflow` để che lỗi geometry khác (luật trong css-layout-safety-contract).

## Case 5 — REM làm vỡ layout khi user đổi font (module 04)

**Chuyện:** biến layout (width, spacing container) khai bằng REM. User chỉnh
Typography trong Customizer → font-size gốc đổi → toàn bộ kích thước layout co giãn theo.
**Vì sao:** REM tính theo root font-size — dùng cho typography thì đúng, dùng cho
khung layout thì đã buộc layout vào font.
**Luật:** kích thước layout dùng px (+ media query); REM để dành cho chữ và spacing
gắn với chữ. Quyết định đơn vị = quyết định "giá trị này scale theo cái gì".

## Case 6 — `$_SERVER['HTTP_HOST']` trong URL tin cậy (module 02)

**Chuyện:** `source_url` cho quote form build từ `HTTP_HOST` — attacker đổi Host header
là độc được URL lưu vào CFDB7 (host-header poisoning).
**Fix thật (1.3.6):** dùng WordPress routing (`get_permalink()`, `home_url()`) —
WordPress biết host thật từ config, không tin request header.
**Luật:** mọi URL "của site mình" phải sinh từ API WordPress, không bao giờ từ
`$_SERVER`. Header của request là input của attacker.

## Case 7 — Feature orphan: store mà không render (module 07)

**Chuyện:** Header Actions (0.12.0) có settings page đầy đủ, option lưu ngon, getter
public — nhưng không một dòng code nào render nó ra frontend. Feature tồn tại trong
admin và không tồn tại với người xem site, suốt nhiều milestone.
**Vì sao:** làm feature theo tầng (plugin xong phần plugin) mà không ai giữ chuỗi
end-to-end; phần theme thuộc milestone khác và bị trôi.
**Luật:** định nghĩa "xong" của feature là **người dùng cuối nhìn thấy** — store →
getter → render → CSS → test. Cắt milestone theo chuỗi dọc (một feature chạy hết chuỗi)
tốt hơn theo tầng ngang.

---

## Tổng kết meta — vì sao các bug này lọt

Nhìn 7 case, có một mẫu chung: **từng mảnh pass, tổng thể fail** — arrows pass +
overflow pass nhưng ghép thì clip; attribute pass + CSS pass nhưng chuỗi đứt giữa;
fix pass build nhưng premise sai. Người thay thế AI agent chính là người giữ **cái
nhìn xuyên chuỗi**: mỗi thay đổi, tự hỏi "đi từ chỗ user bấm đến chỗ user nhìn thấy,
qua những mắt xích nào, mắt nào chưa được chứng minh bằng hành vi thật?"
