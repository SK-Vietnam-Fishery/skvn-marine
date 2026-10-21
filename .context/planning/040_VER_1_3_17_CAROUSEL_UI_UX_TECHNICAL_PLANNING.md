# 040 — V1 / 1.3.17: Carousel — Block UI, Editor UX, Technical

**Ngày:** 2026-10-14  
**Current:** V1 / 1.3.17 — Product/Post Carousel Block UI & Visual QA; IN PROGRESS. **Next:** V1 / 1.3.18 — Product/Post Carousel Editor UX & Technical; PLANNED, chưa start. Stack M1 gồm hai patch; xem §11 (human split 2026-10-14).  
**Status tài liệu:** Human yêu cầu lưu planning tổng thể và phương pháp review; chi tiết từng phần tiếp tục thảo luận. Không đồng nghĩa phê duyệt mọi giải pháp đề xuất hoặc QA PASS.

## 1. Authority và scope

**Planning tiếp nối 2026-10-19:** [045 — kế hoạch 1.3.18 trình human duyệt](045_VER_1_3_18_CAROUSEL_EDITOR_UX_TECHNICAL_EXECUTION_PLAN.md) cụ thể hóa editor preview, Style/Action button, title link appearance, font/size theo vai trò và giữ đầy đủ UX/TECH carry-in. Mũi tên lệch tâm xử lý nhỏ trong 1.3.17. Human sẽ duyệt move riêng; chưa transition hoặc mở implementation 1.3.18.

**Override human 2026-10-19 — Product Carousel manual-only:** tắt autoplay và bỏ Play/Pause cho Product Carousel, kể cả block cũ lưu `autoplay=true`. Editor Product không hiện Autoplay/Autoplay delay; giữ attributes để tương thích, không bulk migrate. Post Carousel và Hero Slider giữ hành vi cũ. Quyết định này thay các yêu cầu autoplay/Pause Product trong baseline dưới đây (TECH-04/05 và mapping039); phần Post, reduced motion cho hover và manual navigation vẫn cần kiểm. Source PHP/runtime/editor đã sửa; chưa build/lint/onsite PASS. Test: `docs/testing/onsite-collection-carousel-m1.md`, mục Product manual-only. Không mở milestone1.3.18 hoặc đổi current1.3.17.

- MILESTONES.md giữ quyền quyết định milestone/transition. [Roadmap 039](039_ROADMAP_C_B2B_TOOLKIT_CAROUSEL_FIRST_PLANNING.md) §4–§4a giữ acceptance đã nhận; 040 phân rã và truy vết, không thay hoặc bỏ acceptance cũ.
- Product theo `.local/product-carousel-swiper-poc-03.html` (POC-03 đã chốt), cùng các delta human chốt 2026-10-14 tại §10 (mobile arrows, full title, square media/contain). Post cần visual baseline/acceptance riêng. Nếu artifact local thiếu ở checkout khác, lấy lại artifact trước khi kết luận visual, không đoán từ số đo.
- Giữ Swiper 11 và custom dynamic Product/Post blocks tự own query/render bằng Woo/WP native APIs. Không đổi namespace/slug/option keys.
- Woo mặc định là baseline; không bắt MOQ/lead time/cert hoặc Woo Catalog để PASS. POC cung cấp thiết kế component, không bê debug labels, preset tags và spacing giữa section demo vào block.
- Product Carousel variation giữ 4–2–1; default block gốc/Grid giữ 3–2–1. Không tự đổi block.json chỉ để đổi default. Post default mới chưa chốt.
- Scope: carousel frontend, collection controls/preview liên quan và Grid regression. Quote URL/context thuộc milestone; CF7/CFDB7 end-to-end thuộc M2. Slider mở rộng, advanced sidebar riêng, theme độc lập, header/footer mới không tự vào 1.3.17.
- Áp dụng [review contract](../../docs/standards/block-ui-editor-ux-technical-review-contract.md). Phân nhóm để không bỏ sót; triển khai theo lát cắt hành vi xuyên nhóm, không làm hết UI rồi mới nhìn editor.

## 2. Baseline, trạng thái và evidence nguồn

Audit là **source snapshot 2026-10-14**, đối chiếu POC với working tree có thay đổi chưa commit. Không phải kết quả browser/onsite. Package tại thời điểm đọc vẫn 1.3.9; không suy ra build deploy đã đồng bộ. Executor đọc lại source/diff trước sửa, giữ các thay đổi song song.

Trạng thái: **Chưa làm / Đã có source / Chờ kiểm chứng / PASS / FAIL / Deferred**. Trạng thái ở đầu cột Hiện trạng. FAIL source chỉ dùng cho đường code chứng minh được vi phạm; chưa quan sát runtime không có nghĩa FAIL. PASS cần evidence đúng tiêu chí/build. Không dùng DONE chung cho implementation và acceptance.

Mẫu: `ID → Nhóm → Hành vi mong đợi → Hiện trạng → Việc cần sửa → Cách kiểm → Evidence`. ID giữ ổn định; thêm ID con khi chia nhỏ. Nguồn dưới đây là evidence source, chưa thay evidence onsite. Số dòng có thể dịch; dùng symbol/selector để kiểm lại.

| Ref | File / điểm đọc |
|---|---|
| E-POC | `.local/product-carousel-swiper-poc-03.html`:85 trở đi — section/card/control CSS |
| E-CSS | `wp-content/plugins/skvn-marine-blocks/src/collection/style.css`:7 header; :150 card/media/CTA, pagination/reduce ở phần sau |
| E-EDIT | `wp-content/plugins/skvn-marine-blocks/src/collection/controls.tsx`:229 Show image/Card style; :375 Advanced; :469 query preview; :524 PlaceholderCard; :774 palette |
| E-RUNTIME | `wp-content/plugins/skvn-marine-blocks/src/collection-view.ts`:1, :48–145 modules/canLoop/useAutoplay/arrows/breakpoints/coordinator/cleanup |
| E-CARD | `wp-content/plugins/skvn-marine-blocks/modules/collection-render/cards.php`:25 Post; :107 Product media; :213 badges; :398 Pause |
| E-QUERY | `wp-content/plugins/skvn-marine-blocks/modules/collection-render/query-products.php`, `query-posts.php` — query native |
| E-CONFIG | Plugin `src/product-collection/variations.ts`, Product/Post `block.json`, `src/collection/constants.ts`, `package.json` |
| E-OWNER | `.context/modules/PLUGIN_SKVN_MARINE_BLOCKS.md`, `.context/modules/collection-card-system.md`, `docs/decisions/collection-carousel-engine-keep-swiper.md` |

Behavior thư viện ngoài phải kiểm docs/changelog đúng major đang dùng trước khi biến thành premise kiến trúc. Các findings dưới đây mô tả source dự án, không tự khẳng định kết quả runtime Swiper.

## 3. Block UI — frontend UI/UX

