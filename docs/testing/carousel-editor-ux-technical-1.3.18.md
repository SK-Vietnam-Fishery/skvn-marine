# Carousel Editor UX & Technical — onsite runbook 1.3.18

## CP-01–05 — Product Carousel canvas presentation (2026-11-05)

Current scope: follow-up1.3.19, approved plan052; source added, onsite pending. Giữ acceptance E-* và carry-over dưới đây. Target frontend: `https://minhhaifishery.com/home/`; mở Edit Page của trang này để kiểm canvas (editor URL/page ID, WP version, build/hash và browser cần ghi khi chạy). Setup: human build/deploy assets mới; Product Carousel có >=12 results và fixture title dài; không dùng Category Grid thay cho block cần kiểm.

1. **CP-01:** Mở/đóng Inspector/Design sidebar; kiểm canvas >760px rồi 760px/520px/320px hoặc Group/Columns hẹp trên trang test được phép. Rộng: card trái khoảng1/3, summary/query phải dùng diện tích còn lại; footer hết hàng. Hẹp: card → summary → query → footer. Không cắt nội dung/horizontal scroll. Heading/intro vẫn chỉnh được. Ghi screenshot, rect section/body/sample/overview và scrollWidth/clientWidth khi có scrollbar dọc.
2. **CP-02:** Toggle image/spec chips/tags/equal height; thử CTA quote/view/both/custom/none và grouped specs/+N. Summary giữ đủ trạng thái cũ; card mẫu giữ thiết kế/attrs consumers. Kiểm loading/empty/error/unavailable/results trong vùng query, card không mất khi request fail. Chỉ mô phỏng lỗi trên test page bằng controlled blocking được phép, không phá production REST.
3. **CP-03:** Nhãn “Illustrative sample — not live card data” phân biệt card với tên query thật; count là fetched. Footer nói rõ native REST approximate/newest-first và unsupported filters/order. Không hiểu summary là bằng chứng query parity.
4. **CP-04:** Tab tới Show more/less, mở rồi resize và thu gọn; focus không rơi vào element đã remove. Đổi query giữ behavior reset/disclosure cũ. Network không có request mới chỉ vì đổi layout/resize; Save/reopen và Undo vẫn bình thường. Editor không autoplay/Swiper.
5. **CP-05:** So frontend Product Carousel trước/sau cùng fixture; markup/query/card behavior không đổi. Product Grid và Post Grid/Carousel editor không nhận presentation mới.

PASS cần screenshot/geometry/keyboard/state evidence theo từng CP; FAIL ghi URL/build/steps/expected/actual/console. Source parse PASS không thay runtime PASS. Root chưa chạy build/PHP lint/onsite; assets thay đổi bởi bên ngoài trong lúc sửa source không chứng minh đã chứa source cuối.

Ngày lập: 2026-10-20. **Chờ kiểm chứng**. Lượt planning không có runtime PASS mới.
Plan: `.context/planning/045_VER_1_3_18_CAROUSEL_EDITOR_UX_TECHNICAL_EXECUTION_PLAN.md` §9–13.

## Onsite checkpoint — 2026-11-04

Human báo Carousel đã cơ bản có thể sử dụng sau khi test onsite và yêu cầu chuyển các phần chưa kiểm sang milestone kế. Ghi nhận kết quả sử dụng cơ bản; report chưa chỉ rõ Product/Post, URL, build hay từng case đã chạy nên không tự tick PASS cho E01–E15 hoặc xóa các gap đã ghi ngày 21/10.

Giữ PASS E02 theo evidence cũ. Human 2026-11-04 hoán đổi Sidebar/Hero: **Sidebar 1.3.19** nhận toàn bộ phần Carousel chưa hoàn tất/thiếu evidence theo register tại MILESTONES.md; **Deferred → 1.3.19** tại checkpoint 1.3.18. Giữ IDs/cases/gap, không tick PASS thay cho chưa kiểm. CTA hover/chip colors, C/D, Post visual và regression Technical phải được kiểm riêng với DS-01–07. Khi 1.3.19 thành current, nhắc chạy phần deferred trước follow-up không liên quan. Category giữ 1.3.20, Hero contrast dời 1.3.21; current chưa đổi.

## Target và setup

