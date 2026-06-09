# MILESTONES_HISTORY.md — SKVN Marine

> Archive for completed milestones from previous versions/phases.
> Không load file này mặc định khi bắt đầu task, trừ khi cần audit milestone cũ.
> Chỉ move milestone vào đây khi human approve chuyển milestone/version.

---

## V1 / 0.1.0 — Child Theme Skeleton + Local Runtime Baseline

Status: **DONE**
Started: **2026-05-18**
Completed: **2026-05-20**
Approved by human: **2026-05-20**

Acceptance:

- [x] Local WP runtime root exists: `D:\Github\minhhaifish`
- [x] `.local/ENVIRONMENT.md` stores local paths and credentials
- [x] `.local/` is git ignored
- [x] WordPress core installed in runtime root
- [x] GeneratePress parent theme installed in runtime root
- [x] `skvn-marine` child theme active in runtime root
- [x] `skvn-marine-blocks` plugin active in runtime root
- [x] External required plugins installed in runtime root, not source repo
- [x] PHP server smoke test returns HTTP 200 on `http://localhost:8080`
- [x] Theme PHP syntax check passed after latest code changes
- [x] Plugin build check passed after latest code changes
- [x] Human approves milestone completion

---

## V1 / 0.2.0 — Design System, Block Styles, Patterns

Status: **CARRIED_WITH_TEST_DEBT**
Started: **2026-05-20**
Carried forward: **2026-05-21**
Approved by human to continue: **2026-05-21**

Acceptance:

- [x] Theme tokens reviewed/refined
- [x] `theme.json` reviewed/refined
- [x] Base typography/layout implemented
- [x] Header baseline implemented with GeneratePress + SKVN child theme CSS, no header builder plugin
- [x] Footer baseline implemented with theme pattern/template + SKVN child theme CSS, no footer builder plugin
- [x] Block styles implemented
- [x] Starter patterns implemented
- [x] Trust feature strip reusable pattern implemented
- [x] Newsletter signup band reusable pattern implemented with replaceable image block and no custom form handler
- [ ] Homepage test page assembled in WP editor with placeholder content/images to validate layout before finalizing reusable patterns
- [ ] Runtime smoke test passed
- [x] Human approves continuing to 0.3.0 with test debt

Test debt carried into V1:

- Insert `wp-content/themes/skvn-marine/patterns/homepage-test.php` into a WordPress page.
- Visually review desktop/mobile layout in runtime.
- Run runtime smoke test when local WP server responds reliably.

---

## V1 / 0.3.0 — Slider / Slide Block

Status: **CARRIED_WITH_TEST_DEBT**
Started: **2026-05-21**
Carried forward: **2026-05-21**
Approved by human to continue: **2026-05-21**

Acceptance:

- [x] TypeScript build pipeline passes
- [x] Slider + Slide block registration works
- [x] Swiper dependency rationale documented
- [x] Swiper loads only for slider block frontend
- [x] Swiper config comes from block attributes
- [x] Keyboard navigation enabled
- [x] Pause on hover implemented
- [x] `prefers-reduced-motion` disables autoplay
- [x] Editor uses stacked/simplified preview, no autoplay
- [ ] Runtime smoke test passed
- [x] Human approves continuing to 0.4.0 with test debt

Test debt carried into V1:

- Create a `Slider Runtime Test 0.3.0` page in runtime.
- Insert `skvn-marine/slider` with at least 3 `skvn-marine/slide` children.
- Verify frontend slider initialization, arrows, dots, keyboard navigation, hover pause, reduced-motion autoplay behavior, and console status.

---

## V1 / 0.4.0 — Woo Product Sections

Status: **CARRIED_WITH_TEST_DEBT**
Started: **2026-05-21**
Carried forward: **2026-05-21**
Approved by human to continue: **2026-05-21**

Acceptance:

