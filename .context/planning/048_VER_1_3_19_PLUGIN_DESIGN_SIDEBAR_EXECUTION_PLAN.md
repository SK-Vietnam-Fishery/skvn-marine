# V1 / 1.3.19 — Plugin Design Sidebar Foundation: execution plan

Ngày: 2026-11-04. **PLANNED; chưa start.** Current vẫn 1.3.18. Version 1.3.19 đã được human xác nhận khi đổi thứ tự Sidebar → Category 1.3.20 → Hero 1.3.21; không hỏi lại SemVer. Tài liệu này là kế hoạch công việc, không phải approval runtime hoặc lệnh chuyển mốc.

**Execution override 2026-11-04:** human yêu cầu “cho agent sol bám sát kế hoạch để thực hiện coding, chuyển giao các thứ cần thiết”. Đã start **V1 / 1.3.19**, AGENTS/MILESTONES đồng bộ và checkpoint 1.3.18 archive **DEFERRED_WITH_CARRY_OVER**; không toàn bộ PASS. Q1=A/Q2=A triển khai, metadata development 1.3.19, không release approval. Các câu chưa-start trong snapshot/memo bên dưới là lịch sử trước lệnh này.

## Execution ledger — 2026-11-04

- **P0 — Đã có source audit:** baseline `edae8f3813b0200ad39b686c1bc1d4a3e43435be`, dirty tree preserved; [inventory](../../docs/testing/plugin-design-sidebar-1.3.19-inventory.md). Không có WP core/node_modules local, onsite WP/build/URL cần human điền. CTA hover/fonts/chips/media đã có trước task, không tạo lại schema. Không có resolved tension mang scope 1.3.18 để archive; future/older decisions giữ tại ACTIVE, không tự đổi trạng thái lịch sử.
- **P1 — Artifact có source, chờ human review:** [scope/states prototype](../../docs/artifacts/editor-ui/plugin-design-sidebar-1.3.19.html), Inspector → Action → Plugin/save, empty/unsupported/multi/narrow/permission/error. Chưa render/visual approve. Media bounds/defaults giữ số baseline, chưa số production được duyệt; đặc biệt padding default8rem/shadow rem cần fixture biên. Post visual giữ gate riêng, không tự duyệt Product styling cho Post.
- **P2/P3 — Đã có source, chưa runtime PASS:** public edit-post PluginSidebar; session UI tab/group, selection/delete-event guards, shortcut/focus/back, component reset; Image/Title/Details/Action chuyển khỏi Inspector, query/fields/actions/basic layout giữ nguyên. Native preview được ghi rõ giới hạn, không endpoint query mới; thêm error/unavailable, container query list3/2/1 +9/6/3 expand và observer cleanup. Source defects được nối: resolveFontSize import, grouped Details preview inheritance, author/media-off Post, consumed chip palette, Post hover text, plugin font/palette fallback và focus hover colors.
- **G-MEDIA compatibility:** missing/empty key giữ legacy ratio/cover, explicit flush/padded/floating opt-in; new Product Carousel variation chọn flush, Grid cũ không migrate. PHP media class trước đây bị array assignment ghi đè; đã đặt class tại final owner. Flush explicit square/contain. Tuning ranges/defaults chưa đổi. Đây là source correction, E12a/E13a/G-MEDIA vẫn cần paired old/new evidence và human approval; không khẳng định flush explicit giống legacy cover.
- **P4 — Đã có source, chưa runtime PASS:** adapter authenticated AJAX ngay module typography hiện có; exact child stylesheet + theme contract v1 + function/capability gate; palette/heading dùng manage_options, fonts theo Customizer mặc định edit_theme_options. Strict shape/allowlist + sanitizer; group revision chống stale sequential saves, merge requested group, read-back persistence check. Đây là optimistic conflict check, không phải transaction liên-owner/atomic multi-user save; concurrent race cần TD-CONFLICT evidence. Draft trong editor session, Page Save không flush global, Save/Discard riêng3nhóm, error giữ draft, beforeunload warning, không preview draft. Theme font registry/loader/storage owners giữ nguyên; no PHP include mới.
- **P5/P6 — Chờ human:** [onsite runbook](../../docs/testing/plugin-design-sidebar-1.3.19.md), build/lint, artifact/source parity, exact WP APIs, DS/TD + toàn bộ carry register. E02 giữ PASS cũ. C/D/arrows/Post/G-MEDIA chưa được tự PASS. Skeleton không sửa. Loading/navigation046 vẫn 1.3.18 với source/test/debt riêng; không nhập acceptance vào Sidebar.

