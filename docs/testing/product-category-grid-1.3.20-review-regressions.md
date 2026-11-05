# Product Category Grid — onsite regression tests sau source review

Ngày: 2026-11-05. Status: **Chờ kiểm chứng — chưa chạy online**.
Review target: commit `7ef45ff` + additive Category wiring của Sidebar1.3.19 trong working tree. Khi test bản sửa, ghi commit/diff/build mới; không mặc định findings vẫn tồn tại hoặc đã hết.
Acceptance: CG-UI-01–04, CG-UX-01–03, CG-TECH-01–05, DS-01–07.
Runbook nền: [product-category-grid-1.3.20.md](product-category-grid-1.3.20.md). Bộ này bổ sung R01–R12, không thay baseline cases.

## 1. Environment và thứ tự chạy

Điền trước khi chạy:

| Input | Giá trị |
|---|---|
| Test site/staging được phép thao tác | TBD |
| Editor URL và frontend URL cùng trang test | TBD / TBD |
| WP/Gutenberg/Woo, browser/OS, theme/plugin versions | TBD |
| Source commit + dirty diff, build/deploy artifact và thời điểm deploy | TBD |
| User roles/session A/session B | TBD |
| Store hide-out-of-stock yes/no; viewport/container widths | TBD |

Không suy URL từ trang Product Carousel hoặc dùng runtime local copy cũ làm bằng chứng. Human build/lint và audit artifact theo runbook nền trước test. Ghi actual build fingerprint, không chỉ metadata version. Lưu bản gốc thumbnail/description của fixtures để khôi phục sau kiểm.

Chạy trên trang/category fixtures riêng. Các test thay role/theme/Woo, tạo 500+ terms, race/conflict và mô phỏng lỗi database chỉ trên staging được phép. DevTools chỉ throttle/block request của chính fixture; không patch DB hoặc inject failure trên production. Chưa có khả năng tạo tình huống thì ghi **Chưa chạy**, không ghi PASS.

Thứ tự: T00 smoke → T01/T02/T04/T05/T08 (data/save feedback) → T03/T10 (controlled server races/failures) → T06/T07/T09/T11/T12 và baseline regression. Mỗi test bắt đầu từ fixture đã reload và base values ghi nhận; không chạy song song trên cùng term trừ test yêu cầu.

## 2. Cases

### T00 — Smoke và baseline

- Setup: category QA-A có published/catalog-visible products và thumbnail; QA-Empty rỗng; page có hai Category blocks cùng QA-A, mỗi block local style khác. Giữ một Product/Post Grid/Carousel page đối chứng.
- Steps: insert/reopen Category; chọn QA-A/QA-Empty; Save page; mở frontend; bật từng display toggle. Kiểm Network/Console và saved block attrs.
- Expected: frontend chỉ có QA-A, một link/card; defaults3/2/1 theo container; saved attrs không có entity snapshot. Các page đối chứng không đổi behavior. Woo unavailable chỉ thử trên staging theo runbook nền.
- PASS/evidence: paired screenshots, href/DOM, serialized attrs, console/network, build. Có lỗi fatal/invalid block thì dừng ca phụ thuộc và ghi FAIL.

### T01 / R01 — Gõ lại giá trị gốc trong khi Save đang pending

- Setup: persisted description `QA base A`; mở Details, bật Network Preserve log và Slow3G hoặc staging delay để request Save category đủ lâu.
- Steps: sửa thành `QA sent B` → Save category → khi request pending gõ lại `QA base A` → để response về → chờ refetch → đóng/mở panel → Save category lần nữa.
- Expected: sau response đầu, persisted là B nhưng draft vẫn A và **Unsaved changes**; refetch không thay A. Save lần hai mới ghi A; cuối cùng clean.
- FAIL: A mất, status clean trước lần save thứ hai, hoặc không thể lưu A. Evidence: video typing/status, request payload/revision/response và native Woo values sau từng save.

### T02 / R02 — Keep my draft không ghi đè field chưa chỉnh

- Setup: A/B là hai editor sessions cùng term, base description D0, thumbnail I0.
- Steps: A sửa description D-A, không đụng ảnh. B lưu description D-B và ảnh I-B. A Save → conflict → Keep my draft → Save lại → reload cả hai sessions.
- Expected: description thành D-A sau quyết định Keep, ảnh vẫn I-B. Retry payload không biến ảnh I0 chưa chỉnh thành thumbnailDirty. Có thể cần refetch để hiện I-B nhưng không được ghi đè nó.
- PASS/evidence: payload trước/sau conflict, persisted description/thumbnail, video lựa chọn Keep và ảnh hai sessions. Lặp biến thể A chỉ sửa ảnh, B đổi description.

