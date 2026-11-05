# Báo cáo tổng hợp: Bản đồ minh họa tương tác dạng SVG cho theme FSE

**Milestone đã xác nhận 2026-11-04:** V1 / **1.4.3 — Interactive Illustrated Map**, **PLANNED — Chưa làm, chưa start**, sau 1.4.2 trước 1.5.0. Human chọn 1.4.3 như ngoại lệ đánh số cho feature mới (thông thường MINOR); không đổi quy tắc chung. Current vẫn 1.3.18; chưa bump/build hoặc triển khai. Scope/acceptance MAP-* tại [MILESTONES.md](../../../.context/MILESTONES.md); các ID digest bên dưới giữ truy vết. Chưa gán stack M1–M6; không tự đóng debt map/contact cũ.

**Ngày tổng hợp:** 2026-11-04
**Phạm vi:** Dự án plugin cho theme FSE của user, case tham chiếu là mục “WILD-CAUGHT IN ALASKA” trên trang https://tridentseafoods.com/brands/trident-seafoods (truy cập và kiểm tra trực tiếp ngày 2026-11-03). Báo cáo này tổng hợp các phân tích, phản biện và quyết định đã chốt trong cuộc trao đổi từ 2026-11-03 đến 2026-11-04. Nó thay thế hướng nhẹ `post_content` đã ghi trước đó bằng hướng tách bảng riêng đã chốt ngày 2026-11-04, và ghi thêm cách sửa popup trong thẻ đã chốt cùng ngày.
**Lưu ý bản quyền:** Báo cáo chỉ mô tả hành vi và cấu trúc của bản đồ tham chiếu, không chép tranh gốc và không chép nguyên văn mô tả popup của Trident. Mọi tranh minh họa và nội dung popup trong bản dựng lại phải là tranh và nội dung gốc của dự án. Các mô tả popup mẫu trong báo cáo này là nội dung mẫu tự viết cho artifact thử, không phải nội dung của Trident.

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

Các mục bên dưới được giữ để truy vết báo cáo ban đầu: schema ba bảng ở §5.2 và mapping popup ở §5.5–6 đã superseded. Khi triển khai dùng hai bảng và detail_* theo quyết định bổ sung; SQL cũ không còn là schema đích. Lưu trong thẻ ở §6/§8 đã được thay bằng Apply changes → Save map. Artifact §7 vẫn là hiện trạng cũ, chưa áp dụng hợp đồng mới.

## 1. Các quyết định đã chốt, đọc trước

| # | Quyết định | Ý nghĩa thực tế |
|---|---|---|
| 1 | Đối tượng là bản đồ minh họa tương tác dạng SVG, kiểu điểm bấm mở popup, có zoom theo bước | Không phải image map HTML, không phải choropleth, không phải tile map (xem mục 2) |
| 2 | Dựng lại bằng plugin riêng cho theme FSE, user tự build custom block | Không dùng plugin PicPoints/MapSVG làm tuyến chính; các plugin đó chỉ là tuyến không code tham khảo |
| 3 | Không dùng CPT cho bản đồ, điểm hay popup | CPT và block là hai trục khác nhau; CPT thêm quan hệ và màn hình sửa mà case 7 điểm không cần (xem mục 4) |
| 4 | Không lưu popup dạng markdown | WordPress core không lưu/render markdown; popup dạng block core hoặc dạng bảng riêng mới là đường ít custom hơn (xem mục 4) |
| 5 | Tách bảng riêng cho dữ liệu bản đồ (chốt 2026-11-04) | Thay thế hướng chỉ dùng `post_content`; page chỉ giữ block tham chiếu `mapId` (xem mục 5) |
| 6 | Sửa popup ngay trong thẻ (chốt 2026-11-04) | Không làm màn hình form riêng tách khỏi bản đồ; thẻ vừa xem vừa sửa, kích thước nhỏ (xem mục 6) |
| 7 | Neo vị trí vào tâm icon, không neo vào tâm cả nhóm icon + nhãn | Nhãn dài không được làm icon lệch; lúc kéo, icon phải di chuyển theo con trỏ, không đứng yên (xem mục 7) |
| 8 | Artifact HTML Tailwind chỉ là công cụ thử riêng | Kéo thả trong artifact không chứng minh được kéo thả trong editor Gutenberg; artifact không phải là plugin WordPress (xem mục 7) |

Các tuyến đã bị bác và lý do ngắn gọn nằm ở mục 4. Phần plugin WordPress thật, theo hướng tách bảng, vẫn chưa được build tính đến thời điểm tổng hợp này; artifact thử là thứ duy nhất đã có bản chạy được.

## 2. Tên gọi đúng và checklist định nghĩa

Tên đầy đủ nên dùng: **“Bản đồ minh họa tương tác dạng SVG, kiểu điểm bấm mở popup, có zoom theo bước”**, tiếng Anh: **“interactive illustrated SVG map — marker-modal type, stepped zoom”**. Đây là tên mô tả, không phải thuật ngữ chuẩn duy nhất của W3C hay WordPress, vì không có một thuật ngữ chính thức duy nhất cho đúng dạng này.

Checklist 7 điểm để nhận dạng đúng đối tượng, và cũng là tiêu chí nghiệm thu hành vi:

1. Tranh là vector minh họa tự vẽ, không phải ảnh raster chụp thật và không phải bản đồ địa lý chính xác theo kinh/vĩ độ.
2. Tranh là SVG, không phải `<img>` raster, không phải canvas, không phải tile.
3. Marker là hình minh họa gắn trên tranh (nhà xưởng, tàu, tháp, dấu chân, cá), không phải pin mặc định của thư viện bản đồ.
4. Bấm marker thì mở popup/modal chứa nội dung, không điều hướng sang trang khác, không lọc danh sách, không đổi trạng thái bản đồ.
5. Vùng địa lý trong tranh không bấm được; chỉ marker mới bấm được.
6. Zoom là zoom theo bước bằng nút Tăng/Giảm, không phải pinch/scroll zoom liên tục, và trong bản gốc không có kéo pan.
7. Vị trí marker là vị trí schematic trên tranh, không phải tọa độ lat/long.

