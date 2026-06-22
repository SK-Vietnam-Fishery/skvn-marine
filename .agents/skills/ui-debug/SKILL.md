---
name: ui-debug
description: >
  Phương pháp debug lỗi giao diện (UI bugs) theo tư duy "State Delta" — tìm
  chính xác điểm behavior thay đổi thay vì đoán nguyên nhân. Dùng skill này
  khi gặp bất kỳ lỗi UI/CSS/JS nào: element bị vỡ layout, sai kích thước,
  không hiển thị đúng, khác nhau giữa editor và frontend, khác nhau giữa
  các môi trường, hoặc lỗi chỉ xảy ra trong một số điều kiện nhất định.
  Đặc biệt hiệu quả với các bug khó tái hiện, bug chỉ xảy ra sau khi
  toggle/scroll/resize, hoặc bug liên quan đến theme/plugin phức tạp như
  Elementor, The7, WPBakery, v.v.
---

# UI Debug — State Delta Framework

## Nguyên tắc cốt lõi

> **Bug giao diện = Sự khác biệt giữa 2 states**
>
> Câu hỏi không phải *"tại sao nó sai"* mà là:
> **"Cái gì khác nhau giữa state ĐÚNG và state SAI?"**

Đây gọi là **State Delta** — tìm delta trước, tìm nguyên nhân sau.

---

## Bước 1: Đọc triệu chứng đúng cách

Trước khi làm bất cứ điều gì, khai thác tối đa thông tin từ mô tả lỗi.

Mỗi câu mô tả lỗi đều chứa **ít nhất 1 clue ẩn**:

| Người dùng nói | Clue thực sự |
|---|---|
| "Editor đúng, frontend sai" | Theme/plugin JS can thiệp khi render |
| "Trước đúng, giờ sai" | Có thay đổi gì đó — update, cache, config |
| "Bật rồi tắt mới hết" | JS chưa được initialized ở default state |
| "Chỉ sai trên mobile" | Breakpoint CSS hoặc JS responsive conflict |
| "Sau khi scroll mới sai" | Sticky/fixed positioning hoặc scroll event JS |
| "Chỉ sai khi login" | CSS/JS load khác nhau cho user roles |
| "Đôi khi sai, đôi khi đúng" | Race condition hoặc cache không nhất quán |
| "Attrs set trong editor, frontend không đổi" | PHP render filter không fire hoặc `$block['attrs']` không đọc đúng key |
| "Editor preview đúng, frontend sai" | PHP render output khác editor JS — wrapperStyle/wrapperProps không gắn vào DOM |
| "Feature bật rồi nhưng style không load" | Late enqueue bị static-var-guard bail sớm, hoặc enqueue sai handle/bundle |
| "Hover/color đúng vars nhưng màu không đổi" | CSS specificity của plugin thua theme — vars có nhưng rule consume bị override |

**→ Luôn liệt kê clues trước khi đề xuất fix.**

---

## Bước 2: Xác định 2 States

Điền vào bảng này trước khi debug:

```
State A (ĐÚNG):   [môi trường / điều kiện / thời điểm]
State B (SAI):    [môi trường / điều kiện / thời điểm]
Delta (khác biệt): [những gì khác nhau giữa A và B]
```

Ví dụ từ case The7 sticky:
```
State A (ĐÚNG):   Elementor Editor / Sau khi toggle sticky on→off
State B (SAI):    Frontend / Page load lần đầu, sticky chưa được toggle
Delta:            The7 sticky JS chưa được initialized
```

---

## Bước 3: 5 Trục Delta

Sau khi có 2 states, phân tích theo 5 trục để tìm delta:

### Trục 1 — Environment Delta
```
Editor ≠ Frontend
Development ≠ Production  
Cache enabled ≠ Cache disabled
Logged in ≠ Logged out
Plugin active ≠ Plugin inactive
```
**Test:** Mở Incognito + tắt cache → vẫn lỗi?

### Trục 2 — Time Delta
```
Trước khi JS load ≠ Sau khi JS load
Trước scroll ≠ Sau scroll
Trước toggle ≠ Sau toggle
Trước resize ≠ Sau resize
```
**Test:** Disable JavaScript → vẫn lỗi? → CSS/HTML problem, không phải JS.

