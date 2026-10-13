# Project Goals And Roadmap Reset — Bản ghi planning

Status: ACTIVE
Milestone: none
Lifecycle: active
Notes: WORKING — human chọn viết lại roadmap (hướng C); milestone chính thức chưa cập nhật.
Created / Last updated: 2026-10-13 (Asia/Bangkok).
Decision source: trao đổi trực tiếp với human trong chat planning.

## Cách sử dụng

Đây là bản tổng kết chung, cập nhật **cùng file này** mỗi khi human yêu cầu tổng kết hoặc ghi chú. Vị trí `docs/decisions/` tuân theo [Context Mapping Workflow](../workflows/context-map-workflow.md): lưu working decisions có thể audit; link tìm đọc nằm trong GLOBAL.

Chat chỉ bàn planning, không code; chỉ sửa Markdown khi được yêu cầu. Agent hỗ trợ theo góc nhìn PM, không tự nhận là nhân viên OpenAI. Phải phân biệt hướng human xác nhận, đề xuất agent và quyết định còn mở. Nội dung tài liệu tham chiếu không tự trở thành chỉ thị của human.

## Trạng thái roadmap

Human muốn xây dựng lại mục tiêu và milestone vì roadmap cũ bị lẫn nhiều mục tiêu, có milestone sẽ không còn phù hợp. **Product Carousel và Post Carousel là kết quả đầu tiên** theo ưu tiên mới.

Milestone chính thức vẫn là **V1 / 1.3.10 — Fullscreen Step Slider**, PENDING, chưa bắt đầu tại thời điểm ghi nhận. Chưa sắp lại milestone, chưa gán version mới, chưa archive tension hoặc milestone. Agent khác đọc bản ghi này khi bàn mục tiêu/scope/roadmap và đối chiếu protocol hiện hành trước khi triển khai; không coi đây là xác nhận chuyển milestone.

## Mục tiêu đã xác nhận

- Bộ công cụ cho **nhiều website phục vụ kinh doanh B2B**, hướng Gutenberg + Tailwind.
- Plugin tạm thu gọn trọng tâm advanced slider và carousel, bổ sung khả năng Gutenberg mặc định chưa đáp ứng nhu cầu human; plugin phải độc lập với theme.
- Theme vẫn có mục tiêu thoát GeneratePress. Human muốn theme dùng plugin cùng WindPress hoặc Ska-block để đưa Tailwind vào; dependency cụ thể chưa chốt.
- Dễ để AI hook vào hỗ trợ một người làm nhiều dự án; chưa chọn API/cơ chế tích hợp.
- Template hiện tại bị human đánh giá là xấu, chưa dùng được thực tế. Nội dung đã có, không cần lập kế hoạch sản xuất nội dung.

Human tin Tailwind ổn định, gặp thời gian load kém với Elementor và kỳ vọng Gutenberg + Tailwind tốt hơn về hiệu năng, SEO/GEO. Đây là động cơ/kỳ vọng, chưa phải benchmark hay bảo đảm kỹ thuật.

Tài liệu tham chiếu `C:/Users/VPF-Champion/Downloads/skvn-marine-tailwind-3-phuong-an-planning.md` có ba phương án Tailwind A/B/C; chưa chọn phương án nào. Hướng C cho roadmap là lựa chọn khác, không phải phê duyệt phương án Tailwind C. Chưa chốt version Tailwind, build pipeline, prefix, Preflight, token mapping hay ownership CSS. Hướng production mới cần đối chiếu contract hiện hành đang coi Tailwind/WindPress là prototype; bản ghi chưa sửa invariant hoặc cho phép triển khai.

## Header/footer và slider

Human cần quản lý header/footer. Header phải quản lý và thiết kế như một trang Gutenberg, thiết kế linh hoạt, có giới hạn chiều cao và tùy chọn sticky. Chưa chốt giới hạn cụ thể, lưu bằng Page hay reusable element/CPT, display rules hoặc ownership quản lý/render. Footer chưa có acceptance chi tiết.

Slider cần settings hoàn chỉnh, có tổ chức; human đánh giá settings hiện tại là ráp tình thế. Các thiết kế slider mới sẽ theo design inspiration tìm được sau này.

## Product/Post Carousel đầu tiên

- Product Carousel làm **dữ liệu mặc định WooCommerce trước**; Post Carousel vẫn nằm trong kết quả đầu tiên.
- MOQ, lead time và chứng nhận chưa bắt buộc xuất hiện; nếu bổ sung trước mắt có thể là text. Chưa yêu cầu structured fields/query riêng.
- Human xác nhận engine đã hỗ trợ swipe/kéo, trước–sau, pagination, autoplay có pause và card link. Chưa kiểm thử runtime trong chat; không coi đây là yêu cầu viết engine mới.
- **POC chuẩn đã chốt:** `D:/Github/skvn-marine/.local/product-carousel-swiper-poc-03.html`. Muse đối chiếu implementation với file này; không xin human chọn/chốt lại POC.
- Qua đọc source trước đó: POC có preset 1–5 card desktop và số lượng tablet/mobile tương ứng, navigation, pagination, CTA; sản phẩm là dữ liệu tĩnh, chưa có demo Post riêng. Human đang cho AI tắt autoplay ở POC, chưa quyết bỏ autoplay khỏi sản phẩm.
- POC là chuẩn giao diện đã chốt; các trường B2B trong demo không tự thành scope bắt buộc. Chưa kiểm thử trực quan/runtime; file `.local` có thể không có trên checkout khác.

## Settings — ghi chú để thảo luận chuyên sâu sau

