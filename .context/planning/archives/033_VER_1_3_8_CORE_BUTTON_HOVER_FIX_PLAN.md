> Archived: 2026-06-30 — prefix giữ nguyên, không renumber.
> Decision: docs/decisions/core-control-core-button-hover.md
> Onsite QA defer: 1.3.11

# 033 — Core Button Hover: Fix Plan & Retrospective

```
Version:   1.3.8
Date:      2026-06-22
Status:    IMPLEMENTED (2026-06-22 — onsite QA pending)
Feature:   Core Control → Core Button Hover Colors
Decision:  docs/decisions/core-control-core-button-hover.md
```

---

## Tại sao document này tồn tại

Feature này đã qua tay ít nhất 2 agent (Cursor, Claude) và vẫn chưa hoạt động đúng
trên frontend. Mỗi agent sửa một lớp nhưng không nhìn thấy toàn bộ pipeline.

Document này ghi lại đầy đủ: điều gì đã xảy ra, tại sao sai, và plan
fix chính xác — để agent hoặc dev tiếp theo không lặp lại vòng lặp này.

---

## 1. Bối cảnh feature

Core Button Hover Colors là một **Gutenberg block extension**, không phải custom block.
Nó mở rộng `core/button` thông qua:

- **PHP**: `render_block_core/button` filter inject CSS vars vào HTML lúc render
- **TypeScript**: `blocks.registerBlockType` + `editor.BlockEdit` filter thêm attrs và Inspector panel
- **CSS**: Rule `:hover` consume vars đó

Pipeline đúng theo Gutenberg:

```
post_content (DB)
  └─ parse_blocks()
       └─ $block['attrs']  ← skvnHoverTextColor, skvnHoverBgColor
            └─ render_block_core/button  ← PHP inject vars vào HTML
                 └─ <div class="wp-block-button" style="--skvn-btn-hover-*:…">
                       └─ <a class="wp-block-button__link">
```

**Quan trọng:** Block comment `<!-- wp:button {…} -->` chỉ tồn tại trong DB.
Frontend HTML không có comment đó — attrs đọc qua `$block['attrs']` trong filter,
không phải từ DOM.

---

## 2. Timeline sai lầm — ai làm gì, sai ở đâu

### Giai đoạn 1 — Implementation đầu (agent không rõ)

Implement `render_block_core/button` filter với scoped `<style>` theo CASE-007 contract:

```css
.wp-block-button.skvn-btn-xxxxxxxx { --skvn-btn-hover-text: #xxx; }
.wp-block-button.skvn-btn-xxxxxxxx .wp-block-button__link:hover { color: var(…); }
```

**Vấn đề phát sinh:** `<style>` bị inject vào trong `wp-block-buttons` (wrapper nhiều button)
gây `margin-left: 192px` do flex child không phải element.

### Giai đoạn 2 — Cursor refactor (gây regression)

Cursor bỏ `<style>` để fix layout, pivot sang:
- Inline vars trên wrapper: `style="--skvn-btn-hover-text:…"`
- Global CSS rule: `.wp-block-button .wp-block-button__link:hover { color: var(…) }`
- Enqueue toàn bộ `style-index.ts.css`

**Những gì Cursor không kiểm tra:**
1. Theme rule `.wp-block-button.skvn-button--primary .wp-block-button__link:hover` có
   specificity 0,3,1 — plugin rule 0,2,1 thua hoàn toàn
2. `wrapperStyle` trong `editor.BlockEdit` không gắn vào DOM — editor preview không hoạt động
3. `ColorPicker` với `enableAlpha` emit `rgba()` nhưng PHP `sanitize_hex_color()` drop giá trị đó

**Kết quả:** Hover vẫn không đổi màu, nhưng không có error. Silent failure.

**CASE-007 bị mark FIXED nhưng thực tế đã REGRESSED** — fix contract (scoped define+consume)
bị bỏ mà không thay thế bằng giải pháp specificity tương đương.

### Giai đoạn 3 — Claude sửa một phần (2026-06-22)

Claude nhận diện đúng các vấn đề nhưng chỉ implement một phần:

| Việc | Kết quả |
|---|---|
| Swap `ColorPicker` → `PanelColorGradientSettings` | ✅ Fix alpha bug, match native Gutenberg UI |
| Thêm gradient sanitize cho `skvnHoverBgColor` | ✅ Gradient path mới |
| `background-color` → `background` trong style.css | ✅ Cần thiết cho gradient |
| Thêm `has-skvn-button-hover` class | ❌ Không làm |
| Fix specificity rule | ❌ Không làm |
| Tách CSS bundle | ❌ Không làm |
| Editor preview (`editor.BlockListBlock`) | ❌ Không làm |

**Kết quả:** Hover vẫn broken với `skvn-button--primary` buttons vì specificity chưa fix.

---

