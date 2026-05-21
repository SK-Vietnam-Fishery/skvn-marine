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