| ID | Nhóm | Hành vi mong đợi | Hiện trạng | Việc cần sửa / làm rõ | Cách kiểm | Evidence |
|---|---|---|---|---|---|---|
| UI-01 | Block UI | Header/heading/eyebrow/intro theo POC; toggle đúng | Chờ kiểm chứng — có source; flex-start/1.25rem khác POC flex-end/28px | Khóa typography/spacing component, token/fallback; không copy section demo | So cùng content/viewport; showHeading off, intro/eyebrow rỗng/dài | E-CSS, E-POC; cần ảnh onsite |
| UI-02 | Block UI | Product shell/body/CTA theo POC với Woo mặc định | Chờ kiểm chứng — radius 8px vs 14px; body 1rem/0.65rem vs 18px/13px; CTA 4px vs 9px | Đồng bộ border/shadow/color/spacing; CTA trust-blue hover và focus | So cạnh nhau; title/chips dài, thiếu specs; mobile CTA luôn thấy | E-CSS, E-CARD, E-POC; cần visual PASS |
| UI-03 | Block UI | Media/fallback giữ ratio, không mất bố cục | Chờ kiểm chứng — ratio ở ảnh; Post không ảnh có span rỗng | Kiểm owner ratio/media; Product/Post placeholder và showImage off | Product mới square theo §10; ratio legacy/Post kiểm riêng; có/không ảnh, tải chậm, portrait/landscape | E-CSS, E-CARD; cần ảnh/rect |
| UI-04 | Block UI | Arrows rõ, dots pill clickable; state không gây hiểu nhầm | Chờ kiểm chứng — ký tự arrows/dots tròn khác POC SVG 46px/pill 26px | Đồng bộ chrome; loop/non-loop/ít item riêng; không bắt disabled đầu/cuối khi loop | Mouse/touch/keyboard, ít/đủ/nhiều item, không clip arrows | E-CSS, E-RUNTIME, E-POC; cần video/DOM |
| UI-05 | Block UI | Archive/catalog đúng URL/label/toggle và wrap mobile | Chờ kiểm chứng — renderer có; style khác POC, chưa rule xếp dọc tương ứng | Khóa footer layout/spacing/label dài | Một/hai/không CTA, click link, màn hình hẹp | E-CSS, E-CARD; cần ảnh/URL |
| UI-06 | Block UI | Responsive/preset ổn định, không tràn/cắt hover | Chờ kiểm chứng — gap 16px vs POC 20px; có 640/1024 | Gap 20 theo roadmap; prove geometry/hover clearance | 5 presets, quanh breakpoint, resize/orientation với scrollbar dọc | E-RUNTIME, E-CSS; cần rect/video |
| UI-07 | Block UI | Post có visual phù hợp article, dùng chung chrome | Chưa làm — chưa Post demo sống được duyệt; renderer đã có | Tạo Post baseline date/author/excerpt/read; human duyệt riêng | Có/không ảnh, nội dung dài, date/author toggle | E-CARD, E-EDIT; cần Post artifact/acceptance |
| UI-08 | Block UI | Empty/thiếu dữ liệu/Woo inactive rõ và graceful | Chờ kiểm chứng — nền fallback có một phần | Chốt states §6; không bắt dữ liệu B2B | Zero result, 1 item, thiếu ảnh/term/spec, Woo inactive | E-CARD, callbacks; cần ảnh/log |

## 4. Editor UX — Deferred → 1.3.18; baseline điều khiển và phản hồi

| ID | Nhóm | Hành vi mong đợi | Hiện trạng | Việc cần sửa / làm rõ | Cách kiểm | Evidence |
|---|---|---|---|---|---|---|
| UX-01 | Editor UX | Control có tác dụng hoặc lý do rõ | FAIL source — Card style chọn được nhưng renderer không dùng; Advanced rỗng | Inventory active/legacy/unsupported; xử lý cardStyle UI giữ compatibility, bỏ panel rỗng | Control → attribute → preview → save/reload → frontend | E-EDIT, E-CARD, E-OWNER |
| UX-02 | Editor UX | Preview đúng Show image và card structure | FAIL source — placeholder luôn media; Post title trước date, không author | Sửa parity; summary đổi không thay được visual phản hồi | Product/Post image/ratio/date/author/excerpt toggle | E-EDIT, E-CARD; cần ảnh hai surfaces |
| UX-03 | Editor UX | Hiểu bố cục/preset ngay khi biên tập, không autoplay | Chờ kiểm chứng — một card mẫu + summary, thiếu phản ánh full header/footer/preset | Đề xuất preview tĩnh; mức độ preview còn thảo luận | Insert, đổi preset/header/footer, tab, nhiều block | E-EDIT; cần UX scenario |
| UX-04 | Editor UX | Query preview không làm hiểu sai kết quả lọc | FAIL source parity — Approximate list không truyền relation; Featured dùng menu_order | Chốt exact hay approximation minh bạch; không tự thêm API/dependency | Fixture AND/OR, featured/manual/newest/shuffle; so IDs frontend | E-EDIT, E-QUERY; cần IDs |
| UX-05 | Editor UX | Chip color đã chọn có kết quả tương ứng | Chờ kiểm chứng — toàn palette dropdown, CSS chỉ teal/navy mapping | Kiểm palette→CSS; chốt option scope hoặc fallback mapping | Mọi màu hiển thị, computed style hai surfaces | E-EDIT, E-CSS; cần bảng màu |
| UX-06 | Editor UX | Hiểu tổng item vs số card/viewport và không-trượt | Chờ kiểm chứng — 3 item default vs Product 4 desktop | Explanation/preview state; không tự đổi schema/default | Insert 1/3/4/6 item, desktop→mobile | E-CONFIG, E-RUNTIME |
| UX-07 | Editor UX | Manual order/Both/quote context dễ hiểu và đúng | Chờ kiểm chứng — Both hiện một quote CTA; view link ở ảnh/title | Chốt wording theo hành vi; không mặc định Both là hai nút | Mọi action, preview/URL/save/reload | E-EDIT, E-CARD; cần wording/URL |
| UX-08 | Editor UX | Không lộ roadmap/dev placeholder; hover preview kiểm được | Chờ kiểm chứng — Catalog data — woo-catalog 1.5.0 trong preview | Bỏ roadmap text, hover/focus feedback; chỉ giữ thông tin có ích | Hover DevTools, tab, editor hẹp, labels | E-EDIT, E-CSS; cần ảnh |

Basic trong Block / advanced sidebar riêng vẫn là hướng thảo luận chưa duyệt kiến trúc. Di chuyển panel không sửa control vô hiệu hoặc preview sai; ưu tiên tính đúng trước vị trí UI.

## 5. Technical — Deferred → 1.3.18; baseline runtime, dữ liệu, độ tin cậy

| ID | Nhóm | Hành vi mong đợi | Hiện trạng | Việc cần sửa / làm rõ | Cách kiểm | Evidence |
|---|---|---|---|---|---|---|
| TECH-01 | Technical | Query/render native, Swiper frontend, Grid không init carousel | Đã có source — foundation/pause coordinator | Giữ boundary; conditional load/init, Woo inactive | Product/Post Grid/Carousel, network/console, Woo off | E-QUERY, E-RUNTIME, E-OWNER |
| TECH-02 | Technical | ←/→, focus/labels đúng, không điều khiển nhầm carousel | Chưa làm — không import/configure Keyboard | Scope/config theo docs Swiper 11; A11y module không chứng minh ←/→ | Tab/Shift+Tab/Enter/Space/←/→, input focus, offscreen, hai carousel | E-RUNTIME; cần interaction evidence |
| TECH-03 | Technical | Badge archive-link và media link hợp lệ | FAIL source — anchor badge trong anchor media ở archive-link | Sửa markup atomic; giữ overlay/target/escaping | DOM, click/tap/tab badge khác media/title | E-CARD:36/107/224–231; cần DOM/URL |
| TECH-04 | Technical | Pause/Play phản ánh autoplay thật; hover/focus toàn controls | FAIL source path — PHP hiện Pause theo attr nhưng JS có thể không coordinator khi reduce/ít item; arrows ngoài pause container | Đồng bộ state/UI, kiểm hover/focus scope; giữ explicit user pause | Off/on, arrows/card/footer hover/focus, tab hidden, pause/play | E-CARD:398, E-RUNTIME:48–145 |
| TECH-05 | Technical | Reduced motion áp dụng slide và hover card/ảnh | Chờ kiểm chứng — autoplay guard có; CSS reduce track, card hover còn translate/zoom | Audit cascade, chốt nonessential motion/focus | OS reduce on/off, computed transform/transition | E-CSS, E-RUNTIME; cần video/styles |
| TECH-06 | Technical | Loop/non-loop/ít item đúng ở mọi viewport/resize | Chờ kiểm chứng — canLoop theo desktop count | Kiểm limits Swiper 11 bằng fixture; sửa theo evidence, không tự thêm setting loop | Counts theo slidesPerView mỗi breakpoint, swipe/arrows/dots nhiều vòng | E-RUNTIME; cần index/video/log |
| TECH-07 | Technical | Query/actions đúng, output an toàn, quote context còn | Đã có source — native/escaping, chưa security audit đầy đủ | Sanitize-at-read/URL/inline CSS theo standard; featured/relation/actions | IDs, invalid attrs, empty values, URLs; CF7 ngoài scope | E-QUERY, E-CARD |
| TECH-08 | Technical | Grid/editor/saved content không regression | Chờ kiểm chứng — shared CSS/render | Kiểm old attrs/reopen/save; card variants không âm thầm bỏ | Product/Post Grid/Carousel, old/new content, presets, invalid-block | E-OWNER, E-EDIT, E-CSS |
| TECH-09 | Technical | Console sạch, lifecycle/performance ổn định | Đã có source — guard/cleanup; chưa memory PASS | Đo init/teardown/listeners/heap, editor query/render, image layout shifts | Fixture trước/sau, số vòng rõ, nhiều block, ảnh chậm | E-RUNTIME, E-EDIT; cần trace/network/heap |
| TECH-10 | Technical | Build/version/artifact kiểm chứng trước onsite | Chờ kiểm chứng — package snapshot 1.3.9, current 1.3.17 | Executor sync development theo workflow; human build/lint; require/include đổi thì audit zip | Source/build/deploy version và command output/inventory | E-CONFIG; cần build/deploy evidence |
| TECH-11 | Technical | QA có mục tiêu/evidence/human visual approval | Chưa làm — chưa test file riêng hoặc PASS được ghi | Tạo docs/testing/onsite-collection-carousel-m1.md khi bước QA bắt đầu | Coverage §6–§7, exact steps/expected/pass-fail | 039 §4–§4a; test file planned |

