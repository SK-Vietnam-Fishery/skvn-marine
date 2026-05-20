# MILESTONES.md — SKVN Marine

> Source of truth cho milestone hiện tại và checklist chuyển mốc.
> File này phải được đọc khi bắt đầu task.
> Chỉ human mới có quyền xác nhận chuyển milestone/version.

---

## Current Milestone

Current: **V1 / 0.1.0 — Child theme skeleton + local runtime baseline**
Status: **IN_PROGRESS**
Started: **2026-05-18**

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

---

## V1 Checkpoints

### 0.1.0 — Child Theme Skeleton + Local Runtime Baseline

Status: **IN_PROGRESS**

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
- [ ] Human approves milestone completion

### 0.2.0 — Design System, Block Styles, Patterns

Status: **PENDING**

Acceptance:

- [ ] Theme tokens reviewed/refined
- [ ] `theme.json` reviewed/refined
- [ ] Base typography/layout implemented
- [ ] Header baseline implemented with GeneratePress + SKVN child theme CSS, no header builder plugin
- [ ] Footer baseline implemented with theme pattern/template + SKVN child theme CSS, no footer builder plugin
- [ ] Block styles implemented
- [ ] Starter patterns implemented
- [ ] Trust feature strip reusable pattern implemented
- [ ] Newsletter signup band reusable pattern implemented with replaceable image block and no custom form handler
- [ ] Homepage test page assembled in WP editor with placeholder content/images to validate layout before finalizing reusable patterns
- [ ] Runtime smoke test passed
- [ ] Human approves milestone completion

### 0.3.0 — Slider / Slide Block

Status: **PENDING**

Acceptance:

- [ ] TypeScript build pipeline passes
- [ ] Slider + Slide block registration works
- [ ] Swiper dependency rationale documented
- [ ] Swiper loads only for slider block frontend
- [ ] Swiper config comes from block attributes
- [ ] Keyboard navigation enabled
- [ ] Pause on hover implemented
- [ ] `prefers-reduced-motion` disables autoplay
- [ ] Editor uses stacked/simplified preview, no autoplay
- [ ] Runtime smoke test passed
- [ ] Human approves milestone completion

### 0.4.0 — Woo Product Sections

Status: **PENDING**

Acceptance:

- [x] V1 decision: use WooCommerce native blocks/patterns
- [ ] Woo native product patterns created/styled
- [ ] Product card visual styling implemented
- [ ] Mobile CTA remains visible
- [ ] No custom Product Grid/List blocks in V1
- [ ] Runtime smoke test passed
- [ ] Human approves milestone completion

### 0.5.0 — Quote Flow Integration

Status: **PENDING**

Acceptance:

- [ ] CF7 ↔ n8n method resolved
- [ ] Request quote page exists
- [ ] Required hidden fields prepared
- [ ] CF7 markup uses project classes
- [ ] Thank-you page exists
- [ ] n8n webhook remains protected
- [ ] Runtime smoke test passed
- [ ] Human approves milestone completion

### 1.0.0 — V1 Launch Ready

Status: **PENDING**

Acceptance:

- [ ] Accessibility pass
- [ ] Mobile QA pass
- [ ] SEO/GEO structure pass
- [ ] Performance and asset loading review
- [ ] No forbidden parent-theme changes
- [ ] No external plugins committed to source repo
- [ ] Human approves V1 launch readiness
