# MILESTONES.md — SKVN Marine

> Source of truth cho milestone hiện tại và checklist chuyển mốc.
> File này phải được đọc khi bắt đầu task.
> Chỉ human mới có quyền xác nhận chuyển milestone/version.

---

## Current Milestone

Current: **V1 / 1.2.3 — SKVN Feature Showcase**
Status: **IN_PROGRESS**
Started: **2026-06-08**

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
3. If this is a release/deploy boundary, verify WordPress theme/plugin release metadata with `node tools/bump-project-version.mjs <version>` and follow `docs/workflows/versioning-release-workflow.md`.
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
- When human explicitly starts a milestone, the agent may run `node tools/bump-project-version.mjs <version>` and rebuild plugin assets so the working WordPress metadata advertises the current milestone version. This is a milestone development build, not release approval.
- Before packaging or deploying a milestone release, verify again with `node tools/bump-project-version.mjs <version>` and rebuild plugin assets.

---

## Current V1.x Checkpoint

### 1.2.3 — SKVN Feature Showcase

Status: **IN_PROGRESS**

Purpose:

- Park and later activate the artifact-inspired `SKVN Feature Showcase` block.
- Keep V1 / 1.2.1 limited to Slider Presets & Inserter while preserving drafted source for later.
- Build a separate editorial showcase block rather than overloading the existing `SKVN Accordion`.
- Use the human-approved mobile split state: intro/copy first, compact panel rail second.

Planning:

- `.context/planning/016_VERSION_1_2_3_FEATURE_SHOWCASE_PLANNING.md`
- `docs/decisions/feature-showcase-1.2.3.md`

Source:

- `wp-content/plugins/skvn-marine-blocks/src/feature-showcase/`
- Metadata is active as `block.json`.

Acceptance draft:

- [x] Human approves activating the parked block source
- [x] `SKVN Feature Showcase` appears under `SKVN Marine`
- [x] Block inserts useful editable sample content
- [x] Intro fields are editable
- [x] Four panel labels, headings, copy, and images are editable
- [x] Desktop expanding panel behavior supports hover and keyboard focus in source
- [x] Mobile uses the split intro plus panel rail state in source
- [x] Reduced-motion users do not receive forced panel animation
- [x] No Tailwind CDN, raw class input, or raw CSS input is required
- [x] Existing `SKVN Accordion` behavior remains unchanged
- [x] Plugin build passes
- [x] Onsite QA target is documented before milestone completion

### 1.2.9 — Slider & Motion Onsite QA

Status: **PENDING**

Purpose:

- Consolidate onsite QA deferred from 1.2.0 and 1.2.1.
- Verify Slider image editing, preset insertion, frontend Swiper behavior, Accordion interaction, Card motion, accessibility, responsive behavior, and fallbacks.
- Keep implementation milestones moving without falsely marking unrun onsite tests as passed.

Testing:

- `docs/testing/onsite-slider-motion-1.2.9.md`

Acceptance draft:

- [ ] Human verifies all three Slider presets insert useful sample content
- [ ] Human verifies Slider image choose/replace/remove and editor persistence
- [ ] Human verifies Hero, Product Showcase, and Card Carousel frontend layouts
- [ ] Human verifies Slider keyboard, autoplay, reduced-motion, navigation, and responsive behavior
- [ ] Human verifies Accordion interaction and accessibility
- [ ] Human verifies Card motion device targeting and no-JS/reduced-motion fallbacks
- [ ] Invalid-block, console, layout, and cache issues are recorded or confirmed clean
- [ ] Any source defects are fixed and re-tested
- [ ] Human approves closing consolidated Slider/Motion QA

### 1.1.2 — Product Quote Flow & Map Block Testing

Status: **PENDING**

Purpose:

- Resolve deferred quote data-flow testing after the CF7 interface has already passed onsite visual review.
- Test quote submission from product/product-card/page-block flows, not only the standalone Request Quote page.
- Confirm map block/display issue because the current map surface is not viewable.
- Treat this as testing and source hardening around blocks/pages, not a custom CF7 form handler.

Carry-in from 0.10.0:

- CF7/Request Quote interface visual check: **PASS by human feedback on 2026-06-05**.
- Remaining quote debt is data flow only: submit, store, hidden/context fields, product-origin query params, and success/thank-you behavior.

