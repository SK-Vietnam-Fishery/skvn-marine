# 046 — 1.3.18 Site Loading & Navigation UX Hotfix

Ngày gốc: 2026-11-03. **Luồng cũ APPROVED, đã có source tại `b9332d3` — Hotfix Site loader; chưa có evidence runtime PASS.**

Cập nhật mới nhất 2026-11-04: **Human đã ra lệnh bắt đầu thực hiện; §11 có source repair, chưa có onsite PASS.** §1–10 giữ làm baseline/quyết định cũ; execution record dưới §11 ghi scope thực tế và phần còn thiếu evidence. Navigation fade vẫn độc lập. Không đổi milestone/version, không bump/build/commit.

Human xác nhận đưa UX loading/logo và fade chuyển trang vào **V1 / 1.3.18** hiện tại, commit theo nhãn **UX hotfix**. Đây là scope bổ sung được human chỉ định; không đổi current/version, không lấy số 1.3.21, không mở 1.3.22/23. Các quyết định mới nhất bên dưới thay thế/bổ sung plan 046 cũ.

## 1. Authority, mục tiêu và giới hạn

Mục tiêu: logo thương hiệu khi thực sự cần chờ ở lần vào site; bỏ overlay mượt; chuyển nội dung giữa trang bằng fade-out ease-out, không cố tình trì hoãn điều hướng.

Không gồm: sửa skeleton local/toàn trang, xử lý toàn bộ FOUC/CLS/carousel-init, AJAX router/SPA, preload/prefetch engine, thay header/footer, sửa GP parent, page builder hoặc redesign settings toàn plugin. Giữ nguyên acceptance/debt 045 và milestone hiện tại. Hoàn thành hotfix không đồng nghĩa đóng 1.3.18 hoặc xử lý xong hiện tượng chớp cũ.

## 2. Baseline và bằng chứng source

| Nơi | Quan sát | Kết luận có giới hạn |
|---|---|---|
| `wp-content/plugins/skvn-marine-blocks/modules/preloader-settings/preloader-settings.php`, defaults/sanitizer/admin form | Đã có use_logo, logo_id, scope front/site, dismiss hero/window/time, min_display 0–5000 | Có settings source; chưa xác nhận onsite |
| `wp-content/themes/skvn-marine/inc/preloader.php:173–175` | opacity .45s ease; is-hidden đồng thời đặt visibility hidden | Có nguy cơ fade không nhìn thấy; cần ghi hình để xác nhận thực tế |
| Cùng file:246–248 | Logo attachment được chọn, không tự đọc site logo ở nhánh này | Có thể mở rộng fallback, không cần store logo thứ hai |
| Cùng file:295–302,329 | Safety timer gọi hide(), nhưng hide() vẫn chịu min_display | Không dùng đường này làm deadline tuyệt đối cho chế độ mới |
| Cùng file:159–223,240–335 | Preloader/splash/skeleton chia sẻ CSS và lifecycle; scroll lock root | Phải scope luồng mới, tránh thay behavior skeleton; kiểm scrollbar/focus |
| `functions.php:23` | Đã include inc/preloader.php | Ưu tiên không thêm PHP runtime include |

Nguồn lịch sử: [035](archives/035_VER_1_3_13_LOADING_SCREEN_PLANNING.md), [ghi chú nghiên cứu](../../docs/research/site-loading-navigation-ux-2026-11-03.md). Không nâng trạng thái các test cũ thành PASS.

## 3. Decision memo — phương án đã duyệt

1. **Vấn đề:** cần logo loading và chuyển trang mượt trong hotfix, không gây chờ giả hoặc làm hỏng điều hướng.
2. **Phương án:** A — opt-in luồng loading cải thiện + native cross-document transition, fallback điều hướng bình thường; ít can thiệp nhưng hiệu ứng không có trên mọi trình duyệt. (B và C không chọn.)
3. **Khuyến nghị:** A; tách hai toggle (Initial loading / Page transitions), dùng native navigation và không dựng router.
4. **Bằng chứng:** bảng source §2; docs browser §10 chỉ rõ same-origin, cả hai document opt-in, có thể skip transition.
5. **Đảo ngược:** hai chiều; tắt settings quay về luồng cũ/normal navigation; giữ key/dữ liệu cũ. Không migration phá hủy.
6. **Values cố định → default của control:** Easing cố định ease-out; safety timeout cố định 3.500 ms, không bị min_display kéo dài; reduced motion luôn tôn trọng, không cho ép animation. Không mở control cho easing, reduced motion.

## 4. Hợp đồng UX đề xuất

### A. Initial loading — chế độ cải thiện

- Opt-in riêng; chỉ áp dụng type `preloader`. Legacy splash/skeleton/brandbar/off giữ nguyên và không bị toggle này biến thành preloader.
- Hai nhóm hiển thị: **Initial loading** (thường dùng) và **Page transitions**; thông số ít dùng nằm trong **Advanced**. Chế độ Smooth initial loading opt-in, mặc định off cho dữ liệu cũ.
- Giữ scope `front` / `site`: front chỉ xét homepage; site xét mọi trang HTML frontend đủ điều kiện.
- **Frequency (tần suất):**
  - `once` (mặc định): một lần trên tab/session (sessionStorage). Trang không đủ điều kiện không tiêu thụ lượt; reload/back/forward về sau không lặp branding.
  - `every`: mỗi lần trang đủ điều kiện tải, vẫn áp dụng ngưỡng `loading_show_after`; không bắt buộc hiện logo trên trang tải nhanh.
  - Nếu tab/session storage lỗi: bỏ overlay, vẫn cho xem nội dung. Không đặt cookie hoặc truy cập server để nhớ lượt.
