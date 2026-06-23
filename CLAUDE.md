# CLAUDE.md — SKVN Marine

⚠️ Do NOT read `docs/standards/ai-rules.md` automatically.
Only read it if I explicitly say "check full rules".

## Project Names

- Theme: `skvn-marine`
- Plugin: `skvn-marine-blocks`
- Block namespace: `skvn-marine`
- Theme PHP prefix: `skvn_marine_`
- Plugin PHP prefix: `skvn_marine_blocks_`
- CSS prefix: `skvn-`

## Rules

- Only read files I explicitly mention
- Do NOT explore project structure unless asked
- Keep responses concise, no explanation unless asked

## Non-Negotiable Rules

- Read `AGENTS.md` for additional context
- Do not edit `wp-content/themes/generatepress/`.
- Do not create custom Gutenberg blocks inside the theme.
- Put custom blocks in `skvn-marine-blocks`.
- Use `block.json` for custom block metadata.
- Use `theme.json` for design tokens.
- Use `editor.css` for Gutenberg preview.
- Use frontend CSS/JS for frontend animation.
- Do not add dependencies without writing rationale.
- Do not rename namespace/prefix without explicit approval.
- Do not use shortcodes for primary layout, except CF7 shortcode usage in quote form patterns.
- Do not overwrite manually entered image ALT text.
- Do not auto-generate captions in V1.

## Work Plan Rule

Follow `AGENTS.md` → **Agent response routing**:

- **`CODE_NOW`** (human đã chọn hướng: `theo E`, `làm A+C1`, `implement`, `fix`): implement ngay; plan ngắn trong reply là đủ.
- **`ASK_ARCHITECTURE`** / scope mơ hồ: plan chi tiết → chờ explicit approval → rồi mới implement.
- **`EXPLAIN`**: chỉ giải thích; không code cho đến khi human bảo làm tiếp.

Do NOT block `CODE_NOW` tasks with a full plan + wait-for-approval loop.

## Task Format

Every task should include: Context, Goal, Files allowed to change, Files forbidden to change, Acceptance checklist, Tension/conflict section.

## Scope Rule

Do not modify more than 3–5 files per task by default.

## Tension Rule

If a requested change conflicts with the rules above, record the tension instead of silently breaking the architecture.

## Local Environment

Local environment details are in `.local/` (gitignored, never commit).

- `.local/ENVIRONMENT.md` — source of truth for WSL distro, paths, credentials, CLI commands, WP server URL, build tool versions.
- `.local/` may also contain test scripts, artifacts, and logs.

If `.local/ENVIRONMENT.md` does not exist, ask the user to create it or run `.local/write-env.sh` before attempting any runtime, WP-CLI, PHP, or build commands.

Key lookup rules:
- All WP-CLI, PHP, MariaDB, and Node commands run inside WSL — check `WSL_DISTRO` and command patterns in `.local/ENVIRONMENT.md` before running.
- WordPress runtime root is outside this repo — check `WP_RUNTIME_ROOT_WSL` / `WP_RUNTIME_ROOT_WINDOWS` in `.local/ENVIRONMENT.md`.
- Plugin build runs from `wp-content/plugins/skvn-marine-blocks/`, not from repo root.
- `context-gen` consistency check command is in `.local/ENVIRONMENT.md` under `CONTEXT_GEN_CHECK_CMD`.

## Ideation Scratchpad

`docs/ideations/` là scratchpad cho phiên chat (gitignored). **Đầu mỗi phiên**, đọc `docs/ideations/README-claude.md` để biết topic nào đang open — tránh lặp lại reasoning đã có hoặc đã bị bác bỏ.

Workflow:
- Brainstorm / hypothesis mới → ghi vào `docs/ideations/<topic>.md` với status `open`.
- User xác nhận / bác bỏ bằng data → update status ngay (`confirmed` / `rejected + lý do`).
- Thành quyết định → promote sang `docs/decisions/` (git). Ideation file giữ nguyên đến hết phiên.
- **Không ghi** transcript, code snippet, hay thứ đã có trong file source — chỉ ghi diagnosis, rejected-why, open-question.

## Blocked / Timeout Rule

If a step requires runtime access (WP server, database, browser, onsite QA) and cannot proceed, stop and ask the user (Dev) instead of guessing or fabricating output.
