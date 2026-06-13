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

## [2026-06-12 11:23] | slider
Tension:    Slider cần dùng Inter trong 1.3.1 nhưng typography base và font delivery toàn site thuộc theme governance, với preset/delivery UI dự kiến ở 1.6.0.
Context:    Làm typography Slider nhỏ gọn theo ảnh tham chiếu mà không đổi font toàn website.
Proposal:   Scope Inter vào asset của Slider với system fallback; không đổi theme typography tokens hoặc thêm font settings UI.
Constraint: Site branding guideline: "Typography base" do theme `style.css` và `theme.json` sở hữu; milestone 1.6.0 dự kiến governance font delivery toàn site.
Severity:   low
Tags:       slider, theme
Milestone:  V1 / 1.3.1
Status:     OPEN
Resolved:
Decision:   Conservative implementation: Slider-only Inter; human review whether to retain, self-host, or promote it into the 1.6.0 typography system.

## [2026-06-12 15:30] | editor-governance
Tension:    Tiện ích copy/paste block là editor-governance dùng chung, trong khi milestone hiện tại tập trung vào Feature Showcase autoplay và panel links.
Context:    Human yêu cầu hai nút copy block và paste block để giữ nguyên Gutenberg block markup khi chuyển nội dung giữa các trang.
Proposal:   Thêm một editor utility độc lập dùng public Gutenberg APIs; không override clipboard handler, parser, UI DOM, hoặc WordPress core.
Constraint: Current milestone: "V1 / 1.3.2 — Feature Showcase Autoplay And Panel Links".
Severity:   low
Tags:       editor-governance, blocks, milestone
Milestone:  V1 / 1.3.2
Status:     OPEN
Resolved:
Decision:   Conservative implementation: one isolated editor entry with removable public-API SlotFill actions; no settings, persistence, core patch, or custom clipboard format.