## 3. Root causes — tại sao các agent mắc lỗi này

### RC-1: Không check specificity theme trước khi viết consuming rule

Cả 2 agent đều viết CSS rule cho plugin trước, rồi mới để ý (hoặc không để ý) rằng
theme đã có rule mạnh hơn cho cùng element.

**Quy tắc bắt buộc cho mọi block extension:**
> Trước khi viết rule `:hover` cho plugin, mở DevTools kiểm tra
> theme rule mạnh nhất đang apply cho element đó. Plugin rule phải ≥ specificity đó.

### RC-2: Không hiểu `editor.BlockEdit` vs `editor.BlockListBlock`

`editor.BlockEdit` inject Inspector controls — nó **không** control wrapper DOM của block.
`wrapperStyle` build trong `BlockEdit` là dead code từ đầu.

Đúng filter để gắn vars vào editor wrapper: `editor.BlockListBlock` với `wrapperProps`.

### RC-3: Test grep PHP source = false positive

Test assert `--skvn-btn-hover-text:` xuất hiện trong PHP source. Test này PASS kể cả khi:
- Rule consume bị theme override
- preg_replace không match markup thực
- Vars có nhưng selector không đúng

**Test đúng:** Gọi PHP function với mock `$block_content` + `$block` array,
assert HTML output có đúng class và inline style.

### RC-4: Pivot không atomic

Fix layout (bỏ `<style>`) là đúng. Nhưng pivot không kèm theo replacement cho specificity.
Khi bỏ scoped class (`skvn-btn-xxxxxxxx`), không có gì thay thế để đạt 0,3,1.

**Quy tắc:** Khi bỏ một cơ chế specificity, phải thay bằng cơ chế tương đương.
`has-skvn-button-hover` là replacement đó.

### RC-5: Enqueue sai boundary

`style-index.ts.css` là bundle toàn bộ plugin (slider, collection, feature showcase…).
Enqueue nó cho button hover làm tất cả CSS đó load trên mọi page có button.

Decision doc nói rõ `style_handle: skvn-marine-core-button-hover` — handle riêng,
chỉ chứa hover rules.

---

## 4. Trạng thái hiện tại (trước khi implement plan này)

### Hoạt động đúng
- PHP filter `render_block_core/button` đã hook ✅
- Attrs đọc đúng key: `skvnHoverTextColor`, `skvnHoverBgColor` ✅
- Inline vars inject lên `<div class="wp-block-button">` ✅
- Gradient background: sanitize + consume ✅
- Editor panel: `PanelColorGradientSettings` với palette + gradient ✅
- Alpha color bug: đã fix (palette không có alpha tự do) ✅

### Broken / thiếu
- **[CRITICAL]** Plugin CSS 0,2,1 thua theme 0,3,1 → hover không đổi màu với `skvn-button--primary`
- **[CRITICAL]** Class `has-skvn-button-hover` không có → không có cơ chế scoping
- **[MEDIUM]** Bundle `style-index.ts.css` load trên mọi page có button
- **[MEDIUM]** Editor preview không hoạt động (`editor.BlockListBlock` chưa implement)
- **[LOW]** CASE-007 status vẫn ghi FIXED thay vì REGRESSED
- **[LOW]** Decision doc status vẫn "implementation not started"

---

## 5. Implementation Plan

### Scope

Files được phép thay đổi trong session này:
- `modules/core-control/features/button-hover.php`
- `src/core-controls/button-hover/style.css`
- `src/core-controls/button-hover/index.tsx`
- `docs/debug-casebook/core-control/007_BUTTON_HOVER_CSS_VARS_BUILT_NOT_EMITTED.md`
- `docs/decisions/core-control-core-button-hover.md`
- `tests/core-control-button-hover.test.mjs`

Files không được đổi:
- `wp-content/themes/skvn-marine/style.css` (theme)
- `src/slider/style.css` (slider scope riêng)

---

### Fix 1 — Thêm `has-skvn-button-hover` class vào PHP render

**File:** `modules/core-control/features/button-hover.php`

**Vấn đề:** `preg_replace` chỉ inject vars, không thêm class scoping.

**Fix:** Mở rộng `preg_replace_callback` để đồng thời:
1. Thêm `has-skvn-button-hover` vào `class="…"`
2. Inject vars vào `style="…"`

```php
// Thêm class vào class attribute
$block_content = preg_replace_callback(
    '/(<div\s+class="([^"]*wp-block-button[^"]*)")(\s+style="([^"]*)")?/i',
    static function ( array $matches ) use ( $style_attr ): string {
        $classes  = $matches[2];
        // Thêm class nếu chưa có
        if ( ! str_contains( $classes, 'has-skvn-button-hover' ) ) {
            $classes .= ' has-skvn-button-hover';
        }
        $existing = isset( $matches[4] ) ? $matches[4] : '';
        // merge style
        $merged = $existing;
        if ( $merged !== '' && ! str_ends_with( rtrim( $merged ), ';' ) ) {
            $merged .= ';';
        }
        $merged .= $style_attr;
        return '<div class="' . esc_attr( $classes ) . '" style="' . esc_attr( $merged ) . '"';
    },
    $block_content,
    1
);
```