Không commit/push/deploy, không thao tác production, không tự build/lint plugin. Completion vẫn cần onsite evidence và human approve; coding source không đóng milestone.

Static self-check:16 block JSON source/build valid và version1.3.19, relative imports resolve/no replacement chars, diff whitespace clean với CRLF được giữ; layout audit chỉ overflow-x cũ, không viewport bleed mới. Không thay evidence compile/PHP/runtime. Padded/default numeric approval, Post visual, native query exact parity và toàn bộ runtime IDs vẫn mở.

## 1. Mục tiêu và authority

Người biên tập dựng Product/Post Collection bằng Inspector ngắn gọn, mở thiết kế chi tiết đúng block/thành phần trong sidebar riêng, lưu và quay lại mà không mất dữ liệu hoặc nhầm cấu hình toàn site. Hoàn tất/kiểm chứng phần Carousel còn lại theo carry-over 1.3.18; sidebar không thay thế nghiệm thu runtime.

Nguồn: `docs/workflows/ideation-plugin-design-sidebar.md` (quyết định shell và Theme Design); `MILESTONES.md` §1.3.19 (scope/carry register); plan 045 §9–13 và runbook `docs/testing/carousel-editor-ux-technical-1.3.18.md` (acceptance cũ); `docs/decisions/typography-scope-and-font-loading.md` (hai lớp typography); plan 047 (identity và plugin independence).

Đã chốt, không cần quyết lại:

- Selected block / Plugin; toolbar/menu entry, shortcut từ block, back navigation. Product/Post là consumers đầu tiên, Grid phải giữ tương thích.
- Inspector giữ Content/Query/Fields/Actions/Layout cơ bản; sidebar chỉnh thiết kế theo thành phần. Không có hai controls ghi cùng setting ở hai surface.
- Block attrs dùng Page Save/Undo; Theme Design lưu riêng. Plugin sở hữu UI/validation, theme sở hữu CSS/token/font delivery; theme khác không có integration nhưng block vẫn dùng được.
- Theme Design gồm Typography, Fonts, Brand colors, Save & scope theo capability thật. Defaults cho block mới và linked global styles nằm ngoài scope.
- Category adapter dùng ở 1.3.20; Slider/Step Slider/2-side không triển khai trong 1.3.19. Không thêm full-FSE styling system, font loader, raw CSS, query owner hay engine mới.
- Giữ prefix/namespace/text domain hiện hành đến migration 1.4.0. Tên hiển thị đề xuất **Marine Blocks**, tabs **Selected block / Plugin**, subgroup **Theme Design — SKVN Marine**, phù hợp identity đã chốt; không rename dữ liệu để đặt nhãn UI.
- Không sửa skeleton/full-page skeleton. Gói D chỉ test/ghi debt. Loading/navigation 046 vẫn thuộc 1.3.18.

## 2. Baseline source và phần chưa biết

Audit đọc source ngày 2026-11-04, working tree đang có thay đổi từ nhiều công việc; không có runtime PASS mới:

| Evidence | Nhận xét và hệ quả |
|---|---|
| `src/collection/controls.tsx:106,140–791` trong plugin | CollectionEdit gom Inspector và preview; các nhóm Content/Query/Card/Actions/Layout/Style/Advanced đã tồn tại. Cần tách control components khỏi shell, không viết lại query/render để di chuyển UI. |
| Cùng file `:621–757`, `:1349–1363` | Đã có typography/CTA hover/chip controls trong source hiện tại. Report E07 thiếu hover và E10 chip không đổi là evidence cũ, cần tái hiện trên build mới; không kết luận vẫn thiếu source, không tự kết luận đã sửa runtime. |
| `src/product-collection/edit.tsx`, `src/post-collection/edit.tsx` | Hai entry cùng dùng collection implementation; adapter phải giữ type/layout context thay vì nhân đôi component. |
| `modules/typography-settings/typography-settings.php:13,29,63,137,156` | Option `skvn_typography`, palette 4 slots, H1–H4 size/weight, sanitize và admin quyền `manage_options` đã có. Registration hiện tại không khai báo REST schema/show_in_rest; không thể coi editor đã có đường save. |
| Theme `inc/customizer.php:25,78,90,107,169,179,229,249` | Font preset và heading scope nằm ở theme_mod, có registry/sanitize/delivery riêng. Không ghi font vào option typography hoặc copy danh sách preset sang plugin. |
| `docs/decisions/typography-scope-and-font-loading.md` §2–3 | CSS chỉ frontend/editor canvas, không nhuộm font/màu wp-admin/sidebar. Theme Design không đồng nghĩa mọi element đều kế thừa. |
| `src/core-controls/block-clipboard/index.tsx:148` | Có registerPlugin utility; tìm kiếm hiện tại chưa thấy PluginSidebar trong src. Shell mới chưa được triển khai. |
| `package.json` | @wordpress/scripts ^30.6.0, Swiper ^11.1.15; package version còn 1.3.17. Không suy phiên bản WordPress onsite hoặc version @wordpress/editor từ build tooling. Audit lock/runtime trước chọn API. |