- Product: trang onsite **Test product carousel**; URL editor/frontend chính xác chưa được ghi nhận, executor/human điền trước chạy.
- Post: trang Post Carousel thử, URL chưa xác nhận. Nested Group/Columns và Grid dùng trang test được human cho phép.
- Ghi WP/plugin/theme version, source commit hoặc dirty changes, build/deployed artifact, browser/OS, viewport và container width.
- Fixture Product 20 items với tên dài, có/không ảnh, nhiều thuộc tính, limit 2/3/4; Post có date/author/excerpt, tên dài/không ảnh. Có query 0/1/ít/nhiều kết quả và các filter/order hiện hành.
- Giữ serialized block cũ trước sửa; tạo bản mới, hai carousel khác style và Grid đối chứng.
- Woo inactive, request error và invalid attrs chỉ kiểm trên môi trường thử/mô phỏng được phép; không tắt Woo production.
- Prototype HTML không thay evidence Gutenberg. Không thêm delay runtime chỉ để thấy loading.

## Cases và pass criteria

Mỗi case ghi actual + status PASS/FAIL/Chờ kiểm chứng, liên kết evidence. Chuỗi mọi setting: control → attr → preview → save/reopen → frontend.

| Evidence / IDs | Steps | Expected / PASS criteria |
|---|---|---|
| E01 / UX-09.a | Bật/tắt ảnh; query 20 items; expand/collapse; lặp Product/Post | Card không stretch theo danh sách, summary đúng attrs, nhãn mẫu rõ, không roadmap |
| E02 / UX-11.a | Normal/Wide/Full; sidebar mở/đóng; Group/Columns; 1600/1200/800/480/320px | Viền đúng container, không tràn ngang canvas/page; modes có thể bằng nhau khi hẹp. Lưu rect + scrollWidth/clientWidth | **PASS** (2026-10-21, từ hình editor/frontend) |
| E03 / UX-10.a | 20 tên dài; container 3/2/1 cột; Tab/Enter/Space mở/thu; resize lúc expanded | 9/6/3 trước mở; tên đầy đủ, đọc theo hàng, focus không mất, itemsToShow không đổi |
| E04 / TECH-12.a | Results→empty; request chậm/lỗi; query A→B nhanh; Woo unavailable trên test env | States phân biệt, kết quả cuối của B; không stale như mới hoặc retry vô hạn; network evidence |
| E05 / UX-04.a | So IDs preview/frontend từng category/tag/relation/order/count | Mode hỗ trợ khớp; approximation có help cụ thể. Shuffle không yêu cầu thứ tự giống giữa requests; Post cap 5 nói rõ |
| E06 / UX-01.a | Từng inventory control: đổi/save/reopen; đổi Product/Post/Grid | Control có consumer; hide/disabled đúng lý do, không panel rỗng/mất saved query/action |
| E07 / UX-12.a | Đổi 4 màu CTA; hover/focus; hai blocks khác màu; reset | Computed colors đúng và scoped; focus rõ; reset không xóa URL/query/mode/layout; footer không bị đổi ngoài scope |
| E08 / UI-18-TITLE.a | Title dài; click/tab/hover/visited | Anchor đúng URL, đọc rõ, full title, focus thấy được; lưu ảnh/styles |
| E09 / UX-13.a | Từng role đổi font/size lớn nhất, save/reopen/reset; kiểm admin | Computed fonts/sizes đúng hai surfaces; không leak vào sidebar/admin/block khác |
| E10 / UX-05.a | Mọi chip color exposed và chipStyle | Mỗi option có output đúng palette, không option giả; lưu bảng màu |
| E11 / TECH-13.a | Fixture missing/malformed slug, token bị gỡ | Fallback an toàn, không injection/error; editor báo unavailable, không overwrite dữ liệu khi mở |
| E12a / UX-02a.b | Product 3 presets (flush/padded/floating); ảnh vuông/ngang/dọc/transparent/thiếu; image off; đổi preset→save/reopen | Flush contain không bóp ảnh; padded padding đều + inner radius; floating width % card + translateY lên trên, không clip ảnh/bóng, card/body đứng yên; off không để khoảng trống nhô/bóng; lưu ảnh trước/sau + serialized attrs |
| E13a / TECH-08a.b | Old/new Product, Post, Grid; explicit legacy ratio; missing mediaLayout key; save/reopen/reset | Không invalid-block, không migration ngầm, không mất data; flush output identical với legacy; Post không nhận floating/padded; Grid dùng chung card CSS |
| E12 / UX-02a.a | Sau P4: 3 presets, ảnh vuông/ngang/dọc/transparent/thiếu; image off | Contain không bóp ảnh; padding đều/radius đúng; Floating không clip/chồng; off không để lỗ hổng |
| E13 / TECH-08a.a | Old/new attrs, explicit legacy ratio, Product/Post/Grid; save/reopen | Không invalid-block/migration ngầm/mất data; lưu serialized diff và ảnh |
| E14 / UI-18-STYLE.a | Mobile/max size/title và CTA dài; hover/focus/touch/reduce | Không tràn/che CTA; focus/contrast đúng tiêu chí a11y project; lưu geometry/contrast thực |
| E15 / TECH-09.a | Network baseline→10 style edits→10 mount/remove; nhiều instances | Style không fetch query; request/listener/observer không tích lũy sau cleanup; console không lỗi mới; lưu số đo |