Human ban đầu muốn tab cạnh Trang/Bài viết và Block, chỉ hiện khi chọn block plugin. Ảnh Ska-block/WindPress cho thấy các panel trong Block và sidebar riêng mở từ biểu tượng toolbar. Agent đề xuất tên SKVN/SKVN Blocks, **human chưa chốt tên**; PluginSidebar không tự bảo đảm tab thứ ba, giải pháp cho vị trí đó chưa được xác minh.

**Ghi chú mới của human: có thể giữ settings cơ bản trong tab Block và đưa settings nâng cao vào sidebar riêng. Sẽ thảo luận chuyên sâu sau.**

Đây là hướng ưu tiên để tiếp tục bàn UX, chưa phải đặc tả triển khai cuối cùng. Chưa chốt control nào cơ bản/nâng cao, cách mở/đóng, tự chuyển sidebar hoặc trạng thái khi chọn block khác/chọn nhiều block. Không tự chuyển toàn bộ settings sang sidebar riêng. Settings của block đang chọn cần phân biệt với cấu hình chung của plugin trong ảnh tham chiếu. Các nhóm settings mà agent từng đề xuất chưa được duyệt.

## Responsive và môi trường

Điện thoại là tiêu chí quan trọng; Duo/Fold chỉ cần responsive tốt, không yêu cầu chế độ riêng cho bản lề/hai màn hình. Chưa chốt breakpoint hoặc ma trận thiết bị. Website vẫn phát triển; staging không chặn planning hiện tại, không có nghĩa bỏ QA hoặc cho phép tự thay đổi production.

## Việc còn mở

- Phân chia settings cơ bản/nâng cao và luồng sidebar.
- Cụ thể hóa acceptance/test theo POC đã chốt và dữ liệu mặc định; không mở lại quyết định chọn POC. Post chưa có demo riêng thì nêu khoảng trống đó, không suy diễn đã có thiết kế chi tiết.
- Chốt kiến trúc Tailwind/dependency bảo đảm plugin độc lập.
- Làm rõ đặc tả header/footer mới sau theme độc lập; thứ tự này đã chốt. Tạm giữ header/footer GeneratePress.
- Rà soát milestone cũ để giữ, cập nhật, gộp, hoãn hoặc bỏ; chưa tự quyết status/version.
- Các điểm từng phát hiện để audit: DONE nhưng còn checklist/QA, QA 1.3.11/1.3.16 trùng, hướng launch chưa đồng nhất giữa tài liệu. Chưa resolve bằng bản ghi này.
- Chưa thay đổi slug/namespace, tên sản phẩm hoặc kế hoạch launch đã có.

## Quyết định bổ sung — roadmap C và handoff Muse

### Human đã chốt

- **Chọn hướng C:** viết lại roadmap theo mục tiêu mới, dùng milestone cũ để đối chiếu/truy vết; không chỉ sắp lại thứ tự roadmap cũ.
- Chat này phụ trách **planning, phản biện, review và soạn plan/prompt**; **chat Muse thực thi**. Lượt lưu này chưa gửi prompt sang Muse.
- **Product/Post Carousel là kết quả đầu tiên**, tập trung hoàn thiện giao diện và kiểm thử trước.
- **Quote Flow sau khi giao diện carousel hoàn chỉnh và qua bước test**; chưa kéo tích hợp form, lưu submission hoặc kiểm chứng end-to-end vào mốc carousel.
- **Slider dời sang mốc sau** để tập trung carousel; chưa phát triển Hero/Parallax/Step Slider trong mốc carousel mới.
- **Header/footer mới sau theme độc lập.** Tạm lấy header/footer GeneratePress đang có; chưa thiết kế hệ thống thay thế trong mốc carousel.
- **POC `.local/product-carousel-swiper-poc-03.html` đã chốt.** Muse audit khoảng cách implementation với chuẩn này, không xin chọn lại thiết kế.
- Settings basic trong Block, advanced ở sidebar riêng là hướng để thảo luận sâu sau; chưa duyệt toàn bộ kiến trúc settings.

### Phản biện/đề xuất của agent — chưa coi là human đã duyệt

- Dời slider không đồng nghĩa bỏ runtime/Swiper carousel đang dùng; đề xuất sửa lỗi nền dùng chung có giới hạn nếu nó chặn carousel.
- Trước Quote Flow, đề xuất card dùng link sản phẩm/bài viết để test giao diện; nếu có CTA báo giá, test hình thức không chứng minh form hoạt động.
- Đề xuất đủ settings cơ bản để test carousel, không để sidebar nâng cao/bộ settings toàn diện chặn kết quả đầu tiên.
- Theme độc lập không tiếp tục dựa vào parent GeneratePress; cách thay/chuyển header/footer tạm ở boundary đó cần planning, chưa chốt cơ chế kỹ thuật.
- Viết lại roadmap không đồng nghĩa xóa code, reset dữ liệu, đổi namespace hoặc version metadata.

### Roadmap mới — khung đề xuất chưa duyệt toàn bộ

| Nhãn thảo luận | Kết quả dự kiến | Trạng thái |
|---|---|---|
| M1 | Product/Post Carousel hoàn chỉnh và test giao diện | Ưu tiên human đã chốt; acceptance chi tiết cần lập |
| M2 | Quote Flow từ carousel | Human chốt sau test giao diện carousel |
| M3 | Nền theme độc lập, kiến trúc Tailwind, chuyển phụ thuộc GeneratePress | Nội dung/đánh số do agent đề xuất |
| M4 | Hệ thống quản lý header/footer mới | Human chốt sau theme độc lập; đặc tả còn mở |
| M5 | Slider trưởng thành và hệ thống settings | Human chốt slider sau carousel; vị trí chính xác chưa duyệt |
| M6 | Template thực tế, kiểm chứng toolkit nhiều website, chuẩn bị phát hành | Đề xuất agent chưa duyệt |

