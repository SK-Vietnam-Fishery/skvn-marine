# Onsite Test Debt Checklist

Use this file to report all pending onsite/manual tests in one place.

Result format:

```text
P = pass
F = fail
```

Do not test on local WordPress unless explicitly requested.

## Environment

```text
Tester:
Date:
Onsite domain:
Browser:
Device/viewport:
Notes:
```

## 0.2.0 Design System / Homepage Test Debt

1. Tính năng: Homepage test page assembly
Hành vi mong muốn: Tester tạo hoặc mở homepage test page, insert pattern `wp-content/themes/skvn-marine/patterns/homepage-test.php` hoặc pattern tương ứng trong editor, page render được bằng content/images placeholder.
Kết quả: P / F

2. Tính năng: Homepage desktop visual review
Hành vi mong muốn: Desktop layout không vỡ, section spacing ổn, heading/button/card không overlap.
Kết quả: P / F

3. Tính năng: Homepage mobile visual review
Hành vi mong muốn: Mobile stack sạch, không horizontal scroll, text/button không tràn container.
Kết quả: P / F

4. Tính năng: Runtime smoke
Hành vi mong muốn: Page load không trắng màn hình, không PHP fatal, không console error nghiêm trọng.
Kết quả: P / F

## 0.3.0 Slider Runtime Test Debt

5. Tính năng: Slider test page setup
Hành vi mong muốn: Tester tạo page `Slider Runtime Test 0.3.0`, insert `skvn-marine/slider` với ít nhất 3 `skvn-marine/slide` children.
Kết quả: P / F

6. Tính năng: Slider frontend initialization
Hành vi mong muốn: Frontend slider khởi tạo đúng, slide render trong Swiper, không trắng hoặc stacked sai ở frontend.
Kết quả: P / F

7. Tính năng: Slider arrows
Hành vi mong muốn: Arrows hiển thị khi bật và chuyển slide đúng.
Kết quả: P / F

8. Tính năng: Slider dots
Hành vi mong muốn: Dots/pagination hiển thị khi bật và chuyển đúng theo slide active.
Kết quả: P / F

9. Tính năng: Slider keyboard navigation
Hành vi mong muốn: Khi focus trong slider, keyboard navigation hoạt động đúng, không kẹt focus.
Kết quả: P / F

10. Tính năng: Slider hover pause
Hành vi mong muốn: Autoplay tạm dừng khi hover và tiếp tục hợp lý khi rời hover.
Kết quả: P / F

11. Tính năng: Slider reduced motion
Hành vi mong muốn: Khi bật prefers-reduced-motion, autoplay không gây chuyển động tự động gây khó chịu.
Kết quả: P / F

12. Tính năng: Slider console/runtime
Hành vi mong muốn: Không có console error nghiêm trọng liên quan Swiper hoặc block runtime.
Kết quả: P / F

## 0.4.0 Woo Product Sections Test Debt

13. Tính năng: Woo product sections test page
Hành vi mong muốn: Tester tạo page `Woo Product Sections Test 0.4.0`, insert `SKVN Woo Category Strip` và `SKVN Woo Product Grid`.
Kết quả: P / F

14. Tính năng: Woo categories render
Hành vi mong muốn: Category strip render category thật từ WooCommerce, không empty bất thường nếu site có category data.
Kết quả: P / F

15. Tính năng: Woo products render
Hành vi mong muốn: Product grid render product thật từ WooCommerce, image/title/price/CTA không vỡ layout.
Kết quả: P / F

16. Tính năng: Product card mobile CTA
Hành vi mong muốn: Mobile CTA `Request a Quote` luôn visible, không chỉ hiện khi hover.
Kết quả: P / F

17. Tính năng: Woo block save/reload
Hành vi mong muốn: Sau save/reload editor, không có invalid block warning.
Kết quả: P / F

## 0.6.0 Quote UI Path Test Debt

18. Tính năng: Product listing CTA
Hành vi mong muốn: Product card CTA text là `Request a Quote`, visible desktop/mobile, URL là `/request-a-quote/?product_id=PRODUCT_ID`.
Kết quả: P / F

