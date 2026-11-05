# SVG Maps — Index và digest để tiếp tục trao đổi

**Milestone đã xác nhận 2026-11-04:** V1 / **1.4.3 — Interactive Illustrated Map**, **PLANNED — Chưa làm, chưa start**, sau 1.4.2 trước 1.5.0. Human chọn 1.4.3 như ngoại lệ đánh số cho feature mới (thông thường MINOR); không đổi quy tắc chung. Current vẫn 1.3.18; chưa bump/build hoặc triển khai. Scope/acceptance MAP-* tại [MILESTONES.md](../../../.context/MILESTONES.md); các ID digest bên dưới giữ truy vết. Chưa gán stack M1–M6; không tự đóng debt map/contact cũ.

**Cập nhật:** 2026-11-04.
**Phạm vi:** Tổng hợp các lượt đọc báo cáo và artifact trong chat; phân tích source, chưa chạy lại artifact trên trình duyệt.
**Trạng thái:** Planning V1 / 1.4.3; chưa triển khai plugin WordPress. Quyết định version ở trên không phải lệnh bắt đầu hoặc thay protocol repository.

## Quyết định bổ sung đã chốt — 04/11/2026

Phần này ưu tiên hơn các mô tả thiết kế cũ bên dưới. Các bảng digest source artifact vẫn mô tả bản thử trước thay đổi; không coi quyết định mới là implementation đã có.

- **Apply changes** trong thẻ: áp dụng nội dung vào nháp bản đồ; **Cancel** bỏ thay đổi chưa áp dụng trong thẻ. Apply changes không ghi DB hoặc xuất bản.
- **Save map** trong sidebar riêng của plugin: lưu bền toàn bộ bản đồ và các điểm. Map published tiếp tục hiển thị bản cũ cho tới khi toàn bộ lần lưu thành công. Lưu lỗi giữ nguyên bản published cũ và nháp editor để thử lại; không cập nhật dở từng phần. Lưu page không thay thế Save map.
- **Cấu hình dữ liệu thuộc sidebar plugin**: cấu hình nền, tỷ lệ và dữ liệu bản đồ, cùng Save map, nằm ở sidebar plugin và gắn rõ map đang sửa. Block giữ vai trò hiển thị/biên tập trực quan, tham chiếu mapId; không nhồi hoặc lặp form cấu hình dữ liệu trong block. Nội dung điểm vẫn sửa ngay trong thẻ. Không kế thừa đường sidebar ghi thẳng dữ liệu như artifact cũ.
- **Nền thiếu/gãy**: hiện lỗi rõ, không tự thay nền hoặc dời điểm để che lỗi. Editor hiện “Map background unavailable” và thao tác chọn lại nền; giữ nguyên tọa độ. Đây là xử lý nguồn nền hỏng, không phải quyết định xây snapshot revision.
- **Tỷ lệ cố định theo từng bản đồ sau khi tạo**, khởi đầu 4:3; không ép mọi bản đồ thành 4:3. Editor hiển thị tỷ lệ yêu cầu, tỷ lệ/kích thước nguồn, mức phù hợp và preview toàn bộ không crop. Khi thay nền đã có điểm, giữ tọa độ và nhắc kiểm lại vị trí. Cùng tỷ lệ không bảo đảm các chi tiết trong tranh ở cùng vị trí.
- **Hai bảng**: ii_maps và ii_map_points. Gộp nội dung popup vào điểm bằng detail_title, detail_body_html, detail_image_media_id. Không còn bảng ii_point_popups. Một điểm vẫn thuộc một map, chưa có thực thể địa điểm dùng chung giữa nhiều map.

