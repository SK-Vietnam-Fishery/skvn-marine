# Site loading and navigation UX

Date: 2026-11-03. Status: research/ideation; cập nhật human đã đặt scope vào current 1.3.18 dưới nhãn UX hotfix. [Plan 046](../../.context/planning/046_VER_1_3_18_SITE_LOADING_NAVIGATION_UX_HOTFIX_PLAN.md) đang chờ duyệt; chưa implementation/commit. Các đề xuất ban đầu bên dưới được làm rõ trong plan, chưa tự thành runtime acceptance PASS.

## User request and decision boundary

Human requests a site logo during initial loading and an ease-out fade when navigating after initial load. Record this as desired UX, not approval of a specific technical mechanism or duration. The 1.3.22/1.3.23 Product/Post design milestones are separately approved; this note does not add loading work to them. Existing instruction not to modify skeleton remains in force.

## Critique and proposed behavior

- Separate initial-load overlay dismissal from subsequent page navigation. A logo overlay is not a skeleton and does not fix underlying layout shifts, late CSS or carousel initialization flashes.
- Initial load: prefer the configured site logo, allow an explicit alternative, fall back to site name if unavailable. Do not impose a minimum branding delay; skip a brief overlay when content is already ready. Define readiness around usable initial content, not every image or third-party request. Have a fail-open path for asset/JS failure.
- Navigation: fading the old page immediately on click can leave a blank screen during a slow response. Prefer keeping useful content visible until a destination transition is available. Evaluate native cross-document View Transitions as progressive enhancement; preserve normal navigation where unsupported or skipped. No custom AJAX router justified by this request.
- Ease-out is a timing function, not a duration. Proposal for prototype: 150–200 ms for navigation, around 200 ms for initial overlay dismissal; numbers are unapproved starting points, not measured performance results.
- No logo replay on every internal link by default. Do not delay navigation to finish animation. Handle Back/Forward, anchors, external links, downloads, modified clicks/new tabs and form/RFQ submission without interference.
- Reduced motion: bypass decorative transition. Keyboard focus, restored scroll position and page usability must survive cancellation and history restoration.

## Source findings, not onsite PASS

`wp-content/plugins/skvn-marine-blocks/modules/preloader-settings/preloader-settings.php` already exposes `use_logo` and `logo_id`; defaults include `min_display = 0`. `wp-content/themes/skvn-marine/inc/preloader.php` renders the selected attachment or site name. Automatic reuse of the native site logo is not established by this path.

The theme declares `transition: opacity .45s ease`, while `.is-hidden` sets opacity zero and visibility hidden together. Immediate visibility hiding is a source-level concern for visible fading; verify computed styles and a recording before calling it the onsite flash root cause. Splash mode includes a time-based wait; therefore do not assume all modes honor a zero branding delay.

## Requirements for later specification

| ID | Group | Expected behavior / states | Current status | Proposed work | Verification | Evidence |
|---|---|---|---|---|---|---|
| LOAD-01 | Block UI | Logo or readable fallback; fast/slow load, absent/broken logo; responsive and no forced wait | Đã có source for manual logo only; Chờ kiểm chứng | Define logo source, readiness and dismissal | Cold/warm cache and throttled network recordings; no added layout shift or blank stall | PHP paths above; onsite pending |
| LOAD-02 | Editor UX | Clearly distinguish initial loading from navigation effects; save/reopen; accessible settings on narrow screen | Chờ kiểm chứng existing controls; new UX Chưa làm | Reuse settings; avoid duplicate logo ownership | Keyboard settings test, reload and frontend comparison | Settings source above; runtime pending |
| NAV-01 | Block UI | Short ease-out departure without blank waiting; reduced-motion bypass | Chưa làm for proposed contract | Prototype transition behavior | Fast/slow destination, mobile, Back/Forward, reduced motion | Recording and actual duration pending |
| NAV-02 | Technical | Native navigation remains reliable if unsupported, canceled or failed; RFQ unaffected | Chưa làm for proposed contract | Capability-based enhancement; no timer-gated routing | Browser matrix, JS off/error, history restore, modified links, form submission | Browser versions and logs required later |

## Open decisions

Initial-entry scope (first page in a tab/session versus homepage only), exact readiness condition, duration, overlay background and whether any header stays visually stationary require prototype review. Do not impose these defaults as approved behavior.

## Sources

- [Chrome: Cross-document view transitions](https://developer.chrome.com/docs/web-platform/view-transitions/cross-document) — same-origin multi-page navigation opt-in; evaluate compatibility and fallback against target browsers at implementation.
- [W3C: prefers-reduced-motion technique](https://www.w3.org/WAI/WCAG22/Techniques/css/C39) — respects user motion preferences. This note proposes bypassing decorative fades without claiming every opacity transition violates WCAG.

Research accessed 2026-11-03. No runtime changes, build, onsite test or performance measurements in this task.