Các tên đã bị loại: “bản đồ SVG” (quá chung, không nói được kiểu tương tác); “image map” (trong HTML nghĩa là `<map>`/`<area>` với tọa độ pixel cố định, sai kiến trúc); choropleth/bản đồ vùng (vùng không bấm được và không tô màu theo dữ liệu); Google/Leaflet/Mapbox tile map (không có tile, lat/long hay attribution); “hotspot map / interactive image map” chỉ dùng được làm tên phụ, vì trong hệ plugin WordPress nó thường nghĩa là ghim điểm lên ảnh raster.

## 3. Phân tích bản đồ tham chiếu Trident

### 3.1. Những gì đã quan sát được

Trang tham chiếu không có heading menu/table of contents trên trang; phần liên quan ở đây là mục “WILD-CAUGHT IN ALASKA” và bản đồ trong mục đó. Các quan sát dưới đây đến từ hai lần kiểm tra trực tiếp bằng trình duyệt ngày 2026-11-03, ở viewport desktop, không đăng nhập, không mua hàng.

- Bản đồ gần như chắc chắn là SVG inline trong DOM, không phải `<img>`, canvas hay thư viện tile. Bằng chứng: nhãn vùng như ARCTIC OCEAN, BEAUFORT SEA, BERING SEA, NORTON SOUND, BRISTOL BAY, GULF OF ALASKA, ALASKA, CANADA hiện thành text thật trong cây accessibility; tranh vẫn sắc nét khi zoom sâu; từng marker bấm được riêng; không thấy tile, attribution hay giao diện thư viện bản đồ.
- Có 7 marker bấm được. Chỉ ghi tên và dạng icon, không chép mô tả popup: 1. St. Paul Plant (nhà xưởng), 2. Cordova Plant (nhà xưởng), 3. North Naknek Plant (nhà xưởng), 4. The Billikin (tàu), 5. Anchorage Support Center (tháp radio), 6. Katmai National Park (gấu), 7. Prince William Sound (cá). Thứ tự liệt kê ở đây là thứ tự ghi chú, không phải thứ tự bắt buộc trong bản dựng lại.
- Tương tác: hover không hiện tooltip; bấm mở modal gồm ảnh, tiêu đề, một dòng species/tagline và mô tả, có nút Close, không có link, không điều hướng, không lọc. Sau khi đóng, marker vừa bấm giữ trạng thái “selected” được highlight.
- Zoom: chỉ có nút Zoom In và Zoom Out ở mép phải. Mỗi lần bấm là một bước rời rạc, phóng to toàn bộ SVG (tranh, nhãn và marker cùng nhau) khoảng 1,5–2 lần, lấy tâm viewport làm tâm phóng. Không có kéo pan. Không tìm thấy giới hạn zoom trên sau 6 lần zoom liên tiếp; mức tối thiểu là khung nhìn đầy đủ ban đầu; không thấy hiệu ứng chuyển động rõ ràng.
- Không có WordPress/CMS nào được quan sát trên site Trident (không có `/wp-content/`, không có generator meta, ảnh từ CDN Bynder `cdn.bfldr.com`, có Cookiebot). Vì vậy mọi tuyến “chuyển đổi” đều là dựng lại trong Gutenberg, không phải migration/export.

### 3.2. Những gì chưa xác minh được, phải giữ cờ

- Markup SVG thật, viewBox và cơ chế zoom thật (viewBox hay CSS transform) chưa xác minh được do giới hạn công cụ kiểm tra.
- Hành vi ở viewport mobile thật chưa xác minh (chỉ mô phỏng bằng cách zoom trang: bản đồ lấp đầy container, tranh bị cắt kiểu cover, nút zoom vẫn dùng được).
- Hành vi thực tế của các plugin mega menu và plugin bản đồ được nêu trong các báo cáo riêng chưa được tự cài và tự test trong cuộc trao đổi này; các nhận xét về chúng là từ tài liệu nhà cung cấp và review người dùng, đã được ghi rõ mức độ tin cậy trong báo cáo riêng.
- Hai snapshot thoáng qua ban đầu có nhắc tới các nút vùng Canada / Alaska / Seattle / Minnesota / Maine đã được đính chính: các nút đó không tồn tại trên trang này; ALASKA và CANADA chỉ là nhãn tranh không tương tác.

### 3.3. Mega menu: ghi chú ngắn, không phải trọng tâm của báo cáo này

Cùng ngày 2026-11-03, user cũng hỏi về mega menu cho FSE, lấy cảm hứng từ panel Products của header Trident (3 cột text: Products, Food Service, Capabilities). Kết luận và bảng so sánh đầy đủ nằm trong báo cáo riêng `bao-cao-fse-mega-menu.md`, không lặp lại ở đây. Ghi chú duy nhất cần giữ trong báo cáo tổng hợp này là: block Navigation core của WordPress hỗ trợ submenu và mobile overlay nhưng không có panel mega nhiều cột chứa block tùy ý; Getwid Mega Menu, Gutenverse PRO, Nexter Blocks, GutenKit Pro thuộc họ block-FSE-native, còn Max Mega Menu thuộc họ classic-menu thích ứng sang FSE. Báo cáo này không chốt chọn plugin mega menu nào.

## 4. Các phản biện và tuyến đã bị bác

### 4.1. Lưu dạng Custom Post Type thay cho block?

