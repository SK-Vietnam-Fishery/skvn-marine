# MILESTONES.md — SKVN Marine

> Source of truth cho milestone hiện tại và checklist chuyển mốc.
> File này phải được đọc khi bắt đầu task.
> Chỉ human mới có quyền xác nhận chuyển milestone/version.

---

## Current Milestone

Current: **V1 / 0.5.0 — SKVN Full Width Layout**
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

### 0.5.0 — SKVN Full Width Layout

Status: **IN_PROGRESS**
Started: **2026-05-21**

Acceptance:

- [x] `SKVN Full Width` page template exists in child theme
- [x] Template keeps GeneratePress header/footer intact
- [x] Template removes the narrow default content wrapper for selected pages
- [x] `.alignfull` sections can reach viewport width
- [x] Inner content remains constrained to SKVN wide width
- [ ] Pattern UI test page uses the full-width layout
- [ ] Desktop hero headline no longer collapses into a narrow column
- [ ] Mobile has no horizontal scroll
- [x] PHP syntax check passed
- [ ] Runtime smoke test passed
- [ ] Human approves milestone completion

### 0.5.1 — Quote Flow Integration

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