## 6. Hai lớp xuyên suốt

**Content & States:** mỗi ID chọn states liên quan; không nhân mọi tổ hợp vô ích. N/A có lý do, không dùng để bỏ acceptance đã nhận.

| Trục | Baseline / edge cases | Evidence |
|---|---|---|
| Content | Product Woo/Post thật; title/excerpt/labels/chips ngắn/dài; có/không ảnh; attributes/terms rỗng/nhiều; Woo inactive | Fixture IDs/settings, ảnh, expected query IDs |
| Count | 0/1/ít/đủ/nhiều so với mỗi breakpoint; fixture thường ≥6 products và đủ posts | Count query vs visible, loop/control states |
| Layout | Grid/Carousel; 1-1-1/2-1-1/3-2-1/4-2-1/5-3-1; mọi imageRatio | Ảnh/rect, viewport/scrollbar |
| Responsive | Baseline 1280/768/375; 639/640/641, 1023/1024/1025; resize/orientation | Video/rect, no overflow/clip |
| Interaction | Mouse/touch/keyboard; arrows/dots/swipe; autoplay off/on, explicit pause, hover/focus/tab hidden; reduce | Video/focus/index/control state |
| Editor | Insert/edit/duplicate/save/reload old/new; sidebar hẹp, nhiều collection, loading/error/empty preview | Ảnh editor/frontend, invalid-block/network/error notes |
| Reliability | Console, lifecycle, Woo off, conditional runtime, theme override scope | Logs/network/heap trước-sau, environment/build |

**Acceptance & Evidence:** mỗi yêu cầu có phương pháp kiểm và expected result; bản chi tiết bổ sung exact steps/fixture/threshold trước nghiệm thu.

- Accessibility: frontend focus/contrast/touch targets; editor labels/keyboard/disabled reasons; technical semantics/ARIA/keyboard scope.
- Responsive: frontend geometry, editor hẹp và runtime breakpoint state.
- Performance: ảnh/layout ổn định, editor query/render phản hồi, conditional load/cleanup. Chưa đo thì không bịa ngưỡng hoặc claim PASS.
- Evidence record: `ID + ngày/người kiểm + URL/page + build/commit + WP/Woo/browser/device + fixture/settings + steps + expected/actual + ảnh/video/log/rect + PASS/FAIL`.
- Không gom accessibility/responsive/performance thành một ô PASS chung; áp vào từng surface liên quan.

## 7. Truy vết toàn bộ acceptance 039

| Nguồn | Nơi nhận |
|---|---|
| §4 Product POC desktop/tablet/mobile | UI-01–06; §6 |
| §4 arrows loop/non-loop/ít item, dots pill/click, ←/→ | UI-04, TECH-02/06 |
| §4 autoplay off, Pause/Play, hover/focus/tab-hidden/reduce | TECH-04/05 |
| §4 Card URLs + quote context | TECH-03/07, UX-07 |
| §4 Woo inactive/empty | UI-08, TECH-01/07 |
| §4 Post visual riêng | UI-07, UX-02 |
| §4 QA nhận từ 1.3.11/1.3.16 | Các dòng §4a bên dưới |
| §4 responsive/resize/orientation/counts | UI-06, TECH-06; §6 |
| §4 console/memory/invalid-block/a11y | TECH-02/08/09; §6 |
| §4 Grid no regression/editor no Swiper | TECH-01/08, UX-03 |
| §4 Human PASS trước M2 | TECH-11, Transition Rule |
| §4a 1.3.11 Product Grid Woo thật, Post Grid, Carousel render/init | TECH-01/08 |
| §4a 1.3.11 toàn 5 presets desktop/mobile | UI-06, TECH-08 |
| §4a 1.3.11 quote/view/context, post read URLs | TECH-07, UX-07 |
| §4a 1.3.11 fallback placeholder/SKVN, Woo inactive | UI-03/08, TECH-01 |
| §4a 1.3.16 imageRatio render/apply, arrows không clip | UI-03/04/06 |
| §4a 1.3.16 spacing, showHeading/eyebrow/intro | UI-01/06, UX-03 |
| §4a 1.3.16 archive URL, catalog toggle/URL/label | UI-05, UX-03 |
| §4a 1.3.16 card variants, badge overlay, 4-2-1 | UI-02/04/06/07, UX-01, TECH-03/08 |
| §4a 1.3.16 CTA hovers/reduce/focus-visible | UI-02/05, TECH-05 |
| §4a 1.3.16 editor preview hover DevTools | UX-08 |
| §4a General carousel console/invalid-block/memory | TECH-08/09 |

CardStyle legacy vs card variants acceptance cần làm rõ UX-01: không xóa acceptance bằng cách xóa control; chỉ rõ supported variants và cách nghiệm thu. QA slider → M5, Accordion/Card motion/Feature Showcase → M6, Core Button Hover mới → 1.3.4, Credits parking giữ nơi nhận theo 039 §4a. Không đổi status 1.3.11/1.3.16 hoặc resolve T-DEMO-2.

## 8. Lát cắt đề xuất trước split — phân công hiện hành tại §11

039 P0–P5 giữ trình tự cấp milestone. Phân rã dưới đây thêm correctness xuyên nhóm, chưa phê duyệt mọi implementation choice:

1. **P0–P1 fixture/smoke:** human Woo + ≥6 products, Posts, 2 pages Product/Post; lưu URLs/settings/build, xác nhận hiện trạng.
2. **Correctness:** TECH-03/04 và UX-01/02/04/05/07 — links, control/preview, pause state. Mỗi sửa đi trọn control→output→evidence; không mở settings architecture mới.
3. **P2–P3 Product visual/geometry:** UI-01–06; đọc CSS safety/specificity contract; kiểm Grid/editor cùng đợt.
4. **P4 Post:** UI-07/UX-02, cùng chrome đã kiểm; human duyệt Post riêng.
5. **P5 integrated QA:** §6–§7 + TECH-02/05/06/08–11; human build/lint/onsite, evidence từng ID, rồi human quyết milestone completion.

