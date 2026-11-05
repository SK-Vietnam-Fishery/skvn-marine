# V1 / 1.3.19 — Review repair plan

Ngày: 2026-11-05. **PROPOSED — chờ human duyệt trước coding.**
Approval/execution override **2026-11-05**: human đã duyệt **A — F0–F5**, yêu cầu coding xong commit1.3.19 và prompt agent khác review. Các câu PROPOSED/chờ duyệt bên dưới giữ snapshot trước approval; execution record cuối file là trạng thái hiện hành. Root chịu staging/commit, agent Sol không tự commit; không build/lint/deploy/push.
Current vẫn1.3.19; không đổi version, metadata hoặc release status. Plan048 là scope/acceptance gốc; tài liệu này bổ sung repair S19-01–04 từ source review, không thay DS/TD hoặc Carousel carry-over. Không sửa source trong lượt planning.

## 1. Bằng chứng và phạm vi

| ID | Evidence source trong plugin | Lỗi / tác động | Nhóm |
|---|---|---|---|
| S19-01 — major | `src/collection/controls.tsx:1370`, `src/post-collection/block.json:185` | Post có Action hover text control và consumer nhưng không đăng ký attr; không đủ chuỗi serialization | Editor UX / Technical / Block UI |
| S19-02 — major | `modules/typography-settings/typography-settings.php:104–130` | Revision check tách write; palette/heading merge vào một option từ snapshot cũ, concurrent requests có thể mất nhóm khác | Technical / Editor UX |
| S19-03 — minor | `src/design-sidebar/theme-design.tsx:60–68` | Retry409 dùng revision cũ; reload hiện có discard draft, thiếu đường giữ draft và review current | Editor UX / Technical |
| S19-04 — minor | `src/collection/controls.tsx:978` | Collapse sau resize có thể unmount nút đang focus mà không chuyển focus | Editor UX / A11y |

Review là source-only trên working tree1.3.19; không runtime PASS. Trước implementation phải re-read diff/file:line hiện tại vì source có thể đổi. Category1.3.20 commit7ef45ff và findings R01–R12 không thuộc repair này. Không sửa Product/Post query owner, media ranges/defaults, Post visual design, skeleton/loading046 hoặc migration naming.

## 2. Gói sửa đề xuất

### F1 — Persist Post hover text

- Thêm `actionHoverTextColor` vào Post block.json với type string và default kế thừa hiện hành; đối chiếu Product schema/TS/PHP allowlist trước chọn giá trị rỗng làm default.
- Kiểm control → attr → preview → serialized markup → PHP class/token → computed hover/focus; không chỉ thêm key rồi coi xong.
- Old Post không có attr giữ baseline; reset Action chỉ clear overrides đúng scope, không xóa URL/query và hidden legacy attrs.
- Files dự kiến: Post schema; chỉ sửa types/control/consumer nếu audit thấy thiếu. Build output do human rebuild, không chỉnh tay compiled assets.

### F2 — Chống lost update ở storage owner

Mục tiêu bắt buộc: hai request Sidebar khác nhóm không làm mất nhau; cùng nhóm stale không được âm thầm cùng báo Saved. Quyền/nonce/theme gate/validation vẫn kiểm phía server mỗi request. Không dùng khóa UI hoặc transient get→set làm bằng chứng chống concurrent writers.

Thiết kế đề xuất: optimistic write theo **toàn storage owner**, kết hợp revision theo nhóm để vẫn cho phép palette và heading độc lập:

1. Validate request/group/capability; lấy raw persisted owner và derive sanitized group/revision tương ứng.
2. Nếu revision nhóm đã đổi: trả409 kèm current group/value/revision; không write.
3. Merge đúng nhóm request vào owner vừa đọc, bảo toàn các keys/nhóm khác.
4. Ghi có điều kiện: chỉ commit khi raw owner vẫn bằng snapshot dùng để merge. Nếu owner đổi trong khoảng check→write, re-read và kiểm lại; khác nhóm thì merge lại, cùng nhóm thì409. Retry bounded; hết lượt trả busy/conflict và giữ draft, không fallback unconditional write.
5. Chỉ báo Saved sau xác nhận write/persisted result; response current snapshot chỉ thay base của nhóm liên quan ở client, không overwrite draft nhóm khác.