- Một khi luồng lần đầu đã được xử lý, đánh dấu đã xử lý kể cả trang đủ nhanh nên không hiện logo. Đánh dấu lúc kích hoạt lifecycle, không đợi tải logo thành công. Không dùng việc click link làm bằng chứng destination đã tải.
- **Logo:** khi use_logo bật, ưu tiên attachment đã chọn hợp lệ (logo_id) → native WordPress custom_logo → mark_text/site name. use_logo tắt giữ text (không ép logo). Ảnh bị lỗi tải chuyển sang text ngay; không chờ logo để giải phóng trang. Giữ khung kích thước để ảnh không làm layout nhảy; dùng màu nền/token hiện có.
- **Advanced — Show loading after:** mặc định **150 ms**, khoảng **0–500 ms**. Tính từ bootstrap phía client nếu initial content còn chưa sẵn sàng. Nếu sẵn sàng trước đó thì bỏ luôn overlay. Đây là delay để quyết định có hiện, không phải delay tải trang. Không thêm min-display. Luồng mới bỏ qua dismiss/min_display cũ; UI phải nói rõ, vẫn lưu nguyên giá trị cũ.
- **Loading fade duration:** mặc định **200 ms**, khoảng **100–500 ms**, easing cố định **ease-out**. Khi sẵn sàng: opacity 1→0 theo fade duration này; chỉ ẩn visibility/remove sau fade hoặc watchdog. Không ẩn visibility ngay ở đầu. Không ép nội dung trang fade-in thêm lần nữa.
- Readiness: DOMContentLoaded và ảnh hero đầu tiên thuộc selector theme đang có, nằm trong vùng nhìn ban đầu, không lazy-load, đã load hoặc error; nếu không có ảnh phù hợp thì DOMContentLoaded là đủ. Không chờ tất cả ảnh, font, third-party hoặc Swiper ready; không đổi loading strategy của ảnh trong hotfix. Ghi rõ đây không bảo đảm mọi lỗi FOUC/CLS đã hết.
- **Safety timeout:** cố định **3.500 ms** từ bootstrap, force-release độc lập, không qua min_display, không bị min_display kéo dài. Timer chỉ là trần an toàn, không phải thời gian buộc chờ. Nếu JS bị chặn hoàn toàn, overlay mặc định không che trang (fail-open). Nếu script lỗi sau khi kích hoạt, CSS fail-open và timer độc lập phải giải phóng cả lớp che, tương tác và scroll state. Không thể cam kết thời gian chính xác khi main thread bị treo; phải kiểm đường phục hồi khi thread hoạt động lại.
- **Reduced motion:** luôn tôn trọng; không cho ép animation. Khi reduced motion, bỏ overlay trang trí/fade, giải phóng ngay, điều hướng bình thường.
- State machine: eligible → pending → visible → dismissing → released; pending→released nếu load nhanh; mọi nhánh lỗi/reduced motion/history restore có đường released idempotent.
- Cleanup history/BFCache: once và every đều phải cleanup khi history/BFCache restore; không replay overlay đang treo.
- Focus: không autofocus vào overlay. Nếu overlay hiện thì chặn keyboard/pointer đi vào nội dung bị che, giữ/khôi phục trạng thái inert/focus trước đó; mọi đường thoát phải cleanup. Ưu tiên không khóa scroll root; không thêm overflow để che geometry. Đánh giá thao tác scroll khi overlay hiện bằng test, không tự thêm body lock ngoài plan.

### B. Chuyển trang

- Setting độc lập `Page transition: None / Fade`; chỉ theme SKVN Marine render. Native cross-document View Transitions, không add dependency, không gọi preventDefault cho link, không setTimeout trước location, không fetch/replace document.
- **Page transition duration:** mặc định **180 ms**, khoảng **100–400 ms**, easing ease-out. Cả source/destination đủ điều kiện và browser hỗ trợ: snapshot trang cũ fade-out theo duration này, trang mới opaque bên dưới để không có khoảng trống do hai lớp cùng trong suốt. Dùng root snapshot cho toàn trang; chưa tách header thành shared element. Verify stacking/blending/size interpolation để chỉ fade, không tự sinh zoom/morph.
- Không làm opacity thật của body về 0 khi click. Trong lúc chờ giữ hành vi điều hướng native; browser có thể bỏ transition khi tải lâu. Không hứa browser giữ nguyên mọi frame đến khi toàn bộ nội dung đích tải xong.
- Frontend HTML public là phạm vi; không editor/admin/login, preview, feed/REST, downloads, cart/checkout/account, quote form/thank-you và kết quả POST. Áp dụng exclusion bằng route/template/cấu hình thực, không hardcode slug suy đoán. Nếu không xác định được route nhạy cảm, mặc định không opt-in route đó.
- Cùng-trang anchor, external, mailto/tel, modifier click/new tab và submit giữ nguyên. Back/Forward native được phép transition nếu đủ điều kiện, nhưng phải giữ scroll/focus restoration và không replay logo. Không intercept submission của CF7/Woo.
- Reduced motion: bỏ overlay trang trí và fade của chế độ mới; nội dung và điều hướng vẫn hoạt động. Browser unsupported/skipped/JS lỗi: normal navigation, không polyfill. Khi destination còn overlay active thì skip view transition, tránh snapshot logo hoặc chồng hai animation. Early lifecycle chỉ dùng khi cần kiểm tra trạng thái này; không dùng render-blocking fetch hoặc chờ promise tài nguyên.
- Với Every page load hoặc legacy overlay: nếu trang đích cần overlay thì ưu tiên loading và skip view transition; không chạy hai animation chồng nhau. Kiểm cả trang nhanh bỏ overlay.

