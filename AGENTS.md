# AGENTS.md — SKVN Marine

> Đọc file này TRƯỚC KHI làm bất kỳ task nào.
> Đây là protocol bắt buộc cho mọi AI agent làm việc trong project này.

---

## Reference Files — Đọc khi cần, không load mặc định

1. Architecture, naming, PHP security: `AGENTS_ARCH.md`
2. Coding standards & animation: `AGENTS_STANDARDS.md`
3. Module-specific rules: `AGENTS_MODULES.md`
4. Environment, runtime, WSL, WP-CLI: `AGENTS_ENV.md`

---

## 0. Milestone hiện tại

Current milestone: **V1 / 0.6.0 — Quote UI & Editor Controls**

Milestone source of truth: `.context/MILESTONES.md`.

Quy tắc quản lý tension theo milestone:

- Trong cùng milestone hiện tại, tension đã `RESOLVED_ACTIVE` nằm trong `.context/TENSIONS_ACTIVE.md` để agent thấy decision liên quan khi làm task V1.
- Chỉ khi human tuyên bố chuyển sang milestone/version mới (ví dụ V1 → V2), agent mới move các tensions đã `RESOLVED_ACTIVE` của milestone cũ sang `.context/TENSIONS_HISTORY.md` và đổi `Status: ARCHIVED`.
- Khi chuyển milestone, agent phải update cả `AGENTS.md` và `.context/MILESTONES.md`, đồng thời move milestone cũ sang `.context/MILESTONES_HISTORY.md`.
- `.context/TENSIONS_HISTORY.md` không load mặc định, chỉ đọc khi cần audit quyết định cũ hoặc human yêu cầu.

---

## 1. Đọc trước — Bắt buộc

Mỗi task bắt đầu bằng 4 bước này, không có ngoại lệ:

```
1. Đọc .context/GLOBAL.md       → hiểu stack, module index, invariants
2. Đọc .context/MILESTONES.md   → biết milestone hiện tại và checklist liên quan
3. Đọc .context/TENSIONS_OPEN.md     → check toàn bộ OPEN tensions
4. Đọc .context/TENSIONS_ACTIVE.md   → check RESOLVED_ACTIVE tensions của milestone hiện tại, dùng tag filter
5. Đọc .context/modules/<module>.md  → load context của module sắp sửa
```

Nếu `.context/modules/<module>.md` chưa tồn tại → load `GLOBAL.md` + hỏi lại trước khi tạo file mới.

Nếu `[manual]` section trong module file còn template placeholder (`<!-- Viết tại đây -->`) → **DỪNG, báo lại**, không tự điền assumption.

### Context load rules

- `.context/planning/*.md` chỉ đọc khi task liên quan scope, UX direction, layout strategy, version planning, hoặc quyết định future block boundary.
- `.context/proposals/` là local review/discussion artifact, không phải active protocol. Không load mặc định và không treat như source of truth.
- `.context/MILESTONES_HISTORY.md` không load mặc định. Chỉ đọc khi audit milestone cũ, migrate milestone, hoặc human yêu cầu.
- `.context/TENSIONS_HISTORY.md` không load mặc định. Chỉ đọc khi audit quyết định cũ, migrate tension, hoặc human yêu cầu.
- Folder structure giúp human đọc đỡ rối; startup protocol trong `AGENTS.md` mới là source of truth cho agent load order.

### Encoding rules

- Context, docs, PHP, CSS, JS/TS, JSON, and HTML files must remain UTF-8.
- Do not save text files through a tool path that turns Vietnamese or punctuation into mojibake.
- In PowerShell, read/write text explicitly as UTF-8 when touching context/docs, for example `Get-Content -Encoding UTF8` and .NET `UTF8Encoding(false)` for bulk rewrites.
- If mojibake appears after reading a file, stop and fix encoding before making semantic edits.

---

## 3. Workflow — Mỗi Task

### Bước 0 — Xác định ref files cần load trước khi làm

Đọc task description, map vào trigger bên dưới, load file tương ứng TRƯỚC khi load module context.

| Nếu task liên quan đến... | Load thêm |
|---|---|
| Tạo file PHP mới, function mới, hook mới | `AGENTS_ARCH.md` |
| Rename, đổi prefix, đổi namespace, đổi slug | `AGENTS_ARCH.md` |
| Viết block mới, animation, TypeScript | `AGENTS_STANDARDS.md` |
| Review code, PR check, coding convention | `AGENTS_STANDARDS.md` |
| Module cụ thể (slider, quote, branding...) | Section tương ứng trong `AGENTS_MODULES.md` |
| WP-CLI, bật server, build plugin, deploy | `AGENTS_ENV.md` |
| Cài plugin, symlink, copy sang runtime | `AGENTS_ENV.md` + `AGENTS_ARCH.md` |
| Không rõ task thuộc loại nào | Load `AGENTS_ARCH.md` + `AGENTS_STANDARDS.md`, bỏ qua ENV và MODULES |