### Trục 3 — Layer Delta
```
HTML → CSS → JavaScript → Gutenberg Editor → PHP Render Filter → DB Attrs → Plugin → Theme → Server
```
**Quy trình loại trừ từng layer:**
1. Disable JS → vẫn lỗi? → Không phải JS
2. Remove inline styles → vẫn lỗi? → Không phải inline CSS
3. Disable plugin một cái → hết lỗi? → Plugin đó là culprit
4. Switch sang default theme → hết lỗi? → Theme problem

**Với Gutenberg block extension — thêm 4 bước loại trừ:**

5. Attrs có trong DB không? → `wp post get <ID> --field=post_content | grep skvnHover`
   - Không có → user chưa Save, hoặc block attribute chưa được register
   - Có → vào bước 6
6. PHP render filter có fire không? → Check `has_filter('render_block_core/button', ...)` qua WP-CLI
   - Không → toggle gate đang off, hoặc `add_filter` chưa chạy
   - Có → vào bước 7
7. CSS vars có trên wrapper HTML không? → `document.querySelector('.wp-block-button')?.getAttribute('style')`
   - Không có → PHP sanitize đang drop giá trị (alpha color, gradient, wrong key name)
   - Có → vào bước 8
8. Rule consume có bị theme override không? → DevTools Elements → hover link → Styles tab → xem rule nào strikethrough
   - Bị strikethrough → specificity thua theme; cần scoped class hoặc tăng specificity

### Trục 4 — Scope Delta
```
Tất cả pages ≠ Chỉ 1 page
Tất cả sections ≠ Chỉ section X
Tất cả elements ≠ Chỉ element Y
Desktop ≠ Tablet ≠ Mobile
```
**Test:** Reproduce lỗi trên trang blank/mới → vẫn lỗi? → Global problem.

### Trục 5 — Data/State Delta
```
Default value ≠ Saved value ≠ Runtime value
Database state ≠ JS runtime state ≠ CSS computed state
```
**Test:** Inspect element → Computed tab → giá trị thực tế là gì so với expected?

---

## Bước 4: Quy trình 3 bước ISOLATE → DIFF → PROVE

### ISOLATE — Thu hẹp đến smallest reproducible case
- Tắt hết plugins không liên quan
- Test trên trang blank
- Xóa hết custom code không liên quan
- Mục tiêu: **Tìm điều kiện tối thiểu để reproduce bug**

### DIFF — So sánh working vs broken
```javascript
// Dùng DevTools để so sánh
// Bước 1: Chụp computed styles ở state ĐÚNG
// Bước 2: Chụp computed styles ở state SAI  
// Bước 3: Tìm dòng khác nhau
```
- Elements tab → Computed → Filter bằng property nghi ngờ
- Network tab → So sánh requests giữa 2 states
- Console tab → Tìm errors/warnings

### PROVE — Verify bằng cách reproduce có chủ đích
- Fix xong → **cố tình reproduce lại bug** theo đúng điều kiện cũ
- Nếu không reproduce được → fix đúng
- Nếu reproduce được → fix chưa đủ hoặc sai nguyên nhân

---

## Bước 5.5: Gutenberg Block Extension — Verification Commands

Dùng khi feature là Gutenberg block filter/extension (không phải custom block). Chạy theo thứ tự pipeline:

### Pipeline chuẩn
```
DB post_content → parse_blocks() → render_block filter → HTML output → CSS consume
```

### WP-CLI (chạy trong WSL, path lấy từ .local/ENVIRONMENT.md)

```bash
# 1. Attrs có trong DB không?
wp post get <POST_ID> --field=post_content --path=<WP_ROOT> | grep -o '"skvnHover[^"]*":"[^"]*"'

# 2. Option toggle có đúng không?
wp option get skvn_core_controls --format=json --path=<WP_ROOT>

# 3. PHP filter có được hook không?
wp eval "echo has_filter('render_block_core/button', 'skvn_marine_blocks_render_button_hover') ? 'HOOKED' : 'NOT HOOKED';" --path=<WP_ROOT>

# 4. Render output thực tế của block
wp post get <POST_ID> --field=post_content --path=<WP_ROOT> | wp eval-file - --path=<WP_ROOT>
# (hoặc view source trang sau khi apply_filters)
```