### C. Chống xung đột

- Luồng cải thiện phải có selector/branch riêng; không sửa opacity/dismiss dùng chung của skeleton để tiện implement. Nếu không cô lập được thì báo lại trước mở scope.
- Khi bật Fade nhưng còn chọn legacy overlay mode (splash/skeleton/preloader cũ), skip transition ở lượt có overlay. Không bỏ qua chế độ mà admin đã chọn một cách ngầm định.
- Chế độ off chỉ tắt loading; Page transition là setting độc lập. Plugin tắt thì các chức năng mới mặc định off, không ép bật toàn site từ theme fallback.

## 5. Editor UX và persistence

Tận dụng trang admin Loading screen hiện có; không phụ thuộc Design Sidebar 1.3.19.

| Control | Contract đề xuất |
|---|---|
| Smooth initial loading | Boolean mới, mặc định false để giữ tương thích site cũ; chỉ enable với type preloader; giải thích once per tab, no minimum hold |
| Frequency | Enum mới once/every, mặc định once; once = một lần trên tab/session, every = mỗi lần đủ điều kiện vẫn áp dụng ngưỡng |
| Use logo / Logo | Tái sử dụng use_logo/logo_id; thêm help về fallback site logo/text, không copy site logo vào store mới |
| Dismiss / Minimum display | Khi Smooth bật: disable và help “Managed automatically”; giữ giá trị đã lưu qua save, không reset do field disabled vắng trong POST |
| Scope | Giữ front/site; help giải thích first eligible page trong tab |
| Page transition | Enum mới none/fade, default none; help unsupported/reduced-motion dùng normal navigation |
| Advanced — Show loading after | Số ms, mặc định 150, khoảng 0–500; label/help phân biệt với Fade duration |
| Advanced — Loading fade duration | Số ms, mặc định 200, khoảng 100–500; easing ease-out cố định |
| Advanced — Page transition duration | Số ms, mặc định 180, khoảng 100–400; easing ease-out cố định |

Trong option `skvn_preloader`, đề xuất thêm `smooth_initial_loading`, `page_transition`, `frequency`, `loading_show_after`, `loading_fade_duration`, `page_transition_duration`; sanitize boolean/enum/range, capability manage_options + settings nonce hiện có. Missing key dùng default off/none/once/150/200/180, preserve dữ liệu cũ; đọc cấu hình transition không phụ thuộc hàm eligibility preloader trả false khi type off hoặc scope front. Không sinh bảng/options cạnh tranh.

Các label/help mới và nhãn trong panel được chạm dùng tiếng Anh/i18n text domain plugin. Không để một hàng mới English nằm giữa panel mới bị pha ngôn ngữ: chuẩn hóa text trong trang Loading screen được sửa, không mở đợt dịch toàn plugin. Mobile/keyboard controls rõ label, trạng thái save/reopen đúng. Existing logo preview hỗ trợ fallback bằng help/preview phù hợp; không dựng frontend simulator phức tạp trong admin.

## 6. Work packages và file dự kiến

| Gói | Việc làm sau khi duyệt | Files / output | Gate |
|---|---|---|---|
| P0 | Chụp baseline trên site test; xác định current options, logo, sensitive routes, browser versions và tình huống chớp | Runbook §9, recordings/options; không giả định credential | Biết baseline và URL trước nghiệm thu |
| P1 | Settings/default/sanitizer/help + logo fallback contract | plugin modules/preloader-settings/preloader-settings.php; theme inc/preloader.php | Old settings round-trip, off mặc định |
| P2 | Branch loading mới, state machine, reveal/fail-open/focus/history cleanup | theme inc/preloader.php; chỉ tách asset nếu thật cần, ghi chính xác vào diff | Không chạm skeleton lifecycle; no-JS test |
| P3 | Scoped cross-document CSS + minimal early lifecycle guard/exclusions | ưu tiên inc/preloader.php đang include sẵn; inc/enqueue.php chỉ khi tách asset | Không interception/router; navigation off độc lập |
| P4 | Tests trạng thái và regression; human build/lint, onsite QA | Runbook + evidence theo IDs | Không tự PASS từ source |
| P5 | Review diff, update evidence/context, commit UX hotfix | Chỉ stage hotfix files/hunks và docs liên quan | Có test evidence; không gom thay đổi của task khác |

Nếu thêm PHP require/include: phải làm deploy artifact audit theo workflow; ưu tiên tránh thêm file PHP runtime. Đọc ui-debug skill lúc debug runtime; CSS layout safety đã là prerequisite, không thêm 100vw/overflow-x để che lỗi. Không bump version trong hotfix này. Không sửa build artifacts đang dirty từ tác vụ khác.

## 7. Acceptance & Evidence — tất cả Chưa làm trừ baseline source đã nêu

