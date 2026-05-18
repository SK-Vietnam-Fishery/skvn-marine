# TENSIONS.md — SKVN Marine

> Agent và human cùng maintain file này.
> File này giữ OPEN tensions và RESOLVED tensions của milestone hiện tại.
> Chỉ move RESOLVED tensions sang `.context/TENSIONS_HISTORY.md` khi human tuyên bố chuyển version/milestone mới.
> Mỗi OPEN tension phải có Decision trước khi task liên quan được thực hiện.

---

## [2025-01-01 00:00] | product-grid / product-list
Tension:    V1 nên dùng WooCommerce native blocks hay custom blocks ngay cho product grid/list?
Context:    Planning phase — chưa bắt đầu implement product section
Proposal:   Tạo custom `skvn-marine/product-grid` và `skvn-marine/product-list` block ngay từ đầu
Constraint: Master context khuyến nghị "Start with WooCommerce-native blocks/patterns where possible. Custom product grid/list blocks can be added after the homepage is working."
Severity:   high
Decision:   RESOLVED 2026-05-18 — V1 dùng WooCommerce native blocks/patterns cho product grid/list. Custom Product Grid/List hoặc style blocks liên quan để V2.

---

## [2025-01-01 00:00] | quote-flow
Tension:    CF7 ↔ n8n integration method: CF7 webhook trực tiếp hay CF7 send email → n8n catch email?
Context:    Planning phase — quote flow chưa implement
Proposal:   Dùng CF7 webhook (add_action 'wpcf7_mail_sent') để POST trực tiếp đến n8n webhook URL
Constraint: Chưa có quyết định chính thức. n8n webhook phải được protect (hard URL + optional secret).
Severity:   high
Decision:   OPEN

---

## [2025-01-01 00:00] | multilingual
Tension:    Polylang activate ngay V1 hay chỉ prepare (text domain + no hardcoded strings)?
Context:    Planning phase
Proposal:   Chỉ prepare: dùng text domain đúng, tránh hardcode Vietnamese strings trong theme/plugin UI
Constraint: Master context: "If the first site can launch in English only — prepare for multilingual, delay multilingual complexity until needed."
Severity:   low
Decision:   OPEN — lean toward: chỉ prepare, KHÔNG activate Polylang V1

---

## [2025-01-01 00:00] | slider
Tension:    Slider editor UX: stacked / selected-slide-preview / lightweight carousel?
Context:    Planning phase — slider block chưa implement
Proposal:   Stacked (slides xếp chồng trong editor, Swiper chỉ chạy frontend)
Constraint: "Not fully decided yet. V1 editor view should likely render slides stacked or in a simplified preview."
Severity:   low
Decision:   OPEN — lean toward: stacked preview

---

## [2025-01-01 00:00] | spam-protection
Tension:    Cloudflare Turnstile cho CF7: add ngay V1 hay delay?
Context:    Planning phase
Proposal:   V1: chỉ dùng CF7 honeypot. Turnstile thêm khi spam tăng.
Constraint: "Cloudflare Turnstile or reCAPTCHA if spam increases"
Severity:   low
Decision:   OPEN — lean toward: honeypot only V1, Turnstile on-demand