M1–M6 là nhãn thảo luận, không phải SemVer/milestone chính thức. Chưa chốt thứ tự Quote Flow so với mọi việc khác, số mốc cuối cùng, thời điểm launch hoặc hủy từng milestone cũ.

### Mapping cũ → mới cần review

- `1.3.3` + `1.3.7` + planning `038`: nền carousel mới; cập nhật artifact chuẩn, phạm vi Product/Post và dữ liệu mặc định. DONE cũ không chứng minh đạt POC mới.
- `1.3.10`: Step Slider dời sang phần slider sau; không tự đổi milestone cũ thành carousel.
- `1.3.11` + `1.3.16`: agent đề xuất gộp QA trùng và đưa QA carousel vào acceptance carousel; chưa duyệt cách gộp cụ thể.
- `1.1.2` + quote/source_url của `1.4.1`: xem xét gom Quote Flow sau test giao diện; map/layout validation xử lý riêng.
- `1.5.0` Woo Catalog: agent đề xuất hoãn, không chặn carousel dùng dữ liệu mặc định; chưa quyết hủy catalog.
- Core Control, TOC, credits, custom icon, setup UI, trang Chuyển đổi số, surface presets, launch: đề xuất dời/loại trước đó cần review từng mục; không coi human duyệt toàn bộ.
- `2.0.0 → 3.0.0`: giữ để truy vết hướng thoát GeneratePress; chưa chốt lại version này.

### Handoff đầu tiên cho Muse

Chuẩn bị roadmap C draft, bảng milestone cũ → mới và kế hoạch M1 dựa trên audit code hiện có. Đọc POC đã chốt, xác định reuse/gap, files dự kiến sửa, acceptance và checklist test/handoff cho human.

Prompt chuẩn bị là bước planning/audit Markdown, **chưa phải lệnh implement**. PM review rồi soạn prompt thực thi phạm vi cụ thể. Không tự đổi current milestone, archive, bump version hoặc sửa source trong bước chuẩn bị. Khi roadmap được duyệt, kế hoạch áp dụng phải nêu các docs/context cần đồng bộ và giữ truy vết lịch sử cũ.

## Nguồn đọc tiếp

- [Global context](../../.context/GLOBAL.md)
- [Milestones chính thức](../../.context/MILESTONES.md)
- [Open tensions](../../.context/TENSIONS_OPEN.md)
- [Active tensions](../../.context/TENSIONS_ACTIVE.md)
- [Plugin context](../../.context/modules/PLUGIN_SKVN_MARINE_BLOCKS.md)
- [Theme context](../../.context/modules/THEME_SKVN_MARINE.md)
- [Context Mapping Workflow](../workflows/context-map-workflow.md)

## Lịch sử cập nhật

- 2026-10-13: tạo bản ghi chung sau khi xác minh lượt ghi trước chưa tạo file; bổ sung dữ liệu mặc định WooCommerce và hướng settings cơ bản trong Block/nâng cao trong sidebar riêng. Milestone chính thức chưa sắp lại.

- 2026-10-13: lưu quyết định roadmap C, vai trò PM/Muse, Quote Flow sau test giao diện, slider ở mốc sau, header/footer mới sau theme độc lập và POC chuẩn đã chốt. Phân biệt khung M1–M6/đề xuất mapping với quyết định human; chưa đổi milestone chính thức.

- 2026-10-13: lượt audit/planning (Muse, chưa implement). Thêm 3 section: `Audit nền carousel hiện có (Muse)`, `Roadmap C draft — mapping cũ → mới (Muse proposal)`, `Kế hoạch carousel M1 (Muse proposal)`. Không đổi các quyết định human đã chốt ở trên; không đổi milestone/version/source.

---

## Audit nền carousel hiện có (Muse) — 2026-10-13, chưa implement

> Phạm vi đọc: `src/collection-view.ts`, `src/collection/style.css`, `src/collection/constants.ts`, `src/collection/controls.tsx`, `src/product-collection/block.json`, `src/post-collection/block.json`, `modules/collection-render/{cards,collection-render,product-collection,post-collection,query-products}.php`, `package.json`, `docs/decisions/collection-carousel-engine-keep-swiper.md`, `.context/planning/038_*`, POC `.local/product-carousel-swiper-poc-03.html`. Không chạy build/lint; không sửa source.

### A1. Collection đã có gì (source, không phải runtime evidence)

- Hai dynamic block đã đăng ký: `skvn-marine/product-collection` (`src/product-collection/block.json:3`) và `skvn-marine/post-collection` (`src/post-collection/block.json:3`). Saved content chỉ lưu attributes (đúng contract 1.3.3).
- Query: product qua `wc_get_products` (`modules/collection-render/query-products.php:59`), có pool shuffle tránh `ORDER BY RAND` (`query-products.php:70-120`); post qua WP native (theo contract, file `query-posts.php` chưa đọc sâu trong lượt này — ghi nhận, không khẳng định chi tiết).
- Woo inactive không fatal: fallback message (`modules/collection-render/product-collection.php:50-53`).
- Grid + Carousel là layout mode (`src/collection/constants.ts:3-6`); 5 responsive preset `1-1-1/2-1-1/3-2-1/4-2-1/5-3-1` đã có trong editor (`constants.ts:18-24`) và CSS grid + Swiper config đọc preset (`cards.php:360-380`).
- Header (eyebrow/heading/intro) + arrows ở header (`cards.php:305-344`); footer (pagination slot + catalog CTA + archive link) (`cards.php:249-292`); carousel wrapper `.swiper/.swiper-slide` + config JSON (`cards.php:359-413`).
- Settings tối thiểu để cấu hình/test **đã tồn tại**: `showArrows`, `showPagination`, `autoplay` (default `false`), `autoplayDelay` (3–10s clamp), `responsivePreset`, `imageRatio` (product default `1:1`, post default `16:9`), `showImage`, `badgeBehavior`, card field toggles, action modes (`product-collection/block.json:118-165`, `post-collection/block.json:114-153`).

