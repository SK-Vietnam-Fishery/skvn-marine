# 037 — SKVN TOC Block + Shortcode (native dynamic) + hội tụ đánh số

```
Version:   1.5.3
Date:      2026-06-28
Status:    PLANNED (brainstorm + làm ở 1.5.3)
Home:      plugin skvn-marine-blocks (custom block — đúng rule "block trong plugin")
Ref:       Obsidian "Nghiên cứu làm block Table of Content - gutenberg supercharger"
           Phản biện: docs/ideations/pending-debts-1.3.x.md (mục TOC native)
Liên quan: post-heading-numbering-1.3.14 (sẽ chuyển nguồn số sang module này)
```

> Open questions cuối doc **chốt trong brainstorm đầu 1.5.3** trước khi code.

---

## Context

Sidebar TOC tạm (1.3.12) chỉ là field paste HTML/shortcode — phụ thuộc Essential Blocks với `headers` serialize cứng. Cần TOC **native dynamic** của SKVN: vừa **Gutenberg block**, vừa **shortcode** (cắm vào sidebar/bất kỳ đâu, kể cả field TOC global hiện có).

Đồng thời, đánh số heading (1.3.14) đang dùng **CSS counter** → không biết cây heading thật → nhảy cấp ra `1.0.1`, và **TOC không có số**. Hai hệ cùng cần một thứ: **cây heading**. 1.5.3 hợp nhất chúng.

Quyết định kiến trúc (đã phản biện + chốt hướng):
- **Dynamic block** (server `render_callback`) → đọc heading bài hiện tại lúc render, luôn tươi.
- **Hybrid:** server render HTML (SEO, no-flash) + client JS (active-highlight/sticky/smooth).
- **ID heading ổn định** `sanitize_title` server-side (không id theo index → không vỡ deep-link).
- **`parse_blocks` đệ quy** innerBlocks (Group/Columns) — bắt buộc.
- **Một nguồn cây heading duy nhất** cho cả TOC **và** numbering.

---

## Hội tụ với đánh số (1.3.14) — thiết kế đã chốt

**Nguyên tắc: service PHP là nguồn SỐ + ID duy nhất, feed cả thân bài lẫn TOC.**

```
extract_post_headings($post, $cfg)  →  [ { level, text, id, number } ]
   (parse_blocks đệ quy + dedupe id + TÍNH SỐ tree-aware theo depth/style)
                           │
                ┌──────────┴───────────┐
                ▼                      ▼
  render_block(core/heading)     TOC block / shortcode
  inject id + data-skvn-num      render: number + text + link (cùng list)
      │
      ▼
  CSS: .entry-content h2::before { content: attr(data-skvn-num) " " }
```

- **Số vào thân bài:** filter `core/heading` gắn `data-skvn-num="1.1"` (PHP tính); CSS hiển thị bằng `content: attr(data-skvn-num)`. **Bỏ `counter()`**.
- **Số vào TOC:** TOC dùng **chính list đó** → `number + text` mỗi item.

**Giải được:**
1. **Skip-level "1.0.1"** — PHP biết cấp nào tồn tại → nhảy cấp ra `1.1` đúng. Hết nợ.
2. **Đồng bộ số TOC ↔ thân bài** — cùng một list → không lệch. Hết nợ.
3. `.no-number` vẫn chạy — service bỏ qua heading có class đó khi đánh số **và** TOC loại.

**Tái dùng từ 1.3.14 (không vứt):** giữ config `skvn_heading_number` (depth/style), feature flag `post_heading_numbers` (bật/tắt hiển thị số), toggle `.no-number`. **Chỉ thay** nguồn số: CSS `counter()` → CSS `attr(data-skvn-num)`.

**Trade-off đã chấp nhận:**
- Numbering phụ thuộc service của TOC (cùng sống/chết) — đúng tinh thần "một nguồn cây heading".
- Số vẫn là `::before`/`attr` → không nằm DOM text (copy không dính số). Muốn số "thật" trong text → inject `<span>` vào innerHTML (nặng hơn) — **để brainstorm 1.5.3 quyết**.

---

## Core service (PHP, dùng chung block + shortcode + numbering + anchor)
- `skvn_marine_blocks_extract_post_headings( $post, $cfg )` — `parse_blocks` **đệ quy**, lọc `minLevel/maxLevel`, **dedupe id** (`-2`,`-3`), **tính `number`** tree-aware theo `depth/style` + bỏ qua `.no-number`. Cache **transient** `skvn_toc_{post_id}`, invalidate `save_post`/`post_updated`.
- `render_block` filter `core/heading` → inject `id` + `data-skvn-num` (idempotent, tôn trọng anchor sẵn có), tiêu thụ list theo thứ tự.

