# Plugin Design Sidebar — 1.3.19 onsite runbook

Started 2026-11-04 by human instruction. **Chờ kiểm chứng**; source/build is not runtime PASS.
Plan: `.context/planning/048_VER_1_3_19_PLUGIN_DESIGN_SIDEBAR_EXECUTION_PLAN.md`.

## Environment and baseline

Baseline commit: `edae8f3813b0200ad39b686c1bc1d4a3e43435be`; dirty docs and loading/navigation build files predate this task. Do not reset or deploy those files implicitly. Development metadata 1.3.19 is not release approval. Local checkout has no WP core/node_modules; onsite WP/Gutenberg version, deployed artifact, editor/frontend URLs **must be filled before testing**.

- Product fixture: “Test product carousel”; editor URL: **TBD**; frontend URL: **TBD**.
- Post fixture: editor/frontend URL **TBD**. Authorized test environment: **TBD**.
- Record browser/OS, WP/Gutenberg/Woo versions, theme/plugin/build/commit and dirty diff, viewport/container.
- Keep old serialized blocks, add two differently styled blocks, Product/Post, Grid/Carousel, long titles, missing/portrait/landscape/transparent images, 0/1/many results, inherited and explicit styles.
- First run deferred Carousel cases in [1.3.18 runbook](carousel-editor-ux-technical-1.3.18.md). Preserve E02 PASS unless affected layout changes. C/D/arrows/Post need separate evidence; do not fix skeleton or infer Technical PASS.
- Theme/Woo changes, denied roles and failure injection only on an explicitly authorized test environment, never production by assumption.

## Sidebar cases

| ID | Exact steps | Expected / pass criteria | Evidence |
|---|---|---|---|
| DS-01 | Select block A, Open design settings; Edit button style; select B | Header/type/heading and Action target B; keyboard focus lands in labeled group | Video + serialized attrs A/B |
| DS-02 | Plugin tab → select B; close/reopen; no selection/unsupported/multi-selection; delete/undo/duplicate | Tab stays Plugin on selection; explicit empty states; no stale clientId or bulk edit | Video, console |
| DS-03 | Keyboard: change query, CTA colors, Floating image; Back to block settings; narrow sidebar | Inspector owns query/basic layout; design owns Image/Title/Details/Action once; visible focus and usable labels | Steps/scroll count + narrow screenshots |
| DS-04 | A/B override/reset, undo/redo, duplicate, Page Save, reopen | Only target attrs change; reset group preserves query/URLs/layout; Page Save persists attrs | Attr diff and reopened screenshots |
| DS-05 / TD-02 | Draft brand/heading/fonts, switch tab, close/reopen, Page Save, fail global save, Discard one group, save success | Draft survives within editor; Page Save does not save global; errors preserve draft; only chosen group saves/discards; reload notice truthful | Network response, saved option/theme_mod, video |
| DS-06 / TD-03 | Old/new Product/Post/Grid; max typography, long text, missing image, mobile, focus/hover | Output preserved; local overrides independent; global tokens only where inherited; admin chrome unchanged | Paired editor/frontend and computed styles/rect |
| DS-07 | Baseline network → 10 style edits/tab switches → 10 selection/mount/remove cycles | No query refetch from style/tab; no extra carousel mount/listener accumulation or console errors | Network counts and lifecycle trace |
| TD-01 | SKVN child compatible/incompatible, GP parent, other theme; switch away/back; roles | Exact child + integration contract server-gated; denied requests cannot write; saved data retained; block controls work independently | Theme IDs/role, denied response + options |
| TD-04 | Every existing font preset/scope → save/reopen; simulate font network failure | Same theme registry/theme_mod/delivery; no new font loader; fallback readable | theme_mod, computed font, requests |
| TD-CONFLICT | Two editor sessions load group; save first then second stale draft | Second save returns conflict and keeps draft; no silent overwrite; reload current settings before retry | Responses/current settings |

## Carry register

All UX-01–13 (including UX-02a/sub-IDs), UI-18-TITLE/STYLE, TECH-01–13 (including TECH-08a/12/sub-IDs), E01–E15/E12a/E13a, G-MEDIA, C, D-01–08, arrows and Post visual remain in the linked original runbooks. E07/E10 historical gaps require re-test because hover/chip source now exists. DS cases supplement these IDs. Skeleton D is test/debt only. Loading/navigation046 remains 1.3.18 with separate UXHF/LDR evidence.