| ID | Nhóm | Hành vi / Content & States | Hiện trạng | Việc cần sửa | Cách kiểm | Evidence yêu cầu |
|---|---|---|---|---|---|---|
| UXHF-01 | Block UI | Logo riêng/site/text; ảnh lỗi, tên dài, mobile | Đã có source logo riêng; phần mới Chưa làm | Fallback, kích thước, màu | Từng fixture, slow/broken image | Ảnh và recording |
| UXHF-02 | Block UI | Fast load skip, slow load hiện, dismiss theo fade duration ease-out; không giữ giả | Chưa làm | Delay threshold/readiness/state | Cold/warm cache, throttle, hero lỗi/không hero | Timeline, computed animation, video |
| UXHF-03 | Block UI | Navigation fade theo page transition duration ease-out, không trắng do opacity gap | Chưa làm | Snapshot transition | Product/Post/Home/archive, nhanh/chậm, scroll đầu/cuối | Video trước/sau, timing |
| UXHF-04 | Editor UX | Enable/disable, save/reopen, scope, frequency, durations dễ hiểu; legacy values không mất | Chưa làm | Settings contracts §5 | Keyboard/mobile, thiếu key, invalid enum, disabled fields, min/max clamp | Screenshots + saved options |
| UXHF-05 | Technical | Không JS/storage, logo error, fail-open deadline, multiple callbacks | Chưa làm | Idempotent cleanup/watchdogs | Fault injection trên staging | Console/state/timing; không kẹt inert/overlay |
| UXHF-06 | Technical | Excluded routes/forms/anchors/modifier/unsupported hoạt động native | Chưa làm | Opt-in eligibility, fallback | Browser matrix và RFQ flow | URL, form submission ID/time, logs |
| UXHF-07 | Technical | Once eligible per tab, reload, BFCache, back/forward, canceled navigation | Chưa làm | Session guard/history cleanup | Điều hướng lặp, history restore, chặn storage | Video + scroll/focus state |
| UXHF-08 | Cả ba | Reduced motion, keyboard, contrast, screen reader status không spam | Chưa làm | Accessible controls/runtime | OS setting, tab navigation, screen reader smoke | Browser/OS + video/notes |
| UXHF-09 | Block UI/Technical | Legacy skeleton/splash/brandbar/off và carousel không đổi | Chờ kiểm chứng baseline | Isolate changes, regression | Compare cùng options/build trước-sau | Screenshots, geometry, logs |
| UXHF-10 | Technical | Không thêm navigation delay/network fetch/layout shift do hotfix | Chưa làm | Không timer-gated routing, reserved logo space | Perf trace + geometry khi scrollbar dọc hiện | Traces trước/sau; scrollWidth ≤ clientWidth |

Timing là cấu hình yêu cầu; test quan sát không đòi frame-perfect trong môi trường throttled. Ghi cấu hình và thời gian thực tế, điều tra khác biệt thay vì bịa PASS. Layout shift/flash đã có phải tách baseline khỏi regression.

## 8. Commit, rollback và gate hoàn thành

- Tên commit dự kiến: `fix(ux): smooth initial loading and page transitions` (UX hotfix, milestone 1.3.18).
- Chưa commit ở lượt planning. Sau code/test: review staged diff; chỉ include source/hunks của hotfix, plan/runbook/evidence liên quan; giữ nguyên dirty work của user/agent khác. Không push/deploy tự động.
- Rollback nhanh: Page transition None + Smooth initial loading off; giữ dữ liệu logo/options. Rollback source: revert đúng commit hotfix nếu không có dependency về sau. Verify lại legacy behavior và purge cache qua quy trình site đã có.
- Build/lint do human chạy theo AGENTS; agent cung cấp đúng lệnh theo files thay đổi, không tự chạy. Tests bổ sung tập trung state race/cleanup/settings preservation, không viết test chỉ lặp CSS constants.
- Hoàn thành hotfix khi UXHF-01–10 có evidence và human duyệt onsite. Browser không hỗ trợ có thể PASS fallback, không gắn PASS hiệu ứng. Không đóng milestone toàn bộ.

## 9. Onsite test handoff

Runbook: [site-loading-navigation-ux-hotfix-1.3.18.md](../../docs/testing/site-loading-navigation-ux-hotfix-1.3.18.md). URLs cần điền từ site test thực trước execution; không tự dựng URL hoặc yêu cầu production credentials. Tests đang Chưa làm.

## 10. Nguồn và giới hạn

