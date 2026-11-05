# Editor preview frames — onsite verification

2026-11-05, current1.3.19 follow-up plan053. **Source added; runtime pending.** Không thay CP01–05, DS/TD hoặc Category acceptance. EF06 Slider/Step chưa implement, không coi PASS.

## Target và setup

- Product baseline: `https://minhhaifishery.com/home/` → Edit Page. Ghi editor URL/pageID, WP/plugin/theme version, source/build hash, browser/OS.
- Trên trang test được phép: Product/Post Grid/Carousel + Product Category Grid, ít nhất hai frames liền nhau. Ghi URL riêng; không tự sửa production content để tạo fixture.
- Human build/deploy source cuối rồi clear asset cache. Có fixture heading/intro dài, Product specs/+N, Post date/author/excerpt, Category có/mất ảnh và missing/empty terms.
- Xác nhận screenshot/build là cùng lượt, viewport có vertical scrollbar; editor iframe cần chọn đúng document khi inspect.

## Cases

| ID | Steps | Expected / PASS criteria | Evidence |
|---|---|---|---|
| EF-01 | Edit heading/intro, toggle showHeading, Undo/Redo, Save/reopen | Collection heading/intro + card + info + footer cùng một root frame; label chỉ editor; không khung lồng khung. Category không tự có heading mới, notices nằm trong khung | Screenshot, saved attrs, editor/frontend comparison |
| EF-02 | Đặt các family cạnh nhau, đổi Collection Grid↔Carousel; kiểm Category | Carousel xám xanh #f6f8fa/#dce4e9; Grid xanh biển #eff8fc/#c9e3ef; nhãn Product/Post/layout đúng; card colors không đổi | Computed background/border và screenshot |
| EF-03a | Stack2frames, parent gap0/8/24px, thử reorder/delete/undo và tăng giảm parent gap | Khoảng hở border dọc >=10px; gap24 không biến thành34; adapter bỏ correction khi không còn frame trước, không nhảy vô hạn | Rect top/bottom 2frames + computed margin/gap sau settle |
| EF-03b | Đặt hai frames cạnh nhau trong layout ngang; Group/Columns hẹp; resize | Không tự đẩy một frame xuống vì đo gap dọc giữa hai item ngang. Native parent alignment/width vẫn đúng; wrapper khác hoặc custom-positioned case không đạt phải ghi FAIL, không che overflow | Screenshot, rects, parent display/row-gap, console |
| EF-04 | Canvas >760/760/520/320px, Inspector/Design sidebar mở/đóng; toggle image/specs/tags/date/author/excerpt/CTA/equalHeight | Overview hai cột ở rộng, stack hẹp; không mất fields, query states, sample labels, Show more/less hoặc limitation note. Post giữ note cap5 | Screenshots + keyboard disclosure/focus + request states |
| EF-05 | Compare frontend trước/sau cùng fixture; inspect saved content/schema; Network khi resize/style | Không serialize chrome/label/margin; không query/schema/runtime thay đổi, không request mới vì frame, không Swiper editor. Parent canvas không tràn ngang | Saved markup, frontend screenshot, Network, scrollWidth/clientWidth |

Keyboard: native toolbar/Add/selection/RichText/IME không bị nhãn/frame cản; Tab/focus ring không clipped. Query empty/loading/error/unavailable vẫn giữ card minh họa. Thay đổi selection/block/unmount phải cleanup observers; không warning setState/unmounted hoặc correction loop. Trong geometry record: parent/container và frame rect, original margin, computed marginBlockStart, rowGap, document scrollWidth/clientWidth. Kiểm actual rendered gap, không suy cộng margin5+5.

Report từng EF: PASS/FAIL, URL/build/steps/expected/actual/screenshots/console. Thiếu URL/geometry là Chờ kiểm chứng. Contract no-global-margin không có nghĩa bảo đảm mọi theme/custom positioned parent trước onsite.