Về mặt kỹ thuật thì được, nhưng CPT và block là hai trục khác nhau: CPT quyết định dữ liệu thuộc post nào, query và trạng thái ra sao; block quyết định sửa và render thế nào trong một post. Một post CPT vẫn có thể chứa block trong `post_content` của nó, nên “thay block bằng CPT” không phải là một phép thay thế một-một. Ba thiết kế đã được tách ra và phân tích:

- Thiết kế A, 1 CPT = 1 bản đồ, điểm lưu meta JSON: tái sử dụng theo ID và có draft/publish riêng, nhưng nội dung popup trong meta JSON mất sửa bằng core, mất search mặc định (search chỉ quét `post_title`/`post_content`, không quét meta) và mất revision tự động; giao diện sửa điểm vẫn phải tự xây, chỉ là dời nó sang màn hình CPT.
- Thiết kế B, 1 CPT = 1 điểm, block bản đồ query điểm: đây là thiết kế duy nhất trong ba thiết kế giữ được sửa bằng core cho nội dung popup (qua `post_content` của điểm) kèm tái sử dụng, search, lọc và REST thật. Nhưng tọa độ `x`/`y` là thuộc tính của “điểm trên một bản đồ/tranh cụ thể”, không phải thuộc tính của điểm, nên tái sử dụng qua nhiều bản đồ cần thêm một lớp quan hệ riêng cho từng bản đồ; sửa bị vỡ vụn qua nhiều màn hình; điểm dạng public sẽ sinh ra các trang single mỏng và sitemap không mong muốn nếu không đặt `public=false` kèm `show_ui=true`; cần quyết orphan và thứ tự (`menu_order` hoặc meta order).
- Thiết kế C, giữ block nhưng lưu dưới dạng synced pattern: pattern đồng bộ vốn đã lưu dưới dạng CPT `wp_block` ở phía dưới, nên đã có tái sử dụng mà không cần đăng ký CPT riêng, và InnerBlocks được giữ nguyên.

Kết luận lúc đó, và vẫn đúng cho case 7 điểm, 1 tranh, 1 page: CPT là over-engineering nếu chưa có nhu cầu tái sử dụng, query hay API thật. Hướng tách bảng riêng chốt sau đó (mục 5) giải quyết đúng nhu cầu query/tái sử dụng đó, nhưng bằng bảng riêng chứ không bằng CPT, và chấp nhận mất revision/search mặc định của `post_content` để đổi lấy khả năng query.

### 4.2. Popup dạng CPT, nội dung dạng “mini markdown”, không style nổi bật?

Đề xuất này trộn ba thứ khác nhau vào một tên, và chính chỗ trộn đó là chỗ gây lỗi. Popup không phải là thực thể, nó chỉ là một cách hiển thị; thực thể đằng sau nó, nếu có, phải là địa điểm/nội dung chi tiết, còn popup chỉ là dialog mở ra để xem nội dung đó. Đặt tên CPT là “popup” sẽ làm người đọc code tưởng CPT đó chỉ tồn tại để phục vụ dialog.

Về “mini markdown”: WordPress core không lưu và không render markdown như một định dạng nội dung. Gutenberg khi gõ markdown thì chuyển ngay thành block HTML, không lưu lại dạng markdown. Vì vậy “trang mini markdown” trong WordPress chỉ có thể là một trong hai thứ, và hai thứ này khác nhau hoàn toàn: (a) nội dung đơn giản chỉ có tiêu đề, đoạn văn và ảnh, không style cầu kỳ — thực ra đó là block core bình thường dùng ít block và template tối giản, không cần thêm gì, vẫn có media library, srcset, caption và alt; (b) lưu thật dạng markdown trong DB, ví dụ trong meta hoặc trong `post_content` dạng text — khi đó phải tự thêm parser, tự làm editor dạng textarea, tự xử lý chèn ảnh bằng URL thay vì chọn từ media library, tự lo srcset/caption/alt, tự sanitize HTML trong markdown và tự xử lý revision/search vì meta markdown không được search và không có revision mặc định như `post_content`. Nói cách khác, “mini markdown” nghe thì đơn giản nhưng trong hệ WordPress lại là đường custom nhiều hơn, không phải đường đơn giản hơn.

Về “không có style nổi bật”: nội dung popup nên kế thừa typography của theme, không làm card màu mè hay shadow nặng — điểm này đúng và được giữ. Nhưng dialog vẫn bắt buộc phải có style chức năng, nếu không sẽ hỏng về mặt sử dụng: chiều rộng tối đa, padding, ảnh không tràn (`max-width: 100%`), nội dung dài phải cuộn được trong dialog, nút Close phải thấy rõ và đủ 44px, backdrop phải phân biệt được với nền, và focus outline phải thấy được khi dùng bàn phím. Ngoài ra còn một chi tiết cần test: dialog khi mở sẽ lên top layer, nếu style của theme chỉ áp cho nội dung trong `.wp-block-post-content` thì nội dung trong dialog có thể không ăn đúng typography đó, vì dialog không nằm trong container đó. Vì vậy “không style nổi bật” phải hiểu là “không thêm style trang trí, chỉ giữ style chức năng và kế thừa typography của theme, và phải test typography trong dialog thật”, chứ không phải “để mặc định trình duyệt”.

### 4.3. Hướng nhẹ `post_content` đã từng được chốt, rồi bị thay thế

