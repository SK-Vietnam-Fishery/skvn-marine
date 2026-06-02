# MILESTONES.md — SKVN Marine

> Source of truth cho milestone hiện tại và checklist chuyển mốc.
> File này phải được đọc khi bắt đầu task.
> Chỉ human mới có quyền xác nhận chuyển milestone/version.

---

## Current Milestone

Current: **V1 / 0.7.0 — Basic CF7/CFDB7 Quote Form**
Status: **IN_PROGRESS**
Started: **2026-06-02**

AGENTS.md current milestone phải match file này.

---

## Transition Rule

Chỉ chuyển milestone khi:

- Tất cả acceptance checklist của milestone hiện tại đã DONE.
- Runtime smoke test liên quan đã chạy.
- Human explicitly approve chuyển milestone.

Khi chuyển milestone:

1. Update `AGENTS.md` current milestone.
2. Update `.context/MILESTONES.md` current milestone.
3. Move completed milestone checklist/notes sang `.context/MILESTONES_HISTORY.md`.
4. Move `RESOLVED_ACTIVE` tensions của milestone cũ từ `.context/TENSIONS_ACTIVE.md` sang `.context/TENSIONS_HISTORY.md`, đổi `Status: ARCHIVED`.
5. Giữ lại OPEN tensions còn liên quan trong `.context/TENSIONS_OPEN.md`.
6. Không tự archive hoặc tự chuyển milestone nếu human chưa approve.

## Version Naming Rule

- Version dùng SemVer: `MAJOR.MINOR.PATCH`.
- `MAJOR` tăng khi đổi phase lớn hoặc đổi kiến trúc/phạm vi sản phẩm lớn, ví dụ `1.x.x` → `2.0.0`.
- `MINOR` tăng khi thêm feature/scope mới nhưng vẫn cùng major, ví dụ `1.0.0` → `1.1.0`.
- `PATCH` tăng khi fix, hardening, hoặc integration nhỏ trong cùng minor, ví dụ `0.5.0` → `0.5.1`.
- Version launch-ready của một major là `MAJOR.0.0`, ví dụ `1.0.0` là V1 launch-ready, `2.0.0` là V2 launch-ready.
- Không dùng nhãn kiểu `1.0.0 Prep` cho feature mới. Nếu là prep trước launch, nó phải nằm trong milestone trước launch hoặc ghi `Future Candidate`.
- Nếu chưa chắc version của future work, ghi `Future Candidate` thay vì tự gán version.
- Planning filename phải khớp target version chính, ví dụ `001_VERSION_1_1_0_<TOPIC>_PLANNING.md` hoặc `002_VERSION_2_0_0_<TOPIC>_PLANNING.md`.
- Không đổi current milestone/version nếu chưa có human approve rõ ràng.

---

## V1 Checkpoints

### 0.7.0 — Basic CF7/CFDB7 Quote Form

Status: **IN_PROGRESS**
Started: **2026-06-02**

Acceptance:

- [ ] CF7 form exists for Request a Quote
- [ ] CF7 markup uses project classes: `skvn-form`, `skvn-quote-form`, `skvn-button`, `skvn-button--primary`
- [ ] Required visible fields prepared
- [ ] Required hidden fields prepared: `product_id`, `product_sku`, `product_name`, `product_url`, `source_url`, UTM fields
- [ ] CFDB7 stores quote submission
- [ ] Thank-you page exists
- [ ] n8n remains deferred/unexposed
- [ ] Runtime quote form smoke test passed
- [ ] Human approves milestone completion

Deferred test debt:

- [ ] Onsite hidden/context field and full UX smoke test is intentionally deferred to V1 / 0.10.0 because human is working under time pressure.
- [ ] See `docs/testing/onsite-quote-flow-0.7.0.md`.

### 0.10.0 — Onsite Quote Flow Test Debt Resolution

Status: **PENDING**

Acceptance:

- [ ] Human runs `docs/testing/onsite-quote-flow-0.7.0.md` on the onsite site
- [ ] Product CTA/query params confirmed from onsite product/product-card flow
- [ ] CF7 hidden/context fields confirmed in submitted data
- [ ] CFDB7 row confirms visible and hidden fields are stored
- [ ] Thank-you/success UX confirmed
- [ ] Desktop/mobile screenshots reviewed
- [ ] Console/log issues recorded or confirmed clean
- [ ] Human approves closing quote-flow onsite test debt

### 1.0.0 — V1 Launch Ready

Status: **PENDING**

Acceptance:

- [ ] Accessibility pass
- [ ] Mobile QA pass
- [ ] SEO/GEO structure pass
- [ ] Performance and asset loading review
- [ ] No forbidden parent-theme changes
- [ ] No external plugins committed to source repo
- [ ] n8n remains deferred/unexposed unless human explicitly moves it into scope
- [ ] Human approves V1 launch readiness
