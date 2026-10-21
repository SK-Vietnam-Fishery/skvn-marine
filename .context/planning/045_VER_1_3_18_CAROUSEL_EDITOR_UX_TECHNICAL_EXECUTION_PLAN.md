# V1 / 1.3.18 — Carousel Editor UX & Technical: kế hoạch trình duyệt

Ngày: 2026-10-19; cập nhật chi tiết: 2026-10-20; chốt Product media controls: 2026-10-21. **Status: planning chi tiết đã lập; implementation Editor UX chưa bắt đầu.**

Current là **V1 / 1.3.18 — Product/Post Carousel Editor UX & Technical**, human chuyển ngày 2026-10-19; 1.3.17 đã archive với carry-over theo ledger. Stack M1 gồm 1.3.17 + 1.3.18. PATCH theo mapping đã xác nhận để hoàn thiện collection hiện có, không mở hệ thống thiết kế mới. 1.3.19 dành cho Hero contrast hotfix, không đổi phạm vi đó. Lượt 2026-10-20 chỉ lập planning, không bump/build hay cấp lệnh implementation Editor UX.

**Ưu tiên đọc:** §9–13 là đặc tả hệ thống cập nhật ngày 20/10; §2 và §8 giữ làm lịch sử đề xuất trước transition, không còn yêu cầu chuyển lại milestone. Acceptance §4–6 vẫn giữ hiệu lực theo các override mới ở §9.

Authority: [milestone ledger](../MILESTONES.md), [planning 040](040_VER_1_3_17_CAROUSEL_UI_UX_TECHNICAL_PLANNING.md) §4–7, §11, §13 và quyết định human ngày 19/10. Tài liệu này bổ sung execution plan, không thay hoặc xóa acceptance gốc.

## 1. Quyết định và giới hạn

- Human đưa Style/Typograpy, diện mạo title link và editor preview sang milestone kế. Mũi tên lệch tâm là sửa nhỏ của **1.3.17**, không dùng 1.3.18 để che acceptance còn thiếu.
- Product Carousel manual-only: kể cả saved `autoplay=true`, không tự chạy và không Play/Pause. Không tự áp quyết định này cho Post/Hero.
- Product là lát cắt triển khai đầu tiên. Post vẫn thuộc acceptance 1.3.18; không ép các trường Product (Size/Pack/Freezing…) sang Post. Grid là consumer cần regression, không redesign Grid.
- Theme sở hữu palette, font, size/spacing tokens; plugin sở hữu controls, attributes, render và runtime. Không thêm font CDN/upload font, raw CSS, dependency hoặc sidebar kiến trúc mới.
- Không đổi query owner, namespace, quote URL contract, schema sản phẩm hay xử lý CF7. Không thêm filter/facet, taxonomy builder, woo-catalog 1.5.0 hoặc Hero features.

## 2. Entry gate trước chuyển mốc

1. Đối chiếu source hiện tại, build deployed và evidence 1.3.17; working tree đang có nhiều thay đổi, không reset/revert công việc agent khác.
2. Xác nhận regression scrollbar/state-owner, skeleton, Product manual-only và arrow centering. Source có thay đổi không đồng nghĩa onsite PASS. Kết quả width onsite đã có giữ nguyên phạm vi chứng minh, không suy rộng toàn bộ carousel.
3. Liệt kê acceptance 1.3.17 còn thiếu. Chỉ đóng completed khi đủ evidence và human approve. Nếu human chọn chuyển ưu tiên dù chưa đủ, ghi **deferred** và giữ từng acceptance/đích nhận; không tự đánh dấu hoàn thành.
4. Human duyệt bắt đầu 1.3.18 rồi mới giao agent transition. Agent cập nhật AGENTS + MILESTONES + history theo protocol; chỉ archive tensions đúng milestone cũ, không cuốn theo tension milestone khác/Hero hotfix.
5. Đối chiếu metadata thực tế với hotfix đang tồn tại trước sync development version; nếu có xung đột version thì báo cụ thể. Không tự ghi đè metadata release mới hơn. Build development không phải release approved.

## 3. Editor UX — bố cục và controls

### 3.1 Preview theo artifact đã duyệt

- Card mẫu gọn căn giữa, chiều cao theo nội dung; nhãn “Dữ liệu mẫu”. Không kéo giãn theo query list và không tạo snapshot sản phẩm trong saved content.
- Cấu hình nhãn–giá trị bên dưới, lấy attributes thật. Toggle ảnh thực sự thay đổi media. Một card không chứng minh equal-height: ghi đúng giới hạn, không thêm toggle giả.
- Query là vùng riêng toàn chiều ngang, đọc theo hàng; 3/2/1 cột và 9/6/3 item trước khi mở rộng. Tên đầy đủ, wrap, số thứ tự nhỏ, không giả dạng chip/button.
- Xem thêm/thu gọn dùng bàn phím được; resize không làm mất focus hoặc cắt nội dung. Breakpoints artifact chỉ là thử nghiệm, production chọn theo available container width và kiểm Group/Columns/sidebar.
- Viền editor biểu thị geometry thực; nhãn Normal/Wide/Full biểu thị lựa chọn. Full không đồng nghĩa viewport; các width có thể bằng nhau khi canvas hẹp. Không copy lab controls hoặc khóa sidebar 320px từ artifact.
- Phân biệt loading/empty/error/results; nếu query gần đúng, nêu giới hạn cụ thể. Không gọi danh sách “đúng truy vấn” khi filters/order chưa parity.
- Không chạy Swiper/autoplay trong editor. Bỏ roadmap/dev placeholder khỏi preview. Post phản ánh đúng image/date/author/excerpt và thứ tự card riêng.

