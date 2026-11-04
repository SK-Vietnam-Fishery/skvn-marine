# Ideation — Plugin Design Sidebar

Date: 2026-10-21  
Status: **PLANNED — V1 / 1.3.21 Plugin Design Sidebar Foundation; chưa start.**
Quyết định cập nhật 2026-10-28: Category Grid 1.3.20 trước Sidebar 1.3.21, thay ưu tiên Sidebar ngay sau 1.3.18 ngày 2026-10-21. Current vẫn 1.3.18. Version 1.3.21 là ngoại lệ đánh số do human xác nhận; không bump metadata hoặc kích hoạt implementation. Các ghi chú “kế tiếp/chưa gán version” bên dưới là lịch sử, được cập nhật này thay thế.

Consumers đầu tiên: Product/Post Carousel và Product Category Grid; Slider/Step Slider/2-side Product tích hợp ở milestone riêng. Category details dùng Block Inspector hiện có trong 1.3.20, giữ dữ liệu Woo dùng chung tách khỏi style cục bộ. Execution plan phải giữ DS-01–DS-07 và acceptance 1.3.18; cần human xác nhận start, không hỏi lại SemVer.

## 1. Bối cảnh và giới hạn quyết định

Carousel có nhiều controls khiến sidebar Block dài và khó tìm. Human đề xuất sidebar riêng của plugin mở từ biểu tượng trên thanh công cụ editor hoặc từ nút trong block, với hai tab: chỉnh đối tượng đang chọn và settings chung của plugin. Human đồng ý lưu hướng này, chia thành các phần để cân nhắc triển khai sớm.

Mục tiêu là tách tác vụ dựng nội dung khỏi thiết kế chi tiết, không chỉ chuyển danh sách controls dài sang chỗ khác. Sidebar riêng vẫn hẹp; bên trong cần nhóm theo thành phần và hiện controls theo ngữ cảnh.

Current vẫn là V1 / 1.3.18 — Product/Post Carousel Editor UX & Technical. Tài liệu này không thay thế acceptance hoặc lệnh triển khai trong planning 045, không chuyển milestone, không mở rộng scope hiện tại. Human chốt sidebar triển khai ở milestone kế tiếp để các đợt Slider, Step Slider và 2-side Product dùng chung nền editor. Step Slider 1.3.10 vẫn DEFERRED về stack M5; quyết định này không tự kích hoạt các consumer đó. Trước implementation phải đối chiếu ledger, đề xuất SemVer và được human xác nhận version/start theo AGENTS.md; không hỏi lại việc có làm sidebar hay không.

Phạm vi nền đã chọn: shell hai tab Selected block / Plugin; selection, shortcut và back navigation; component design controls dùng attributes hiện có; Theme Design theo §4.1; save/permissions/fallback và QA theo DS-01–DS-07. Product/Post là consumer đầu tiên để kiểm chứng. Thiết kế điểm tích hợp cho Slider, Step Slider và 2-side Product nhưng giữ query/runtime/controls đặc thù tại từng block; không xây trước các block hoặc áp một schema chung cho mọi consumer. Defaults/linked global styles (gói E) vẫn là scope riêng. Execution plan cần inventory và mapping acceptance từ 045, không làm mất yêu cầu khi chuyển controls.

## 2. Cấu trúc trải nghiệm đề xuất

Tên sidebar tạm: **SKVN Design**. Product UI dùng English và i18n; tên cuối cùng còn mở.

| Surface | Trách nhiệm |
|---|---|
| Block Inspector | Content, Query, số item, Fields, Actions/URLs, Layout, navigation và chọn preset tổng thể |
| Selected block tab | Thiết kế chi tiết Image, Title, Attributes/Details, CTA: màu, typography, shadow, border/radius, tinh chỉnh media preset |
| Plugin tab | Tóm tắt cấu hình chung thực có và đường dẫn tới trang quản trị; defaults/preset management chỉ thêm khi có scope riêng |

Selected block là tab mặc định khi mở từ block. Nút **Open design settings** mở đúng block; shortcut **Edit button style** có thể mở thẳng nhóm Action. Có đường quay lại **Back to block settings**. Không nhân đôi cùng một control có thể chỉnh ở nhiều nơi nếu không có nhu cầu rõ ràng.

Preset tổng thể giữ tại Block Inspector để người biên tập tạo carousel cơ bản mà không phải mở Design. Tab Plugin không được dựng thành panel trống hoặc chứa placeholder cho tính năng tương lai.

## 3. Các gói công việc

