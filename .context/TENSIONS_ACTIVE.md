# Tensions — Active

> Chỉ chứa `Status: RESOLVED_ACTIVE` entries của milestone hiện tại.
> Agent đọc file này mỗi task, nhưng dùng tag filter để tập trung vào entries liên quan.
> Move sang `TENSIONS_HISTORY.md` chỉ khi human approve milestone transition.

---

## [2026-05-26] | quote-flow
Tension:    CF7 ↔ n8n integration method: CF7 webhook trực tiếp hay CF7 send email → n8n catch email?
Context:    Human changed scope: 0.5.1 focuses on quote UI/editor controls; CF7 moves to the next milestone after 0.5.1; n8n moves after version 1.0.0.
Proposal:   Do not implement CF7, CFDB7 workflow, or n8n automation in 0.5.1.
Constraint: n8n webhook must remain protected and must not be exposed; no custom-code quote form handler.
Severity:   high
Tags:       quote-flow, php, milestone
Milestone:  V1 / 0.5.1
Status:     RESOLVED_ACTIVE
Resolved:   2026-05-26
Decision:   Defer CF7/CFDB7 to the next milestone after 0.5.1; defer n8n automation until after version 1.0.0. Current milestone is quote UI and sidebar/editor controls only.

---

## [2026-05-26] | spam-protection
Tension:    Cloudflare Turnstile cho CF7: add ngay V1 hay delay?
Context:    CF7 implementation moved out of 0.5.1.
Proposal:   Do not add Turnstile or CF7 spam configuration in 0.5.1.
Constraint: CF7 form should use dedicated protection when CF7 returns to scope.
Severity:   low
Tags:       quote-flow, spam-protection
Milestone:  V1 / 0.5.1
Status:     RESOLVED_ACTIVE
Resolved:   2026-05-26
Decision:   Delay CF7 spam-layer decision until CF7 returns to scope. No Turnstile dependency in 0.5.1.