### A2. Kiểm chứng claim "engine đã hỗ trợ" từ source (không coi là runtime PASS)

| Claim của human | Source xác minh | Kết luận |
|---|---|---|
| Swipe/kéo | `new Swiper(...)` (`src/collection-view.ts:100`); Swiper `^11.1.15` (`package.json:11`) | Có trong source; cần runtime evidence (init không lỗi, kéo tay) |
| Trước–sau | `Navigation` module + `prevEl/nextEl` (`collection-view.ts:62,78-80`) | Có trong source; cần evidence onsite |
| Pagination | `Pagination` + `renderBullet` button (`collection-view.ts:63,82-90`) | Có trong source; cần evidence dots đồng bộ |
| Autoplay có pause | `Autoplay` + `createAutoplayPauseCoordinator` + Pause/Play button (`collection-view.ts:64,92-140`) | Có trong source; autoplay default **off** (`block.json:126-129`) khớp hướng test giao diện trước |
| Card link | Media/title link tới product/post URL; quote URL giữ context (`cards.php:107,117,184-196`) | Có trong source; Quote URL đúng nhưng **không chứng minh CF7/CFDB7 hoạt động** |
| Keyboard/a11y | Có A11y nhưng chưa import/configure Keyboard trong `src/collection-view.ts` | Chưa chứng minh hỗ trợ phím ←/→; cần kiểm tra và xử lý trong phạm vi carousel |
| Reduced motion | `prefersReducedMotion()` gate autoplay (`collection-view.ts:51`) + CSS `transition: none` (`style.css:368-372`) | Có trong source |

Phân biệt rõ: bảng trên là **source-có**, không phải **runtime-đạt**. Runtime PASS thuộc checklist test M1 bên dưới.

### A3. Lệch POC-03 ở đâu (POC là chuẩn, không mở lại chọn POC)

POC-03 (`product-carousel-swiper-poc-03.html`) là Swiper với 5 section preset cố định, `spaceBetween: 20`, breakpoints `640/1024` (`poc-03.html:714-743`), card shell radius `14px`, body `padding: 18px/gap: 13px` (`poc-03.html:159-232`), CTA navy → hover trust-blue (`poc-03.html:293-308`), pagination pill active `width: 26px` (`poc-03.html:359-372`), arrows tròn 46px header (`poc-03.html:133-151`), media product `1:1` trên **media box** (`poc-03.html:176-181`).

Delta chính của implementation hiện tại:

1. **Geometry gần nhưng chưa khóa POC:** `spaceBetween: 16` (`collection-view.ts:69`) vs POC `20`; breakpoints trùng `640/1024`; Product Carousel variation đã dùng `4-2-1` (`product-collection/variations.ts:23`), còn default block gốc/Grid là `3-2-1`. Không đổi default toàn block. Không có mobile peek `80%` như artifact cũ trong 038 — POC-03 không yêu cầu peek (mobile `slidesPerView: 1`), nên **không mang peek vào M1** trừ khi human yêu cầu.
2. **Card visual:** radius `8px` (`style.css:147`) vs POC `14px`; body `1rem/0.65rem` (`style.css:184-190`) vs POC `18px/13px`; CTA radius `4px`, hover **teal** (`style.css:485-500`) vs POC radius `9px`, hover **trust-blue**; shadow hover yếu hơn POC; spec-tag/cert dùng `• ::before` text (`style.css:449-457`) vs POC **pill + 5px teal dot** (`poc-03.html:257-272`); stats grid 2 cột thiếu divider (`style.css:460-466`) vs POC flex + divider (`poc-03.html:274-282`); class cert hiện tại là `skvn-collection-card__cert-dot` (`cards.php:154`), POC là `skvn-collection-card__cert` + `<i>` — **ưu tiên giữ class, sửa CSS; không yêu cầu DOM giống hệt POC**.
3. **Media ratio:** ratio đặt trên `img` (`style.css:220-224`) nên media box không vuông khi thiếu ảnh; POC đặt `aspect-ratio` trên **media box** (`poc-03.html:176-182`).
4. **Arrows:** text `‹ ›` (`cards.php:336-337`) vs POC SVG; kích thước/border khác. Nằm header — đúng hướng POC (không bị clip).
5. **Pagination:** dots tròn (`style.css:334-347`) vs POC pill active dài; vị trí footer-left hiện tại vs POC footer flex — cần lock theo POC.
6. **Header/footer chrome:** header `align-items: flex-start`, `margin-bottom: 1.25rem` (`style.css:7-12`) vs POC `flex-end`, `28px` + eyebrow `letter-spacing: .16em` (`poc-03.html:85-103`); catalog CTA hiện tại outline teal (`style.css:95-109`) vs POC sky-50 box (`poc-03.html:374-384`).
7. **Post card gap:** POC-03 **có CSS article** (`--article` media `16:9`, date/excerpt/read-more, `poc-03.html:182,233,324-347`) nhưng **không có section Post demo nào được render** — JS chỉ clone section product (`poc-03.html:686-698`). Khoảng trống: **chưa có chuẩn visual Post card sống để đối chiếu**, chỉ có CSS gợi ý. Đề xuất kế thừa (không mở lại chọn POC): Post carousel reuse 100% track/header/footer/pagination/arrows của Product; card gồm media `16:9` + category overlay + date + title + excerpt (clamp 3) + read-more — đối chiếu bằng dữ liệu WP thật trong M1, human duyệt visual Post như một acceptance riêng.