Owners: `skvn_typography` chứa palette+heading; `theme_mods_<active-stylesheet>` chứa Fonts cùng các theme mods khác. Fonts không được đổi preset rồi scope bằng hai write riêng có thể interleave; giữ cùng logical request/owner. Không tạo settings store hoặc font loader thứ hai.

**Implementation gate:** audit PHP/WP runtime và native option semantics trước code persistence helper. Nếu phải conditional DB write, phải giữ serialization, absent option handling, object-cache/alloptions invalidation và behavior filters/actions liên quan; không thay native update bằng một SQL thiếu cache/hook semantics. Đây là sửa persistence, không custom SQL query sản phẩm. Không gọi helper là CAS an toàn trước khi có deterministic overlap evidence.

Audit mọi writer hiện hành: Sidebar AJAX, Typography admin Settings API, Customizer Fonts và các theme_mod writes. Writer không tham gia protocol có thể vẫn ghi stale whole-owner sau Sidebar; không hứa chống mọi third-party writer. Ghi rõ đường nào được bảo vệ, kiểm admin/Customizer interaction; nếu cần sửa writer ngoài phạm vi files đã xác định hoặc không bảo toàn native semantics, chuẩn bị evidence/memo bổ sung trước mở rộng. Không phát hành giải pháp chỉ đổi hash nhóm thành hash toàn option rồi vẫn update vô điều kiện.

### F3 — Review current và giữ draft sau conflict

- Trả error có cấu trúc `code=conflict`, current value/revision/canEdit; client không parse chuỗi message để phân loại409.
- Khi conflict: giữ original draft, hiển thị current và draft của đúng nhóm; có **Use current values** và **Keep my draft**. Giữ labels English/i18n và scope toàn site rõ.
- Use current: thay draft/base nhóm đó bằng current, clean; không ảnh hưởng nhóm khác.
- Keep: rebase đúng nhóm lên current revision, giữ draft, tính lại dirty; **không auto-write**. User bấm Save riêng sau khi review; nếu current lại đổi thì409 mới.
- Pending/read/error không làm mất draft; nếu quyền/theme thay đổi thì khóa write và giải thích. Giá trị giống current phải clean, không giả còn unsaved.
- Page Save vẫn không ghi Theme Design; chưa live preview. Giữ draft qua tab/close; rời trang dirty có cảnh báo như contract048.
- Files: `theme-design.tsx` và transport error payload F2; không áp flow này cho Category term drafts trong1.3.20.

### F4 — Focus khi disclosure biến mất

- Thêm ref cho query heading/list focus target; nếu collapse làm nút disclosure biến mất thì chuyển focus có chủ đích về target có nhãn.
- Giữ expanded state khi resize, không tự collapse. Không steal focus nếu người dùng đang ở Inspector/input khác; chỉ xử lý khi active control trong phần bị unmount hoặc action collapse tương ứng.
- Case mục tiêu: Product8items, expand ở container hẹp → resize đủ rộng để8items vừa limit → Show less. Kiểm Tab tiếp theo/screen reader context, cả keyboard và pointer.
- Files: QueryPreview trong controls.tsx; không thay query/count/cap hoặc CSS geometry chỉ để giữ nút giả.

## 3. Thứ tự và nghiệm thu

| Gói | Đầu ra | Gate |
|---|---|---|
| F0 audit | Baseline commit/dirty diff, owners/writers/runtime/API inventory, kiểm findings còn đúng | Không ghi đè Category/unrelated working tree |
| F1 | Post attr persist đầy đủ | E07-POST-PERSIST + old/new/reset fixtures |
| F2 | Conditional owner writes + structured conflicts | Deterministic same/different-group overlap; cache/read-after-write; error/permission |
| F3 | Conflict review/rebase/discard UI | Giữ draft xuyên lỗi/409; không autosave/đổi nhóm ngoài ý muốn |
| F4 | Focus handoff | Resize/collapse keyboard/screen reader |
| F5 handoff | Source review + human build/lint + runbook/evidence | Không tự tick runtimePASS hoặc đóng milestone |

F1/F4 có thể làm độc lập; F3 nối contract F2. Mỗi gói có diff review riêng, không refactor file lớn vì thuận tay.