- [x] V1 decision: use WooCommerce native blocks/patterns
- [x] Woo native product patterns created/styled
- [x] Product card visual styling implemented
- [x] Mobile CTA remains visible
- [x] No custom Product Grid/List blocks in V1
- [ ] Runtime smoke test passed
- [x] Human approves continuing to 0.5.0 with test debt

Test debt carried into V1:

- Create a `Woo Product Sections Test 0.4.0` page in runtime.
- Insert `SKVN Woo Category Strip` and `SKVN Woo Product Grid`.
- Verify categories/products render from real WooCommerce data.
- Verify product card CTA remains visible on mobile.
- Verify no invalid block warning after save/reload.

---

## V1 / 0.5.0 — SKVN Full Width Layout

Status: **DONE**
Started: **2026-05-21**
Completed: **2026-05-22**
Approved by human: **2026-05-22**

Acceptance:

- [x] `SKVN Full Width` page template exists in child theme
- [x] Template keeps GeneratePress header/footer intact
- [x] Template removes the narrow default content wrapper for selected pages
- [x] `.alignfull` sections can reach viewport width
- [x] Inner content remains constrained to SKVN wide width
- [x] Pattern UI test page uses the full-width layout
- [x] Desktop hero headline no longer collapses into a narrow column
- [x] Mobile has no horizontal scroll
- [x] PHP syntax check passed
- [x] Runtime smoke test passed
- [x] Human approves milestone completion

---

## V1 / 0.5.1 — Page Display & Sidebar Controls

Status: **DONE**
Started: **2026-05-22**
Completed: **2026-06-02**
Approved by human: **2026-06-02**

Acceptance:

- [x] CF7/CFDB7 deferred until after V1 / 0.6.0
- [x] n8n deferred until after version 1.0.0
- [x] Quote UI/page surface moved to V1 / 0.6.0
- [x] Page editor exposes a Hide site header toggle
- [x] Page editor exposes a Hide site footer toggle
- [x] Toggle values are saved as page meta with `skvn_marine_`/`_skvn_` naming
- [x] Frontend applies header/footer visibility per page without editing GeneratePress parent
- [x] Controls are available through admin/editor UI, not raw class input
- [x] Runtime visual smoke test passed
- [x] Human approves milestone completion

---

## V1 / 0.6.0 — Quote UI & Editor Controls

Status: **DONE**
Started: **2026-06-02**
Completed: **2026-06-02**
Approved by human: **2026-06-02**

Acceptance:

- [x] Quote CTA visual path uses `/request-a-quote/?product_id=123`
- [x] Request quote page visual surface exists or is planned as an editor-owned page
- [x] Quote UI uses project visual classes/tokens
- [x] Editing controls that marketing users need are exposed through sidebar controls, not raw class input
- [x] Runtime visual smoke test passed
- [x] Human approves milestone completion

---

## V1 / 0.7.0 — Basic CF7/CFDB7 Quote Form

Status: **CARRIED_WITH_RUNTIME_VERIFICATION_DEBT**
Started: **2026-06-02**
Carried forward: **2026-06-02**
Approved by human to continue: **2026-06-02**

Acceptance:

- [x] CF7 form contract prepared for Request a Quote
- [x] CF7 markup uses project classes: `skvn-form`, `skvn-quote-form`, `skvn-button`, `skvn-button--primary`
- [x] Required visible fields prepared
- [x] Required hidden fields prepared: `product_id`, `product_sku`, `product_name`, `product_url`, `source_url`, UTM fields
- [x] Thank-you page contract prepared
- [x] n8n remains deferred/unexposed
- [x] WP Admin setup path documented for onsite testing
- [ ] Onsite CF7 form existence confirmed by human evidence
- [ ] Onsite request quote page contains the CF7 form
- [ ] Onsite thank-you page exists
- [ ] CFDB7 stores quote submission
- [ ] Runtime quote form smoke test passed
- [ ] Human approves milestone completion

Completion snapshot:

Done:

- CF7 markup contract exists in `docs/artifacts/cf7-quote-form-0.7.0.md`.
- Visible and hidden field contract exists.
- Theme source contains SKVN form/button styling for quote form classes.
- Dev/runtime setup shortcut exists in `tools/setup-quote-flow-070.php`.
- Onsite-first test checklist exists in `docs/testing/onsite-quote-flow-0.7.0.md`.
- WP Admin manual setup path is documented for onsite testing.
- n8n remains deferred and unexposed by repo/source changes.

Not done / carried to 0.7.1:

- Onsite CF7 form existence has not been confirmed with evidence in repo.
- Onsite request quote page shortcode placement has not been confirmed with evidence in repo.
- Onsite thank-you page existence has not been confirmed with evidence in repo.
- CFDB7 storage has not been confirmed with a submitted row.
- Runtime smoke test has not passed.

Deferred test debt:

- [ ] Onsite hidden/context field and full UX smoke test is intentionally deferred to V1 / 0.10.0 because human is working under time pressure.
- [ ] See `docs/testing/onsite-quote-flow-0.7.0.md`.
- [ ] Related docs/files to open at V1 / 0.10.0:
  - `docs/testing/onsite-quote-flow-0.7.0.md` — exact onsite test checklist and evidence format
  - `docs/artifacts/cf7-quote-form-0.7.0.md` — expected CF7 form markup/classes/fields
  - `docs/decisions/quote-flow.md` — quote-flow scope and deferred n8n decision
  - `.context/modules/QUOTE_FLOW.md` — active quote-flow module protocol
  - `docs/standards/security-guidelines.md` — form/security constraints
  - `wp-content/themes/skvn-marine/style.css` — source styles for `skvn-form` / `skvn-quote-form`

---

## V1 / 0.7.1 — Quote Flow Runtime Verification & Handoff

Status: **CARRIED_TO_0.10.0**
Started: **2026-06-02**
Carried forward: **2026-06-03**
Approved by human to continue: **2026-06-03**

Purpose:

- Close the remaining 0.7.0 verification gap without pretending this is a new feature milestone.
- Confirm that the manually created onsite/admin setup matches the source/docs contract.
- Apply only small quote-flow fixes if onsite evidence shows a mismatch.
- Do not add new quote-flow features before onsite/runtime evidence or explicit approval.

Acceptance:

- [x] Onsite/admin handoff checklist exists in `docs/testing/onsite-quote-flow-0.7.1.md`
- [ ] Human confirms the onsite CF7 form exists
- [ ] Human confirms `/request-a-quote/` contains the CF7 shortcode/form
- [ ] Human confirms `/quote-thank-you/` exists
- [ ] CF7 form markup matches `docs/artifacts/cf7-quote-form-0.7.0.md`
- [ ] CFDB7 stores at least one test submission
- [ ] Hidden/context fields appear in stored submission or mismatch is documented for 0.10.0
- [ ] Runtime smoke test result is recorded
- [ ] No n8n webhook is exposed
- [ ] Human approves closing 0.7.x quote-form setup

Carried to 0.10.0:

- Onsite CF7 form existence evidence.
- Request quote page CF7 shortcode/form evidence.
- Thank-you page existence evidence.
- CF7 markup comparison.
- CFDB7 stored submission evidence.
- Hidden/context field verification.
- Runtime smoke test result.
- No exposed n8n webhook evidence.

---

## V1 / 0.8.0 — SKVN Editor Controls

Status: **CARRIED_TO_0.10.0**
Started: **2026-06-03**
Carried forward: **2026-06-03**
Approved by human to continue: **2026-06-03**

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
- [ ] Editor and frontend output stay visually aligned onsite
- [x] GeneratePress parent remains untouched
- [x] Human approves continuing to 0.9.0 with onsite test debt

Carried to 0.10.0:

- Run `docs/testing/onsite-editor-controls-0.8.0.md` on the onsite site.
- Confirm SKVN Accordion controls persist after save/reload.
- Confirm frontend output matches editor intent.
- Review desktop/mobile screenshots.
- Record console/editor errors or confirm clean.