Acceptance draft:

- [ ] Human runs `docs/testing/onsite-quote-flow-0.7.0.md` data-flow section from a product/product-card/page-block origin
- [ ] Human runs `docs/testing/onsite-quote-flow-0.7.1.md` runtime handoff data-flow section
- [ ] Human runs `docs/testing/onsite-map-block-1.1.2.md` on the onsite map page/surface
- [ ] Request Quote form submission succeeds from product-origin URL/query params
- [ ] CFDB7 stores at least one test submission
- [ ] CFDB7 row confirms visible fields are stored
- [ ] CFDB7 row confirms hidden/context fields are stored: `product_id`, `product_sku`, `product_name`, `product_url`, `source_url`, and UTM fields
- [ ] Thank-you/success UX confirmed
- [ ] Product CTA/query params confirmed from onsite product/product-card/page-block flow
- [ ] Console/log issues recorded or confirmed clean for quote flow
- [ ] Map block/display surface is visible onsite
- [ ] If current map cannot be viewed, mismatch is documented with screenshot, target URL, block markup/source, and console notes
- [ ] No custom PHP form handler is introduced
- [ ] No n8n webhook is exposed or required
- [ ] Human approves closing 1.1.2 testing

### 1.3.0 — Slider Dynamic Rendering Architecture

Status: **PENDING**

Purpose:

- Convert `skvn-marine/slider` and `skvn-marine/slide` from static Gutenberg
  saved frontend markup to a server-rendered PHP architecture while the block
  family is still early enough to migrate deliberately.
- Establish one stable media/overlay/content render contract shared by Hero
  Slider, Product Showcase, Card Carousel, and future Slider presets.
- Prevent compound deprecation debt from accumulating each time Slider frontend
  markup evolves.
- Preserve Gutenberg InnerBlocks editing, existing block namespace/attributes,
  Swiper runtime behavior, and existing published content through an explicit
  migration/compatibility plan.

Planning:

- `.context/planning/017_VERSION_1_3_0_SLIDER_DYNAMIC_RENDERING_ARCHITECTURE_PLANNING.md`
- `docs/decisions/slider-block.md`
- `docs/decisions/slider-static-markup-migration-1.2.1.md` (superseded before implementation)
- `docs/testing/slider-frontend-media-content-layer-bug-1.2.1.md`

Constraints:

- Do not silently convert the blocks during a 1.2.1 bug patch.
- Do not rename `skvn-marine/slider` or `skvn-marine/slide`.
- Do not replace InnerBlocks with a `slides` array or custom slide manager.
- Do not duplicate Swiper or introduce another Slider controller.
- Define plugin PHP module ownership, render callbacks, escaping, and deploy
  artifact requirements before implementation.
- Existing Slider content must remain editable and render without invalid-block
  recovery.

Acceptance draft:

- [ ] Static versus dynamic rendering decision and migration boundary are finalized
- [ ] Current saved Slider/Slide markup variants are inventoried
- [ ] PHP render contract defines Slider shell and Slide media/content layers
- [ ] Slider/Slide block metadata registers approved server render paths
- [ ] Existing InnerBlocks content renders through the dynamic path
- [ ] Existing saved Slider content remains editable without invalid-block recovery
- [ ] Hero Slider uses a stable media/overlay/content frame
- [ ] Product Showcase and Card Carousel retain their distinct flow layouts
- [ ] Swiper fade, autoplay, arrows, dots, keyboard, loop, and reduced-motion behavior remain functional
- [ ] PHP input/output handling follows WordPress sanitize/escape rules
- [ ] New PHP runtime files are included in deploy artifact and plugin zip audits
- [ ] Plugin build, PHP syntax checks, and onsite migration QA pass
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

### 1.4.1 — Layout Blocks Validation & Quote Evaluation

Status: **PENDING**

Purpose:

- Run the deferred editor/frontend/runtime validation for the `1.1.0` card-grid and card blocks.
- Evaluate whether `skvn-marine/quote` is justified only after card-grid/card validation evidence is available.
- Keep deferred validation explicit instead of treating the untested `1.1.0` implementation as runtime-approved.

Testing:

- `docs/testing/card-grid-layout-blocks-1.1.0.md`

Acceptance draft:

- [ ] Human runs the deferred card-grid/card editor and frontend test
- [ ] Desktop, tablet, and mobile screenshots are recorded
- [ ] No invalid-block or recovery warning appears
- [ ] Editor/frontend parity is acceptable for layout decisions
- [ ] Preset combinations do not cause overflow, overlap, or unreadable text
- [ ] Browser console issues are recorded or confirmed clean
- [ ] `skvn-marine/quote` is evaluated after validation evidence is available
- [ ] Any source defects found during validation are fixed and re-tested
- [ ] Human approves closing the deferred `1.1.0` validation

### 1.5.0 — Fullscreen Step Slider

Status: **PENDING**

Purpose:

- Add a dedicated fullscreen process/timeline Slider block for urgent landing
  page storytelling needs.
- Build it on top of the V1 / 1.3.0 dynamic Slider rendering foundation instead
  of creating a second Slider engine.
- Provide bottom tab navigation with per-step progress, media/content layers,
  and motion presets suitable for sequential process narratives.
- Keep Gutenberg-native editing with InnerBlocks and child step blocks; do not
  switch to a `slides` array or custom slide repeater.

Planning:

- `.context/planning/018_VERSION_1_5_0_FULLSCREEN_STEP_SLIDER_PLANNING.md`
- `docs/decisions/fullscreen-step-slider-1.5.0.md`
- External research input: `fullscreen-step-slider-report.md`

Dependencies:

- V1 / 1.3.0 dynamic Slider render architecture must be complete enough to
  provide shared render, media/content, Swiper, reduced-motion, and deploy
  artifact conventions.
- Reconcile whether V1 / 1.3.1 Slider migration QA runs before or alongside
  this milestone.

Constraints:

- Implement as separate blocks such as `skvn-marine/step-slider` and
  `skvn-marine/step-slide`, not as a fourth variation of `skvn-marine/slider`.
- Reuse Swiper; do not introduce a custom autoplay timer/controller that
  competes with Swiper.
- Use governed presets for height, overlay, motion, CTA, and tab styling instead
  of raw arbitrary color, pixel, timing, or class inputs.
- Preserve keyboard navigation, touch behavior, reduced-motion fallback, and
  no-JS readability.
- Do not include video support unless the milestone explicitly budgets media
  performance, inactive-slide pause, poster, and mobile autoplay behavior.

Acceptance draft:

- [ ] Architecture contract is approved before source implementation
- [ ] `skvn-marine/step-slider` and `skvn-marine/step-slide` ownership is documented
- [ ] Step Slider uses dynamic PHP render and shared Slider foundation
- [ ] Editor uses Gutenberg-native InnerBlocks/List View operations
- [ ] Bottom tab navigation reflects slide order and active state
- [ ] Progress bar synchronizes with Swiper autoplay, click, hover/focus pause, and reduced motion
- [ ] Wipe/text motion uses governed presets and has reduced-motion fallback
- [ ] Mobile tab UI remains readable without overflow
- [ ] No custom slide manager, `slides` array, or second runtime is introduced
- [ ] Plugin build, PHP syntax checks, deploy artifact audit, and onsite QA pass
- [ ] Human approves milestone completion

### 1.6.0 — SKVN Surface Presets

Status: **PENDING**

Purpose:

- Add SKVN-local approved surface presets such as flat, soft, glass, elevated, and outlined.
- Keep production visual output independent from WindPress/Tailwind utilities.
- Let the theme own `skvn-surface--*` classes, tokens, fallbacks, and contrast/readability rules.
- Let plugin/editor controls select safe presets later without raw CSS, raw class, arbitrary color, blur, or shadow input.
- Design governed typography presets and a font delivery mode switch: Google CDN or local self-hosted, without raw CSS or arbitrary font URLs.

Planning:

- `.context/planning/009_VERSION_1_6_0_SKVN_SURFACE_PRESETS_PLANNING.md`

Acceptance draft:

- [ ] Surface preset contract is documented before code
- [ ] Typography preset and delivery-mode contract is documented before code
- [ ] Admin can choose approved body, heading, and UI font presets without raw CSS or raw URLs
- [ ] Admin can choose Google CDN or local self-hosted delivery mode
- [ ] Frontend enqueues only the selected font delivery mode by default
- [ ] Local self-hosted mode documents cache path, failure fallback, and license/source constraints
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
