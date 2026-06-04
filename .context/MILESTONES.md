# MILESTONES.md — SKVN Marine

> Source of truth cho milestone hiện tại và checklist chuyển mốc.
> File này phải được đọc khi bắt đầu task.
> Chỉ human mới có quyền xác nhận chuyển milestone/version.

---

## Current Milestone

Current: **V1 / 0.10.0 — Onsite Test Debt Resolution**
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
3. If this is a release/deploy boundary, sync WordPress theme/plugin release metadata with `node tools/bump-project-version.mjs <version>` and follow `docs/workflows/versioning-release-workflow.md`.
4. If the milestone added or changed runtime PHP `require`/`include` paths, run the runtime file audit in `docs/workflows/deploy-artifact-workflow.md` before zip upload.
5. Move completed milestone checklist/notes sang `.context/MILESTONES_HISTORY.md`.
6. Move `RESOLVED_ACTIVE` tensions của milestone cũ từ `.context/TENSIONS_ACTIVE.md` sang `.context/TENSIONS_HISTORY.md`, đổi `Status: ARCHIVED`.
7. Giữ lại OPEN tensions còn liên quan trong `.context/TENSIONS_OPEN.md`.
8. Không tự archive hoặc tự chuyển milestone nếu human chưa approve.

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
- `.context/MILESTONES.md` is the planning/scope source of truth. It does not automatically update WordPress theme/plugin `Version:` headers.
- Before packaging or deploying a milestone release, run `node tools/bump-project-version.mjs <version>` and rebuild plugin assets.

---

## V1 Checkpoints

### 0.10.0 — Onsite Test Debt Resolution

Status: **IN_PROGRESS**
Started: **2026-06-03**

Acceptance:

- [ ] Human runs `docs/testing/footer-page-settings-0.9.0.md` on the online site and reports evidence
- [ ] Footer Page Settings selected footer page renders online
- [ ] Footer Page Settings default GeneratePress fallback works online
- [ ] Footer Page Settings invalid page ID fallback is confirmed or mismatch is documented
- [ ] Human runs `docs/testing/onsite-editor-controls-0.8.0.md` on the onsite site and reports evidence
- [ ] Agent reminds human to validate 0.8.0 editor/frontend visual parity before closing onsite test debt
- [ ] SKVN Accordion editor controls persist after save/reload onsite
- [ ] SKVN Accordion frontend output matches editor intent onsite
- [ ] Desktop/mobile visual smoke for 0.8.0 editor controls is reviewed
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
- [ ] Human approves closing onsite test debt

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

### 0.11.0 — SKVN Marine Admin Menu

Status: **PENDING**

Purpose:

- Move the existing SKVN Footer admin surface out of `Settings`.
- Create a top-level `SKVN Marine` admin menu owned by `skvn-marine-blocks`.
- Put the footer settings screen under the `SKVN Marine` admin menu as one tab/page.
- Add a safe footer background preset setting for the selected footer page.
- Leave admin menu ordering/repositioning to the external ASE plugin instead of hardcoding menu position rules in SKVN code.

Decision contract:

- `docs/decisions/footer-appearance-settings-0.11.0.md`

Acceptance draft:

- [ ] `Settings → SKVN Footer` is no longer the primary admin location.
- [ ] Top-level `SKVN Marine` admin menu exists.
- [ ] Footer settings are available under the `SKVN Marine` admin menu.
- [ ] Existing option key `skvn_footer_page_id` remains unchanged.
- [ ] New option key `skvn_footer_background_preset` stores an approved preset, not raw color input.
- [ ] Footer background preset applies only when `skvn_footer_page_id` points to a valid published footer page.
- [ ] Footer background preset affects `.skvn-footer-page` and the outermost `.skvn-site-footer` Gutenberg block.
- [ ] Viewport space below the custom footer uses the same footer background preset.
- [ ] Default GeneratePress fallback footer is unaffected by the footer background preset.
- [ ] Existing footer rendering behavior remains unchanged.
- [ ] Capability checks and nonce protection remain in place.
- [ ] SKVN code does not hardcode advanced admin menu ordering; ASE handles menu repositioning onsite.
- [ ] Human approves milestone completion.