---

## V1 / 0.9.0 — Footer Page Settings

Status: **CARRIED_TO_0.10.0**
Started: **2026-06-03**
Carried forward: **2026-06-03**
Approved by human to continue: **2026-06-03**

Purpose:

- Add a plugin settings page that stores the selected reusable footer page ID in `skvn_footer_page_id`.
- Let the theme render the selected footer page into GeneratePress' `generate_footer` surface.
- Keep GeneratePress as the footer foundation and fall back to the default GeneratePress footer when no page is selected.
- Implement the footer settings code as a migration-ready module inside the current `skvn-marine-blocks` plugin; do not create or rename to `gutenberg-supercharger` or `gutenberg-turbo` in 0.9.0.
- Treat `Gutenberg Supercharger` as a possible V4 / 4.0.0 umbrella-plugin direction and standard/core product name only. `Gutenberg Supercharger Stage 2` is the pro/commercial stage name. `Gutenberg Remap` is retained only as an alternate/redirect candidate.

Acceptance:

- [x] Plugin settings page stores `skvn_footer_page_id`
- [x] Setting value is restricted to a valid WordPress page ID
- [x] Theme `inc/footer.php` renders the selected footer page through `generate_footer`
- [x] Default GeneratePress footer remains the fallback when no page is selected
- [x] No custom CPT is introduced
- [x] No display rules system is introduced
- [x] Footer settings are module-shaped inside `skvn-marine-blocks`, with current slug/text domain/option key preserved
- [x] No `gutenberg-supercharger`/`gutenberg-turbo` plugin, plugin slug rename, namespace rename, or option-key rename is introduced
- [x] GeneratePress parent remains untouched
- [x] Footer output is escaped/sanitized through WordPress-safe rendering paths
- [ ] Online smoke test confirms selected footer page renders
- [ ] Online smoke test confirms fallback footer works
- [x] Online test checklist/report exists in `docs/testing/footer-page-settings-0.9.0.md` and `docs/decisions/footer-page-settings-0.9.0.md`
- [x] Human approves continuing to 0.10.0 with online test debt

Carried to 0.10.0:

- Run `docs/testing/footer-page-settings-0.9.0.md` on the online site.
- Confirm selected footer page renders.
- Confirm default GeneratePress fallback works.
- Confirm invalid page ID fallback or document mismatch.

---

## V1 / 0.10.0 — Onsite Test Debt Resolution

Status: **DONE_WITH_CARRIED_DATA_FLOW_DEBT**
Started: **2026-06-03**
Completed: **2026-06-05**
Approved by human: **2026-06-05**

Purpose:

- Resolve onsite test debt carried from 0.8.0 and 0.9.0.
- Record footer evidence and CF7 interface evidence.
- Separate remaining CF7 form/data flow checks from visual/UI readiness.

Acceptance:

- [x] Human runs `docs/testing/footer-page-settings-0.9.0.md` on the online site and reports evidence
- [x] Footer Page Settings selected footer page renders online
- [x] Footer Page Settings default GeneratePress fallback works online
- [x] Footer Page Settings invalid page ID fallback is confirmed or mismatch is documented
- [x] Onsite CF7 form existence confirmed by human evidence
- [x] Onsite request quote page contains the CF7 shortcode/form
- [x] CF7/Request Quote interface visual check passed onsite
- [x] Desktop/mobile benchmark UI issues were reviewed and source CSS hardening was applied where needed
- [x] Human approves closing onsite test debt milestone

Not closed in 0.10.0 / carried to 1.1.2:

- CF7 form submission/data flow.
- CFDB7 stored submission evidence.
- Visible field storage evidence.
- Hidden/context field storage evidence.
- Product/product-card/page-block origin query params.
- Thank-you/success UX after submit.
- Map block/display issue because the current map surface is not viewable.

Carry-forward target:

- V1 / 1.1.2 — Product Quote Flow & Map Block Testing.