P0 phải ghi baseline commit + dirty diff, artifact đã deploy và WP/Gutenberg version; lập control inventory từng key với label/type/default, điều kiện hiện, owner, preview/PHP consumer, legacy/reset và test ID. Những dòng lịch sử trong 045 ghi “Chưa làm” không thay cho audit source hiện tại; giữ evidence lịch sử và bổ sung kết quả mới có ngày/build.

## 3. Mapping controls để triển khai

Đây là mapping đề xuất cụ thể dựa trên ideation đã duyệt; P0 bổ sung inventory từng key, không tự thêm control chỉ vì có chỗ trống.

| Surface / nhóm | Controls và states | Save/owner |
|---|---|---|
| Inspector / Content | Eyebrow/heading; query/order/taxonomy/count; fields visibility; archive/catalog labels/URLs | Attrs; giữ query owner Product/Post |
| Inspector / Actions | Action mode, URL, quote context, read-more label | Attrs; không lẫn màu nút |
| Inspector / Layout & preset | Grid/Carousel, responsive preset, width, equal height, card/media preset, arrows/pagination; Post autoplay/delay; show image | Attrs; Product không autoplay; navigation chỉ Carousel |
| Inspector / Advanced | Accessible label và options có consumer thực | Không panel rỗng; legacy data không xóa khi ẩn controls |
| Selected block / Image | Tinh chỉnh preset: floating width/offset/shadow X/Y/blur/opacity; padded padding; contextual help/reset | Attrs hiện có; media preset cha ở Inspector; nhóm chỉ hiện đúng Product/preset/showImage |
| Selected block / Title | Font role/size/color, reset; giữ anchor và full title | Attrs; không ảnh hưởng section heading |
| Selected block / Details | Font/size/color, chip style/scheme nếu có consumer | Attrs; fields on/off vẫn ở Inspector |
| Selected block / Action | Font/size, normal/hover background/text, reset theo nhóm | Attrs; Post link không có background consumer thì không hiện option đó; footer CTA riêng |
| Plugin / Theme Design | Palette/heading scale hiện có; Fonts theo quyết định Q1; scope/help/save/status | Settings riêng; gate theme + capability server |

Selection không lưu clientId vào nội dung. Chọn block mới cập nhật target; đang ở Plugin tab không tự nhảy về Selected block. Xóa/undo/duplicate/no selection/unsupported/multi-selection có state rõ; không bulk edit. Chuyển tab không fetch query hoặc mount carousel. Shortcut đặt focus vào nhóm có nhãn; Back trả về đúng block settings. Category chỉ nhận interface tối thiểu khi consumer tồn tại, không làm framework đăng ký mọi block từ trước.

## 4. Các gói thực hiện và điểm dừng

