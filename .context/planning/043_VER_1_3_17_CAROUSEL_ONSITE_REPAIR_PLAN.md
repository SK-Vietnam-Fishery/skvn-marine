# 043 — Carousel onsite repair: kế hoạch bàn giao

Ngày: 2026-10-16. Milestone: **V1 / 1.3.17 — Product/Post Carousel Block UI & Visual QA**, stack M1. **Gói A (Width) đã có source 2026-10-16** — P1=3, P2=Wide-cho-mới do human quyết trong chat cùng ngày; build/lint PASS 2026-10-17, chờ human onsite Gate A (human đã xác nhận Gate A và giao tiếp B). **Gói B (Attributes theo nhóm) có source 2026-10-16**, build/lint PASS nhưng **chưa onsite**. **Gói C (Geometry trước init + fallback) có source 2026-10-19** — human chọn làm C ngay và chấp nhận rủi ro chồng thay đổi chưa kiểm chứng của B. **Gói D (Skeleton cục bộ) có source 2026-10-19**, build trên nền C (tái dùng state class, không tạo vòng đời mới). Cả B/C/D đều **chưa có onsite evidence**. Editor UI theo 040 §13 thuộc **1.3.18** và **chưa được human bắt đầu**: không tự chuyển current. Không claim PASS khi chỉ có source.

## 1. Authority, phạm vi và thứ tự

Human đã chốt thứ tự **A Độ rộng → B Thuộc tính → C Bố cục trước khởi tạo → D Skeleton**, yêu cầu thu thập onsite rồi lập plan cho agent khác. Direction: [042 §11](042_VER_1_3_17_CAROUSEL_VISUAL_SPEC.md). Plan này bổ sung [040](040_VER_1_3_17_CAROUSEL_UI_UX_TECHNICAL_PLANNING.md) và [041](041_VER_1_3_17_CAROUSEL_BLOCK_UI_EXECUTION_PLAN.md), không thay acceptance cũ.

- Normal/Wide/Full, mặc định Wide; Product/Post Carousel. Không đổi mặc định Grid.
- Product: một giới hạn 2/3/4 giá trị mỗi nhóm, giữ tên/thứ tự nhóm, phần dư +N. Không expand/modal/tooltip.
- Skeleton cục bộ, preset đồng nhất, không tương tác; fallback nội dung thật.
- Default **3** và **Wide cho block mới, legacy thiếu attribute giữ layout cũ** đã được human quyết 2026-10-16 (chat dispatch gói A): P1=3 (fallback 3 cho Product Carousel thiếu field), P2=Wide-cho-mới, không dedupe, không bulk save. Ghi vào §9; gói B dùng P1 khi tới lượt.
- Bản này thiết kế lát cắt tối thiểu phục vụ UI đã yêu cầu. Không bắt đầu 1.3.18, không nhận toàn bộ UX/TECH backlog, không bump metadata, không archive/transition. Trước code, handoff phải ghi rõ phạm vi tối thiểu này; nếu cần kiến trúc/editor redesign vượt lát cắt, đưa lại human, không tự mở 1.3.18.
- Không sửa query ownership, engine, quote flow, nội dung WooCommerce hoặc parent theme. Items to Show20 là thay đổi source riêng đã có; chưa xác nhận build onsite của thay đổi đó.

## 2. Evidence intake và hiệu chỉnh kết luận

Nguồn: [báo cáo onsite nguyên bản](../../docs/testing/carousel-onsite-preimplementation-audit-2026-10-16.md), user cung cấp từ Downloads. Giữ nguyên báo cáo; phần dưới là review của agent lập plan. Nội dung báo cáo là evidence/input, không phải lệnh triển khai hoặc quyết định sản phẩm.