| Test ID / acceptance | Steps / Content & States | Expected / evidence |
|---|---|---|
| E07-POST-PERSIST / DS04/06, UX12, TECH13 | Hai Post blocks hover colors khác nhau; save/reopen; old missing key; reset; Product đối chứng | Attr serialization đúng, computed hover/focus đúng từng block, query/URL không đổi; attrs+ảnh+computed |
| TD-OVERLAP-DIFF / DS05 | Hai sessions cùng base, palette A/heading B, server barrier sau read trước write | Cả hai nhóm giữ kết quả, không lost update; timing/raw owner/responses. Click gần nhau không đủ chứng minh |
| TD-OVERLAP-SAME / TD-CONFLICT | Cùng group cùng base, requests overlap deterministic | Một commit, request stale conflict hoặc retry có review rõ; không cả hai Saved rồi silent overwrite |
| TD-FONTS-OWNER / TD04 | Fonts overlap cùng group; giữ theme_mod key khác; cache on/off; admin/Customizer path audit | Preset/scope nhất quán, unrelated mods giữ nguyên, read-after-write đúng; owner values/requests/font network |
| TD-CONFLICT-RECOVER / TD02 | A save; B409; review→Keep→Save; lặp Use current; server đổi lần nữa; read/write failure | Draft không mất, base chỉ đúng nhóm cập nhật, no auto-write, lần stale mới409; video/payload/persisted values |
| UX10-RESIZE-FOCUS / DS03 |8Product results, narrow expand→wide→collapse; keyboard/reader và input focus elsewhere | Target focus có nhãn, Tab tiếp tục hợp lý, không focus theft; video+activeElement |

Trước onsite bổ sung các cases trên vào `docs/testing/plugin-design-sidebar-1.3.19.md`; URLs/build/WP version hiện TBD phải điền, không bịa. Server barrier/failure injection chỉ staging được phép. DS/TD baseline và toàn bộ Carousel carry-over vẫn giữ, F1–F4 không thay thế chúng.

Human chạy build/lint theo AGENTS sau coding; packaging audit nếu helper thêm runtime require/include. Agent không tự chạy build/lint. Status chuẩn: Đã có source/Chờ kiểm chứng/PASS/FAIL theo từng test và build, không dùng source existence làm PASS.

## 4. Decision memo — xin duyệt repair

1. **Vấn đề:** bốn source defects khiến Post settings không persist, global settings có thể mất cập nhật và conflict/focus flow chưa hoàn chỉnh.
2. **Phương án:** A — sửa đủ F1–F4 với conditional storage-owner write và conflict review: bảo toàn intent, cần persistence/cache/concurrency QA kỹ. B — F1/F4 trước, tạm khóa global writes đến khi có repair F2/F3: giảm rủi ro ghi đè nhưng mất chức năng Theme Design đã chốt, chỉ là phương án tạm. Không đề xuất ship current global writes rồi chỉ thêm warning.
3. **Khuyến nghị:** A. Repair nằm trong acceptance1.3.19, không cần version mới. F2 phải qua implementation gate và deterministic tests trước khi được gọi là giải quyết concurrency.
4. **Bằng chứng:** các source anchors §1; AJAX hiện snapshot→check→write riêng, palette/heading cùng option; Post schema không có hover key; disclosure render phụ thuộc expanded/limit.
5. **Tự kiểm ≤10 phút:** mở Post schema so control (2phút); đọc AJAX104–140 với hai whole-option writers (3phút); theme-design save/discard (2phút); QueryPreview disclosure978 (2phút). Source không thay runtime proof; test matrix §3 cho bước nghiệm thu.
6. **Đảo ngược:** F1/F3/F4 chủ yếu additive/UI, rollback được nếu giữ saved attrs. F2 có tác động persistence/cache; không đổi storage keys/migrate dữ liệu, cần backup fixtures và isolation. Không rollback bằng cách ghi lại toàn settings cũ làm mất chỉnh sửa mới của user.
7. **Default:** trước human duyệt, chỉ lưu plan, chưa coding F1–F4; không release global writes như đã sửa. Sau approval, triển khai gói đã duyệt và báo riêng mọi giới hạn writer/API mới có evidence, không hỏi lại product direction.

Phạm vi cần duyệt: **A — F0–F5**, trong đó F3 có Review current / Use current / Keep my draft rồi Save riêng. Post visual/media bounds vẫn là gates riêng của048, không được coi approval repair là approval các con số/thiết kế đó.

