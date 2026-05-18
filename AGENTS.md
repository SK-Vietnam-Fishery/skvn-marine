# AGENTS.md — SKVN Marine

> Đọc file này TRƯỚC KHI làm bất kỳ task nào.
> Đây là protocol bắt buộc cho mọi AI agent làm việc trong project này.

---

## 1. Đọc trước — Bắt buộc

Mỗi task bắt đầu bằng 3 bước này, không có ngoại lệ:

```
1. Đọc .context/GLOBAL.md       → hiểu stack, module index, invariants
2. Đọc .context/TENSIONS.md     → check OPEN tensions liên quan đến task
3. Đọc .context/<module>.md     → load context của module sắp sửa
```

Nếu `.context/<module>.md` chưa tồn tại → load `GLOBAL.md` + hỏi lại trước khi tạo file mới.

Nếu `[manual]` section trong module file còn template placeholder (`<!-- Viết tại đây -->`) → **DỪNG, báo lại**, không tự điền assumption.

---

## 2. Architecture — Biết rõ trước khi code

### Boundary rules

| Thuộc về | Layer | Ví dụ |
|---|---|---|
| `skvn-marine/` | Theme | Visual system, design tokens, block styles, patterns, WooCommerce visual override, animation runtime, media helpers |
| `skvn-marine-blocks/` | Plugin | Custom Gutenberg blocks có logic: slider, accordion, product-grid, product-list |
| External plugins | Không touch | WooCommerce, CF7, CFDB7, Rank Math, Polylang, n8n |
| GeneratePress | Không touch tuyệt đối | `themes/generatepress/**` |

**Rule quyết định nhanh**: nếu thay theme mà feature bị mất → feature thuộc plugin.

### Naming — Không bao giờ đổi

```
Theme slug:         skvn-marine
Plugin slug:        skvn-marine-blocks
Block namespace:    skvn-marine
Theme text domain:  skvn-marine
Plugin text domain: skvn-marine-blocks
Theme PHP prefix:   skvn_marine_
Plugin PHP prefix:  skvn_marine_blocks_
CSS prefix:         skvn-
```

### PHP security — Không thương lượng

```php
// Input — luôn sanitize
$product_id = isset($_GET['product_id']) ? absint($_GET['product_id']) : 0;
$sku        = isset($_GET['sku']) ? sanitize_text_field(wp_unslash($_GET['sku'])) : '';

// Output — luôn escape
echo esc_html($title);
echo esc_attr($value);
echo esc_url($url);
echo wp_kses_post($content);
```

---

## 3. Workflow — Mỗi Task

```
1. Load context (GLOBAL → TENSIONS → module)
2. Đọc [manual] của module — hiểu constraints và invariants
3. Nếu [manual] còn placeholder → DỪNG, hỏi lại
4. Detect tensions (xem Section 4)
5. Nếu tension HIGH → generate TENSIONS.md entry → DỪNG, chờ quyết định
6. Nếu tension LOW hoặc không có → tiếp tục
7. Plan changes — liệt kê files sẽ sửa (≤5 files trừ khi có lý do)
8. Implement — follow coding standards bên dưới
9. Self-check (xem checklist cuối file)
10. Update .context/ nếu có quyết định mới
```

### Sau khi implement xong

```bash
# Theme PHP
php -l wp-content/themes/skvn-marine/functions.php

# Plugin JS/TS (nếu có thay đổi block)
cd wp-content/plugins/skvn-marine-blocks && npm run build 2>&1 | tail -10

# Kiểm tra block nếu thay đổi block registration
grep -r "registerBlockType" wp-content/plugins/skvn-marine-blocks/src/
```

---

## 4. Tension Detection

Ghi tension khi phát hiện conflict giữa task yêu cầu và constraint đã có trong `[manual]`.

### Format ghi vào `.context/TENSIONS.md`

```markdown
## [YYYY-MM-DD HH:MM] | [module]
Tension:    Mô tả conflict ngắn gọn
Context:    Đang làm task gì
Proposal:   Agent muốn làm gì
Constraint: [manual] rule nào conflict (quote lại)
Severity:   low | high
Decision:   OPEN
```

### Routing

```
Severity LOW  → ghi tension → tiếp tục theo hướng conservative → báo human review sau
Severity HIGH → ghi tension → DỪNG task → chờ human fill Decision
```

### Ví dụ triggers tension

| Situation | Severity |
|---|---|
| Task yêu cầu sửa file trong `themes/generatepress/` | HIGH |
| Task muốn custom-code quote form handler thay CF7 | HIGH |
| Task muốn thêm animation logic riêng trong block, bỏ qua shared runtime | LOW |
| Task muốn đặt custom block trong theme folder | HIGH |
| Task muốn rename prefix/namespace | HIGH |
| Task muốn add dependency không có rationale | LOW |
| Không chắc feature thuộc theme hay plugin | LOW |

---

## 5. Coding Standards