Artifact tham khảo: `docs/artifacts/editor-ui/carousel-editor-preview-prototype.html` và `.md`. Prototype PASS không phải Gutenberg runtime PASS.

### 3.2 Nhóm settings

| Nhóm | Phạm vi |
|---|---|
| Content | Query/filter/order/count; heading/intro; thông tin hiển thị; limit 2/3/4 mỗi thuộc tính; action label/URL |
| Style | Card/media presets hiện có; title; thuộc tính/chips; Action button; typography từng vai trò |
| Layout | Normal/Wide/Full, responsive preset, equal-height, navigation hiện có |
| Advanced | Chỉ options đang có tác dụng và cần thiết; không panel rỗng. Autoplay chỉ theo scope Post hiện hành |

### 3.3 Style/typography đề xuất để thực hiện

- **Product title:** giữ link tới sản phẩm; default dùng màu/typography của title thay appearance link xanh/gạch chân ngoài ý muốn. Có hover/focus phân biệt, keyboard focus luôn nhìn thấy. Không đổi URL hoặc semantics anchor để sửa style.
- **Action button:** chọn màu nền/chữ và nền/chữ hover từ palette theme; focus có phản hồi tương ứng nhưng không xóa focus indicator. Cùng setting áp cho action buttons của block, không per-product.
- **Typography:** tối thiểu ba vai trò độc lập: title, thông tin thuộc tính, action. Chọn font family và font size theo preset theme đang cung cấp; mặc định kế thừa, không ép một cỡ chữ toàn card. Post ánh xạ vai trò phù hợp, không thêm tất cả controls Product một cách máy móc.
- **Reset:** reset từng nhóm về theme/default contract; không xóa Content/Query/URL. Giá trị thiếu hoặc token đã bị bỏ phải fallback an toàn. Không lưu computed pixel như snapshot của theme.
- **Compatibility:** xác định rõ default mới và block cũ thiếu attr. Typography mặc định kế thừa; thay diện mạo title link được giới hạn collection scope, ghi evidence trước/sau. Không global override mọi link của website.
- Trước code lập bảng token/options/default từ theme thực tế. Nếu thiếu font/size, không bịa option hoặc thêm font loader; báo thiếu và đề xuất nhỏ trong phạm vi theme governance.

## 4. Requirement và acceptance bổ sung

Mọi dòng dưới có Content & States ở §6. Status ban đầu là **Chưa làm** trừ nơi có evidence cụ thể; artifact chỉ là bằng chứng thiết kế. Trạng thái được dùng: Chưa làm / Đã có source / Chờ kiểm chứng / PASS / FAIL / Deferred.

| ID | Nhóm | Hành vi mong đợi | Hiện trạng | Việc cần sửa | Cách kiểm | Evidence cần lưu |
|---|---|---|---|---|---|---|
| UX-09–11 | Editor UX | Preview gọn, query wrap/mở rộng, width thật | Artifact có; production chờ audit | Port theo §3.1 | Sidebar, Group/Columns, resize, keyboard | Ảnh editor + rect + steps |
| UX-12 | Editor UX | Action colors chỉnh/reset được | Source baseline hardcode, chưa control | Palette controls và render mapping | Mỗi option → preview → save/reload → frontend | Attrs + computed colors 2 surfaces |
| UI-18-TITLE | Block UI | Title giữ link, appearance có chủ đích | Human thấy xanh/gạch chân | Scoped title style, hover/focus | Long title, click/tab và theme cascade | Screenshot + URL + computed styles |
| UX-13 | Editor UX | Font/size độc lập theo 3 vai trò | Chưa có controls được xác minh | Preset controls, inheritance/reset | Chọn/clear token, reopen, title dài | Attrs + font/size computed |
| TECH-13 | Technical | Token hợp lệ, không CSS injection, old content an toàn | Chưa làm | Validate/sanitize/escape, fallback thiếu token | Invalid slug, missing attrs, theme token đổi | Cases + output DOM/CSS |
| TECH-12 | Technical | Preview trung thực, không fetch từng card dư thừa | Artifact có; production chờ audit | State/query adapter phù hợp | Loading/error/empty, 20 items, network | Request count + state evidence |
| UI-18-STYLE | Block UI | Styles không tràn/che CTA, đọc/focus được | Chờ kiểm chứng | Scoped styles theo theme ownership | Font/size lớn nhất, mobile, hover/focus | Ảnh + geometry + contrast |

## 5. Carry-in Technical và acceptance cũ — không mất việc