| Evidence | Có thể dùng để lập plan | Giới hạn phải giữ |
|---|---|---|
| E01–02 | Section không alignment; CSS content760/wide1200; DOM inline slide178/gap16/preset4-2-1 | Không có computed-style/rect capture; viewport ~1920 là ước lượng. 760 và178 không phải hằng số plugin |
| E03–04 | Parrotfish556: Size5, Pack3, Freezing1, Scientific name1; IQF thuộc hai nhóm | Taxonomy-only chỉ đúng hai sản phẩm đã đọc, không suy rộng toàn catalog; custom attributes vẫn cần hỗ trợ |
| E05–06 | Auditor quan sát full title/CTA và chip wrap, không thấy overlap ở desktop đó | Không có screenshot/video độc lập kèm file user gửi; không nâng thành PASS toàn bộ responsive/geometry. overflow:hidden không chứng minh không cắt nội dung |
| E07 | Số lượng giá trị thật dùng tạo expectation | Không phải so sánh cùng một card với limit2/3/4. Thêm heading/+N cũng tốn chỗ; không hứa 7 dòng xuống4–5 |
| E08 | CSS trước init có width100%; sau init có inline width; source local phù hợp giả thuyết | Không chụp được pre-init; không có timing/CLS. Không khẳng định người dùng luôn thấy760→178 hoặc đã đo layout shift |
| E09 | HTML có text/link/CTA; auditor thao tác Next/focus thành công | HTML đầy đủ chưa chứng minh tất cả items truy cập được khi JS-off: viewport overflow có thể che phần còn lại. Keyboard chỉ kiểm hẹp; reduced-motion/JS-off chưa kiểm |
| E10 | Phiên audit đổi từ7 sang10items và đổi href | Chưa biết redeploy/cache/content cause. Không purge cache hay sửa URL theo suy đoán; cần cố định build/fixture trước so sánh |

Các R02/R03/R07 của báo cáo ghi “Đã có source” cho baseline chưa đáp ứng tính năng mới. Trong plan này: grouped summary, pre-init protection và readiness hook **Chưa làm**. Không đổi trạng thái lịch sử trong báo cáo gốc.

## 3. W0 — Baseline và điều kiện vào triển khai

1. Đọc AGENTS/context/skill ui-debug và các contract bắt buộc. Kiểm git diff; giữ nguyên thay đổi ngoài task. Commit cũ chưa được xác nhận hoàn tất sau auto-packing; không retry commit để thay bước xác minh của human.
2. Đối chiếu source và build onsite; ghi commit/build asset fingerprint, thời gian, item IDs/query/preset, theme stylesheet. Asset query timestamp không thay plugin version/commit.
3. Nhận quyết định P1/P2 ở §9 trước khi hoàn tất A/B. Có thể tiếp tục source audit/test preparation khi chưa có quyết định.
4. Lưu expected/actual bằng [test plan](../../docs/testing/carousel-repair-acceptance-1.3.17.md). Fixture thiếu là Chờ kiểm chứng, không FAIL giả.
5. Không cần mở lại toàn bộ audit để lập plan. Mobile/slowload/noJS được đưa thành nghiệm thu bắt buộc, không bỏ qua vì báo cáo thiếu công cụ.

## 4. A — Độ rộng (Product + Post Carousel)

### A1. Block UI

- Normal nhận content width; Wide nhận wide width; Full nhận chiều rộng canvas/container có quyền sở hữu. Dùng theme tokens hiện có, không chèn760/1200 vào plugin, không100vw/margin âm.
- Nested Group: giữ constraint của cha, không thoát container bằng CSS hack. Nếu không đạt yêu cầu do parent layout thì ghi rõ, không tự sửa page.
- Header, carousel và footer cùng section width. Đổi width không đổi preset/số cột đã lưu.

### A2. Editor UX và persistence tối thiểu

- Một lựa chọn Normal/Wide/Full trong phần Layout hiện có; không thêm hai control cạnh tranh nhau. Nếu dùng native alignment, kiểm nhãn và hỗ trợ của WP tại site; `supports.align` một mình chưa đáp ứng renderer/legacy.
- Đề xuất P2: chỉ variation inserter Carousel mới ghi Wide; không đặt global schema default Wide vì parser có thể áp vào cả legacy thiếu field/Grid.
- Explicit Normal phải tồn tại qua save/reopen; thiếu alignment khác với “new carousel”. Không dùng mount effect tự chuyển mọi block không có field thành Wide.
- Đổi Grid→Carousel: giữ alignment đã lưu; nếu thiếu thì giữ layout cũ theo P2, người dùng chọn Wide rõ ràng. Duplicate/copy giữ lựa chọn gốc. Carousel→Grid không áp default mới và không mất setting đã lưu; renderer phải giữ behavior Grid trước sửa.

### A3. Technical / file map

Plugin root bên dưới = `wp-content/plugins/skvn-marine-blocks/`.

