# Onsite runbook — UX hotfix loading/navigation 1.3.18

2026-11-03, cập nhật 2026-11-04. **Baseline cũ đã có source tại b9332d3 — Hotfix Site loader; runtime chưa kiểm chứng. Homepage curtain repair là proposal, chưa code/build.** Sequence cũ bên dưới giữ để kiểm baseline/navigation/legacy. Với homepage repair sau khi được duyệt, dùng phần LDR cuối file thay expectations threshold/fade/3500ms cũ, theo mapping plan 046 §11. Không coi approval cũ là approval implementation proposal mới.

Plan và IDs: [046](../../.context/planning/046_VER_1_3_18_SITE_LOADING_NAVIGATION_UX_HOTFIX_PLAN.md).

## Preconditions và target

- Dùng staging/test site được human cho phép; không tự thay config production. Snapshot options và build trước test để khôi phục.
- Điền trước execution: site/base URL __; Home __; Product có gallery __; Product thiếu ảnh __; Post dài __; archive/category __; trang carousel __; Quote __; Thank-you __; Woo cart/account/checkout nếu có __. Route không tồn tại ghi N/A, không coi missing local content là lỗi source.
- Build/commit trước __; sau __; WP/Woo versions __; logo riêng/site logo/test broken logo __; options trước/sau __.
- Browser versions thực: desktop Chrome/Edge __; Safari/iOS __; Firefox __. Test browser có support để nghiệm thu hiệu ứng và browser không support để nghiệm thu fallback. Browser chưa test ghi Chờ kiểm chứng, không suy PASS từ docs.
- Device/viewports: desktop 1440px với scrollbar dọc, tablet 768px, mobile 390px; keyboard và reduced-motion. Không coi giả lập mobile là đủ evidence iOS nếu iOS là target.
- DevTools Preserve log, Network cache on/off và throttle, recording có timestamps. CF7/CFDB7 test theo quy trình quote hiện có, dùng dữ liệu thử được phép; không gửi RFQ thật ngoài dự kiến.

## Test sequence

| Bước / IDs | Thao tác chính xác | Expected / tiêu chí PASS |
|---|---|---|
| 1 — 04,09 | Với build mới nhưng hai setting mới off, lần lượt chạy options legacy preloader/splash/skeleton/brandbar/off; compare baseline cùng fixtures | Giữ behavior cũ; không có transition mới. Known debt ghi riêng, không đòi sửa skeleton |
| 2 — 01,04 | Bật Smooth cho type preloader; use_logo on, lần lượt logo riêng → bỏ logo riêng → không site logo → broken image; save/reopen | Chọn đúng fallback; broken image chuyển text; UI English/i18n; min_display/dismiss cũ vẫn được giữ dù controls disabled |
| 3 — 02,07 | Tab mới, scope front, mở Post rồi Home; sau đó reload Home, qua Post rồi Back. Lặp scope site bằng tab session mới | Chỉ first eligible page có cơ hội hiện; các lượt sau không replay; scope front không tiêu thụ lượt trên Post |
| 4 — 02 | Cold cache/throttle vào eligible page; lặp warm cache/no throttle; thử không hero, hero lỗi, hero lazy/offscreen, slow unrelated image | Nhanh trước threshold bỏ overlay; chậm hiện nếu chưa ready; chỉ initial qualifying hero được xét; không chờ ảnh không liên quan |
| 5 — 02,05 | Stall hero request; legacy min_display lưu 5000 rồi bật Smooth; xem timeline | Chế độ mới không bị hold 5000; release theo deadline 3500ms khi thread chạy, fade theo config; không khóa tương tác lâu dài |
| 6 — 03,06 | Bật Fade; Home→archive→Product→Post, đầu/cuối trang; thử network rất chậm và response lỗi trên staging | Có fade theo page transition duration ease-out ở browser support; không body opacity zero trên click; không khoảng trống do animation; browser skip thì normal navigation |
| 7 — 05,07 | JS disabled; chặn storage; lỗi script sau activate (fault injection staging); Back/Forward BFCache; cancel navigation rồi bấm lại; nhanh nhiều link | Trang không bị che/khóa vĩnh viễn, không kẹt inert/scroll/focus, không replay logo hoặc listener nhân đôi |
| 8 — 06 | Anchor, Ctrl/Cmd-click, target blank, download, external, mailto/tel; Quote form success/error và thank-you; Woo sensitive routes nếu tồn tại | Native semantics; không intercept; sensitive routes không opt-in; RFQ context/data/submission giữ đúng |
| 9 — 08 | Bật reduced motion trước load, thao tác bằng Tab/Enter; thử đổi preference; screen reader smoke; admin form mobile | Không animation trang trí ở luồng mới; không focus vào vùng bị che; release khôi phục focus; thông báo không lặp spam; controls có label |
| 10 — 09,10 | So carousel/slider/header/footer ở 3 viewport trước/sau; đo scrollWidth/clientWidth và bounding boxes khi scrollbar dọc hiện; capture perf trace | Không tràn mới, không nhảy ngang do lock/unlock; không script-gated delay hoặc fetch router; ghi baseline/new layout shifts, không đánh đồng với flash cũ |
| 11 — rollback | Tắt Fade và Smooth, save/reopen, lặp Home→Product; khôi phục test options | Về normal navigation/legacy loading; không mất logo/config hoặc tồn tại overlay state |