Files có thể cần: plugin `src/collection/style.css`, `src/collection-view.ts`, `modules/collection-render/cards.php`, `src/collection/controls.tsx`, test doc. Query/default/schema chỉ khi slice chứng minh cần và có compatibility check. Đây là bản đồ ảnh hưởng, không lệnh sửa toàn bộ. Không đụng shared/slider/theme nếu chưa evidence; không sửa GeneratePress.

## 9. Bổ sung chi tiết tiếp theo và handoff

- **Block UI:** typography/token, spacing/component boundaries, media/fallback, footer/states và Post artifact. Phản biện copy nguyên POC/demo hoặc chỉ kiểm dữ liệu đẹp.
- **Editor UX:** inventory controls active/legacy, preview đủ đến đâu, exact vs approximate query, wording/actions/colors. Phản biện chuyển panel như giải pháp cho preview sai.
- **Technical:** pause/loop/responsive states, keyboard scope, markup/security, compatibility/lifecycle/fixture. Phản biện “có Swiper/A11y/cleanup” đồng nghĩa chạy đúng.
- Bổ sung vào ID tương ứng: source/evidence, trade-off, boundary, acceptance; khi cần quyết định kiến trúc dùng Decision Memo chuẩn. Không hỏi lại POC/Swiper/Woo/default đã chốt.

Human 2026-10-14 giao lưu planning tổng thể và protocol ba nhóm + hai lớp; chi tiết từng phần tiếp tục trao đổi. Lượt này chỉ Markdown, không bump/build/lint/onsite, không PASS/transition/archive. File `docs/testing/onsite-collection-carousel-m1.md` đang planned, chưa được tạo bởi lượt này. Trước onsite phải tạo test doc với target URL, preconditions, exact steps, expected UX, pass/fail và evidence; human dời test thì ghi deferred + target milestone theo AGENTS.

## 10. Tổng kết tích lũy Block UI — quyết định và giải thích cho người đọc

**Nguồn:** trao đổi human ngày 2026-10-14 sau khi đồng ý lưu planning. Phần này bổ sung/thu hẹp spec ở các ID liên quan; khi khác với proposal trước, quyết định có ngày dưới đây là hướng thiết kế hiện hành. Đây là design approval, không phải implementation hoặc test PASS. Không suy rộng sang Post hay toàn bộ card khi human chỉ chốt một phần.

### 10.1 Những gì đã chốt và những gì còn mở

| ID liên quan | Human đã chốt | Chưa chốt |
|---|---|---|
| UI-04 | Mobile giữ arrows khi showArrows bật và có nội dung có thể trượt; không copy rule POC ẩn header-nav dưới 768px | Vị trí/breakpoint để xuống hàng; ít item dùng hide hay disabled; chi tiết header còn mở |
| UI-02 | Tên Product hiển thị đầy đủ, cho xuống dòng tự nhiên, không line-clamp/ellipsis/cắt hai dòng | Post title; khoảng cách, equal-height/alignment và cấu trúc card còn lại |
| UI-03 | Product có media frame tỷ lệ cố định 1:1; ảnh contain, căn giữa, giữ aspect gốc, không crop/kéo méo | Kích thước/padding/radius/border/offset/default/control cụ thể |
| UI-03 / UX-02 | Ba hướng bố cục Sát mép / Có khoảng đệm / Nổi phía trên dùng chung frame vuông và contain; editor phản ánh khung/kết quả thật, có hướng dẫn ngắn | Cơ chế control, preset/adjustment, fallback ảnh thiếu ở Floating |
| UX-01 / TECH-08 | Hướng Product mới không cần control imageRatio; attributes ratio cũ phải được xử lý tương thích | Cơ chế migration/normalize/deprecation cụ thể; không tự bỏ attr hoặc ép nội dung cũ |
| UI-07 | Post không tự áp dụng square Product | Ratio/title/visual Post phải bàn và duyệt riêng |

Giữ POC-03 làm điểm xuất phát UI, nhưng các quyết định trên là delta có chủ đích: mobile arrows và Product full title/media phải theo human đã chốt, không quay lại copy POC máy móc. Các số đo POC trong §3 vẫn là baseline so sánh, chưa phê duyệt toàn bộ cấu trúc Header/Card.

### 10.2 Vì sao giữ arrows trên mobile?

Swipe không phải lúc nào cũng dễ nhận ra. Arrows làm khả năng chuyển nội dung rõ hơn và thêm một cách thao tác bằng button. Giữ arrows không đồng nghĩa ép chúng nằm cạnh heading ở mọi chiều rộng.

Đề xuất đang mở: khi thiếu chỗ, đưa controls xuống hàng riêng để heading có không gian. Chưa khóa breakpoint hoặc alignment. Khi không có nội dung để trượt, phải tránh controls trông như vẫn có tác dụng; hide hay disabled cần quyết tiếp. Loop không có “đầu/cuối” như non-loop, nên không áp cùng quy tắc disabled cho cả hai.

Acceptance bổ sung UI-04: kiểm mobile với showArrows on/off, title header dài, ít/đủ/nhiều item, focus/tap targets, resize; không collision header/arrows. **Việc giữ/ẩn mobile không còn là câu hỏi mở**; chỉ placement/state còn bàn.

### 10.3 Tên Product đầy đủ và hệ quả bố cục

Human chọn “Tên đầy đủ”: không cắt mất tên hàng để card trông đều. Title wrap theo chiều rộng; người xem phải đọc được toàn bộ tên mà không cần hover để xem tooltip.

Độ cao phần title có thể khác nhau giữa các card. Cập nhật human 2026-10-14 (§11): CTA thẳng hàng đã chốt cùng full title; cơ chế equal-height/vùng nội dung co giãn và số đo vẫn cần đặc tả, không tự coi cách code đã duyệt. Không dùng fixed height che text để làm đẹp hàng.

Acceptance bổ sung UI-02: tên ngắn/dài/rất dài cùng một hàng và mobile; kiểm word wrap, không clip, CTA đọc/thao tác được. Không tự áp full-title policy này cho Post trước khi thảo luận.

### 10.4 Khung media, ảnh nguồn và ba trách nhiệm

Human chốt **khung vuông 1:1 + ảnh contain** và nêu rationale: có khung media riêng giúp code, thay đổi và kiểm thử dễ hơn.

- **Card** sở hữu bố cục tổng thể: title, badges/chips, CTA, khoảng cách và liên kết giữa các vùng.
- **Media frame** sở hữu hình học: width/height theo ratio 1:1, vị trí, radius/clipping và khoảng trống bố trí.
- **Ảnh bên trong** sở hữu cách fit: contain, căn giữa, giữ tỷ lệ gốc. Tách các trách nhiệm giúp sửa padding hay offset mà không vô tình kéo méo ảnh.

Contain hoạt động theo giới hạn của khung: với ảnh ngang, chiều ngang fit vào frame và còn khoảng trống trên/dưới; với ảnh dọc, chiều dọc fit và còn khoảng trống hai bên. Ảnh vuông fit cả hai chiều. “Fit” là vừa khung theo tỷ lệ, không phải kéo hình cho kín mọi cạnh.

| Cách fit | Kết quả | Hướng hiện hành Product |
|---|---|---|
| Contain | Thấy toàn bộ ảnh, có thể có khoảng trống quanh ảnh | Đã chốt |
| Cover | Phủ kín frame bằng cách cắt phần vượt khung | Không dùng cho Product theo quyết định này |
| Stretch/fill không giữ tỷ lệ | Ép ảnh theo khung, có thể méo vật thể | Không dùng |

**Frame width 100% không đảm bảo vật thể nhìn thấy chạm cạnh.** File ảnh có nền trắng hoặc transparent padding vẫn giữ phần trống đó. Hai file cùng 1:1 không bảo đảm sản phẩm trông cùng kích thước: vật thể có thể chiếm 90% file này nhưng chỉ 50% file kia.