Giữ nguyên IDs và nội dung đầy đủ tại 040 §4–7/§11. Những findings cũ phải re-audit trước sửa, không coi snapshot lịch sử là lỗi vẫn còn.

| Acceptance gốc | Lát cắt thực hiện / kiểm |
|---|---|
| UX-01, UX-05–08 | Control inventory active/legacy; màu chip; wording count/action; bỏ dev text, feedback hover/focus |
| UX-02, UX-02a, UX-03 | Product/Post preview parity, media geometry/presets, static header/footer/config |
| UX-04 | Query preview relation/order/featured/manual; ưu tiên native parity, approximation phải minh bạch. Endpoint/architecture mới cần decision riêng |
| TECH-01, TECH-07 | Native query, Woo inactive, conditional assets, actions/quote context/security |
| TECH-02, TECH-03 | Keyboard đúng instance, valid links không nested anchor; kiểm badge/media/title độc lập |
| TECH-04, TECH-05 | Product manual-only; Post autoplay/pause đúng effective state; reduced motion và controls scope |
| TECH-06 | Item count/loop/non-loop/resize; chứng minh theo version Swiper thực tế, không suy đoán clone behavior |
| TECH-08, TECH-08a | Legacy ratio và saved content; Product/Post/Grid compatibility, không bulk migration ngầm |
| TECH-09 | Init/cleanup/state owner, nhiều instance, editor request/render, network/listeners/memory/layout shift |
| TECH-10, TECH-11 | Human build/lint, source–artifact–deploy đúng version, test evidence và human visual approval |

TECH-04 Product autoplay cũ được superseded bởi manual-only ngày 19/10, không xóa acceptance Post. Source defects không liên quan không tự đưa thành refactor rộng.

## 6. Test matrix và bằng chứng

Chuỗi bắt buộc với setting: **control → attribute → preview → save → reopen → frontend**; reset và legacy cũng đi hết chuỗi.

- Product/Post; Grid regression; Woo active/inactive; old/new block, autoplay=true cũ của Product.
- 0/1/ít item/bằng số cột/nhiều item, Product tối đa 20; tên dài; thiếu ảnh/ảnh thật; nhiều thuộc tính; giới hạn 2/3/4 và +N.
- Normal/Wide/Full; viewport mobile/tablet/desktop; editor sidebar mở/đóng; nested Group/Columns; nhiều block.
- Loading/error/empty/results; SSR/init/ready/fallback/destroy; JS lỗi/tắt; ảnh chậm.
- Keyboard, focus visibility/order, screen reader labels; hover/touch; reduced motion; palette contrast.
- Đo page scrollWidth/clientWidth và component rect; không đòi track dài có scrollWidth bằng viewport. Không dùng overflow hidden toàn trang để che lỗi.
- Performance: ghi fixture, số instance, số lần mount/unmount/resize và số request/listener trước/sau. Không đặt PASS memory/CLS từ ảnh tĩnh hoặc grep source.

Agent tạo test doc `docs/testing/carousel-editor-ux-technical-1.3.18.md` trước onsite: URL/page, preconditions, exact steps, expected, pass/fail, build/commit, evidence. Dùng trang Product test onsite đã có nếu phù hợp; không tạo giả dữ liệu local để tuyên bố onsite PASS. Chưa có URL Post/nested fixture thì ghi cần human cung cấp, không đoán.

## 7. Thứ tự triển khai sau human duyệt

1. **Transition + baseline:** thực hiện §2, audit lại ID cũ, kiểm source–artifact; không sửa source trong task chỉ move docs.
2. **Editor preview:** implement UX-09–11 theo artifact; sửa các parity/labels trực tiếp liên quan; kiểm Product trước rồi Post.
3. **Style/typography:** chốt bảng token thực có → attrs/validation/render → controls/preview/reset. Mỗi nhóm chạy đủ save/reload/frontend, không giao riêng UI control bỏ backend.
4. **Technical carry-in:** query parity, links/keyboard, runtime states/loop/reduced motion/cleanup; chia sửa theo defect đã chứng minh.
5. **Regression + onsite:** chạy §6, build/lint do human theo AGENTS; agent đọc output, cập nhật PASS/FAIL từng ID; sửa FAIL trong scope rồi kiểm lại.
6. **Nghiệm thu:** bảng acceptance đủ evidence, outstanding rõ ràng và human approve. Không tự release/deploy/commit hoặc mở milestone kế tiếp.

File dự kiến: plugin `src/collection/*`, `src/product-collection/*`, `src/post-collection/*`, `src/collection-view.ts`, `modules/collection-render/*`; theme tokens/scoped styles khi ownership yêu cầu. Không mặc định sửa tất cả; lựa chọn theo baseline audit.

## 8. Decision memo — để human duyệt bước chuyển

