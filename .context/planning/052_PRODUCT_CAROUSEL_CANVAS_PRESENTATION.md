# Product Carousel — canvas presentation follow-up

Ngày: 2026-11-05. **Human approved design direction; chưa implement, chưa runtime PASS.**

Execution override 2026-11-05: human yêu cầu làm ngay. Đã thêm source Product Carousel editor-only trong `controls.tsx` và `style.css`: card trái / overview phải, summary + query giữ fields/states/disclosure, limitation footer, named container query stack ở <=760px. CP-01–05: **Đã có source / chờ kiểm chứng**; các trạng thái chưa implement bên dưới là snapshot trước implementation. TSX syntax parse và CSS PostCSS parse PASS; chưa full typecheck/build/lint/onsite. Runbook CP-01–05 tại `docs/testing/carousel-editor-ux-technical-1.3.18.md`. Không đổi query/schema/frontend render/sidebar hoặc breakpoint Category Grid.

Scope: trình bày preview trên canvas Product Carousel, carry-over Editor UX được tracking trong current V1 / 1.3.19. Không chuyển milestone, bump version, thay query ownership hoặc tự áp thiết kế này cho Post. Sidebar UI không thuộc lượt thiết kế này.

## 1. Vấn đề và rationale

Human đánh giá preview trước khá đẹp nhưng giao diện mới giảm chất lượng trình bày. Source hiện giữ `PlaceholderCard` và thêm khu thông tin/query; chưa có bằng chứng card cũ phải viết lại hoàn toàn. Giữ card mẫu và các consumer thiết kế đã có, cải thiện bố cục/thứ bậc thông tin.

Đề xuất một card nằm giữa canvas bị human bác bỏ: mất thông tin bản trước, kéo dài theo chiều dọc và bỏ trống phần bên phải. Không dùng phương án đó làm implementation contract. Không thay bằng nhiều card giả dễ bị hiểu là dữ liệu thật.

## 2. Bản vẽ được human duyệt

```text
  Featured products
  Optional introduction…

  ┌─────────────────────────┬─────────────────────────────────────────────┐
  │ CARD DESIGN             │ CONTENT SUMMARY                             │
  │                         │                                             │
  │ ┌─────────────────────┐ │ Image       On       Spec chips    On       │
  │ │                     │ │ Tags badge  On       Equal height  On       │
  │ │        IMAGE        │ │ Action      Request quote                   │
  │ │                     │ │                                             │
  │ ├─────────────────────┤ │ ─────────────────────────────────────────── │
  │ │ Product name        │ │ QUERY PREVIEW                    12 fetched │
  │ │                     │ │ Latest products · Fish                      │
  │ │ SIZE                │ │                                             │
  │ │ [100–200] [+3]       │ │ 01 Red Drum          02 Barramundi          │
  │ │                     │ │    Whole GGS            Fillet              │
  │ │ PACK                │ │                                             │
  │ │ [IQF] [+1]           │ │ 03 Pangasius         04 Grouper             │
  │ │                     │ │    Fillet               Whole               │
  │ │ [ Request quote ]   │ │                                             │
  │ └─────────────────────┘ │ 05 Mahi-Mahi         06 Red Snapper         │
  │                         │    Portion              Whole               │
  │ Illustrative sample     │                                             │
  │                         │ Show 6 more                                 │
  ├─────────────────────────┴─────────────────────────────────────────────┤
  │ Query sample uses native REST; some frontend filters and ordering    │
  │ are not reproduced. Frontend results remain authoritative.           │
  └───────────────────────────────────────────────────────────────────────┘
```

Tên sản phẩm, số lượng và summary trong tranh chỉ minh họa vị trí; không hardcode vào sản phẩm. Summary chỉ mô tả cấu hình thật; không ám chỉ query preview đã tái hiện các bộ lọc/thứ tự chưa được hỗ trợ.

## 3. Contract trình bày

Execution053 2026-11-05: root frame bao heading/intro đã có source và presentation được áp các Collection theo contentType/layout; Post dùng sample riêng và note cap5. Giới hạn Product-only của bản052 ban đầu được mở rộng bởi human chấp thuận thực hiện053, không phải thay query ownership. Khung chung/body unframed chờ onsite EF/CP.