| Gói | Công việc / file dự kiến | Điều kiện hoàn tất trước gói kế |
|---|---|---|
| P0 — Baseline và inventory | Audit source/build; inventory controls; đối chiếu 045 và E01–E15; ghi token/legacy truth table; nhắc chạy deferred tests khi 1.3.19 current | Biết case nào đã source, còn lỗi hay chỉ thiếu test; không viết lại phần đã chạy đúng |
| P1 — UX artifact và contract | Artifact trong `docs/artifacts/editor-ui/`: Inspector → shortcut → selected group → Plugin/save; empty/unsupported/multi/narrow/error states. Chốt Q1/Q2; xác minh API theo lock + WP onsite | Human kiểm ba tác vụ: đổi query, đổi CTA style, đổi brand color; target/scope/save dễ hiểu. Artifact không phải runtime PASS |
| P2 — Shell và một lát cắt | Dự kiến `src/design-sidebar/` cho shell/selection; import từ `src/index.ts`; collection adapter và nhóm Action dùng attrs hiện tại | DS-01/02/04: hai blocks khác nhau; shortcut, selection/delete/undo/reopen không ghi nhầm; màu Action đi hết control→frontend |
| P3 — Các nhóm design và preview | Tách groups khỏi `src/collection/controls.tsx`; Title → Details → Image; reuse schema/types/preview. Sửa gaps xác nhận được theo 045 S1–S6 | Mỗi nhóm có reset/conditional controls và E06–E14; old/new Product/Post/Grid bảo toàn. Chỉ thay schema nếu acceptance thực cần và có compatibility table |
| P4 — Theme Design | Transport/gate/settings UI; tái dùng typography sanitizer và font registry/delivery theme. Audit `inc/typography.php`, `inc/customizer.php`, theme.json, CSS consumers | DS-05 và TD-01–04: đúng quyền, save/error/draft, theme mismatch, editor/frontend output; không thêm font loader/store |
| P5 — Carousel runtime và QA chung | TECH-01–13, C/D/arrows/Post; state owner, query, links/security, keyboard/motion, counts/resize, cleanup/assets | Evidence theo ID trên build xác định; test-only debt không bị chuyển thành PASS. Skeleton FAIL ghi debt, sửa cần scope riêng |
| P6 — Handoff | Runbook/evidence, human build/lint và artifact audit nếu đổi PHP include; regression admin settings/Customizer; cập nhật ledger | Không còn FAIL trong acceptance bắt buộc hoặc human quyết defer rõ ID/đích; human onsite approve trước completion |

P2–P4 làm theo lát cắt, không di chuyển toàn bộ Inspector trong một lần. P5 có baseline từ P0 và kiểm lại phần bị ảnh hưởng sau mỗi lát cắt; không đợi cuối mới phát hiện runtime regression. Refactor UI và sửa behavior ghi tách rõ trong diff/commit để review và rollback.

Transport và API là việc agent phải xác minh, không đẩy thành câu hỏi sản phẩm: dùng extension point public tương thích WordPress thực tế; xác định editor selection/update/undo APIs, permission checks và settings endpoint trước implementation. Không dùng DOM injection vào chrome. Không khẳng định API khả dụng dựa trên tài liệu latest chưa đối chiếu version. Không thêm dependency mặc định.

## 5. Acceptance và evidence

DS-01–07 giữ nguyên nội dung ở ideation §6; chưa có source sidebar hoặc runtime evidence. Bảng dưới map nơi thực hiện, không thay hoặc rút gọn acceptance gốc.

| ID / Nhóm | Hành vi mong đợi | Hiện trạng | Việc cần sửa | Cách kiểm | Evidence |
|---|---|---|---|---|---|
| DS-01/02 — Editor UX | Entry/target/tab/empty states đúng | Chưa làm | P1/P2 selection shell | Hai blocks; chọn/xóa/duplicate/multi; Plugin tab giữ nguyên | Video target header/focus |
| DS-03 — Editor UX | Controls dễ tìm, keyboard/narrow dùng được | Chưa làm | P1/P3 grouping | Query → CTA → Floating; sidebar hẹp | Artifact, số thao tác, video editor thật |
| DS-04 — Technical | Attrs/Undo/Save đúng block | Chưa làm | P2/P3 adapter | Save/reopen, undo/redo, reset, duplicate | Serialized attrs trước/sau |
| DS-05 — Technical | Global save riêng, đúng quyền/error | Chưa làm | P4 | Role đủ/thiếu quyền; failed save; draft tab switch | Saved values, response và notices |
| DS-06 — Block UI | Output bảo toàn khi chuyển control | Chưa làm | P3 compatibility | Product/Post/Grid old/new; title dài/thiếu ảnh/mobile | Paired editor/frontend, computed/rect |
| DS-07 — Technical | Không fetch/listener dư | Chưa làm | P2–P5 lifecycle | 10 style edits/tab switches/mount-remove so baseline | Network/console/cleanup trace |
| TD-01 — Technical | Theme gate đúng child theme/capabilities; đổi theme không mất data | Chưa làm | P4 handshake + server gate | SKVN compatible/incompatible, GP parent, theme khác, đổi quay lại trên test env | Theme/build IDs, options, ảnh states |
| TD-02 — Editor UX | Phân biệt local/page save với site save; giữ draft khi lỗi | Chưa làm | P4 theo Q2 | Draft→switch tab/close/reopen→save fail/success/discard | Video + values persisted/draft |
| TD-03 — Block UI | Tokens đúng nơi kế thừa, override độc lập; admin không đổi style | Chờ kiểm chứng baseline; sidebar Chưa làm | P4 mapping | Inherited/override, max heading size, long text/mobile | Paired screenshots/computed styles |
| TD-04 — Technical | Font choices/delivery tái dùng owner; fallback/network đúng | Chưa làm cho sidebar | P4 theo Q1 | Chọn preset/reopen, font request lỗi, theme switch test | theme_mod, network và computed font |

