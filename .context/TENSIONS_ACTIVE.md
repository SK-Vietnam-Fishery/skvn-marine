# Tensions — Active

> Chỉ chứa `Status: RESOLVED_ACTIVE` entries của milestone hiện tại.
> Agent đọc file này mỗi task, nhưng dùng tag filter để tập trung vào entries liên quan.
> Move sang `TENSIONS_HISTORY.md` chỉ khi human approve milestone transition.

---

## [2026-06-02] | quote-flow
Tension:    n8n automation timing during basic CF7/CFDB7 quote form work.
Context:    Human approved moving from tested 0.6.0 to 0.7.0.
Proposal:   Implement only the basic same-site CF7/CFDB7 quote form in 0.7.0.
Constraint: n8n webhook must remain protected and must not be exposed; no custom-code quote form handler.
Severity:   high
Tags:       quote-flow, php, milestone
Milestone:  V1 / 0.7.0
Status:     RESOLVED_ACTIVE
Resolved:   2026-06-02
Decision:   0.7.0 scope is basic CF7/CFDB7 quote form. n8n automation remains deferred until after version 1.0.0.
