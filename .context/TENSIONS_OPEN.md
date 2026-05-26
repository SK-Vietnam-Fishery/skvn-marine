# Tensions — OPEN

> Chỉ chứa `Status: OPEN` entries.
> Agent luôn đọc toàn bộ file này trước mỗi task.
> OPEN entries không được filter theo tag.
> Khi human resolve → move sang `TENSIONS_ACTIVE.md`, đổi `Status: RESOLVED_ACTIVE`.

---

## [2025-01-01 00:00] | multilingual
Tension:    Polylang activate ngay V1 hay chỉ prepare (text domain + no hardcoded strings)?
Context:    Planning phase
Proposal:   Chỉ prepare: dùng text domain đúng, tránh hardcode Vietnamese strings trong theme/plugin UI
Constraint: Master context: "If the first site can launch in English only — prepare for multilingual, delay multilingual complexity until needed."
Severity:   low
Tags:       multilingual, theme
Milestone:  V1 / 1.0.0
Status:     OPEN
Resolved:
Decision:   Lean toward: chỉ prepare, KHÔNG activate Polylang V1.

---

## [2025-01-01 00:00] | slider
Tension:    Slider editor UX: stacked / selected-slide-preview / lightweight carousel?
Context:    Planning phase — slider block chưa implement
Proposal:   Stacked (slides xếp chồng trong editor, Swiper chỉ chạy frontend)
Constraint: "Not fully decided yet. V1 editor view should likely render slides stacked or in a simplified preview."
Severity:   low
Tags:       blocks, slider
Milestone:  V1 / 0.3.0
Status:     OPEN
Resolved:
Decision:   Lean toward: stacked preview.