| ID | Nhóm | Hành vi mong đợi | Hiện trạng | Việc cần làm | Cách kiểm | Evidence |
|---|---|---|---|---|---|---|
| ED-11 | Editor UI | Apply changes khác Save map | Chốt thiết kế; Chưa làm | Thể hiện nháp/saving/saved/error; Cancel đúng phạm vi | Apply/Cancel, Save lỗi/thành công, mở lại | Human chốt trong chat 04/11 |
| ED-12 | Editor UI | Cấu hình dữ liệu ở sidebar plugin | Chốt thiết kế; Chưa làm | Gắn rõ map, phạm vi dùng chung; không lặp form trong block | Chọn map/block rồi sửa, kiểm đúng chủ sở hữu | Human chốt trong chat 04/11 |
| ED-13 | Editor UI | Đủ thông tin để chọn/thay nền | Chốt thiết kế; Chưa làm | Hiện tỷ lệ, kích thước, mismatch, preview và nhắc kiểm vị trí | Nguồn đúng/sai tỷ lệ; tranh khác cùng tỷ lệ | Human chốt trong chat 04/11 |
| UI-10 | Giao diện | Nền hỏng hiện lỗi rõ và giữ tọa độ | Chốt thiết kế; Chưa làm | Hiện lỗi; editor có chọn lại nền | Chặn/xóa nguồn rồi chọn lại; so tọa độ | Human chốt trong chat 04/11 |
| BE-10 | Backend kỹ thuật | Hai bảng, nội dung trong điểm | Chốt thiết kế; Chưa làm | Mapping JSON/CRUD dùng detail_* | Lưu/đọc/xóa điểm gồm nội dung chi tiết | Human chốt trong chat 04/11 |
| BE-11 | Backend kỹ thuật | Chỉ công bố bản lưu thành công toàn bộ | Chốt thiết kế; Chưa làm | Thiết kế lưu thống nhất, xử lý lỗi và cache | Gây lỗi giữa lượt lưu; hai page cùng map vẫn thấy bản cũ; retry thành công mới cập nhật | Human chốt trong chat 04/11 |

ED-11 giải quyết hợp đồng lưu còn mở ở ED-10; BE-11 bổ sung BE-06. Snapshot, xử lý chỉnh đồng thời, thẻ rỗng, zoom và undo/reorder vẫn chưa chốt. Chưa đổi milestone hoặc bắt đầu implementation WordPress/artifact.

## 1. Đọc gì trước trong chat kế

| Tài liệu | Vai trò | Cách dùng |
|---|---|---|
| [Index này](README.md) | Baseline trao đổi, digest ba lớp và danh sách vấn đề còn mở | Đọc đầu tiên, dùng ID ở §5–7 để giao việc |
| [Báo cáo tổng hợp](bao-cao-tong-hop-ban-do-tuong-tac.md) | Ghi quyết định ngày 04/11: bảng riêng, block tham chiếu mapId, sửa popup ngay trong thẻ; có schema và rationale | Ưu tiên khi cần hiểu hướng kiến trúc hiện tại |
| [Artifact HTML](<Bản đồ SVG tương tác.html>) | Source của bản thử: đặt điểm, kéo, popup sửa/xem, ảnh và JSON | Đọc mã để xác định hiện trạng; không coi nội dung tab Tài liệu là đã đồng bộ toàn bộ |
| [Báo cáo nghiên cứu gốc](bao-cao-ban-do-svg-tuong-tac.md) | Quan sát Trident, thuật ngữ, các tuyến tái tạo và hướng post_content cũ | Giữ làm nguồn bối cảnh; phần kiến trúc cũ đã bị báo cáo tổng hợp thay thế |

Báo cáo tổng hợp còn dẫn `huong-nhe-ban-do-tuong-tac.md` và `bao-cao-fse-mega-menu.md`; hai file này chưa có trong thư mục ở lần digest này. Không giả định đã đọc được chúng.

Thứ tự ưu tiên cần phân biệt theo loại bằng chứng: báo cáo tổng hợp ghi **quyết định thiết kế**; artifact cho biết **source đang thực hiện gì**; screenshot/DOM/test thật mới chứng minh **behavior runtime**. Index ghi lại cả khoảng cách giữa ba loại này.

## 2. Baseline hiện tại

Đối tượng là bản đồ minh họa SVG với các điểm bấm mở thẻ thông tin, zoom theo bước. Tranh tạo ngữ cảnh; điểm giữ vị trí/icon/nhãn; nội dung chi tiết xuất hiện trong dialog. Desktop và mobile có thể trình bày cùng dữ liệu theo hai cách khác nhau.

Hướng hiện tại **theo báo cáo tổng hợp**:

```text
Page/template
  └── Block tham chiếu mapId
       └── Map trong bảng riêng
            └── Points: tọa độ, icon, nhãn, thứ tự
                 └── Popup: tiêu đề, nội dung, ảnh
```

- Page giữ `mapId`; PHP query dữ liệu và render bản đồ.
- Hai bảng đã chốt: `ii_maps`, `ii_map_points`, có tiền tố site. Nội dung popup nằm trong điểm; phương án ba bảng đã superseded.
- Không CPT bản đồ/điểm/popup; không markdown parser.
- Popup sửa ngay trong thẻ, có nháp và Lưu/Hủy; nội dung dự kiến lưu HTML, không còn là InnerBlocks theo hướng cũ.
- X/Y là phần trăm, tối đa hai chữ số thập phân, neo **tâm icon**.
- Icon production lưu preset key; SVG do code kiểm soát.
- Ảnh popup, artwork nền và icon marker là ba loại media khác nhau.
- Zoom hiện tại, điểm được chọn và dialog đang mở là trạng thái runtime, không lưu DB.

Đây là hướng thiết kế được tài liệu mới ghi là đã chốt; chưa có implementation WordPress hay runtime acceptance. Bối cảnh tài liệu nói plugin cho theme FSE, còn checkout hiện tại là GeneratePress child theme: repository không chuyển sang FSE. Scope bản đồ được lên lịch riêng 1.4.3, current vẫn 1.3.18. Cần xác định môi trường kiểm chứng khi lập execution plan.

## 3. Lịch sử digest và phần bị thay thế

| Giai đoạn | Nội dung | Trạng thái hiện tại |
|---|---|---|
| Nghiên cứu Trident | Bảy điểm, modal, selected state, zoom bước, không quan sát được pan/hover tooltip | Bối cảnh tham chiếu; DOM/zoom thực tế và mobile thật chưa xác minh |
| Hướng nhẹ ban đầu | Hai block cha–con; attributes + InnerBlocks trong post_content; tái sử dụng qua synced pattern | **Superseded về kiến trúc lưu trữ** bởi báo cáo tổng hợp 04/11 |
| Artifact ban đầu | Soạn qua sidebar, HTML marker trên SVG, JSON trao đổi | Có source; không phải công cụ sinh SVG hoàn chỉnh hoặc converter Gutenberg |
| Sửa neo và kéo | Hộp neo 44px, nhãn absolute, lấy lại node sau render, giữ grab offset | Đã có source; không tiếp tục ghi lỗi node cũ/neo theo nhãn như lỗi chưa sửa |
| Hướng bảng riêng | Page tham chiếu mapId, ba bảng, sort_order, nội dung popup HTML | Quyết định được ghi trong báo cáo; WordPress Chưa làm |
| Sửa ngay trong thẻ | Click không kéo mở sửa; ba trường tùy chọn; nháp Lưu/Hủy; thêm ảnh và JSON image | Đã có source artifact; chờ kiểm chứng bằng thao tác thật |

Rationale của thay đổi lưu trữ: ưu tiên query và dùng chung bản đồ thay cho ít code và các tiện ích nội dung WordPress có sẵn. Hướng post_content không bị loại vì sai; ưu tiên đã thay đổi. Giữ rationale này để tránh chat sau đề xuất lại hướng cũ như thể chưa từng được cân nhắc.

## 4. Quy ước ba lớp

1. **Giao diện:** trải nghiệm người xem, bố cục, marker, zoom, dialog, responsive và accessibility frontend; tương ứng Block UI trong review contract.
2. **Editor UI:** trải nghiệm người biên tập, chọn/đặt/quản lý điểm, sửa nội dung, nháp, Lưu/Hủy, preview và thông báo.
3. **Backend kỹ thuật:** schema, quyền sở hữu dữ liệu, render/API/lưu trữ, validation, media, migration, import/export và lifecycle kỹ thuật dùng chung.

Frontend runtime và focus vẫn được ghi tại lớp giao diện hoặc Editor UI theo surface bị ảnh hưởng; không coi mọi mã JavaScript là backend. Backend kỹ thuật ở đây là nhóm Technical mở rộng cho dữ liệu và tích hợp.