## Human commands and report

Agent checks 2026-11-04: all16 source/build block JSON parse with version1.3.19; relative imports resolve and no replacement characters in new editor files; source diff whitespace check clean with existing CRLF respected. Layout audit finds only pre-existing carousel overflow rules, no new viewport units/overflow mask. These checks do not compile TypeScript, lint PHP, or validate deployed Gutenberg. No local PHP/node_modules/WP runtime is available. UI artifact is source-only until reviewed/rendered.

Repair2026-11-05 adds an owner advisory lock around Sidebar saves plus optimistic group revision, native Options API and locked raw/effective readback. It is not CAS or an atomic transaction across owners/nonparticipating writers. GET_LOCK timeout0 fails closed with structured busy503 and keeps the draft; retry is explicit/manual. No automatic retryloop or unconditional fallback. Native Typography admin/Customizer/third-party writers do not use this lock and remain a documented interaction risk; do not claim allwriters protected.

## Repair cases — 2026-11-05, all Chờ kiểm chứng

Record WP/PHP/DB vendor+version, object-cache enabled/disabled and DB connection/routing topology with the existing URL/build fixture fields. Use authorized staging only for deterministic barriers, missing rows, failure injection, role/theme changes and hooks. Capture full request/response, raw owner before/after, cache/get_option readback and timing; clicking two buttons nearly together is insufficient concurrency evidence.

| ID | Exact steps | Expected / pass criteria |
|---|---|---|
| E07-POST-PERSIST | Two Post blocks choose different Action hover text; Page Save/reopen; hover/focus; reset one; old Post missing key; Product control | Serialized Post key persists, per-block colors apply on frontend hover/focus; old missing inherits, reset preserves URLs/query and hidden background attrs |
| TD-OVERLAP-DIFF | Two Sidebar sessions samebase, A palette/Bheading. Staging barrier in a test harness around native pre_update_option hook holds A after lock/read. Send B while A held; releaseA; manually retryB | B either busy503 before writing, draftkept, then retry merges latest othergroup; or serial success. Both saved groups survive; no lostupdate. Response503 never implies Saved |
| TD-OVERLAP-SAME | Samegroup different drafts/base; barrier holdsA. Bwhileheld then retryafterA | First commits; Bbusy503 then409 currentgroup (or direct409 if afterrelease). No second Saved with stale revision; no write until explicit review/rebase+Save |
| TD-FONTS-OWNER | Fontspreset/scope pair overlap; seed unrelated theme_mod; repeat missing modernowner/legacymods fixture, cacheon/off | One pair per logicalSidebar save, unrelated mods retained, native absent/migration policy preserved. Physical and get_theme_mod readback agree; font delivery unchanged |
| TD-CONFLICT-RECOVER | A saves; B409; compare Current/Draft→Keep→Save; repeat Usecurrent; change serveragain afterKeep; fail read/write; revokepermission thenrefresh | Conflictcode/current payload; Keep retainsdraft and updates onlygroupbase without autosave; Usecurrent clean onlygroup; nextstale409; failures keepdraft; revokedwrite locked, refreshaccess preservesdraft/base |
| UX10-RESIZE-FOCUS |8Product results narrow→Showmore→resizewide where8fit→Showless; repeat keyboard/pointer; keep Inspector input focused duringresize | Active disappearing disclosure focus moves to labeled queryheading; nextTab sensible; unrelatedinput focus stays. Resize alone keeps expanded |
| TD-LOCK-NATIVE | Force lockbusy/unavailable/reconnect and native optionfilter/read/write failures; instrument sanitizer/pre_update/update/updated and font pre_set hooks | Lockfailure503 no write; lock released before JSON; native hooks and storage semantics retained; filtered/nonpersisted desired values not falsely Saved; no recursion/unboundedretry |
| TD-EXTERNAL-WRITER | Audit/run authorized overlapping native Typographyadmin and Customizerfont writes vsSidebar | Record actual interaction and unresolved limitation separately; these writers do not share the lock. Do not mark global allwriter atomicity PASS |

No test-only barrier hook is shipped in production source. Harness may attach to native `pre_update_option_<owner>` while holding an external test-process barrier, then release; scope it to the test request and remove after verification. Two independent PHP requests/connections are required for overlap. Build/lint and actual concurrency evidence remain human-run gates.