Trước quyết định tách bảng, hướng nhẹ đã được chốt là: một plugin, hai block, dữ liệu nằm trong `post_content` của page, không CPT, không markdown parser, không REST fetch popup, không kéo thả trong editor WordPress. Hướng đó có ưu điểm thật: sửa nội dung thì mở đúng page đó sửa như block thường, sửa icon thì thêm một key và một đoạn SVG path vào mảng PHP, sửa vị trí thì sửa hai con số, lỗi chỉ có thể nằm ở render PHP, view JS hoặc CSS, và revision/search/export của WordPress được miễn phí. Hướng đó vẫn là tuyến đúng nếu ưu tiên cao nhất là ít code và ít màn hình quản trị. Nó bị thay thế không phải vì nó sai, mà vì user chốt ưu tiên khác cao hơn: tách bảng riêng để query và tái sử dụng cho tiện (mục 5). Báo cáo này ghi rõ sự thay thế đó để lần sau không ai đọc lại mục hướng nhẹ cũ mà tưởng nó vẫn là quyết định hiện hành.

## 5. Hướng đã chốt hiện tại: tách bảng riêng

### 5.1. Kiến trúc tổng thể

Page/template chỉ giữ một block tham chiếu, không giữ điểm. Ví dụ `post_content` của page chỉ còn:

```html
<!-- wp:your-namespace/interactive-illustrated-map {"mapId":1} /-->
```

Lúc hiển thị, `render_callback` của block đọc `mapId`, query 3 bảng dưới, rồi render ra khung, marker, dialog và nút zoom. JS dialog/zoom, CSS responsive và danh sách mobile giữ nguyên như mô tả ở các mục trước. Thay `your-namespace` bằng namespace thật của plugin khi build.

Việc tạo bảng dùng `dbDelta` và version của schema lưu trong option của plugin, để nâng cấp schema sau này có migration rõ ràng thay vì tạo bảng thủ công bằng SQL ngoài.

### 5.2. Schema chốt

Tên bảng đã gắn tiền tố bảng của site (`$wpdb->prefix`); ví dụ dưới dùng tiền tố `wp_`.

```sql
CREATE TABLE wp_ii_maps (
  map_id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  title               VARCHAR(255) NULL,
  status              VARCHAR(20) NOT NULL DEFAULT 'draft',
  background_kind     VARCHAR(32) NOT NULL DEFAULT 'attachment',
  background_media_id BIGINT UNSIGNED NULL,
  background_svg      MEDIUMTEXT NULL,
  aspect_ratio        VARCHAR(10) NOT NULL DEFAULT '4 / 3',
  show_zoom           TINYINT(1) NOT NULL DEFAULT 1,
  created_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (map_id)
);

CREATE TABLE wp_ii_map_points (
  point_id    BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  map_id      BIGINT UNSIGNED NOT NULL,
  public_code VARCHAR(32) NOT NULL,
  sort_order  INT NOT NULL DEFAULT 0,
  x           DECIMAL(5,2) NOT NULL,
  y           DECIMAL(5,2) NOT NULL,
  icon_key    VARCHAR(32) NOT NULL,
  label       VARCHAR(255) NULL,
  PRIMARY KEY (point_id),
  UNIQUE KEY uk_map_point_code (map_id, public_code),
  KEY idx_map_order (map_id, sort_order),
  KEY idx_icon (icon_key),
  CONSTRAINT fk_point_map FOREIGN KEY (map_id)
    REFERENCES wp_ii_maps (map_id) ON DELETE CASCADE,
  CONSTRAINT chk_point_xy CHECK (x BETWEEN 0 AND 100 AND y BETWEEN 0 AND 100)
);

CREATE TABLE wp_ii_point_popups (
  popup_id       BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  point_id       BIGINT UNSIGNED NOT NULL,
  title          VARCHAR(255) NOT NULL,
  body_html      MEDIUMTEXT NOT NULL,
  image_media_id BIGINT UNSIGNED NULL,
  PRIMARY KEY (popup_id),
  UNIQUE KEY uk_popup_point (point_id),
  CONSTRAINT fk_popup_point FOREIGN KEY (point_id)
    REFERENCES wp_ii_map_points (point_id) ON DELETE CASCADE
);
```

Các quy ước giữ nguyên qua mọi lần phản biện:

- `x`/`y` là % với tối đa 2 số thập phân, neo là tâm icon (xem mục 7), không phải px, không phải tọa độ lat/long.
- `icon_key` chỉ lưu key như `barn`, `ship`, `tower`, `paw`, `fish`, `factory`; SVG do code whitelist, tuyệt đối không render SVG thô từ dữ liệu người nhập. Nếu sau này cho phép icon upload thì lưu `mediaId` riêng và render bằng `<img>` trang trí, không phá quy tắc key.
- `label` rỗng lưu NULL và lúc render fallback sang tiêu đề popup, để nút không bao giờ mất tên đọc được (`aria-label`).
- `public_code` là `pt_01`, `pt_02`... để nối nút với dialog và để xuất/nhập JSON; `point_id` số tự tăng chỉ dùng nội bộ cho khóa ngoại.
- `sort_order` là thứ tự điểm, thay cho thứ tự block con trong `post_content` ở hướng cũ. Thứ tự này cũng là thứ tự tab và thứ tự danh sách trên mobile.
- Nền inline schematic trong JSON artifact map sang `background_kind = 'inline-svg-schematic'` và `background_svg` chứa mã SVG đó; nền là ảnh upload thì map sang `background_kind = 'attachment'` và `background_media_id`. Tỉ lệ khung mặc định `4 / 3` lấy theo tranh schematic mẫu; tranh thật sau này lấy tỉ lệ theo tranh thật.
- Mức zoom hiện tại, điểm đang chọn và dialog đang mở vẫn không lưu vào bảng nào; chúng chỉ là trạng thái lúc xem trong trình duyệt.
- Xóa bản đồ thì điểm và popup đi theo nhờ `ON DELETE CASCADE`, không còn post mồ côi như trường hợp tách CPT trước đó.

### 5.3. Frontend: mở popup và zoom

