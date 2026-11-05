# Hero Slider — contrast repair và kiểm chứng fade

Ngày: 2026-10-19 (cập nhật sau audit source). Status: **Chưa chạy — test plan**, implementation/version TBD; không phải PASS hoặc test đã được human defer.

Plan: `.context/planning/044_HERO_SLIDER_CONTRAST_AND_FLICKER_REPAIR_PLAN.md` (đã sửa 2026-10-19 sau audit: specificity thật của rule theme, cơ chế `!important` của WP preset, và hướng sửa mới "carrier + inherit" thay cho "gán trắng trực tiếp").

## Target và preconditions

- Report URL: https://minhhaifishery.com/test-product-carousel/ — Hero bên dưới Featured products. Không chỉnh production/content tại URL này khi chưa được human cho phép.
- Fixture bổ sung cần tạo trên staging hoặc page được human cho phép; ghi URL thật trước khi chạy. Local thiếu page không phải source failure.
- Ghi commit/build, plugin/theme version, URL asset/version query, browser/version, OS, viewport/DPR, login/cache, reduced-motion state và thời điểm. Đảm bảo asset đúng bản, không dùng chỉ version header để suy ra CSS đã cập nhật.
- Human đã chạy build/lint và có output. Kiểm trước sửa trước khi deploy repair; sau sửa giữ nguyên content/settings để so.
- Editor phải là stacked preview, không Swiper/autoplay.

## Fixtures tối thiểu

| ID | Dữ liệu/trạng thái |
|---|---|
| F1 | Hero hai slide, không ảnh, gradient mặc định, heading/paragraph/CTA không custom color |
| F2 | Hero có ảnh nền/overlay; text mặc định |
| F3 | Heading/paragraph palette color; custom inline color; rich-text span có màu |
| F4 | Group có text color chứa heading/paragraph, kể cả block con không tự set màu |
| F5 | CTA không custom; text-only custom; background-only custom; cả hai; gradient; custom hover từng kênh |
| F6 | Hero saved content cũ, mở lại và save; một Hero mới từ inserter |
| F7 | Product Showcase, Card Carousel; core button ngoài Hero; một Product/Post Collection smoke dùng chung bundle |

Không cần nhân mọi tổ hợp. F1/F5 kiểm full states; F2/F3/F4/F6 kiểm màu và parity; F7 regression nhỏ.

## Các bước và pass/fail

Nguồn thiết kế và đối chiếu source: `.context/planning/044_HERO_SLIDER_CONTRAST_AND_FLICKER_REPAIR_PLAN.md` §4.2–§4.3. Cơ chế được kỳ vọng sau patch:

- Hero đặt `color: #fff` trên `.skvn-slide__content` (carrier).
- Heading/paragraph/link trong Hero được đặt `color: inherit` ở specificity cao hơn rule theme, nên chúng **kế thừa** thay vì bị gán thẳng.
- Hệ quả bắt buộc phải kiểm: màu của Group chứa heading phải tới được heading; palette color phải thắng; reset phải trả về trắng.

