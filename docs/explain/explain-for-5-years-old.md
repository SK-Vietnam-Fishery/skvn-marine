# Explain For 5 Years Old

## Status

UI test hiện tại: **FAIL / chưa đạt visual**.

Lý do: trang test đã có code và component, nhưng hình nhìn trong browser chưa giống reference. Nó vẫn bị ảnh hưởng bởi layout mặc định của WordPress/GeneratePress ở runtime, và chưa được kiểm bằng screenshot chuẩn desktop/mobile.

File này ghi lại đã làm gì, tại sao làm vậy, cái gì còn phải test, và test như thế nào.

---

## Nói đơn giản

Hãy tưởng tượng website là một cái hộp đồ chơi.

- WordPress là cái bàn để bày đồ chơi.
- GeneratePress là cái khung bàn có sẵn.
- `skvn-marine` là bộ màu, miếng dán, và cách sắp đồ chơi riêng của mình.
- Pattern là các cụm đồ chơi đã ráp sẵn để sau này đặt vào trang nhanh hơn.

Phase 0.2.0 chưa phải làm trang chủ hoàn chỉnh. Phase này là làm mấy miếng Lego nền trước:

- màu chính,
- nút,
- card,
- section,
- trust strip,
- newsletter band,
- footer baseline,
- homepage test pattern.

Nhưng khi đem mấy miếng đó đặt thử lên bàn WordPress, cái bàn có thể vẫn còn đồ mặc định như sidebar, title, widget nếu page setup sai. Vì vậy test UI phải setup đúng trước khi kết luận pass/fail.

Hiện tại dự án đang ở 0.4.0 để làm Woo Product Sections. Test visual/runtime của 0.2.0 và 0.3.0 vẫn được ghi nợ rõ ràng.

---

## Đã Làm Được Gì

### 1. Design tokens

File:

```text
wp-content/themes/skvn-marine/theme.json
wp-content/themes/skvn-marine/style.css
```

Đã tạo màu nền tảng cho SKVN:

```text
skvn-blue-950
skvn-blue-900
skvn-blue-700
skvn-teal-600
skvn-mint-100
skvn-gold-300
skvn-sky-50
skvn-slate-700
skvn-white
```

Tại sao làm:

Website cần một bộ màu chung. Nếu không có token, mỗi section sẽ tự chọn màu lung tung, sau này sửa rất cực.

### 2. Layout CSS cơ bản

File:

```text
wp-content/themes/skvn-marine/style.css
```

Đã thêm class nền:

```text
skvn-container
skvn-section
skvn-button
skvn-button--primary
skvn-card
skvn-support-card
skvn-premium-accent
skvn-site-header
skvn-site-footer
```

Tại sao làm:

Các pattern cần ngôn ngữ chung. Section nào cũng phải biết container rộng bao nhiêu, nút bo góc ra sao, card có shadow thế nào.

### 3. Block styles

File:

```text
wp-content/themes/skvn-marine/inc/block-styles.php
```

Đã register style:

```text
SKVN Primary button
SKVN Section group
SKVN Card group
SKVN Rounded Media image
```

Tại sao làm:

Để trong editor có thể chọn style cho core block mà không cần custom block riêng.

### 4. Starter pattern

File:

```text
wp-content/themes/skvn-marine/patterns/section-intro.php
```

Pattern này là section đơn giản gồm heading + paragraph.

Tại sao làm:

Nó là viên gạch nhỏ nhất để test spacing, typography, section wrapper.

### 5. Trust feature strip

File:

```text
wp-content/themes/skvn-marine/patterns/trust-strip.php
```

Pattern có 4 item:

```text
Fresh from the Coast
Clear Origin
Export Packaging
Quality Checked
```

Tại sao làm:

Reference có dải lợi ích/tin cậy nằm gần footer hoặc dưới hero. Đây là phần dùng lại nhiều, nên làm thành pattern.

### 6. Newsletter signup band

File:

```text
wp-content/themes/skvn-marine/patterns/newsletter-band.php
```

Pattern có:

```text
Title
Description
CTA button
Replaceable Image block
```

Không có custom form handler.

Tại sao làm:

Theo protocol, theme không tự xử lý form. Nếu sau này cần submit thật thì dùng CF7 hoặc form plugin đúng scope.

Image dùng core Image block để user thay ảnh trong editor được.

### 7. Header/footer baseline

File:

```text
wp-content/themes/skvn-marine/style.css
wp-content/themes/skvn-marine/patterns/site-footer.php
```

Đã thêm CSS baseline cho GeneratePress header và một footer pattern dùng lại.

Tại sao làm:

Header/footer là khung ngoài của site. Nếu chỉ test section ở giữa mà không có header/footer thì trang nhìn rời rạc và khó đánh giá giống website thật.

Lưu ý:

Đây là baseline code. Nó chưa được tính là visual-approved cho tới khi page runtime được chụp desktop/mobile và human review.

### 8. Homepage test pattern

File:

```text
wp-content/themes/skvn-marine/patterns/homepage-test.php
```

Đã tạo một pattern gom các phần chính để test layout:

```text
Hero
Trust strip
Why choose us
Factory standards
Process cards
Newsletter band
Footer direction
```

Tại sao làm:

Thay vì test từng mảnh riêng lẻ, pattern này cho phép đặt cả một trang test vào WordPress editor rồi nhìn tổng thể như một homepage.

### 9. Encoding guardrail

File:

```text
.editorconfig
tests/context-encoding.test.mjs
```

Đã thêm rule UTF-8 và test bắt lỗi mojibake trong context/docs.

Tại sao làm:

Repo có tiếng Việt. Nếu tool đọc/ghi sai encoding thì chữ sẽ vỡ. Test này giúp phát hiện sớm trước khi commit.

### 10. Slider/Slide block source-level

File:

```text
wp-content/plugins/skvn-marine-blocks/src/slider/
wp-content/plugins/skvn-marine-blocks/src/slide/
wp-content/plugins/skvn-marine-blocks/skvn-marine-blocks.php
docs/decisions/slider-block.md
tests/slider-block.test.mjs
```

Đã làm:

```text
Slider block
Slide child block
Swiper frontend runtime
Stacked editor preview
Keyboard navigation
Pause on hover
Reduced-motion autoplay guard
Guard JSON config parsing
Swiper dependency rationale
```

Tại sao làm:

Slider có logic tương tác nên thuộc plugin, không thuộc theme. Theme đổi đi thì block logic vẫn phải còn.

Lưu ý:

Phần này đã pass source/build test. Runtime smoke trong WordPress thật vẫn là test debt.

### 11. Woo product sections

File:

```text
wp-content/themes/skvn-marine/patterns/woo-product-grid.php
wp-content/themes/skvn-marine/patterns/woo-category-strip.php
wp-content/themes/skvn-marine/style.css
tests/woo-product-sections.test.mjs
```

Đã làm:

```text
SKVN Woo Product Grid pattern
SKVN Woo Category Strip pattern
WooCommerce Product Collection/Product Template direction
Product card visual styling
Product category strip styling
Mobile-visible product CTA
No custom skvn-marine/product-grid block
No custom skvn-marine/product-list block
```

Tại sao làm:

Milestone 0.4.0 đã quyết định dùng WooCommerce native blocks/patterns trong V1. Nghĩa là sản phẩm, category, price, button vẫn do WooCommerce quản lý. Theme chỉ làm layout và visual.

Lý do không tạo custom product block:

```text
Ít logic hơn
Ít rủi ro hơn
Dễ test hơn
Không phá boundary theme/plugin
Phù hợp quyết định V1 đã resolve
```

Lưu ý:

Phần này đã pass source-level test. Chưa được tính runtime pass cho tới khi insert pattern vào WordPress page có sản phẩm WooCommerce thật.

---

## Đã Test Gì

### Có test pass