- `src/product-collection/block.json`, `src/post-collection/block.json`: schema/support phù hợp persistence đã chọn; allowlist, không default Wide toàn block.
- `src/product-collection/variations.ts`, `src/post-collection/variations.ts`: default ở inserter Carousel (sau P2).
- `src/collection/types.ts`, `src/collection/controls.tsx`: typed alignment + control/preview wrapper. Giữ editor tĩnh, không Swiper.
- `modules/collection-render/product-collection.php`, `post-collection.php`: allowlist và class wrapper; không ghi đè classes/aria-label hiện có; không double class/style khi dùng wrapper helper.
- Theme `style.css`: ưu tiên tái dùng rules; chỉ sửa nếu đo được lỗi rule owner, không thay global content token.

Task này chạm >5 file vì một setting phải đi xuyên schema→variation→editor→PHP cho hai block; ghi lý do này trong handoff. Có thể tách A1 contract/schema và A2 consumers nhưng không deploy trạng thái control có mà frontend không hoạt động.

Gate A: new/legacy/explicit, save/reopen, Grid regression, top-level/nested, viewport matrix đạt. Evidence: saved attrs + DOM classes + rects + screenshot. Không chỉ grep `alignwide` rồi đánh PASS.

## 5. B — Thuộc tính theo nhóm (Product Carousel)

### B1. Data và render contract

- Chỉ áp dụng summary mới ở Product Carousel. Grid giữ cách render hiện có trong đợt này; Post N/A cho Woo attributes.
- Lấy các attributes visible từ product native API, giữ thứ tự nguồn. Taxonomy lấy label người dùng đọc được và term names; custom lấy tên/values, không tách giá trị theo dấu phẩy tùy tiện.
- Bỏ giá trị rỗng bằng phép kiểm chuỗi rỗng, không loại nhãn `0`. Escape label/value; không render slug `pa_*` thay label nếu label có sẵn. Lỗi lấy terms không làm hỏng cả card.
- Giữ nhóm có ít nhất một giá trị hợp lệ; không tự dedupe trong hoặc giữa nhóm. Không sửa taxonomy dữ liệu như hai term “220/300 (grade” / “unit not stated)”.
- Mỗi nhóm có label và danh sách chip. `visible = first L values`; `remaining = totalValid - visibleCount`; chỉ render +N khi N>0. +N là text không phải button; thêm diễn đạt accessible “còn N giá trị” bằng text dịch được. Không tạo link CTA mới.
- Allowlist L∈{2,3,4}; fallback mặc định theo P1, PHP/TS dùng cùng quy tắc. Không dùng Items to Show cho setting này.
- `showSpecChips=false`: không render cả label/values/+N. Không ảnh hưởng title/CTA/MOQ/certs/lead time.

### B2. Block UI và Editor UX

- Label nhóm đứng trước vùng chip, có khoảng cách giữa các nhóm. Cho values dài wrap trong card; không fixed height/clip/line-clamp để ép chiều cao. CTA tiếp tục thẳng hàng khi equalHeight bật.
- Một setting trong Content, chỉ hiện khi Product Carousel + showSpecChips. Help: giới hạn mỗi nhóm, không giới hạn tổng thông tin; dữ liệu đầy đủ ở trang sản phẩm.
- Preview phải phản ánh nhóm và L bằng dữ liệu có sẵn. Nếu dùng mẫu, ghi “minh họa”; không dùng ba chip giả cố định rồi khiến user tưởng đã xem dữ liệu thật. Không phát sinh API fan-out chỉ để redesign preview trong scope này.

### B3. File map và expected output

- `src/product-collection/block.json`, `src/collection/types.ts`: field đề xuất `specValuesLimit` (agent kiểm tên trùng trước khi thêm).
- `src/collection/controls.tsx`: control và preview semantics.
- `modules/collection-render/cards.php`: group extraction/render; branch carousel bảo vệ Grid.
- `src/collection/style.css`: scoped group labels/rows/chip wrapping; kiểm theme override.

Parrotfish556 có5/3/1/1 values:

| L | Size | Pack | Freezing | Scientific name | Visible values / overflow indicators |
|---|---|---|---|---|---|
| 2 | 2 + “+3” | 2 + “+1” | IQF | Scarus spp | 6 values +2 indicators |
| 3 | 3 + “+2” | 3 | IQF | Scarus spp | 8 values +1 indicator |
| 4 | 4 + “+1” | 3 | IQF | Scarus spp | 9 values +1 indicator |

