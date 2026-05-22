# MILESTONES.md — SKVN Marine

> Source of truth cho milestone hiện tại và checklist chuyển mốc.
> File này phải được đọc khi bắt đầu task.
> Chỉ human mới có quyền xác nhận chuyển milestone/version.

---

## Current Milestone

Current: **V1 / 0.4.0 — Woo Product Sections**
Status: **IN_PROGRESS**
Started: **2026-05-21**

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

### 0.4.0 — Woo Product Sections

Status: **IN_PROGRESS**
Started: **2026-05-21**

Acceptance:

- [x] V1 decision: use WooCommerce native blocks/patterns
- [x] Woo native product patterns created/styled
- [x] Product card visual styling implemented
- [x] Mobile CTA remains visible
- [x] No custom Product Grid/List blocks in V1
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
