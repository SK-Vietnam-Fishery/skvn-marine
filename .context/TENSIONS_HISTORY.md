# Tensions — History

> Chỉ chứa `Status: ARCHIVED` entries.
> KHÔNG load mặc định — chỉ đọc khi human yêu cầu audit hoặc task cần quyết định từ milestone cũ.
> Chỉ move entries vào đây khi human approve milestone transition.

---

## [2025-01-01 00:00] | product-grid / product-list
Tension:    V1 nên dùng WooCommerce native blocks hay custom blocks ngay cho product grid/list?
Context:    Planning phase — chưa bắt đầu implement product section
Proposal:   Tạo custom `skvn-marine/product-grid` và `skvn-marine/product-list` block ngay từ đầu
Constraint: Master context khuyến nghị "Start with WooCommerce-native blocks/patterns where possible. Custom product grid/list blocks can be added after the homepage is working."
Severity:   high
Tags:       woocommerce, blocks
Milestone:  V1 / 0.4.0
Status:     ARCHIVED
Resolved:   2026-05-18
Decision:   V1 dùng WooCommerce native blocks/patterns cho product grid/list. Custom Product Grid/List hoặc style blocks liên quan để V2.

---

## [2026-05-26] | quote-flow
Tension:    CF7 ↔ n8n integration method: CF7 webhook trực tiếp hay CF7 send email → n8n catch email?
Context:    Human changed scope again: 0.5.1 now focuses on page display/sidebar controls; quote UI moves to 0.6.0; CF7 moves after 0.6.0; n8n moves after version 1.0.0.
Proposal:   Do not implement CF7, CFDB7 workflow, or n8n automation in 0.5.1.
Constraint: n8n webhook must remain protected and must not be exposed; no custom-code quote form handler.
Severity:   high
Tags:       quote-flow, php, milestone
Milestone:  V1 / 0.5.1
Status:     ARCHIVED
Resolved:   2026-05-26
Decision:   Current 0.5.1 scope is page display/sidebar controls. Move Quote UI/editor controls to 0.6.0. Defer CF7/CFDB7 until after 0.6.0; defer n8n automation until after version 1.0.0.

---

## [2026-05-26] | spam-protection
Tension:    Cloudflare Turnstile cho CF7: add ngay V1 hay delay?
Context:    CF7 implementation moved out of 0.5.1.
Proposal:   Do not add Turnstile or CF7 spam configuration in 0.5.1.
Constraint: CF7 form should use dedicated protection when CF7 returns to scope.
Severity:   low
Tags:       quote-flow, spam-protection
Milestone:  V1 / 0.5.1
Status:     ARCHIVED
Resolved:   2026-05-26
Decision:   Delay CF7 spam-layer decision until CF7 returns to scope. No Turnstile dependency in 0.5.1.