### T03 / R03 — Hai writers cùng base chạy đồng thời

- Setup: staging có phương tiện đồng bộ hai requests tại điểm server đã đọc base nhưng chưa write; người thực hiện ghi cơ chế barrier/instrumentation. Hai tab click gần nhau chỉ là thử xác suất, không đủ chứng minh chống race.
- Steps: hai sessions load D0, sửa D-A/D-B, gửi Save cùng base; cho cả hai vượt điểm đọc rồi thả write; thu responses và persisted value.
- Expected: một writer thành công, writer còn lại conflict/review hoặc cơ chế serialize tương đương; không silent last-write-wins khi cả hai cùng báo saved.
- Evidence: timestamp/order ở server, payload/response hai requests và final data. Nếu chưa có barrier/tooling thì đánh dấu Chưa chạy; không thêm delay production để test.

### T04 / R04 — Outcome page và category tách biệt

- Setup: page có một local attribute dirty và Category details dirty. DevTools xác định chính xác page write endpoint và `/product-categories/details`.
- Steps: (a) block page write nhưng cho category success; (b) page success, category fail; (c) page chậm hơn category; (d) category chậm hơn page; (e) cả hai success. Reload để xác nhận dữ liệu thật sau mỗi case.
- Expected: không khẳng định Page saved khi page fail/pending; không khẳng định all saved khi term fail/pending. Page/terms đã success không bị rollback. Draft phần lỗi còn nguyên và retry chỉ phần đó.
- Evidence: HAR đã bỏ cookie/nonce/auth, video notices, page values và term values. Lặp autosave: không có term write; Save/Update/Publish kiểm riêng, không suy từ một trường hợp.

### T05 / R05 — Notices khi đóng panel và formatting warning

- Setup: Console/Network mở, dùng đúng native notices UI.
- Steps: Save category success; repeat với request blocked; gửi description có inline HTML hợp lệ và tag bị allowlist bỏ, ví dụ `<strong>QA bold</strong><div>QA removed wrapper</div>`; đóng Details trong lúc pending; lặp khi nhiều category có success/failure.
- Expected: success/error/warning nhìn thấy dù panel đóng; sanitization warning không bị mất trong success branch; draft/error không mất khi dismiss. Thông báo tổng và Review/Retry dẫn được tới item lỗi theo contract.
- Evidence: screenshots notices, stored text và raw response có `notice`; ghi notice context/consumer nếu cần debug. Không chỉ thấy record trong data store rồi coi UI PASS.

### T06 / R06 — Limits render và batch save

- Setup: staging61 category có products; tên có số thứ tự QA-001…061. Mỗi term lưu base values; không dùng production catalog thật.
- Steps: chọn cả61 qua picker, Save/reopen page, đếm saved selection/editor/frontend và vị trí cuối. Sửa description của51 terms trong cùng phiên → explicit Page Save → xem mọi batch response → reload/native values.
- Expected: không âm thầm mất card sau60; nếu có limit đã được human duyệt thì phải chặn/giải thích trước Apply. Tất cả51 dirty terms được xử lý hoặc hiển thị lỗi rõ; không báo đã lưu đầy đủ khi server chỉ nhận50.
- Evidence: selected IDs, frontend card count, toàn bộ batch payload/results, bảng term→persisted/dirty/status. Thử Retry chỉ các items chưa thành công.

### T07 / R07 — Load more cũ không làm bẩn search mới

- Setup: query A có hơn50 kết quả, query B có tên phân biệt rõ; network delay.
- Steps: A → Load more → trong lúc pending đổi search B → chờ B xuất hiện rồi cho A trả về; lặp với filter Only categories with products và dialog đóng/mở.
- Expected: list/total/hasMore chỉ thuộc query hiện tại; response A không append vào B hoặc đổi loading/page state của B. Apply giữ đúng selected IDs/order.
- Evidence: video, request timestamps/query params và danh sách IDs cuối. Nếu chỉ throttle không tạo đúng thứ tự, ghi test chưa tái hiện thay vì PASS race.

### T08 / R08 — Preview dùng shared draft

- Setup: hai blocks cùng term; bật description/image. Persisted D0/I0.
- Steps: đổi description D1 và ảnh I1 ở block A, chưa Save; quan sát cả hai cards; đổi local style A; Discard; lặp chỉnh lại rồi Save.
- Expected: preview cả hai phản ánh shared draft D1/I1 và có indication chưa lưu; local style chỉ đổi A. Frontend/new tab vẫn D0/I0 trước save. Discard trả về D0/I0; Save thành công mới đổi frontend.
- Evidence: paired editor/frontend screenshots trước/sau mỗi action; persisted values và requests chứng minh không autosave blur/close.