## Controls-specific checks (thêm vào sequence trên)

| Control | Kiểm |
|---|---|
| Frequency once | sessionStorage ghi sau lần hiện đầu; tab mới/lần sau không hiện lại trên trang eligible |
| Frequency every | Mỗi lần load eligible page vẫn kiểm tra ngưỡng show loading after; trang nhanh (trong 150ms) không hiện overlay |
| Show loading after min 0 | Overlay hiện ngay khi bootstrap (nếu chưa ready) |
| Show loading after max 500 | Chờ tối đa 500ms trước khi quyết định hiện |
| Show loading after invalid/non-number | Clamp về default 150ms, không lỗi JS |
| Loading fade duration min 100 / max 500 | Fade-out tốc độ tuỳ config, ease-out |
| Loading fade duration invalid | Clamp về default 200ms |
| Page transition duration min 100 / max 400 | Fade-out trang cũ theo config |
| Page transition duration invalid | Clamp về default 180ms |
| save/reopen | Tất cả giá trị control lưu đúng, reopen hiển thị lại |
| Disabled field not in POST | Giá trị cũ preserved, không reset |

## Evidence gửi lại

Mỗi ID UXHF-01–10: status Chưa làm / Đã có source / Chờ kiểm chứng / PASS / FAIL / Deferred; expected, actual, URL, build, OS/browser, options, timestamp. Attach video loading/navigation, screenshots settings/logo/fallback, console errors, network/perf traces và geometry. Quote: submission time/ID, CFDB7 row và email status theo test contract hiện có; không đưa thông tin nhạy cảm vào repo.

Không đạt nếu navigation bị delay nhân tạo, logo lặp mọi trang, overlay/scroll/inert bị kẹt, input/form bị intercept, accessibility hoặc skeleton/carousel regression. Unsupported browser normal navigation = PASS fallback, không phải PASS animation. Human duyệt visual trước completion; còn lỗi thì giữ FAIL/Chờ kiểm chứng, không đánh dấu toàn milestone xong.

## Homepage curtain repair — LDR runbook đề xuất 2026-11-04

**Execution update:** human đã yêu cầu thực hiện; source repair đã có, onsite Chờ kiểm chứng. Target **https://minhhaifishery.com/home/**. Main duy nhất `.skvn-slider--hero[data-skvn-slider]`, Hero2 slides/SPV1/autoplay7000/loop/fade800/viewport-below-header; Collection4/2/1 gap16 autoplay=false là secondary. Evidence DOM do human cung cấp, không phải agent đo geometry. Snapshot loader: preloader, Smooth=false, once, transition=fade, 100/100/200ms; scope/palette/version chưa có trong snapshot.