**Rule bắt buộc:**
- Nếu task đề cập tên module cụ thể → load section đó trong `AGENTS_MODULES.md`, không load cả file
- Nếu task cross-module hoặc không rõ boundary → load `AGENTS_ARCH.md` trước, detect tension nếu có conflict
- Nếu không chắc có cần load file nào không → load, đừng skip

### Bước 1 — Load context và implement

```
1. Load context (GLOBAL → MILESTONES → TENSIONS_OPEN → TENSIONS_ACTIVE → module)
2. Đọc [manual] của module — hiểu constraints và invariants
3. Nếu [manual] còn placeholder → DỪNG, hỏi lại
4. Detect tensions (xem Section 4)
5. Nếu tension HIGH → generate `TENSIONS_OPEN.md` entry → DỪNG, chờ quyết định
6. Nếu tension LOW hoặc không có → tiếp tục
7. Plan changes — liệt kê files sẽ sửa (≤5 files trừ khi có lý do)
8. Implement — follow coding standards bên dưới
9. Self-check (xem checklist cuối file)
10. Update .context/ nếu có quyết định mới
11. Chỉ khi human chuyển version/milestone mới, update `AGENTS.md` + `.context/MILESTONES.md`, move milestone cũ sang `.context/MILESTONES_HISTORY.md`, và move tension `RESOLVED_ACTIVE` của milestone cũ khỏi `.context/TENSIONS_ACTIVE.md` sang `.context/TENSIONS_HISTORY.md` với `Status: ARCHIVED`
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

### Format ghi vào `.context/TENSIONS_OPEN.md`

```markdown
## [YYYY-MM-DD HH:MM] | [module]
Tension:    Mô tả conflict ngắn gọn
Context:    Đang làm task gì
Proposal:   Agent muốn làm gì
Constraint: [manual] rule nào conflict (quote lại)
Severity:   low | high
Tags:       tag-one, tag-two
Milestone:  V1 / 0.x.0
Status:     OPEN
Resolved:
Decision:   OPEN
```

Agent không được parse `Decision` để suy ra state. Chỉ dùng `Status`.

Allowed `Status`: `OPEN`, `RESOLVED_ACTIVE`, `ARCHIVED`.

Allowed `Severity`: `low`, `high`.

Tag taxonomy hiện tại:

```text
agent
a11y
blocks
editor-governance
map
milestone
multilingual
patterns
php
planning
product-data
quote-flow
schema
slider
spam-protection
tailwind
theme
woocommerce
```

Khi cần tag mới, update taxonomy trong file này trước khi dùng.

### Routing

```
Severity LOW  → ghi tension → tiếp tục theo hướng conservative → báo human review sau
Severity HIGH → ghi tension → DỪNG task → chờ human fill Decision
```

### Tension files và archive

`.context/TENSIONS_OPEN.md` chỉ chứa `Status: OPEN`.

`.context/TENSIONS_ACTIVE.md` chỉ chứa `Status: RESOLVED_ACTIVE` của milestone hiện tại.

`.context/TENSIONS_HISTORY.md` chỉ chứa `Status: ARCHIVED`.

Khi human resolve một OPEN tension, agent move entry từ `TENSIONS_OPEN.md` sang `TENSIONS_ACTIVE.md`, set `Status: RESOLVED_ACTIVE`, set `Resolved: YYYY-MM-DD`, và ghi decision.

Khi human tuyên bố chuyển sang version/milestone mới, agent phải move các entry `Status: RESOLVED_ACTIVE` của milestone cũ sang `.context/TENSIONS_HISTORY.md`, đổi `Status: ARCHIVED`.

Không load `.context/TENSIONS_HISTORY.md` mặc định khi bắt đầu task. Chỉ đọc history khi cần audit quyết định cũ hoặc human yêu cầu.

`Status: OPEN` luôn đọc đầy đủ, không filter theo tag. `TENSIONS_ACTIVE.md` được đọc với tag filter: entries match task tag đọc đầy đủ; entries không match chỉ đọc title/status/tags. Nếu không chắc, đọc đầy đủ.

Milestone checklist và trigger chuyển mốc được quản lý trong `.context/MILESTONES.md`. Khi chuyển milestone, milestone cũ phải được archive sang `.context/MILESTONES_HISTORY.md`.

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

## 7.1 Project Skill Copy

Project keeps a repo-local copy of the HTML-2-Gutenberg skill for audit and handoff:

```text
.agents/skills/html-2-gutenberg/SKILL.md
```

The auto-discovered machine-local install may live at:

```text
C:\Users\VPF-Champion\.codex\skills\html-2-gutenberg\SKILL.md
```

When updating HTML-2-Gutenberg rules, keep both copies in sync if the machine-local skill exists.

HTML-2-Gutenberg boundary:

- Plugin `skvn-marine-blocks` owns artifact intake, translation tooling, validation, and future admin publisher/create-page flow.
- Theme `skvn-marine` owns visual output contract: `skvn-*` classes, CSS, tokens, patterns, editor/frontend parity, and shared animation runtime.
- Before translating artifacts, inspect and reuse the existing theme CSS/pattern contract.
- Current translated-layout CSS families to prefer: `skvn-translated-*`, `skvn-kpi-strip*`, `skvn-section__*`, `skvn-placeholder-media`, `skvn-card`, `skvn-button*`, and registered `is-style-skvn-*`.
- New layout-critical `skvn-*` classes must be implemented in theme CSS in the same task or listed in `missing_theme_classes`; do not silently rely on missing CSS.
- Do not paste raw `<style>`, `<script>`, base64/data URI image, or decorative SVG/canvas into Gutenberg content.
- Do not implement HTML-2-Gutenberg tooling/admin logic in the theme.

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
[ ] TENSIONS_OPEN.md updated nếu có conflict phát sinh
[ ] TENSIONS_ACTIVE.md updated nếu human resolve tension trong milestone hiện tại
[ ] context-gen check-consistency . passed
[ ] MILESTONES.md updated nếu milestone checklist/status thay đổi
[ ] Chỉ archive resolved tensions khi human chuyển version/milestone mới
[ ] .context/modules/<module>.md updated nếu có decision mới
```