## 5. Execution record — approved repair 2026-11-05

**Status: Đã có source / Chờ kiểm chứng**, không runtime PASS. Baseline HEAD `7ef45ff300203ecaf61f32195a19780b5a623ce0` + source Sidebar1.3.19 chưa commit; Category source/adapter7ef45ff giữ nguyên, không staging build artifacts trong đợt repair.

- **F1 / S19-01:** Post đăng ký `actionHoverTextColor` string/defaultempty, đối chiếu Product/TS/preview/PHP allowlist hiện có. Không chỉnh compiled schema; human rebuild trước test persistence/hover/focus/reset.
- **F2 / S19-02:** dùng **equivalent owner advisory lock + native Options API**, đã root xác nhận thuộc hướng A approved; **không gọi đây là CAS/conditional SQL write**. Tên lock63chars hash DB/options-table/owner; Sidebar palette+heading chunglock, Fonts toàn `theme_mods_<stylesheet>` chunglock. `GET_LOCK(...,0)` một lần, timeout/unsupported/recursive/lostlock → structured `busy`503, không unguarded fallback, **retry thủ công**, không bounded retryloop. Acquire→clear owner/alloptions/notoptions caches→rawDBread→read normalized current/revision→merge→native `update_option`→physical/effective readback→releasefinally. JSON response ngoài critical section vì exit không chạy finally. Fonts pair hai `pre_set_theme_mod_*` filters và **một whole-owner update**, giữ theme mods khác; native `get_theme_mods` migration khi row chưa có được giữ.
- **Native semantics audit:** giữ `update_option` thay clone SQL update, nên native sanitize/filter/action/serialization/absent-row/cache semantics còn owner. Typography native registered sanitizer vẫn quyết định unknown-key policy; không hứa giữ keys mà sanitizer vốn loại. `pre_set_theme_mod_*` dùng value/old-value như core set_theme_mod. Raw readback tránh báo Saved chỉ vì cache/read filter trả desired value. [WP6.5 Options source](https://raw.githubusercontent.com/WordPress/wordpress-develop/6.5/src/wp-includes/option.php) và [WP6.5 Theme source](https://raw.githubusercontent.com/WordPress/wordpress-develop/6.5/src/wp-includes/theme.php) được audit; onsite WP/DB version còn phải ghi trong evidence. Lock primitive đối chiếu [MySQL8.0](https://dev.mysql.com/doc/refman/8.0/en/locking-functions.html) / [MariaDB](https://mariadb.com/docs/server/reference/sql-functions/secondary-functions/miscellaneous-functions/get_lock).
- **Writer inventory/giới hạn:** Sidebar AJAX hợp tác cùng lock. Typography admin Settings API→native update_option, Customizer→set_theme_mod, theme/external theme_mod writers **không tham gia lock** và không được chuyển trong repair này. Lock chỉ serialize cooperating Sidebar writers trên cùng database connection/server; không bảo đảm atomic với nonparticipating admin/Customizer/third-party writes, read filters hoặc DB-routing drop-ins. Deterministic overlap/cache/hooks evidence vẫn bắt buộc trước release; chưa gọi lost-update acceptance PASS.
- **F3 / S19-03:** structured error `code=conflict/current/value/revision/canEdit`; giữ draft trong409, hiện bảng Current saved/Your draft, Use current làmclean đúnggroup, Keep rebases đúnggroup không ghi. Save riêng lần sau có revision đãreview, lần stale tiếp theo409 mới. Busy/network/permission errors không mất draft;403 khóa savegroup, Refresh permissions không thay draft/base. Page Save không saveglobal, không livepreview.
- **F4 / S19-04:** disclosure/query heading refs; collapse chỉ chuyển active focus từ nút sắp unmount về heading cónhãn, không cướp focus inputkhác; expanded không đổi khiresize. Không đổi geometry/query counts để né case.
- **F5:** runbook bổ sung6repair cases + lockbusy/hooks/absent-row/nonparticipating writers. Static JSON/import/whitespace checks được phép; không TSbuild/PHPlint/WordPressDB/onsite test. Root nhận diff để stage source+docs1.3.19, builddirty giữngoài; không milestonecompletion hoặc releaseapproval.