### 0.12.0 — SKVN Header Actions And B2B Search

Status: **PENDING**

Purpose:

- Add governed header actions without replacing the GeneratePress header shell.
- Support product search, post/site search, contact CTA, and optional Request Quote CTA.
- Plan the B2B search results experience as a governed page that separates Products from Related articles.
- Keep phase 1 search lightweight: taxonomy/title-first native WP/Woo queries, no Elastic/OpenSearch, and no custom query cache.

Decision contract:

- `docs/decisions/header-actions-search-0.12.0.md`

Acceptance draft:

- [ ] Human confirms 0.12.0 as the exact implementation target before code.
- [ ] GeneratePress header remains the shell.
- [ ] No GeneratePress parent files are edited.
- [ ] `SKVN Marine → Header` settings are documented and implemented.
- [ ] Product search can be enabled/disabled.
- [ ] Post/site search can be enabled/disabled.
- [ ] Contact button can be enabled/disabled.
- [ ] Request Quote button can be enabled/disabled.
- [ ] Search target is explicit: products, articles, or all site.
- [ ] B2B search results page separates Products from Related articles.
- [ ] Product matching uses product tags/categories/title before content fallback.
- [ ] Related article matching uses post tags/categories/title before content fallback.
- [ ] No Elastic/OpenSearch dependency is added in phase 1.
- [ ] No custom query cache or SQL cache table is added in phase 1.
- [ ] Header mobile behavior does not break GeneratePress navigation.
- [ ] Keyboard/focus behavior is reviewed for search and buttons.
- [ ] Human approves milestone completion.

## Future V1.x Checkpoints

### 1.1.0 — Layout Blocks

Status: **PENDING**

Purpose:

- Add governed layout blocks for repeated artifact sections that are too fragile with core blocks plus raw class names.
- Start with `skvn-marine/card-grid` and `skvn-marine/card`.
- Evaluate `skvn-marine/quote` after card-grid/card are validated.
- Keep visual styling theme-owned and block logic plugin-owned.

Planning:

- `.context/planning/003_VERSION_1_1_0_LAYOUT_BLOCKS_PLANNING.md`
- Source proposal: `.context/proposals/proposal-layout-blocks.md`

Acceptance draft:

- [ ] At least two real layout artifacts justify the same grid/card governance
- [ ] Brainstorm trigger reviewed for `1.7.0 — Front page trang Chuyển đổi số`: identify which layout pieces can reuse card-grid/card, and which pieces need separate resource/search/taxonomy planning
- [ ] Core block plus theme-pattern alternative is documented as too fragile or too slow for editors
- [ ] `skvn-marine/card-grid` block exists in plugin
- [ ] `skvn-marine/card` block exists in plugin
- [ ] Blocks use `block.json`, TypeScript, and InnerBlocks
- [ ] Editor preview matches frontend closely enough for layout decisions
- [ ] Theme CSS implements all layout-critical `skvn-*` classes used by saved markup
- [ ] No frontend JavaScript runtime is added unless a real interaction requires it
- [ ] Layout blocks do not expose motion controls and do not depend on theme-only animation behavior
- [ ] `skvn-marine/quote` is evaluated only after card-grid/card validation
- [ ] Build passes for `skvn-marine-blocks`
- [ ] Human approves milestone completion

### Future Candidate — Gutenberg Supercharger Motion

Status: **PENDING**

Purpose:

- Plan portable animation/motion support for plugin-owned blocks after V1 launch readiness.
- Candidate promotion target after human approval: `1.2.0 — Gutenberg Supercharger Motion`.
- Keep plugin-owned motion portable for possible future migration/rename toward `Gutenberg Supercharger`.