### A4. Reuse được

- Toàn bộ query/render PHP (`query-*.php`, `cards.php`, `product/post-collection.php`), config JSON → Swiper (`cards.php:372-380` → `collection-view.ts:31-76`), pause coordinator + reduced-motion + teardown (`collection-view.ts:104-160`), header/footer renderer, preset system, editor controls/query preview (`controls.tsx`). **Không viết lại collection hoặc engine.**
- Decision giữ Swiper (`docs/decisions/collection-carousel-engine-keep-swiper.md:12-13`, human chốt 2026-10-07) vẫn hiệu lực — M1 làm trên Swiper, không mở lại B/C của planning 038 trừ khi measurement触发 revisit trigger (`collection-carousel-engine-keep-swiper.md:55-59`).

### A5. Lỗi/thiếu sót cần xử lý để đạt POC (dự kiến,待 runtime xác nhận)

- (a) Token màu: POC dùng `--skvn-color-navy #082f49` + `--tmp-*` neutral chưa có token (`poc-03.html:13-33`); theme hiện tại navy `#073b5a` (THEME module). Cần map, không hardcode hex mới vào saved content.
- (b) `imageRatio` CSS đặt sai layer (mục A3.3).
- (c) Không cần rename cert class để đạt giao diện POC; cert chưa bắt buộc trong M1. Ưu tiên giữ class.
- (d) Pagination/autoplay semantics: hiện tại `loop: canLoop` (`collection-view.ts:70`) trong khi POC `loop: slideCount > preset[0]` (`poc-03.html:717`) — đối chiếu riêng loop bật (trượt liên tục) và không loop (giới hạn/disabled); không yêu cầu disabled đầu/cuối khi loop bật.
- (e) Editor preview là placeholder/skeleton (`controls.tsx:523-616`), không chạy Swiper trong editor — giữ nguyên (đúng invariant).

### A6. Dependency thực sự chặn carousel

- **Không có dependency code nào chặn M1.** WooCommerce + ≥6 products có ảnh (hoặc placeholder) + 1 test page là đủ cho Product; dữ liệu WP thật là đủ cho Post. MOQ/lead time/cert/PDF render có điều kiện khi có meta (`cards.php:151,158,178`) — với quyết định "dữ liệu mặc định trước", card không có B2B fields vẫn PASS nếu khớp POC ở trạng thái không-B2B (human đã chốt dùng dữ liệu Woo mặc định; không yêu cầu demo meta B2B để M1 PASS).
- Shared runtime (`src/shared/autoplay.ts`, `src/shared/motion.ts`) dùng chung slider + collection: sửa collection CSS/view **không đụng** shared thì không ảnh hưởng slider; nếu phải sửa shared, phạm vi/ảnh hưởng phải ghi rõ trong prompt implement (xem Kế hoạch M1 §Phạm vi shared runtime).

---

## Roadmap C draft — mapping cũ → mới (Muse proposal, chưa duyệt)

> Quy ước cột: **Kế thừa** = mang code/quyết định sang roadmap mới; **Dời** = làm sau, giữ trong roadmap; **Gộp** = nhập checklist vào mốc mới; **Loại** = không còn trong roadmap tương lai (không đồng nghĩa xóa code); **Xem lại** = cần human quyết. "Đã implement" ≠ "đã kiểm thử + human xác nhận".

| Milestone cũ | Trạng thái cũ | Mapping đề xuất | Mốc mới (nhãn thảo luận) | Ghi chú truy vết |
|---|---|---|---|---|
| 1.3.3 Dynamic Collections | DONE (nhiều acceptance trống, QA dời 1.3.11/1.3.16) | **Kế thừa** (nền query/render/preset) + **Gộp** QA carousel vào M1 | M1 | DONE cũ không chứng minh đạt POC-03 |
| 1.3.7 Collection UI & Card Styles | DONE (onsite QA dời 1.3.11) | **Kế thừa** (spacing/heading/CTA/badge) + delta visual lock theo POC-03 ở M1 | M1 | Xem A3 delta list |
| Planning 038 Visual Lock | PLANNING (Path B/C, artifact cũ) | **Kế thừa** phương pháp (gated B→C, motion gates) nhưng **đổi artifact chuẩn sang POC-03**, engine = Swiper đã chốt | M1 | 038 không thay thế milestone chính thức; engine decision 2026-10-07 supersede tranh luận B/C |
| 1.1.2 Quote Flow & Map testing | PENDING | **Gộp** phần quote-data-flow vào M2; map block tách riêng **Xem lại** | M2 (+ map xem lại) | Thứ tự human đã chốt: M1 test giao diện xong mới M2 |
| 1.4.1 T-2 source_url + quote evaluation | PENDING | **Gộp** T-2 verification vào M2 | M2 | Source fix đã có (`cards.php:517-566`); còn thiếu evidence end-to-end |
| 1.3.10 Step Slider | PENDING (current chính thức) | **Dời** sang mốc slider sau | M5 | Không tự đổi milestone chính thức trong lượt này |
| 1.3.8 Parallax, 1.3.1 controls, slider QA | DONE/PENDING QA | **Dời** sang M5 | M5 | T-DEMO tensions giữ nguyên, không resolve ở đây |
| Header actions 0.12 / Footer settings 0.9–0.11 | DONE/planned | **Giữ tạm** (dùng GP header/footer hiện có) | — (tạm giữ) | Hệ thống mới sau theme độc lập (M4) — đã chốt thứ tự |
| 2.0.0→3.0.0 thoát GeneratePress | Planned | **Kế thừa** làm khung M3 | M3 | Chưa chốt lại version |
| 1.5.0 Woo Catalog (MOQ/cert structured) | BRAINSTORM | **Dời** (không chặn M1); **Xem lại** có hủy hay giữ sau M2 | Sau M2 | M1 dùng dữ liệu mặc định — đã chốt |
| 1.6.0 Surface presets, 1.4.0 Setup UI, 1.3.4 Core Control, 1.3.12 Credits, 1.3.9 Icon, 1.7.0 CĐS page, TOC 1.5.3, 1.3.16 QA | PENDING/BRAINSTORM | **Dời** hoặc **Xem lại** từng mục (chưa đề xuất loại cụ thể) | M5/M6 hoặc xem lại | Không coi human đã duyệt dời/loại toàn bộ — cần review từng mục với PM/human |
| 1.3.11 + 1.3.16 QA trùng | PENDING cả hai | **Gộp** QA carousel vào acceptance M1; phần slider còn lại **Dời** M5 | M1 + M5 | Cách gộp chi tiết chưa duyệt |
| GU Supercharger launch post-1.5.x | PENDING | **Xem lại** (launch chưa đồng nhất giữa tài liệu) | Chưa đặt | Không đổi slug/namespace trong M1 |