Các bảng sau dùng ID ổn định. “Đã có source” không có nghĩa PASS. Source references dùng tên hàm/selector trong artifact để tránh lệch số dòng sau cập nhật. Mọi runtime evidence trong chat này còn thiếu; chưa tạo runbook onsite vì lượt này chỉ tổng hợp tài liệu.

## 5. Lớp 1 — Giao diện

### 5.1. Hướng giao diện đang mô tả

- Tranh schematic mẫu 4:3; marker HTML đặt trên SVG, không nhúng thành nhóm g trong tranh.
- Hộp marker 44 × 44px; badge 36px; nhãn treo bên cạnh, không đổi tâm icon.
- Preview mở thẻ chỉ xem; không có ô nhập hay nút Lưu/Hủy.
- Thẻ gồm tiêu đề → nội dung → ảnh. Trường trống được ẩn; cả ba trống thì artifact hiện thông báo hỗ trợ.
- Thẻ tối đa 440px, padding 16px; ảnh tối đa 180px, mobile 160px; mobile dự kiến bottom sheet tối đa 75dvh.
- Zoom scale cả stage, tâm stage, bước nhân/chia 1,5, min 1, max 4; có Reset, không pan.
- Đích responsive trong báo cáo: dưới 640px chuyển cùng dữ liệu sang danh sách, ẩn nền/zoom.

### 5.2. Hiện trạng, việc còn thiếu và cách kiểm

| ID | Nhóm | Hành vi mong đợi | Hiện trạng | Việc cần sửa/làm rõ | Cách kiểm | Evidence hiện có |
|---|---|---|---|---|---|---|
| UI-01 | Giao diện | Đổi/ẩn nhãn không dời tâm icon | Đã có source sửa | Kiểm geometry thật, không mở lại lỗi cũ như chưa sửa | Đổi nhãn dài/ngắn/rỗng, qua breakpoint; so tâm badge với điểm nền | `.marker`, `.badge`, `.mlabel` |
| UI-02 | Giao diện | Zoom giữ tọa độ và truy cập được các điểm | Đã có source scale; khả năng truy cập chờ kiểm chứng | Điểm rìa có thể ra ngoài viewport khi zoom, không pan; chưa có giải pháp mới được chốt | Zoom 1–4, mở từng điểm rìa ở desktop và mobile | `applyZoom()`, `#stageViewport`, `updateMobileList()` |
| UI-03 | Giao diện | Marker mở được bằng chuột, touch và bàn phím | FAIL source: đường mở marker hiện dựa pointerdown | Bổ sung đường activation bàn phím khi giao fix | Tab tới marker, Enter/Space phải mở đúng thẻ | Handler `pointerdown`; chưa có click marker tương ứng |
| UI-04 | Giao diện | Dialog có tên đọc được và trả focus đúng nguồn mở | FAIL source / chờ kiểm runtime | aria-labelledby trỏ heading phần xem có thể bị ẩn; đóng luôn tìm marker thay vì dòng mở | Mở từ marker/list; title rỗng; kiểm accessibility tree và focus sau Close/ESC/backdrop | `#popup`, `openPopup()`, listener `close` |
| UI-05 | Giao diện | Preview desktop dùng bố cục phù hợp khi ẩn sidebar | Chờ kiểm chứng; source vẫn giữ grid hai cột | Kiểm khoảng trống cột 400px trước khi kết luận visual | Preview ở ≥1024px, đo rect grid và bản đồ | Grid workspace, `setMode()` |
| UI-06 | Giao diện | Mobile chuyển sang danh sách theo đặc tả | FAIL source so với đặc tả | Hiện chỉ thêm list dưới 1024px; nền và zoom còn hiện | 360/390/640/768/1024px, so với báo cáo §5.3 | `updateMobileList()`, CSS max-width 639px |
| UI-07 | Giao diện | Thẻ gọn, ẩn trường thiếu, sheet đúng kích thước | Đã có source một phần | Mobile vẫn bị max-width 440px và chừa hai bên; không đúng mô tả rộng 100%; title/body-only cần kiểm | Các tổ hợp thiếu/có từng trường, nội dung dài, ảnh dọc/ngang | CSS `dialog#popup`, `openPopup()` |
| UI-08 | Giao diện | Nhãn sát biên vẫn đọc được; không cắt icon/focus ngoài ý muốn | Chờ kiểm chứng | Nhãn absolute bên phải có thể bị viewport cắt; điểm 0/100% cũng cần kiểm | Bốn góc + giữa, nhãn dài, keyboard focus | `.mlabel`, `#stageViewport` |
| UI-09 | Giao diện | Reduced motion và hình học zoom có ý nghĩa rõ | Đã có source reduced motion | Badge/text vẫn scale cùng stage; “giữ px” không phải giữ kích thước nhìn thấy khi zoom; nấc zoom có làm tròn | Bật reduced motion; tăng rồi giảm zoom, quan sát readout/icon | CSS reduced-motion, handlers zoom |