1. **Vấn đề:** cần hoàn thiện editor và controls/style mà vẫn giữ đủ acceptance Technical đã dời từ 1.3.17.
2. **Phương án:** A — bắt đầu 1.3.18 theo các lát cắt §7 sau gate 1.3.17; phạm vi đầy đủ, cần nhiều đợt kiểm. B — ưu tiên preview/style trước và hoãn phần Technical còn lại bằng quyết định explicit; có UI sớm nhưng giữ debt và cần đích nhận, không gọi toàn 1.3.18 hoàn tất.
3. **Khuyến nghị:** A, làm tuần tự preview → style → hardening; sửa technical blocker ngay ở lát cắt gặp nó. Không bỏ Technical chỉ vì phần nhìn thấy đã đẹp.
4. **Bằng chứng:** 040 §4–5 có inventory defects; §13 có prototype/evidence. Source `src/collection/style.css` selector `.skvn-collection-card__cta` hardcode màu; `modules/collection-render/cards.php` render title/action; `src/collection/controls.tsx` là control/preview baseline. Screenshot human 19/10 xác nhận nhu cầu title/link và style.
5. **Tự kiểm chứng (≤10 phút):** mở artifact HTML/MD (3 phút), đọc 040 §4–5 và bảng §4–5 trên (4 phút), tìm selector CTA và controls màu/font (2 phút). Phân biệt prototype với source production.
6. **Đảo ngược:** planning/controls có thể sửa lại; saved attributes cần backward compatibility nên chỉ bổ sung có default/fallback, không bulk migrate/delete legacy values.
7. **Default lịch sử:** đề xuất này đã được thay bởi transition human ngày 19/10. Current là 1.3.18; không chuyển lại milestone. Lượt planning 20/10 không tự bắt đầu implementation Editor UX.

## 9. Baseline và override hiện hành — 2026-10-20

Human đồng ý phạm vi Editor UI/UX và yêu cầu planning chi tiết. Đây là kế hoạch bàn giao; chưa phải lệnh code toàn bộ.

- **Không sửa skeleton trong đợt này.** Ý tưởng skeleton toàn trang không triển khai. Hiện tượng chớp khi load chưa xác định nguyên nhân, không tuyên bố đã sửa. Gói D/test D-01…08 giữ truy vết, không tự xóa hoặc đánh PASS; sửa D không là dependency để làm preview.
- Arrow centering đã có source sửa riêng (SVG + reset padding), chờ onsite theo `docs/testing/carousel-arrow-centering-1.3.18.md`.
- Width A và grouped attributes B giữ kết quả human nghiệm thu trong phạm vi cũ; kiểm regression khi shared code thay đổi.
- C/state owner, Post visual, controls hide/disabled, G-MEDIA và preview giữ acceptance carry-in. Kiểm lại C trước thay runtime; editor source có thể làm độc lập.
- §2/§8 là lịch sử trước transition, không phải yêu cầu chuyển lại milestone. §7 được thay bằng thứ tự chi tiết §12.

### Source baseline

Đường dẫn `src/` và `modules/` dưới đây tính từ `wp-content/plugins/skvn-marine-blocks/`.

| Surface | File/symbol | Quan sát source, không phải onsite PASS |
|---|---|---|
| Inspector/preview | `src/collection/controls.tsx`: CollectionEdit, PlaceholderCard, EditorInfoPanel | Panel Content/Query/Layout/Card/Actions/Carousel; Advanced rỗng; còn roadmap woo-catalog |
| Query | `useQueryPreview` trong cùng file | Product cap 20, Post cap 5; featured/manual cùng map menu_order; chưa truyền relation; chưa có nhánh error riêng |
| Schema | Product/Post `block.json`, `src/collection/types.ts` | Inventory key/default/consumer trước thêm; không rename chỉ để đổi label |
| Frontend | `modules/collection-render/cards.php`, callbacks Product/Post | Đích kiểm output thật, không suy kết quả từ card mẫu |
| Tokens | `wp-content/themes/skvn-marine/theme.json` | 10 màu, 3 font-role presets và font-size presets có sẵn |
| Typography | `docs/decisions/typography-scope-and-font-loading.md` | Theme owns delivery; style chỉ canvas/frontend, không admin chrome |

Re-audit findings trước sửa vì working tree có nhiều task. Không coi source findings lịch sử 040 là lỗi runtime hiện tại.

## 10. Đặc tả từng hệ thống

### S1 — Preview shell, width và thông tin

**Owner:** plugin editor. **IDs:** UX-03/08/09/11. Block UI N/A cho DOM editor-only; vẫn kiểm frontend regression.

Thứ tự: heading/intro theo setting → card minh họa căn giữa → cấu hình nhãn–giá trị → query full-width → tóm tắt footer hiện hành.

- Card chiều cao tự nhiên, nhãn “Dữ liệu mẫu”, không stretch theo query. Toggle ảnh phản ánh thật; equal-height chỉ mô tả vì một card không chứng minh cân chiều cao.
- Summary lấy attrs thật: ảnh, fields, action, equal-height, responsive preset. Bật ảnh không đồng nghĩa mọi item có ảnh. Không hiển thị roadmap/version/dev placeholder.
- Viền nhẹ quanh geometry thật, label Normal/Wide/Full; default Wide cho carousel mới. Không tự ghi đè saved width cũ. Canvas hẹp có thể làm ba mode rộng bằng nhau.
- Layout theo container, gồm sidebar mở và Group/Columns. Breakpoints 380/700px artifact là điểm thử, chưa là token production. Một nguồn xác định số cột và hạn mức query để CSS/JS không lệch nhau.
- Không copy lab controls hoặc khóa sidebar 320px của artifact. Không dùng viewport bleed/overflow hidden toàn trang.
- Preview không Swiper, không fetch chi tiết từng item. Thiếu heading/footer thì không giữ khoảng trắng giả.