Trước test: human build/lint source, đưa lên môi trường được phép theo workflow deploy và purge cache; snapshot options rồi bật **Smooth initial loading** (không bật thì vẫn là legacy). Chọn frequency Every để lặp test, sau đó test Once bằng tab/session mới và restore options. Kiểm /home/ cả khi là front page WordPress và khi là landing page riêng nếu có fixture. Với scope Site, show-after/fade vẫn enabled vì các trang khác còn dùng; với Smooth+front, chúng disabled và save phải giữ values. Kiểm transition duration luôn chỉnh độc lập. Màu/logo giữ option hiện tại; human cung cấp screenshot palette/contrast và selectors decoration nếu thấy animation nền cần pause — phần pause decoration chưa có source.

Evidence local: `node tools/tests/site-loader-curtain.test.mjs` **8/8 PASS** trên actual head lifecycle với mock clock/DOM, không phải onsite PASS. Node test-runner subprocess bị sandbox EPERM; chạy file trực tiếp thành công. Test bao gồm filter transition đúng panel/property, ready sớm, late/missing/ambiguous/zero-width main, deadline, storage, preference/history, keyboard và exception watchdog. Agent chưa chạy plugin build/PHP lint theo AGENTS. Kiểm thêm Console không còn SyntaxError trong bootstrap legacy sau sửa dấu ngoặc.

Source timing: transition750ms; deadline1500 bắt đầu fallback, transitionend thường cleanup khoảng2250ms; nếu thiếu event, cleanup backup2350ms; watchdog2500ms. Đo scheduling thực, không ép frame-perfect. CSS watchdog là đường an toàn, không chứng minh state JS/autoplay đã cleanup; kiểm root attributes, listeners/pause và CTA sau release.

Status: **Đã có source / Chờ kiểm chứng onsite**, không phải test deferred. Target current V1 / 1.3.18, không đổi version. Baseline b9332d3 và repair chưa commit; human build/lint và điền inventory còn thiếu trước khi nghiệm thu.

### Setup bắt buộc

- Home URL __; secondary page __; slider chính selector duy nhất __; block/runtime __; main/secondary asset URL __. Không có URL/DOM thực thì chưa bắt đầu nghiệm thu.
- Điền plan §11.3: actual config/data attributes, breakpoint/columns/gap, surface token/màu __, logo __, animation selectors __; type/scope/Smooth/frequency __; installed/deployed build/version __.
- Baseline b9332d3 hoặc build thực __; repair __; OS/browser __. Chụp options để restore; chỉ fault-injection trên staging được phép. Build/lint do human chạy theo AGENTS khi có source mới.
- Cold cache + Slow 3G, warm cache; desktop có scrollbar dọc, tablet/mobile. Thử các width ngay dưới/tại/trên breakpoint thật, không áp 640 cho Slider card dùng 600. Bật Preserve log, filmstrip/recording và performance timestamps.
- Đo main container/track/first two slides/pagination và page clientWidth/scrollWidth trước init, sau ready, sau release; ghi delta height px. Review ngưỡng chấp nhận từ baseline trước khi ghi PASS.

### Steps / Expected / Evidence