Human chuẩn bị/cân chỉnh ảnh nguồn, gồm transparent whitespace. Block không tự remove background hoặc tự crop theo silhouette. Gợi ý help text cho editor (wording còn chỉnh): “Ảnh hiển thị đầy đủ trong khung vuông. Khoảng trống có sẵn trong ảnh được giữ nguyên; hãy chuẩn bị ảnh nguồn phù hợp.” Preview phải thể hiện đúng khoảng trống/kết quả, không dùng thumbnail crop để tạo kỳ vọng sai.

### 10.5 Ba bố cục media dùng chung square/contain

Đây là **bố cục ảnh trong card**, không phải ba preset thay đổi toàn bộ typography, chip, CTA và query. Không đồng nhất một media option với một cardStyle mới khi chưa chốt kiến trúc.

| Bố cục | Ý nghĩa dễ hiểu | Hình học/điểm cần đặc tả |
|---|---|---|
| Sát mép | Frame trải hết chiều rộng bên trong card | Khung vuông, width theo inner card; xử lý radius hai góc trên, border/clipping riêng; visible object vẫn có thể cách mép do contain/ảnh nguồn |
| Có khoảng đệm | Frame lùi đều từ trên, trái, phải | Padding đều làm frame nhỏ lại, height giảm theo width để vẫn vuông; radius khung tính theo khoảng lùi, không giữ bán kính quá lớn |
| Nổi phía trên | Frame/ảnh trông nhô lên khỏi nền card, phù hợp asset transparent | Khung vuông contain, layer riêng và có headroom; offset/size/giới hạn/default còn mở |

**Radius và inset:** đường cong bên trong thường cần bán kính nhỏ hơn đường cong card. Hướng tính đang đề xuất là `max(0, R − distance)`, với distance đo từ mép ngoài tương ứng và tính cả border nếu có. Ví dụ nếu tổng khoảng lùi thực bằng P: R20/P8 → 12; R16/P12 → 4; R14/P16 → 0. Đây là giải thích hình học, chưa khóa token hoặc công thức implementation; phải xác định rõ width box/border trước khi áp dụng.

Radius của frame làm tròn frame/clipping, **không biến silhouette của vật thể transparent thành hình bo góc**. Không ép crop tròn theo ảnh tham chiếu món ăn: ảnh món ăn chỉ minh họa hiệu ứng nổi, không quyết định tất cả product phải tròn.

### 10.6 Floating: vì sao không chỉ translate ảnh lên?

Card hiện có overflow hidden; track carousel cũng có clipping để che các slide ngoài viewport. Nếu chỉ translate media lên, ảnh/shadow có thể bị cắt. Đổi tất cả thành overflow visible lại có thể làm lộ slide kế bên hoặc phá geometry.

Hướng cần chi tiết tiếp:

1. Tách lớp nền/body card và lớp media để media có thể nhô so với nền.
2. Dành headroom **bên trong vùng item/carousel** cho phần nhô; không lấy khoảng trống bằng cách tràn tùy ý ra ngoài track.
3. Tính cả hover lift và shadow clearance, không chỉ chiều cao ảnh ở trạng thái nghỉ.
4. Kiểm vùng header/arrows, card bên cạnh và footer khi mobile/resize/title dài.
5. Giới hạn offset/size theo preset hoặc range có kiểm chứng; chưa chốt giá trị.

Human đồng ý hướng shared square frame, contain và reserved headroom; các số đo/layer cụ thể vẫn cần source/geometry review trước implementation. Không dùng overflow-x hidden/clip để che bounding box sai theo CSS safety contract.

Ảnh thiếu ở Floating: đề xuất fallback về frame/placeholder ổn định hoặc bố cục không nổi; **chưa được human chốt**. Tránh tạo khoảng trống lớn vô nghĩa nhưng không tự thay mode được lưu mà không giải thích.

### 10.7 Controls: phương án và trade-off đang đề xuất

| Phương án | Ưu điểm | Đánh đổi |
|---|---|---|
| Presets cố định | Dễ dùng, ít trạng thái sai, QA nhỏ | Ít linh hoạt khi asset/site khác nhau |
| Controls tự do | Điều chỉnh nhiều tình huống | Dễ vỡ geometry, khó giải thích và tăng test matrix; không phù hợp arbitrary values theo governance hiện tại |
| Preset + điều chỉnh có giới hạn | Bắt đầu an toàn, vẫn có khoảng tinh chỉnh hữu ích | Cần chọn rõ phạm vi hợp lệ, normalize/reset và kiểm các giới hạn |

Cập nhật human 2026-10-14 (§11): hướng preset + bounded adjustment + reset rõ đã chốt; chi tiết và kiến trúc controls chưa thiết kế. Bảng trên giữ trade-off để giải thích lựa chọn. Có thể chỉ hiện controls liên quan mode, giải thích value do preset quản lý và có reset về mặc định. Phải phân biệt “ẩn vì không áp dụng” với “disabled vì bị preset quản lý”; chưa khóa UI này.

Đề xuất cũ cho Product chọn tỷ lệ media theo mode đã **superseded** bởi square cố định. Không đưa ratio dropdown trở lại hướng mới Product chỉ vì block.json còn attr. Inset padding, floating size/offset, radius/border, breakpoint/default/reset còn mở. Editor không chạy Swiper/autoplay; preview tĩnh phải đủ phản ánh frame/contain/inset/floating và states thật.

Legacy ratio là compatibility task, không thể xóa bằng cách bỏ control. Cần quyết cách đọc/render giá trị cũ, khi nào migrate và cách human biết nội dung đổi; kiểm old/new/save/reload. Cho đến khi mechanism chốt, không ép nội dung đã lưu về 1:1 tự động. Post giữ track riêng.

### 10.8 Tổng hợp việc tiếp tục và trace nghiệm thu

- **Header/navigation:** typography/spacing, mobile arrow placement/breakpoint, ít item hide/disabled.
- **Product media:** kích thước/padding/radius/border, floating limits/headroom, fallback thiếu ảnh, defaults; fixed 1:1 + contain đã chốt.
- **Product card:** badges/chips, CTA, equal-height/alignment, spacing, full title đã chốt.
- **Footer và responsive:** archive/catalog CTA, wrap/gap, interaction states.
- **Post:** visual/title/ratio chưa tự áp Product.
- **Editor UX:** control inventory/parity/preview/query/labels, bounded control proposal, legacy ratio UI.
- **Technical:** markup/keyboard/pause/loop/security/lifecycle/Grid compatibility/build/evidence theo TECH IDs.

ID bổ sung để không hòa design approval với source status:

| ID | Nhóm | Hành vi mong đợi | Hiện trạng | Việc cần sửa | Cách kiểm | Evidence |
|---|---|---|---|---|---|---|
| UI-04a | Block UI | Mobile arrows còn khi enabled/có thể trượt | Chờ kiểm chứng — design approved 2026-10-14 | Chốt placement/state, kiểm CSS/runtime thực | Mobile header dài + counts + focus/touch | Human decision §10.1; chưa runtime PASS |
| UI-02a | Block UI | Full Product title wrap, không clamp/ellipsis | Chờ kiểm chứng — design approved 2026-10-14 | Kiểm cascade, card/CTA alignment | Tên nhiều độ dài cùng hàng/mobile | Human decision §10.3; cần screenshot |
| UI-03a | Block UI | New Product square frame/contain/3 layouts | Chưa làm — design direction approved, chi tiết values còn mở | Khóa media geometry/controls, không tự migrate legacy | Ngang/dọc/vuông/transparent whitespace, modes, resize/hover | Human decision §10.4–6; cần artifact/rect |
| UX-02a | Editor UX | Preview thật + guidance ảnh nguồn | Chưa làm — direction approved | Frame/fit/layout parity, help wording | Cùng source image/editor/frontend | §10.4/7; cần parity evidence |
| TECH-08a | Technical | Legacy ratio tương thích; Post không đổi ngầm | Chưa làm — requirement approved, mechanism mở | Quyết migration/read/render boundary | Old/new attributes, save/reopen, Product/Post | §10.7; cần decision + regression |

