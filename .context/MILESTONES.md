# MILESTONES.md — SKVN Marine

> Source of truth cho milestone hiện tại và checklist chuyển mốc.
> File này phải được đọc khi bắt đầu task.
> Chỉ human mới có quyền xác nhận chuyển milestone/version.

---

## Current Milestone

Current: **V1 / 1.3.6 — Block Editor UX, Slider Parallax & Single Post Fix**
Status: **IN PROGRESS**
Started: **2026-06-17**

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

Status: **DONE**
Started: **2026-06-11**
Completed: **2026-06-12**
Approved by human: **2026-06-12**

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

- [x] `dots` migrates to the approved pagination contract without invalidating existing content
- [x] Arrow visibility, three styles, and four positions follow the decision contract
- [x] Pill is disabled when Side center is selected
- [x] Pagination visibility, four styles, and three positions follow the decision contract
- [x] Matching bottom positions cluster as `arrows | pagination`
- [x] Different arrow and pagination positions remain independent
- [x] Zero/one real Slide hides both control families and disables autoplay
- [x] Timed pagination follows Swiper autoplay without a second timer/controller
- [x] Current/total numbering uses real Slides and excludes loop clones
- [x] Slider duration uses governed `5/7/9/12s` choices with a documented legacy-value policy
- [x] Hover, focus, document visibility, and interaction pause reasons compose correctly
- [x] Mobile and reduced-motion fallbacks work
- [x] Editor preview remains static and does not run autoplay/timer progress
- [x] Human approves 1.3.1 controls UX implementation



### 1.3.3 — Dynamic Product And Post Collections

Status: **DONE**
Started: **2026-06-13**
Completed: **2026-06-16**
Approved by human: **2026-06-16**
Note: Implementation shipped. Remaining onsite QA for product grid and post collection deferred to 1.3.9.

Purpose:

- Add governed dynamic collection blocks for product and post surfaces:
  Product Grid, Product Carousel, Post Grid, and Post Carousel.
- Use custom SKVN dynamic blocks while querying/rendering through native
  WordPress and WooCommerce APIs under the hood.
- Keep editor UX simple with four inserter choices, backed by two logic blocks:
  `skvn-marine/product-collection` and `skvn-marine/post-collection`.
- Preserve B2B funnel context through product card actions and Request Quote
  URLs.
- Ship plugin-owned baseline CSS/runtime so the blocks remain portable if the
  SKVN theme changes.

Decision:

- `docs/decisions/skvn-dynamic-collections-1.3.3.md`

Planning:

- `.context/planning/024_VERSION_1_3_3_DYNAMIC_COLLECTIONS_PLANNING.md`

Testing:

- `docs/testing/onsite-dynamic-collections-1.3.3.md`

Architecture:

- Product collections own WooCommerce product query, product card markup,
  quote/context actions, and WooCommerce inactive fallback.
- Post collections own WordPress post query and post card markup.
- Grid and Carousel are layout modes, not separate query owners.
- Editor-facing inserter choices are:
  `SKVN Product Grid`, `SKVN Product Carousel`, `SKVN Post Grid`,
  and `SKVN Post Carousel`.
- Saved content stores query/layout/action attributes only. It does not store
  snapshots of product or post card HTML.
- Carousel layouts reuse only the relevant Slider/Swiper foundation: adapter,
  pause policy, document visibility behavior, and reduced-motion guard.
- Grid layouts do not load carousel runtime.
- Product/Post cards must not be forced into `skvn-marine/slide` InnerBlocks.

Constraints:

- Do not use direct custom SQL.
- Do not depend on WooCommerce experimental Product Collection extension APIs
  as the SKVN source of truth in V1.
- Do not save product/post snapshots into page content.
- Do not implement Product Taxonomy Collections admin in this milestone.
- Do not implement product attribute query, faceted filters, AJAX load more,
  grouped taxonomy navigation, archive builder, or universal CPT collection in
  this milestone.
- Do not add custom object/transient caching unless onsite evidence proves it
  is required.