Nhãn M1–M6 vẫn là nhãn thảo luận, không phải SemVer. Thứ tự đã chốt được giữ: M1 carousel + test giao diện → M2 Quote Flow → M5 slider sau → M4 header/footer mới sau theme độc lập (M3).

---

## Kế hoạch carousel M1 (Muse proposal, chưa duyệt — chuyển được thành prompt implement)

### Scope (làm)

- S1. Product Carousel visual + motion lock theo POC-03 trên Swiper hiện có (không đổi engine, không viết lại query/render).
- S2. Post Carousel kế thừa track/chrome Product; card theo CSS article gợi ý trong POC + dữ liệu WP thật; human duyệt visual Post như acceptance riêng.
- S3. Settings tối thiểu đã có — chỉ bổ sung nếu test chứng minh thiếu (không làm sidebar nâng cao).
- S4. Responsive + reduced-motion + keyboard/a11y + Woo-inactive fallback.
- S5. Test doc + evidence (screenshots, console) cho human.

### Exclusions (không làm trong M1)

- Quote Flow/CF7/CFDB7 end-to-end (sang M2); Woo Catalog structured fields (dời); theme migration/Tailwind pipeline (sang M3); header/footer mới (sang M4); Hero/Parallax/Step Slider (sang M5); sidebar nâng cao toàn diện; engine switch/migration; namespace/slug/version changes.

### Phạm vi shared runtime

- Mặc định: chỉ sửa `src/collection/style.css`, `src/collection-view.ts` (geometry/config), `modules/collection-render/cards.php` (DOM còn thiếu + class policy); không đổi default `block.json` để khớp POC, test doc. **Không sửa `src/shared/*`, `src/slider/**`, theme, GeneratePress.**
- Nếu test chứng minh phải sửa `src/shared/autoplay.ts` hoặc `motion.ts`: nêu diff + ảnh hưởng slider (regression matrix slider) trong cùng prompt implement; PM/human duyệt riêng trước khi đụng.

### Các bước theo dependency

1. P0 — Chuẩn bị test fixture (human): Woo on, ≥6 products có ảnh, 1 page Product Carousel + 1 page Post Carousel, build hiện tại. Ghi URL test.
2. P1 — Smoke runtime (agent hướng dẫn, human chạy hoặc agent kiểm tra nếu có runtime): Swiper init không lỗi, arrows/pagination/keyboard hoạt động, Grid không regression.
3. P2 — Product card + chrome CSS lock theo POC (delta A3.2–A3.6): radius/padding/CTA hover/shadow/pagination pill/arrows SVG/header-footer chrome; `imageRatio` chuyển sang media box.
4. P3 — Swiper geometry lock: `spaceBetween: 20`, presets giữ nguyên, giữ preset hiện có, kiểm tra variation Product Carousel đã dùng `4-2-1`, loop/non-loop theo POC, arrows disabled chỉ khi không loop và chạm giới hạn.
5. P4 — Post Carousel: reuse track/chrome; card 16:9 + overlay + date/title/excerpt/read-more; human duyệt visual.
6. P5 — Responsive + reduced-motion + a11y + fallback pass; viết `docs/testing/onsite-collection-carousel-m1.md`; tổng hợp evidence cho PM/human review.

### Files dự kiến sửa (M1 implement, chưa sửa trong lượt này)

```text
wp-content/plugins/skvn-marine-blocks/src/collection/style.css
wp-content/plugins/skvn-marine-blocks/src/collection-view.ts
wp-content/plugins/skvn-marine-blocks/modules/collection-render/cards.php
wp-content/plugins/skvn-marine-blocks/src/product-collection/block.json   (chỉ nếu duyệt đổi default preset)
wp-content/plugins/skvn-marine-blocks/src/post-collection/block.json      (chỉ nếu duyệt đổi default preset)
docs/testing/onsite-collection-carousel-m1.md                             (tạo mới khi implement)
```

Giữ ≤5 files/source slice; docs test không tính vào giới hạn source.

### Acceptance checklist (kiểm chứng được)

- [ ] Product Carousel với dữ liệu Woo mặc định khớp POC-03 ở desktop/tablet/mobile (so ảnh cạnh nhau).
- [ ] Arrows header hoạt động: loop bật thì chuyển liên tục; không loop thì kiểm giới hạn/disabled; thiếu item để trượt thì controls không gây hiểu nhầm. Dots pill đồng bộ + clickable; keyboard ←/→ được kiểm tra riêng.
- [ ] Autoplay off mặc định; khi bật: Pause/Play hiển thị, hover/focus/tab-ẩn pause đúng, reduced-motion tắt autoplay.
- [ ] Card link (media/title/CTA) tới đúng product/post URL; quote URL giữ product context (không chứng minh CF7 — sang M2).
- [ ] Woo inactive: message graceful, không fatal. Empty query: empty state rõ ràng.
- [ ] Post Carousel duyệt visual riêng bởi human (không có POC sống để auto-so).
- [ ] Console sạch; Grid layout không regression; editor preview không chạy Swiper.
- [ ] Human xác nhận M1 PASS trước khi sang M2.