Mỗi điểm, PHP render ra hai phần nối với nhau bằng `public_code`:

- Một `<button>` có `data-popup` trỏ tới id của dialog, `aria-haspopup="dialog"`, `aria-expanded`, chứa SVG icon theo key và `<span>` nhãn. Nhãn trên nút không phải là Heading block, vì trong `<button>` không được chứa `<h2>` hay `<p>` theo chuẩn HTML.
- Một `<dialog>` có `aria-label` bằng tên điểm, chứa nút Close do PHP render và nội dung popup bên trong. Nút Close và vỏ dialog không nằm trong dữ liệu popup, để người sửa không xóa nhầm.

JS khai báo bằng `viewScript` trong `block.json` của block cha, để WordPress chỉ nạp khi trang thật sự có block. JS chỉ làm ba việc, bằng một listener ủy quyền ở block cha, không gắn `onclick` từng marker và không dán `<script>` vào Custom HTML block trong post:

- Bấm nút marker thì tìm dialog theo `data-popup`, gọi `showModal()`, đặt `aria-expanded="true"`, thêm class `is-selected` cho nút đó.
- Đóng bằng nút Close, bấm backdrop hoặc ESC; khi đóng thì trả focus về đúng nút vừa bấm.
- Bấm Zoom In/Out thì đổi `transform: scale()` của wrapper chứa cả nền và marker, theo bước 1,5, kẹp trong [1, 4]. Nền và marker nằm chung wrapper nên chân marker không lệch khi zoom, với điều kiện tọa độ lưu bằng % và khung khóa `aspect-ratio` đúng tỉ lệ tranh (không dùng nền `object-fit: cover` với khung cố định chiều cao, vì khi đó % của khung không còn là % của tranh).

Responsive bằng CSS media query, không dùng JS đo viewport, không dùng attribute ẩn theo breakpoint, không ẩn block trên mobile theo mặc định. Desktop và tablet giữ dạng bản đồ; mobile dưới 640px chuyển cùng DOM sang dạng danh sách (ẩn nền và nút zoom, mỗi điểm thành một hàng icon + nhãn, bấm vẫn mở đúng dialog cũ), dialog trên mobile thành bottom sheet. Hover không được mang thông tin, vì thiết bị cảm ứng không có hover và bản gốc cũng không có hover tooltip.

### 5.4. Được gì và mất gì khi tách bảng

Được: query điểm theo bản đồ, icon, vị trí và thứ tự mà không cần parse `post_content`; tái sử dụng bản đồ ở nhiều page chỉ bằng cách đổi `mapId`; xóa cascade không còn orphan; xuất/nhập JSON artifact map 1-1 sang bảng (xem mục 5.5); dữ liệu có cấu trúc rõ ràng cho API sau này nếu cần.

Mất: revision, search mặc định và export chuẩn của `post_content` không còn áp dụng cho dữ liệu trong 3 bảng này; sửa popup không còn bằng InnerBlocks trong Gutenberg mà phải sửa trong thẻ theo mục 6; phải tự làm migration schema, màn hình quản trị và logic trạng thái `draft`/`published` cho bản đồ. Nếu sau này cần revision thì phải làm thêm bảng snapshot riêng, không có sẵn như trước. Đây là cái giá đã được nêu rõ trước khi user chốt tách bảng, không phải là phát hiện mới sau khi chốt.

### 5.5. Dữ liệu mẫu: JSON artifact và 3 bảng tương ứng

Dữ liệu mẫu dưới đây là JSON artifact thử do user cung cấp để biểu diễn thử, đã được dùng trong các turn trước. Tọa độ là tọa độ hiện tại trong JSON đó, không phải tọa độ đo từ tranh Trident và không phải tọa độ mẫu cũ trong các bản nháp đầu.

JSON gốc (rút gọn phần background, giữ nguyên 7 điểm):

```json
{
  "version": 1,
  "type": "interactive-illustrated-map",
  "background": { "kind": "inline-svg-schematic", "aspectRatio": "4 / 3" },
  "points": [
    { "id": "pt_01", "x": 30.74, "y": 37.97, "icon": "barn", "label": "St. Paul Plant", "popup": { "title": "St. Paul Plant", "text": "Nhà máy chế biến trên đảo nhỏ giữa Bering Sea. Điểm mẫu: icon preset “barn”, nhãn hiển thị đầy đủ." } },
    { "id": "pt_02", "x": 30.44, "y": 61.73, "icon": "ship", "label": "The Billikin", "popup": { "title": "The Billikin", "text": "Tàu hỗ trợ đội tàu đánh bắt. Điểm mẫu để thử kéo–thả trên vùng biển của tranh schematic." } },
    { "id": "pt_03", "x": 51.28, "y": 24.16, "icon": "tower", "label": "Anchorage Support Center", "popup": { "title": "Anchorage Support Center", "text": "Trung tâm hỗ trợ trên đất liền. Trong bản WordPress, nội dung này là InnerBlocks Heading + Paragraph + Image." } },
    { "id": "pt_04", "x": 52.96, "y": 82.15, "icon": "paw", "label": "Katmai National Park", "popup": { "title": "Katmai National Park", "text": "Vườn quốc gia — điểm mẫu dùng icon dấu chân gấu (preset “paw”)." } },
    { "id": "pt_05", "x": 74, "y": 74, "icon": "fish", "label": "", "popup": { "title": "Prince William Sound", "text": "Điểm mẫu cố ý để trống nhãn hiển thị: định danh của điểm lấy từ tiêu đề popup này, nhãn ẩn trên bản đồ nhưng nút vẫn có aria-label đầy đủ." } },
    { "id": "pt_06", "x": 86, "y": 60, "icon": "factory", "label": "Cordova Plant", "popup": { "title": "Cordova Plant", "text": "Nhà máy ở phía đông Prince William Sound — một trong 7 điểm của case Trident." } },
    { "id": "pt_07", "x": 35.84, "y": 73.95, "icon": "factory", "label": "North Naknek Plant", "popup": { "title": "North Naknek Plant", "text": "Nhà máy bên Bristol Bay — điểm thứ 7 của case Trident, bổ sung để đủ bộ điểm mẫu." } }
  ]
}
```

