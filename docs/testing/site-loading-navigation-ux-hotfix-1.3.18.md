# Onsite runbook — UX hotfix loading/navigation 1.3.18

2026-11-03. **APPROVED — plan 046 đã duyệt, controls mới: frequency (once/every), show loading after (0–500ms, default 150), loading fade duration (100–500ms, default 200ms), page transition (none/fade, default 180ms, range 100–400ms).** Build hotfix chưa có.

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