## 6. Lớp 2 — Editor UI

### 6.1. Luồng đang có trong artifact

```text
Soạn
  ├── Nắm và kéo marker → cập nhật X/Y
  ├── Bấm marker không kéo / click dòng điểm → mở thẻ sửa
  │    └── Nháp: tiêu đề, nội dung, ảnh
  │         ├── Lưu → cập nhật điểm/JSON → đóng thẻ
  │         └── Hủy / Close / ESC / backdrop → bỏ nháp
  └── Sidebar còn lại → đường phụ ghi trực tiếp

Xem trước → bấm marker/list → thẻ chỉ xem
```

- Ngưỡng phân biệt click/kéo: vượt 5px ở một trong hai trục thì đánh dấu moved.
- Kéo giữ khoảng lệch lúc nắm, không ép tâm icon nhảy tới con trỏ. Source lấy lại marker sau selectPoint() để tránh cập nhật node đã bị gỡ.
- Khi kéo có class is-dragging, tăng z-index, scale badge 1,15 và tăng shadow; có reduced-motion fallback cho transition.
- Trường trống trong thẻ sửa hiện nút “+ Thêm…”; ảnh có Đổi/Xóa. Nháp chỉ ghi khi Lưu.
- Cả ba trường trống thì Lưu bị khóa. Sidebar vẫn ghi ngay, không thuộc giao dịch nháp trong thẻ.
- Thêm điểm dùng icon pin mặc định; duplicate sinh ID mới, dời +2% mỗi trục, copy nội dung và ảnh; xóa chọn điểm lân cận.
- Tên fallback: label → popup title → tên preset + thứ tự. Tên tự sinh có thể đổi khi thứ tự/nhóm icon đổi, không dùng làm ID bền vững.

### 6.2. Hiện trạng, việc còn thiếu và cách kiểm