File:

```text
tests/theme-baseline.test.mjs
```

Test này kiểm tra:

```text
theme.json có màu cần thiết
không dùng font size clamp/vw
style.css có class nền
block-styles.php có register style
pattern files tồn tại
trust strip có 4 item
newsletter có image block
newsletter không có form handler custom
homepage test pattern tồn tại
footer pattern tồn tại
```

Ý nghĩa:

Đây là **structure/smoke test**. Nó chứng minh file có đúng contract cơ bản.

Nó **không chứng minh UI nhìn đúng**.

File:

```text
tests/slider-block.test.mjs
```

Test này kiểm tra:

```text
Slider/Slide block namespace đúng
Slider cho phép Slide child block
Attributes slider tồn tại
Editor không initialize Swiper
Frontend có reduced-motion guard
Keyboard navigation bật
Pause on hover bật
Swiper dependency có rationale
PHP đăng ký đúng file build
```

File:

```text
tests/woo-product-sections.test.mjs
```

Test này kiểm tra:

```text
Woo product grid pattern tồn tại
Woo category strip pattern tồn tại
Pattern dùng Woo native Product Collection/Product Template
Pattern dùng Woo native Product Categories
CSS product card tồn tại
CTA mobile luôn visible
Không tạo custom product-grid/product-list block trong V1
```

### PHP syntax pass

Đã từng chạy `php -l` cho:

```text
functions.php
block-styles.php
section-intro.php
trust-strip.php
newsletter-band.php
site-footer.php
homepage-test.php
woo-product-grid.php
woo-category-strip.php
```

Ý nghĩa:

PHP không bị lỗi cú pháp.

Nó **không chứng minh UI đẹp**.

---

## Cái Gì Chưa Đạt

### 1. UI test page chưa đạt

Trang:

```text
http://localhost:8080/pattern-ui-test-0-2-0/
```

Tình trạng:

```text
FAIL visual
```

Lý do:

```text
Page ban đầu còn default GeneratePress title
Page ban đầu còn sidebar/widgets
Layout không phải full-width test surface
Chưa có screenshot desktop/mobile làm bằng chứng
Chưa match reference cụ thể
```

### 2. Test hiện tại chưa phải visual regression

Test Node hiện tại chỉ đọc file.

Nó không mở browser, không chụp screenshot, không so bố cục.

Vì vậy không được gọi là UI pass.

### 3. Header/footer chưa visual-approved

Header/footer baseline đã có code, nhưng chưa được duyệt bằng browser screenshot.

Vì vậy nếu page test trông chưa giống một site thật thì không được tick pass. Phải setup đúng page, chụp desktop/mobile, rồi mới kết luận.

### 4. Woo product sections chưa runtime-approved

Pattern Woo đã có source code và test file pass, nhưng chưa insert vào page runtime có WooCommerce products thật.

Chưa được tick runtime pass cho tới khi:

```text
WooCommerce active
Có sản phẩm thật hoặc sample product
Insert SKVN Woo Product Grid
Insert SKVN Woo Category Strip
Chụp desktop/mobile
Kiểm tra CTA mobile visible
Kiểm tra không invalid block
```

---

## Vì Sao Làm Theo Cách Này

### Không tạo custom block

Protocol nói V1 ưu tiên:

```text
Core Gutenberg blocks
Theme patterns
Theme CSS
WooCommerce native blocks
```

Nên trust strip và newsletter được làm bằng pattern trước.

Lý do:

Nếu tạo custom block sớm, sẽ tăng logic, tăng build/test, tăng rủi ro. Pattern đủ cho visual section đơn giản.

### Không tạo custom product grid/list block trong 0.4.0

Protocol và resolved tension nói V1 dùng WooCommerce native blocks/patterns.

Nên 0.4.0 làm:

```text
Theme pattern
Theme CSS
WooCommerce native blocks
```

Không làm:

```text
skvn-marine/product-grid custom block
skvn-marine/product-list custom block
Custom SQL query
Custom product CPT
```