Carry-over bắt buộc: UX-01–13 (kể cả UX-02a/IDs con), UI-18-TITLE/STYLE, TECH-01–13 (kể cả TECH-08a/IDs con), G-MEDIA, C, D-01–08, arrows, Post visual. Mapping chi tiết E01–E15/E12a/E13a giữ tại 045 §11 và runbook 1.3.18; không đổi ID để tạo cảm giác hoàn thành. E02 giữ PASS cũ; chỉ retest nếu thay layout liên quan. E07/E10 cần tái hiện vì source đã khác report cũ. DS không thay cho technical acceptance này.

Onsite runbook: trước P2 tạo `docs/testing/plugin-design-sidebar-1.3.19.md`, link runbook Carousel hiện có. URL Product đang chỉ biết tên “Test product carousel”, Post URL chưa biết; human/executor điền editor/frontend URL và test environment trước chạy. Fixture: hai blocks style khác, Product/Post, Grid/Carousel, old/new saved content, long/missing media, inherited/override. Ghi WP/theme/plugin/build/commit/dirty diff, browser, viewport/container, expected/actual và ảnh/video/attrs/network. Chỉ test đổi theme/Woo off/error injection ở môi trường được phép; không tự thao tác production.

## 6. Decision register — human duyệt 2026-11-04

Human xác nhận “đồng ý với recommend”: **Q1 = A, Q2 = A**. Fonts chỉnh trực tiếp trong sidebar bằng preset/heading scope và theme_mod hiện có; không thêm font loader. Theme Design dùng draft và Save riêng theo nhóm, chỉ áp dụng giá trị đã lưu; giữ draft khi chuyển tab/đóng sidebar hoặc save lỗi, Discard theo nhóm, Page Save không ghi settings chung. Chưa làm live preview draft trong foundation. Đây là approval hướng triển khai; chưa phải lệnh start 1.3.19 hoặc runtime PASS. Các memo bên dưới giữ rationale và phương án đã cân nhắc; không hỏi lại Q1/Q2.

### Q1 — Fonts trong Theme Design: APPROVED A

1. **Vấn đề:** font hiện lưu theme_mod qua Customizer, trong khi sidebar được yêu cầu có nhóm Fonts; cần chọn mức tích hợp ở 1.3.19.
2. **Phương án:** A — chọn các preset/heading scope hiện có ngay trong sidebar, ghi đúng theme_mod qua adapter có quyền và theme gate: luồng liền mạch, thêm transport/QA. B — hiển thị cấu hình font và link Customizer: ít code nhưng rời editor, chỉ hoàn thành Fonts ở mức điều hướng; cần ghi rõ phần chỉnh trực tiếp còn deferred.
3. **Khuyến nghị:** A, giới hạn đúng registry hiện có. Phù hợp yêu cầu Theme Design đã chốt, không mở font delivery 1.6.0. Palette/heading và fonts có save/status theo nhóm riêng, không giả một transaction atomic cho hai storage owners.
4. **Bằng chứng:** theme `inc/customizer.php:78–92,107–180`; plugin typography settings `:13,29–43`; contract typography §2 phân biệt option và theme_mod.
5. **Tự kiểm chứng ≤5 phút:** mở contract §2; so Typography admin với Appearance → Customize → Typography và hai getter trong source; thấy hai nơi lưu khác nhau.
6. **Đảo ngược:** UI hai chiều, không migrate data nếu A tái dùng đúng keys; bỏ adapter vẫn dùng Customizer được.
7. **Quyết định:** human duyệt A ngày 2026-11-04; khi milestone được start, P4 triển khai adapter theo owner/capability hiện có. B không được dùng thay acceptance chỉnh font trực tiếp.