### Browser DevTools — Editor layer

```javascript
// Attrs hiện tại của block đang select trong editor
wp.data.select('core/block-editor').getSelectedBlock()?.attributes

// Filter đã được register chưa?
wp.hooks.hasFilter('blocks.registerBlockType', 'skvn-marine/button-hover-attributes')
wp.hooks.hasFilter('editor.BlockEdit', 'skvn-marine/button-hover-controls')

// CSS vars có trên wrapper wrapper không (editor)?
const w = document.querySelector('[data-block].wp-block-button');
w?.getAttribute('style'); // inline vars phải có ở đây nếu wrapperProps đúng
```

### Browser DevTools — Frontend layer

```javascript
// CSS vars có trên wrapper HTML không?
const w = document.querySelector('.wp-block-button');
console.log({
  inlineStyle: w?.getAttribute('style'),
  computedText: getComputedStyle(w).getPropertyValue('--skvn-btn-hover-text').trim(),
  computedBg:   getComputedStyle(w).getPropertyValue('--skvn-btn-hover-bg').trim(),
});
// computedText/computedBg rỗng → vars không có → PHP pipeline lỗi
// computedText/computedBg có giá trị → vars OK → vào kiểm tra specificity
```

### CSS Specificity Conflict Detection

Trước khi viết rule consume, PHẢI kiểm tra specificity tối thiểu cần đạt:

```
Plugin rule cần ≥ specificity của theme rule mạnh nhất cho cùng element.
```

**Workflow:**

1. Mở DevTools → Elements → chọn `<a class="wp-block-button__link">` → Styles tab
2. Lọc theo `:hover` — liệt kê tất cả rules đang apply kèm specificity
3. Tìm rule nào có specificity cao nhất với hard-coded value (không phải var)
4. Plugin rule phải có specificity ≥ rule đó, HOẶC dùng scoped class để tăng

**Ví dụ pattern SKVN Marine:**
```css
/* Theme — 0,3,1 (class + class + element) */
.wp-block-button.skvn-button--primary .wp-block-button__link:hover { background: #hardcoded; }

/* Plugin cần ≥ 0,3,1 để thắng — dùng scoped class */
.wp-block-button.has-skvn-button-hover .wp-block-button__link:hover { background: var(--skvn-btn-hover-bg, inherit); }
```

Global rule 0,2,1 sẽ thua — đây là bug im lặng phổ biến nhất với block extension.

---

## Công cụ: sanitize.sh — Làm sạch HTML trước khi phân tích

Khi user cung cấp HTML thô (copy từ browser source, DevTools, hoặc file lớn), chạy sanitize trước để loại bỏ noise: base64 data URIs, script blocks, inline style dài, aria/non-UI attributes. Output là file `.debug.html` chỉ giữ class, id, CSS, layout structure.

**Khi nào đề xuất:**
- HTML > ~50 dòng, hoặc
- Có dấu hiệu base64 (`data:image/`), hoặc
- Có nhiều `<script>` blocks, hoặc
- User hỏi "tại sao element này..." mà chưa có file HTML sạch

**Command (output để user copy và chạy trong WSL):**
```bash
bash .agents/skills/ui-debug/tools/sanitize.sh ".local/Seafood Carousels.html"
# Output: .local/Seafood Carousels.debug.html
```

**Sau khi sanitize:** Đọc file `.debug.html` để phân tích class/CSS — không đọc file gốc vì noise làm tốn context.

**Rule cho agent:** Không tự chạy tool qua Bash. Output lệnh dưới dạng text, user tự copy và chạy.

---

## Bước 5: DevTools Commands Nhanh

