# Review contract — Block UI, Editor UX, Technical

**Status:** Active — human yêu cầu 2026-10-14.  
**Áp dụng:** audit/review/planning và implementation Gutenberg blocks hoặc UI có frontend/editor/runtime liên quan. Đây là cách phân tích/ghi evidence; không tự mở rộng milestone, thêm tính năng hoặc bắt xin lại approval đã có.

## 1. Ba nhóm bắt buộc xem xét

| Nhóm | Câu hỏi chính | Nội dung review |
|---|---|---|
| Block UI (frontend UI/UX) | Người xem thấy gì, thao tác được gì? | Hierarchy, typography, media, spacing, navigation, CTA, feedback, states |
| Editor UX | Người biên tập hiểu và điều khiển kết quả được không? | Discoverability, controls có tác dụng, defaults, labels/help, preview parity, save/reload |
| Technical | Cơ chế có đúng, an toàn, ổn định và tương thích không? | Query/render/data, sanitization/escaping, markup, runtime/lifecycle, assets, compatibility/build |

Không dùng “có code” để kết luận cả ba nhóm đã hoàn thành. Một control phải nối được control → attribute → preview → saved/reopened state → frontend. Preview giản lược phải nói rõ giới hạn; không được trình bày sai hiệu lực setting.

Với task hẹp, ghi ngắn phạm vi ảnh hưởng và N/A có lý do cho nhóm không liên quan; không bắt viết bảng dài hay mở audit toàn project. Ví dụ sửa nhãn editor không đổi behavior: Block UI N/A (không đổi output), Editor UX kiểm wording, Technical kiểm translation/save không bị tác động. Docs-only không cần runtime test; vẫn phải phân biệt đề xuất với decision. Không dùng N/A để bỏ acceptance đã nhận.

## 2. Hai lớp review xuyên suốt

**Content & States:** mỗi yêu cầu nêu loại dữ liệu và trạng thái liên quan: Product/Post, có/không ảnh, thiếu/đủ dữ liệu, title dài, loading/error/empty, ít/đủ/nhiều item, plugin dependency inactive, old/new content. Chọn baseline + edge cases có lý do; không nhân toàn bộ tổ hợp vô ích.

**Acceptance & Evidence:** mỗi yêu cầu có cách kiểm, expected result và evidence kiểm lại được. Source/file:line chứng minh implementation hoặc defect source; screenshot/DOM/geometry/URLs/logs chứng minh kết quả thực tế. Thiếu evidence thì ghi chờ kiểm chứng, không kết luận runtime PASS.

Accessibility, responsive và performance áp vào cả ba nhóm theo surface liên quan:

- UI: focus/contrast/touch targets; geometry/wrap; ảnh không gây layout shift bất thường.
- Editor: label/keyboard/help; canvas/sidebar hẹp; query/render phản hồi.
- Technical: semantics/ARIA/focus scope; resize state; conditional load/cleanup/listeners/memory.

Không chỉ gom thành một checklist kỹ thuật cuối cùng. Evidence phải nêu environment, fixture, build/commit và expected/actual; không bịa số đo hay ngưỡng.

## 3. Requirement record và trạng thái

Mẫu bắt buộc cho planning/review có nhiều yêu cầu:

`ID → Nhóm → Hành vi mong đợi → Hiện trạng → Việc cần sửa → Cách kiểm → Evidence`

| ID | Nhóm | Hành vi mong đợi | Hiện trạng | Việc cần sửa | Cách kiểm | Evidence |
|---|---|---|---|---|---|---|
| UX-example | Editor UX | Tắt ảnh thì preview và frontend cùng không hiện media | FAIL source — preview bỏ qua setting | Nối setting vào preview | Toggle, save/reload, so frontend với cùng fixture | File/symbol hiện tại; ảnh trước/sau sẽ bổ sung |

Giữ ID ổn định, thêm ID con khi tách. Có thể ghi status ở đầu Hiện trạng hoặc thêm cột Status riêng.

| Status | Ý nghĩa / điều kiện |
|---|---|
| Chưa làm | Chưa có phần thực hiện đáp ứng yêu cầu cụ thể |
| Đã có source | Có implementation; chưa kết luận behavior/acceptance |
| Chờ kiểm chứng | Đã có phần cần kiểm hoặc còn thiếu evidence; nêu loại kiểm còn thiếu |
| PASS | Đạt tiêu chí trong phạm vi/environment/build ghi rõ; kèm evidence |
| FAIL | Có evidence vi phạm tiêu chí; ghi source hay runtime, steps/expected/actual |
| Deferred | Hoãn có lý do, human decision nếu đổi ưu tiên, nơi nhận/target milestone; giữ acceptance |

Không dùng DONE chung cho implementation và nghiệm thu. Không biến design approval thành runtime PASS. Không đổi Status của TENSIONS (OPEN/RESOLVED_ACTIVE/ARCHIVED) sang taxonomy này.

Transition Rule hiện có vẫn hiệu lực: acceptance DONE để chuyển mốc phải hiểu là acceptance đã được kiểm chứng đạt và có approval cần thiết, cộng runtime smoke và human approve chuyển mốc; “đã có source” không đủ. Không tự sửa trạng thái lịch sử hoặc đóng milestone cũ chỉ để làm sạch bảng.

## 4. Cách áp dụng trong workflow

1. Load context đúng AGENTS; xác định scope/authority và baseline source/build.
2. Xem cả ba nhóm, chọn states, ghi yêu cầu/evidence theo ID. Nêu rõ source finding, runtime finding, design proposal và human decision.
3. Review trade-off/boundary nếu chưa chốt; dùng Decision Memo khi cần human quyết. Không xin lại approval đã có.
4. Triển khai lát cắt hành vi xuyên nhóm, kiểm regression các consumers chung (ví dụ Grid/Carousel dùng card CSS).
5. Cập nhật status/evidence từng ID; kế thừa đầy đủ acceptance cũ qua mapping, không bỏ vì đổi bố cục planning.
6. Trước onsite tạo/update docs/testing theo AGENTS với URL/setup/steps/expected/pass-fail/evidence. Build/lint do human chạy theo project workflow.
7. Báo rõ đã làm/chưa kiểm/human cần cung cấp gì; không coi không chạy test là PASS.

## 5. Tài liệu lưu trữ phải tự đọc được

Khi human yêu cầu lưu planning/giải thích, lưu cả rationale và các phân biệt cần để hiểu decision, không chỉ bullet cuối. Ghi ngày, phạm vi human đã chốt, proposal còn mở và proposal bị superseded. Ví dụ media frame khác ảnh nguồn; “contain” khác “cover”; design approval khác evidence thực thi.

Không đưa lịch sử chat không liên quan vào protocol. Với quyết định thay đổi, cập nhật premise trong cùng đợt và giữ trace tới decision mới. Tham chiếu mẫu áp dụng: [planning carousel 040](../../.context/planning/040_VER_1_3_17_CAROUSEL_UI_UX_TECHNICAL_PLANNING.md).