---

## Phases

### Phase 1 — Shared heading service + anchor/number injection
**Files:** `modules/toc-render/toc-render.php` (service + filter).
- `extract_post_headings()` (đệ quy + dedupe id + tính number + transient + invalidate).
- `render_block_core/heading` inject `id` + `data-skvn-num`.
**AC:** Group/Columns vẫn lấy đủ heading; id ổn định qua reload; anchor sẵn có không bị ghi đè; number tree-aware đúng (nhảy cấp không ra `0`); `php -l` sạch.

### Phase 2 — Dynamic block `skvn-marine/toc`
**Files:** `src/toc/{block.json,edit.tsx,save.tsx}`, render_callback trong `toc-render.php`.
- **Attributes:** `title`, `minLevel`(2), `maxLevel`(4), `sticky`, `showNumbers`, `collapsible`, `ordered`.
- **render_callback:** dùng `extract_post_headings()` → `<nav class="skvn-toc">`.
- **edit.tsx:** `useSelect(getBlocks)` → live preview trong editor.
**AC:** preview đúng; frontend link nhảy đúng; đổi nội dung → TOC đổi; số khớp khi `showNumbers`; `npm run build` sạch.

### Phase 3 — Shortcode `[skvn_toc]`
**Files:** `toc-render.php` (add_shortcode).
- `[skvn_toc title="" min="2" max="4" sticky="1" numbers="0"]` → cùng render function.
- **Migration:** dán `[skvn_toc]` vào field TOC global (1.3.12) chạy ngay, **không đổi theme** → bỏ phụ thuộc Essential Blocks.
**AC:** shortcode trong content + sidebar field đều đúng; param map đúng attributes.

### Phase 4 — View JS + CSS (hybrid client)
**Files:** `src/toc/{view.ts,style.css}`.
- `IntersectionObserver` active-highlight; **sticky trừ offset header** (giải nợ sticky-sidebar) qua `--skvn-header-h` + `scroll-margin-top` cho heading; smooth scroll; `prefers-reduced-motion`; collapsible optional.
**AC:** active chạy; click nhảy không bị header che; sticky không tràn; reduced-motion ok.

### Phase 5 — Chuyển nguồn số (retire CSS-counter)
**Files:** sửa `modules/core-control/features/post-heading-numbers.php`.
- Đổi CSS body từ `counter()` → `attr(data-skvn-num)`; numbering 1.3.14 thành **consumer** của service (không tự tính nữa).
- Number vào TOC bật theo `showNumbers`.
**AC:** số TOC == số thân bài; nhảy cấp không ra `1.0.1`; tắt feature → hết số; 1.3.14 cũ không kéo lùi (chỉ đổi nguồn).

---

## Open questions — chốt trong brainstorm đầu 1.5.3
1. **showNumbers** default on/off? Auto-bật khi `post_heading_numbers` đang on?
2. Số "thật" trong DOM text (inject `<span>`) hay giữ `data-attr` + CSS?
3. Anchor injection áp mọi `post` hay chỉ khi trang có TOC? (đề xuất: mọi `post`.)
4. Scope: chỉ `post` hay cả `page`/`product`?
5. Cache TTL + invalidate khi đổi heading qua REST/bulk.
6. Có retire hẳn CSS-counter (1.3.14) hay giữ fallback khi service tắt?

## Risks
- `parse_blocks` chi phí bài dài → transient + invalidate đúng.
- ID slug đổi khi sửa tiêu đề → deep-link cũ có thể đứt (bản chất slug anchor; chấp nhận như WP core).
- Coupling numbering↔TOC ở Phase 5 — làm sau cùng, tách bạch để không kéo lùi 1.3.14.

## Files tổng (dự kiến)
- `modules/toc-render/toc-render.php` (service + render_callback + shortcode + anchor/number filter)
- `src/toc/{block.json,edit.tsx,save.tsx,view.ts,style.css}`
- `skvn-marine-blocks.php` (require) + build/register
- sửa `modules/core-control/features/post-heading-numbers.php` (Phase 5)

## Tension
Anchor/number injection sửa output `core/heading` (render_block filter) — coupling vào core block nhưng idempotent + tôn trọng anchor sẵn có (giống button-hover). Numbering phụ thuộc service TOC — chấp nhận có chủ ý.
