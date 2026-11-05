# V1 / 1.3.19 — Kế hoạch xử lý review độc lập lần hai

Ngày: 2026-11-05. **APPROVED — human duyệt A/G0–G4 trong chat ngày2026-11-05.** Các phần đề xuất/memo giữ rationale trước approval; execution record§10 là trạng thái triển khai. Chưa runtime/release approval.

Baseline: commit `e13fa2c` — `feat(blocks): implement 1.3.19 design sidebar and repair review findings`. Current vẫn **V1 / 1.3.19 — Plugin Design Sidebar Foundation**. Plan048 giữ scope/acceptance gốc; plan050 giữ lịch sử repair đã triển khai. File051 là checklist thực hiện sau khi được duyệt, không thay nghiệm thu DS/TD hoặc Carousel carry-over.

## 1. Kết luận review được dùng làm đầu vào

| Finding | Kết luận kiểm tra | Xử lý trong đợt này |
|---|---|---|
| R19-01 — editor scope | Shell/shortcut gọi `core/edit-post` trực tiếp, không guard; lỗi thực tế ngoài post editor chưa được tái hiện | G1: giới hạn shell theo context có kiểm chứng; có đường chỉnh design dự phòng ngoài context đó |
| R19-02 — AJAX thiếu sanitizer | **Bác bỏ tiền đề:** admin-ajax chạy admin_init; registration hiện tại có hiệu lực trên đường AJAX | Không chuyển register_setting sang init, không thêm sanitize lần hai theo finding này; sửa claim “any source” quá rộng trong comment nếu cần |
| R19-03 — session/nonce | **Lỗi source xác nhận:** 403 bị suy thành mất quyền; nonce cũ không tự phục hồi. Body `-1` là JSON number hợp lệ, không phải luôn lỗi parse | G2: phân loại lỗi, phục hồi nonce có kiểm quyền, giữ draft/base và đường xuất draft trước reload |
| R19-04 — lock leak | exit bỏ qua finally; mysqli persistent mặc định có cleanup GET_LOCK khi tái sử dụng. Chưa có bằng chứng kẹt lock trên môi trường onsite | G4: bổ sung điều kiện/kiểm chứng; chưa thêm shutdown handler hoặc thay lock protocol |
| R19-05 — eager read | useThemeDesign đọc ngay khi shell mount dù chưa mở Plugin tab | G3: tải lần đầu khi thực sự mở Plugin tab, giữ model mounted để giữ draft |
| R19-06 — Post hover background | Selector CSS dư, Post không phát class; không phải control hỏng đang được expose | Giữ scope Post hiện có, chưa mở control hoặc sửa CSS chỉ để cleanup |
| R19-07 — cleanup/ledger | Guard is_array trùng; exists chưa dùng; ledger cần phân biệt mốc kiểm lịch sử và snapshot hiện hành | G4: cleanup nhỏ, cập nhật ledger đúng baseline và giới hạn build |

Source đã đối chiếu: `src/design-sidebar/{index,navigation,theme-design}.tsx`, `modules/typography-settings/typography-settings.php`, `src/collection/{controls.tsx,style.css}`, `modules/collection-render/cards.php` trong plugin. Review này chưa chứng minh runtime PASS.