**Output:** preview components trong `src/collection/`, CSS scoped editor; chỉ tách file khi giúp đọc/kiểm, không tạo framework chung toàn plugin. Kiểm card rect không đổi khi expand query, keyboard và container 320px.

### S2 — Query preview và danh sách

**Owner:** plugin editor/query; PHP giữ source of truth frontend. **IDs:** UX-04/10, TECH-01/07/12.

- Danh sách đọc theo hàng: 3/2/1 cột, hiện 9/6/3 item; tên đầy đủ và wrap, số thứ tự nhỏ, không giả thành nút. Decode entities thành text an toàn, không inject HTML title; title trống có fallback.
- “Xem thêm N” tính trên kết quả preview đã lấy, không phải toàn catalog. Expand không tăng `itemsToShow`; state expanded không lưu vào block.
- Đổi query reset collapsed; resize giữ expanded/focus. Thu gọn không làm focus mất vào body; nếu nút biến mất chuyển focus về heading danh sách.
- State riêng: loading/results/empty/error/Woo unavailable. Không coi null luôn là loading; kiểm resolver/error API theo package đang dùng. Không stale response ghi đè query mới, không trình kết quả cũ như kết quả mới.
- Lập parity matrix: categories/tags/relation/newest/featured/manual/shuffle/count. Hỗ trợ native thì map đúng; chưa parity phải nói giới hạn cụ thể, không chỉ “Approximate list”. Shuffle không hứa thứ tự giống giữa hai request độc lập.
- Giữ cap Post 5 ở lát cắt đầu, ghi rõ chỉ preview tối đa 5 bài; không giả toàn bộ kết quả hoặc tự tăng thành 20. Product giữ tối đa 20.
- Không thêm endpoint/cache/dependency mới. Nếu exact parity cần endpoint mới, ghi gap để quyết riêng; vẫn hoàn thành preview trung thực. Không retry loop; lỗi có thao tác thử lại rõ ràng.
- Đổi màu/font không fetch lại query; không fetch N chi tiết sản phẩm để dựng card minh họa.

**Output:** typed result items/status/limitations, adapter tách view khi cần; parity matrix, IDs và network evidence. Kiểm query đổi nhanh, empty/error, 20 tên dài và keyboard disclosure.

### S3 — Inspector và hiệu lực settings

**Owner:** plugin controls/schema. **IDs:** UX-01/05–08.

| Nhóm | Nội dung | Quy tắc |
|---|---|---|
| Content | Heading/intro, Query, Fields, Actions, Footer | Query là subgroup; phân biệt tổng item và số card nhìn thấy |
| Style | Media layout, Title, Details/chips, Action | Theo vai trò, theo Product/Post |
| Layout | Width, responsive preset, equal-height, arrows/dots | Navigation chỉ carousel |
| Advanced | Accessible label và options nâng cao còn tác dụng | Không panel rỗng, không thêm option để lấp nhóm |

Inventory bắt buộc trước code: label/key/type/default/group, điều kiện hiện/disabled, preview/PHP consumer, legacy policy và test ID.

Quy tắc đề xuất: hide setting không áp dụng theo type/layout; disable kèm help nếu tạm vô hiệu do setting cha hoặc bị preset khóa. Legacy key không còn consumer thì ẩn khỏi lựa chọn mới nhưng giữ schema/data. Kiểm `cardStyle`, Price/SKU/Stock/categories/Post tags trước kết luận; không xóa acceptance variants bằng cách ẩn control.

Product không hiện autoplay/delay và saved autoplay=true không chạy lại. Post giữ scope cũ, không thêm loop setting. “Both” phải giải thích đúng view link ở ảnh/title và CTA, không hứa hai nút nếu renderer chỉ có một. Labels/help dùng text domain plugin.

**Output:** control inventory và UI nhóm mới; mỗi control đi đủ control→attribute→preview→save/reopen→frontend.

### S4 — Màu title, action và chip

**Owner:** theme palette/tokens; plugin lựa chọn, validation và consuming styles có fallback. **IDs:** UX-05/12, UI-18-TITLE/STYLE, TECH-13.

- Title giữ anchor/URL; default có diện mạo title, không nhận xanh/gạch chân ngoài ý muốn từ global link. Scoped collection, hover/focus rõ, visited vẫn đọc được.
- Card action có bốn token: background/text normal và background/text hover. Focus-visible có indicator độc lập. Áp cùng bộ màu cho action buttons trong một block, không per-item.
- Footer archive/catalog là surface riêng, không tự nhận màu card CTA. Post read-more vẫn là link, không ép thành button; background controls không hiện nếu không có consumer.
- Chip color phải resolve mọi option được expose; dùng palette theme, không thêm bảng màu riêng.
- Reset theo nhóm chỉ clear overrides nhóm đó, không xóa query/URL/action mode/layout/media preset. Không lưu state hover.
- Màu lưu slug, không raw hex/computed CSS. PHP allowlist palette/escape; token thiếu fallback an toàn và editor báo giá trị không còn khả dụng, không tự overwrite saved data lúc mở.
- Kiểm contrast normal/hover/focus với màu thực. Palette có sẵn không có nghĩa mọi cặp màu đều dễ đọc; default phải đạt đọc/focus rõ, dùng help/cảnh báo hiện có khi cần, không xây color-audit system toàn site.

