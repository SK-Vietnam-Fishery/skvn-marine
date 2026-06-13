# SKVN Debug Casebook

`debug-casebook` là hệ thống án lệ kỹ thuật của SKVN Marine.

Mỗi case lưu lại một lỗi đã được điều tra bằng evidence, chuỗi lập luận dẫn đến
root cause, thay đổi tối thiểu đã sửa lỗi, và regression guard ngăn lỗi quay
lại.

Mục tiêu không phải tạo danh sách mẹo sửa nhanh. Mục tiêu là lưu một cách tư
duy có thể tái sử dụng:

```text
Triệu chứng
→ xác định state đúng và state sai
→ đo State Delta
→ thu thập evidence
→ chứng minh root cause
→ áp dụng minimal fix
→ thêm regression guard
```

## Nguyên Tắc

1. Không ghi root cause khi mới chỉ có phỏng đoán.
2. Phân biệt rõ observation, hypothesis và proven evidence.
3. Ưu tiên snapshot DOM, console, network, computed style và geometry.
4. Fix phải tác động vào layer sở hữu lỗi.
5. Không dùng CSS masking, retry hoặc workaround để che nguyên nhân.
6. Mỗi case phải rút ra một nguyên tắc tổng quát có thể dùng cho lỗi khác.
7. Case chưa chứng minh được giữ trạng thái `INVESTIGATING`, không ghi
   `FIXED`.

## Trạng Thái Case

| Status | Ý nghĩa |
|---|---|
| `INVESTIGATING` | Đã có triệu chứng nhưng chưa chứng minh root cause |
| `PROVEN` | Root cause đã được evidence chứng minh |
| `FIXED` | Source fix đã được triển khai và kiểm tra kỹ thuật |
| `REGRESSION_GUARDED` | Đã có test hoặc assertion ngăn lỗi quay lại |
| `ONSITE_VERIFIED` | Human đã xác nhận lỗi không còn trên môi trường thật |
| `SUPERSEDED` | Case hoặc giải pháp đã được case mới thay thế |

Một case có thể mang nhiều trạng thái, ví dụ:

```text
PROVEN · FIXED · REGRESSION_GUARDED
```

Không dùng `ONSITE_VERIFIED` nếu chưa có human evidence.

## Cấu Trúc Case

Mỗi case nên dùng cấu trúc sau:

```markdown
# CASE-XXX — Tên lỗi

## Metadata
## Summary
## Symptoms
## State Delta
## Evidence
## Hypotheses
## Root Cause
## Causal Chain
## Fix
## Regression Guard
## Verification
## General Principle
## Related Files
```

Không bắt buộc mọi section phải dài. Tuy nhiên `State Delta`, `Evidence`,
`Root Cause`, `Fix` và `General Principle` không được bỏ trống.

## Quy Tắc Đặt Tên

```text
<CATEGORY>/<NNN>_<UPPER_SNAKE_CASE_TITLE>.md
```

Ví dụ:

```text
slider/001_SWIPER_JS_WITHOUT_CORE_CSS.md
gutenberg/002_DEPRECATION_MIGRATION_INVALIDATES_SAVED_MARKUP.md
layout/003_DUPLICATE_FULL_WIDTH_OWNERSHIP.md
```

- Số case tăng toàn cục, không reset theo category.
- Không đổi số của case đã được tham chiếu.
- Tên file mô tả failure mode, không chỉ mô tả component.
- Category chỉ phục vụ tìm kiếm; `INDEX.md` là registry chính.

## Workflow Thêm Case

1. Xác nhận case chưa tồn tại trong `INDEX.md`.
2. Ghi triệu chứng bằng ngôn ngữ người dùng nhìn thấy.
3. Xác định State A, State B và Delta.
4. Ghi evidence có thể kiểm tra lại.
5. Tách hypothesis bị loại khỏi root cause đã chứng minh.
6. Mô tả minimal fix và layer sở hữu fix.
7. Ghi verification thực tế, kể cả bước chưa chạy.
8. Thêm regression guard nếu khả thi.
9. Đăng ký case trong `INDEX.md`.

## Quan Hệ Với Tài Liệu Khác

- `docs/decisions/`: quyết định kiến trúc hoặc UX được phê duyệt.
- `docs/testing/`: quy trình và evidence cần human kiểm tra.
- `docs/debug-casebook/`: lỗi cụ thể, cách chứng minh và tiền lệ sửa lỗi.
- `.context/`: active working memory và milestone protocol.

Casebook không thay thế decision document hoặc onsite test checklist.