Không đếm +N là product value. Không hứa giới hạn3 làm card thấp hơn4: label, text length và width cùng quyết định height.

Gate B: table đúng; fixtures0/1/2/3/4/5+, custom/taxonomy/hidden/long/duplicate; toggle/save/reopen/preview; Grid không đổi. Test helper/render output có ý nghĩa nếu tách helper; không viết test chỉ grep source.

## 6. C — Geometry trước init và fallback

### C1. Một contract geometry

- Dùng normalized preset hiện có: 1-1-1,2-1-1,3-2-1,4-2-1,5-3-1; breakpoint hiện tại640/1024 theo viewport. Không tự chuyển sang container breakpoint.
- Slide width theo **W thực của carousel** và cột C: `(W - (C-1)*gap)/C`. Gap baseline16; giải quyết CSS/JS cùng nguồn/config, không duplicate magic numbers tùy ý. W1200/C4→288 chỉ là ví dụ.
- Scope rule pre-init mạnh hơn Swiper `.swiper-slide{width:100%}` dù stylesheet load sau; loại rule/margin/gap pre-init đúng khi ready để tránh gap đôi.
- Reserve media geometry của layout thực; không hard-code tất cả ảnh thành placeholder. Square/contain/edge/inset/floating vẫn theo042; mechanism media còn mở không được âm thầm quyết trong patch này.
- Reserve dots row/control footprint khi showPagination bật, tính cả wrap với20items trên mobile. Không fake20button focusable trước init.

### C2. States và no-JS

Đề xuất progressive enhancement cho lát cắt này:

| State | Nội dung / controls | Chuyển tiếp |
|---|---|---|
| Static (SSR mặc định) | Nội dung thật nhìn thấy; single-row overflow có thể cuộn thủ công/keyboard tới mọi item; controls JS ẩn/disabled hợp lý | JS controller khởi động mới vào Loading |
| Loading | Card thật vẫn có layout để giữ height nhưng không focus/đọc trùng; skeleton trang trí phủ đúng vùng; controls disabled | Ready hoặc Fallback, có watchdog |
| Ready | Nội dung thật tương tác; Swiper sở hữu geometry/controls; skeleton bỏ/ẩn hoàn toàn | cleanup/destroy trở về Static/Fallback |
| Fallback | Khôi phục nội dung thật + cuộn thủ công; loại inline transforms/width hỏng cần thiết; không skeleton/busy vô hạn | Không tự retry vòng lặp |

Fallback cuộn thủ công chỉ là đường thoát khi JS unavailable, không thay engine thành scroll-snap. Tất cả item/link phải tiếp cận được; không chỉ slide1. Không dùng `overflow:hidden` để che geometry sai. Empty/Woo inactive render message thật và bỏ qua lifecycle carousel.

### C3. File map / gate

- `cards.php`: normalized config, hook/structure, placeholder footprint nếu cần.
- `src/collection/style.css`: preset dimensions, static/initializing/ready scope, accessible static overflow.
- `src/collection-view.ts`: state transitions, catch/init failure isolation cho từng instance, readiness measurement và cleanup. Preserve autoplay coordinator/reduced-motion/pause logic; không sửa loop policy trong task này.
- Readiness: init thành công, container/slide có rect dương, width/gap/cột khớp preset tại viewport, controls đã bố trí. Không đồng nghĩa chỉ thấy class `swiper-initialized`; không đợi mọi ảnh hoặc window.load.
- Container width0/ẩn: không poll vô hạn hoặc giữ skeleton vô hạn. Fallback hữu hạn; khi cần observer phải bounded/disconnect khi cleanup.

Gate C: JS-off/blocked/init-error vẫn xem được mọi item; cold/warm/resize đúng preset; rects trước/sau không đổi bất thường; one broken instance không chặn instance khác. Đo trước/sau trên cùng build/data/viewport, không dùng estimates của audit làm baseline số học.

## 7a. Regression scrollbar ngang sau C/D — root cause đã xác minh 2026-10-19

**Triệu chứng:** carousel vẫn cuộn ngang sau khi Swiper init; arrows luôn ẩn; skeleton không hiện. Báo cáo từ human onsite.