**Output:** attrs/types/validation/render/control theo từng lát cắt; computed colors hai surfaces và test hai block có style khác nhau.

### S5 — Typography theo vai trò

**Owner:** theme font/delivery, plugin chọn preset. **IDs:** UX-13, UI-18-STYLE, TECH-13.

| Vai trò | Product | Post | Bounded sizes đề xuất |
|---|---|---|---|
| Title | Tên sản phẩm | Tên bài | Inherit, base, lg, xl, 2xl |
| Details | Label/value thuộc tính, metadata | Date/author/excerpt | Inherit, sm, base, lg |
| Action | Card CTA | Read-more | Inherit, sm, base, lg |

Subset này lấy từ tokens thực có, là đề xuất trước code. Không expose display/3xl/4xl/5xl cho card trong đợt này. Section heading/intro không gộp vào card title.

Font: Inherit + `skvn-system`, `skvn-heading`, `skvn-ui`, label/availability từ theme. Đây là **vai trò font**, không đảm bảo ba font khác nhau. `skvn-system` thực tế trỏ `--skvn-font-body`; help phải làm rõ kế thừa typography site. Không thêm Google Font/CDN/upload font.

- Inherit/reset về baseline đúng vai trò, không ép mọi text thành body.
- Override chỉ áp vai trò trong block, không đổi admin/sidebar hay block khác.
- Full title wrap, details wrap, CTA dài không tràn; kiểm line-height khi chọn size lớn. Không mở thêm weight/line-height/letter-spacing controls trong gói này.
- Kiểm font vars resolve thật trong canvas/frontend, đặc biệt `--skvn-font-ui`; slug JSON không chứng minh font đã tải. Fallback an toàn nếu theme thiếu token.

**Output:** token options/default table, schema+controls+consumer, test role độc lập và save/reset/reopen.

### S6 — Media presets và legacy

**Owner:** plugin attrs/normalization, theme spacing/radius tokens. **IDs:** UX-02/02a, UI-03/03a, TECH-08/08a; carry G-MEDIA.

- Product: frame 1:1, contain; ba preset Sát mép / Có khoảng đệm / Nổi phía trên. Ảnh nguồn không bị bóp vuông; khoảng trống contain là dự kiến.
- Sát mép theo card width/radius; padded dùng cùng padding top/left/right, inner radius = max(0, card radius − padding); Floating có offset giới hạn và clearance, không clip ảnh hoặc chồng heading/navigation.
- Help nói ảnh transparent phù hợp Floating, không tự xóa nền. Không ảnh có fallback đúng geometry; showImage=false không giữ khoảng trống offset.
- Trước code có truth table: old explicit ratio, old missing key, new block, mỗi preset, Grid/Post. Bảo toàn explicit legacy, new Product theo hướng square; không bulk rewrite khi load editor.
- Nếu thiếu key phân biệt layout/legacy, chuẩn bị schema/effective-value table trước triển khai S6. Không đặt default khiến PHP không phân biệt old/new rồi âm thầm đổi output.
- Post visual duyệt riêng; không áp square/Floating Product sang Post chỉ vì chung component. S1–5 chỉ phản ánh media thật đang có, không giả ba preset đã chạy khi S6 chưa xong.

**Output:** media/legacy decision table, preview/renderer matching, paired screenshots và serialized old/new evidence.

#### S6.1 — Product media controls đã chốt ngày 2026-10-21

Human duyệt hướng từ artifact `docs/artifacts/collection-media-frame-test.html`: **ảnh trong suốt nhô lên trên mép card; card và phần body giữ nguyên vị trí**. Artifact là công cụ chọn cảm giác thị giác, không phải source hoặc bằng chứng WordPress runtime. Quyết định sau cùng dùng **chiều rộng card** làm mốc kích thước ảnh; đề xuất cũ tính theo chiều cao media được thay thế. Ba preset Product vẫn là Sát mép / Có khoảng đệm / Nổi phía trên. Post cần duyệt riêng.

