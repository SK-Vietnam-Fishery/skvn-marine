# Tensions — OPEN

> Chỉ chứa `Status: OPEN` entries.
> Agent luôn đọc toàn bộ file này trước mỗi task.
> OPEN entries không được filter theo tag.
> Khi human resolve → move sang `TENSIONS_ACTIVE.md`, đổi `Status: RESOLVED_ACTIVE`.

---

## [2025-01-01 00:00] | quote-flow
Tension:    CF7 ↔ n8n integration method: CF7 webhook trực tiếp hay CF7 send email → n8n catch email?
Context:    Planning phase — quote flow chưa implement
Proposal:   Dùng CF7 webhook (`add_action 'wpcf7_mail_sent'`) để POST trực tiếp đến n8n webhook URL
Constraint: Chưa có quyết định chính thức. n8n webhook phải được protect (hard URL + optional secret).
Severity:   high
Tags:       quote-flow, php
Milestone:  V1 / 0.5.0
Status:     OPEN
Resolved:
Decision:   OPEN

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

---

## [2025-01-01 00:00] | spam-protection
Tension:    Cloudflare Turnstile cho CF7: add ngay V1 hay delay?
Context:    Planning phase
Proposal:   V1: chỉ dùng CF7 honeypot. Turnstile thêm khi spam tăng.
Constraint: "Cloudflare Turnstile or reCAPTCHA if spam increases"
Severity:   low
Tags:       quote-flow, spam-protection
Milestone:  V1 / 0.5.0
Status:     OPEN
Resolved:
Decision:   Lean toward: honeypot only V1, Turnstile on-demand.