| Test / IDs | Thao tác chính xác | Expected và tiêu chí PASS | Evidence cần gửi |
|---|---|---|---|
| T1 / LDR-01,03 | Tab session mới; cold/Slow 3G vào Home, lặp warm cache với lượt đủ điều kiện; record first paint→release | Pre-init không phình/co; một rèm lên 750ms khi ready; logo cùng panel; không hold giả | Filmstrip/video, config, boxes/delta px, ready/reveal/end timestamps |
| T2 / LDR-02 | Delay main Swiper asset trên staging; lần khác chỉ delay slider phụ/ảnh không liên quan. Lặp cache nhanh để main init trước listener | Chỉ main usable geometry quyết readiness; không bỏ lỡ ready sớm; không chờ slider phụ/ảnh khác | Network log, timeline và main geometry |
| T3 / LDR-01,05 | Chặn main bundle hoàn toàn; reload Home. Sau đó bỏ chặn và delay init đến sau 2250ms | Bắt đầu fallback reveal khoảng bootstrap+1500ms; nominal kết thúc khoảng +2250ms, ghi scheduling thực; static content dùng được; late init không replay rèm/ẩn track | Warning một lần, timestamps, DOM/static/late-init video |
| T4 / LDR-05 | Disable JS rồi reload; riêng lượt khác fault-inject exception sau activation. Hủy transition hoặc tắt transition bằng DevTools để không có transitionend | No-JS không che trang từ đầu; partial failure/missing-event có bounded cleanup; không kẹt overlay/inert/scroll | Screenshot, fault point, final state, actual cleanup time |
| T5 / LDR-04 | Với fixture autoplay vốn bật, record slide index từ init đến release; giữ focus/hover hoặc ẩn tab; thử late init. Lặp loader off/theme khác trên staging nếu có | Không đổi slide dưới loader; release chỉ bỏ pause của loader; giữ pause khác; không-loader vẫn dùng policy gốc, không ép bật autoplay | Index/timestamps và pause-state notes; môi trường không có ghi Chờ kiểm chứng |
| T6 / LDR-03,06 | Bật reduced motion trước load; lần khác đổi preference giữa reveal; dùng Tab/Enter, click CTA sau release, screen reader smoke | Không rèm/fade khi reduce; nội dung truy cập được; không focus vào vùng bị che, không status spam, không lớp vô hình chặn click | OS/browser, video, focus/status notes |
| T7 / LDR-07 | Snapshot options; đổi Smooth/type/scope/frequency, save/reopen; kiểm disabled fields không POST | Control vô hiệu có help đúng; giá trị cũ show-after/fade/dismiss/min-display còn nguyên; UI English/i18n | Settings screenshots và options trước/sau, không chứa secrets |
| T8 / LDR-08 | Scope front: Post→Home; once/every: reload; Home↔Post Back/Forward/BFCache; bật/tắt page fade, legacy modes; restore options | Eligibility theo contract đã chốt, history không replay overlay treo, không chồng rèm/fade; navigation/form native; skeleton giữ baseline | URL/options, history video, regression screenshots, console |
| T9 / LDR-09,07 | Tại Loading screen trên staging (admin URL __), chọn branch mà controls enabled. Baseline nhập show-after 150, fade 175/250, page transition 180; xem validity.stepMismatch và thử Save. Sau patch lặp save/reopen, ArrowUp/Down; kiểm defaults 150/200/180. Đặt show-after 0 và thử branch Smooth hiện hữu còn dùng threshold | Baseline step=100 có step mismatch với các số trên; sau patch step=1, không stepMismatch với integer trong range, arrow ±1, save/reopen không đổi số. Show-after 0 không bị runtime thay thành 150. Homepage curtain branch disable threshold theo contract, không dùng branch đó để kết luận runtime zero PASS | Screenshot trước/sau + native validation; values/step/min/max/validity, sanitized options và inline config; timeline bootstrap/show-decision cho zero. Ghi browser/build/options |

T9 bổ sung biên: show-after 0/500, fade 100/500, page transition 100/400 hợp lệ. Giá trị ngoài range (-1/501, 99/501, 99/401 tương ứng) không được persist thành giá trị runtime ngoài range; fractional có step mismatch theo integer contract. Không yêu cầu 750 hợp lệ trong field duration cũ. Restore options sau test. Nếu browser chặn form baseline trước request thì ghi rõ không có POST; không quy lỗi cho database/sanitizer. Các checks threshold/fade cũ vẫn áp dụng ngoài homepage repair theo applicability đã chốt.

FAIL nếu static content không truy cập được, autoplay đổi slide dưới overlay, late init replay reveal, cleanup kẹt, geometry sai hoặc regression skeleton/settings/native navigation. Không FAIL chỉ vì log deadline warning khi cố tình chặn bundle; warning là expected trong T3. Đánh giá performance/glass repaint bằng trace thực, không suy ra từ việc chỉ dùng transform.

Mỗi LDR-01–09 gửi expected/actual/status, URL, build, options, timestamps, browser/device và evidence trong bảng. Giữ evidence UXHF-03/06/07/09/10 của navigation/legacy; không bỏ tests cũ vì thêm runbook. Chưa có evidence thì ghi Chưa làm hoặc Chờ kiểm chứng, không PASS.