1. Chụp F1 ở rest cả hai slide trước/sau; DevTools ghi computed color của heading/paragraph và CTA text/background, kèm selector/declaration/file thắng. Sau repair default text phải sáng (`#fff` hoặc giá trị tương đương), CTA default theo visual đã duyệt; không trắng chữ trên nền trắng. FAIL nếu vẫn rơi về `#082f49` / `#334155` trên gradient tối.
2. Chạy F2: có ảnh và tháo ảnh trên fixture được phép. Default text không phụ thuộc việc có class ảnh. Kiểm `.skvn-slide--has-background` tiếp tục phản ánh ảnh thực tế; không mất media/layer. Ghi rõ class có/không trước và sau khi thêm ảnh.
3. Chạy F3/F4: chọn màu, save, reload frontend; mở lại editor; reset về Default. Explicit values phải giữ, reset trả về hero fallback. **F4 là điểm dễ FAIL nhất**: Group có text color phải làm heading bên trong đổi theo màu Group, không bị ép trắng. FAIL nếu heading vẫn bị theme chặn hoặc Group color bị ép trắng.
4. Với từng CTA F5, kiểm normal/hover/Tab focus; custom hover text và background riêng. Palette/gradient và core style variants phải giữ; fallback không phủ gradient, focus indicator còn thấy. Trường hợp background custom nhưng không text custom: kiểm core/theme behavior hiện có, không tự coi navy text là default bắt buộc. Ghi riêng kết luận về rule GeneratePress mà bug report suy luận (`a.wp-block-button__link:not(.has-background)`) — đây là điều kiện quyết định có đổi nền CTA hay không.
5. Kiểm link in-container trong Hero (nếu có): sau patch link kế thừa màu Hero. Ghi lại ảnh để human quyết định có giữ hay không.
6. F6 mở/save/reopen: không invalid-block warning, không mất attrs, không cần migrate nội dung cũ để có default. So editor với frontend của cùng content.
7. Kiểm F1 và CTA chính ở desktop, tablet và mobile; ghi viewport thực tế. Không đổi wrap/geometry so baseline, không phát sinh overflow. Tab tới controls/CTA và kích hoạt keyboard, kiểm trạng thái focus và link vẫn hoạt động.
8. F7: màu/presentation không đổi ngoài Hero; navigation/link còn chạy, không lỗi console mới liên quan repair.
9. Bật reduced motion ở OS/browser rồi reload: ghi behavior thực tế, không có motion mới do patch; editor vẫn static. Tắt lại để kiểm fade thường.

## Fade và hiện tượng chớp — outcome riêng

1. Trên F1 sau contrast fix, ghi config/effect/speed, dùng arrows, keyboard, pagination qua ít nhất ba transition và hai slide. Autoplay kiểm riêng, tránh để focus/visibility/hover làm sai điều kiện; ghi nguyên nhân nếu không kiểm được.
2. Lưu clip hoặc frames có timestamp trước/giữa/sau transition; không chỉ ảnh sau fade.
3. Nếu tái hiện trắng ↔ tối: dùng DevTools/browser có thực thi JS để buffer samples rAF cho cùng node, định danh ổn định theo slide identity thay vì DOM order. Thu timestamp, class/inline style, computed color/opacity của heading/paragraph/CTA, slide và ancestor opacity, background, transition events/indices nếu truy cập được.
4. Không log per frame ra console. Lưu JSON sau phiên đo, cùng clip và stylesheet/cascade tại frame lỗi. Không đổi effect/duration trong phép so trước/sau repair.
5. Nếu màu computed đổi, tìm declaration/mutation tương ứng. Nếu màu không đổi nhưng vẫn thấy artifact, ghi nhận và thu trace trước khi quy cho compositing/text-shadow.

Outcome được phép: **PASS/FAIL contrast**, và riêng **chớp tái hiện / không tái hiện trong môi trường đã ghi / chưa kiểm được**. Không kết luận engine đã sửa chỉ vì ba transition không thấy chớp.

## Evidence human gửi lại

- Build/lint output, build/commit/asset identity và môi trường.
- Ảnh frontend/editor F1, F3/F4, CTA states; file ảnh đính kèm trực tiếp hoặc đường dẫn truy cập được trong chat này, tránh sandbox link của phiên khác.
- Computed values + winning rule source, xác nhận save/reopen/reset và invalid-block/console status.
- Clip fade, JSON khi tái hiện; nếu thiếu DevTools/JS ghi giới hạn rõ.
- Bảng kết quả theo HERO-UI-01/02/03, HERO-UX-01, HERO-TECH-01/02/03 của plan, giữ mục chưa kiểm ở trạng thái Chờ kiểm chứng.

Agent tiếp tục từ evidence để chỉnh cascade hoặc mở điều tra chớp cụ thể; không bắt human chạy lại fixtures đã đủ bằng chứng nếu source mới không ảnh hưởng chúng.
