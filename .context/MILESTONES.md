# MILESTONES.md — SKVN Marine

> Source of truth cho milestone hiện tại và checklist chuyển mốc.
> File này phải được đọc khi bắt đầu task.
> Chỉ human mới có quyền xác nhận chuyển milestone/version.

---

## Current Milestone

Current: **V1 / 0.6.0 — Quote UI & Editor Controls**
Status: **IN_PROGRESS**
Started: **2026-06-01**

AGENTS.md current milestone phải match file này.

---

## Transition Rule

Chỉ chuyển milestone khi:

- Tất cả acceptance checklist của milestone hiện tại đã DONE.
- Runtime smoke test liên quan đã chạy.
- Human explicitly approve chuyển milestone.

Khi chuyển milestone:

1. Update `AGENTS.md` current milestone.
2. Update `.context/MILESTONES.md` current milestone.
3. Move completed milestone checklist/notes sang `.context/MILESTONES_HISTORY.md`.
4. Move `RESOLVED_ACTIVE` tensions của milestone cũ từ `.context/TENSIONS_ACTIVE.md` sang `.context/TENSIONS_HISTORY.md`, đổi `Status: ARCHIVED`.
5. Giữ lại OPEN tensions còn liên quan trong `.context/TENSIONS_OPEN.md`.
6. Không tự archive hoặc tự chuyển milestone nếu human chưa approve.

## Version Naming Rule

- Version dùng SemVer: `MAJOR.MINOR.PATCH`.
- `MAJOR` tăng khi đổi phase lớn hoặc đổi kiến trúc/phạm vi sản phẩm lớn, ví dụ `1.x.x` → `2.0.0`.
- `MINOR` tăng khi thêm feature/scope mới nhưng vẫn cùng major, ví dụ `1.0.0` → `1.1.0`.
- `PATCH` tăng khi fix, hardening, hoặc integration nhỏ trong cùng minor, ví dụ `0.5.0` → `0.5.1`.
- Version launch-ready của một major là `MAJOR.0.0`, ví dụ `1.0.0` là V1 launch-ready, `2.0.0` là V2 launch-ready.
- Không dùng nhãn kiểu `1.0.0 Prep` cho feature mới. Nếu là prep trước launch, nó phải nằm trong milestone trước launch hoặc ghi `Future Candidate`.
- Nếu chưa chắc version của future work, ghi `Future Candidate` thay vì tự gán version.
- Planning filename phải khớp target version chính, ví dụ `001_VERSION_1_1_0_<TOPIC>_PLANNING.md` hoặc `002_VERSION_2_0_0_<TOPIC>_PLANNING.md`.
- Không đổi current milestone/version nếu chưa có human approve rõ ràng.

---

## V1 Checkpoints

### 0.6.0 — Quote UI & Editor Controls

Status: **IN_PROGRESS**
Started: **2026-06-01**

Acceptance:

- [ ] Quote CTA visual path uses `/request-a-quote/?product_id=123`
- [ ] Request quote page visual surface exists or is planned as an editor-owned page
- [ ] Quote UI uses project visual classes/tokens
- [ ] Editing controls that marketing users need are exposed through sidebar controls, not raw class input
- [ ] Runtime visual smoke test passed
- [ ] Human approves milestone completion

### 0.7.0 — Brand Profile & Theme Tokens

Status: **PENDING**

Purpose:

- Create a theme-owned brand profile layer so translated Gutenberg pages map prototype colors and visual intent into SKVN theme tokens instead of raw artifact colors.
- Keep HTML-2-Gutenberg as a translator/tooling concern, while the theme owns actual brand variables, editor/frontend parity, and visual output.

Acceptance:

- [ ] Brand profile source of truth is documented before code.
- [ ] External references are recorded for manual development without AI.
- [ ] Theme token names are defined for primary, accent, dark/navy, surface, text, CTA, card radius, card shadow, and section spacing.
- [ ] `theme.json` presets and `style.css` CSS variables have a documented sync rule.
- [ ] Editor and frontend use the same token contract.
- [ ] HTML-2-Gutenberg output can report `brand_source_scan`, `brand_mapping`, `brand_mismatch`, and `token_changes_needed`.
- [ ] No raw prototype colors are required in Gutenberg content.
- [ ] GeneratePress parent remains untouched.

### 0.8.0 — SKVN Editor Controls

Status: **PENDING**

Purpose:

- Add Elementor-inspired but token-governed sidebar controls for SKVN-owned Gutenberg blocks and translated layout surfaces.
- Let editors adjust tone, spacing, width, margin, padding, responsive visibility, and block-specific behavior without raw classes, raw hex values, or unrestricted inline CSS.
- Keep theme-owned visual tokens as the source of truth while plugin-owned blocks expose safe presets.

Acceptance:

- [ ] Editor controls contract is documented before code.
- [ ] Theme owns tone, spacing, width, radius, shadow, and visual classes.
- [ ] Plugin owns block sidebar UI, block attributes, saved markup, and interactive block behavior.
- [ ] Controls are grouped into Content, Style, Layout, and Advanced sections.
- [ ] Margin and padding controls use presets/tokens first, with responsive overrides only where needed.
- [ ] No freeform raw class input is required for marketing editors.
- [ ] No raw hex/rgb/hsl values or arbitrary inline spacing values are required in Gutenberg content.
- [ ] Slider editor UX tension is resolved before implementing slider-specific controls.
- [ ] Editor and frontend output stay visually aligned.
- [ ] GeneratePress parent remains untouched.

### 1.0.0 — V1 Launch Ready

Status: **PENDING**

Acceptance:

- [ ] Basic CF7/CFDB7 quote form works without n8n automation
- [ ] Accessibility pass
- [ ] Mobile QA pass
- [ ] SEO/GEO structure pass
- [ ] Performance and asset loading review
- [ ] No forbidden parent-theme changes
- [ ] No external plugins committed to source repo
- [ ] n8n remains deferred/unexposed unless human explicitly moves it into scope
- [ ] Human approves V1 launch readiness

### 1.1.0 — Visual Governance Layer

Status: **FUTURE CANDIDATE**

Purpose:

- Add a governance layer for brand-aware sections, pattern variants, and safer editor controls after V1 launch readiness.

Acceptance:

- [ ] Brand preset/profile variants are planned.
- [ ] Pattern and section style variants are documented.
- [ ] HTML-2-Gutenberg review report includes artifact palette, mapped theme tokens, rejected prototype colors, and missing tokens.
- [ ] Marketing/editor controls avoid raw class entry where practical.
- [ ] External references remain listed in planning docs for non-AI implementation.

### 2.0.0 / Future Candidate — Brand System Productization

Status: **FUTURE CANDIDATE**

Purpose:

- Evaluate productized multi-brand support, admin workflows, and optional AI-assisted brand/artifact review after the manual and governance layers are stable.

Acceptance:

- [ ] Multi-brand or client-specific brand-pack need is validated.
- [ ] Admin workflow boundaries are documented before implementation.
- [ ] Optional AI-assisted intake has approved credential, privacy, logging, cost, and review rules before any code.