| ID | Nhóm | Hành vi mong đợi | Hiện trạng | Việc cần sửa/làm rõ | Cách kiểm | Evidence hiện có |
|---|---|---|---|---|---|---|
| ED-01 | Editor UI | Kéo liên tục, không nhảy lúc nắm | Đã có source sửa; Chờ kiểm chứng | Thử con trỏ thật, ra ngoài khung rồi thả; không coi pointer capture đã PASS | Nắm tâm/mép/nhãn, kéo nhanh/chậm, cancel | `pointerdown`, `pointermove`, `endDrag`, dx/dy |
| ED-02 | Editor UI | Click mở sửa, kéo không mở sửa | Đã có source ngưỡng 5px | Movement nhỏ vẫn cập nhật X/Y; làm rõ có chấp nhận click rung nhẹ dời tọa độ | Rung ≤5px, kéo >5px, kéo ra rồi quay lại | moved, sx/sy trong drag |
| ED-03 | Editor UI | Chuột và Enter/Space trên dòng mở cùng thẻ sửa | FAIL source | Keydown của dòng chỉ selectPoint(), click mở openPopup() | Dùng click rồi dùng bàn phím trên cùng dòng | `renderList()` |
| ED-04 | Editor UI | Lưu/Hủy bảo toàn dữ liệu và focus | Đã có source draft; Chờ kiểm chứng | Save đóng dialog rồi refresh thay DOM; cần kiểm focus cuối cùng | Sửa từng trường, Lưu/Hủy/ESC, mở lại và kiểm JSON/focus | draft, `popupSave`, `cancelEdit()`, `close` |
| ED-05 | Editor UI | Chính sách thẻ rỗng nhất quán | Đã có source khóa Lưu | Không thể lưu thao tác xóa sạch thẻ đã có; cần quyết nếu có nhu cầu xóa nội dung hoàn toàn | Xóa hết 3 trường, thử Lưu; kiểm trạng thái điểm mới | `draftIsEmpty()`, `updateSaveState()` |
| ED-06 | Editor UI | Chọn ảnh không ghi sang nháp khác hoặc gây lỗi sau đóng | Chờ kiểm chứng; source có rủi ro lifecycle | FileReader.onload dùng biến draft hiện tại, chưa gắn phiên/ID hoặc kiểm null khi trả về | Chọn ảnh rồi đóng/đổi phiên sửa trước khi đọc xong | `popupImageFile` change và callback |
| ED-07 | Editor UI | Sửa title cập nhật tên fallback ngay | FAIL source ở đường sidebar | Handler title chỉ renderJson(), chưa cập nhật marker/list | Label rỗng, sửa title qua sidebar rồi xem tên/aria-label | Input `popupTitle`, `displayName()` |
| ED-08 | Editor UI | Phím mũi tên di chuyển liên tiếp và giữ focus | Chờ kiểm chứng; source thay node sau mỗi lần | refresh() dựng lại marker đang focus | Tab tới marker, nhiều Arrow liên tiếp, Shift+Arrow | Handler keydown marker |
| ED-09 | Editor UI | Duplicate/xóa có kết quả dễ nhận biết, trạng thái rỗng rõ | Đã có source một phần | Clamp ở 100% có thể làm bản sao chồng; xóa cuối chỉ giảm opacity form; chưa undo/reorder | Duplicate góc 100/100, xóa hết, đổi thứ tự nếu được giao bổ sung | `duplicatePoint()`, `deletePoint()`, `renderForm()` |
| ED-10 | Editor UI | Phân biệt nháp, lưu phiên và lưu bền | Đã có chú thích; Chờ kiểm UX | Sidebar ghi ngay; thẻ cần Lưu; JSON chỉ trong RAM, reload mất nếu chưa xuất | Sửa qua hai đường, Hủy, reload sau export/import | Sidebar, draft, panel JSON |

Artifact chưa chứng minh thao tác editor Gutenberg. Hướng sửa trong thẻ đã thay kỳ vọng dùng core InnerBlocks; cách tích hợp quản trị/block selector mapId vào WordPress vẫn Chưa làm.

## 7. Lớp 3 — Backend kỹ thuật

### 7.1. Hợp đồng dữ liệu dự kiến

| Thực thể | Trường chính | Ý nghĩa |
|---|---|---|
| Map | map_id, title, status, background_kind, background_media_id/background_svg, aspect_ratio, show_zoom, timestamps | Nguồn bản đồ dùng chung giữa các page |
| Point | point_id, map_id, public_code, sort_order, x/y, icon_key, label, detail_title, detail_body_html, detail_image_media_id | Một điểm thuộc một bản đồ, chứa luôn nội dung chi tiết |
<!-- Bảng popup riêng đã superseded bởi quyết định gộp nội dung vào điểm. -->

- public_code như pt_01 dùng trao đổi/ghép dữ liệu; point_id là khóa nội bộ. public_code chỉ unique trong map theo schema, chưa đủ làm DOM ID toàn trang.
- Nhiều page dùng cùng mapId sẽ dùng chung dữ liệu. Schema hiện không có thực thể địa điểm độc lập dùng chung giữa nhiều map.
- Dự kiến dbDelta + option schema version; bảng có FK cascade và CHECK trong SQL minh họa. Chưa có migration thực thi hoặc audit tương thích môi trường.
- Dữ liệu popup không còn hưởng revision/search/export chuẩn của post_content. Snapshot revision chưa chốt.
- Artifact dùng popup.image dạng data URL; bản WordPress dùng attachment ID. JSON không tự chuyển ảnh vào Media Library.
- JSON nền mới ghi kind/aspectRatio, chưa mang toàn bộ artwork. Import không tự phục hồi một tranh tùy ý.