- Product collections must not fatal when WooCommerce is inactive.
- Carousel autoplay is off by default, disabled for reduced motion, and must not
  run in the editor.
- If autoplay is enabled, render a visible Pause/Play control.

Implementation order:

1. Contract and shared constants/types.
2. Post Collection Grid.
3. Product Collection Grid.
4. Shared carousel runtime and Product/Post Carousel variations.
5. Performance, dependency, fallback, and accessibility hardening.
6. Human onsite QA and source fix pass.

Acceptance draft:

- [x] `docs/decisions/skvn-dynamic-collections-1.3.3.md` is reviewed before source implementation
- [x] `skvn-marine/product-collection` and `skvn-marine/post-collection` are registered as dynamic blocks
- [x] Four inserter choices exist: Product Grid, Product Carousel, Post Grid, Post Carousel
- [x] Saved markup stores attributes only and does not snapshot product/post cards
- [ ] Product collections query through WooCommerce/native APIs without direct custom SQL
- [x] Post collections query through WordPress native APIs
- [ ] Query controls support category/tag multi-select, `AND`/`OR`, item limit, and approved order modes
- [ ] `Shuffle balanced pool` avoids SQL random ordering and uses the documented pool strategy
- [x] Grid respects max 5 columns and max 3 rows
- [x] Carousel respects max 10 items
- [x] Responsive presets `1-1-1`, `2-1-1`, `3-2-1`, `4-2-1`, and `5-3-1` work
- [ ] Product cards support governed field visibility and quote/view/custom action modes
- [ ] Request Quote action preserves product context in the generated URL
- [ ] Post cards support governed field visibility and read/custom action modes
- [ ] Badge behavior supports display-only and archive-link modes
- [ ] Product image fallback uses product image, WooCommerce placeholder, then SKVN fallback
- [ ] Post image fallback uses featured image, then SKVN fallback
- [ ] Carousel reuses shared pause/reduced-motion policy without making grid load carousel runtime
- [ ] Autoplay off/default, pause, document visibility, focus, and reduced-motion behavior pass
- [x] WooCommerce inactive state does not fatal product collection blocks
- [x] Plugin baseline CSS keeps blocks readable without relying on theme patterns
- [ ] Theme pattern follow-up is deferred until plugin implementation and onsite QA pass
- [ ] Plugin build, PHP syntax checks, deploy artifact audit if runtime PHP paths change, and onsite QA pass
- [ ] Human approves milestone completion

Deferred to 2.x.x or later:

- `Products -> Taxonomy Collections`
- Product Taxonomy Collections admin
- Attribute/tag thumbnail metadata UI
- Group by taxonomy
- Faceted/AJAX filtering
- Archive builder
- Technical product/specification card
- Universal CPT collection block

### 1.3.5 — Post, Product & Archive Page Improvements

Status: **PLANNING**
Started: **2026-06-17**

Purpose:

- Cải thiện giao diện và UX cho trang Post, Product, và Archive theo Style C Hybrid.
- Thiết lập font token system configurable qua WP Customizer.
- Fix CSS token bridge giữa `--skvn-*` và `--wp--preset--*`.
- Ẩn comment section và Reviews tab không phù hợp B2B.
- Phục vụ context xuất nhập khẩu thủy sản cao cấp (grouper, mahi-mahi) — positioning gần luxury food brand.

Planning:

- `.context/planning/025_VER_1_3_5_POST_PRODUCT_ARCHIVE_IMPROVEMENTS.md`

Design artifacts:

- `docs/artifacts/post-archive-style-C-hybrid.html`
- `docs/artifacts/single-post-style-C.html`
- `docs/artifacts/single-product-style-C.html`
- `docs/artifacts/font-compare-b2b.html`

Implementation order:

1. Font Customizer control (default: Instrument Serif)
2. CSS token bridge fix (`--skvn-color-*` → `--wp--preset--color--*`)
3. Archive page — Style C Hybrid
4. Single Product page — conversion priority
5. Single Post page — content marketing

Acceptance draft:

