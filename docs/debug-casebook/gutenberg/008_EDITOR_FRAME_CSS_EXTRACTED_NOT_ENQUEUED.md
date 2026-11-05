# CASE-008 — Editor frame CSS extracted but not enqueued

## Metadata

- Date: 2026-11-05.
- Category: Gutenberg / editor asset integration.
- Status: **PROVEN · FIXED** — source defect/import correction only; **không có ONSITE_VERIFIED hoặc automated asset regression guard**.
- Scope: plan053 Collection/Category root preview frame, không thay yêu cầu màu đã duyệt.

## Summary / Symptoms

Sau khi di chuyển màu nền/viền từ preview body sang root frame bao cả heading/intro, human thấy label, heading và card/query nhưng mất khung ngoài và màu nền. Tôi chịu trách nhiệm bỏ style owner cũ trước khi verify replacement từ source tới editor. Các test cú pháp/VM spacing đã chạy không kiểm CSS loading và không bắt được regression này.

## State Delta

| State | Visual owner / asset path |
|---|---|
| Trước | Khung body nằm trong `src/collection/style.css`, đi vào stylesheet đang đăng ký |
| Sau refactor lỗi | Body bị gỡ nền/viền; root có class mới nhưng CSS shared được extract sang `build/index.ts.css` chưa đăng ký |
| Source correction | Đưa CSS shared vào file có basename `style.css`, import đúng path; cần human build rồi xác nhận output/handle/editor |

## Evidence

1. `b4dba14`: chuyển source sang shared root frame, xóa background/border/padding frame body. JS có class/label không đồng nghĩa CSS nạp được.
2. Snapshot `05b9cef`: `build/index.ts.css`/RTL có `.skvn-editor-preview-frame`, còn registered `build/style-index.ts.css` không có rules này. Provenance: `docs/testing/editor-preview-frame-build-snapshot-2026-11-05.md`.
3. `wp-content/plugins/skvn-marine-blocks/skvn-marine-blocks.php:86–96` đăng ký editor stylesheet `build/style-index.ts.css`, không có registration `build/index.ts.css` tương ứng snapshot.
4. `c70c4ce`: di chuyển CSS không đổi nội dung từ `src/shared/editor-preview-frame.css` tới `src/shared/editor-preview-frame/style.css`; hook import `./editor-preview-frame/style.css` theo đường extraction đang sử dụng cho các style.css khác.
5. Human screenshot 2026-11-05: Product Carousel root label và heading/intro hiện, khung/nền không hiện. Screenshot là evidence symptom; chưa có Network/computed onsite để khẳng định chính xác asset/cache/cascade trên site sau correction.

Không dùng timestamp hoặc marker trong JS để suy human đã deploy đúng CSS. Lượt đọc local sau screenshot thấy frame rules trong `build/style-index.ts.css`; đó chỉ chứng minh artifact local lúc đọc đã có rule, không phải onsite PASS.

## Hypotheses và root cause

**Proven source integration defect:** CSS được viết và build ra file, nhưng file đó không nằm trong asset graph WordPress đã đăng ký. Import/build output/registration không nối liền. Đây là lỗi pipeline integration, không thiếu màu trong yêu cầu thiết kế.

**Onsite sau source correction:** delivery/cache, rule matching và cascade vẫn cần Network/DOM/computed trong đúng editor document. Chưa đủ bằng chứng để chọn một nguyên nhân onsite mới hoặc tuyên bố đã hết lỗi.

## Causal Chain

```text
Di chuyển khung body → root
→ xóa visual owner cũ
→ CSS root dùng import/extraction path khác
→ tạo CSS file chưa registered/enqueued
→ root class/JS hiện nhưng màu/viền không hiện
→ syntax và VM geometry vẫn PASS vì không kiểm stylesheet delivery
```

## Fix

Source correction `c70c4ce` nối import shared CSS về pipeline `style.css` của stylesheet editor hiện có. Không thêm `!important`, không inline màu trên mọi block, không sửa theme hoặc đổi palette để che lỗi. Không chỉnh minified assets bằng tay. Sau rebuild phải kiểm output thực tế; basename convention là cách sửa đã chọn cho repo này, không được coi là bảo đảm mọi pipeline/thư viện tương lai.

## Regression Guard / Verification

**Chưa có automated guard cho enqueue parity.** Các test hook8/8 chỉ kiểm spacing/cleanup mô phỏng; parseTSX/CSS chỉ kiểm syntax. Không gắn nhãn REGRESSION_GUARDED cho asset delivery dựa trên các test đó.

Check bắt buộc cho lần sửa visual owner tiếp theo:

1. Trước sửa: ghi owner DOM/CSS hiện tại và registered handle/path.
2. Replacement phải có import → output CSS thực tế → WordPress handle/path → target editor document/iframe trong cùng lát cắt thay đổi. Không bỏ owner cũ rồi để enqueue cho “bước sau”.
3. Sau human build: search selector trong **file được đăng ký**, không chỉ search toàn build folder. Xác nhận JS marker và CSS rule cùng build; old/new artifacts phân biệt rõ.
4. Onsite: inspect root `.skvn-editor-preview-frame`, Network stylesheet URL/version, computed background/border/padding và matched rules; chụp cả heading/intro/frame. Kiểm Carousel và Grid, sidebar mở/đóng, nested/hẹp theo EF01–05.
5. Khi chưa có delivery/computed evidence, báo source/artifact status và phần còn thiếu; không tuyên bố visual PASS hoặc đoán cache. Nếu fail, xử lý layer chứng minh bị lỗi trước khi thêm specificity.

Runbook: `docs/testing/editor-preview-frames-1.3.19.md`. Onsite evidence và regression guard bổ sung sau; không đổi status chỉ vì đã commit.

## General Principle

**CSS tồn tại trong source hoặc build không có nghĩa nó được tải vào nơi cần hiển thị.** Thay visual owner phải atomic từ source tới asset graph, và kiểm runtime phải đúng loại bằng chứng. Agent phải chịu trách nhiệm integration, không dùng thiếu onsite evidence để giảm mức độ lỗi đã gây ra.
