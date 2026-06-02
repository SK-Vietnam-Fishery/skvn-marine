# Footer Builder — Planning

File: `005_VERSION_2_0_0_FOOTER_BUILDER_PLANNING.md`
Topic: Footer Builder — từ Settings Page (C) → Block Type (A) → Element System (B)
Status: Future Candidate / planning only — chưa có human xác nhận version target chính thức

---

## Tổng quan

Footer editable trong Gutenberg được triển khai theo ba giai đoạn độc lập.
Mỗi giai đoạn là một working product — không có trạng thái dở dang.

Trigger chuyển sang giai đoạn B (Element System) là khi theme `skvn-marine` không còn là
child theme của GeneratePress nữa. Lúc đó `generate_footer` hook biến mất, rendering engine
phải được rebuild anyway — đó là thời điểm justification cho B rõ ràng nhất.

```
Giai đoạn C  →  Giai đoạn A  →  [trigger: bỏ GP]  →  Giai đoạn B
V1 scope         V1 hoặc V2                              V2.0.0+
```

---

## Giai đoạn C — Settings Page chọn Footer Template

### Target version

V1 — có thể ship trong 0.6.0 hoặc 0.7.0 tùy priority.
Chưa có human xác nhận slot version cụ thể → ghi `Future Candidate / V1`.

### Vấn đề giải quyết

Marketing cần edit footer trong Gutenberg editor mà không cần GP Premium Elements.

### Cách hoạt động

```
Plugin: Settings page → lưu option skvn_footer_page_id
Theme:  inc/footer.php → query page đó → render vào generate_footer hook
Marketing: tạo WP Page "Footer Template" → edit bằng Gutenberg bình thường
```

### Files cần tạo / sửa

```
Plugin:
  skvn-marine-blocks.php           — đăng ký settings page, register_setting()
  src/settings/footer-settings.php — settings page UI (PHP, admin-only)

Theme:
  inc/footer.php                   — query skvn_footer_page_id → render nội dung
  functions.php                    — require inc/footer.php
```

### Ownership boundary

```
Plugin owns:  admin settings page, option key skvn_footer_page_id
Theme owns:   generate_footer hook, rendering logic, skvn-* CSS cho footer
```

### Invariants

- Không edit GeneratePress parent.
- Option key phải dùng prefix `skvn_` — không dùng `wp_` hay bare name.
- Settings page phải check `current_user_can('manage_options')`.
- Nếu `skvn_footer_page_id` empty hoặc page không tồn tại → fallback về GP default footer, không lỗi.
- Không cache footer content nếu page có shortcode hoặc dynamic block.

### Acceptance checklist

```
[ ] Settings page hiện trong wp-admin, chỉ admin thấy
[ ] Chọn page → footer frontend thay đổi đúng
[ ] Fallback hoạt động khi chưa chọn page
[ ] Footer page editable trong Gutenberg với skvn-* blocks bình thường
[ ] Không conflict với page-display controls (hide footer meta vẫn hoạt động)
[ ] PHP syntax ok, sanitize/escape đầy đủ
```

---

## Giai đoạn A — Block Type `skvn-marine/footer-area`

### Target version

V1 hoặc V2 tùy khi nào marketing cần guardrails trong editor.
Chưa có human xác nhận → ghi `Future Candidate`.

### Vấn đề giải quyết

Giai đoạn C cho phép bất kỳ block nào trong footer page → marketing có thể vô tình
xóa cấu trúc hoặc thêm block không phù hợp. Giai đoạn A thêm một block wrapper
với locked structure và sidebar controls rõ ràng.

### Cách hoạt động

```
Footer template page chỉ chứa một block skvn-marine/footer-area.
Block này có InspectorControls: chọn số cột, màu nền, hiện/ẩn copyright bar.
save.tsx emit HTML structure → theme CSS render đúng.
Theme PHP vẫn query page và render vào generate_footer — không thay đổi.
```

### Điểm khác biệt so với C