Planning:

- `.context/planning/011_FUTURE_CANDIDATE_GUTENBERG_SUPERCHARGER_MOTION_PLANNING.md`
- `docs/decisions/block-animation-strategy.md`

Acceptance draft:

- [ ] Human approves exact target milestone/version before implementation
- [ ] Animation remains out of scope for 0.10.0, 0.11.0, 1.0.0, and 1.1.0
- [ ] Plugin-owned animation works without `skvn-marine` theme active
- [ ] Plugin-owned animation ships required CSS/JS/keyframes/reduced-motion fallback inside the plugin
- [ ] Device targeting uses independent Desktop/Tablet/Mobile checkboxes
- [ ] No frontend content remains invisible when JS fails
- [ ] Editor content remains visible and editable
- [ ] Theme animation remains limited to core blocks, page sections, and theme decoration
- [ ] Human approves milestone completion

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

### 1.6.0 — SKVN Surface Presets

Status: **PENDING**

Purpose:

- Add SKVN-local approved surface presets such as flat, soft, glass, elevated, and outlined.
- Keep production visual output independent from WindPress/Tailwind utilities.
- Let the theme own `skvn-surface--*` classes, tokens, fallbacks, and contrast/readability rules.
- Let plugin/editor controls select safe presets later without raw CSS, raw class, arbitrary color, blur, or shadow input.

Planning:

- `.context/planning/009_VERSION_1_6_0_SKVN_SURFACE_PRESETS_PLANNING.md`

Acceptance draft:

- [ ] Surface preset contract is documented before code
- [ ] Theme CSS defines approved `skvn-surface--*` classes
- [ ] Presets use SKVN tokens and do not depend on WindPress
- [ ] Glass has a non-blur fallback
- [ ] Editor/frontend parity is checked for the first supported surface
- [ ] At least one SKVN-owned block or layout surface can select a preset through safe controls
- [ ] No raw class, raw CSS, or arbitrary color/blur/shadow input is required
- [ ] Human approves milestone completion

### 1.7.0 — Front page trang Chuyển đổi số

Status: **PENDING**

Purpose:

- Plan and implement the front page inspired by `.local/test-artifacts/ChuyenDoiSo.html`.
- Provide a document/resource front page with post list items that can show a thumbnail or fallback icon.
- Display tags from the external plugin-owned taxonomy without recreating or renaming that taxonomy.
- Display category lists and document/guide counts from real data, not hardcoded numbers.
- Support whole-site search styling and integration boundary; search logic remains WordPress/search-plugin owned, while SKVN owns safe visual classes and optional wrapper/block rendering.

Planning:

- `.context/planning/010_VERSION_1_7_0_FRONT_PAGE_TRANG_CHUYEN_DOI_SO_PLANNING.md`

Acceptance draft:

- [ ] Human confirms the external plugin taxonomy names and search integration boundary
- [ ] Front-page IA is documented before code: hero/search, resource list, category/count sidebar, access CTA, KPI strip, footer
- [ ] Source data ownership is documented: external plugin taxonomy/search owns data, SKVN consumes and styles
- [ ] Theme-owned visual classes are defined before plugin output depends on them
- [ ] Post/resource list supports thumbnail or fallback icon
- [ ] Tag/status badges are governed by presets/classes, not raw arbitrary classes
- [ ] Category/document counts come from real WordPress data
- [ ] Whole-site search form works with WordPress/search-plugin logic or documented hook fallback
- [ ] Core Query Loop/pattern alternative is evaluated before creating a custom block
- [ ] If a custom block is needed, it belongs in `skvn-marine-blocks` and degrades safely when the external taxonomy/search plugin is inactive
- [ ] No raw Tailwind, inline CSS, or inline scripts from the benchmark artifact are used as production content
- [ ] Human approves milestone completion
