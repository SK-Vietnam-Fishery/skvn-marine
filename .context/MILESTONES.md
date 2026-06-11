# MILESTONES.md — SKVN Marine

> Source of truth cho milestone hiện tại và checklist chuyển mốc.
> File này phải được đọc khi bắt đầu task.
> Chỉ human mới có quyền xác nhận chuyển milestone/version.

---

## Current Milestone

Current: **V1 / 1.3.1 — Slider Navigation & Pagination Controls UX**
Status: **IN_PROGRESS**
Started: **2026-06-11**

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

### 1.3.1 — Slider Navigation & Pagination Controls UX

Status: **IN_PROGRESS**
Started: **2026-06-11**

Purpose:

- Replace the dots-specific editor concept with a complete pagination contract.
- Add independent arrow and pagination visibility, style, and position controls.
- Add Swiper-owned timed fraction and timed segments without a second autoplay
  or timer controller.
- Keep one Slider-level governed duration and no per-Slide duration.
- Build on the corrected V1 / 1.3.0 media, geometry, pagination, and memory
  foundation.

Decision:

- `docs/decisions/slider-navigation-and-pagination-controls.md`

Dependencies:

- V1 / 1.3.0 dynamic rendering and repair foundation completed with human
  onsite approval on 2026-06-11.
- Existing `dots` and arbitrary delay values need explicit Gutenberg
  compatibility behavior.

Acceptance draft:

- [ ] `dots` migrates to the approved pagination contract without invalidating existing content
- [ ] Arrow visibility, three styles, and four positions follow the decision contract
- [ ] Pill is disabled when Side center is selected
- [ ] Pagination visibility, four styles, and three positions follow the decision contract
- [ ] Matching bottom positions cluster as `arrows | pagination`
- [ ] Different arrow and pagination positions remain independent
- [ ] Zero/one real Slide hides both control families and disables autoplay
- [ ] Timed pagination follows Swiper autoplay without a second timer/controller
- [ ] Current/total numbering uses real Slides and excludes loop clones
- [ ] Slider duration uses governed `5/7/9/12s` choices with a documented legacy-value policy
- [ ] Hover, focus, document visibility, and interaction pause reasons compose correctly
- [ ] Mobile and reduced-motion fallbacks work
- [ ] Editor preview remains static and does not run autoplay/timer progress
- [ ] Human approves 1.3.1 controls UX implementation

### 1.3.2 — Slider Dynamic Rendering & Controls Onsite QA

Status: **PENDING**

Purpose:

- Run the Slider, Accordion, Card motion, and B2B Feature Showcase checks that
  were not completed before starting the 1.3.0 dynamic rendering migration.
- Test Slider frontend behavior against the 1.3.0 server-rendered contract,
  instead of approving the superseded static frontend architecture.
- Verify the V1 / 1.3.1 navigation, pagination, timed-progress, responsive, and
  accessibility controls on the corrected rendering foundation.
- Repeat image editing, Slider controls, and Full Width Canvas checks because
  the 1.3.0 renderer and 1.3.1 controls UX changed those surfaces.
- Keep unrelated previously passed evidence only where the implementation did
  not change the tested surface.

Testing:

- `docs/testing/onsite-slider-motion-1.3.2.md`
- `docs/testing/onsite-feature-showcase-1.2.3.md`

Acceptance draft:

- [ ] Human verifies all three Slider presets insert useful sample content
- [ ] Human verifies existing Slider content opens without invalid-block recovery
- [ ] Human verifies Hero, Product Showcase, and Card Carousel frontend layouts through the dynamic render path
- [ ] Human verifies V1 / 1.3.1 arrow and pagination controls across desktop and mobile
- [ ] Human verifies timed pagination, pause/resume, reduced-motion fallback, and real-Slide numbering
- [ ] Human verifies idle and repeated-interaction memory use settles without continuous growth
- [ ] Human verifies Accordion interaction and accessibility
- [ ] Human verifies Card motion device targeting and no-JS/reduced-motion fallbacks
- [ ] Human verifies the B2B Seafood Feature Showcase pattern editor/frontend layout
- [ ] Invalid-block, console, layout, and cache issues are recorded or confirmed clean
- [ ] Any source defects are fixed and re-tested
- [ ] Human approves closing Slider Dynamic Rendering onsite QA