### 7.2. Hiện trạng, việc còn thiếu và cách kiểm

| ID | Nhóm | Hành vi mong đợi | Hiện trạng | Việc cần sửa/làm rõ | Cách kiểm | Evidence hiện có |
|---|---|---|---|---|---|---|
| BE-01 | Backend kỹ thuật | Block mapId query/render được dữ liệu | Chưa làm trong WordPress | Xác định scope/version/môi trường trước implementation | Sau triển khai: nhúng hai page cùng map, sửa rồi đọc lại | Báo cáo tổng hợp §5, §8 |
| BE-02 | Backend kỹ thuật | Schema upgrade/xóa nhất quán | Chưa làm | Kiểm khả năng dbDelta/DB đích với FK/CHECK; không coi cascade đã vận hành từ SQL mẫu | Fresh install, upgrade, xóa map/point, kiểm orphan | SQL trong báo cáo §5.2 |
| BE-03 | Backend kỹ thuật | JSON export/import giữ dữ liệu hợp lệ | Đã có source một phần | Custom SVG xuất custom-svg nhưng không xuất payload; điểm thiếu icon bị bỏ; mảng rỗng xuất được nhưng import bị từ chối | Export→import các fixture rồi so dữ liệu | `exportJson()`, `applyImported()` |
| BE-04 | Backend kỹ thuật | Import lỗi không làm hỏng phiên hiện tại | FAIL source về validation thiếu | Kiểm type/version, object từng điểm, ID trùng, chuỗi, tọa độ trước khi thay state | JSON sai kiểu/null/trùng ID/unknown icon | `applyImported()`, `pointById()`, `.trim()` consumers |
| BE-05 | Backend kỹ thuật | Media ảnh và SVG có hợp đồng an toàn | Prototype có sanitizer blacklist; production Chưa làm | Không dùng sanitizer thử làm security boundary; chuẩn hóa media ID, loại/kích thước ảnh và dữ liệu SVG | Review upload/render path khi có implementation | `sanitizeSvg()`, FileReader, schema media |
| BE-06 | Backend kỹ thuật | CRUD có quyền, validation và trạng thái lưu rõ | Chưa làm | Capability/nonce hoặc quyền REST, sanitize/escape, trạng thái draft/published, lỗi lưu và tranh chấp sửa chưa có contract | Khi được giao build: thử quyền thiếu, request sai và lưu đồng thời | Báo cáo ghi phải tự làm quản trị/status; chưa có endpoints |
| BE-07 | Backend kỹ thuật | Nhiều instance không trùng ID hoặc mở nhầm dialog | Chưa làm | Prefix DOM ID theo instance/map/point; cùng map xuất hiện hai lần vẫn phải tách DOM ID | Hai map, cùng map hai lần trên một page | public_code unique theo map; frontend dự kiến §5.3 |
| BE-08 | Backend kỹ thuật | Cơ chế asset/render/fallback bền vững | Chưa làm trong WordPress | viewScript, lifecycle/cleanup, không JS, map thiếu/xóa/draft, lỗi query cần đặc tả | JS blocked, mapId không tồn tại, plugin inactive theo scope | Báo cáo §5.1/5.3; artifact chỉ là trang độc lập |
| BE-09 | Backend kỹ thuật | Tái sử dụng và rollback có giới hạn rõ | Quyết định bảng riêng đã ghi; revision mở | Page revision không khôi phục bảng; chưa có snapshot, search/export chuẩn | Chốt policy rồi test sửa map dùng chung và khôi phục | Báo cáo §5.4, §8 |

BE-02/05/06/08 là việc cần thiết kế/kiểm tra khi chuẩn bị implementation, không phải lỗi runtime đã quan sát hoặc quyết định thêm dependency. Chưa nghiên cứu lại tài liệu WordPress/DB đúng phiên bản trong lượt digest này.

## 8. Chênh lệch tài liệu cần giữ cờ

