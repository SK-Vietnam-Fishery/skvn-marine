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