Follow-up approved 2026-11-05: [plan053](053_EDITOR_PREVIEW_FRAMES_ROLLOUT.md) + `docs/standards/editor-preview-frame-contract.md` mở rộng chrome để heading/intro nằm **trong cùng khung ngoài**, nhãn Product Carousel và spacing/palette theo family. Phần “heading/intro giữ ở đầu block” dưới đây vẫn đúng vị trí tương đối, nhưng không còn là quyết định để chúng ở ngoài frame. Source052 hiện chưa move heading/intro hoặc áp frame chung. Giữ card/summary/query acceptance, tránh khung lồng khung; Sidebar Design/visual picker là scope khác.

- Heading/intro giữ ở đầu block, chỉnh trực tiếp như hiện có.
- Card bên trái chiếm khoảng 1/3; cột phải nhận phần còn lại. Giữ kích thước card hợp lý, không phóng toàn chiều rộng hoặc dùng fixed height cắt nội dung.
- Cột phải có Content summary và Query preview. Kết quả query thật tách rõ card minh họa; danh sách chia cột theo available width, ban đầu có hạn mức và mở rộng/thu gọn theo contract đã có.
- Giữ trạng thái ảnh, spec chips, tags badge, action/CTA, equal height; không bỏ thông tin chỉ để đẹp hơn. Card vẫn có grouped specs/+N và phản ánh attrs thiết kế hiện có.
- Nền nhẹ thống nhất, khoảng thở, phân cách mảnh; không bọc mỗi thông tin trong một hộp riêng. Product UI English first/i18n.
- Loading/empty/unavailable/error nằm trong vùng query, không che mất card mẫu. Giữ cảnh báo giới hạn native REST/query ở footer, không giấu thông tin có ảnh hưởng đến cách hiểu kết quả.
- Canvas hẹp chuyển một cột theo thứ tự card → summary → query. Không overflow/margin âm/viewport math để ép hai cột.
- Không Swiper/autoplay trong editor; không mở endpoint mới hoặc sửa query/persistence trong task trình bày.

## 4. Acceptance & evidence cần khi implementation

| ID | Nhóm | Hành vi mong đợi / hiện trạng | Cách kiểm / evidence |
|---|---|---|---|
| CP-01 | Editor UX | Hai cột tận dụng canvas rộng, một cột khi hẹp — design approved, chưa implement | Ảnh editor cùng fixture, sidebar mở/đóng và Group/Columns; rect card/info/parent; không overflow |
| CP-02 | Editor UX | Không mất card mẫu, summary fields hoặc query states — chưa implement | So inventory trước/sau; image/specs/tags/action/equal-height toggle; loading/empty/error/unavailable/results |
| CP-03 | Editor UX | Phân biệt dữ liệu minh họa và native REST thật — chưa implement | Label/footer nhìn rõ; không hứa parity filter/order không có source |
| CP-04 | Technical | Không đổi query/save/schema/frontend; resize/disclosure/focus giữ an toàn — chưa implement | Scoped diff; keyboard Show more/less, resize, query change; không mất focus/listeners |
| CP-05 | Block UI | Frontend không đổi bởi task editor-only | So frontend trước/sau trên cùng build/fixture; shared CSS chỉ scoped editor |

A11y: heading hierarchy, label không chỉ dựa vào màu, text dễ đọc, disclosure keyboard/focus đúng. Responsive: title dài/specs nhiều/ảnh off, canvas hẹp và có scrollbar. Performance: không thêm request vì đổi bố cục. Geometry và runtime chưa kiểm; không coi design approval là PASS.

## 5. Tracking/handoff

Baseline source: `src/collection/controls.tsx` (`PlaceholderCard`, `EditorInfoPanel`, `QueryPreview`) và `src/collection/style.css`. Giữ acceptance carry-over trong plan045/MILESTONES; tài liệu này bổ sung hướng trình bày, không đóng debt hoặc thay plan051. User chỉ yêu cầu lưu hướng đã duyệt và commit tồn đọng trong lượt này; chưa code thiết kế canvas.

Trước implementation cập nhật runbook `docs/testing/carousel-editor-ux-technical-1.3.18.md` hoặc runbook current có mapping rõ CP-01–05, URL/build/fixture và evidence. Build/lint do human chạy theo AGENTS.md.
