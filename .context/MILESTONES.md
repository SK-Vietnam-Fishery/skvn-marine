# MILESTONES.md — SKVN Marine

> Source of truth cho milestone hiện tại và checklist chuyển mốc.
> File này phải được đọc khi bắt đầu task.
> Chỉ human mới có quyền xác nhận chuyển milestone/version.

---

## Current Milestone

Current: **V1 / 0.8.0 — SKVN Editor Controls**
Status: **IN_PROGRESS**
Started: **2026-06-03**

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

### 0.8.0 — SKVN Editor Controls

Status: **IN_PROGRESS**
Started: **2026-06-03**

Purpose:

- Add Elementor-inspired but token-governed sidebar controls for SKVN-owned Gutenberg blocks and translated layout surfaces.
- Let editors adjust tone, spacing, width, margin, padding, responsive visibility, and block-specific behavior without raw classes, raw hex values, or unrestricted inline CSS.
- Keep theme-owned visual tokens as the source of truth while plugin-owned blocks expose safe presets.

Acceptance:

- [x] Editor controls contract is documented before code in `docs/decisions/skvn-editor-controls-0.8.0.md`
- [x] Onsite editor-controls test checklist exists in `docs/testing/onsite-editor-controls-0.8.0.md`
- [x] Theme owns tone, spacing, width, radius, shadow, and visual classes for the first `SKVN Accordion` control pass
- [x] Plugin owns block sidebar UI, block attributes, saved markup, and interactive block behavior for the first `SKVN Accordion` control pass
- [x] Controls are grouped into Content, Style, Layout, and Advanced sections for `SKVN Accordion`
- [x] Margin and padding controls use presets/tokens first, with responsive overrides only where needed
- [x] No freeform raw class input is required for marketing editors in `SKVN Accordion`
- [x] No raw hex/rgb/hsl values or arbitrary inline spacing values are required in `SKVN Accordion`
- [x] Slider editor UX tension is resolved before implementing slider-specific controls
- [ ] Editor and frontend output stay visually aligned
- [ ] GeneratePress parent remains untouched
- [ ] Human approves milestone completion

### 0.9.0 — Footer Page Settings

Status: **PENDING**

Purpose:

- Add a plugin settings page that stores the selected reusable footer page ID in `skvn_footer_page_id`.
- Let the theme render the selected footer page into GeneratePress' `generate_footer` surface.
- Keep GeneratePress as the footer foundation and fall back to the default GeneratePress footer when no page is selected.

Acceptance:

- [ ] Plugin settings page stores `skvn_footer_page_id`
- [ ] Setting value is restricted to a valid WordPress page ID
- [ ] Theme `inc/footer.php` renders the selected footer page through `generate_footer`
- [ ] Default GeneratePress footer remains the fallback when no page is selected
- [ ] No custom CPT is introduced
- [ ] No display rules system is introduced
- [ ] GeneratePress parent remains untouched
- [ ] Footer output is escaped/sanitized through WordPress-safe rendering paths
- [ ] Runtime smoke test confirms selected footer page renders
- [ ] Runtime smoke test confirms fallback footer works
- [ ] Human approves milestone completion

### 0.10.0 — Onsite Quote Flow Test Debt Resolution

Status: **PENDING**

Acceptance:

- [ ] Human runs `docs/testing/onsite-quote-flow-0.7.1.md` on the onsite site and reports evidence
- [ ] Human runs `docs/testing/onsite-quote-flow-0.7.0.md` on the onsite site
- [ ] Agent reminds human to open the related docs/files listed in the 0.7.0 deferred test debt section
- [ ] Onsite CF7 form existence confirmed by human evidence
- [ ] Onsite request quote page contains the CF7 shortcode/form
- [ ] Onsite thank-you page exists
- [ ] CF7 form markup matches `docs/artifacts/cf7-quote-form-0.7.0.md` or mismatch is documented
- [ ] CFDB7 stores at least one test submission
- [ ] Product CTA/query params confirmed from onsite product/product-card flow
- [ ] CF7 hidden/context fields confirmed in submitted data
- [ ] CFDB7 row confirms visible and hidden fields are stored
- [ ] Thank-you/success UX confirmed
- [ ] Desktop/mobile screenshots reviewed
- [ ] Console/log issues recorded or confirmed clean
- [ ] Human approves closing quote-flow onsite test debt

### 1.0.0 — V1 Launch Ready

Status: **PENDING**

Acceptance:

- [ ] Accessibility pass
- [ ] Mobile QA pass
- [ ] SEO/GEO structure pass
- [ ] Performance and asset loading review
- [ ] No forbidden parent-theme changes
- [ ] No external plugins committed to source repo
- [ ] n8n remains deferred/unexposed unless human explicitly moves it into scope
- [ ] Human approves V1 launch readiness

## Future V1.x Checkpoints

### 1.4.0 — SKVN Theme Init Setup UI

Status: **PENDING**

Purpose:

- Discuss and design an wp-admin setup screen for reusable SKVN setup tasks.
- Candidate setup card: Request A Quote Workflow.
- The UI should let an admin load a reviewed setup template from wp-admin instead of requiring WP-CLI for every setup pass.

Wireframe note:

```text
Admin sidebar
└── SKVN Theme init setup
    ├── [Request A Quote Workflow]
    ├── [Future setup card]
    ├── [Future setup card]
    └── [Nạp setup]
```

Constraints:

- Do not implement in 0.7.0 or 0.10.0.
- Must be discussed carefully before implementation.
- Must not add a dependency/plugin unless human explicitly changes dependency policy.
- Must not custom-code CF7 form handling; setup UI may only create/update approved WP content/config.
- Must require wp-admin capability checks and nonce protection when implemented.

Acceptance draft:

- [ ] Human approves exact setup cards and button behavior
- [ ] Admin capability requirement is defined
- [ ] Setup actions are previewable or clearly described before running
- [ ] Setup actions are idempotent where possible
- [ ] Request A Quote Workflow setup maps to approved CF7/page/docs contract
- [ ] No n8n webhook is exposed
- [ ] Human approves milestone completion