- [Chrome cross-document view transitions](https://developer.chrome.com/docs/web-platform/view-transitions/cross-document): same-origin/both pages opt-in; native navigation có thể skip; pageswap/pagereveal để guard. Không cần chuyển WordPress thành SPA. Tóm tắt nguồn giới hạn ở các premise này.
- [W3C reduced motion](https://www.w3.org/WAI/WCAG22/Techniques/css/C39): cơ sở tôn trọng tùy chọn giảm chuyển động; không khẳng định mọi opacity fade vi phạm WCAG.
- [Review contract](../../docs/standards/block-ui-editor-ux-technical-review-contract.md), [CSS safety](../../docs/standards/css-layout-safety-contract.md), [Decision memo](../../docs/standards/decision-memo-format.md).

Đọc nguồn ngày 2026-11-03. Browser API là progressive enhancement; chưa xác nhận browser versions onsite. Plan approval không phải runtime evidence.

## 11. Proposal 2026-11-04 — homepage loader kéo rèm

### Execution record — human start + DOM evidence 2026-11-04

Record này cập nhật authority/status của proposal bên dưới; các câu “chưa code/chưa được phép implementation” ở snapshot planning giữ để truy vết, không còn là gate xin phép lại. Human đã yêu cầu bắt đầu và cung cấp DOM/config của `https://minhhaifishery.com/home/`.

- Main: duy nhất `.skvn-slider--hero[data-skvn-slider]`, 2 slides, slidesPerView=1, viewport-below-header, loop, autoplay 7000ms, fade 800ms, parallax Advanced (horizontal/both, translate42/scale1.14/inset35). Secondary Collection: 4/2/1, gap16, autoplay=false. Không đổi các cấu hình này.
- Loader snapshot onsite: type=preloader, **smooth_initial_loading=false**, frequency=once, page_transition=fade, show_after=100/fade=100/page_transition=200. Snapshot không chứa scope/palette/logo option đầy đủ hoặc asset version. Không tự sửa option onsite. Để chạy repair cần human bật Smooth.
- Quy tắc tích hợp đã triển khai: giữ opt-in Smooth + preloader; homepage WordPress và landing page `/home/` human chỉ định dùng curtain; scope front/site và once/every giữ nghĩa trong branch mới. `/home/` cũng đủ điều kiện front khi Smooth bật dù chưa được đặt làm WordPress front page. Outside homepage giữ bootstrap hiện có. Show-after/fade chỉ disable khi Smooth + front, vì site scope còn dùng chúng ở trang khác; dismiss/min-display được Smooth quản lý; sanitizer preserve fields vắng POST.
- **Đã có source, chờ kiểm chứng onsite:** L1 Hero CSS fallback một khung (overlap slides thay vì xếp chồng viewport); L2 ready sau constructor+size sync+rAF/usable geometry và gate autoplay optional; L3 head lifecycle, transform750, deadline1500, cleanup850 sau reveal khi thiếu event, independent watchdog2500, CSS/no-JS/reduced-motion bypass, history cleanup/transition skip; L4 controls/help/persistence và ba field step1/show-after0. Không sửa Collection/skeleton/query/bundle order; màu nền/logo vẫn dùng cấu hình hiện tại. Không pause decoration chưa định danh; selector/evidence decoration và visual màu/contrast còn thiếu, không coi phần này hoàn thành.
- Ready contract: đọc lại data-skvn-slider-ready hoặc nhận skvn:slider-ready; chỉ chấp nhận duy nhất Hero có geometry dương. Không dùng class initialized trước constructor làm proof. Loader gate có deadline để theme lỗi không pause plugin vĩnh viễn; chỉ bỏ reason loader, giữ focus/pointer/hidden/interaction. Kiểm tra local Swiper module `node_modules/swiper/modules/autoplay.mjs` cho thấy init chỉ start khi params.autoplay.enabled; gate tắt enabled trước constructor, coordinator start sau release. Deployed version vẫn chưa xác minh.
- Numeric patch còn sửa lỗi cú pháp baseline: dư dấu `}` trước catch trong sessionStorage check của page-transition bootstrap đã chặn toàn script. Đã sửa cùng đợt; đây khác nguyên nhân stepMismatch. Giữ ranges/defaults/keys, không bump/migration dữ liệu.
- **Evidence tự động giới hạn:** `node tools/tests/site-loader-curtain.test.mjs` chạy 8/8 PASS trên actual inline lifecycle trong Node VM với mock DOM/clock: ready/late/missing/ambiguous/zero-width, event filtering, timeout, once/storage/reduced-motion, history, keyboard cleanup, exception/watchdog. Không chứng minh paint/geometry/CSS/autoplay Swiper thực. `node --test` bị sandbox EPERM khi spawn; chạy trực tiếp file test thành công. Build/lint do human chạy, chưa chạy onsite, không đánh dấu toàn LDR PASS.
- L0 còn thiếu geometry trước/sau, pre-init stylesheet timing trên cache thực, palette/contrast, decoration selectors và deployed assets. L5 chưa thực hiện. Agent tiếp tục từ build output và onsite evidence; không đóng milestone hoặc commit tự động.

### 11.1. Authority và phạm vi

Human yêu cầu đọc spec, điều tra commit **`b9332d336edcde207b6ae5bfb53392325b68c9f7` — Hotfix Site loader**, phân tích cải thiện và đề xuất planning; **không code và không tạo commit**. Kiểu kéo rèm lên, 750ms, trigger Swiper ready, pre-init độc lập, fallback 1500ms, reduced-motion bỏ chuyển động là hướng human đã ghi trong spec; không hỏi lại lựa chọn hiệu ứng. Chi tiết tích hợp/settings dưới đây vẫn là proposal cần review trước implementation.

Target đề xuất: sửa tiếp hotfix loading trong **V1 / 1.3.18**, không mở version mới (mapping mới 2026-11-04: 1.3.19 Sidebar, 1.3.21 Hero contrast). Current và acceptance 045 giữ nguyên. Homepage là phạm vi repair; không ép áp dụng kéo rèm toàn site. Không sửa skeleton local/toàn trang, query/content, số slide, cấu hình autoplay/loop/slidesPerView, thứ tự bundle hoặc thêm animation library. Tạm giữ autoplay vì loader là lifecycle gate, không đổi setting autoplay đã lưu.

### 11.2. Baseline đã xác minh và giới hạn kết luận

| Evidence tại b9332d3 | Finding source | Giới hạn |
|---|---|---|
| `inc/preloader.php:279–292` | Overlay fade opacity, is-hidden ẩn visibility ngay; no-JS CSS thoát sau 3500ms | Chưa phải kéo rèm hoặc no-JS hiện trang ngay |
| `inc/preloader.php:409–478` | Smooth release theo document/ảnh hero và watchdog 3500ms; chưa đọc Swiper readiness | Không bảo đảm geometry slider ổn định |
| `inc/preloader.php:548–569` | Logo nằm trong overlay; Smooth không render spinner | Chưa chứng minh logo có fade độc lập; spinner onsite cần xác minh nhánh/settings/deploy |
| `src/slider/view.ts:447,576,604,636` | Class skvn-slider--initialized có trước constructor; autoplay chưa có loader gate; card breakpoints 600/1024 | Không dùng class đó làm proof init thành công; config homepage chưa biết |
| `src/collection-view.ts:26–29,80–117,295–313`; `src/collection/style.css:315–354` | Collection đã có pre-init, geometry check và skvn-is-ready; breakpoints 640/1024 | Audit/reuse trước; không thêm owner hoặc sửa skeleton |
| Plugin `package.json:11`, `package-lock.json:19863–19865` | Range ^11.1.15, lock Swiper 11.2.10 | Chưa xác nhận bundle/version đang deploy onsite |

Các con trỏ `src/*` thuộc `wp-content/plugins/skvn-marine-blocks/`; `inc/*` thuộc theme. Không suy ra chớp là regression do commit này: commit không sửa runtime/pre-init slider. Không coi màu hiện tại, nguyên nhân glass repaint, hạt/cá hoặc màu thay thế là đã xác minh từ source.

### 11.3. P0 — inventory bắt buộc trước code

| Ô cần điền | Đã biết | Evidence còn thiếu / gate |
|---|---|---|
| Target URL và slider chính | Candidates: [data-skvn-slider] hoặc [data-skvn-collection-carousel] với .skvn-collection__carousel | URL staging/Home, saved markup + DOM; xác định duy nhất slider cổng, không lấy first match tùy tiện |
| Loader/render | #skvn-preloader, theme inc/preloader.php, wp_body_open | Settings thực: type/scope/Smooth/frequency, CSS override/cache và build onsite |
| Runtime/version | Slider src/slider/view.ts; Collection src/collection-view.ts; lock 11.2.10 | Xác nhận block thật và asset/version thực được tải |
| Columns/gap/breakpoints/height | Hai runtime khác nhau, Collection có vars/preset sẵn | data attributes, computed CSS, width/height trước/sau init tại từng breakpoint; không dùng preset 4–2–1 giả định |
| Màu và decoration | Có palette/theme tokens; chưa biết surface homepage thực | Màu trung tính khớp surface + contrast logo; selectors/owner animation nền và rule ảnh hưởng logo |
| Asset delivery/deploy | Theme inline head/body; plugin @wordpress/scripts; tools/build-deploy-artifact.mjs và tools/package-plugin-zip.sh | Prove CSS pre-init có trước first paint và cache purge path; human chạy build/lint/package khi có code |

Không điền được ô nào từ repo thì dừng phần implementation phụ thuộc ô đó và xin đúng evidence onsite còn thiếu; không tự bịa URL/selector/màu. Planning và review source vẫn thực hiện được. Không giả định credentials hoặc thay production settings.

### 11.4. Contract đề xuất và mapping thay thế

Chỉ áp dụng trong homepage repair đã định danh ở P0; ngoài phạm vi này giữ branch hiện hữu và regression-test. Không đổi dữ liệu cũ để ép scope/frequency. Đề xuất giữ once/every hiện có: first eligible homepage visit trong once, mỗi homepage load trong every; câu “loader hiện từ first paint” chỉ áp dụng lượt đủ điều kiện. Điểm tích hợp này cần được chốt trước code.

| Contract cũ | Proposal cho homepage repair | Mapping acceptance |
|---|---|---|
| Show-after 150ms, load nhanh bỏ overlay | Lượt đủ điều kiện có lớp che từ first paint; ready sớm thì thoát ngay, không minimum hold | UXHF-02 → LDR-01,02; fast-skip cũ chỉ bị thay trong homepage repair |
| Dismiss theo DOM/hero | Đúng slider chính init thành công + geometry dùng được, không đợi ảnh/font/slider phụ; nếu late subscription phải đọc state hiện tại | UXHF-02,05 → LDR-02,05 |
| Fade duration 100–500ms/ease-out | Panel transform lên khỏi viewport 750ms, easing chậm hai đầu; logo/spinner nếu có đi cùng panel; không opacity/visibility/filter transition trên panel | UXHF-01,02 → LDR-03 |
| Safety 3500ms | Bắt đầu reveal ở deadline 1500ms từ early bootstrap nếu chưa ready; log warning một lần; 750ms sau mới kết thúc nominal | UXHF-05 → LDR-05 |
| Giữ control cũ | Trong branch repair, disable/help show-after, fade-duration, dismiss/min-display; preserve values qua save/reopen; 750/1500 là constants, không thêm control | UXHF-04 → LDR-07 |
| Navigation fade | Giữ nguyên scope độc lập; nếu destination có overlay thì không chồng hai hiệu ứng | UXHF-03,06,07 giữ; bổ sung LDR-08 |

Mốc 1500ms đo từ bootstrap sớm nhất kiểm chứng được, không tuyên bố đo chính xác first paint bằng timer. Nominal fail-open hoàn toàn khoảng **2250ms** cộng scheduling; main thread treo không thể bảo đảm deadline cứng. Cần bounded cleanup dự phòng khi transitionend không tới, không giữ loader vĩnh viễn.

**Pre-init:** ưu tiên geometry khớp config thật và một owner trong plugin. Collection reuse CSS/state hiện hữu; Slider audit fallback riêng. CSS phải có hiệu lực trước lần vẽ đầu, bỏ gap pre-init khi runtime own spacing để tránh gap kép, giữ chiều cao media/pagination theo DOM thật. Nếu không thể khớp config, phương án ẩn track phải có reserved space và đường static-visible cả no-JS lẫn bundle lỗi; không chỉ có noscript. Không thay skeleton state/markup; nếu không tách được phải báo scope conflict trước code. Không dùng overflow-x để che geometry sai.

**Lifecycle:** eligible → covering → revealing → released. Mọi ready/deadline/error/history callback dùng cùng đường reveal/cleanup idempotent. Ready phải có đường đọc lại trạng thái để không bỏ lỡ init sớm; không dựa riêng vào class thêm trước constructor. Deadline thắng thì tiếp tục fallback tĩnh, late init không được replay reveal hoặc để track ẩn. Không trì hoãn slider phụ bằng một scheduler mới.

**Motion/autoplay:** trước reveal chỉ pause các decoration đã inventory và spinner nếu thực có; không animate filter/backdrop-filter/width/height/background. Runtime plugin phối hợp loader bằng gate optional, mặc định hoạt động bình thường nếu theme/loader vắng; không khiến plugin phụ thuộc theme. Gate phải có trước autoplay đầu tiên, áp dụng cả init muộn, và release chỉ bỏ pause reason loader, giữ hidden/focus/user pause. Cleanup khôi phục trạng thái animation trước đó, không bật tất cả animation toàn trang. Không thêm spinner vào Smooth nếu chưa có quyết định thay đổi visual.

**Safety/a11y:** không JS thì overlay không che từ đầu, nội dung static-visible. Early safety độc lập bundle Swiper; nếu JS lỗi sau activation phải giải phóng cả overlay lẫn interaction/scroll state. Ưu tiên không khóa root scroll. Nếu cần inert thì lưu/khôi phục giá trị trước đó; CSS ẩn overlay không tự khôi phục inert, nên không đặt inert khi chưa có đường recovery đã chứng minh. Không autofocus loader; status không spam; cleanup lọc transitionend đúng panel/property, có fallback transitioncancel/missing-event. Reduced-motion từ CSS ban đầu và JS: không rèm/fade, bỏ overlay ngay, giữ slider static/ready dùng được. History restore không replay overlay.

### 11.5. Work packages — thứ tự và gate

| Gói | Công việc đề xuất | Files dự kiến | Gate |
|---|---|---|---|
| L0 | Điền inventory P0, baseline cold/warm, chọn scope/frequency/settings và màu thực | Plan + runbook/evidence | Không thiếu input §11.3; review proposal trước code |
| L1 | Audit/fix đúng pre-init owner, xác nhận CSS đến trước paint; không sửa skeleton | Plugin src/slider/style.css hoặc src/collection/style.css; render/enqueue chỉ nếu evidence cần | No-JS/bundle-blocked dùng được, geometry trước/sau có số đo |
| L2 | Ready contract + optional autoplay gate, xử lý early/late init | Runtime block chính; shared/autoplay.ts chỉ khi cần invariant chung | Không bỏ lỡ ready, không autoplay dưới overlay, theme vắng vẫn hoạt động |
| L3 | Cover/reveal/release, pause decoration, independent safety, reduced-motion/history | Theme inc/preloader.php; shared theme runtime chỉ cho decoration thực | Một lần kéo rèm; mọi đường lỗi đều thoát; không động skeleton |
| L4 | Control applicability/help, preserve saved values, English/i18n; vá bước nhập ms và zero fallback theo §11.8 | Plugin modules/preloader-settings/preloader-settings.php; theme inc/preloader.php | Save/reopen đúng, ms nguyên bất kỳ trong range không stepMismatch, 0 không bị đổi thành 150 |
| L5 | Human build/lint, onsite QA, rollback rehearsal; ghi evidence theo ID | Runbook dưới đây | Acceptance có evidence, human duyệt visual; không tự đóng milestone |

Không đổi thứ tự bundle nếu chưa có evidence đó là nguyên nhân. Chỉ tách asset/include khi cần; thêm/đổi PHP runtime include phải audit packaging. Rollback source phải là patch riêng được review, không revert mù toàn b9332d3 vì commit chứa cả docs và thay đổi khác. Tắt Smooth chỉ quay về legacy, không có nghĩa legacy đã hết flash. Không commit/push/deploy trong lượt này.

### 11.6. Acceptance & Evidence bổ sung

| ID | Nhóm | Hành vi mong đợi / states | Hiện trạng | Việc cần sửa | Cách kiểm | Evidence |
|---|---|---|---|---|---|---|
| LDR-01 | Block UI/Technical | Pre-init khớp ở cold/warm/no-JS, mobile/desktop, ít slide | Collection đã có source; homepage Chờ kiểm chứng | Đúng owner/config/asset timing | Chặn bundle, throttle và đo trước/sau init | URL/DOM/options, filmstrip, slide/track/pagination boxes và delta height px |
| LDR-02 | Technical | Mở theo slider chính usable; không phụ thuộc ảnh/slider phụ | Chưa làm | Ready contract + đọc state nếu init sớm | Stall main, stall secondary, init trước/sau listener | Timeline init/ready/reveal và geometry |
| LDR-03 | Block UI | Một rèm 750ms, logo cùng panel; không lớp vô hình chặn | Chưa làm | Transform lifecycle, decoration pause, remove | Cold/warm, CSS transition bị hủy, click/Tab sau reveal | Video, computed transitions, DOM/focus notes |
| LDR-04 | Technical | Autoplay chỉ sau release, giữ pause khác, plugin không theme | Chưa làm | Optional gate trong runtime, late init | Delay init qua deadline; hover/focus/hidden, theme/loader absent | Slide index/timestamps, pause state, console |
| LDR-05 | Technical | 1500ms fallback bắt đầu; cleanup bounded; no-JS/bundle lỗi/partial JS đều xem trang được | Chưa làm | Independent recovery + static fallback | Block bundle, throw sau activation, missing transitionend | Timeline, warning, overlay/inert/scroll final state |
| LDR-06 | Block UI/Technical | Reduced motion không rèm/fade; keyboard/status hợp lý | Chưa làm | CSS-first bypass + focus recovery | Preference trước load và đổi lúc reveal; Tab/screen reader smoke | OS/browser/video, focus/status notes |
| LDR-07 | Editor UX | Controls đúng applicability, values cũ không mất | Chưa làm | Scoped disable/help + sanitize/preserve | Toggle modes, save/reopen, disabled fields thiếu POST | Screenshots và sanitized option snapshot |
| LDR-08 | Cả ba | Scope/frequency/history/native navigation không regression; skeleton không đổi | Chờ kiểm chứng baseline | Isolate branch, không chồng animation | Home↔Post, once/every, Back/Forward, legacy modes | Before/after cùng fixtures; UXHF-03,06,07,09,10 |
| LDR-09 | Editor UX/Technical | Ba field ms nhận số nguyên bất kỳ trong range; save/reopen/runtime giữ đúng, show-after 0 hợp lệ | FAIL source: step=100; runtime dùng zero-falsy fallback; human báo lỗi qua screenshot | L4: step=1, giữ range/default, bỏ fallback bằng truthiness cho show-after | Nhập 150/175/250/180, save/reopen; arrow ±1; show-after 0; kiểm default 150/180 và biên | Screenshot + validity.stepMismatch, options/inline config trước/sau, timeline bootstrap; runtime chưa kiểm |

Không đặt ngưỡng height tùy tiện: L0 ghi delta px và nguyên nhân; L1 phải review mức chấp nhận cụ thể trước nghiệm thu, không ghi PASS bằng “không đáng kể”. IDs UXHF-01–10 vẫn giữ: LDR thay contract homepage được mapping phía trên; acceptance navigation/legacy ngoài phạm vi đó tiếp tục nguyên vẹn. Tất cả runtime tests còn Chưa làm/Chờ kiểm chứng; có source không đồng nghĩa PASS.

### 11.7. Quyết định còn mở trước implementation

Không xin duyệt lại kéo rèm. Cần chốt cách tích hợp homepage với scope/frequency và control cũ sau khi có inventory. Proposal ưu tiên giữ options cũ, scope repair hẹp, không đổi hành vi site-wide; phương án áp mọi homepage load bất kể frequency che tốt hơn nhưng thay nghĩa settings. Màu thay thế chỉ chọn sau khi thấy surface/logo thực. Default trước lệnh bắt đầu implementation: **giữ source hiện tại, chỉ hoàn thiện planning/evidence**; không tự kích hoạt sửa code vì tài liệu đã đủ chi tiết.

### 11.8. Bổ sung bug nhập milliseconds — human report 2026-11-04

**Triệu chứng:** human cung cấp screenshot Advanced (Show loading after, Loading fade duration, Page transition duration), báo chỉ điều chỉnh được theo nấc 100ms. Screenshot ghi nhận UI/giá trị, chưa tự chứng minh browser validation hay dữ liệu đã lưu. Áp dụng ui-debug State Delta: state mong đợi là nhập/lưu 150/175/250/180 trong range; state sai là native spinner nhảy 100 và giá trị giữa nấc có stepMismatch. Layer khác biệt nằm ở HTML control, không phải CSS hay Swiper.

**Evidence source:** `modules/preloader-settings/preloader-settings.php:353–363` khai báo `step="100"` cho cả ba input number. Show-after có min=0, hai duration có min=100 nên các nấc hợp lệ đều là bội 100. Default show-after **150** và page-transition **180** cũng không khớp bước nhập của chính control. Sanitizer cùng file `:168–170` dùng absint + min/max, không làm tròn bội 100; do đó ưu tiên sửa constraint UI, không thêm rounding server. `theme inc/preloader.php:463` dùng `cfg.loading_show_after || 150`, khiến **0 đã lưu vẫn bị dùng như 150ms** trong nhánh hiện tại. Đây là bug runtime liên quan, không phải nguyên nhân stepMismatch.

**Patch đề xuất trong L4, chưa implement:**

1. Đổi **ba input Advanced** sang `step="1"`: nhập số nguyên ms bất kỳ, spinner/ArrowUp/ArrowDown ±1ms. Không dùng step=any vì contract là integer; không sửa mọi field number của trang bằng replace toàn cục.
2. Giữ range hiện tại: show-after **0–500**, loading fade **100–500**, page transition **100–400**; giữ defaults **150/200/180**. Ví dụ 750 không hợp lệ cho duration cũ; rèm 750ms vẫn là constant riêng của proposal, không mở range để lẫn hai chế độ.
3. Giữ option keys và dữ liệu cũ, không migration/rounding theo 100; kiểm UI→sanitize→saved option→PHP/JS config. Bỏ fallback truthiness ở show-after, dùng giá trị số đã normalize (hoặc kiểm missing/invalid rõ ràng), để 0 thực sự có nghĩa không trì hoãn quyết định. Giá trị ngoài range vẫn bị chặn/clamp theo contract hiện tại; fractional không thuộc scope integer ms.
4. Homepage repair có thể disable show-after/fade theo §11.4, nhưng phải vá field khi dùng branch khác; page-transition vẫn độc lập. Không che bug bằng disable toàn bộ Advanced, không thay đổi eligibility hoặc tự bật/tắt settings. Thêm help integer/range nếu cần, English/i18n.

**Acceptance:** LDR-09 kế thừa UXHF-04 và mở rộng LDR-07. Kiểm bằng runbook T9; source finding không đồng nghĩa fix/runtime PASS. Không sửa code/build/commit trong lượt ghi kế hoạch này.