19. Tính năng: Product listing CTA behavior
Hành vi mong muốn: Click CTA mở same-site Request Quote page, giữ `product_id` trong URL, không trigger add-to-cart.
Kết quả: P / F

20. Tính năng: Single product CTA
Hành vi mong muốn: Single product primary CTA là `Request a Quote`, URL là `/request-a-quote/?product_id=PRODUCT_ID`, không submit add-to-cart.
Kết quả: P / F

21. Tính năng: Request Quote page with valid product_id
Hành vi mong muốn: `/request-a-quote/?product_id=PRODUCT_ID` load không fatal, visual surface readable.
Kết quả: P / F

22. Tính năng: Request Quote page with invalid product_id
Hành vi mong muốn: `/request-a-quote/?product_id=999999` không làm vỡ page.
Kết quả: P / F

23. Tính năng: Request Quote page without query
Hành vi mong muốn: `/request-a-quote/` load được, missing `product_id` không làm trắng màn hình hoặc 404.
Kết quả: P / F

24. Tính năng: Request Quote editor stability
Hành vi mong muốn: Mở Request Quote page trong editor, edit text, save/reload, không invalid block warning.
Kết quả: P / F

25. Tính năng: Quote UI desktop visual
Hành vi mong muốn: Desktop quote UI readable, spacing ổn, không nested-card rối, CTA rõ.
Kết quả: P / F

26. Tính năng: Quote UI mobile visual
Hành vi mong muốn: Mobile quote UI stack sạch, không horizontal scroll, button text vừa container.
Kết quả: P / F

27. Tính năng: Quote CTA accessibility smoke
Hành vi mong muốn: CTA tab được bằng keyboard, focus visible, accessible name có quote intent.
Kết quả: P / F

## 0.7.x / 1.1.2 Quote Flow Runtime Test Debt

Status note:

```text
2026-06-05: Human reported that the CF interface has been tested onsite. Remaining untested debt is data flow: filled submission, CFDB7 storage, visible/hidden field storage, product/product-card/page-block origin query params, and success/thank-you behavior. Target milestone: V1 / 1.1.2.
```

28. Tính năng: CF7 plugin active
Hành vi mong muốn: Contact Form 7 active trên onsite site.
Kết quả: P / F

29. Tính năng: CFDB7 plugin active
Hành vi mong muốn: CFDB7 active trên onsite site.
Kết quả: P / F

30. Tính năng: CF7 Request a Quote form exists
Hành vi mong muốn: WP Admin > Contact có form Request a Quote.
Kết quả: P

31. Tính năng: CF7 form project classes
Hành vi mong muốn: Form wrapper có `skvn-form` và `skvn-quote-form`; submit button có `skvn-button` và `skvn-button--primary`.
Kết quả: P

32. Tính năng: CF7 visible required fields
Hành vi mong muốn: Form có `full_name`, `company_name`, `email`, `country`, `product_interest`, `quantity`, `message`.
Kết quả: P

33. Tính năng: CF7 optional visible fields
Hành vi mong muốn: Form có hoặc ghi rõ mismatch cho `phone`, `destination_port`, `packaging_requirement`.
Kết quả: P / F

34. Tính năng: CF7 hidden/context fields
Hành vi mong muốn: Form có `product_id`, `product_sku`, `product_name`, `product_url`, `source_url`, `utm_source`, `utm_medium`, `utm_campaign`, `utm_content`, `utm_term`.
Kết quả: P / F

35. Tính năng: Request Quote page embeds CF7
Hành vi mong muốn: `/request-a-quote/` tồn tại và chứa đúng CF7 shortcode/block.
Kết quả: P

36. Tính năng: Request Quote frontend form render
Hành vi mong muốn: Frontend render CF7 form, form nhìn theo SKVN style, không plain unstyled CF7 defaults.
Kết quả: P

37. Tính năng: Thank-you page exists
Hành vi mong muốn: `/quote-thank-you/` tồn tại, không 404/fatal, message phù hợp sau submit quote.
Kết quả: P / F

38. Tính năng: Required-field validation
Hành vi mong muốn: Bỏ trống required fields thì CF7 báo validation rõ ràng, không overlap field.
Kết quả: P / F

