# 046 — 1.3.18 Site Loading & Navigation UX Hotfix

Ngày: 2026-11-03. **APPROVED — human đã duyệt phương án A và controls bổ sung; bắt đầu implementation; không đổi milestone/version.**

Human xác nhận đưa UX loading/logo và fade chuyển trang vào **V1 / 1.3.18** hiện tại, commit theo nhãn **UX hotfix**. Đây là scope bổ sung được human chỉ định; không đổi current/version, không lấy số 1.3.19, không mở 1.3.22/23. Các quyết định mới nhất bên dưới thay thế/bổ sung plan 046 cũ.

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

Tận dụng trang admin Loading screen hiện có; không phụ thuộc Design Sidebar 1.3.21.

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