Nguồn kiểm tiền đề: [WordPress admin_init](https://developer.wordpress.org/reference/hooks/admin_init/), local core `D:/Github/minhhaifish/wp-admin/admin-ajax.php:45`; [PHP exit](https://www.php.net/manual/en/function.exit.php); [mysqli persistent cleanup](https://www.php.net/manual/en/mysqli.persistconns.php). Local core là evidence đối chiếu, không đại diện version onsite. Trước implementation phải đối chiếu APIs với WP thực tế và assets dependency; không suy version WP từ @wordpress/scripts.

## 2. G0 — Chốt baseline và kiểm API trước sửa

- Kiểm HEAD/diff; giữ nguyên Category1.3.20, build assets và các thay đổi không liên quan đang có trong working tree.
- Xác minh context post editor, template editing bên trong post editor và Site Editor; xác định availability của store/PluginSidebar bằng source core đúng version, không dựa riêng vào getCurrentPostType hoặc sự tồn tại của store.
- Kiểm Button forwardRef trong components runtime mục tiêu. Local WP components đã có forwardRef; không báo thiếu ref forwarding như một defect đã xác nhận. Onsite focus vẫn phải test.
- Ghi API/version source và kết luận vào execution record cuối file này. Nếu môi trường chưa đủ, ghi Chờ kiểm chứng; không tự dựng bằng chứng onsite.

## 3. G1 — Shell đúng context, không mất đường chỉnh design

**Hướng đề xuất để duyệt:** foundation dùng shell riêng trong post editor. Ngoài context được hỗ trợ, dùng lại các component design trong Inspector làm fallback; không xây sidebar Site Editor mới trong đợt này.

1. PHP bootstrap cung cấp editor context từ screen hiện hành, tách khỏi theme gate. Context không rõ mặc định không kích hoạt shell riêng. Không dùng post type có/không tồn tại làm guard duy nhất.
2. Đặt guard ở component ngoài; chỉ mount component shell bên trong khi context hợp lệ. Không conditional-call React hooks, không gọi core/edit-post trước guard.
3. Shortcut và Back chỉ xuất hiện khi shell hợp lệ và API cần thiết khả dụng. Store/session navigation có thể đăng ký nhưng không dispatch vào editor sai context.
4. Với editor ngoài context đó, tái dùng CollectionDesignControls cho Image/Title/Details/Action trong Inspector. Category adapter dùng lại component design tương ứng, chỉ sửa điểm tích hợp nếu cần; không sửa query/term persistence của1.3.20.
5. Mỗi context chỉ có một nơi chỉnh cùng setting: post editor dùng shell; fallback dùng Inspector. Fallback vẫn có reset đúng nhóm, Undo/Redo, saved attrs; không đưa lại design controls vào cả hai surface.
6. Template editing bên trong post editor chỉ giữ shell nếu API/navigation đã kiểm hợp lệ. Nếu không, cùng fallback như trên; không chỉ chặn theo wp_template rồi coi đã giải quyết.

Files dự kiến: plugin typography bootstrap; design-sidebar index/navigation; Collection và Category điểm render shortcut/design trong edit/controls. Tái dùng component, không copy một bộ logic setting mới. Nếu audit phát hiện phải thay architecture vượt phạm vi fallback này, ghi evidence và quay lại xin điều chỉnh; không tự xây framework editor chung.

## 4. G2 — Phục hồi session, giữ draft và tách lỗi quyền

### G2.1 — Response có cấu trúc

- Dùng check_ajax_referer với chế độ không tự die, kiểm kết quả rồi trả JSON `code=expired_session`, HTTP403 khi nonce không hợp lệ. Việc này **không** bỏ nonce gate hoặc cho write tiếp.
- Client kiểm shape JSON trước đọc envelope. Response số -1/0, HTML, network error hoặc WAF403 là lỗi transport/session chưa xác định; không tự đặt canEdit=false.
- Chỉ khóa nhóm vì quyền/theme khi server trả code/canEdit có cấu trúc tương ứng. Session lỗi có trạng thái riêng và tạm ngăn Save đến khi phục hồi; draft/base/conflict của mọi nhóm giữ nguyên.
- Không tự retry write, không tự ghi lại sau khi refresh thành công.

### G2.2 — Nút Refresh session

- Thêm endpoint authenticated **chỉ cấp lại nonce cho Theme Design**, không đọc/ghi giá trị thiết kế. Không đăng ký nopriv. Kiểm capability truy cập giống endpoint hiện tại, dùng auth cookie hiện hành; response không cache.
- Endpoint phục hồi không yêu cầu chính nonce đã hết hạn. Đây là đường lấy token read-only, không bỏ xác thực cho endpoint save. Same-origin fetch; không thêm permissive CORS/JSONP.
- Nút “Refresh session” gọi endpoint này một lần theo thao tác user. Thành công: cập nhật nonce trong memory, kiểm lại availability/quyền bằng read hiện có; chỉ cập nhật access, **không thay draft hoặc baseline revision**. Nếu settings đã đổi, Save tiếp theo vẫn phải409 và review current.
- Nếu đã logout/mất quyền/server lỗi: thông báo đúng trạng thái, giữ draft và cho thử lại sau khi user đăng nhập; không vòng retry/poll, không tự mở hoặc submit login form.

### G2.3 — Đường thoát trước reload

- Khi còn draft và cần reload, có “Export draft” hiển thị bản JSON chỉ chứa dữ liệu Theme Design do user đang sửa, kèm group/schema version để đọc được; không đưa nonce/cookie hoặc credential vào bản xuất.
- Dùng textarea read-only chọn/copy thủ công làm fallback luôn có; Clipboard API nếu dùng chỉ là tiện ích, thất bại không chặn lấy dữ liệu.
- Nói rõ bản xuất là bản sao để khôi phục thủ công, **chưa có import tự động**. Không auto-save draft vào DB, localStorage hoặc sessionStorage trong đợt này.
- Giữ cảnh báo rời trang khi dirty; reload không được quảng cáo là giữ draft. Khi session phục hồi bình thường, user tiếp tục Save group mà không cần reload.

Files: `theme-design.tsx`, `typography-settings.php`; English/i18n đúng text domain hiện hành. Không đổi quyền palette/heading/fonts, storage keys, owner locks, revision hoặc Category save policy.

## 5. G3 — Lazy read, giữ lifecycle và draft

- useThemeDesign nhận trạng thái active từ **sidebar đang mở + tab Plugin**, chỉ bắt đầu read khi active lần đầu. Mở Selected block hoặc editor mà chưa mở Plugin không tạo request Theme Design.
- Sau khi đã tải, model nằm ngoài children của PluginSidebar để đóng panel/chuyển tab không unmount hoặc mất draft. Mở lại không tự fetch/ghi đè snapshot/draft.
- Khi read chưa hoàn tất mà đóng panel: không set draft từ một response cũ sau request mới; có cleanup/AbortController khi owner unmount. Pending/error có trạng thái và Retry read rõ, không infinite spinner sau abort.
- Retry read ban đầu chỉ khởi tạo khi chưa có snapshot/draft; refresh session/permissions dùng flow bảo toàn base/draft của G2. Không dùng một hàm read thay toàn bộ state cho mọi trường hợp.
- beforeunload chỉ gắn khi có draft dirty, cleanup khi clean/unmount; không tăng listener qua tab/mount cycles.

Files: design-sidebar index/theme-design. DS-07 đo baseline mới; không đổi query fetching hoặc carousel runtime.

## 6. G4 — Cleanup, evidence và giới hạn còn giữ

- Bỏ một guard is_array trùng; bỏ field exists nếu audit xác nhận không caller nào dùng. Không refactor persistence helper lớn.
- Comment sanitizer ghi rõ registration trong context admin_init, không hứa mọi execution context đều đăng ký. Không đổi sanitizer/schema/unknown-key policy.
- Ledger giữ mốc kiểm2026-11-04 như lịch sử, bổ sung snapshot hiện hành:8source blocks1.3.19 + Category1.3.20. Build artifacts chỉ được ghi nhận theo bản human thực sự build; không suy version/buildPASS từ source.
- Bổ sung test exit/exception ở native hook, DB driver/persistent/cache/topology vào runbook. Phân biệt finally chạy khi exception với exit bỏ qua finally; ghi mysqli cleanup mặc định và cấu hình ngoại lệ. Chưa triển khai shutdown handler nếu chưa có evidence cần thiết.
- Không mở Post hover-background; không thay CSS selector, naming migration, metadata/version, query owner, media defaults/ranges, Post visual design, skeleton hoặc loading046.
- Block UI: không chủ động đổi output frontend. Editor UX và Technical là trọng tâm; DS06/Post persistence/focus cũ vẫn cần regression evidence.

## 7. Acceptance & Evidence — bổ sung runbook sau approval

Tất cả cases dưới **Chưa làm / Chờ kiểm chứng**, không phải PASS.

| ID → Nhóm | Content & States / cách kiểm | Expected / evidence |
|---|---|---|
| SR2-EDITOR → Editor UX/Technical | Post/page editor, template editing trong post editor, Site Editor; supported/unsupported/multi-selection; shortcut, Back, reset, Undo/Page Save/reopen | Không gọi API sai context/console error; một nơi chỉnh design; context fallback vẫn chỉnh và persist được. Ghi WP/version/build, video, attrs trước/sau |
| SR2-NONCE → Editor UX/Technical | Sau khi tải và sửa ít nhất2nhóm, thay nonce bằng giá trị sai trên staging; Save; Refresh session; Save thủ công | Server403 expired_session; draft/base các nhóm giữ nguyên; refresh không write; Save thủ công thành công hoặc409 đúng. Capture requests/responses, owner before/after |
| SR2-SESSION-STALE → Technical/Editor UX | Session A draft, B save cùng nhóm; A nonce lỗi→refresh→Save | Refresh không rebase ngầm; A nhận409, Use current/Keep vẫn đúng; nhóm khác không mất draft |
| SR2-AUTH → Editor UX/Technical | Logout/login hoặc hạ quyền;403structured,403HTML/WAF,body-1/0,network failure | Không đồng nhất mọi403 với mất quyền; không save khi chưa phục hồi; thông báo/session retry đúng; không auto-write |
| SR2-EXPORT → Editor UX | Dirty2nhóm; refresh không được; Export draft; Clipboard bị chặn; reload/cancel reload | Textarea vẫn lấy đủ draft và group; không credential; cảnh báo rời trang vẫn có; không tuyên bố tự khôi phục sau reload |
| SR2-LAZY → Technical | Reload editor, chưa mở Plugin; mở Selected; mở Plugin; đóng/mở/chuyển tab10lần; read chậm/lỗi, Retry, unmount |0ThemeDesign read trước kích hoạt;1initial read thành công cho mỗi model mount; reopen không refetch hoặc mất draft; retry theo user; request/listener cleanup. Network counts + video |
| SR2-LOCK-EXIT → Technical | Staging được phép: native hook throw exception/exit; nonpersistent/persistent driver/cache config ghi rõ; request sau | Không gọi exception=exit; ghi lock state/timing/recovery thực tế. Nếu leak kéo dài thật thì mở repair có evidence, không tự tickPASS |
| SR2-REGRESSION → Block UI/Editor UX/Technical | TD409/permission/theme mismatch; Product/Post hover serialization/reset; disclosure resize/collapse; Category design adapter | Các acceptance đã có vẫn giữ; Category term/query/save không thay; không suy source review thành runtimePASS |

URLs/editor/frontend và WP/PHP/DB/cache/build onsite phải điền vào `docs/testing/plugin-design-sidebar-1.3.19.md` trước chạy. Fault injection/chuyển theme/quyền chỉ trên môi trường được phép. Agent chuẩn bị test; human chạy build/lint/onsite theo protocol. Không tự build/lint qua tool.

## 8. Thứ tự triển khai và bàn giao

1. G0 audit context/API và baseline.
2. G1 context shell/fallback, kiểm mỗi surface còn đường chỉnh setting.
3. G2 transport/session refresh/export; kiểm không thay draft/base và không tự write.
4. G3 lazy lifecycle sau khi error/retry contract đã ổn định.
5. G4 cleanup, đồng bộ premise trong plan050/comment/runbook và ghi execution record ở051. Không sửa lịch sử approval thành chưa duyệt.
6. Static diff/JSON/import/encoding checks được phép; human build/lint và test runtime. Commit source/docs riêng cho repair1.3.19 nếu human yêu cầu commit hoặc approval có bao gồm commit; không stage unrelated/build assets tự động.

Sau sửa báo files/behavior, static checks, tests còn chờ, limitations và prompt review mới theo commit/diff thực tế. Chưa đóng milestone, chưa release/push/deploy.

## 9. Decision memo — phạm vi xin duyệt

1. **Vấn đề:** review lần hai phát hiện session recovery chưa kín và shell chưa xác định editor context, trong khi một số nhận xét không đủ căn cứ để sửa runtime.
2. **Phương án:** **A — G0–G4 theo file này:** post-editor shell + Inspector fallback, refresh session không mất draft, export thủ công, lazy read và cleanup; thêm endpoint read-only/cases regression. **B — chỉ sửa phân loại lỗi/copy, giữ eager read và giới hạn hỗ trợ bằng tài liệu:** ít source hơn nhưng session lỗi vẫn thường buộc reload và context ngoài post editor còn khoảng trống.
3. **Khuyến nghị:** A; sửa đúng đường thất bại và giữ khả năng chỉnh block ngoài shell, không mở thêm Site Editor sidebar hoặc Post background feature.
4. **Bằng chứng:** baselinee13fa2c; index.tsx:23/30/75 gọi edit-post và mount ThemeDesign; theme-design.tsx:38–47 suy403→canEditfalse, initial effect:56–63; typography-settings.php:180 nonce die, :84–85 guard trùng. admin-ajax.php local:45 bác bỏ R19-02. Source refs phải xác minh lại khi implement.
5. **Tự kiểm ≤10 phút:** đọc3file trên và navigation.tsx (5phút); xem core admin-ajax:45 và tài liệu admin_init (1phút); xem G1/G2 và matrix§7 để kiểm expected recovery/fallback (3phút). Runtime vẫn cần evidence riêng.
6. **Đảo ngược:** thay đổi source/UI có thể rollback, không migrate settings hoặc đổi identity. Endpoint refresh không ghi design; export chỉ do user chủ động. Rollback không ghi lại owner snapshot cũ lên dữ liệu mới.
7. **Default:** trước khi human duyệt file051, chỉ lưu kế hoạch; **không coding G1–G4**, không thay current/version hoặc claim runtimePASS. Không có approval ngầm theo thời gian.

**Phạm vi duyệt đề xuất: A — G0–G4**, bao gồm Inspector fallback ngoài shell và Export draft thủ công; không bao gồm session draft persistence/import tự động hoặc shutdown-lock mitigation.

## 10. Execution record

**Đã có source / Chờ kiểm chứng — 2026-11-05.** Human duyệt A/G0–G4; baselinee13fa2c, giữ unrelated dirty context/build. Không bump version, không release/deploy/push hoặc commit trong lượt này.

- **G0:** local core đối chiếu WP7.1 tại `D:/Github/minhhaifish/wp-includes/version.php:19`; edit-post.js:9818/10120 delegates complementary-area API, :11794 delegates PluginSidebar sang editor. components.js:26874 dùng forwardRef cho Button. admin-ajax.php:45 gọi admin_init. Đây là source/API evidence local; onsite version/UI vẫn TBD.
- **G1:** PHP bootstrap thêm postEditor từ screen base=post và is_block_editor; JS guard context trước khi truy cập edit-post, kiểm selectors/actions khả dụng. Outer shell không conditional hooks. Collection/Category dùng component controls + reset chung trong Inspector khi không dùng shell; context shell chỉ hiện shortcut, không lặp controls. Template editing trong post editor dùng cùng context/API gate, cần onsite xác nhận navigation. Category chỉ sửa integration render, không term/query/save logic.
- **G2:** nonce check không tựdie, trả expired_session có cấu trúc; request kiểm envelope để-1/0/null/HTML không bị suy thành mất quyền. Refresh session endpoint chỉ wp_ajax/authenticated/capability-gated, không settings write hoặc nopriv; trả nonce, nocache. Client refreshnonce→read access, không thay group value/revision/draft/conflict, không autoSave. Session lỗi có trạng thái riêng; Save bị chặn đến phục hồi. Export JSON chỉ dirty ThemeDesign groups/schemaVersion, textarea read-only/copy thủ công, không storage/import/credential. Warning rời trang dirty giữ nguyên.
- **G3:** hook activate lần đầu khi sidebar mở/tabPlugin; initial read giữ chạy qua đóng panel, không fetch lại khi mở lại; retry thủ công và initial read cleanup có abort/live guard. Save/read action dùng lifecycle signal khi unmount. Session/permissions recovery không dùng initial read để overwrite base/drafts.
- **G4:** bỏ guard trùng/exists chưa dùng, sửa sanitizer comment đúng admin_init; reset dùng chung shell/fallback. Không mở Post background, không sửa CSS hoặc thay advisory-lock behavior. Runbook thêm SR2cases và persistent-driver limits; evidence ledger tách lịch sử2026-11-04 với snapshot hiện hành.
- **Kiểm chứng agent:** Node24 chạy actual transport/hook qua native type stripping trong VM với simulated hooks/fetch (không compile plugin/emit assets, không thay thế React/Gutenberg integration). Script `tools/tests/design-sidebar-session.test.mjs`; cases lazy/close/reopen/unmount, nonce/403, stale revision409, permission, retry/session failure. Lệnh direct Node tránh spawn test worker bị EPERM trong sandbox. Kết quả cuối ghi trong report lượt triển khai. Diff whitespace/import/JSON/UTF8 kiểm tĩnh; không build/PHPlint/WordPressDB/UItest.
- **Giới hạn còn giữ:** object-cache/DB lock overlap và hook exit/exception cần staging; mysqli persistent mặc định cleanup GET_LOCK khi reuse nhưng không dùng điều đó làm runtimePASS. OnsiteURL/version/build, template-mode/back/fallback/reset/Undo/reopen và export keyboard/render chưa kiểm. Toàn bộ DS/TD/Carousel carry register vẫn hiệu lực.

Kết quả kiểm cuối: **12/12 Node VM tests PASS**; UTF8/relative-import checks cho6source files PASS;9source blockJSON parsePASS; scoped diff whitespace checkPASS. Không thực hiện TS/plugin build, PHP lint hoặc onsite. `node --test` bị sandboxEPERM khi spawn worker; chạy file trực tiếp bằng Node thành công, không escalation hoặc sửa môi trường. Review prompt mới tại `docs/testing/plugin-design-sidebar-1.3.19-review-handoff.md` mục Target hiện hành; không dùng commit050 làm bằng chứng source051 đã commit.

## Commit checkpoint — 2026-11-05

Human subsequently requested feature-separated commits for all pending work. During staging audit, Category DesignShortcut referenced `clientId` without destructuring it from EditProps; the binding is corrected with the fallback integration. Twelve VM tests rerun PASS; changed TS/TSX syntax parsed without diagnostics (not a typecheck/build), JSON and diff whitespace checks PASS. Existing compiled assets are preserved separately and must be rebuilt after this source correction. No onsite/release approval.