---

## 9. Versioning Milestones (để biết scope hiện tại)

| Version | Scope |
|---|---|
| 0.1.0 | Child theme skeleton |
| 0.2.0 | Design system, block styles, patterns |
| 0.3.0 | Slider/Slide block |
| 0.4.0 | Woo product grid/list |
| 0.5.0 | SKVN Full Width layout |
| 0.5.1 | Page display and sidebar controls |
| 0.6.0 | Quote UI and editor controls |
| 0.7.0 | Brand profile and theme tokens |
| 0.8.0 | SKVN editor controls |
| 1.0.0 | V1 launch-ready |
| 1.1.0 | Visual governance layer |
| 2.0.0 / Future Candidate | Brand system productization |

### Version naming rules

- Version dùng SemVer: `MAJOR.MINOR.PATCH`.
- `MAJOR` tăng khi đổi phase lớn hoặc đổi kiến trúc/phạm vi sản phẩm lớn, ví dụ `1.x.x` → `2.0.0`.
- `MINOR` tăng khi thêm feature/scope mới nhưng vẫn cùng major, ví dụ `1.0.0` → `1.1.0`.
- `PATCH` tăng khi fix, hardening, hoặc integration nhỏ trong cùng minor, ví dụ `0.5.0` → `0.5.1`.
- Version launch-ready của một major là `MAJOR.0.0`, ví dụ `1.0.0` là V1 launch-ready, `2.0.0` là V2 launch-ready.
- Không dùng nhãn kiểu `1.0.0 Prep` cho feature mới. Nếu là prep trước launch, nó phải nằm trong milestone trước launch hoặc ghi `Future Candidate`.
- Future planning phải ghi đúng target version dự kiến. Nếu chưa chắc version, ghi `Future Candidate` thay vì tự gán version.
- Tên file planning phải khớp version chính trong nội dung. Ví dụ planning cho `1.1.0` dùng dạng `001_VERSION_1_1_0_<TOPIC>_PLANNING.md`; planning cho `2.0.0` dùng dạng `002_VERSION_2_0_0_<TOPIC>_PLANNING.md`.
- Không đổi tên planning file hoặc milestone version nếu chưa có human xác nhận.
- Khi đổi version/milestone thật, phải update đồng bộ `AGENTS.md`, `.context/MILESTONES.md`, và file planning/docs liên quan.

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
