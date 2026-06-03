# Tensions — Active

> Chỉ chứa `Status: RESOLVED_ACTIVE` entries của milestone hiện tại.
> Agent đọc file này mỗi task, nhưng dùng tag filter để tập trung vào entries liên quan.
> Move sang `TENSIONS_HISTORY.md` chỉ khi human approve milestone transition.

---

## [2025-01-01 00:00] | slider
Tension:    Slider editor UX: stacked / selected-slide-preview / lightweight carousel?
Context:    Planning phase — slider block chưa implement
Proposal:   Stacked (slides xếp chồng trong editor, Swiper chỉ chạy frontend)
Constraint: "Not fully decided yet. V1 editor view should likely render slides stacked or in a simplified preview."
Severity:   low
Tags:       blocks, slider
Milestone:  V1 / 0.8.0
Status:     RESOLVED_ACTIVE
Resolved:   2026-06-03
Decision:   Use stacked/simplified slider preview in the editor. Do not run Swiper autoplay in the editor. Slider sidebar behavior controls may persist as saved attributes, but frontend Swiper runtime remains frontend-only and must respect prefers-reduced-motion.