| Gói | Nội dung | Đầu ra / điều kiện |
|---|---|---|
| A — Inventory và artifact | Map controls hiện có sang Inspector/Selected block/Plugin; thử hai tab, narrow view, các trạng thái selection | Artifact nhìn được; không tự chuyển toàn bộ controls sang sidebar mới |
| B — Sidebar shell và selection | Entry trên toolbar/menu, nút mở từ block, hai tab khi có nội dung, header đối tượng, back navigation | Selected block hoạt động trước; entry không phụ thuộc biểu tượng luôn nằm chính xác cạnh Save |
| C — Component design controls | Di chuyển từng nhóm Image → Title → Details → Action với attrs/preview/reset chung | Không đổi schema chỉ vì đổi vị trí UI; mỗi nhóm kiểm end-to-end trước nhóm kế |
| D — Plugin tab tối thiểu | Hiển thị cấu hình chung đã tồn tại, phạm vi/quyền và Open plugin settings | Không tạo bản sao settings store, không kéo toàn bộ admin vào sidebar |
| E — Defaults/presets tương lai | Tách rõ defaults cho block mới với linked global styles | Scope độc lập, cần product decision và SemVer; không bắt buộc để hoàn thành B–D |
| F — QA và mở rộng consumer | Product/Post trước; đánh giá Step Slider khi có implementation thật | Không mặc định các block có cùng cấu trúc; adapter chỉ cho block được hỗ trợ |

Thứ tự đề xuất: A → B → C → D → F; E có thể hoãn. Nếu chưa có nội dung chung hữu ích, B chỉ phát hành Selected block và giữ thiết kế tab Plugin cho D. Không đưa settings vô nghĩa vào để đủ hai tab.

## 4. Phạm vi dữ liệu và lưu

| Loại | Tác động | Quy tắc đề xuất |
|---|---|---|
| Selected block overrides | Một block | Lưu attrs của block, dùng Save và Undo/Redo của trang |
| Defaults for new blocks | Block được tạo sau đó | Không sửa block cũ; thao tác lưu defaults riêng, nếu được triển khai |
| Linked global styles | Các block liên kết style | Cần nhận diện liên kết và ảnh hưởng rõ ràng; chưa thuộc scope triển khai ban đầu |
| Plugin configuration | Hành vi/module chung | Quyền phù hợp, lưu riêng và trạng thái pending/success/error rõ ràng |

Không đặt tất cả dưới nhãn General. Page Save không được khiến người dùng tưởng đã lưu Plugin settings hoặc ngược lại. Quyền phải kiểm phía server, không chỉ ẩn controls. Chính sách chuyển tab khi settings chung chưa lưu phải được đặc tả trước code.

Theme tiếp tục sở hữu palette/font/tokens; plugin chọn giá trị được phép, không xây hệ thống font/màu toàn site cạnh tranh. Không thêm font loader hoặc raw CSS input theo ideation này.

### 4.1 Bổ sung 2026-10-21 — Typography, fonts và brand colors

Human muốn tab settings chung tập trung chủ yếu vào typography, font và màu thương hiệu. **Human xác nhận cách chia ngày 2026-10-21:** Theme Design gồm Typography, Fonts, Brand colors và Save & scope; plugin sở hữu controls/validation/lưu, theme sở hữu tokens/CSS/font delivery, block sở hữu lựa chọn riêng. Chỉ hiện controls tích hợp theme khi SKVN Marine có hỗ trợ phù hợp; theme khác ẩn nhóm đó nhưng giữ dữ liệu và chức năng block độc lập. Đây là **quyết định thiết kế đã chốt**, chưa phải lệnh triển khai, gán SemVer hoặc chuyển milestone.

Hướng đã chốt: đây là **Theme Design settings**, được truy cập qua sidebar plugin. Plugin sở hữu UI, validation và lưu cấu hình theo contract hiện có; theme sở hữu token mapping, phạm vi CSS và font delivery. Không tạo bộ typography/palette thứ hai dành riêng cho sidebar.

Source baseline: `wp-content/plugins/skvn-marine-blocks/modules/typography-settings/typography-settings.php` hiện sở hữu option `skvn_typography` và admin UI, gồm palette primary/accent/surface/text và heading h1–h4 size/weight. `wp-content/themes/skvn-marine/inc/typography.php` đọc cùng option và cấp CSS cho frontend/editor. Đây là evidence source, không chứng minh mọi token hoặc font control đã được hỗ trợ. Font family/loading phải audit riêng theo `docs/decisions/typography-scope-and-font-loading.md`; không suy từ tên Typography rằng module đã quản lý font đầy đủ.

Chia gói D thành:

