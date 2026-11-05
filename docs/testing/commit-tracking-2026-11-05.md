# Commit tracking — 2026-11-05 backlog checkpoint

## Follow-up checkpoint — frame pitfalls and corrected asset extraction

User requested agent commits after recording the missing-frame failure. Current stays1.3.19; no implementation change or onsite approval in this checkpoint.

| Commit | Group | Status |
|---|---|---|
| `c02b4a9` | Agent pitfalls / CASE-008 | AP-06/AP-07, technical Pitfall8, case evidence, registry and context pointer. Records incomplete visual-owner replacement and evidence limits; no onsite PASS or automated asset guard claimed. |
| `69f52e6` | Existing generated snapshot after source import correction | Five external generated changes preserved. Shared frame rules now occur in registered `build/style-index.ts.css` and RTL counterpart; old standalone `index.ts.css`/RTL removed by the existing build output. Script asset version observed: `6363a1ecc7263f75117c`. |

The committing agent did not run build/PHP lint, edit generated output by hand, deploy or push. CSS timestamp observed before staging: 2026-11-05 07:58:11 UTC, not evidence of builder identity or successful full build. Marker/output-path inspection proves the local snapshot contains the frame rules in the registered file; it does **not** prove stylesheet delivery, DOM matching, cascade or appearance in the onsite editor/iframe. Continue EF/CP onsite checks. The earlier pre-correction snapshot notes below remain historical.

UTF-8 text checks and staged whitespace checks PASS. Source files unchanged in this commit task. This ledger commit follows the two hashes above.

## Follow-up checkpoint — Sidebar visual-controls planning

User requested plan054 and agent commits; no visual Sidebar implementation in this checkpoint. Q1 Category scope, Q2 Media layout owner/reset and Q3 group navigation remain open recommendations, not human approval. Current stays1.3.19.

| Commit | Group | Status |
|---|---|---|
| `c70c4ce` | Editor frame CSS extraction correction | Moved unchanged shared frame CSS under `editor-preview-frame/style.css` and updated import to join existing style extraction path. Reference/TS/CSS parse PASS; human rebuild and registered stylesheet inspection pending. |
| `05b9cef` | Existing frame generated snapshot | Six external build files preserved separately. JS includes frame, but frame rules extracted into new `index.ts.css` that bootstrap does not register; snapshot predates correction above. Not a deploy candidate before rebuild. Provenance in `editor-preview-frame-build-snapshot-2026-11-05.md`. |
| `3e0124e` | Visual Sidebar plan054 | SVG/CSS/palette/type control plan, capability inventory, Q1–Q3 decision memos and context index; no visual-control source implementation. |

Plan054/context commit can be found with `git log --oneline -- .context/planning/054_DESIGN_SIDEBAR_VISUAL_CONTROLS_PLAN.md`; final ledger commit follows it. Schema audit corrected Q2: Product mediaLayout has no schema default; unset/undefined is distinct from choosing explicit Flush, and existing empty strings are not normalized automatically. No build/PHP lint/deploy/push by agent.

## Follow-up checkpoint — canvas frames (same date)

Human approved implementation of plan053 P1/P2 and agent commits. New baseline was `b82e546`; the original backlog checkpoint below remains historical.

| Commit | Group | Status |
|---|---|---|
| `b4dba14` | Canvas presentation + editor preview frames | Product/Post Collection Grid/Carousel and Product Category Grid use editor-only type labels/palettes; Collection heading/intro now inside the outer frame, illustrative card and query overview retained. Shared bounded measured gap correction and cleanup hook, 8 VM tests, plan052/053, standard/runbooks. Source added; onsite pending. |
| `7ee509e` | Existing generated snapshot | Four external generated files preserved separately. Bundle/styles have prior overview but no `skvn-editor-preview-frame` marker, so they do not contain the new frame source. Human rebuild required before onsite/deploy. |

This tracking/index commit includes GLOBAL/MILESTONES/module status synchronization; find its hash with `git log --oneline -- docs/testing/commit-tracking-2026-11-05.md`.

Verification rerun: `node --disable-warning=ExperimentalWarning tools/tests/editor-preview-frame.test.mjs` **8/8 PASS** against the actual hook with simulated DOM geometry/lifecycle. Cases cover parent gap0/8/24, collapsed margins, gap changes, side-by-side children, previous removal/reorder targets and observer/listener/rAF cleanup. Three changed TS/TSX syntax parses, two CSS parses and staged whitespace checks PASS. This is not a full typecheck, plugin build, PHP lint, browser geometry or WordPress runtime PASS.

Asset metadata observed before staging: `build/index.ts.js` mtime `2026-11-05T06:49:00.546Z`, `build/style-index.ts.css` mtime `2026-11-05T06:50:44.583Z`; timestamps do not identify the builder or prove a successful/parity build. No generated output manually edited and no build/lint/deploy/push run by this agent.

Outstanding: run EF/CP onsite checks in `docs/testing/editor-preview-frames-1.3.19.md`; Category viewport breakpoint repair remains separate; Slider palette/mockup remains pending and Step Slider has no source registration in current inventory, with1.3.10 deferred→M5 requiring confirmed activation/version before coding. Source claims below saying canvas is unimplemented predate this checkpoint. Current milestone/version unchanged; no acceptance closed.

## Original backlog checkpoint

Human yêu cầu lưu hướng canvas đã duyệt và giao agent commit toàn bộ thay đổi đang tồn đọng, chia theo tính năng và docs. Baseline trước lượt này: `e13fa2c`. Current vẫn **V1 / 1.3.19 IN PROGRESS**; đây không phải milestone transition, release approval hoặc deployment.

## Feature-separated commits