```bash
php -l wp-content/themes/skvn-marine/functions.php
php -l wp-content/themes/skvn-marine/inc/customizer.php
source /home/shinkuro/.nvm/nvm.sh && nvm use 20 && cd /mnt/d/Github/skvn-marine/wp-content/plugins/skvn-marine-blocks && npm run build && find modules/ -name "*.php" -exec php -l {} \;
```

No new PHP runtime include is planned. If include paths change, run deploy artifact/zip audit per workflow before upload. Do not deploy implicitly. Send complete build/lint output and per-case actual/status/evidence, URLs/build/browser/fixture. Agent continues with concrete failures and acceptance ledger; completion requires human onsite approval.

## Second review repair — plan051, 2026-11-05

Human approved G0–G4. Source baselinee13fa2c + working-tree repair; **Chờ kiểm chứng**. Record target editor/frontend URLs, authorized environment, WP/Gutenberg/PHP/DB/driver/cache and deployed build before tests. Agent VM tests exercise actual TS transport/hook with simulated hooks/fetch, not React DOM/PHP/DB/Gutenberg integration. Run with Node>=22.13: `node tools/tests/design-sidebar-session.test.mjs`.

Ledger clarification: “all16” above is the report recorded2026-11-04, not a claim for the current artifact. Current source has8blockJSON at1.3.19 and Category at1.3.20; compiled assets remain outside this repair, require human rebuild/verification. This is development metadata, not release approval.

| ID | Setup / exact steps | Expected / PASS criteria / evidence |
|---|---|---|
| SR2-EDITOR | Same authorized fixture in post/page editor, template editing inside post editor, and Site Editor with a compatible block theme. Open supported/unsupported/multi-selected blocks; change design, reset, Undo/Redo, Page Save/reopen; try shortcut/Back | Post context uses shell once; outside context Inspector fallback exposes existing component controls/reset. No dead shortcut or edit-post error; only target attrs change. Capture screen/context/version, console, video and attrs. Site Editor availability on another theme is a separate fixture, not an assumed GP feature |
| SR2-NONCE | Draft palette and fonts; set window.skvnMarineDesign.nonce to invalid; Save palette; Refresh session; inspect requests; click Save manually |403 expired_session keeps drafts/base; token endpoint has no settings write; read updates access only; no autoSave. Manual Save persists or conflicts; raw options + requests/video |
| SR2-SESSION-STALE | Two sessions same group/base; B saves; A invalid nonce→Save→Refresh session→Save | A keeps old revision through refresh and gets409; Keep/Use current remains explicit; unrelated draft survives. Capture payload/current/raw owner |
| SR2-AUTH | Authorized staging: logout/login or remove capability; inject403HTML,body-1/0/null and failed network | Only structured permission response revokes access. Session/transport error keeps draft, Refresh session is manual, no retryloop/write. Login is human-controlled; capture actual status/body/UI |
| SR2-EXPORT | Dirty2groups; simulate failed recovery; Export draft; Tab into textarea/copy; cancel page departure then reload only after copying | JSON contains only dirty group values/schemaVersion, no nonce/auth material; read-only textarea works without clipboard permission. Warning before leaving remains; no autoimport promise. Video/copied JSON |
| SR2-LAZY | Fresh load; Selected tab first; then open Plugin; close/reopen/switch10times. Slow first read and close/reopen; fail initial read→Retry; unmount while pending |0ThemeDesign requests before first activePlugin;1successful initial read per model mount, no refetch on reopen or extra listeners; draft preserved; explicit retry and abort/live cleanup. Network counts/console |
| SR2-LOCK-EXIT | Staging only, instrument native update hooks: throw exception separately from exit/wp_die; record nonpersistent/persistent driver settings and object cache. Inspect lock owner immediately and after subsequent request/reuse | Exception must pass finally; exit skips finally. Record release/recovery timing rather than assume persistent=leak. mysqli normally cleans GET_LOCK on reuse; custom cleanup-disabled builds/pools/topology need separate evidence. If lock remains stuck, report as new observed failure before changing protocol |
| SR2-REGRESSION | Repeat TD-CONFLICT-RECOVER, TD permission/theme cases, E07-POST-PERSIST, UX10-RESIZE-FOCUS, Category design save/reset | Preserve original acceptance; no Post background control added, no Category term/query/persistence changes. Report each ID separately, not a blanketPASS |

Send per-case expected/actual, environment/build, screenshots/video, network payload and raw saved values as applicable. Static tests do not close these onsite cases.