Mapping JSON sang bảng: `id` → `public_code`, `x`/`y` → `x`/`y`, `icon` → `icon_key`, `label` rỗng → NULL, `popup.title` → `point_popups.title`, `popup.text` → `point_popups.body_html` dạng `<p>...</p>`. `version` và `type` không thành cột riêng. `background.kind` và `aspectRatio` map sang `background_kind` và `aspect_ratio` của bảng `ii_maps`.

Bảng `ii_maps` (ID nội bộ 1 là gán minh họa theo thứ tự, JSON không có ID số):

| map_id | title | background_media_id | background_kind | aspect_ratio | show_zoom |
|---:|---|---|---|---:|---:|
| 1 | NULL | NULL | inline-svg-schematic | 4 / 3 | 1 |

Bảng `ii_map_points`:

| point_id | map_id | public_code | sort_order | x | y | icon_key | label |
|---:|---:|---|---:|---:|---:|---|---|
| 1 | 1 | pt_01 | 0 | 30.74 | 37.97 | barn | St. Paul Plant |
| 2 | 1 | pt_02 | 1 | 30.44 | 61.73 | ship | The Billikin |
| 3 | 1 | pt_03 | 2 | 51.28 | 24.16 | tower | Anchorage Support Center |
| 4 | 1 | pt_04 | 3 | 52.96 | 82.15 | paw | Katmai National Park |
| 5 | 1 | pt_05 | 4 | 74.00 | 74.00 | fish | NULL |
| 6 | 1 | pt_06 | 5 | 86.00 | 60.00 | factory | Cordova Plant |
| 7 | 1 | pt_07 | 6 | 35.84 | 73.95 | factory | North Naknek Plant |

Bảng `ii_point_popups` (cột `body_html` trong bảng thật là mỗi đoạn text bọc thành `<p>...</p>`, dưới đây ghi text để đọc cho gọn):

| popup_id | point_id | title | text |
|---:|---:|---|---|
| 1 | 1 | St. Paul Plant | Nhà máy chế biến trên đảo nhỏ giữa Bering Sea. Điểm mẫu: icon preset “barn”, nhãn hiển thị đầy đủ. |
| 2 | 2 | The Billikin | Tàu hỗ trợ đội tàu đánh bắt. Điểm mẫu để thử kéo–thả trên vùng biển của tranh schematic. |
| 3 | 3 | Anchorage Support Center | Trung tâm hỗ trợ trên đất liền. Trong bản WordPress, nội dung này là InnerBlocks Heading + Paragraph + Image. |
| 4 | 4 | Katmai National Park | Vườn quốc gia — điểm mẫu dùng icon dấu chân gấu (preset “paw”). |
| 5 | 5 | Prince William Sound | Điểm mẫu cố ý để trống nhãn hiển thị: định danh của điểm lấy từ tiêu đề popup này, nhãn ẩn trên bản đồ nhưng nút vẫn có aria-label đầy đủ. |
| 6 | 6 | Cordova Plant | Nhà máy ở phía đông Prince William Sound — một trong 7 điểm của case Trident. |
| 7 | 7 | North Naknek Plant | Nhà máy bên Bristol Bay — điểm thứ 7 của case Trident, bổ sung để đủ bộ điểm mẫu. |

## 6. Sửa popup ngay trong thẻ (chốt 2026-11-04)

Nguyên tắc: thẻ popup vừa là nơi xem, vừa là nơi sửa, không nhảy sang một form khác, không làm màn hình quản trị riêng tách khỏi bản đồ.

Luồng bấm:

- Chế độ Soạn: bấm marker trên bản đồ, hoặc bấm dòng điểm trong danh sách, thì thẻ thông tin của điểm đó mở ra ở trạng thái sửa ngay trong thẻ. Không qua bước xem trước rồi mới bấm nút Sửa.
- Chế độ Xem trước: bấm marker thì thẻ mở ở trạng thái chỉ xem, không có ô nhập, không có nút Lưu/Hủy, không có nút đổi ảnh.
- Đóng thẻ bằng nút Close, backdrop hoặc ESC như cũ; focus trả về đúng marker hoặc dòng vừa bấm.

Ba trường trong thẻ, theo đúng thứ tự hiển thị: heading (map sang `point_popups.title`), nội dung (map sang `point_popups.body_html` dạng `<p>...</p>`), ảnh đại diện (map sang `point_popups.image_media_id`). Ảnh này là ảnh trong thẻ popup, không phải tranh nền và không phải icon marker.

Quy tắc “cái nào không có thì không show”, áp cho trạng thái xem:

- Không có heading thì không render thẻ heading nào cả, không để một dòng trống hay tiêu đề placeholder chiếm chỗ.
- Không có nội dung thì không render đoạn nội dung nào cả.
- Không có ảnh đại diện thì không render khung ảnh, không render hộp placeholder, không render ảnh vỡ. Thẻ chỉ còn heading và/hoặc nội dung.
- Ngoại lệ duy nhất: trong trạng thái sửa, trường đang trống vẫn cần một lối để thêm vào, nhưng không phải bằng một hộp trống to. Cách làm là một nút nhỏ dạng “+ Thêm tiêu đề” hoặc “+ Thêm ảnh đại diện” đặt đúng chỗ trường đó sẽ hiện, bấm vào mới hiện ô nhập hoặc trình chọn ảnh. Khi đã có ảnh thì nút đó đổi thành hai nút nhỏ “Đổi ảnh” và “Xóa ảnh”.