### PHP (Theme)

- WordPress Coding Standards mindset
- Sanitize input, escape output — luôn luôn
- Prefix mọi function: `skvn_marine_`
- Không dùng `@` để suppress errors
- `filemtime()` cho asset versioning
- Không dùng Composer/PHPCS trong V1 nếu làm chậm dev

### TypeScript / JavaScript (Plugin blocks)

- TypeScript cho tất cả block logic
- @wordpress/scripts build pipeline
- ESLint/Prettier optional V1, expected V2
- Shared animation runtime — KHÔNG tạo animation logic riêng per block
- Swiper chỉ dùng cho slider block
- IntersectionObserver cho scroll reveal

### Gutenberg / Block

- Block attributes định nghĩa rõ trong `block.json`
- Sidebar controls thay vì raw class input cho marketing users (V3 goal)
- Editor view KHÔNG set `opacity: 0` cho reveal elements nếu không có safe fallback
- Slider editor: stacked preview hoặc simplified — KHÔNG run Swiper autoplay trong editor
- Keyboard navigation bắt buộc cho accordion và slider

### Animation

- Tất cả animation phải check `prefers-reduced-motion`:

```javascript
const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
if (!prefersReduced) { /* run animation */ }
```

---

## 6. Module-Specific Rules

### Theme: `inc/media.php`

- `skvn_marine_auto_set_image_alt_from_title()` — chỉ fill khi ALT empty
- KHÔNG overwrite ALT đã có
- KHÔNG auto-generate caption trong V1

### Theme: `inc/enqueue.php`

- Dùng `filemtime()` cho versioning
- Conditional load: Swiper chỉ load khi có slider block trên page
- Block assets: load via block.json `viewScript`, không global

### Plugin: Slider / Slide

- Swiper config qua block attributes (autoplay, delay, loop, arrows, dots, effect, slidesPerView)
- Editor: render slides stacked (không run Swiper carousel trong editor)
- Keyboard nav: Swiper `keyboard: { enabled: true }`
- Pause on hover: `autoplay.pauseOnMouseEnter: true`

### Plugin: Product Grid / Product List

- Dùng WooCommerce native query — KHÔNG custom SQL trực tiếp
- V1: WooCommerce native blocks/patterns trước, custom block sau khi homepage đã xong
- Pagination cho Product List
- Mobile: CTA (Request a Quote) luôn visible, KHÔNG chỉ hiện khi hover

### Quote Flow

- URL pattern: `/request-a-quote/?product_id=123`
- Hidden fields bắt buộc: `product_id`, `product_sku`, `product_name`, `product_url`, `source_url`, UTM fields
- CF7 markup dùng class `skvn-form`, `skvn-quote-form`, `skvn-button`, `skvn-button--primary`
- n8n webhook: hard-to-guess URL + optional shared secret header. KHÔNG expose unprotected.

---

## 7. AI Task Format

Mỗi task đưa cho AI nên có đủ 6 phần:

```markdown
## Context
[Load từ .context/ hoặc mô tả ngắn]

## Goal
[1-2 câu: làm gì]

## Files allowed to change
[List cụ thể]

## Files forbidden to change
[Luôn include: themes/generatepress/**]

## Acceptance checklist
- [ ] PHP syntax ok
- [ ] No fatal errors
- [ ] Sanitize/escape đầy đủ
- [ ] Prefix đúng
- [ ] .context/ updated nếu có decision mới

## Tensions / Conflicts
[Ghi nếu biết trước có conflict, để agent xử lý đúng]
```

---

## 8. Self-Check Trước Khi Submit

```
[ ] Không sửa themes/generatepress/
[ ] Không rename namespace/prefix
[ ] Không đặt block logic trong theme
[ ] Input sanitized, output escaped
[ ] Animation có prefers-reduced-motion guard
[ ] Editor không hide content với opacity: 0
[ ] Image ALT: chỉ fill khi empty, không overwrite
[ ] Dependency mới có rationale
[ ] Files sửa ≤ 5 (hoặc có lý do nếu hơn)
[ ] TENSIONS.md updated nếu có conflict phát sinh
[ ] .context/<module>.md updated nếu có decision mới
```

---

## 9. Versioning Milestones (để biết scope hiện tại)

| Version | Scope |
|---|---|
| 0.1.0 | Child theme skeleton |
| 0.2.0 | Design system, block styles, patterns |
| 0.3.0 | Slider/Slide block |
| 0.4.0 | Woo product grid/list |
| 0.5.0 | Quote flow integration |
| 1.0.0 | V1 launch-ready |

---

## 10. Không thuộc phạm vi V1

- Quote cart, multi-product quote table
- Advanced product filtering
- Popup/modal làm primary quote flow
- Polylang activation (chỉ prepare)
- Composer/PHPCS strict enforcement
- Redis / CDN
- GitHub Actions CI/CD
- Technical Product Card với specs table
- Custom CPT cho product