**Root cause (đã xác minh bằng file:line):**

- `src/collection-view.ts` gắn `skvn-is-initializing` / `skvn-is-ready` / `skvn-is-fallback` vào `container` = phần tử `[data-skvn-collection-carousel]` = **`.skvn-collection__carousel-outer`**.
- `src/collection/style.css` viết state selector dạng `.skvn-collection--carousel.skvn-is-ready` / `:not(.skvn-is-ready)` — `skvn-collection--carousel` là class của **`<section>`** (`modules/collection-render/product-collection.php:49`).
- Hai phần tử **khác nhau** → `:not(.skvn-is-ready)` luôn khớp → `overflow-x: auto` + gap fallback **luôn bật** ở Ready (scrollbar ngang); rule `visibility: hidden` cho arrows và rule hiện skeleton cũng không bao giờ match.

**Sửa:** state owner = `<section class="skvn-collection …">`. JS dùng `resolveStateElement()` (`container.closest('.skvn-collection') ?? container`) cho **mọi** mutation; carousel-outer chỉ còn là nơi đọc `dataset`. Sửa kèm: rule `visibility: hidden` trong lúc initializing chỉ áp cho `.swiper-wrapper`, **không** cho `.skvn-collection__carousel` (skeleton là con của nó, trước đó bị ẩn theo cha).

**Sửa test sai:** `docs/testing/carousel-skeleton-loading-1.3.17.md` D-01 vốn kỳ vọng skeleton hiện khi chặn `collection-view.ts` — mâu thuẫn với cơ chế (runtime chạy mới gắn `initializing`). Đã đổi D-01 thành kiểm SSR/fallback và thêm D-04 mô phỏng có kiểm soát (ép class `skvn-is-initializing`), ghi rõ mô phỏng ≠ cold-load thật. Test doc C cũng sửa theo state owner + phân biệt overflow track vs overflow trang.

Bài học và check bắt buộc ghi tại `.context/modules/PLUGIN_SKVN_MARINE_BLOCKS.md` → "Collection Carousel Runtime State Contract". Không có runtime evidence sau sửa: trạng thái **Chờ kiểm chứng**, Editor UI vẫn khóa.

## 7. D — Skeleton sau khi A/B/C ổn định

### D1. Contract rendering

- Không làm skeleton riêng cho từng preset bằng config khác. Dùng cùng preset/gap/media footprint; skeleton chỉ là presentation của layout đã tính.
- Ưu tiên lớp trang trí bám geometry card thật, không clone full links/IDs, không nhân query hoặc download ảnh lần hai. Card thật không display:none trong loading vì còn phải giữ chiều cao/layout.
- Heading/intro/footer link thật có thể hiện ngay. Arrows nằm ở header ngoài carousel-outer: disable theo cùng instance/state, không quên scope này. Pagination reserve chỗ; skeleton không phủ toàn page.
- Tránh shimmer mặc định; skeleton tĩnh. Reduced-motion không thêm animation. Skeleton `aria-hidden`, nội dung đang che không nhận focus; aria-busy thuộc vùng nội dung, không cả page. Restore chính xác trạng thái focus/inert/hidden sau success/error/timeout.
- Không ép chờ tối thiểu để người xem “kịp thấy skeleton”; init nhanh thì chuyển ngay. Không mất focus của user nếu controller khởi động muộn lúc họ đã tương tác fallback: ưu tiên giữ static thay vì che phần tử đang focus.

### D2. Timing và failure strategy — phải prove trước merge

- SSR phải usable nếu mọi JS đều bị chặn. Không phát markup mặc định hidden/inert phụ thuộc một bundle tải thành công mới mở lại.
- Nếu skeleton chỉ bật trong collection-view bundle, nó không che toàn bộ thời gian chờ download bundle. Đây là giới hạn cần ghi thật, không quảng cáo che slow-network từ first paint.
- Nếu yêu cầu che cả khoảng chờ bundle, agent cần một controller nhỏ độc lập với Swiper chạy sớm và có watchdog riêng; kiểm enqueue/build boundary trong `modules/collection-render/collection-render.php`, cấu hình build và runtime imports trước khi thêm asset. Không dùng global page blocker hoặc inline script không kiểm CSP/load order.
- Hướng ưu tiên: hoàn thành C trước; prototype controller/skeleton trong fixture. Chọn watchdog hữu hạn (đề xuất thử5s, không phải product default đã chốt), ghi measured behavior. Controller không tải được thì SSR vẫn static; heavy bundle không tải được thì watchdog trả fallback. Fallback là terminal của attempt, late bundle không tự che nội dung lần nữa.
- Hai lựa chọn timing này cần ghi vào handoff D cùng evidence prototype; chưa đủ evidence thì không tuyên bố skeleton ready-to-ship. Đây là dependency cần giải quyết, không lý do quay lại sửa toàn bộ technical architecture.