Lý do:

Product data thuộc WooCommerce. Theme chỉ nên quyết định nó nhìn như thế nào.

### Không làm form handler trong newsletter

Protocol cấm custom-code quote/form handler trong theme.

Nên newsletter band chỉ có CTA placeholder.

Khi cần form thật, dùng CF7 hoặc form stack đã quyết định.

### Không sửa GeneratePress parent

Protocol cấm sửa parent theme.

Mọi thay đổi nằm trong:

```text
wp-content/themes/skvn-marine/
```

---

## Quy Tắc Test Đúng Từ Giờ

Một UI test chỉ được coi là pass khi có đủ:

```text
[ ] Page no-sidebar
[ ] Page full-width content
[ ] Default page title hidden nếu đang test custom hero
[ ] Không có Search widget
[ ] Không có Recent Posts
[ ] Không có Recent Comments
[ ] Desktop screenshot checked
[ ] Mobile screenshot checked
[ ] So với reference cụ thể
[ ] Ghi rõ phần giống và phần khác
```

Nếu thiếu screenshot:

```text
Không được gọi là UI pass.
Chỉ được gọi là smoke pass.
```

---

## Hướng Làm Tiếp

Khi xử lý nợ test của phase 0.2.0, thứ tự nên là:

```text
1. Bật đúng WordPress runtime.
2. Tạo page test full-width, no-sidebar, no default title nếu theme/page setting cho phép.
3. Insert pattern: SKVN Homepage Test Layout.
4. Chụp desktop screenshot.
5. Chụp mobile screenshot.
6. So với reference và checklist trong docs/testing/frontpage-testing.md.
7. Nếu đạt mới tick homepage/runtime smoke test.
```

Không nên tick thêm visual acceptance chỉ bằng code/file test.

Khi test phase 0.4.0, thứ tự nên là:

```text
1. Bật đúng WordPress runtime.
2. Xác nhận WooCommerce active.
3. Tạo hoặc import vài product test có ảnh, tên, giá, category.
4. Tạo page: Woo Product Sections Test 0.4.0.
5. Insert pattern: SKVN Woo Category Strip.
6. Insert pattern: SKVN Woo Product Grid.
7. Publish/preview page.
8. Kiểm tra desktop: grid 4 cột nếu đủ rộng, card đều, CTA rõ.
9. Kiểm tra mobile: card stack sạch, CTA luôn visible, không cần hover.
10. Kiểm tra console: không có serious JS error.
11. Chụp desktop/mobile screenshot.
12. Nếu đạt mới tick runtime smoke test.
```

---

## Current Milestone Note

Milestone 0.2.0 đã được carry forward với test debt. Các phần đã làm ở mức source/code:

```text
Theme tokens reviewed/refined
theme.json reviewed/refined
Base typography/layout implemented
Block styles implemented
Starter patterns implemented
Trust feature strip reusable pattern implemented
Newsletter signup band reusable pattern implemented
Header/footer baseline implemented
Homepage test pattern implemented
Encoding guardrail implemented
```

Nhưng cần hiểu đúng:

```text
Các mục này đã pass mức code/structure.
Chưa pass mức visual UI.
```

UI test page hiện tại phải được xem là:

```text
Source pattern created.
Runtime/page-editor visual test pending.
Do not mark as UI pass until screenshots are reviewed.
```

0.3.0 đã được carry sang 0.4.0 với test debt:

```text
Slider Runtime Smoke Test pending
Human 0.3.0 clean approval pending
```

0.4.0 hiện có các mục đã pass mức source/code:

```text
V1 decision: use WooCommerce native blocks/patterns
Woo native product patterns created/styled
Product card visual styling implemented
Mobile CTA remains visible
No custom Product Grid/List blocks in V1
```

0.4.0 còn thiếu:

```text
Runtime smoke test passed
Human approves milestone completion
```

Current milestone:

```text
V1 / 0.4.0 — Woo Product Sections
```