### 1.3.3 — Feature Showcase Autoplay And Panel Links

Status: **PENDING**

Purpose:

- Add governed Hover and Autoplay interaction modes to
  `skvn-marine/feature-showcase`.
- Add a magnetic/snapping delay control limited to `3`, `5`, `7`, and `9`
  seconds.
- Add one optional Gutenberg LinkControl destination per panel, rendered as a
  CTA inside panel content rather than turning `summary` into a link.
- Share reduced-motion, governed delay, pause/resume, and document visibility
  invariants with Slider after Slider stability is verified.

Planning:

- `.context/planning/020_VERSION_1_3_3_FEATURE_SHOWCASE_AUTOPLAY_AND_LINKS_PLANNING.md`

Dependencies:

- V1 / 1.3.0 dynamic Slider rendering must be complete.
- V1 / 1.3.2 onsite QA must confirm Slider autoplay, hover/focus pause,
  accessibility, reduced motion, compatibility, and no-JavaScript behavior are
  stable.

Constraints:

- Keep Feature Showcase on native `details`/`summary`; do not initialize Swiper.
- Keep Swiper as the Slider's only movement controller.
- Do not run autoplay in Gutenberg.
- Do not expose arbitrary delay values.
- Do not wrap the panel or `summary` in a navigation link.
- Existing Feature Showcase and Slider content must remain valid and editable.

Acceptance draft:

- [ ] V1 / 1.3.0, V1 / 1.3.1, and V1 / 1.3.2 stability gates are complete
- [ ] Hover and Autoplay modes work without an all-closed state
- [ ] Delay control snaps only to `3s`, `5s`, `7s`, and `9s`
- [ ] Hover, keyboard focus, document visibility, and reduced motion pause or disable autoplay correctly
- [ ] Each panel supports an optional Gutenberg LinkControl CTA
- [ ] CTA interaction does not conflict with disclosure activation
- [ ] Existing Feature Showcase content opens without invalid-block recovery
- [ ] Shared autoplay helpers do not regress Slider runtime behavior
- [ ] No-JavaScript disclosure and links remain usable
- [ ] Plugin build and onsite QA pass
- [ ] Human approves milestone completion

### 1.3.5 Custom Icon made by WhySchools

- [ ] Upload custom icon

### 1.3.9 — SKVN Team Credits Easter Egg

Status: **PENDING**

Purpose:

- Close the Slider milestone sequence with a private wp-admin tribute to SKVN
  employees who contributed to the project.
- Keep the tribute discoverable as an Easter egg without adding public
  frontend output or changing normal admin navigation.

Decision:

- `docs/decisions/skvn-team-credits-easter-egg-1.3.9.md`

Dependencies:

- Complete the planned Slider sequence before starting 1.3.9.
- Human must approve the final tribute copy and any employee names or
  nicknames.

Scope:

- Add `Built with care by the SKVN team.` to the plugin header comment.
- Add `/* To the people who built SKVN, thank you. */` to the authored and
  production admin Easter egg asset.
- Trigger an accessible credits dialog/panel after five clicks on the existing
  top-level `SKVN Marine` wp-admin menu.
- Preserve normal menu navigation and count clicks across reloads with
  `sessionStorage`.

Constraints:

- Admin only; no frontend asset, HTML, or console message.
- No database write, tracking, AJAX, or external request.
- Do not change the menu slug, capability, plugin slug, or text domain.
- Do not publish employee names without explicit human confirmation.

Acceptance draft:

- [ ] Plugin header and admin asset contain the approved credit messages
- [ ] Normal menu clicks continue to navigate normally
- [ ] Five clicks within the governed interval open the credits experience
- [ ] Counter survives navigation in the same admin session and resets safely
- [ ] Credits UI is accessible and dismissible
- [ ] No frontend output, database write, tracking, or network request is added
- [ ] Human approves final tribute copy and any names
- [ ] PHP lint, plugin build, and wp-admin smoke test pass
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