Kích thước thẻ giữ nhỏ, thay cho dialog rộng khoảng 640px trước đây:

- Desktop và tablet: chiều rộng thẻ là `min(440px, 100vw - 32px)`, padding 16px, khoảng cách giữa các phần 10px.
- Ảnh đại diện: rộng 100% thẻ, cao tối đa 180px, `object-fit: cover`, bo góc 10px.
- Heading: cỡ khoảng 17px, line-height 1.3. Nội dung: cỡ khoảng 14px, line-height 1.55.
- Mobile: thẻ vẫn là bottom sheet rộng 100%, nhưng chiều cao tối đa giảm từ khoảng `85dvh` xuống `75dvh`, padding vẫn 16px, ảnh tối đa 160px.
- Thẻ sửa dùng đúng kích thước thẻ xem, không phình to thêm vì chuyển sang chế độ sửa, để người sửa không bị nhảy bố cục. Ô nhập heading dùng đúng cỡ chữ của heading, ô nội dung là textarea tự cao tối thiểu 3 dòng, ảnh xem trước trong lúc sửa dùng đúng khung ảnh đã nêu.

Lưu và hủy trong thẻ sửa:

- Có hai nút nhỏ ở cuối thẻ là Lưu và Hủy, không tự lưu theo từng ký tự gõ. Bấm Lưu mới ghi vào bảng (hướng tách bảng) hoặc vào dữ liệu phiên/JSON (artifact thử). Bấm Hủy thì thẻ đóng và mọi thay đổi chưa lưu bị bỏ, lần mở sau hiện lại dữ liệu cũ.
- Lưu xong thì thẻ đóng lại và quay về trạng thái xem với dữ liệu mới, marker và dòng danh sách cập nhật theo nếu heading hoặc ảnh thay đổi. Không lưu được khi cả ba trường đều trống, vì khi đó thẻ xem sẽ không còn gì để show; trong trường hợp đó nút Lưu giữ trạng thái vô hiệu và hiện một dòng nhắc ngắn trong thẻ, không hiện hộp cảnh báo toàn trang.

## 7. Artifact thử: đã làm gì, chứng minh được gì và chưa chứng minh được gì

Artifact thử là trang HTML Tailwind, slug `ban-do-svg-tuong-tac`, gồm: tranh schematic Alaska tự vẽ khóa tỉ lệ 4:3 với 7 điểm mẫu; Editor UI có danh sách điểm và nút Thêm, chọn hai chiều list ↔ marker, kéo–thả vị trí kèm nhập số X/Y và phím mũi tên làm đường dự phòng, duplicate (id mới + dời +2% + copy popup), xóa điểm; icon preset dạng key là bắt buộc (thiếu icon thì điểm bị loại khỏi JSON xuất ra); nhãn tùy chọn với định danh dự phòng theo thứ tự nhãn → tiêu đề popup → tên tự sinh từ icon; ô dán SVG chỉ để thử hình và được làm sạch bằng DOMParser (gỡ `script`/`foreignObject`, thuộc tính `on*` và liên kết ngoài); chế độ Xem trước tách riêng (bấm marker mở popup dialog/bottom sheet, zoom theo bước phóng cả stage); panel JSON sống với Sao chép / Tải file / Nhập (file hoặc dán); tab Tài liệu chứa phản biện và hướng phát triển. Dữ liệu sửa trong bản thử chỉ nằm trong phiên trang, không lưu bền sang lần mở khác — mang đi nơi khác bằng Xuất/Nhập JSON.

Hai lần sửa artifact đã được áp dụng và cần ghi rõ để không hiểu nhầm là tính năng gốc:

- Sửa neo vị trí (sau khi user báo lỗi kèm file xuất): trước đây marker là nút flex gồm badge icon 36px rồi tới nhãn text, đặt ở `left/top` bằng `translate(-50%, -50%)`, tức là tọa độ neo vào tâm của cả nhóm icon + nhãn, nên nhãn càng dài thì tâm icon càng lệch trái con trỏ. Ngoài ra, `pointerdown` gọi chọn điểm và việc chọn điểm đó dựng lại toàn bộ marker, làm nút vừa bấm bị xóa khỏi DOM và thay bằng nút mới, trong khi biến kéo vẫn giữ nút cũ đã bị tách ra, nên nút đang hiển thị đứng yên cho tới khi thả chuột mới dựng lại một lần. Bản sửa: hộp marker thu về vùng chạm 44px với badge nằm chính giữa, nhãn treo tuyệt đối sang phải icon và không còn ảnh hưởng tới phép neo, tọa độ lưu/thả xuống luôn là chính giữa của icon; logic kéo lấy lại đúng phần tử đang hiển thị để cập nhật theo thời gian thực. Lưu ý về cách kéo sau bản sửa: bản sửa giữ độ lệch lúc nắm icon, tức là nếu nắm ở mép icon thì icon di chuyển cùng con trỏ với đúng độ lệch đó, chứ không nhảy tâm icon về đúng dưới con trỏ ngay lúc nắm; nếu user muốn tâm icon luôn nằm đúng dưới con trỏ bất kể nắm ở đâu thì đó là một thay đổi riêng, chưa được chốt.
- Sửa popup trong thẻ (sau khi user chốt mục 6): thẻ thu nhỏ còn tối đa 440px như mục 6, chế độ Soạn bấm marker (không kéo) hoặc bấm dòng điểm thì mở thẳng trạng thái sửa với ba trường tiêu đề, nội dung, ảnh đại diện, sửa trên bản nháp, Lưu mới ghi, Hủy thì bỏ; trạng thái xem ẩn trường trống; trường trống ở trạng thái sửa chỉ hiện nút “+ Thêm…”, có ảnh thì hiện “Đổi ảnh / Xóa ảnh”, cả ba trường đều trống thì nút Lưu vô hiệu kèm dòng nhắc; xuất JSON thêm `popup.image` khi có ảnh, nhập JSON đọc lại `popup.image`. Điểm St. Paul Plant có sẵn một ảnh minh họa mẫu trong artifact để thấy ngay thẻ có ảnh; các điểm khác không có ảnh nên thẻ xem không hiện phần ảnh. Form bên cạnh trong artifact vẫn còn như đường dự phòng sau bản sửa này, chưa bị gỡ đi.