| Setting | Điều kiện hiện | UI và dữ liệu cần lưu | Hiệu lực / giới hạn |
|---|---|---|---|
| Media layout | Product + Show image | Chọn một trong ba preset | Control cha; khi tắt ảnh không giữ khoảng trống nhô hoặc bóng. |
| Kích thước ảnh nổi | Preset Nổi phía trên | Slider, % **chiều rộng card**; hiện số đang chọn | Giữ tỉ lệ ảnh nguồn, `contain`; giới hạn chiều rộng để không tràn ngang. Không lưu pixel đo từ DOM. |
| Độ nhô | Preset Nổi phía trên | Slider, giá trị có giới hạn; hiện số/đơn vị | Chỉ dịch **ảnh** qua mép trên card; có clearance cho navigation/heading và giữ phần ảnh đủ nhìn thấy trong card. Card/body không dịch. |
| Bóng ảnh | Preset Nổi phía trên | Một nhóm riêng với bật/tắt | Bóng theo biên ảnh trong suốt, độc lập với shadow card; tắt thì các giá trị con không có hiệu lực. |
| Lệch bóng ngang X / dọc Y | Bóng ảnh bật | Hai ô số có dấu âm/dương, kiểu nhập như spacing; chọn đơn vị `px`/`rem` | Không dùng bốn cạnh kiểu margin/padding vì bóng chỉ có hai trục; giới hạn và sanitize từng trục. |
| Độ nhòe / độ đậm bóng | Bóng ảnh bật | Hai slider độc lập, hiện giá trị; độ đậm là % | Nhãn tách blur khỏi opacity, tránh cả hai cùng gọi “độ mờ”. Không expose raw CSS/màu tùy ý. |
| Khoảng đệm | Preset Có khoảng đệm | Ô số + select `px`/`rem`, giá trị có giới hạn | Áp padding top/left/right của frame; inner radius theo quy tắc S6. Không hiện trong preset khác. |

Inspector dùng **hiện/ẩn theo setting cha**: chỉ có nhóm tương ứng với preset đã chọn; chỉ mở các giá trị bóng khi bật Bóng ảnh. Không thêm toggle cho từng con số. Giá trị ẩn không được âm thầm điều khiển preset khác; quy tắc lưu/giữ override khi chuyển preset phải được viết trong bảng effective values trước code và kiểm save/reopen/reset. Range, step và default cụ thể là việc cần xác định bằng artifact + responsive QA trước implementation, **chưa được human duyệt như con số production**.

Implementation gate: audit DOM/CSS hiện có trước khi viết layout. `.skvn-collection-card` đang `overflow: hidden`, ảnh đang `object-fit: cover` (`src/collection/style.css`), nên chỉ thêm attrs/Inspector sẽ cắt phần nhô và bóng. Chọn owner cho media frame, clipping và khoảng trống phía trên; kiểm card/body đứng yên, không dùng page-level overflow để giấu lỗi. Nếu media cũng là link, giữ semantics và focus hợp lệ, không tạo nested anchor. So sánh editor/frontend cùng dữ liệu, card width khác nhau, ảnh trong suốt/ảnh thường, thiếu ảnh, Grid/Carousel, mobile và nhiều instance. Không coi PNG thử nghiệm là đại diện mọi ảnh sản phẩm.

### S7 — Technical và regression

**IDs:** TECH-01–13, TECH-08a. Chỉ sửa defect tái xác nhận, không refactor runtime rộng.

1. Bảng effective values cho valid/missing/invalid attrs; fixtures đồng bộ TS/PHP. Không lưu query results/card snapshots/expanded state/computed pixels.
2. State owner section `.skvn-collection` theo module contract; kiểm lại C/SSR/ready/fallback, không đổi skeleton để phục vụ editor.
3. Native query, quote context, safe URLs/escaping; nếu nested anchor còn tồn tại thì sửa atomic. CF7 ngoài phạm vi.
4. Keyboard đúng instance, không bắt phím khi focus input/instance khác; Product manual-only, Post pause/reduced-motion theo effective state.
5. Loop/count/resize, destroy/reinit và nhiều instances; Grid không init carousel, editor không Swiper.
6. Cleanup observer/listener/request; style changes không fetch; query nhanh không stale results. Kiểm payload/render theo baseline, không hứa tối ưu bằng cảm tính.
7. Đổi PHP require/include thì kiểm packaging theo workflow, không gửi zip thiếu runtime files.

## 11. Requirement register và bằng chứng

States của mỗi row theo S tương ứng. ID con giữ truy vết ID gốc §4–5/040; không thay acceptance cũ. Evidence onsite mới **chưa có**.