---

## V1 / 0.11.0 — SKVN Marine Admin Menu

Status: **DONE**
Started: **2026-06-05**
Carried forward: **2026-06-05**
Approved by human to continue: **2026-06-05**
Completed after onsite launch: **2026-06-05**

Purpose:

- Move the existing SKVN Footer admin surface out of `Settings`.
- Create a top-level `SKVN Marine` admin menu owned by `skvn-marine-blocks`.
- Put the footer settings screen under the `SKVN Marine` admin menu as one tab/page.
- Add a safe footer background preset setting for the selected footer page.
- Leave admin menu ordering/repositioning to the external ASE plugin instead of hardcoding menu position rules in SKVN code.

Acceptance:

- [x] Source implements `SKVN Marine -> Footer` under the plugin-owned admin menu.
- [x] Existing option key `skvn_footer_page_id` remains unchanged.
- [x] New option key `skvn_footer_background_preset` stores an approved preset, not raw color input.
- [x] Footer background preset is sanitized to approved values.
- [x] Theme applies footer background only when a valid custom footer page is active.
- [x] Theme styles `.skvn-footer-page` and outer `.skvn-site-footer` for selected presets.
- [x] Default GeneratePress fallback footer remains the source fallback path.
- [x] Capability checks and nonce protection remain through the WordPress Settings API.
- [x] SKVN code does not hardcode advanced admin menu ordering; ASE handles menu repositioning onsite.
- [x] Onsite evidence confirms `Settings -> SKVN Footer` is no longer the primary admin location.
- [x] Onsite evidence confirms custom footer and fallback behavior.
- [x] Human approves milestone completion after onsite evidence.

Closed in 1.0.0 onsite launch pass:

- `docs/testing/footer-appearance-settings-0.11.0.md`.
- `docs/testing/onsite-0.11-0.12-completion-checklist.md`.

---

## V1 / 0.12.0 — SKVN Header Actions And B2B Search

Status: **DONE**
Started: **2026-06-05**
Carried forward: **2026-06-05**
Approved by human to continue: **2026-06-05**
Completed after onsite launch: **2026-06-05**

Purpose:

- Add governed header actions without replacing the GeneratePress header shell.
- Support product search, post/site search, contact CTA, and optional Request Quote CTA.
- Plan the B2B search results experience as a governed page that separates Products from Related articles.
- Keep phase 1 search lightweight: taxonomy/title-first native WP/Woo queries, no Elastic/OpenSearch, and no custom query cache.

Acceptance:

- [x] Human confirms 0.12.0 as the exact implementation target before code.
- [x] Source renders header actions through GeneratePress hook integration without editing GeneratePress parent files.
- [x] `SKVN Marine -> Header` settings are documented and implemented.
- [x] Product search can be enabled/disabled in source settings.
- [x] Post/site search can be enabled/disabled in source settings.
- [x] Contact button can be enabled/disabled in source settings.
- [x] Request Quote button can be enabled/disabled in source settings.
- [x] Search target is explicit in source: products, articles, or all site.
- [x] B2B search results template separates Products from Related articles.
- [x] Product matching uses product tags/categories/title before content fallback.
- [x] Related article matching uses post tags/categories/title before content fallback.
- [x] No Elastic/OpenSearch dependency is added in phase 1.
- [x] No custom query cache or SQL cache table is added in phase 1.
- [x] Onsite evidence confirms GeneratePress header remains stable.
- [x] Onsite evidence confirms header mobile behavior does not break GeneratePress navigation.
- [x] Onsite evidence confirms keyboard/focus behavior for search and buttons.
- [x] Human approves milestone completion after onsite evidence.

Closed in 1.0.0 onsite launch pass:

- `docs/testing/header-actions-search-0.12.0.md`.
- `docs/testing/onsite-0.11-0.12-completion-checklist.md`.

---

## V1 / 1.0.0 — V1 Launch Ready