### Q2 — Global draft và thời điểm áp dụng: APPROVED A

1. **Vấn đề:** chưa chốt thay đổi Theme Design có preview tức thì trong canvas hay chỉ sau Save riêng.
2. **Phương án:** A — form draft, Save riêng theo nhóm, chỉ giá trị đã lưu mới được áp dụng; sau save thông báo reload canvas/page nếu pipeline hiện có cần: đơn giản và scope rõ, feedback chậm hơn. B — live preview draft chỉ trong canvas, Save/Discard và revert khi rời editor: dễ chọn màu/font nhưng phải xây preview rollback, scope isolation và xử lý hai owners.
3. **Khuyến nghị:** A cho foundation. Giữ draft trong phiên editor khi đổi tab/đóng sidebar; save lỗi giữ draft; Discard chỉ reset nhóm đó; Page Save không ghi settings chung. Rời trang khi còn draft phải có cảnh báo phù hợp, không âm thầm mất hoặc auto-save. Không hứa preview tức thì nếu pipeline chưa có.
4. **Bằng chứng:** ideation §4 yêu cầu save riêng và chưa chốt unsaved policy; typography contract §2–3 cho thấy CSS/font delivery do theme quản lý, khác block attrs.
5. **Tự kiểm chứng ≤5 phút:** đọc hai section trên; đối chiếu theme `inc/typography.php` và `inc/customizer.php` đường cấp CSS với option/theme_mod. So luồng Page Save và Save typography hiện có.
6. **Đảo ngược:** hai chiều; có thể thêm live preview sau mà giữ storage keys và save API.
7. **Quyết định:** human duyệt A ngày 2026-11-04; P1 thể hiện flow draft/save đã chốt, P4 triển khai, TD-02 vẫn cần runtime evidence. Live preview draft nằm ngoài foundation.

### Các việc chưa rõ nhưng chưa cần human quyết kiến trúc

- **Quyền:** đề xuất giữ quyền hiện có: chỉnh block theo quyền sửa nội dung; palette/heading theo `manage_options`; fonts đối chiếu capability thực của Customizer khi audit. Không mở quyền thiết kế toàn site cho editor/marketing mặc định. Nếu muốn nới quyền thì mới cần quyết định sản phẩm riêng; server luôn kiểm.
- **Post visual:** acceptance còn thiếu approval riêng; trước P3 agent chuẩn bị cặp ảnh hiện trạng + đề xuất trên fixture Post. Không áp Product square/Floating cho Post hoặc hỏi chọn style khi chưa có artifact.
- **Media ranges/defaults:** 045 S6.1 đã chốt loại controls nhưng chưa duyệt con số production. Agent đo source/artifact/responsive, trình bảng min/max/step/default và ảnh các biên trong P1/P3; không bắt human đoán số ngay bây giờ.
- **Query exact parity:** audit trước; giữ native + công khai giới hạn theo 045, không mở endpoint chỉ để xây sidebar. Nếu endpoint mới thực sự cần, trình evidence/memo riêng.
- **Token independence:** audit theme token consumers và plugin fallbacks, đặc biệt font role/UI và màu chip. Sửa fallback scoped trong acceptance; không biến thành đổi token/prefix toàn repo trước 1.4.0.
- **Lệnh start:** chưa có. Sau khi human yêu cầu bắt đầu, thực hiện transition theo AGENTS, bảo toàn carry-over và xử lý trạng thái loading/navigation 046 đúng evidence; không đóng 1.3.18 thành toàn bộ PASS chỉ vì Carousel dùng được.

## 7. Rollback và completion

Giữ schema/storage owners để trả control về Inspector được; shell có thể gỡ mà nội dung vẫn render. Không xóa legacy keys hoặc dữ liệu theme khi tắt integration. Transport global phải kiểm input/nonce/capability/server theme gate, xử lý lỗi và tránh ghi đè nhóm khác bằng draft cũ; chi tiết concurrency được agent đặc tả khi audit P4.

Completion gồm DS-01–07, TD-01–04 và toàn bộ carry register có evidence hoặc quyết định defer cụ thể được human chấp thuận; không coi chỉ hoàn thành shell là xong milestone. Skeleton giữ ngoại lệ không sửa và ghi debt minh bạch. Human chạy build/lint theo AGENTS sau code; nếu PHP includes đổi thì audit deploy artifact. Lượt lập plan này chỉ sửa Markdown, không build/lint, không bump/version/start/commit.