| Commit | Nhóm | Nội dung / trạng thái |
|---|---|---|
| `38523bb` | Sidebar repair 051 | Editor context guard, Inspector fallback/reset chung, nonce recovery giữ draft/base/conflict, Export draft, lazy Theme Design; 12 VM tests và runbooks/handoff. Category fallback binding `clientId` được bổ sung sau static audit. Source có; onsite pending. |
| `3a5b2e0` | Category Grid UX/width | Desktop/Tablet/Mobile number inputs cùng hàng; native alignment mặc định Wide, Normal/Wide/Full choice; source schema/types/CSS và runbook + review regression tests. Build/geometry/save-reload evidence pending. |
| `42480ac` | Product Carousel canvas design | Plan052 lưu tranh text đã duyệt: card trái, Content summary + Query preview phải; giữ states/info, stack trên canvas hẹp. **Docs-only, chưa implement.** |
| `b1e77fe` | Roadmap/carry-over | Sidebar1.3.19 → Category1.3.20 → Hero1.3.21, acceptance carry-over, related planning/research/ideation. Full MILESTONES diff giữ GP follow-up register cùng file; không mất checklist. |
| `47b40e7` | GP migration follow-up | GP-LAYOUT-01/02 trong plan013, runbook và hai PNG references. Deferred →2.0.0, standalone gate3.0.0; không khởi động migration/DB writes. |
| `0c2066d` | Generated snapshot | Lưu riêng 17 file build sẵn có; **không phải rebuild được agent chạy hoặc chứng nhận source parity**. Chi tiết ở dưới. |

Final docs/index commit chứa file tracking này và GLOBAL index cleanup, xem `git log --oneline -- docs/testing/commit-tracking-2026-11-05.md`. Tất cả nhóm được commit local; không push/deploy.

## Kiểm tra và giới hạn

- `node --disable-warning=ExperimentalWarning tools/tests/design-sidebar-session.test.mjs`: **12/12 PASS** trong lượt commit. VM test kiểm transport/hook/context; không React DOM/WordPress integration.
- Changed TS/TSX parsed bằng TypeScript `createSourceFile`: không syntax diagnostics. Đây **không phải full typecheck hoặc plugin build**. Source block JSON parse PASS; scoped Git whitespace check PASS (CRLF normalization warnings không phải failure).
- Hai ảnh GP được mở kiểm tra: editor settings/Contained và frontend reference tương ứng runbook; không credential/session token. Chúng không chứng minh geometry PASS.
- Agent không chạy build/PHP lint/packaging/onsite. Không sửa compiled output bằng tay. Không đổi version, archive milestone hoặc tự tick acceptance PASS.

## Generated asset provenance / rebuild gate

`build/index.ts.js` đã tồn tại trước lượt commit. Timestamp filesystem quan sát: `2026-11-05T05:34:25.701Z`; timestamp chỉ là metadata, không chứng minh ai build hoặc build thành công từ source nào. Kiểm text thấy các marker `postEditor`, `expired_session`, `Refresh session`, `Content width`, `column-inputs`; không còn nhãn cũ `Columns — wide container`. Snapshot chứa phần Category number/width và Sidebar session source gần đây, nhưng vẫn có `DesignShortcut` dùng identifier `clientId` không được bind trong hàm Category Edit. Source đã sửa destructuring tại `38523bb`; snapshot vì vậy **known inconsistent, không dùng làm deploy candidate trước rebuild**. Không suy toàn bộ parity từ vài marker hoặc `.asset.php` hash.

Human chạy từ môi trường repo đã cấu hình, cung cấp output và rebuild assets trước onsite/deploy:

```bash
php -l wp-content/themes/skvn-marine/functions.php
php -l wp-content/themes/skvn-marine/inc/customizer.php
source /home/shinkuro/.nvm/nvm.sh && nvm use 20 && cd /mnt/d/Github/skvn-marine/wp-content/plugins/skvn-marine-blocks && npm run build && find modules/ -name "*.php" -exec php -l {} \;
```

Không có runtime require/include mới trong repair này; snapshot build không phải artifact packaging audit. Nếu chuẩn bị deploy boundary, vẫn áp dụng deploy workflow và kiểm module runtime đầy đủ.

## Outstanding follow-up

1. **Category responsive behavior:** labels Desktop/Tablet/Mobile đã đổi, nhưng source CSS vẫn container-based 480/880px. Hướng chọn theo màn hình được human yêu cầu, implementation chưa làm; không gọi label change là fix breakpoint. Cần giữ hẹp Group/Columns an toàn và kiểm editor preview đúng mode.
2. **Category width:** Wide default đã có source, cần human build + onsite `/home/`, Normal/Wide/Full save/reopen, old blocks không alignment, parent hẹp và scrollbar geometry theo runbook.
3. **Carousel canvas:** plan052 đã duyệt và lưu; chưa code bố cục mới. Giữ card mẫu, summary, query count/results/states/disclosure và limitation note; không mở endpoint/query/persistence mới.
4. **Sidebar051:** SR2 runbook/DS/TD cần onsite evidence; lock/persistent connection/hook exit, permission/session, fallback/reset/Undo/reopen/export keyboard vẫn pending ngoài VM coverage.
5. **Category review:** R01–R12 runbook chỉ chuẩn bị test; không coi findings đã sửa hoặc nghiệm thu. Các acceptance Carousel cũ vẫn carry-over, không bị Sidebar hoặc thiết kế mới thay thế.
6. **GP layout:** deferred baseline/migration compatibility audit khi2.0.0 bắt đầu; không yêu cầu sửa từng page để migrate, không rewrite DB trong lượt này.

Không còn file pending trong phạm vi yêu cầu sau final commit; `git status --short` là kiểm tra cuối, không phải bằng chứng runtime/release PASS.