### Checklist test editor/frontend + responsive

- Editor: block insert, preset switch, toggles (arrows/pagination/autoplay/image/badge), placeholder preview, không invalid-block, không chạy autoplay.
- Frontend ở 3 viewport (đề xuất 1280/768/375 — POC không khóa breakpoint, Duo/Fold chỉ cần responsive tốt): chụp header/card/footer; kiểm arrows ẩn/hiện, dots, CTA wrap, excerpt clamp, ảnh fallback.
- Handoff: agent chuẩn bị steps + expected; human chạy onsite, trả screenshots + console notes + submission context (M1 không cần submission); agent đối chiếu và fix.

### Rủi ro tương thích

- R1. Giữ class cert hiện tại; chỉ đổi DOM nếu có nhu cầu được chứng minh. Card render động, không coi resave là đổi class một chiều.
- R2. Đổi CTA hover teal → trust-blue + radius đổi ảnh hưởng Grid (dùng chung card CSS) → test Grid cùng slice; nếu human muốn tách, thêm modifier carousel-only.
- R3. Product Carousel variation đã dùng `4-2-1`; không đổi default block gốc/Grid. Nếu về sau đổi default, phải kiểm nội dung cũ thiếu attribute và PHP fallback.
- R4. Token navy/neutral POC vs theme (`#082f49` vs `#073b5a`, `--tmp-*` chưa có token) → map vào token hiện có hoặc ghi technical debt tại chỗ (A13); không đưa raw hex vào saved content.

### Tài liệu bổ sung đề xuất (chưa tạo — nêu đường dẫn + mục đích)

- `docs/testing/onsite-collection-carousel-m1.md` — test steps/evidence M1 (tạo khi implement).
- Post visual baseline và mọi thay đổi tương thích thực sự cần thiết ghi vào file này hoặc decision file riêng khi human chốt.

---

## Review PM — 5 điểm đã được human xác nhận (2026-10-13)

Nguồn: human đồng ý cả 5 điểm review PM của audit Muse. Section này thay thế memo D1–D3 cũ; không phê duyệt toàn bộ M1, khung M1–M6 hoặc chuyển milestone. Agent sau không hỏi lại 5 điểm này. Nhận định/đề xuất trong audit trước trái với section này đã bị thay thế.

1. **Baseline dữ liệu đã chốt:** Product Carousel dùng WooCommerce mặc định. MOQ/lead time/chứng nhận chưa bắt buộc; không yêu cầu demo meta B2B để M1 PASS. B2B demo trong POC không tự thành scope.
2. **Default preset:** `src/product-collection/variations.ts:23` đặt Product Carousel là `4-2-1`; block gốc/Grid dùng `3-2-1`. Không đổi default toàn block để khớp section đầu POC. Post Carousel hiện là `3-2-1`; POC chưa chốt default Post. Đổi default sau này phải audit saved attributes/PHP fallback, không khẳng định chỉ tác động block mới.
3. **Keyboard:** `src/collection-view.ts:2` chưa import Keyboard/config keyboard. A11y không chứng minh hỗ trợ ←/→. Giữ keyboard trong acceptance, kiểm tra và xử lý gap của carousel nếu cần; không viết lại engine.
4. **Navigation:** loop bật thì trượt liên tục, không bắt disabled đầu/cuối; không loop thì kiểm giới hạn/disabled. Test thêm khi không đủ item để trượt. Không áp một expectation cho mọi preset/số lượng item.
5. **Cert class:** ưu tiên giữ `skvn-collection-card__cert-dot`, sửa CSS nếu cần. Khác class/DOM với POC không tự là lỗi giao diện. Cert chưa bắt buộc; tránh đổi DOM không cần thiết. `cards.php:154` render class động nên memo về resave gây thay đổi một chiều chưa có căn cứ.

Không dùng D1–D3 làm điều kiện P2 phải chờ human chốt. M1 vẫn cần review plan/test; visual Post vẫn có acceptance human riêng.

## Review roadmap 039 — phần còn chưa hoàn thiện

- [Planning 039](../../.context/planning/039_ROADMAP_C_B2B_TOOLKIT_CAROUSEL_FIRST_PLANNING.md) là DRAFT, phải phản ánh 5 điểm trên.
- Mapping vẫn gom Core Control, TOC, credits, icon, setup UI, CĐS page, surface presets, launch; cần phân tích từng mục để đề xuất giữ/dời/gộp/loại. Chưa quyết định loại mục nào.
- M1–M6 là nhãn thảo luận; slider sau carousel đã chốt, chưa chốt phải sau theme/header/footer.
- 1.3.10 chưa bắt đầu: phải phân biệt superseded/deferred với completed, không archive như milestone đã hoàn thành.
- Section version của 039 cần đối chiếu AGENTS.md: milestone development build khác release; ghi chỉ bump ở release boundary chưa đầy đủ. Lượt này không bump, chuyển milestone hoặc archive tension.

Lịch sử: 2026-10-13 — ghi 5 chỉnh sửa PM đã được human đồng ý để agent khác tiếp tục. Không sửa source/runtime.

## Xác nhận SemVer 1.3.17 + mapping từng mục (human chốt trong chat planning 2026-10-13)