```javascript
// Xem tất cả computed styles của element
window.getComputedStyle(document.querySelector('.your-selector'))

// Xem kích thước thực tế
document.querySelector('.your-selector').getBoundingClientRect()

// Tìm CSS rule nào đang override
// → Elements tab → Computed → click vào property → thấy nguồn file

// Xem element có bị hidden không
document.querySelector('.your-selector').offsetParent // null = hidden

// Force trigger JS events để test
window.dispatchEvent(new Event('scroll'))
window.dispatchEvent(new Event('resize'))
```

## References Theo Case

- WordPress/Gutenberg footer đã render nhưng layout vẫn sai, `alignfull` tràn ngang, wrapper `site-footer` lệch âm, hoặc có khoảng trắng dưới footer: đọc `references/footer-full-width-overflow.md`.
- SKVN Marine — full agent+human workflow (diagnose → compare options → human chọn → implement → DevTools verify → decision vs ideation docs): `docs/workflows/agent-ui-layout-collaboration-method.md`. Case: single post hero GP flex sibling + mobile island padding stack.

---

## Anti-patterns Cần Tránh

### ❌ Shotgun approach
```
"Thử clear cache, thử tắt plugin, thử đổi CSS, thử regenerate..."
→ Không có logic loại trừ, mất thời gian, may rủi
```

### ❌ Fix trước khi tìm delta
```
"Thêm !important vào CSS" trước khi biết nguyên nhân
→ Che giấu bug thay vì fix bug
```

### ❌ Assume thay vì verify
```
"Chắc là cache" → flush cache → vẫn lỗi → mất thời gian
→ Luôn verify assumption bằng test cụ thể
```

### ❌ Test grep PHP source = false positive (Gutenberg-specific)
```
assert.match(phpSource, /--skvn-btn-hover-text:/) → PASS
→ Nhưng không chứng minh vars được consume đúng, hoặc specificity đủ thắng theme.

Test đúng: gọi PHP function với mock $block_content + $block array,
assert output HTML có class scoped + inline vars.
Không assert "có string trong source" — assert "output HTML đúng contract".
```

### ❌ Viết rule consume trước khi check specificity theme
```
Plugin CSS 0,2,1 viết trước → deploy → hover không đổi màu
→ Trước khi viết rule consume, PHẢI mở DevTools kiểm tra
   theme rule mạnh nhất đang apply cho element đó là bao nhiêu.
```

### ✅ Đúng: Đọc clue → Tìm delta → Loại trừ từng layer → Prove fix

---

## Template Báo Cáo Bug

Khi debug cho người khác, luôn yêu cầu đủ thông tin này:

```
1. Môi trường SAI:   [browser, device, login status, cache status]
2. Môi trường ĐÚNG:  [browser, device, login status, cache status]  
3. Bước tái hiện:    [step by step để reproduce]
4. Expected:         [trông như thế nào khi đúng]
5. Actual:           [trông như thế nào khi sai]
6. Đã thử:           [những gì đã thử và kết quả]
7. Screenshots:      [cả 2 states nếu có thể]
```

---

## Checklist Debug Nhanh (30 giây)

Trước khi đào sâu, chạy qua checklist này:

- [ ] Hard refresh (Ctrl+Shift+R) → hết chưa?
- [ ] Incognito mode → hết chưa?
- [ ] Disable tất cả cache → hết chưa?
- [ ] Disable JS → lỗi thay đổi không?
- [ ] Default theme → hết chưa?
- [ ] Lỗi ở tất cả pages hay chỉ 1 page?
- [ ] Lỗi ở tất cả elements hay chỉ 1 element?
- [ ] Lỗi xuất hiện ngay hay sau action nào đó?

**Thêm nếu là Gutenberg block extension:**
- [ ] Attrs có trong DB không? (WP-CLI `wp post get`)
- [ ] Toggle option đang on? (`wp option get skvn_core_controls`)
- [ ] PHP filter có hook không? (`has_filter(...)`)
- [ ] CSS vars có trên wrapper HTML không? (`getAttribute('style')`)
- [ ] Specificity plugin rule có ≥ theme rule không? (DevTools → hover → Styles)
- [ ] Test file có đang chỉ grep PHP source không? (false positive risk)

Mỗi câu trả lời **loại trừ** ít nhất 1 layer, thu hẹp phạm vi debug.
