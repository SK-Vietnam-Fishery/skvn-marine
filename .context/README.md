# Context Map Layout — SKVN Marine

> Start here when the `.context/` folder feels noisy.
> This file explains what stays flat and what is grouped.

---

## Root Files

These files stay flat because they are loaded frequently or used as governance entrypoints:

- `GLOBAL.md` — stack, invariants, module index
- `PROJECT.md` — project-wide decisions
- `MILESTONES.md` — current milestone source of truth
- `MILESTONES_HISTORY.md` — completed milestone archive
- `TENSIONS_OPEN.md` — unresolved tensions
- `TENSIONS_ACTIVE.md` — resolved decisions still active in the current milestone
- `TENSIONS_HISTORY.md` — archived tensions

Do not move milestone or tension files unless the governance protocol and context tooling are updated together.

---

## Modules

Module-specific context lives in:

```text
.context/modules/
```

Current files:

- `THEME_SKVN_MARINE.md`
- `PLUGIN_SKVN_MARINE_BLOCKS.md`
- `QUOTE_FLOW.md`

Use these when a task touches a specific implementation layer.

---

## Planning

Planning snapshots and version decisions live in:

```text
.context/planning/
```

Current naming rule:

- Planning files use a three-digit ordering prefix for human scanning (thứ tự tư duy).
- The first planning file starts at `000_`.
- Implemented or completed plans move to `.context/planning/archives/` — prefix giữ nguyên.
- Archive index: `.context/planning/archives/README.md`.

Active planning lives at `.context/planning/` root; see `GLOBAL.md` [manual] Planning section.

Use planning files when discussing scope, layout direction, UX decisions, and future block boundaries.

---

## Proposals

Unapproved structural proposals live in:

```text
.context/proposals/
```

Files here are for review and debate. They are not active protocol until the human approves and the relevant root files are updated.