- [ ] Font Customizer control hoạt động, 4 preset chuyển đổi được
- [ ] Instrument Serif là default, load từ Google Fonts
- [ ] CSS token bridge: `--skvn-color-*` reference đúng chiều từ WP preset vars
- [ ] Archive page dùng layout Style C Hybrid (featured post + card grid)
- [ ] Sidebar archive: open = 2fr + 1fr, closed = full-width 3-col grid
- [ ] Single Product: gallery trái + details phải, Quote CTA primary nổi bật
- [ ] Single Product: Reviews tab đã bị ẩn
- [ ] Single Product: placeholder navy branded "Hình sắp cập nhật"
- [ ] Single Product: trust signals (VSATTP, HACCP, Cold Chain, Bảo hành)
- [ ] Single Post: hero image + content + sidebar phải
- [ ] Single Post: comment section đã bị ẩn
- [ ] Layout sizing dùng `fr`/`%` — không hardcode px/em
- [ ] `prefers-reduced-motion` áp dụng cho mọi animation
- [ ] Desktop + Mobile QA pass cho cả 3 page types
- [ ] Human approves milestone completion

### 1.3.6 — Block Editor UX, Slider Parallax & Single Post Fix

Status: **IN PROGRESS**
Started: **2026-06-17**

Purpose:

- Nâng cấp Inspector panel UX cho tất cả SKVN blocks (Content/Style/Layout/Advanced).
- Thêm Slider Parallax effect dùng Swiper built-in Parallax Module.
- Fix single post hero width, heading font, và thumbnail aspect-ratio (1.3.5 visual debt).
- ThumbPress-compatible: dùng CSS `aspect-ratio` + `object-fit: cover`, không `add_image_size()`.

Planning:

- `.context/planning/026_VER_1_3_6_BLOCK_EDITOR_UX_AND_SLIDER_PARALLAX_PLANNING.md`

Acceptance draft:

**Trục C — Single Post Fix:**
- [ ] Hero `.skvn-post-hero` render full-width trong GP content area
- [ ] Heading trong hero dùng `font-family: var(--skvn-font-heading)`
- [ ] Thumbnail hero dùng `aspect-ratio: 16/9` + `object-fit: cover`
- [ ] Card thumbnail (archive, related) dùng `aspect-ratio: 3/2` + `object-fit: cover`
- [ ] Không có `add_image_size()` mới — ThumbPress-compatible

**Trục A — Editor UX:**
- [ ] Tất cả Inspector panels dùng 4-section Content/Style/Layout/Advanced
- [ ] Slider và Accordion có empty state placeholder với action button
- [ ] Collection skeleton grid match responsive preset khi editor đang load
- [ ] Block icons và descriptions không còn blank trong inserter
- [ ] Inspector panel refactor không invalidate content hiện có

**Trục B — Slider Parallax:**
- [ ] `enableParallax` attribute tồn tại trong Slider block.json
- [ ] Parallax chạy đúng trên desktop với intensity preset (subtle/medium/strong)
- [ ] `prefers-reduced-motion` tắt parallax hoàn toàn
- [ ] Parallax tắt khi `slidesPerView > 1`
- [ ] Editor không chạy parallax — chỉ hiện badge "Parallax ON"

**Chung:**
- [ ] Plugin build pass, PHP lint pass
- [ ] Human approves milestone completion

---

### 1.3.4 — Core Control Foundation & Core Button Hover

Status: **PENDING**

Purpose:

- Add `SKVN Marine -> Core Control` as the shared settings and registry surface
  for optional WordPress core-block and editor enhancements.
- Establish a migration-ready Core Control architecture inside the current
  `skvn-marine-blocks` plugin.
- Prove the architecture with the first opt-in enhancement:
  `Core Button Hover Colors`.
- Move the existing editor-only Block Copy/Paste utility under Core Control and
  govern it with the same registry and option.

Decision:

- `docs/decisions/core-control-core-button-hover.md`

Planning:

- `.context/planning/023_VERSION_1_3_4_CORE_CONTROL_PLANNING.md`

Dependencies:

- V1 / 1.3.1 remains focused on Slider controls and contract completion.
- Human must approve starting V1 / 1.3.4 before implementation or project
  version metadata changes.

Scope:

- Add a module-shaped PHP foundation under `modules/core-control/`.
- Add editor extension code under `src/core-controls/`.
- Store feature toggles in the namespaced `skvn_core_controls` option.
- Register one reusable feature registry so later core enhancements do not
  duplicate menu, Settings API, sanitization, or asset-loading logic.
- Add `Core Button Hover Colors`, disabled by default.
- Add `Block Copy/Paste`, disabled by default.
- Extend all `core/button` blocks with namespaced hover text/background color
  controls in a separate stable Inspector panel.
- Apply configured colors to `:hover` and `:focus-visible`, with scoped CSS and
  reduced-motion handling.
- Preserve compatibility attributes and saved values when the feature is
  disabled.
- Block Copy/Paste must not override native clipboard handlers, alter saved
  markup, add frontend assets, or depend on Gutenberg private DOM/UI.

Constraints:

- Do not modify WordPress core, Gutenberg private panel UI, or GeneratePress.
- Do not create or rename a plugin.
- Do not use raw unnamespaced attributes, classes, options, or CSS variables.
- Do not use `!important` or require frontend JavaScript.
- The feature toggle controls UI and styling, not parsing compatibility.
- Do not add Core Control enhancements beyond Button Hover and Block Copy/Paste
  in this milestone.
- Do not add automatic third-party plugin conflict detection.
- Keep the first admin UI functional and clear; visual redesign is later scope.

Acceptance draft:

- [ ] `Core Control` appears under the existing `SKVN Marine` admin menu
- [ ] The registry supports adding another feature without duplicating the settings-page foundation
- [ ] `skvn_core_controls` is registered and sanitizes only known boolean keys
- [ ] `Core Button Hover Colors` defaults to disabled
- [ ] `Block Copy/Paste` defaults to disabled
- [ ] Disabled state exposes no hover controls and applies no hover styling
- [ ] Enabled state exposes text/background hover controls for all `core/button` blocks
- [ ] Existing buttons remain unchanged until hover values are configured
- [ ] Hover and `:focus-visible` use the configured colors
- [ ] Reduced-motion removes the governed color transition
- [ ] Disable/re-enable preserves configured values
- [ ] Editor reload and plugin deactivate/reactivate do not produce invalid blocks
- [ ] Editor preview and frontend output remain consistent
- [ ] Enabled Block Copy/Paste exposes exactly two editor menu actions
- [ ] Disabled Block Copy/Paste exposes neither action
- [ ] Slider and nested block hierarchy survive cross-page visual-editor paste
- [ ] Native WordPress copy/paste behavior remains unchanged
- [ ] No `!important`, frontend JavaScript, WordPress core change, or GeneratePress change is introduced
- [ ] PHP lint, plugin build, deploy artifact audit, and onsite QA pass
- [ ] Human approves milestone completion

### 1.3.8 Custom Icon made by WhySchools

- [ ] Upload custom icon

### 1.3.9 — Slider Dynamic Rendering & Controls Onsite QA

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

**Deferred from 1.3.3 — Collections onsite QA (run at end of 1.3.9):**

- [ ] Product Grid renders correctly onsite with WooCommerce products (real data, not editor preview)
- [ ] Post Grid renders correctly onsite with real posts
- [ ] Product Carousel renders and Swiper initializes without errors
- [ ] Post Carousel renders and Swiper initializes without errors
- [ ] Responsive presets (`1-1-1`, `2-1-1`, `3-2-1`, `4-2-1`, `5-3-1`) work on desktop and mobile
- [ ] Product card quote/view actions link correctly with product context
- [ ] Post card read action links to the correct post URL
- [ ] Image fallback (product placeholder / SKVN fallback) works when no image is set
- [ ] WooCommerce inactive state shows graceful fallback, no fatal
- [ ] Browser console is clean for all four collection layouts
- [ ] Human approves collections onsite QA pass

### 1.3.10 — SKVN Team Credits Easter Egg

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