Những gì artifact chứng minh được: mô hình tọa độ % với neo tâm icon, list ↔ marker đồng bộ hai chiều, duplicate và xóa, JSON xuất/nhập, dialog/bottom sheet và zoom theo bước không làm lệch marker, thẻ sửa trong thẻ với quy tắc ẩn trường trống và kích thước nhỏ.

Những gì artifact không chứng minh được, và không được ghi là đã chứng minh: kéo thả trong editor Gutenberg (artifact đứng riêng ngoài Gutenberg, không xung đột với kéo của block); thao tác bấm/kéo bằng con trỏ thật và trình chọn file ảnh (probe tự động không kéo và không chọn file được, chỉ kiểm chứng được bằng tay); hiệu năng trên máy yếu (đã trả lời bằng phân tích: bản sửa neo không thêm chi phí đáng kể, chi phí mỗi lần di chuột vẫn là đổi `--x`/`--y` của một marker, chi phí nặng nhất là tải Tailwind/font lần đầu và dựng lại toàn bộ khi thả/gõ, nhưng với 7 điểm thì không đáng kể; bản WordPress thật còn nhẹ hơn artifact vì không có list editor, panel JSON hay Tailwind).

## 8. Checklist nghiệm thu và việc chưa làm

Checklist cho bản WordPress theo hướng tách bảng, khi bắt đầu build:

- 7 điểm mẫu hiển thị đúng vị trí trên desktop, tablet và mobile; mobile dưới 640px chuyển sang dạng danh sách, dialog thành bottom sheet tối đa `75dvh`, không tràn ngang trang.
- Bấm marker mở đúng dialog của điểm đó, đóng được bằng nút Close, backdrop và ESC, focus trả về đúng nút vừa bấm.
- Zoom qua 1x, 2x, 4x mà tâm icon vẫn đè đúng chi tiết địa lý; zoom về 1x khi load lại trang.
- Ở chế độ Soạn, bấm marker hoặc dòng điểm thì thẻ mở ở trạng thái sửa trong thẻ với đúng 3 trường; Lưu ghi vào bảng, Hủy bỏ thay đổi; ở chế độ Xem trước, trường trống không render, thẻ rộng tối đa 440px.
- Xóa bản đồ thì điểm và popup đi theo; dùng chung bản đồ ở nhiều page chỉ bằng cách đổi `mapId`; query điểm theo bản đồ, icon và thứ tự không cần parse `post_content`.
- Tọa độ lưu là tâm icon, không phải tâm nhóm icon + nhãn; sửa nhãn dài thêm không làm icon nhích đi.

Việc chưa làm, tính đến thời điểm tổng hợp:

- Plugin WordPress thật theo hướng tách bảng chưa được build: chưa có `dbDelta` tạo 3 bảng, chưa có màn hình quản trị sửa trong thẻ, chưa có block tham chiếu `mapId` và `render_callback` query bảng.
- Tranh minh họa gốc cho bản WordPress chưa có: tranh schematic trong artifact chỉ là tranh thử, không phải tranh gốc của dự án, và chưa được upload thành attachment hay lưu thành `background_svg` chính thức.
- Thử tay còn nợ: kéo/bấm bằng con trỏ thật với ngưỡng phân biệt 5px trong artifact, trình chọn file ảnh trong thẻ sửa, và typography trong dialog khi theme áp style chỉ cho `.wp-block-post-content`.
- Quyết định chưa chốt: có muốn tâm icon luôn nhảy về đúng dưới con trỏ khi nắm ở mép hay giữ độ lệch lúc nắm như hiện tại; có cần bảng snapshot revision cho dữ liệu tách bảng hay không; popup có cần thêm List/Buttons ngoài Heading/Paragraph/Image hay không.

## 9. Tài liệu liên quan

- `bao-cao-fse-mega-menu.md` — mega menu cho theme FSE, plugin so sánh và checklist test trước khi chốt, cùng dự án plugin FSE nhưng là tuyến riêng.
- `bao-cao-ban-do-svg-tuong-tac.md` — báo cáo gốc về bản đồ: tên gọi, phân tích Trident, so sánh inline SVG với `<img>`/`<object>`/background, vì sao không dùng HTML image map hay tile library, ghi chú Safe SVG vs SVG Support, ba tuyến tái tạo và checklist accessibility đầy đủ hơn.
- `huong-nhe-ban-do-tuong-tac.md` — tài liệu làm việc đã ghi hướng nhẹ `post_content`, sau đó được cập nhật thêm mục 8 (tách bảng riêng) và mục 9 (sửa popup trong thẻ). Báo cáo tổng hợp này là bản chốt hiện tại và ưu tiên hơn tài liệu làm việc đó khi có điểm khác nhau, vì nó ghi rõ quyết định tách bảng đã thay thế hướng `post_content`.
- Artifact thử `ban-do-svg-tuong-tac` — trang HTML Tailwind để thử UX list, kéo thả, JSON và sửa trong thẻ; không phải plugin WordPress và không thay thế checklist ở mục 8.
