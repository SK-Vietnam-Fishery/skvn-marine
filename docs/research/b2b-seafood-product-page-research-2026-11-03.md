# Nghiên cứu trang chi tiết sản phẩm B2B thủy sản

Ngày nghiên cứu: **2026-11-03**.  
Trạng thái: **Research / đề xuất để thảo luận**, chưa phải đặc tả được duyệt hoặc lệnh triển khai.  
Phạm vi: từ carousel đã có đến trang chi tiết sản phẩm và hành trình yêu cầu báo giá dùng được cho SKVN Marine.

## 1. Kết luận nghiên cứu

Carousel giúp giới thiệu và dẫn khách đến sản phẩm. Trang sản phẩm B2B cần nối đủ hành trình:

**Tìm sản phẩm → hiểu quy cách → kiểm tra khả năng cung cấp → gửi yêu cầu báo giá có ngữ cảnh.**

Khuyến nghị là kết hợp thông số rõ ràng của catalog với nội dung bán hàng ngắn gọn. Không cần sao chép toàn bộ hệ thống lọc hoặc độ dài website của nhà cung cấp lớn. Ưu tiên dữ liệu thật, một template dùng chung và quote flow hoàn chỉnh trước khi thêm nhiều hiệu ứng hoặc block mới.

Tài liệu này không thay đổi milestone, metadata, source code, artifact đã khóa hoặc các quyết định đang có. Các mục “đề xuất”, “ưu tiên” và “cần” bên dưới là kết quả nghiên cứu, chưa tự trở thành scope triển khai.

## 2. Phương pháp và giới hạn bằng chứng

- Đối chiếu nội dung web công khai qua search và text extraction với source/theme/plugin và tài liệu quote flow trong repository.
- Không thực hiện visual audit bằng trình duyệt, không đo UX/conversion của website tham khảo, không kiểm thử form của họ.
- Pacific Seafood Rockfish và sell sheet được tham khảo từ nội dung search index; lần mở trực tiếp trang Rockfish bị timeout. Trang danh mục Trident đọc được, nhưng lần mở một trang chi tiết sản phẩm trả lỗi. Không suy diễn các hành vi chưa truy cập được.
- Source findings chỉ xác nhận mã đang có tại ngày nghiên cứu. Chưa xác nhận build nào được deploy hoặc trang onsite có thực thi các đoạn đó hay không.
- Không có bằng chứng mới để đánh dấu onsite PASS. Không coi một control, hook hoặc meta reader là bằng chứng hoàn chỉnh cho nhập → lưu → render → gửi báo giá.

## 3. Benchmark ngành và điều có thể học

| Nguồn | Nội dung quan sát được | Suy luận áp dụng cho SKVN |
|---|---|---|
| Pacific Seafood — Rockfish | Nêu nguồn gốc, dạng chế biến và trọng lượng đóng gói. Sell sheet có thông số, mã hàng và pack size. | Người mua cần xác định đúng dạng hàng đang hỏi. Specs và tài liệu nên hỗ trợ kiểm tra, không chỉ trang trí. |
| Trident Seafoods — Food Service Products | Danh mục có search, filter theo loài, ngành khách hàng, cách chế biến; listing có quy cách và mã hàng. | Carousel cần đường dẫn tới danh mục đầy đủ; không nên là cách duy nhất để tìm hàng. Chưa cần sao chép toàn bộ filter. |
| MOWI Signature | Trình bày fillet, pre-cut portions và khả năng phát triển theo yêu cầu bếp chuyên nghiệp. | Mô tả cần giải thích giá trị sử dụng và khả năng tùy chỉnh bên cạnh thông số. |
| Royal Greenland — Foodservice | Nhấn mạnh nhu cầu bếp chuyên nghiệp, nguồn cung, bảo quản và bao bì phù hợp. | Packaging là thông tin hỗ trợ quyết định mua, không chỉ một badge trên card. |

Nguồn tham khảo, truy cập/search ngày 2026-11-03:

- [Pacific Seafood — Rockfish](https://www.pacificseafood.com/global-showcase/rockfish/).
- [Pacific Seafood — Rockfish sell sheet PDF](https://www.pacificseafood.com/pac-incoming/2018/05/Sellsheet_Rockfish.pdf).
- [Trident Seafoods — Food Service Products](https://tridentseafoods.com/food-service/products).
- [MOWI Signature / Foodservice](https://mowifoodservice.com/).
- [Royal Greenland — Foodservice](https://www.royalgreenland.com/en/foodservice).

Đây là benchmark về nội dung và cách tổ chức thông tin, không chứng minh layout nào có hiệu quả chuyển đổi cao nhất.

## 4. Block UI — các thành phần đề xuất

| Thành phần | Nội dung | Mức ưu tiên đề xuất |
|---|---|---|
| Nhận diện sản phẩm | Tên đầy đủ, tên khoa học nếu có, SKU, category, mô tả ngắn | Cần cho bản dùng được |
| Gallery | Ảnh thực tế; thêm ảnh dạng cắt, túi/thùng khi có | Cần cho bản dùng được |
| Quy cách cung cấp | Size/grade, processing, skin/bone, freezing, packaging; ghi rõ đơn vị | Cần cho bản dùng được |
| Điều kiện thương mại | MOQ, lead time, custom pack/private label theo khả năng đã xác nhận | Hiển thị khi có dữ liệu thật |
| Request a Quote | CTA rõ ràng, chuyển đúng sản phẩm sang form | Cần cho bản dùng được |
| Tài liệu và chứng nhận | Spec sheet, chứng nhận áp dụng, tài liệu có thể yêu cầu; rõ phạm vi sản phẩm/nhà máy/lô hàng | Theo dữ liệu thực |
| Packaging & Storage | Nhiệt độ, shelf life, net weight/glazing khi áp dụng, cấu hình túi/thùng | Theo loại hàng |
| Related Products | Cùng loài, dạng cắt khác, sản phẩm thay thế có liên quan | Tận dụng carousel |
| Ứng dụng và FAQ | Câu hỏi mua hàng thực tế, chế biến, khả năng tùy chỉnh | Bổ sung khi có nội dung hữu ích |

Bố cục khởi đầu để thảo luận:

```text
Breadcrumb

Gallery                         Tên sản phẩm + SKU
                                Mô tả ngắn
                                Quy cách nổi bật
                                MOQ / lead time nếu có
                                [Request a Quote]
                                [Download specification nếu có]

Specifications — bảng đầy đủ, dễ quét
Packaging & Storage
Documents & Certifications
Ứng dụng / FAQ nếu có nội dung hữu ích
Related Products — tận dụng carousel
```

Desktop có thể dùng hai cột ở phần đầu, mobile xếp dọc và giữ CTA dễ tiếp cận. Thông số chính nên nhìn thấy ngay, không dồn toàn bộ vào tab. Đây chưa phải quyết định loại bỏ cơ chế tab hiện tại.

### Những điểm không nên bê nguyên từ carousel

- Không phóng lớn nguyên card thành trang chi tiết: card phục vụ quét nhanh, trang chi tiết phục vụ kiểm tra đủ thông tin.
- Giới hạn giá trị thuộc tính bằng `+N` phù hợp với card; không nên che thông tin cốt lõi trong phần specifications đầy đủ.
- Khung media 1:1 contain của card không bắt buộc áp lên mọi ảnh gallery. Ảnh bao bì/dạng cắt có tỷ lệ khác; gallery cần xem đầy đủ và cân nhắc phóng lớn.
- Có thể tận dụng token, kiểu CTA, dữ liệu sản phẩm và carousel related; không vì tái sử dụng mà ép bố cục chi tiết vào cấu trúc card.

## 5. Quy cách thông tin khác với biến thể có thể yêu cầu mua

Ví dụ một sản phẩm có size `100–200 g`, `200–300 g`; pack `1 kg/bag`, `10 kg bulk`; freezing `IQF`, `Block`.

Ba dropdown độc lập có thể khiến người mua hiểu mọi tổ hợp đều được cung cấp. Danh sách attributes hiện tại chưa chứng minh điều đó.

Đề xuất bước đầu:

1. Hiển thị **available specifications** như thông tin.
2. Cho khách ghi **requested specification** trong form báo giá.
3. Chỉ xây bộ chọn cấu hình khi có dữ liệu xác định tổ hợp hợp lệ.

Nếu mỗi tổ hợp có SKU, ảnh, MOQ hoặc tài liệu riêng, đánh giá Woo variations sau. Không tự sinh tích Descartes của size × pack × freezing và coi đó là các lựa chọn hợp lệ.

## 6. Editor UX — dữ liệu phải cập nhật được mà không sửa PHP

| Nơi quản lý | Dữ liệu/phạm vi đề xuất |
|---|---|
| Woo Product native | Tên, short/long description, ảnh/gallery, SKU, category, attributes |
| Dữ liệu B2B bổ sung | MOQ, lead time, spec sheet và các trường native chưa đáp ứng; phải audit owner/schema hiện có trước khi thêm |
| Dữ liệu doanh nghiệp/nhà máy dùng chung | Hồ sơ năng lực, tài liệu chung, chính sách liên hệ; không gán đồng loạt thành chứng nhận của mọi sản phẩm |
| Template / Design settings | Bố cục, typography, màu, spacing, cách trình bày; tách khỏi dữ liệu thương mại |

Carousel và trang chi tiết đọc cùng nguồn dữ liệu. Không nhập lại thông số trong từng block rồi để chúng sai lệch sau khi sửa sản phẩm.

Editor cần phân biệt chưa có dữ liệu, có nhưng chưa công khai, có tài liệu tải trực tiếp và tài liệu chỉ cung cấp khi khách yêu cầu. Thiếu dữ liệu thì ẩn phần tương ứng hoặc hiện trạng thái phù hợp; không điền nội dung mẫu nhằm lấp chỗ trống.

Plugin UI mới theo English-first/i18n của dự án. Kiểm label, keyboard, sidebar/canvas hẹp, preview và save/reload trước khi kết luận sử dụng được. Chưa có quyết định bổ sung editor/sidebar mới trong nghiên cứu này.

## 7. Technical — hiện trạng source và khoảng trống

### 7.1 Có nền tảng để tận dụng

- Theme có hook thay Add to Cart bằng CTA báo giá ở single product; helper tạo URL `/request-a-quote/?product_id=…`.
- Có phần mô tả, `wc_display_product_attributes()` và các tab Product Info, Certifications & Docs, Packaging & Cold Chain, Related Articles.
- Collection renderer đọc `_skvn_certifications`, `_skvn_moq`, `_skvn_lead_time`, `_skvn_spec_sheet_url` tại `cards.php:392–395`. Điều này chỉ chứng minh có reader; chưa chứng minh UI quản trị, validation và save path đã hoàn chỉnh.
- Contract quote flow dùng same-site page, CF7 và CFDB7. Không cần viết một form handler mới.

Source tham chiếu (số dòng tại ngày nghiên cứu):

- [Theme Woo integration](../../wp-content/themes/skvn-marine/inc/woocommerce.php): `skvn_marine_get_product_quote_url`, `skvn_marine_woocommerce_single_quote_cta` (105), `skvn_marine_tab_product_info` (186), `skvn_marine_tab_documents` (239).
- [Collection cards](../../wp-content/plugins/skvn-marine-blocks/modules/collection-render/cards.php): metadata readers (392–395).
- [Product data model](../decisions/product-data-model.md).
- [Quote flow decision](../decisions/quote-flow.md) và [quote module context](../../.context/modules/QUOTE_FLOW.md).

### 7.2 Nội dung viết cố định phải được phân biệt với dữ liệu thật

Các giá trị sau tồn tại trực tiếp trong `inc/woocommerce.php`:

| Dòng | Source finding | Hệ quả cần xử lý trước khi coi trang là catalog thật |
|---|---|---|
| 134 | `Cold Chain Certified` | Chưa có bằng chứng từ đoạn render rằng claim này được xác định theo sản phẩm/nhà máy |
| 138 | `Bảo hành 24 tháng` | Không dùng “bảo hành” thay cho shelf life; cần xác định nội dung thực sự muốn công bố |
| 226 | Yield `35–40%` | Không áp một tỷ lệ cố định cho mọi loại sản phẩm |
| 256 | `Grade A · Valid 2024–2025` | Thời hạn viết cố định, không phản ánh trạng thái hiện hành tự động |
| 288, 298 | `CURRENT` cho tài liệu | Trạng thái tĩnh không chứng minh tài liệu còn hiệu lực |

Đây là **bằng chứng source**, chưa phải khẳng định onsite đang hiển thị chúng, cũng không xác minh doanh nghiệp có hay không có các chứng nhận này. Đề xuất nối với dữ liệu đã xác nhận hoặc bỏ khỏi output nếu chưa có căn cứ. Nghiên cứu không thực hiện thay đổi đó.

### 7.3 Quote flow cần bằng chứng end-to-end

Module context vẫn ghi còn thiếu bằng chứng cho product context, submit, lưu CFDB7 và thank-you behavior. Form giao diện ổn hoặc có thông báo thành công chưa đủ chứng minh sales nhận được đúng yêu cầu.

Cần kiểm sản phẩm/SKU/URL nguồn, yêu cầu quy cách, contact fields; giữ lại dữ liệu khi submit lỗi; kiểm bản ghi lưu và thông báo/email theo cấu hình thực. Không suy diễn trạng thái onsite từ tài liệu cũ; cần evidence trên build triển khai cụ thể.

Giữ boundary hiện có: Woo native là nguồn sản phẩm; plugin sở hữu logic/data/editor, theme sở hữu visual/layout. Không sửa GeneratePress, không thêm CPT hoặc form handler riêng chỉ để triển khai layout mới.

## 8. Ma trận review đề xuất — chưa phải checklist đã duyệt

| ID | Nhóm | Hành vi mong đợi | Hiện trạng | Việc cần sửa/bổ sung đề xuất | Cách kiểm | Evidence hiện có/cần có |
|---|---|---|---|---|---|---|
| PDP-UI-01 | Block UI | Gallery, summary, specs, CTA đọc được trên desktop/mobile | Chờ kiểm chứng — chưa visual audit single product | Thiết kế template với dữ liệu đại diện, không phóng lớn card | Title dài; ảnh vuông/ngang/dọc/thiếu; keyboard; zoom; viewport hẹp | Cần URL/build, screenshot và geometry; chưa có runtime PASS |
| PDP-UI-02 | Block UI | Specs đầy đủ, không ngụ ý tổ hợp đặt hàng không tồn tại | Đã có source hiển thị WC attributes; semantics cấu hình chờ kiểm chứng | Phân biệt available specs với requested specs | Nhiều giá trị size/pack/freezing; đối chiếu khả năng cung cấp thật | Theme `skvn_marine_tab_product_info`; cần dataset và review nghiệp vụ |
| PDP-UX-01 | Editor UX | Sửa dữ liệu một lần, card và detail cùng cập nhật | Đã có source readers; toàn bộ edit/save path chờ kiểm chứng | Audit owner của từng field và UI nhập/lưu | Edit → save → reload → so card/detail; lỗi quyền/save; sidebar hẹp và keyboard | `cards.php:392–395`; cần evidence quản trị và frontend |
| PDP-TECH-01 | Technical | Claims/tài liệu được xác định bằng dữ liệu thực, đúng phạm vi | FAIL ở source đối với mục tiêu dữ liệu động: có literal dùng chung | Thay literal bằng dữ liệu có căn cứ hoặc ẩn; mô hình hiệu lực/tài liệu cần thiết kế | Sản phẩm khác nhau; thiếu/hết hạn/không công khai; không tự gắn CURRENT | Findings §7.2; onsite chờ kiểm chứng |
| PDP-TECH-02 | Technical | Quote giữ đúng product context tới bản ghi nhận | Chờ kiểm chứng theo module context | Kiểm và hoàn thiện tích hợp CF7/CFDB7 hiện có | CTA → form → submit → stored row → thank-you; thử lỗi/mất dữ liệu | Contract quote flow; cần submission ID/time, row, email status và screenshot |
| PDP-TECH-03 | Technical | Trang ổn định, không tải runtime thừa hoặc gây tràn | Chờ kiểm chứng — không performance audit trong research | Kiểm scope assets/gallery/related; giữ nội dung truy cập được | Mobile, keyboard, no-JS nơi áp dụng; network, layout shift, overflow | Cần measurements trên URL/build cụ thể; không đặt số PASS giả |

Content & States phải bao phủ ít nhất: sản phẩm đủ dữ liệu; thiếu ảnh hoặc PDF; tên dài; nhiều thuộc tính; không có related products; tài liệu không công khai/hết hạn; form lỗi; Woo dependency inactive ở phần liên quan. Không cần nhân mọi tổ hợp thành test matrix vô ích.

Khi được giao implementation hoặc onsite testing, tạo test document riêng dưới `docs/testing/` với URL, setup, bước kiểm, expected behavior và evidence. Tài liệu nghiên cứu này chưa thay thế test runbook.

## 9. Phạm vi tối thiểu và bước tiếp theo đề xuất

Ưu tiên một bản dùng được gồm:

1. Một template single product dùng chung: gallery, summary, specifications, CTA.
2. Dữ liệu thống nhất giữa card/detail và loại bỏ nội dung mẫu khỏi output thực.
3. Quote flow giữ đúng sản phẩm, yêu cầu và thông tin liên hệ; có bằng chứng nhận/lưu.
4. Tài liệu/chứng nhận đúng phạm vi, không giả lập nội dung thiếu.
5. Category/archive dẫn vào sản phẩm; carousel dùng cho featured/related.
6. Onsite verification với dữ liệu đại diện, mobile, keyboard và gửi báo giá.

Compare, multi-product quote basket, đăng nhập khách hàng, giá riêng và advanced filters là hướng nghiên cứu sau, không phải điều kiện mặc định để bắt đầu dùng trang.

Bước tiếp theo được khuyến nghị là chọn **một sản phẩm thật có bộ dữ liệu đại diện** để thiết kế trang chi tiết. Từ đó xác định phần nào dùng Woo template, phần nào cần block mới và phần nào tái sử dụng carousel. Chưa chọn sản phẩm mẫu, chưa duyệt layout và chưa bắt đầu implementation.
