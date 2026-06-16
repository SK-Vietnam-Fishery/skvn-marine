# CLAUDE.md — SKVN Marine

> Full rules: `docs/standards/ai-rules.md`

## Project Names

- Theme: `skvn-marine`
- Plugin: `skvn-marine-blocks`
- Block namespace: `skvn-marine`
- Theme PHP prefix: `skvn_marine_`
- Plugin PHP prefix: `skvn_marine_blocks_`
- CSS prefix: `skvn-`

## Non-Negotiable Rules

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

## Blocked / Timeout Rule

If a step requires runtime access (WP server, database, browser, onsite QA) and cannot proceed, stop and ask the user (Dev) instead of guessing or fabricating output.