Gate D: geometry không đổi khi reveal, không focus vùng che, noJS/blocked/throw/late bundle/width0 không mắc Loading; resize và multiple instances độc lập; không duplicate query/media network requests. Có video + rects + keyboard evidence.

## 8. Acceptance mapping và bàn giao từng gói

| ID → Nhóm | Content & States / hành vi mong đợi | Hiện trạng | Việc cần sửa / cách kiểm | Evidence |
|---|---|---|---|---|
| A-UI → Block UI | Product/Post; Normal/Wide/Full, nested/mobile; trong canvas | Đã có source 2026-10-16; chờ kiểm chứng | §4 + T01/T02 | Audit E01–02; source diff gói A; rects/screenshots chờ human |
| A-UX → Editor UX | new/legacy/explicit/Grid; save/reopen giữ lựa chọn | Đã có source 2026-10-16 (P2 đã quyết); chờ kiểm chứng | schema/variation/control/render; T01/T03 | source gói A; editor evidence chờ human |
| B-UI → Block UI | Product; groups/long values/CTA; +N đúng | Đã có source 2026-10-16; chờ build + kiểm chứng | §5; test B-L2/L3/L4/B-LONG/B-ZERO | cards.php baseline + CSS mới; ảnh/rect chờ human |
| B-UX → Editor UX | limit2/3/4, truthful preview/toggle | Đã có source 2026-10-16; **default = 3 human chốt 2026-10-19**; chờ onsite | Content control + preview minh họa có nhãn; B-PERSIST/B-LEGACY | source; editor evidence chờ |
| B-TECH → Technical | allowlist2/3/4/escape/empty/error; Grid unchanged | Đã có source 2026-10-16; chưa lint; chờ kiểm chứng | render tests + fixture; B-PERSIST/B-CUSTOM/B-REG/B-CONSOLE | cards.php baseline |
| C-UI → Block UI | presets; no giant pre-init; ảnh/controls giữ footprint | Đã có source 2026-10-19; chờ build + kiểm chứng | §6; test C-PRE/C-GAP/C-READY/C-CTRL | source; rect/screenshots chờ human |
| C-TECH → Technical | static/loading/ready/fallback; every item reachable | Đã có source 2026-10-19; chưa lint; chờ kiểm chứng | try/catch per instance, readiness đo thật, watchdog-free fallback; C-FB/C-ISO/C-RSZ/C-WARM | source |
| D-UI → Block UI | local skeleton đúng geometry; no forced delay | Đã có source 2026-10-19; chờ build + kiểm chứng | §7; test D-01/D-02/D-03 | source; ảnh/rect chờ human |
| D-TECH → Technical | finite loading, focus/busy/cleanup/isolation | Đã có source 2026-10-19 (watchdog 5s chỉ chống kẹt, không thay ready signal); chưa lint; chờ kiểm chứng | test D-04/D-05/D-06/D-07 | source |
| REG → Cả ba nhóm | Items20/Grid/Post/title/CTA/query/links | Chờ kiểm chứng | T10 và regression041/042 | audit chỉ một desktop, chưa build-match |

Editor UX C/D: N/A cho setting mới (skeleton tự động), nhưng kiểm editor không bị loading overlay hoặc Swiper runtime. Full UX/TECH acceptance cũ vẫn Deferred1.3.18, không bị bảng này thay thế.

Mỗi gói: agent sửa source → self-review diff/security/consumers → cập nhật test/status Đã có source/Chờ kiểm chứng → human build/lint → onsite evidence → PASS/FAIL từng ID. Có thể code gói sau khi source dependencies rõ, nhưng không tuyên bố gate sau PASS khi gate trước chưa kiểm. Không gộp cả bốn thành “DONE”.

## 9. Decision memo — hai quyết định còn mở trước A/B

