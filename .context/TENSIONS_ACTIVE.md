# Tensions — Active

> Chỉ chứa `Status: RESOLVED_ACTIVE` entries của milestone hiện tại.
> Agent đọc file này mỗi task, nhưng dùng tag filter để tập trung vào entries liên quan.
> Move sang `TENSIONS_HISTORY.md` chỉ khi human approve milestone transition.

---

## [2026-06-08 12:15] | blocks
Tension:    Add Feature Showcase block during 1.2.1 even though milestone scope names exactly three Slider presets.
Context:    Human approved naming artifact-inspired expanding accordion/gallery as "Feature Showcase" and wants the mobile split state used intentionally.
Proposal:   Implement a conservative `skvn-marine/feature-showcase` block that reuses plugin-owned CSS, no Tailwind CDN, no new dependency, and leaves existing Accordion unchanged.
Constraint: 1.2.1 planning says "Implement exactly three inserter-facing presets in 1.2.1" and non-scope excludes extra slider experiences.
Severity:   low
Tags:       blocks, slider, editor-governance
Milestone:  V1 / 1.2.1
Status:     RESOLVED_ACTIVE
Resolved:   2026-06-08
Decision:   Park Feature Showcase source for V1 / 1.2.3. During 1.2.1, keep the source unregistered, keep it out of the inserter, and document the future milestone before activation.