## Carry-in và regression

- C: chạy `carousel-preinit-fallback-1.3.17.md` trên build hiện hành, ghi section/carousel-outer classes, overflow/gap, rect.
- Arrows: `carousel-arrow-centering-1.3.18.md`, source đã sửa, onsite chưa tự coi PASS.
- D: `carousel-skeleton-loading-1.3.17.md` giữ debt; **không sửa skeleton đợt này**, không full-page skeleton hoặc claim hết chớp.
- TECH-02–07: keyboard đúng instance/input focus, links không nested, quote URL/context, Product manual-only kể cả autoplay=true cũ, Post pause/reduce, loop/count/resize. Kiểm 0/1/bằng cột/nhiều items.
- Grid không init carousel; editor không Swiper; Post visual cần human duyệt riêng.

## Build và kết quả

Human chạy khi có thay đổi plugin, theo AGENTS. Lượt planning không cần chạy:

```bash
source /home/shinkuro/.nvm/nvm.sh && nvm use 20 && cd /mnt/d/Github/skvn-marine/wp-content/plugins/skvn-marine-blocks && npm run build && find modules/ -name "*.php" -exec php -l {} \;
```

Nếu PHP require/include đổi, audit deploy artifact trước zip/upload.

Mỗi evidence ghi: ID, ngày/người kiểm, URL editor/frontend, build/commit/artifact, browser/viewport/container, fixture, steps, expected/actual, status, ảnh/video/DOM/network/log, giới hạn.

Human gửi build output và cases đã chạy; agent cập nhật requirement register, sửa FAIL trong scope được giao. Không suy PASS toàn milestone từ lint, không tự chuyển milestone.

## Ghi nhận QA từ hình/onsite (2026-10-21) — chưa sửa code

| ID | Tình trạng | Ghi chú từ hình/onsite |
|----|-----------|------------------------|
| E02 / UX-11.a | PASS | Width Normal/Wide/Full kiểm qua hình editor/frontend, viền đúng container, không tràn ngang. |
| E07 / UX-12.a | **Gap** | Màu CTA chỉ đổi được trạng thái mặc định (bg/text). **Chưa có option đổi màu hover** — hover không phản ánh màu đã chọn. |
| E10 / UX-05.a | **Gap** | Chip màu **không đổi màu được** khi chọn chipColorScheme/color palette trong editor. |

Việc cần làm sau khi human xác nhận gap trên:
- Thêm control màu CTA hover (background + text) vào Style panel Product, ánh xạ sang attr mới + render PHP + CSS.
- Kiểm lại chipColorScheme consumer trong cards.php/controls.tsx và CSS `.skvn-chips--*` có phản ánh chọn màu hay không.

## Lỗi đã gặp và lesson learned (tránh mắc lại)

**Lỗi:** `npm run build` thất bại với `Module parse failed: Cannot parse JSON` trên `src/product-collection/block.json` (position ~4905), do webpack import file này vào `src/index.ts`.

**Nguyên nhân:** Khi edit thêm các attribute `mediaLayout`, `mediaWidthPercent`, …, `mediaPaddingUnit` vào `block.json`, agent đã đặt chúng **ngoài khối `"attributes"`** (sau dấu `}` đóng `"attributes"`). Kết quả JSON có hai cấp `"attributes"` cùng cấp, không hợp lệ.

**Cách khắc phục:** Đảm bảo mọi attribute mới nằm **trong** khối `"attributes"`, trước dấu `}` đóng `"attributes"`. Sau khi edit `block.json`, luôn validate:

```bash
node -e "JSON.parse(require('fs').readFileSync('wp-content/plugins/skvn-marine-blocks/src/product-collection/block.json','utf8')); console.log('JSON OK')"
```

Áp cả cho `build/product-collection/block.json` (artifact build được sinh từ `src/`, phải đồng bộ).

**Check trước build:** `src/product-collection/block.json` và `build/product-collection/block.json` phải parse JSON thành công.