**Tại sao:** Class này là hook để CSS rule scoped apply đúng block.
Không có class → không có cách nào tăng specificity mà không dùng `!important`.

---

### Fix 2 — Nâng specificity CSS rule

**File:** `src/core-controls/button-hover/style.css`

**Vấn đề:** Rule 0,2,1 thua theme 0,3,1.

**Fix:**

```css
/* Specificity: 0,3,1 — bằng theme skvn-button--primary */
.wp-block-button.has-skvn-button-hover .wp-block-button__link:hover,
.wp-block-button.has-skvn-button-hover .wp-block-button__link:focus-visible {
    color: var( --skvn-btn-hover-text, inherit );
    background: var( --skvn-btn-hover-bg, inherit );
    transition: color 0.15s ease, background 0.15s ease;
}

@media ( prefers-reduced-motion: reduce ) {
    .wp-block-button.has-skvn-button-hover .wp-block-button__link {
        transition: none;
    }
}
```

**Tại sao:** `.wp-block-button.has-skvn-button-hover` (2 class) + `.wp-block-button__link` (1 class) + `:hover` (pseudo) = 0,3,1. Bằng với theme rule → cascade (load order) quyết định; plugin style thường load sau theme → thắng.

**Load order đảm bảo:** `wp_enqueue_style` với `array('skvn-marine-styles')` làm dependency.

---

### Fix 3 — Tách CSS khỏi bundle

**File:** `modules/core-control/features/button-hover.php`

**Vấn đề:** Đang enqueue `build/style-index.ts.css` — bundle toàn plugin.

**Fix:** Dùng `wp_register_style` + `wp_add_inline_style` với handle riêng.
CSS chỉ 2 rules (~15 dòng) — không cần file riêng, inline style đủ và clean hơn:

```php
function skvn_marine_blocks_enqueue_button_hover_frontend_style(): void {
    static $enqueued = false;
    if ( $enqueued ) return;
    $enqueued = true;

    // Register empty handle với dependency vào theme stylesheet
    wp_register_style(
        'skvn-marine-core-button-hover',
        false,           // no file
        array( 'skvn-marine-styles' ),  // load sau theme → cascade thắng
        null
    );

    wp_enqueue_style( 'skvn-marine-core-button-hover' );

    $css = '
.wp-block-button.has-skvn-button-hover .wp-block-button__link:hover,
.wp-block-button.has-skvn-button-hover .wp-block-button__link:focus-visible {
  color: var(--skvn-btn-hover-text, inherit);
  background: var(--skvn-btn-hover-bg, inherit);
  transition: color .15s ease, background .15s ease;
}
@media (prefers-reduced-motion: reduce) {
  .wp-block-button.has-skvn-button-hover .wp-block-button__link { transition: none; }
}';

    wp_add_inline_style( 'skvn-marine-core-button-hover', $css );
}
```

**Tại sao `wp_register_style` với `false` URL:**
- Không có file build để sync
- CSS đủ nhỏ để inline
- `wp_add_inline_style` append sau registered handle
- Dependency `skvn-marine-styles` đảm bảo load order

**Tại sao bỏ source style.css consuming rule:**
Nếu CSS được inject qua `wp_add_inline_style` ở PHP, file `style.css` trong src chỉ còn
dùng cho editor preview. Editor preview dùng class khác (xem Fix 4).

---

### Fix 4 — Editor preview (editor.BlockListBlock)

**File:** `src/core-controls/button-hover/index.tsx`

**Vấn đề:** `wrapperStyle` trong `editor.BlockEdit` là dead code — filter đó inject
Inspector controls, không control wrapper DOM.

**Fix:** Thêm `editor.BlockListBlock` filter riêng:

```tsx
addFilter(
    'editor.BlockListBlock',
    'skvn-marine/button-hover-wrapper-props',
    ( BlockListBlock: React.ComponentType< any > ) => {
        return function ButtonHoverWrapper( props: any ) {
            if ( props.name !== 'core/button' ) {
                return <BlockListBlock { ...props } />;
            }

            const { skvnHoverTextColor, skvnHoverBgColor } = props.attributes;

            if ( ! skvnHoverTextColor && ! skvnHoverBgColor ) {
                return <BlockListBlock { ...props } />;
            }

            const vars: Record< string, string > = {};
            if ( skvnHoverTextColor ) vars[ '--skvn-btn-hover-text' ] = skvnHoverTextColor;
            if ( skvnHoverBgColor )   vars[ '--skvn-btn-hover-bg' ]   = skvnHoverBgColor;

            const wrapperProps = {
                ...( props.wrapperProps ?? {} ),
                style: { ...( props.wrapperProps?.style ?? {} ), ...vars },
            };

            return <BlockListBlock { ...props } wrapperProps={ wrapperProps } />;
        };
    }
);
```

