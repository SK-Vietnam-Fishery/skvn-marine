# Carousel arrow centering — V1 / 1.3.18

2026-10-20. Status: **Đã có source / Chờ kiểm chứng onsite**. Scope chỉ căn giữa icon prev/next; không sửa skeleton, autoplay, Editor UX hoặc vị trí/kích thước nút.

## Source diagnosis

State A mong đợi: icon ở tâm nút. State B human báo: icon lệch tâm. Source cũ dùng glyph ‹/›, flex-center/line-height1 nhưng không reset button padding; theme đặt font UI cho button. Glyph phụ thuộc font metrics. Chưa có computed styles onsite để khẳng định padding là nguyên nhân duy nhất; GeneratePress runtime CSS không có trong checkout.

Thay glyph bằng SVG20×20, viewBox24×24, hai path có bounds tâm(12,12). Scoped padding0/border-box; SVG block không shrink. Giữ nút2.5rem, header position, classes, labels, hover/focus/disabled và runtime. SVG aria-hidden/focusable=false; accessible name vẫn ở button. Product/Post dùng chung header; Hero không thay. Block UI: icon geometry; Editor UX: N/A; Technical: giữ semantics/navigation hooks, không thêm listener/request.

## Target/setup/steps

- Human điền Product URL/Post URL và build/commit/browser/theme. Fixture ≥6 items, arrows bật; deploy build mới và refresh HTML/CSS.
- So trước/sau desktop1280, tablet768, mobile375; default/hover/focus-visible, disabled nếu có; zoom200%. Expected: icon cân tâm, button vẫn2.5rem, vị trí không đổi.
- Tab, Enter/Space, mouse/touch từng nút: next/prev đúng collection, không tab vào SVG; hai collection cùng trang không điều khiển lẫn nhau.
- Dùng snippet bên dưới: PASS khi |dx|/|dy| ≤0.5 CSS px, icon20×20, padding0. Nếu fail, gửi winning CSS selector và computed styles. Không thêm important để che lỗi chưa đo.
- Evidence: URL/build, screenshots trước/sau, rect/computed output, video thao tác, console và PASS/FAIL từng state. Không kết luận onsite PASS bằng source check. Skeleton không thuộc nghiệm thu này.

```js
[...document.querySelectorAll('.skvn-collection__arrow')].map(button => {
  const b = button.getBoundingClientRect();
  const i = button.querySelector('svg')?.getBoundingClientRect();
  return { label: button.getAttribute('aria-label'), button: b.toJSON(),
    icon: i?.toJSON(), padding: getComputedStyle(button).padding,
    dx: i && i.x + i.width / 2 - b.x - b.width / 2,
    dy: i && i.y + i.height / 2 - b.y - b.height / 2 };
});
```

## Human build/lint

```bash
source /home/shinkuro/.nvm/nvm.sh && nvm use 20 && cd /mnt/d/Github/skvn-marine/wp-content/plugins/skvn-marine-blocks && npm run build && find modules/ -name "*.php" -exec php -l {} \;
```

Agent chưa chạy build/lint/onsite; không bump/commit. Sau evidence human, chỉ xử lý regression thuộc icon centering.