```
C: mọi block đều được trong footer page
A: footer page có một root block skvn-marine/footer-area
   → editor locked, marketing không break structure
   → sidebar controls thay raw class input
```

### Data compatibility với C

Data vẫn là `post_content` của WP Page → không cần migrate.
Upgrade từ C sang A: marketing chỉ cần wrap nội dung cũ vào `skvn-marine/footer-area` block.

### Files cần tạo / sửa

```
Plugin:
  src/footer-area/block.json    — metadata, attributes: columns, bgColor, showCopyright
  src/footer-area/edit.tsx      — editor UI với InspectorControls
  src/footer-area/save.tsx      — saved markup với skvn-footer-area class
  src/index.ts                  — đăng ký block

Theme:
  style.css                     — CSS cho .skvn-footer-area, columns, responsive
  (inc/footer.php không đổi)
```

### Invariants

- Block namespace phải là `skvn-marine/footer-area` — không đổi.
- Editor không được set `opacity: 0` cho bất kỳ element nào trong block.
- Keyboard navigation: nếu footer có nav links, phải accessible.
- Animation nếu có phải check `prefers-reduced-motion`.
- Không run Swiper hoặc external library trong footer block.

### Acceptance checklist

```
[ ] Block đăng ký đúng namespace skvn-marine
[ ] InspectorControls hiện sidebar controls, không raw class input
[ ] save.tsx output hợp lệ Gutenberg markup
[ ] Theme CSS render đúng columns và responsive
[ ] Editor preview match frontend ~80%
[ ] Upgrade path từ C: wrap nội dung cũ → không mất data
[ ] PHP syntax ok ở theme, TypeScript build ok ở plugin
```

---

## Giai đoạn B — Element System (`skvn_element` CPT + display rules)

### Target version

**V2.0.0** — gắn với trigger bỏ GeneratePress.

### Trigger bắt buộc

Giai đoạn B CHỈ được bắt đầu khi cả hai điều kiện sau đều đúng:

```
1. Human tuyên bố chính thức chuyển theme skvn-marine
   từ GeneratePress child sang standalone/custom base theme.

2. generate_footer hook không còn tồn tại trong theme mới.
   (Nếu base theme mới vẫn có hook tương đương, evaluate lại
   xem có cần B không hay A đã đủ.)
```

Không được bắt đầu B khi theme vẫn còn là GP child — không có justification.

### Vấn đề giải quyết

Khi bỏ GP, rendering engine phải rebuild. Đây là thời điểm đúng để thêm
conditional footer: trang sản phẩm dùng footer A, trang blog dùng footer B,
landing page dùng footer C. Một footer toàn site không còn đủ.

### Cách hoạt động

```
Plugin: đăng ký CPT skvn_element
  → mỗi element là một Gutenberg-editable post
  → sidebar: chọn Element Type (site_footer, site_header, sidebar_left, ...)
  → sidebar: Display Rules (post type, page template, taxonomy, page ID)
  → sidebar: Priority (nếu nhiều rules match)

Theme (custom base, không còn GP):
  → hook vào rendering engine của base theme mới
  → query skvn_element posts có type=site_footer
  → evaluate display rules theo current context
  → render element có priority cao nhất match
  → fallback: element có display rule "toàn site" nếu không có rule cụ thể hơn
```

### Ownership boundary

```
Plugin owns:
  CPT skvn_element
  Meta box: element_type, display_rules, priority
  Admin UI: list elements, edit element trong Gutenberg
  Query logic: lấy đúng element theo context

Theme owns:
  Hook vào rendering engine của base theme mới
  CSS cho tất cả footer variants
  Fallback rendering nếu không có element match
```

### Display Rules schema (tối giản cho V2)

```php
// Meta key: _skvn_element_display_rules
// Format: serialized array
[
  'scope'      => 'all' | 'post_type' | 'page_template' | 'page_id' | 'taxonomy',
  'post_type'  => 'product' | 'post' | 'page' | ...,
  'page_id'    => [123, 456],
  'template'   => 'templates/landing.php',
  'taxonomy'   => ['category' => ['news']],
]
```

