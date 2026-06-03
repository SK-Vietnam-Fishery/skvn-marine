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