Status: **DONE**
Started: **2026-06-05**
Completed: **2026-06-05**
Approved by human: **2026-06-05**

Purpose:

- Confirm the V1 launch-ready baseline after onsite launch.
- Close 0.11.0 and 0.12.0 onsite UI/test debt after human reported pass.
- Keep post-launch quote data-flow and map checks deferred to V1 / 1.1.2.

Recommended onsite stability plugins:

- Admin and Site Enhancements (ASE) — recommended onsite admin/governance helper for menu organization and wp-admin workflow polish. SKVN source must not hardcode advanced admin menu ordering that belongs to ASE.
- Ultra Addons for Contact Form 7 — recommended onsite CF7 helper for form UI/stability enhancements around the existing CF7 workflow. This does not change the V1 rule: do not custom-code quote form handling; CF7/CFDB7 remain the quote form/data layer.
- Both plugins are external WordPress runtime dependencies only. They must not be copied or committed into this source repo.

Acceptance:

- [x] Accessibility pass
- [x] Mobile QA pass
- [x] SEO/GEO structure pass
- [x] Performance and asset loading review
- [x] Admin and Site Enhancements (ASE) is reviewed/active onsite if needed for admin workflow stability.
- [x] Ultra Addons for Contact Form 7 is reviewed/active onsite if needed for CF7 form UI/stability.
- [x] No forbidden parent-theme changes
- [x] No external plugins committed to source repo
- [x] Human approves V1 launch readiness

Boundary carry-forward:

- n8n remains deferred/unexposed and should be reviewed only at the V1 → V2 boundary unless human explicitly moves it into scope earlier.

Init prompt:

- `docs/artifacts/init-prompt-v1-1.0.0-launch-ready.md`

Next milestone:

- V1 / 1.1.0 — Layout Blocks.

---

## V1 / 1.1.0 — Layout Blocks

Status: **DONE — VALIDATION DEFERRED**
Started: **2026-06-05**
Moved forward by human: **2026-06-07**

Purpose:

- Add governed `skvn-marine/card-grid` and `skvn-marine/card` layout blocks.
- Keep visual styling theme-owned and block logic plugin-owned.
- Prepare stable layout markup without coupling layout blocks to motion behavior.

Implementation completed:

- [x] Footer background preset admin UI uses stable swatch/preview treatment.
- [x] Repeated layout artifacts justify shared grid/card governance.
- [x] Core-block and theme-pattern alternatives were evaluated.
- [x] `skvn-marine/card-grid` and `skvn-marine/card` exist in the plugin.
- [x] Blocks use `block.json`, TypeScript, and InnerBlocks.
- [x] Controls save stable `skvn-*` layout classes.
- [x] Theme CSS implements the layout-critical classes.
- [x] No frontend JavaScript runtime was added for static grids.
- [x] Layout blocks do not expose or depend on motion controls.
- [x] Plugin build passed.

Deferred by explicit human decision:

- Run `docs/testing/card-grid-layout-blocks-1.1.0.md`.
- Confirm editor/frontend behavior at desktop, tablet, and mobile widths.
- Evaluate `skvn-marine/quote` only after card-grid/card validation.
- Close validation under V1 / 1.4.1.

Next milestone:

- V1 / 1.2.0 — Slider Editor & Motion.

---

## V1 / 1.2.0 — Slider Editor & Motion

Status: **DONE — ONSITE QA DEFERRED**
Started: **2026-06-07**
Moved forward by human: **2026-06-07**

Implemented:

- [x] Slide background image choose, replace, remove, and editor preview.
- [x] Governed overlay opacity.
- [x] Editable heading, lead, and CTA template.
- [x] Plugin-owned Swiper and structural CSS.
- [x] Accessible Accordion progressive enhancement with real-height animation.
- [x] Card motion presets with independent Desktop/Tablet/Mobile targeting.
- [x] Reduced-motion and no-JavaScript fallback architecture.
- [x] Plugin build and PHP syntax checks passed.

Deferred by explicit human decision:

- Onsite editor and frontend verification.
- Slider image persistence and Swiper behavior.
- Accordion and Card motion accessibility/responsive checks.
- Consolidate these tests with 1.2.1 under V1 / 1.2.9.

Next milestone:

- V1 / 1.2.1 — SKVN Slider Presets & Inserter.

---

## V1 / 1.2.1 — SKVN Slider Presets & Inserter

Status: **DONE — FRONTEND ARCHITECTURE FIX DEFERRED**
Started: **2026-06-07**
Completed: **2026-06-08**
Approved by human: **2026-06-08**

Purpose:

- Register one `SKVN Marine` Block Inserter category.
- Add Hero Slider, Product Showcase, and Card Carousel as variations/templates
  over the existing Slider/Slide blocks and Swiper runtime.
- Keep native Gutenberg stacked editing and List View operations.

Acceptance:

- [x] Human approved the add-and-see preset direction and rejected a custom slide manager for MVP.
- [x] `SKVN Marine` appears as a dedicated Block Inserter category.
- [x] Existing SKVN-owned blocks appear under `SKVN Marine`.
- [x] Hero Slider, Product Showcase, and Card Carousel appear as inserter choices.
- [x] Presets reuse `skvn-marine/slider`, `skvn-marine/slide`, and one Swiper runtime.
- [x] No slide manager, selected-slide canvas, or setup modal was introduced.
- [x] Native Gutenberg List View and block actions remain usable.
- [x] Existing Slider serialization remained valid.
- [x] Plugin build passed.
- [x] Human approved milestone completion and transition to V1 / 1.2.3.

Deferred:

- Frontend Hero Slider media/content frame bug is documented in
  `docs/testing/slider-frontend-media-content-layer-bug-1.2.1.md`.
- The static markup workaround was rejected to avoid compound deprecation debt.
- Dynamic Slider rendering architecture is planned for V1 / 1.3.0.
- Slider/motion onsite QA must be reconciled with the V1 / 1.3.0 migration
  before the deferred QA milestone is closed.

Next milestone:

- V1 / 1.2.3 — SKVN Feature Showcase.

---

## V1 / 1.2.3 — SKVN Feature Showcase

Status: **DONE — ONSITE QA CARRIED TO 1.2.9**
Started: **2026-06-08**
Completed: **2026-06-09**
Approved by human: **2026-06-09**

Purpose:

- Activate and refactor `SKVN Feature Showcase` into a reusable panel-only block.
- Keep the B2B seafood editorial intro as an optional theme pattern.
- Preserve the existing standard Accordion as a separate content contract.

Completed:

- [x] Registered `skvn-marine/feature-showcase` under `SKVN Marine`.
- [x] Added editable panel count, order, label, heading, copy, image, and ALT.
- [x] Added horizontal and vertical desktop layouts.
- [x] Added desktop hover/focus activation and mobile tap disclosure.
- [x] Added governed Ocean, Deep navy, Marine teal, and Fresh sky color packages.
- [x] Preserved native `details`/`summary`, keyboard access, no-JS readability,
      and reduced-motion fallback.
- [x] Prevented active-panel collapse and image/content flashing.
- [x] Improved panel response by animating `flex-grow` instead of the full
      `flex` shorthand.
- [x] Added the `B2B Seafood Feature Showcase` theme pattern.
- [x] Restored Gutenberg normal/wide/full width semantics under
      `SKVN Full Width Canvas`.
- [x] Documented the full-width surface plus governed inner-container contract.
- [x] Plugin build, PHP lint, diff check, and context consistency passed.

Carried to V1 / 1.2.9:

- Onsite Feature Showcase interaction and responsive verification.
- Real Media Library image and ALT verification.
- B2B pattern editor/frontend parity.
- Full Width Canvas normal, wide, and full alignment verification.
- Legacy saved-pattern compatibility verification.

Next milestone:

- V1 / 1.2.9 — Slider & Motion Onsite QA.