**style.css giữ nguyên** cho editor (selector `.wp-block-button .wp-block-button__link:hover`
dùng specificity thấp hơn nhưng OK trong editor vì không có theme conflict).

**Tại sao `editor.BlockListBlock` không phải `editor.BlockEdit`:**
`BlockEdit` = render Inspector panel, không access wrapper DOM.
`BlockListBlock` = wrap toàn bộ block trong editor, có `wrapperProps` để inject style.

---

### Fix 5 — Cập nhật docs

**CASE-007:** Đổi status từ `FIXED` → `REGRESSED → RE-FIXED`.
Thêm section mô tả pivot và lý do final implementation dùng class + inline vars thay vì scoped `<style>`.

**Decision doc:** Đổi status từ `DECIDED, implementation not started` → `IMPLEMENTED (partial — editor preview pending)`.

**tests/core-control-button-hover.test.mjs:** Thêm test gọi PHP function với mock HTML,
assert output có class `has-skvn-button-hover` và inline vars.

---

## 6. Thứ tự implement

```
Fix 1 (PHP class)
  → Fix 3 (PHP enqueue — cùng file, cùng lần edit)
  → Fix 2 (style.css — chỉ còn editor rule, frontend dùng inline)
  → Fix 4 (TSX editor preview)
  → Fix 5 (docs + test)
  → npm run build
  → Verify onsite: DevTools hover link → Styles tab không có strikethrough
```

---

## 7. Acceptance checklist

Frontend:
- [ ] Wrapper `<div class="wp-block-button has-skvn-button-hover">` có trong Elements
- [ ] `getAttribute('style')` trả về `--skvn-btn-hover-text` và/hoặc `--skvn-btn-hover-bg`
- [ ] DevTools hover link → rule plugin không bị strikethrough
- [ ] Màu thực sự đổi khi hover — kể cả button với class `skvn-button--primary`
- [ ] Gradient background render đúng khi hover
- [ ] `skvn-marine-core-button-hover` style có trong DOM (không phải `style-index.ts.css`)

Editor:
- [ ] Panel "Hover Colors" xuất hiện dưới panel "Color" trong tab Styles
- [ ] Chọn màu → wrapper trong editor có inline vars (kiểm tra DevTools trong iframe)

Toggle:
- [ ] Tắt toggle → panel ẩn, style không inject
- [ ] Bật lại → giá trị cũ được restore
- [ ] Plugin deactivate → button vẫn valid, không có block recovery

---

## 8. Bài học cho agent và dev tương lai

### L1 — Gutenberg block extension có 5 layer riêng biệt

Mỗi layer có cách verify khác nhau, không thể suy luận từ layer khác:

| Layer | Tool verify |
|---|---|
| DB attrs | `wp post get --field=post_content \| grep skvnHover` |
| PHP filter hook | `wp eval "has_filter('render_block_core/button', …)"` |
| HTML output | DevTools Elements → `getAttribute('style')` |
| CSS specificity | DevTools → hover element → Styles tab → xem strikethrough |
| Editor DOM | DevTools trong editor iframe → wrapper element |

### L2 — Specificity phải check trước khi viết consuming rule

Không bao giờ viết rule `:hover` cho plugin mà không biết theme rule mạnh nhất cho element đó là bao nhiêu. Đây là bước đầu tiên, không phải bước cuối.

### L3 — Pivot phải atomic

Khi bỏ một cơ chế (scoped `<style>` → inline vars), phải thay bằng cơ chế tương đương ngay trong cùng commit. Thiếu replacement = silent regression.

### L4 — Test phải verify behavior, không verify source code

```
BAD:  assert.match(phpSource, /--skvn-btn-hover-text:/)
      → Pass kể cả khi hover hoàn toàn không hoạt động

GOOD: const output = renderButtonHover(mockHtml, mockBlock);
      assert(output.includes('has-skvn-button-hover'));
      assert(output.includes('--skvn-btn-hover-text:#fff'));
```

### L5 — `editor.BlockEdit` ≠ `editor.BlockListBlock`

`BlockEdit` → Inspector panel, không access wrapper DOM.
`BlockListBlock` → wrapper block, có `wrapperProps`.

Nhầm filter = editor preview không hoạt động, không có error.

### L6 — Bundle boundary

Enqueue bundle để load 15 dòng CSS là sai boundary. Dùng `wp_add_inline_style`
cho CSS nhỏ, hoặc file riêng với handle riêng có dependency đúng.