| ID | Chênh lệch | Cách hiểu tạm thời |
|---|---|---|
| DOC-01 | Artifact tab B/C và báo cáo gốc vẫn ghi không bảng riêng, hai block + InnerBlocks | Đã superseded về storage bởi báo cáo tổng hợp; chưa sửa nguồn trong task index này |
| DOC-02 | Một đoạn trong artifact nói Soạn bấm chỉ chọn điểm | Mã mới click không kéo mở thẻ sửa; dùng hành vi source mới để mô tả prototype |
| DOC-03 | Responsive mô tả mobile list <640px; mã thêm list <1024px và còn bản đồ | UI-06, chưa PASS |
| DOC-04 | Tài liệu có đoạn 85dvh, hướng mới là 75dvh trên mobile | CSS mới dùng 75dvh; cần đồng bộ văn bản cũ |
| DOC-05 | Báo cáo yêu cầu sheet mobile rộng 100%, source vẫn có max-width 440px và gutter | UI-07, chưa PASS |
| DOC-06 | Báo cáo gọi một số behavior là đã chứng minh, nhưng cũng ghi nợ thao tác con trỏ/file picker | Trong chat này chỉ có source review; chưa kế thừa kết luận PASS không kèm evidence |
| DOC-07 | Báo cáo nói popup kế thừa typography có thể đổi vì lên top layer | Chưa kiểm chứng; không dùng nhận định đó làm premise đã xác minh về DOM/inheritance; cần đo trên host thật |
| DOC-08 | Báo cáo dùng FSE; repo hiện là GeneratePress child theme | Chưa tự chuyển môi trường, namespace, plugin hoặc milestone |

Quan sát Trident cũng còn giới hạn: chưa xác minh markup thật, viewBox/transform, thư viện, mobile thật. Các khẳng định chắc chắn về nhóm SVG hoặc loại trừ mọi cách dựng khác vượt mức bằng chứng đang có; không dùng làm ràng buộc implementation nếu chưa kiểm tra thêm.

## 9. Điểm mở và bước trao đổi tiếp

Những mục tài liệu đã ghi còn mở:

- Giữ grab offset như hiện tại hay ép tâm icon về con trỏ khi nắm; hiện chỉ có phương án giữ offset trong source.
- Có cần snapshot revision cho bảng riêng không.
- Popup có cần List/Buttons ngoài ba trường hiện tại không.
- Version đã xác nhận 1.4.3; trước implementation cần execution plan xác định môi trường kiểm chứng và tích hợp plugin thực tế, cùng lệnh start riêng.

Các vấn đề cần lựa chọn khi giao scope tiếp: thẻ rỗng và xóa sạch nội dung (ED-05); đường tới mọi điểm khi zoom (UI-02); thứ tự điểm/undo (ED-09); hợp đồng nháp/lưu và dùng chung dữ liệu (ED-10, BE-09). Đây là danh sách mở, không phải quyết định mặc định thêm tính năng.

Ưu tiên trao đổi hợp lý: xác định môi trường và phạm vi → thống nhất source/tài liệu → chốt giao diện và Editor UI → hoàn thiện backend contract → lập acceptance/runbook. Trước onsite/runtime test phải tạo tài liệu dưới docs/testing theo AGENTS, ghi URL/setup/steps/expected/evidence; trước implementation mới phải đi qua quy tắc SemVer/start milestone của repo. Không cần chạy build/lint cho lượt Markdown này.

## 10. Prompt ngắn để nối chat

> Đọc docs/ideations/svg-maps/README.md trước, rồi mở báo cáo tổng hợp và artifact theo index. Tiếp tục từ baseline ngày 04/11/2026: hai bảng maps/map_points + block mapId, nội dung popup nằm trong điểm; Apply changes cập nhật nháp, Save map ở sidebar plugin lưu toàn bộ; đọc quyết định bổ sung đầu index trước các mô tả artifact cũ, tọa độ neo tâm icon. Phân tích theo ba lớp Giao diện / Editor UI / Backend kỹ thuật, dùng ID UI-*, ED-*, BE-* và DOC-* để giữ truy vết. Hướng post_content + InnerBlocks cũ đã superseded về kiến trúc lưu trữ. Chưa coi source artifact là runtime PASS hoặc plugin WordPress đã triển khai. Task tiếp theo: [điền yêu cầu].