| ID | Nhóm | Hành vi mong đợi | Hiện trạng | Việc cần sửa | Cách kiểm | Evidence |
|---|---|---|---|---|---|---|
| UX-09.a | Editor UX | Card gọn, summary thật | Chưa làm S1 | Shell/summary | Expand query, toggle image | E01 ảnh/card rect |
| UX-11.a | Editor UX | Khung đúng width | Chưa làm | Wrapper/label | 3 widths/sidebar/nested | E02 rect/scrollWidth |
| UX-10.a | Editor UX | 9/6/3, full names/focus | Chưa làm | List/disclosure | 20 tên, resize/tab | E03 video/focus |
| TECH-12.a | Technical | State không gây hiểu sai | Source thiếu error branch | Typed state/adapter | Error/Woo off/query nhanh | E04 network/state |
| UX-04.a | Editor UX | Exact/approx phân biệt | Source có parity gaps | Mapping/help | So IDs/filter/order | E05 parity matrix |
| UX-01.a | Editor UX | Settings có tác dụng | Advanced rỗng | Inventory/group/visibility | Save/reopen từng control | E06 inventory |
| UX-12.a | Editor UX | Action colors/reset đúng | Chưa làm | End-to-end tokens | 4 màu, hai blocks | E07 attrs/computed |
| UI-18-TITLE.a | Block UI | Title link có style đúng | Human báo link ngoài ý muốn | Scoped states | Click/tab/hover/visited | E08 URL/styles/ảnh |
| UX-13.a | Editor UX | Font/size độc lập | Chưa làm | Role controls/resolver | Max sizes/reset/reload | E09 computed fonts |
| UX-05.a | Editor UX | Chip color đúng option | Chờ kiểm chứng | Palette mapping | Mọi màu exposed | E10 mapping/colors |
| TECH-13.a | Technical | Invalid token an toàn | Chưa làm | Allowlist/fallback | Malformed/missing tokens | E11 fixtures/DOM |
| UX-02a.a | Editor UX | Media đúng output | Carry G-MEDIA | S6 sau legacy table | 3 presets/no image | E12 paired images |
| UX-02a.b | Editor UX | Product Floating có controls đúng ngữ cảnh; kích thước theo card width, bóng/đệm hiện khi áp dụng | Human chốt UI 21/10; production chưa làm | S6.1 controls, attrs, preview và reset | Từng control → save/reopen → frontend; đổi preset/showImage | E12a attrs + paired images/Inspector |
| TECH-08a.a | Technical | Legacy/Post/Grid bảo toàn | Chờ kiểm chứng | Compatibility | Old/new/save/reopen | E13 serialized diff |
| TECH-08a.b | Technical | Giá trị media bounded, không cắt ảnh/bóng hoặc đổi layout cũ ngầm | Source hiện có clipping/cover; chưa implement | Effective-value table + media frame/clipping scoped | Ảnh trong suốt/thường/thiếu; Grid/Carousel, old/new, mobile, scrollbar | E13a DOM/rect + serialized diff |
| UI-18-STYLE.a | Block UI | Text/CTA không tràn | Chờ kiểm chứng | Wrap/scoped fallback | Mobile/max size/long text | E14 rect/contrast |
| TECH-09.a | Technical | Không fetch/leak dư | Chờ kiểm chứng | Query keys/cleanup | 10 style edits, 10 mount/remove | E15 network/trace |

A11y/responsive/performance kiểm theo từng surface. Evidence ghi build, URL, viewport/container, setup, expected/actual, ngày kiểm. Không dùng prototype 172/172 hoặc grep source thay PASS WordPress.

## 12. Gói triển khai và dependencies

| Gói | Phụ thuộc | Deliverable | Gate |
|---|---|---|---|
| P0 Baseline | Context/current/working tree | Inventory controls, query parity, tokens/defaults, carry status | Biết consumer từng setting, không overwrite dirty work |
| P1 Preview | P0 | S1 + S2 list presentation, Product rồi Post | E01–03, frontend không regression |
| P2 Query/Inspector | P0 parity + P1 | S2 states/limitations + S3 | E04–06, saved query/actions nguyên vẹn |
| P3 Style/Type | P0 tokens + P2 | S4/S5: title → CTA → details/chips | E07–11/E14, save/reopen/parity |
| P4 Media | P0 + legacy table rõ; S6.1 controls/range/default table | S6/G-MEDIA Product, Post visual riêng | E12–13 + E12a/E13a; không suy PASS từ artifact |
| P5 Technical QA | P1–4 + baseline C | S7, fix proven defects | TECH cũ+mới có evidence, D track riêng |
| P6 Review | Human build/test output | PASS/FAIL/pending register, docs/module cập nhật | Human nghiệm thu, không tự đóng milestone |

Mỗi task ưu tiên 3–5 files; schema/render/editor/style cần nhiều hơn để nối đủ đường đi thì ghi rationale. Không giao song song nhiều agent sửa controls.tsx/style.css/cards.php. Lượt này chưa dispatch implementation.

Điểm còn cần quyết tại đúng gói: hide/disabled inventory (P2), bounded size/default visual (P3), **con số range/default Product media, media schema/legacy và Post visual** (P4). Hướng Product Floating/controls ở S6.1 đã chốt; không hỏi lại mốc kích thước theo card width hoặc cơ chế hiện/ẩn. Chỉ hỏi phần chưa chốt với evidence; không hỏi lại width/full title/9-6-3/manual Product/không sửa skeleton.

## 13. Test, handoff và completion

Runbook: `docs/testing/carousel-editor-ux-technical-1.3.18.md`. Agent chuẩn bị source/diff/fixtures; human build/lint và onsite theo AGENTS. Kết quả theo từng ID, thiếu kiểm thì giữ Chờ kiểm chứng.

Mỗi gói bàn giao files, rationale, source findings, kiểm đã làm, command chính xác, URL/setup/evidence cần trả. Rollback chỉ task-owned changes; giữ attrs/data tương thích, không reset working tree.

Milestone chỉ nghiệm thu khi acceptance đã kiểm hoặc human quyết carry/defer có đích rõ. “Không sửa skeleton đợt này” không xóa debt D hoặc biến chớp thành đã giải quyết. Không bump/release/commit/deploy trong task planning.