### Migration từ A sang B

```
1. Export footer page content từ giai đoạn A (copy post_content).
2. Tạo skvn_element post mới, type = site_footer, display_rules = 'all'.
3. Paste content vào → đây là footer mặc định.
4. Tạo thêm element cho từng context nếu cần.
5. Xóa option skvn_footer_page_id (không còn dùng).
6. Update inc/footer.php (hoặc equivalent trong base theme mới)
   từ "query WP Page" sang "query skvn_element".
```

Data của footer content (Gutenberg blocks) không thay đổi format —
chỉ thay đổi container từ WP Page sang skvn_element CPT.

### Files cần tạo / sửa

```
Plugin:
  src/element-cpt/register.php           — register_post_type('skvn_element')
  src/element-cpt/meta.php               — register_meta: element_type, display_rules, priority
  src/element-cpt/admin-columns.php      — list table columns: type, rules, priority
  src/element-cpt/display-rules-ui.tsx   — sidebar panel: Display Rules controls
  src/element-cpt/query.ts              — helper: get_active_element(type, context)

Theme (base theme mới, không còn GP):
  inc/element-renderer.php              — hook vào base theme, query + render skvn_element
  inc/element-fallback.php             — fallback nếu không có element match
  style.css                            — CSS cho footer variants
```

### Invariants

- CPT slug phải là `skvn_element` — không đổi.
- Meta keys phải dùng prefix `_skvn_` — không dùng bare name.
- Query phải có fallback — không để footer trống nếu display rules không match.
- Admin UI phải check `current_user_can('edit_theme_options')` hoặc `manage_options`.
- Sanitize tất cả display rule meta trước khi lưu.
- Không cache element HTML nếu element có dynamic block (WooCommerce, shortcode).
- Keyboard navigation bắt buộc cho nav links trong footer elements.

### Acceptance checklist

```
[ ] CPT đăng ký đúng, chỉ admin thấy trong menu
[ ] Element type = site_footer render đúng vị trí
[ ] Display rules: 'all' scope hoạt động như footer mặc định
[ ] Display rules: post_type scope override đúng cho trang sản phẩm
[ ] Priority: element có priority cao hơn thắng khi nhiều rules match
[ ] Fallback: không có element match → không lỗi, không trống
[ ] Migration từ A: content paste vào element mới không mất formatting
[ ] PHP sanitize/escape đầy đủ cho tất cả meta
[ ] TypeScript build ok, không có type error
[ ] Không conflict với page-display hide footer meta
```

---

## Dependency giữa các giai đoạn

```
C → A:  không cần migrate data, upgrade là wrap nội dung vào block mới
A → B:  trigger bắt buộc là bỏ GP, content migrate bằng copy/paste post_content
B:      không thể ship nếu theme vẫn là GP child (không có justification)
```

## Tensions cần log trước khi implement

Trước khi bắt đầu giai đoạn nào, agent phải log tension vào `.context/TENSIONS_OPEN.md`:

```markdown
## [Date] | plugin / theme
Tension:    Footer Builder giai đoạn [C/A/B] vượt scope milestone hiện tại
Context:    Implementing footer builder feature
Proposal:   [mô tả cụ thể]
Constraint: Milestone hiện tại là [X], footer builder chưa có slot version
Severity:   low
Tags:       theme, blocks, planning
Milestone:  [current milestone]
Status:     OPEN
```

## Ghi chú cho agent

- File này là planning artifact — không phải active protocol.
- Không implement bất kỳ giai đoạn nào chỉ vì đọc file này.
- Chỉ implement khi human assign task cụ thể với milestone đã xác nhận.
- Giai đoạn B tuyệt đối không bắt đầu nếu theme vẫn là GP child.
- Khi human xác nhận version target, update filename và version trong file này cho đồng bộ.