- **D1 — Theme integration gate:** nhận diện active SKVN Marine và contract thật có; không dùng tên hiển thị theme. SKVN Marine là GeneratePress child theme nên kiểm mỗi parent template GeneratePress là sai. Không mở controls chỉ vì theme có tên tương tự; mechanism capability/version cần xác định khi implementation.
- **D2 — Typography:** vai trò Body/Heading/UI và scale được theme hỗ trợ; tái dùng nguồn cấu hình đã có, giải quyết mapping trước khi thêm control.
- **D3 — Fonts:** chọn font/preset được phép và fallback; font loading do theme hiện hành quản lý. CDN/self-host/upload hoặc mở rộng thư viện font không tự vào scope này, phải đối chiếu planning typography/font governance 1.6.0.
- **D4 — Brand colors:** semantic palette theo vai trò, tái dùng brand source; audit mapping palette hiện có với theme.json và consumers, không tạo thêm slot trùng tên nhưng khác giá trị.
- **D5 — Scope/save UX:** ghi rõ thay đổi thiết kế toàn site ở các nơi dùng token; lưu riêng có quyền và pending/error feedback, không hứa mọi element đều đổi nếu đang có override/hardcoded value.

| Theme/state | Hành vi đã chốt |
|---|---|
| SKVN Marine với integration tương thích | Hiện Theme Design trong tab Plugin; chỉnh các capability thực hỗ trợ |
| SKVN Marine nhưng thiếu contract/version phù hợp | Hiện lý do và không cho ghi settings không thể áp dụng |
| Theme khác, gồm GeneratePress parent đang active | Ẩn nhóm Theme Design; Selected block và tính năng plugin độc lập vẫn hoạt động theo fallback |
| Đổi khỏi SKVN Marine rồi quay lại | Giữ dữ liệu đã lưu, không xóa options hoặc sửa nội dung; chỉ ngừng áp dụng integration không còn phù hợp |
| Tab Plugin không còn mục hợp lệ | Không để tab trống; có thể ẩn tab, giữ trợ giúp ở nơi thích hợp |

“Bỏ khi theme khác” đã chốt là bỏ **controls tích hợp theme**, không xóa dữ liệu, không vô hiệu hóa toàn bộ plugin. Không ghi đè theme khác; không tự tạo adapter cho theme bên thứ ba trong gói này. Giao diện dùng phạm vi **Theme Design — SKVN Marine**, tránh để người dùng hiểu font/màu toàn site chỉ tác động block plugin. Nơi kế thừa token và nơi có override riêng phải được phân biệt; không hứa đổi token sẽ đổi mọi phần tử. Chi tiết API nhận diện capability, mapping tokens, inventory controls và cơ chế font thực tế còn phải audit khi lập execution plan.

Acceptance bổ sung cho F: thử SKVN Marine/GeneratePress/theme khác; đổi theme rồi quay lại; role không có quyền; paired editor/frontend với inherited token và block override; font fallback/network chỉ tải nguồn đã chọn; màu/contrast và typography không tràn trên mobile. Evidence cần ảnh, saved values, network/console và build/theme version. Trạng thái **Chưa làm**; không tự chạy đổi theme trên production để kiểm.

## 5. Content & States

- Header Selected block ghi loại block và heading nếu có; chỉ rõ toàn bộ block hay thành phần đang chỉnh. “Chọn CTA” trong panel không đồng nghĩa CTA phải là Gutenberg child block.
- Chọn block được hỗ trợ khác: tab Selected block đổi đối tượng ngay, không giữ clientId cũ để ghi nhầm.
- Không chọn block, chọn block không hỗ trợ, multi-selection: có trạng thái hướng dẫn riêng; chưa hỗ trợ bulk edit.
- Đang ở Plugin tab: chọn block khác không tự chuyển tab; quay lại Selected block thấy selection mới nhất.
- Xóa, duplicate, undo/redo block: giải phóng target cũ; không lưu vào block đã mất.
- Product/Post, Grid/Carousel, showImage on/off và media preset quyết định nhóm controls áp dụng; không hiện autoplay cho Product.
- Hẹp, sidebar mở/đóng, keyboard-only: tab/nhóm truy cập được, focus quay về entry hợp lý; không chỉ dùng icon không nhãn.
- Không mount thêm carousel runtime hoặc refetch query vì chuyển tab/chỉnh style; quan sát network và lifecycle khi QA.

## 6. Acceptance & Evidence dự kiến

Tất cả yêu cầu dưới đây **Chưa làm** trong phạm vi sidebar mới. Đồng ý ý tưởng không phải runtime PASS. Khi kích hoạt, lập runbook onsite với URL/build/fixture/steps và evidence thực.