1. **M1 → `1.3.17` đã được human xác nhận** cho scope Product/Post Carousel visual lock, hardening và QA trên nền collection hiện có. Chưa có lệnh bắt đầu implementation; chưa kích hoạt current. M1–M6 giữ vai trò định danh stack, không thay SemVer.
2. **039 là nơi quản lý stack**; MILESTONES chỉ tham chiếu ngắn, không sao chép toàn bộ bảng stack.
3. **Mapping từng mục (quyết định đã chốt):**
   - Setup UI: phần phục vụ theme độc lập → M3; phần còn lại xem lại.
   - Core Control: tách phần đã có cần giữ tương thích / phần mở rộng mới hoãn (không vào 1.3.17).
   - Credits: Parking, không chặn M6. Custom Icon: candidate, chỉ kích hoạt khi có nhu cầu thiết kế cụ thể.
   - Surface Presets: đánh giá cùng visual/Tailwind architecture ở M3.
   - CĐS page: template ứng dụng ở M6, không là dependency của toolkit.
   - TOC 1.5.3 (planning 037: TOC native dynamic + shortcode, hội tụ numbering 1.3.14): **đề xuất M6, chưa chốt** — lý do: article-reading UX, không phụ thuộc carousel/toolkit core. Human quyết nơi nhận cuối.
   - Khung thoát GP 2.0→3.0 → M3. Header/footer GP hiện có giữ tạm; hệ thống mới ở M4 (sau M3).
   - Slider sau carousel (M5); không suy ra toàn bộ M1–M6 chạy tuần tự.
4. **Quy trình chuyển ưu tiên:** milestone hiện tại (1.3.10) chưa bắt đầu/chưa hoàn thành vẫn có thể deferred khi human quyết — không buộc hoàn thành Step Slider trước carousel, không ghi milestone bị dời là completed, giữ checklist + truy vết. Protocol bổ sung vào AGENTS.md + MILESTONES.md; lượt này giữ Current V1 / 1.3.10 PENDING.
5. **Mapping QA:** mọi acceptance 1.3.11/1.3.16 đều có nơi nhận — carousel → acceptance 1.3.17; slider → QA stack slider (M5); Accordion/Card motion Feature Showcase → deferred M6 application validation; Core Button Hover feature (chưa implement) ở lại 1.3.4, chỉ phần hover-compat đã ship mới QA ở 1.3.17/M5; Credits → parking; kiểm chung phân bổ theo surface. Chi tiết ở 039 §4a. Chưa đóng QA cũ, chưa resolve tension chỉ vì mapping.
6. **5 chỉnh sửa audit PM vẫn hiệu lực** (Woo mặc định; giữ default variation 4-2-1; gap Keyboard; tách loop/non-loop; cert class không chặn M1). Không hỏi lại.

Lịch sử: 2026-10-13 — ghi xác nhận 1.3.17 + mapping từng mục + protocol chuyển ưu tiên (planning/Markdown, chưa implement, chưa chuyển mốc).

## Milestone stack và quyết định SemVer — human chốt 2026-10-13

Roadmap 039 là milestone stack; M1–M6 là định danh scope, không thay version hiện tại. Khi một mục được đưa vào triển khai, agent phải đề xuất SemVer theo scope và các version hiện có; human xác nhận version cùng việc bắt đầu/chuyển milestone trước implementation. Phê duyệt ưu tiên hoặc scope chưa phải phê duyệt version.

Nếu chưa có version được xác nhận thì chỉ audit/planning, không dùng `M1` thay cho current `1.3.x`, không bump hoặc archive. Khi có xác nhận, ghi mapping stack → SemVer trong 039, đồng bộ AGENTS.md/MILESTONES.md và ghi version trong prompt thực thi. Quyết định đã có thì không hỏi lại.

Protocol đã ghi trong AGENTS.md và MILESTONES.md. M1 đã được xác nhận 1.3.17 và promote theo quyết định bên dưới; M2–M6 chưa gán SemVer. Không duyệt toàn bộ roadmap bằng việc kích hoạt M1.

## Promote milestone carousel stability — human bắt đầu 2026-10-13

Human: “Bắt đầu promote milestone hiện tại là cho carousel chạy ổn định”. Áp dụng SemVer 1.3.17 đã chốt, không hỏi lại.

- **Current: V1 / 1.3.17 — Product/Post Carousel Stability & QA**, IN PROGRESS; Started 2026-10-13; stack M1.
- Mục tiêu: Product/Post Carousel ổn định trên dữ liệu Woo/WP mặc định; visual theo POC-03, Post visual được human duyệt riêng; swipe/navigation/pagination/autoplay-pause/keyboard và responsive (resize/orientation máy gập) hoạt động đúng. Acceptance: roadmap 039 §4–§4a, không tự ghi test PASS.
- **1.3.10 Step Slider DEFERRED → M5**, chưa hoàn thành; giữ toàn bộ checklist/links trong MILESTONES. History ghi sự kiện chuyển ưu tiên, không archive như completed.
- Quote Flow chỉ sau human PASS carousel UI; slider mở rộng dời sau; GP header/footer giữ tạm, hệ thống mới sau theme độc lập. Advanced settings không thuộc 1.3.17.
- Không active tension nào thuộc 1.3.10 cần archive; giữ entry của mốc khác và OPEN QA debt.
- Chat này tiếp tục planning/review, chỉ sửa Markdown. **Muse thực thi**; development metadata 1.3.17, build/lint và onsite evidence là bước tiếp theo, chưa chạy/kiểm chứng ở đây. Development build không phải release approval.
- Quyết định này thay thế mọi ghi chú “chưa bắt đầu/current 1.3.10” trước đó về M1; future mapping chưa chốt vẫn mở.