# Prompt review — Sidebar1.3.19 + repair051

## Target hiện hành — repair051 working tree

Human approved G0–G4 ngày2026-11-05. Review **diff so vớie13fa2c**, chưa commit repair051. Scope6source files: `src/design-sidebar/{index,navigation,theme-design}.tsx`, `src/collection/controls.tsx`, `src/product-category-grid/edit.tsx`, `modules/typography-settings/typography-settings.php`; cùng plan051, runbook và `tools/tests/design-sidebar-session.test.mjs`. Những context/build dirty khác không thuộc target.

```text
Repo D:\Github\skvn-marine. Review read-only repair051 của V1/1.3.19.
Đọc AGENTS/startup context, plan048/050/051 và runbook Sidebar1.3.19.
Target: working-tree source diff so với e13fa2c, chỉ6files ghi trên.
Không sửa file/build/lint/stage/commit/deploy. Có thể chạy direct Node test
tools/tests/design-sidebar-session.test.mjs; đây là simulated hooks/fetch,
không chứng minh React DOM, PHP/DB hoặc onsitePASS.
Kiểm: guard trước edit-post API/hooks; shell vs Inspector fallback đúng một
surface, reset/Undo/attrs/Category integration; nonce recovery authenticated
read-only/no nopriv/no settingswrite;403/-1/HTML handling; refresh không thay
draft/base/conflict hoặc autoSave; export không chứa credential; lazy read,
retry/abort/unmount/close-reopen và beforeunload listener.
Không lặp finding “admin-ajax không fire admin_init”; core có fire.
Không coi persistent=mysqli tất yếu leak vô hạn: xem cleanup thực tế.
Ghi finding có source/repro/severity/đề xuất; tách risk/evidence gap khỏi defect.
Onsite version/template-mode/fallback keyboard/render/DB vẫn chờ kiểm chứng.
```

## Handoff lịch sử — repair050 commit e13fa2c

Ngày: 2026-11-05. Source review độc lập sau repair, chưa runtime approval.
Commit target: `feat(blocks): implement 1.3.19 design sidebar and repair review findings`.
Lấy hash: `git log -1 --format="%H %s" -- docs/testing/plugin-design-sidebar-1.3.19-review-handoff.md` và đối chiếu subject trên. Parent baseline: `7ef45ff` (Category1.3.20). Repair commit chứa cả foundation source1.3.19 trước đó chưa commit và F1–F4; không có rebuilt assets. Current1.3.19 IN PROGRESS, chưa release/runtime approval.

Copy prompt dưới cho agent khác:

```text
Repo D:\Github\skvn-marine. Review source V1/1.3.19 — Plugin Design Sidebar
Foundation và repair S19-01–04 theo plan050, không triển khai thay đổi mới.

Đọc AGENTS.md và startup context đúng protocol. Đọc:
- .context/planning/048_VER_1_3_19_PLUGIN_DESIGN_SIDEBAR_EXECUTION_PLAN.md
- .context/planning/050_VER_1_3_19_REVIEW_REPAIR_PLAN.md
- docs/testing/plugin-design-sidebar-1.3.19.md
- docs/testing/plugin-design-sidebar-1.3.19-inventory.md
- docs/workflows/ideation-plugin-design-sidebar.md
- docs/decisions/typography-scope-and-font-loading.md

Target commit/range ở đầu file handoff này. Kiểm git show/status để phân biệt
source đã commit với dirty build/unrelated work. Category1.3.20 commit7ef45ff
và các findings R01–R12 là scope riêng; Category additive adapter trong shell
chỉ review integration boundary, không gộp review toàn bộ Category.

Human đã approve full repair050, không hỏi lại hướng:
S19-01: Post actionHoverTextColor phải đăng ký schema/persist; old/new/reset
không mất query/URL/legacy attrs.
S19-02: palette/heading cùng option owner không lost update khi overlap;
samegroup stale phải conflict. Fonts preset/scope cập nhật cùng logical owner,
giữ unrelated theme_mods. Review actual atomicity, absent option, bounded retry,
SQL parameterization nếu có, sanitize/filter/action semantics, cache/alloptions,
read-after-write. Không gọi hash check rồi update_option là CAS an toàn.
Repair hiện chọn equivalent owner-scoped DB advisory lock (GET_LOCK timeout0),
refresh cache/raw read dưới lock rồi native update_option, Fonts pair một write;
fail-closed busy503 nếu lock unavailable/occupied. Không JSON/exit trong critical
section; finally phải release mọi return/error. Scope chỉ các Sidebar writers
tham gia khóa; Typography admin/Customizer/third-party writers chưa cùng protocol
là residual risk, không claim chống tất cả writers. Kiểm behavior thật của DB
proxy/connection/cache/hook; current WP/API evidence và onsite lock support còn gate.
S19-03: structured409 giữ draft và có Review current / Use current / Keep draft;
rebase không auto-save, không thay draft nhóm khác; lần stale mới vẫn conflict.
S19-04: query expand→resize→collapse không mất focus khi button bị unmount;
không steal focus từ input/Inspector khác.

Giữ contracts048:
- Block overrides theo Page Save/Undo; Theme Design Save riêng theo nhóm.
- No global draft live preview; nonce/server caps/exact child-theme gate.
- Font registry/theme_mod cũ, không loader/settings store thứ hai.
- Plugin fallback không phụ thuộc WindPress/theme.
- Không sửa skeleton, Post visual/media ranges chưa được approve vẫn gates riêng.
- Chưa có build/lint/onsite PASS cho repair nếu evidence ledger chưa ghi.

CHỈ đọc/review: không code, stage, commit, reset/stash, build/lint, deploy hoặc
thao tác production. Không đụng GeneratePress/build/node_modules. Không suy
source đúng = runtime PASS. Nếu cần xác minh API ngoài, dùng primary docs/source
đúng version; ghi rõ runtime WP version còn thiếu nếu chưa được cung cấp.

Trả findings thực theo bảng:
ID | Lớp | blocker/major/minor/nit | absolute file:line | Trigger/vấn đề |
contract bị vi phạm | đề xuất | cách kiểm chứng.
Phân biệt source defect, risk và thiếu runtime evidence. Sau bảng ghi top3
risks, evidence còn thiếu và human decisions thật sự cần. Nếu không còn defect
trong phạm vi, nói rõ vẫn còn các runtime gates, đừng tạo nit để đủ bảng.
```