| ID | Nhóm | Hành vi mong đợi | Hiện trạng | Việc cần sửa | Cách kiểm | Evidence cần |
|---|---|---|---|---|---|---|
| DS-01 | Editor UX | Mở đúng block và nhóm từ shortcut | Chưa làm | Shell, entry và selection | Hai carousel khác heading, mở CTA rồi đổi selection | Video và ảnh target header |
| DS-02 | Editor UX | Tabs phân biệt phạm vi, không giật tab khi chọn block | Chưa làm | Tab state, empty/unsupported/multi-selection | Chuyển tab/chọn/xóa/duplicate | Video các states |
| DS-03 | Editor UX | Controls dễ tìm ở container hẹp, dùng bàn phím được | Chưa làm | Nhóm controls, labels, focus/back | Artifact rồi editor thật; query, CTA color, Floating | Ghi số thao tác/cuộn, ảnh và keyboard notes |
| DS-04 | Technical | Styles lưu đúng block, Undo/Redo/reopen giữ giá trị | Chưa làm | Kết nối attrs hiện có, không store trùng | Hai block, save/reload, duplicate, undo/redo | Attrs và paired screenshots |
| DS-05 | Technical | Settings chung lưu riêng, đúng quyền | Chưa làm | Tái dùng config owner và capability checks | Save/error/unsaved-tab-change, user có/không quyền | Response/log và ảnh scope/save status |
| DS-06 | Block UI | Di chuyển controls không đổi output ngoài ý muốn | Chưa làm | Giữ defaults/legacy/render contracts | Old/new Product/Post, Grid/Carousel; title dài, thiếu ảnh, CTA/focus, mobile | Ảnh frontend trước/sau cùng fixture |
| DS-07 | Technical | Chuyển tab/style không tạo fetch/listener dư | Chưa làm | Subscription/cleanup và lazy rendering hợp lý | Chuyển tab/selection lặp, theo dõi query và runtime | Network/console/lifecycle evidence |

## 7. Trade-offs, các quyết định còn mở

Lợi: giảm độ dài Inspector, phân tách nội dung và thiết kế, có điểm truy cập chung cho các block được hỗ trợ. Giá: thêm bước chuyển panel, dễ nhầm block/global và tăng công việc selection/focus/save lifecycle. Sidebar mới không tự làm controls rộng hơn.

Cần quyết ở bước planning: tên sidebar/tab; inventory controls cuối cùng; nội dung thực của Plugin tab; role/capabilities; xử lý thay đổi chung chưa lưu; có đưa defaults vào scope hay chỉ link quản trị. Global styles không phải mặc định được duyệt. Phương án quay lại: giữ attrs/render không đổi để có thể trả controls về Inspector mà không migrate nội dung.

Default trước khi human xác nhận SemVer và bắt đầu milestone kế tiếp: chuẩn bị execution plan theo ưu tiên đã chốt; chưa code shell, không sửa plan 045 thành scope mới, không bump metadata. Quyết định 2026-10-21 thay thế ưu tiên dự kiến sau Step Slider; không thay đổi acceptance hiện tại hoặc coi sidebar đã triển khai.

## 8. Tham chiếu và bài học

- [Planning 045](../../.context/planning/045_VER_1_3_18_CAROUSEL_EDITOR_UX_TECHNICAL_EXECUTION_PLAN.md), đặc biệt S3–S6: acceptance hiện có phải được giữ khi chuyển surface.
- [Review contract](../standards/block-ui-editor-ux-technical-review-contract.md).
- [WordPress PluginSidebar](https://developer.wordpress.org/block-editor/reference-guides/slotfills/plugin-sidebar/): extension point cho sidebar; phải đối chiếu phiên bản dependencies của repo trước implementation.
- [Yoast sidebar](https://yoast.com/help/optimize-your-content-with-yoast-seo-sidebar/) và [site features](https://yoast.com/help/yoast-seo-settings-site-features/): tách chỉnh nội dung hiện tại với quản trị toàn site.
- [Kadence block defaults](https://docs.nexcess.com/software/kadence/set-block-defaults/): thao tác Save as Default rõ ràng.
- [GenerateBlocks global styles](https://learn.generatepress.com/blocks/block-guide/getting-started-generateblocks/generateblocks-pro/global-styles/): phân biệt local/global và không gian quản lý style.

Nguồn đối chiếu 2026-10-21; các plugin trên là ví dụ phân chia trách nhiệm, không khẳng định đều dùng cùng mô hình hai tab. Không sao chép API/behavior theo tài liệu phiên bản khác mà chưa kiểm tương thích.