### T09 / R09 — Count workload trên catalog lớn

- Setup: staging dataset có kích thước được ghi rõ, parent với nhiều descendants và nhiều selected roots. Dùng profiler/server metrics sẵn có; ghi PHP memory limit và cold/warm cache. Không tự nhân dữ liệu production.
- Steps: render frontend parent; mở picker mặc định rồi Only with products; thử1/10/60 selected roots; lặp request với counts overlapping. Ghi elapsed/query count/peak memory/response size.
- Expected: không timeout/OOM; số liệu phù hợp performance budget được thống nhất cho môi trường. Count giữ đúng publish/visibility/stock/dedup, không “tối ưu” bằng cắt product list.
- Evidence: bảng dataset→metrics và fixture expected counts. Chưa có budget không kết luận performance PASS chỉ từ cảm giác nhanh; ghi measured + Chờ đánh giá.

### T10 / R10 — Thumbnail write failure không bị báo saved

- Setup: staging cho phép fault injection đúng meta update/delete của fixture; không dùng lỗi network làm thay thế lỗi DB/server write. Persisted D0/I0.
- Steps: sửa D1/I1, ép thumbnail write failure nhưng description write được; Save; kiểm response/native values/draft; bỏ injection và Retry. Lặp remove-thumbnail failure.
- Expected: D1 được ghi nhận riêng; thumbnail không đạt I1 phải có item/field error, draft ảnh giữ để retry. Không clean thumbnail về I0 rồi báo success. Retry không làm mất description đã lưu.
- Evidence: server fault configuration/log, results fields/status, persisted meta và video dirty state. Gỡ injection sau kiểm.

### T11 / R11 — Picker filtered scan trên500 terms

- Setup: staging hơn500 terms alphabetically ordered; category có products ở vị trí sau500. Có fixture cùng tên/parent để test search paths.
- Steps: bật Only with products, Load more tới hết; tìm category cuối bằng search riêng; so total/hasMore và kết quả với inventory fixture.
- Expected: không trình kết quả giới hạn như toàn bộ catalog; vẫn có đường tìm/chọn category hợp lệ. Nếu có bounded scan, UI giải thích phạm vi và cách tìm tiếp.
- Evidence: dataset/order, responses total/hasMore, video tìm/chọn. Số500 không phải product limit đã được human duyệt.

### T12 / R12 — Reorder keyboard/touch và accessible names

- Setup:3 terms có tên phân biệt; keyboard + screen reader, desktop fine pointer và touch/iPad thật nếu có.
- Steps: focus handle item giữa, ArrowUp/Down tới đầu/cuối; Move buttons trên touch; drag/drop/cancel; kiểm focus sau reorder/remove. Bật/tắt Show name/image/CTA, kiểm tên truy cập card link.
- Expected: đúng một ordered list; announce tên/vị trí mới; focus không rơi body hoặc control disabled không thể tiếp tục. Card links luôn có accessible name có nghĩa kể cả khi tắt tên/ảnh; một tab stop/card, focus ring rõ. Hybrid pointer không mất phương án reorder.
- Evidence: video + screen-reader notes + activeElement/accessibility tree + serialized IDs. Chỉ emulation không thay được evidence iPad thật; ghi giới hạn.

## 3. Record kết quả

Mỗi test ghi:

```text
Test ID / acceptance IDs:
Date / executor:
URLs / environment / build / source:
Fixture / role / browser / viewport / container:
Setup + exact steps thực tế:
Expected:
Actual:
Status: PASS | FAIL | Chưa chạy | Chờ kiểm chứng
Evidence: screenshot/video/DOM/requests/server logs/persisted values
Giới hạn / cleanup / follow-up:
```

Không đưa cookies, nonces, passwords hoặc customer data vào evidence. Case được thực hiện khác setup phải ghi khác biệt. PASS theo environment/build, không dùng một case để tick toàn bộ CG-*; source review finding được đối chiếu lại sau fix.

## 4. Test riêng cho1.3.19

Agent review1.3.19 được giao riêng ngày2026-11-05, phạm vi source-only. Giữ runbook [plugin-design-sidebar-1.3.19.md](plugin-design-sidebar-1.3.19.md) cho DS/TD và Carousel carry-over. Sau findings của agent, bổ sung cases cụ thể theo ID review; chưa tự ghi review xong hoặc runtime PASS.
