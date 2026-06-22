# Ideation — End-User Deep Customization (SKVN Marine)

Status: **BRAINSTORM** — chưa quyết định, chưa implement  
Date: 2026-06-19  
Owner: Human (product direction)  
Related:

- **Decisions đã chốt (hero + mobile surfaces):** `docs/decisions/single-post-layout-and-mobile-surfaces.md`
- Bug/fix tức thời: `.context/planning/030_VER_1_3_7_SINGLE-POST-HERO-LAYOUT.md`
- UI debug evidence: `docs/testing/single-post-hero-layout-review-1.3.6.md`
- Editor governance: `.context/planning/004_VERSION_0_8_0_EDITOR_CONTROLS_PLANNING.md`
- Surface presets: `.context/planning/009_VERSION_1_6_0_SKVN_SURFACE_PRESETS_PLANNING.md`
- Inspiration workflow mẫu: `docs/workflows/ideation-chom-revslider-theme-tra-phi-to-gutenberg.md`

---

## Mục đích

Ghi lại hướng sản phẩm: **marketing end-user tùy biến sâu** mà vẫn trong governance SKVN (preset/token, không raw CSS/class).

Đây **không** phải milestone approved. Nhiều hạng mục khác vẫn đang sửa — file này chỉ để nhớ và brainstorm khi đến lượt.

---

## Bối cảnh kích hoạt

1. Single post onsite (`/iqf-technology-for-seafood-industry/`) — hero bị GP `.site-content` flex xếp ngang (UI debug Grok).
2. Plan implement tức thời (Haiku **030**): restructure `single.php` + CSS grid — hero top-left, sidebar span 2 rows.
3. Human goal: không chỉ **fix layout**, mà **end-user customize sâu** về sau (layout, surface, typography…).

**Tách rõ:**

| Việc | Loại | File |
|------|------|------|
| Đóng bug hero sai vị trí | Fix tức thời | `030` |
| End-user deep customization | Brainstorm / future | **file này** |

---

## Nguyên tắc SKVN (không đổi khi brainstorm)

```text
Theme owns:     tokens, visual classes, template shells, CSS parity
Plugin owns:    block sidebar UI, attributes, interactive behavior
Content owns:   copy, media, links, selected preset values
Content KHÔNG:  raw class, raw hex, arbitrary spacing, inline style/script
```

Tham chiếu: `AGENTS.md` SKVN Editor Controls, `004_VERSION_0_8_0_EDITOR_CONTROLS_PLANNING.md`.

---

## Hiện trạng — end-user customize được gì?

| Layer | Hiện tại | Độ sâu |
|-------|----------|--------|
| SKVN blocks | Sidebar controls (đang mở rộng 1.3.6) | Khá sâu **trong block** |
| Pages | Hide header/footer, Landing Canvas (0.5.1) | Vừa |
| Single post / archive / product | PHP Style C **cố định** | Gần như không (chỉ body content Gutenberg) |
| Site-wide | Font preset Customizer, Typography admin | Global, chưa layout |

---

## Mô hình 3 tầng (đề xuất brainstorm)

```text
Tầng 1 — Site defaults
  Customizer / SKVN Admin
  → font, palette, default layout mode, surface family

Tầng 2 — Template surfaces (post / product / archive)
  Post meta hoặc document sidebar
  → hero layout, sidebar on/off, island variants, archive density

Tầng 3 — In-content
  SKVN blocks + core blocks
  → Content / Style / Layout / Advanced (0.8.0 model)
```

“Tùy biến sâu” thực tế = **Tầng 2 + 3**, không chỉ Customizer global.

---

## Single post hero — options layout (từ plan 030 + mở rộng)

### Option A — Banner (full-width hero)

```text
┌──────────────────────────────────┐
│         .skvn-post-hero          │  grid-column: 1 / -1
├──────────────────────┬───────────┤
│  .skvn-single-main   │  sidebar  │
└──────────────────────┴───────────┘
```

### Option B — Magazine (default sau fix 030)

```text
┌─────────────────────┬───────────┐
│   .skvn-post-hero   │  sidebar  │  row 1
├─────────────────────┤           │
│  .skvn-single-main  │           │  row 2
└─────────────────────┴───────────┘
```

### Option C — No hero (future)

Featured image chỉ trong content hoặc ẩn hero shell.

### Gợi ý kỹ thuật khi implement sau này

- CSS theo **body class** hoặc **post meta**, không một grid cứng duy nhất:
  - `.skvn-post-layout--banner`
  - `.skvn-post-layout--magazine`
  - `.skvn-post-layout--no-hero`
- Control surface: post editor sidebar (marketing) hoặc Customizer default + per-post override.
- Plan 030 có thể implement **magazine** trước; giữ **class contract** để Option A/C thêm sau không rewrite PHP.

### Layout preset vs hero bleed (margin âm) — tách hai tầng

Brainstorm 2026-06-19 (thread mobile hero full-width): **margin âm gutter** và **layout preset** là hai concern khác nhau; không gộp vào một rule CSS duy nhất.

```text
Tầng layout (preset)     → ai nằm hàng/cột nào trong .skvn-single-layout
Tầng surface / bleed     → hero có chạm mép canvas hay nằm trong gutter wrap
```

| Tầng | Ví dụ class | Trả lời câu hỏi |
|------|-------------|-----------------|
| Layout preset | `.skvn-post-layout--magazine`, `--banner` | Hero cạnh sidebar hay full hàng trên? |
| Bleed modifier | `.skvn-post-hero--bleed` (tên gợi ý) | Ảnh/gradient hero có cancel `--skvn-page-gutter` không? |