“Đã chốt” ở đây là evidence ý định sản phẩm. Chỉ sau implement và kiểm đúng fixture/build mới cập nhật PASS cho hành vi. Planning chi tiết phải giữ các lý giải này để agent/human sau hiểu giới hạn quyết định mà không cần đọc lại chat.

## 11. Quyết định tách scope thành hai SemVer — 2026-10-14 (hiện hành)

Human yêu cầu: “semver hiện tại giải quyết giao diện, semver + 1 sẽ giải quyết editor UX và technical… chỉ đưa các setting vào các phần tương ứng, chưa thiết kế gì, chốt các khuyến nghị”.

- **Current: V1 / 1.3.17 — Product/Post Carousel Block UI & Visual QA**, IN PROGRESS. Giữ số current; thu hẹp scope theo quyết định human.
- **V1 / 1.3.18 — Product/Post Carousel Editor UX & Technical**, PLANNED. Human xác nhận next PATCH, **chưa bắt đầu**. Kiểm trước khi ghi: không tìm thấy 1.3.18 trong AGENTS/.context/docs; không chiếm version đang dùng/đã reserved.
- Lý do PATCH: tách phần sửa/hardening của collection hiện có trong cùng minor 1.3; không mở major hoặc feature architecture mới. Nếu thảo luận sau phát sinh kiến trúc/feature vượt phạm vi, phải review scope/SemVer trước implementation.
- Stack M1 nay gồm **1.3.17 (Block UI) + 1.3.18 (Editor UX/Technical)**. Scope cũ vẫn còn đủ; không coi toàn M1 hoàn tất khi chỉ UI PASS.
- Đây là scope refinement tại current, không phải chuyển sang 1.3.18. Không archive milestone/tension, không bump/build; không tự đóng 1.3.11/1.3.16. Quote Flow vẫn sau visual gate carousel; việc bắt đầu M2 là quyết định riêng, không tự kích hoạt do đổi bảng.

Các §1–§9 là baseline audit/acceptance tổng thể trước split; §7 giữ toàn bộ trace cũ. **Dùng §11 làm authority phân công version**, không đọc các dòng QA toàn diện cũ thành tất cả phải làm ở 1.3.17. Source findings/FAIL vẫn có giá trị; mục chuyển sang 1.3.18 có lifecycle **Deferred → target 1.3.18**, không đổi defect thành đã sửa.

### 11.1 Mapping ID và acceptance — không bỏ việc

| ID / acceptance gốc | 1.3.17 Block UI & Visual QA | 1.3.18 Editor UX & Technical |
|---|---|---|
| UI-01/02/02a | Header, typography, spacing, Product shell/full title, CTA thẳng hàng | Control parity tương ứng ở UX IDs |
| UI-03/03a | Product square/contain/ba media layouts; visual fallback; legacy output không bị đổi ngầm bởi sửa UI | TECH-08a migration/schema/read-render cơ chế chi tiết |
| UI-04/04a | Arrow/dots chrome, mobile arrows, visible/disabled feedback theo khả năng có thật; visual không clip | TECH-02/06 keyboard, loop/count/state machine đầy đủ |
| UI-05/06 | Footer/CTA, spacing, gap, 5 preset visual, responsive/resize/orientation, hover clearance | Runtime breakpoint/loop hardening TECH-06 |
| UI-07/08 | Post visual bàn riêng; empty/placeholder/Woo-inactive presentation | Query/dependency failure handling đầy đủ TECH-01/07 |
| UX-01–08, UX-02a | Chỉ ghi inventory và yêu cầu hướng đã chốt; không thiết kế panel/control/preview mới | **Deferred → 1.3.18**: controls có tác dụng, preview/query parity, labels/help/defaults/save/reload |
| TECH-01–09, TECH-08a | Chỉ smoke/regression tối thiểu §11.2 đối với UI thực sự đổi | **Deferred → 1.3.18**: engine/query, keyboard/markup, pause/reduce runtime, loop, security, migration, lifecycle/performance đầy đủ |
| TECH-10 | Build/lint/source–artifact verification cho thay đổi UI khi thực thi, theo workflow human | Build/artifact verification cho thay đổi 1.3.18; hardening release liên quan |
| TECH-11 | Test doc/visual evidence/human UI PASS riêng | Test doc/editor/runtime evidence/human PASS riêng |
| 039 §4 Product POC + Post approval | UI-01–08 với delta đã chốt §10/§11 | Không thay visual approval |
| 039 §4 arrows/dots/keyboard | Arrows/pill/visible feedback/layout, smoke click/swipe trên fixture UI | Keyboard ←/→, đầy đủ loop/non-loop/ít item/state correctness |
| 039 §4 autoplay/Pause/hover/focus/tab-hidden/reduce | Visual control + không thêm motion vi phạm reduce; smoke hiện có khi UI tác động | TECH-04/05 pause/autoplay state/coordination và reduce runtime đầy đủ |
| 039 §4 Card URL/quote context | Smoke link không bị UI che/đổi đích trên fixture | TECH-03/07 badge semantics, actions/context/query đầy đủ; CF7 vẫn M2 |
| 039 §4 Woo inactive/empty | UI presentation trên fixture | Graceful dependency handling/query correctness |
| 039 §4 responsive/console/memory/invalid-block/a11y/Grid | Geometry + minimum safety §11.2 | Full runtime/memory/editor/compat/a11y matrix |
| §4a 1.3.11 Product/Post Grid + Carousel native rendering/init | Grid shared-style visual regression và carousel smoke | Native query/render/init/conditional assets đầy đủ |
| §4a 1.3.11 5 presets desktop/mobile | Hình học/số card/spacing theo preset hiện có | Breakpoint logic/loop transitions, settings semantics |
| §4a 1.3.11 quote/view/read/context | Click smoke khi card UI đổi | Full action/query/context correctness |
| §4a 1.3.11 fallback/Woo inactive | Fallback/empty visual | Error/dependency handling đầy đủ |
| §4a 1.3.16 ratio/arrows/spacing/heading/eyebrow/intro | UI-01/03/04/06; Product mới square, legacy/Post không tự đổi | Attribute/preview parity và migration |
| §4a 1.3.16 archive/catalog label/toggle/URL | Footer visual + smoke bật/tắt/link fixture hiện có | Control/serialization/URL normalization đầy đủ |
| §4a 1.3.16 card variants/badge/4-2-1 | Supported visual modes/layout/badge | cardStyle legacy behavior, invalid markup, schema parity |
| §4a 1.3.16 CTA hover/reduce/focus-visible | CSS visual hover/focus/reduce khi sửa UI | Full shared/runtime motion/a11y logic |
| §4a 1.3.16 editor preview hover | Existing preview smoke chỉ nếu shared CSS tác động | **Deferred → 1.3.18** preview hover/parity audit và sửa |
| §4a General console/invalid-block/memory | Không tạo regression rõ trên surface vừa đổi | Full repeated interaction/memory/editor validation |

Các QA ngoài carousel đã dời M5/M6/1.3.4/parking trong 039 §4a vẫn giữ nguyên. Không dùng split này để xóa acceptance hoặc gán PASS. UI-03/TECH-08a giao nhau: nếu UI mới buộc đổi dữ liệu cũ, tách đường áp dụng cho content mới hoặc báo dependency để human quyết; không lén triển khai migration 1.3.18 trong 1.3.17.

### 11.2 Regression tối thiểu để thay UI an toàn ở 1.3.17

Đây là safety của phần UI được sửa, không kéo cả Technical trở lại current:

- Product/Post và Grid dùng shared CSS vẫn render; không che nội dung/CTA/controls, không tạo overflow/clip mới; kiểm các viewport/resize liên quan.
- Existing click/swipe/link/toggles cần dùng để xem UI vẫn thao tác được trên fixture; focus-visible và reduced-motion CSS của phần mới không bị bỏ.
- Content cũ mở/save/reload smoke khi markup/attributes thực sự bị đụng; không tự migrate ratio hay thêm settings architecture.
- Console/no-fatal/invalid-block smoke trên surface đổi; human build/lint đúng workflow trước onsite.
- Mỗi test ghi scope/build/fixture/evidence. Defect cũ còn tồn tại được ghi Deferred 1.3.18, không giả PASS. Nếu defect chặn việc nhìn/kiểm UI hoặc sửa UI buộc gây regression, báo dependency cụ thể để human quyết, không âm thầm mở toàn bộ technical scope.

Full keyboard/loop/pause/query/security/lifecycle/memory hardening và editor UX chỉ triển khai khi 1.3.18 được human bắt đầu, sau thảo luận sâu.

### 11.3 Khuyến nghị đã chốt ở mức hướng — chưa thiết kế chi tiết

| Hướng đã chốt | Phạm vi áp dụng | Phần còn mở |
|---|---|---|
| CTA Product thẳng hàng giữa card cùng hàng; tên vẫn đầy đủ | Block UI 1.3.17 | Cơ chế equal-height/body flex và số đo; không cắt title để căn CTA |
| Controls chỉ hiện khi có tác dụng, không tạo kỳ vọng sai | Visual controls 1.3.17; editor policy 1.3.18 | Hide hay disabled/giải thích theo state cụ thể |
| Post bàn riêng | Block UI 1.3.17 và editor tương ứng 1.3.18 | Post title/ratio/card details |
| Preset + điều chỉnh có giới hạn + reset rõ | Direction Editor UX 1.3.18 | Options/range/default, reset semantics, panel layout; không phải approve kiến trúc mới |
| Preview trung thực + hướng dẫn ngắn | Editor UX 1.3.18 | Component/design/wording chi tiết; không chạy Swiper/autoplay |
| Inset padding vài mức token, đều top/left/right; radius tự tính theo geometry | Visual direction 1.3.17; selection controls 1.3.18 | Token names/values/default, border-distance formula cụ thể và bounds |

Bảng này supersedes các dòng “preset + bounded controls đang đề xuất/chưa chốt” và “CTA alignment còn mở” ở §10 về **hướng**; rationale/trade-off vẫn giữ. Các giá trị, defaults, cơ chế reset và cách code không được suy ra đã duyệt.

### 11.4 Inventory settings tạm — chỉ phân loại, chưa thiết kế UX

Đây là nơi lưu yêu cầu để bàn ở 1.3.18, không phải panel layout/order hay cam kết phải expose tất cả setting. Có thể một kết quả visual được preset sở hữu, không cần control riêng.

| Nhóm tạm | Nội dung cần giữ trong inventory | Scope / chưa chốt |
|---|---|---|
| Content | Query/source/category/tag/relation/order/count; heading/eyebrow/intro; image/date/author/excerpt visibility; action labels/URLs, archive/catalog content | 1.3.18 review control semantics/parity; 1.3.17 chỉ chốt output visual |
| Style | Card visual/palette, badge/chip appearance, media layout presets và inset spacing token/radius tự tính | 1.3.17 visual directions; 1.3.18 cách lựa chọn/bounds; chưa values/default |
| Layout | Responsive preset, media placement/inset/floating size-offset, spacing, equal-height/CTA alignment, arrow/footer placement | Frame Product mới cố định 1:1; không ratio control mới. Vị trí và giới hạn cụ thể chưa thiết kế |
| Advanced | Autoplay/delay/pause behavior, accessibility labels/behavior, quote-context option và compatibility nếu có điều chỉnh dành editor | 1.3.18 thảo luận; không tự expose technical internals hoặc tạo sidebar riêng |
| Xuyên nhóm | Reset, contextual visibility, disabled reasons, preview/help | Hướng đã chốt; reset semantics/panel arrangement/architecture còn mở |

Media layout là cách bố trí ảnh, không tự thành full-card preset. Legacy ratio lưu để compatibility analysis, không biến thành control mới Product. Post settings inventory riêng khi bàn Post. Không thêm schema/API/controls hoặc mockup UX trong lượt này.

### 11.5 Thứ tự tiếp tục sau split

1. Tiếp tục chốt chi tiết **Block UI 1.3.17**; giữ source baseline và giải thích §10.
2. Thực thi/test UI theo phạm vi đã chốt với regression tối thiểu; human visual acceptance riêng.
3. **1.3.18 PLANNED** giữ đủ UX/TECH findings, acceptance và setting inventory; trao đổi sâu sau và chỉ start khi human yêu cầu.
4. §8 correctness-first của baseline tổng thể được thay bằng phân công version này; không giao executor “sửa hết TECH/UX trước UI” trong current.

## 12. Kế hoạch chi tiết trước handoff

Editor preview direction được human chốt ngày 2026-10-19 tại §13 bên dưới; target 1.3.18, chỉ thử artifact độc lập trước implementation.