39. Tính năng: Filled quote submission
Hành vi mong muốn: Submit test data thành công, user thấy success message hoặc được đưa tới `/quote-thank-you/`.
Kết quả: P / F

40. Tính năng: CFDB7 stores submission
Hành vi mong muốn: CFDB7 có row cho test submission, có submission time/ID rõ.
Kết quả: P / F

41. Tính năng: CFDB7 stores visible fields
Hành vi mong muốn: Row CFDB7 lưu đúng các visible field đã submit.
Kết quả: P / F

42. Tính năng: CFDB7 stores hidden/context fields
Hành vi mong muốn: Row CFDB7 có hidden/context fields; nếu thiếu thì ghi rõ field nào thiếu.
Kết quả: P / F

43. Tính năng: No n8n webhook exposed
Hành vi mong muốn: Page content/source/admin-visible public output không expose n8n webhook URL hoặc secret.
Kết quả: P / F

44. Tính năng: Quote flow console/log
Hành vi mong muốn: Không có console error nghiêm trọng liên quan CF7/theme scripts; không có PHP fatal.
Kết quả: P / F

45. Tính năng: Quote flow desktop/mobile screenshots
Hành vi mong muốn: Tester gửi desktop screenshot, mobile screenshot, CF7 result/thank-you screenshot, CFDB7 row/detail screenshot.
Kết quả: P / F

## 0.8.0 SKVN Accordion Editor Controls Test Debt

Ghi chú sử dụng: Gutenberg có thể hiện panel native tên `Styles` với `Default`, `SKVN Section`, `SKVN Card`. Panel đó không phải control chính cần test trong phase này. Với `SKVN Accordion`, tester cần chọn đúng block và test các panel riêng: `Content`, `Style`, `Layout`, `Advanced`.

46. Tính năng: Insert SKVN Accordion block
Hành vi mong muốn: Tester insert được block `SKVN Accordion` vào page draft/private.
Kết quả: P / F

47. Tính năng: Content control
Hành vi mong muốn: Sidebar có nhóm `Content`, cho sửa Heading, save/reload editor vẫn giữ đúng heading.
Kết quả: P / F

48. Tính năng: Style control
Hành vi mong muốn: Sidebar có nhóm `Style`, Tone có `Default`, `Fresh`, `Trust`, `Navy`; đổi preset thì editor và frontend đổi style tương ứng.
Kết quả: P / F

49. Tính năng: Layout control - Spacing
Hành vi mong muốn: Sidebar có nhóm `Layout`, Spacing có `Small`, `Medium`, `Large`; đổi preset thì padding thay đổi rõ, không cần nhập px/rem.
Kết quả: P / F

50. Tính năng: Layout control - Width
Hành vi mong muốn: Width có `Content`, `Wide`, `Full`; đổi preset thì độ rộng block thay đổi đúng trên frontend.
Kết quả: P / F

51. Tính năng: Advanced control
Hành vi mong muốn: Sidebar có nhóm `Advanced`, ARIA label lưu được sau save/reload và không làm frontend lỗi.
Kết quả: P / F

52. Tính năng: Accordion save/reload stability
Hành vi mong muốn: Sau khi đổi Heading, Tone, Spacing, Width, ARIA label rồi save/reload, block không hiện invalid block warning.
Kết quả: P / F

53. Tính năng: Accordion frontend visual parity
Hành vi mong muốn: Frontend hiển thị đúng các preset đã chọn trong editor.
Kết quả: P / F

54. Tính năng: Accordion mobile layout
Hành vi mong muốn: Trên mobile, accordion không bị tràn ngang, text không overlap, spacing nhìn ổn.
Kết quả: P / F

55. Tính năng: Raw input governance
Hành vi mong muốn: Tester không cần nhập raw class, raw hex/rgb/hsl, custom CSS, custom JS, hoặc spacing tự do.
Kết quả: P / F

56. Tính năng: Accordion console/editor errors
Hành vi mong muốn: Không có lỗi console nghiêm trọng; editor save/update bình thường.
Kết quả: P / F

## Evidence To Send Back

```text
Tester:
Date:
Onsite domain:
Overall result: P / F
Failed item numbers:
Screenshots attached:
Console errors:
PHP/log errors:
Notes:
```