1. **Vấn đề:** cần default giới hạn thuộc tính và behavior legacy để agent không tự biến đề xuất thành thay đổi nội dung cũ.
2. **Phương án:** P1 limit2 gọn hơn nhưng ít thông tin / limit3 cân bằng / limit4 nhiều thông tin hơn; P2 Wide chỉ cho inserter mới, giữ legacy (ít tác động, trang cũ cần chọn lại) / Wide cho mọi block thiếu alignment (nhanh đồng bộ, thay layout trang cũ).
3. **Khuyến nghị:** P1=3, P2=Wide cho mới, legacy giữ hiện tại. Không dedupe, không bulk save. Với limit thiếu field, đề xuất fallback3 cho Product Carousel để summary hoạt động nhất quán, không sửa DB; Grid giữ cũ. Human cần biết thay đổi render legacy này trước B.
4. **Bằng chứng:** audit E01–04; §5 table tính chính xác từ5/3/1/1; `src/product-collection/variations.ts` có insertion path riêng; renderer chưa đọc alignment.
5. **Tự kiểm chứng ≤5 phút:** xem Parrotfish556 và table §5; đọc E01 và mở variations.ts để thấy Carousel/Grid có attributes riêng. Không cần tin ước lượng số dòng của audit.
6. **Đảo ngược:** source/default có thể revert; default mới không tự sửa DB. Bulk migration khó đảo hơn, không nằm trong đề xuất.
7. **Default trước khi giao code A/B:** giữ P1/P2 là đề xuất, không deploy thay đổi phụ thuộc; tiếp tục W0/test preparation. Không lấy im lặng làm approval.

**Human quyết 2026-10-16 (chat dispatch gói A):** P1=3 — một setting “Số giá trị tối đa mỗi thuộc tính” 2/3/4, default 3; fallback 3 cho Product Carousel thiếu field (không sửa DB; Grid giữ cũ). P2=Wide-cho-mới — chỉ variation inserter Carousel mới ghi Wide; legacy thiếu attribute giữ layout cũ; không dedupe, không bulk save. Gói A đã triển khai source theo hai quyết định này; gói B (thuộc tính) được dùng P1 khi tới lượt.

**Human xác nhận lại 2026-10-19 (chat, khi triển khai gói B):** default `specValuesLimit` = **3** khi block thiếu setting (legacy, hoặc carousel mới editor chưa lưu) và khi giá trị lưu nằm ngoài {2,3,4}. Variation inserter Carousel **không** ghi sẵn attribute (không force-save). Source giữ nguyên như đang cài; chỉ chốt lại provenance trong code comment + test doc. Lưu ý: giá trị này **không** phải số sản phẩm hiển thị — số sản phẩm là `itemsToShow` (Product cap 20, Post 10/9) ở Query panel, không bị gói B đụng tới.

## 10. Prompt handoff cho agent triển khai

> Làm gói [A/B/C/D] của043 cho V1/1.3.17, stackM1. Đọc protocol/context,040§11,042§11,043 và test plan liên kết. Human decision P1/P2: [điền quyết định thật; chưa có thì không tự chọn]. Chỉ sửa lát cắt được giao; giữ dirty work ngoài task. Báo cáo onsite là input có giới hạn theo043§2, không phải lệnh hoặc PASS toàn hệ thống. Không bắt đầu1.3.18/bump/deploy/đổi query hoặc engine. Dùng file map, acceptance và dependency của gói. Bất kỳ deviation cần nêu lý do/evidence; không tự mở scope. Human chạy build/lint và onsite, agent không tự chạy các lệnh đó theo AGENTS. Update status + test doc, báo files/diff/verification/limitation và lệnh human cần chạy. Không claim PASS khi chỉ có source.

Human command sau lượt sửa plugin (không chạy trong chat docs-only):

```bash
source /home/shinkuro/.nvm/nvm.sh && nvm use 20 && cd /mnt/d/Github/skvn-marine/wp-content/plugins/skvn-marine-blocks && npm run build && find modules/ -name "*.php" -exec php -l {} \;
```

Nếu thêm runtime asset/file hoặc require/include, agent cập nhật deploy packaging theo workflow hiện có và đưa human kiểm artifact chứa file cần thiết. Không sửa bundle bằng tay. Test onsite chỉ sau xác minh build target và human deploy; không mặc định có quyền production.