**Human bổ sung 2026-10-16:** chốt tóm tắt Product theo từng nhóm thuộc tính, một setting2/3/4 giá trị mỗi nhóm và chỉ báo+N; skeleton tĩnh riêng vùng carousel dùng chung preset/geometry với nội dung thật và có fallback; alignment Normal/Wide/Full **default Wide** cho carousel. Đặc tả, rationale, giới hạn và acceptance tại [042 §11](042_VER_1_3_17_CAROUSEL_VISUAL_SPEC.md#11-human-lock-2026-10-16--attribute-summary-local-skeleton-alignment). Inventory: Content → giới hạn giá trị; Layout → alignment. Chưa chọn default2/3/4 hoặc cơ chế legacy thiếu alignment; không bulk migrate, chưa implement các mục này, chưa mở milestone1.3.18. Các điểm này là quyết định bổ sung cụ thể, không approval toàn bộ Editor UX/Technical.

[041 — Kế hoạch thực hiện Block UI 1.3.17](041_VER_1_3_17_CAROUSEL_BLOCK_UI_EXECUTION_PLAN.md): W0–W6, gates G1–G5, file boundaries, test cases V01–V10 và mẫu handoff. Human yêu cầu lập kế hoạch 2026-10-14; chưa dispatch implementation. Không thay scope split §11 hoặc tự phê duyệt chi tiết còn mở.

## 13. Editor preview: khung width và danh sách tự xuống dòng — human lock 2026-10-19

**Authority:** Human đồng ý các đề xuất sau khi review screenshot editor ngày 2026-10-19, yêu cầu lưu và chuẩn bị prompt cho agent tạo artifact thử. Đây là phê duyệt hướng và prototype, không bắt đầu milestone 1.3.18 hoặc cho phép sửa plugin. Current vẫn 1.3.17. Phần này bổ sung chi tiết được yêu cầu vào inventory §11; không mở toàn bộ thiết kế Editor UX/Technical.

### 13.1 Vấn đề và evidence

- Screenshot cho thấy card mẫu bên trái có khoảng trắng lớn, bên phải xếp liên tiếp cấu hình, roadmap woo-catalog và danh sách tên dài. Thông tin ít nhưng block cao, khó rà đọc.
- Source đọc ngày 2026-10-19: `src/collection/style.css` selector `.skvn-collection--editor-preview .skvn-collection__editor-body` dùng `260px 1fr`; `src/collection/controls.tsx` chứa `PlaceholderCard`, `EditorInfoPanel`, `useQueryPreview`. Đây là source evidence, chưa đo DOM để chứng minh cơ chế stretch onsite.
- Screenshot còn chip mẫu cũ; source đã có nhóm mẫu Size/Pack/Freezing cho carousel. Chưa xác nhận build/layout của screenshot; không quy kết phần giới hạn thuộc tính mới FAIL.

### 13.2 Hướng đã chốt và rationale

1. Vùng minh họa card gọn căn giữa, chiều cao theo nội dung; cấu hình hiển thị gọn bên dưới. Không kéo card cao theo danh sách query. Nhãn rõ “Dữ liệu mẫu”; không khiến người dùng tưởng card đang dùng dữ liệu sản phẩm thật.
2. “Sản phẩm trong truy vấn” là một vùng riêng bên dưới, sử dụng toàn bộ chiều rộng khả dụng. Tên căn trái, thứ tự trái sang phải rồi xuống dòng. Cloud ở đây nghĩa là danh sách wrap, không phải tag cloud có chữ lớn nhỏ/vị trí ngẫu nhiên.
3. Tên đầy đủ, cùng cỡ chữ, có số thứ tự nhỏ. Mục tĩnh không tạo hình thức nút/tag có thể bấm. Tên dài được wrap bên trong mục; không cắt tên, giảm chữ hoặc ép khoảng cách khi hẹp. Khi thiếu chiều ngang, cho phép vùng cao lên.
4. Danh sách dài: grid responsive đọc theo hàng, hiện trước tối đa 3 hàng: 3/2/1 cột tương ứng 9/6/3 tên; số còn lại qua nút “Xem thêm … sản phẩm”, có thu gọn. Human xác nhận đồng bộ hướng này ngày 2026-10-19 sau khi agent artifact ghi nhận quyết định grid. Thay thế phương án ban đầu luôn hiện 5 tên: grid tận dụng chiều ngang nhưng giữ tổng chiều cao vừa phải khi hẹp. Đây là tương tác editor, không thay `+N` thuộc tính frontend (chỉ thông tin, không tương tác). Breakpoint prototype 380/700px theo content-box container chỉ là giá trị thử, chưa khóa token production.
5. Cấu hình dùng nhãn–giá trị/Bật–Tắt, không đưa mọi thứ thành cloud. Nhãn “Cấu hình hiển thị” thay “Card data”; bật Image không đồng nghĩa sản phẩm có ảnh. Equal height thuộc bố cục: artifact chỉ hiển thị “Bật (mẫu)” và giải thích một card không minh họa được căn đều nhiều card, không tạo switch vô tác dụng. Chỉ control ảnh thực sự đổi preview; các giá trị còn lại là tóm tắt minh họa, không khóa thiết kế control production. Bỏ roadmap `woo-catalog 1.5.0` và các trường tương lai khỏi vùng làm việc chính.
6. Viền nhẹ bao quanh ranh giới thực của block, nhãn `Width: Normal / Wide / Full`. Nhãn biểu thị lựa chọn; khung biểu thị không gian thực có trong editor. Khi canvas hẹp, các chế độ có thể cùng chiều rộng; không vẽ rộng giả hoặc ép tràn. Full là hết vùng chứa cho phép, không mặc định hết màn hình. Default Wide theo quyết định trước.
7. Viền/nhãn chỉ editor, không xuất frontend; phân biệt với viền chọn block Gutenberg. Responsive dựa vào chiều rộng thực của vùng chứa, gồm trường hợp sidebar mở hoặc block nằm trong Group/Columns.
8. Query preview phải nói rõ giới hạn nếu chỉ gần đúng; phân biệt loading, empty và error. Prototype dùng fixture có nhãn mô phỏng, không coi danh sách mẫu là query thật.

**Phương án không chọn:** ba cột luôn cố định; cloud căn giữa từng dòng; tag cloud đổi cỡ chữ; card stretch theo danh sách; khung Normal/Wide/Full giả tạo vượt container. Ba cột trước đó chỉ là đề xuất, được thay bằng hướng vùng minh họa căn giữa + vùng query full-width này.

### 13.3 Acceptance và trạng thái

| ID | Nhóm | Hành vi mong đợi | Hiện trạng | Việc cần sửa/thử | Cách kiểm | Evidence |
|---|---|---|---|---|---|---|
| UX-09 | Editor UX | Card mẫu gọn, không stretch theo query; cấu hình rõ nghĩa | Production Deferred → 1.3.18; artifact đã có source | Human review độ gọn/nhãn | Toggle ảnh; so card trước/sau mở danh sách | §13.5: selftest card-height-stable, equal-height-summary-only; ảnh Wide |
| UX-10 | Editor UX | Grid có thứ tự, đủ tên, hiện 9/6/3, xem thêm/thu gọn | Production Deferred → 1.3.18; artifact automated checks PASS | Keyboard thật/screen reader chờ kiểm chứng | 20 tên dài, 3/2/1 cột, toggle và focus | §13.5: grid/toggle tests và ảnh narrow |
| UX-11 | Editor UX | Khung đúng geometry, nhãn width đúng lựa chọn | Production Deferred → 1.3.18; artifact geometry PASS trong phạm vi mô phỏng | Gutenberg/Group/Columns cần kiểm riêng | 1600/1200/800/480/320px; 320 khóa sidebar cạnh nội dung | §13.5: Normal 760/Wide 1200/Full 1566px ở canvas 1600 |
| TECH-12 | Technical | State/semantics trung thực, không fetch theo từng sản phẩm chỉ để làm mockup | Production Deferred → 1.3.18; artifact state tests PASS | Keyboard thật/screen reader chờ kiểm chứng | Fixture loading/empty/error/results, console error listener | §13.5; không thay evidence WordPress/runtime |
| UI-09 | Block UI | N/A — prototype editor không đổi frontend | N/A cho artifact | Không sửa frontend/width đã nghiệm thu | Kiểm file scope | Chỉ planning hiện tại; artifact độc lập ở bước sau |

### 13.4 Phạm vi artifact được phép

Tạo HTML/CSS/JS độc lập để human thử; không sửa plugin/theme, không deploy, không bump/build hoặc chuyển milestone. Mô phỏng canvas/sidebar và lựa chọn width; hiển thị riêng các control thử nghiệm ngoài khung editor. Giá trị kích thước Normal/Wide trong mockup chỉ mô phỏng và phải ghi rõ, chưa chốt token production. Kiểm container 1200/800/480/320px, danh sách 0/1/5/20, tên dài, bật/tắt ảnh, loading/error, keyboard và console. Báo PASS/FAIL/Chờ kiểm chứng đúng phần thực sự chạy; prototype PASS không phải Gutenberg/onsite PASS. Chưa chốt breakpoint, token spacing, màu viền hoặc kiến trúc query thực tế; dùng artifact để đánh giá trước.

### 13.5 Artifact revision và evidence — 2026-10-19

- Artifact: `docs/artifacts/editor-ui/carousel-editor-preview-prototype.html`; báo cáo cùng thư mục `.md`; test protocol: `docs/testing/carousel-editor-preview-prototype.md`.
- Human giao hoàn thiện ba việc: đồng bộ grid, làm rõ control minh họa, bổ sung evidence và canvas lớn. Thêm canvas 1600px để Wide/Full khác nhau thực sự.
- Canvas 320px không hỗ trợ sidebar cạnh nội dung rộng 280px trong prototype: đóng và khóa control, có giải thích. Thay cách miễn trừ overflow cũ; không suy ra Gutenberg mobile cũng xử lý như vậy.
- Chạy Chrome headless ngày 2026-10-19: **172 graded / 172 PASS / 0 FAIL**; log `docs/artifacts/editor-ui/evidence/selftest.txt`, ảnh `wide-1600.png`, `full-1600.png`, `narrow-320.png` cùng thư mục. Đây là automated artifact evidence, không phải approval mỹ thuật, kiểm keyboard thật hoặc WordPress onsite.
- Báo cáo cũ được tạo trong môi trường không có planning 040 theo lời agent trước; điều đó không mô tả workspace hiện tại. Các số 131/144 và screenshot thiếu chỉ giữ làm lịch sử, không dùng làm evidence hiện hành.