**Magazine (hiện tại, D1):** hero cột trái row 1; sidebar span 2 rows — không “full hàng” trên desktop.

**Banner (Option A, future):**

```text
┌──────────────────────────────────┐
│           Hero                   │  grid-column: 1 / -1
├──────────────────────┬───────────┤
│       Content        │  Sidebar   │
└──────────────────────┴───────────┘
```

Desktop banner: **grid span** (`grid-column: 1 / -1`) — **không** cần margin âm; wrap vẫn owner gutter, hero placement do grid.

**Bleed trong wrap (mobile / tùy surface):** khi wrap còn `padding-inline: var(--skvn-page-gutter)` mà muốn hero **visual** sát mép SKVN canvas:

```css
.skvn-post-hero--bleed {
	margin-inline: calc(-1 * var(--skvn-page-gutter));
}
.skvn-post-hero--bleed .skvn-post-hero__content {
	padding-inline: var(--skvn-page-gutter);
}
```

- Cùng pattern đã dùng cho `.skvn-island--navy` (decision D5/D7) — sync token, không hardcode `-1rem`.
- **Không** dùng `100vw` / margin viewport (css-layout-safety D3).

**Matrix gợi ý khi có preset:**

| Preset | Desktop hero full hàng? | Bleed modifier |
|--------|-------------------------|----------------|
| `--magazine` | Không (cột trái) | Optional mobile `--bleed` |
| `--banner` | Có (`grid-column: 1 / -1`) | Desktop: thường không; mobile: optional `--bleed` nếu wrap còn gutter |
| `--no-hero` | N/A | N/A |

**Anti-patterns (không implement):**

- Gắn `margin-inline: calc(-1 * …)` vào `.skvn-post-hero` **default** — banner desktop đã full grid, margin âm thừa và dễ lệch sidebar.
- Nhầm preset banner với bleed — banner = đổi grid; bleed = cancel gutter wrap.
- Đổi HTML `single.php` mỗi lần thêm preset — chỉ đổi class trên `.skvn-single-layout` (+ optional modifier trên hero).

**Lộ trình gợi ý:**

```text
V1 / now (tuning)   Magazine + optional .skvn-post-hero--bleed @mobile (chưa preset picker)
~1.6.0            Layout class contract + Customizer default; surface presets (009) có thể map bleed
```

Liên quan surface presets (009): `skvn-surface--flat` / `--edge` có thể **delegate** bleed modifier — một implementation, không duplicate logic per layout.

---

## Phạm vi “tùy biến sâu” — bảng brainstorm

| ID | Hướng | Độ sâu | Milestone gợi ý | Ghi chú |
|----|-------|--------|-----------------|---------|
| A | Layout presets (hero, sidebar, archive grid) | Cao cho templates | 1.3.7+ hoặc 1.6.0 | Khớp bug hero; ít đổi kiến trúc |
| A1 | Hero image crop position (top/center/bottom) | Thấp | 1.7.0 hoặc V2 | Featured image dùng ở nhiều nơi, giữ aspect flexible; mặc định center center (1.3.7); thêm post meta / Customizer chọn `object-position` sau |
| B | Surface presets (flat, glass, elevated…) | Cao cho blocks/islands | 1.6.0 | Planning 009 sẵn |
| C | Typography delivery + preset | Trung bình | 1.6.0 | Governed fonts, không arbitrary URL |
| D | Full block theme / FSE thay PHP templates | Rất sâu | 2.x | Đổi phase — không V1 |
| E | Elementor-style freeform | Rất sâu | — | **Rejected** — trái governance |

---

## Câu hỏi mở (chờ human quyết định)

1. **Per-post** hero layout (mỗi bài chọn banner vs magazine) — ưu tiên cao không?
2. **Site-wide** Customizer default trước, per-post override sau — thứ tự nào?
3. Single post **sidebar**: cho tắt hoàn toàn / đổi islands (quote CTA, related) không?
4. “Sâu” có gồm **archive + single product** layout presets **cùng lúc** với post không?
5. Fix **030** có cần **mở class contract** ngay (customization-ready) hay chỉ đóng bug, customize milestone sau?

---

## Không thuộc brainstorm này (tracked elsewhere)

- WooCommerce Coming Soon che product page
- CF7 quote data-flow (1.1.2)
- Typography scope isolation (1.3.6 / 029)
- WindPress/Tailwind làm production contract (1.6.0 explicitly rejects)

---

## Next steps (khi human sẵn sàng quyết định)

1. Trả lời câu hỏi mở → chọn 1–2 hướng ID (A/B/C).
2. Nếu approve: tạo `.context/planning/03x_...` hoặc mở rộng `1.6.0` checklist.
3. Nếu 030 đang implement: xác nhận Haiku có thêm layout class hook hay chỉ fix magazine.
4. Cập nhật `.context/MILESTONES.md` **chỉ khi** human declare milestone mới.

---

## Changelog

| Date | Note |
|------|------|
| 2026-06-19 | Tạo brainstorm từ thread Grok UI debug + human goal “end-user tùy biến sâu” |
| 2026-06-19 | Thêm A1: Hero image crop position (object-position) — mặc định center center (030), future customize top/center/bottom |
| 2026-06-19 | Link decision doc `docs/decisions/single-post-layout-and-mobile-surfaces.md` — tách decided vs brainstorm |
| 2026-06-19 | Thêm § layout preset vs hero bleed: modifier `.skvn-post-hero--bleed`, banner = grid span, margin âm ≠ preset |