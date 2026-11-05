# GP-LAYOUT-01 — Content Container / SKVN canvas migration checks

Reported: 2026-11-04. Status: **Deferred theo yêu cầu human**, xử lý khi bắt đầu tách GeneratePress. Target: **V2 / 2.0.0** baseline/audit/contract; **V3 / 3.0.0** xác nhận standalone. Không thuộc hotfix loader; không yêu cầu sửa source hiện tại.

Planning: `.context/planning/013_VERSION_2_0_0_TO_3_0_0_STANDALONE_BLOCK_THEME_PLANNING.md`, carry-in GP-LAYOUT-01. Đọc thêm `docs/decisions/canvas-alignment-and-generatepress-exit.md` và CSS layout safety contract; không coi root cause của case cũ là đã chứng minh cho case này.

## Report và evidence gốc

Human: khi không chọn GP Layout → Content Container **Contained**, content tràn ra ngoài dù bật hay tắt SKVN Full width canvas. Chưa rõ option thay thế cụ thể, extent/direction overflow hoặc controls khác giữ nguyên thế nào.

- [Editor settings](evidence/gp-content-container-2026-11-04/editor-contained.png): SKVN Landing Canvas, Hide page title on, Full width canvas on, No Sidebars, GP Content Container Contained.
- [Frontend reference](evidence/gp-content-container-2026-11-04/frontend-reference.png): Hero, Featured products, header/footer. Không có screenshot lỗi theo từng state, không suy ra geometry PASS từ reference này.
- Target candidate: **https://minhhaifishery.com/home/** theo page đang được trao đổi; human xác nhận page URL/ID của screenshot trước test. Secondary fixture URL __. Không tự sửa production.

## Preconditions

1. Khi 2.0.0 current, nhắc human thực hiện baseline trước thay layout; chạy trên staging/copy được phép, cùng content trước/sau.
2. Ghi URL/page ID, WP/GP/SKVN/plugin versions/build, browser/OS/viewport/zoom, cache, logged-in/admin bar; chụp page preset, meta và hai panel.
3. Inventory **actual** GP Content Container options và defaults tại version đang test; không tự đoán tên option còn lại. Lưu values gốc để restore.
4. Giữ Sidebar Layout, header/footer, content markup, alignment và options block không đổi. Phân biệt background full-bleed mong muốn với inner content vượt constraint.
5. Có fixture normal, alignwide, alignfull, nested legacy alignfull; core Group/Columns và Hero/Product/Post surfaces cần thiết. Editor view cũng cần ảnh đối chiếu.

## Steps / Expected / Pass–Fail

| Step | Thao tác | Expected / tiêu chí |
|---|---|---|
| 1 | GP Contained + SKVN Full width off; reload, đo geometry và lưu screenshot | Baseline thực, không tự gọi PASS; ghi page/content/component owners |
| 2 | Chỉ bật SKVN Full width, save/reload | Thay canvas theo contract, normal/wide/full vẫn khác nhau; inner content không tự bung theo nền |
| 3 | Giữ SKVN Full width on, đổi từng GP container option khác Contained | Ghi đúng state tái hiện; xác định element đầu tiên vượt wrapper/viewport và rule/meta tạo delta |
| 4 | Với từng option đó, chỉ tắt SKVN Full width; save/reload | Xác minh report rằng toggle không khắc phục; không kết luận từ một state |
| 5 | Lặp matrix ở desktop có scrollbar dọc, tablet/mobile; editor↔frontend | Không tràn ngoài canvas; không clip CTA/controls; report px và breakpoint thật |
| 6 | Khi có SKVN layout contract/adapter, chạy lại matrix và round-trip options cũ/preset/custom | Precedence rõ, values không mất; editor không cần mẹo GP Contained để có layout hợp lệ |
| 7 | Trên standalone fixture giai đoạn 2.x/3.0.0, tắt GP compatibility adapter và dùng template SKVN | Normal/wide/full/nested chạy từ SKVN/theme.json; không phụ thuộc GP DOM/classes/meta để tính width |
| 8 | Restore test settings; smoke header/footer/Hero/Product/Post và page thường | Không regression content, template hoặc control portability |

Đo `document.documentElement.clientWidth/scrollWidth` và `getBoundingClientRect()` của page canvas, content wrapper, .entry-content, alignfull sections, inner container, carousel track/slides, header/footer; ghi computed width/max-width/margins/overflow cùng rule thắng. PASS geometry cần `scrollWidth <= clientWidth`, surfaces trong canvas và đúng content/wide/full contract; không chấp nhận dùng overflow-x hidden/clip để che sai layout. Nếu vấn đề chỉ là mất inner constraint, vẫn FAIL contract dù không có scrollbar ngang.

## Evidence human gửi / phần agent tiếp tục

### GP-LAYOUT-02 — không phải chỉnh thủ công toàn site khi migrate

Human bổ sung 2026-11-04: migration phải giữ tương thích nội dung và cấu hình cũ, không yêu cầu mở/sửa/save lại từng page/post. Status: **Deferred → 2.0.0**, tiếp tục là gate nghiệm thu standalone 3.0.0.

1. Tạo staging clone/backup; inventory URL, templates, saved block markup/patterns, GP layout meta, SKVN presets và page-level overrides. Chọn fixtures đại diện cho từng tổ hợp đang dùng, đồng thời giữ danh sách URL toàn site để kiểm tra coverage.
2. Lưu baseline content/settings, block validity, links/media, screenshots và geometry. Review mapping dữ liệu cũ sang layout mới; nếu cần chuyển đổi dữ liệu, human duyệt dry-run/report và rollback trước khi chạy.
3. Chuyển nền theme/adapter và chạy migration đã duyệt trên clone, **không mở/sửa/save lại từng trang**. Kiểm toàn bộ URL inventory và so visual/geometry của fixtures trước-sau: content không mất, links/media không đổi ngoài mapping được duyệt, blocks không invalid, normal/wide/full và overrides giữ đúng ý định.
4. Nếu có migration dữ liệu, chạy lại để xác minh không nhân đôi/ghi đè sai; diễn tập rollback/restore theo cơ chế đã chọn và kiểm lại fixtures. Adapter/default không đổi DB thì ghi N/A cho bước chuyển đổi dữ liệu, vẫn kiểm rollback theme và compatibility.
5. Báo coverage, PASS/FAIL và mọi ngoại lệ theo URL/lý do/cách xử lý trước cutover. FAIL nếu layout chỉ đúng sau khi resave/chỉnh từng trang hoặc tồn tại ngoại lệ chưa được human quyết định; không dùng một trang đẹp làm bằng chứng toàn site đã migrate thành công.

Evidence: inventory/coverage report, mapping, dry-run/migration logs nếu áp dụng, content/settings diff, screenshots/geometry, invalid-block checks, danh sách ngoại lệ và rollback result. Đây là kế hoạch nghiệm thu, không cho phép tự thay dữ liệu production.

Gửi từng state GP container × SKVN toggle: URL/ID, options/meta, screenshot hai panel + frontend/editor, dimensions/computed styles, console notes và browser/build. Agent dùng state delta để trace owner và đề xuất migration/layout contract dựa trên bằng chứng. Chưa có số đo thì giữ Deferred/Chờ kiểm chứng; không ghi nguyên nhân hay PASS. Không giảm acceptance migration cũ; chỉ thêm regression fixture cho dependency được báo lần này.
