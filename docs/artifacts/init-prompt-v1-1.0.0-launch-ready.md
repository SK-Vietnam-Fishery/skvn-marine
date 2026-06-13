# Init Prompt — V1 / 1.0.0 Launch Ready

Use this prompt after human explicitly approves moving to `V1 / 1.0.0 — V1 Launch Ready`.

Current handoff note:

- Human approved moving past `0.12.0` except for onsite testing.
- `0.11.0` and `0.12.0` are carried into `1.0.0` with onsite test debt, not marked DONE.
- The first `1.0.0` launch-readiness pass should review or remind human to run `docs/testing/onsite-0.11-0.12-completion-checklist.md`.

```markdown
## Context

Read first, in order:

1. `AGENTS.md`
2. `.context/GLOBAL.md`
3. `.context/MILESTONES.md`
4. `.context/TENSIONS_OPEN.md`
5. `.context/TENSIONS_ACTIVE.md`
6. `.context/modules/THEME_SKVN_MARINE.md`
7. `.context/modules/PLUGIN_SKVN_MARINE_BLOCKS.md`
8. `.context/modules/QUOTE_FLOW.md`
9. `docs/testing/onsite-0.11-0.12-completion-checklist.md`
10. `docs/testing/footer-appearance-settings-0.11.0.md`
11. `docs/testing/header-actions-search-0.12.0.md`
12. `docs/decisions/footer-appearance-settings-0.11.0.md`
13. `docs/decisions/header-actions-search-0.12.0.md`
14. `docs/workflows/deploy-artifact-workflow.md`
15. `docs/workflows/versioning-release-workflow.md`

Human has approved carrying source-complete 0.11/0.12 work forward except for onsite testing:

- `0.11.0 — SKVN Marine Admin Menu`
- `0.12.0 — SKVN Header Actions And B2B Search`

Human has explicitly approved moving to:

`V1 / 1.0.0 — V1 Launch Ready`

Important carry-over:

- 0.11 and 0.12 may be deployed/tested together onsite during 1.0.0.
- 0.11/0.12 onsite evidence is not complete until human reports it.
- Recommended onsite stability plugins for 1.0.0 review:
  - Admin and Site Enhancements (ASE) for admin/menu governance and wp-admin workflow polish.
  - Ultra Addons for Contact Form 7 for CF7 form UI/stability enhancements.
- These are external WordPress runtime plugins only; do not copy or commit them into the source repo.
- Local UI runtime should not be treated as source of truth because it is slow and may not match onsite editor-created content.
- CF7 data-flow and map/display testing remain deferred to `V1 / 1.1.2`.
- n8n remains out of scope and must stay unexposed.
- Polylang remains prepare-only unless human explicitly changes multilingual scope.

## Goal

Start milestone `V1 / 1.0.0 — V1 Launch Ready`.

Do not implement new features by default. Perform a launch-readiness pass:

- Accessibility pass
- Mobile QA pass
- SEO/GEO structure pass
- Performance and asset loading review
- Forbidden-change audit
- Deploy/package sanity check
- Document any launch blockers or deferred debt

## Files allowed to change

Prefer docs/context updates first. Code changes only if a concrete launch blocker is found.

Allowed likely files:

- `AGENTS.md`
- `.context/MILESTONES.md`
- `.context/MILESTONES_HISTORY.md`
- `.context/TENSIONS_OPEN.md`
- `.context/TENSIONS_ACTIVE.md`
- `.context/TENSIONS_HISTORY.md` only if the human-approved milestone transition requires archiving active tensions
- `.context/modules/THEME_SKVN_MARINE.md`
- `.context/modules/PLUGIN_SKVN_MARINE_BLOCKS.md`
- `.context/modules/QUOTE_FLOW.md`
- `docs/testing/*`
- `docs/decisions/*`
- `docs/workflows/*`
- `wp-content/themes/skvn-marine/**` only for concrete launch blockers
- `wp-content/plugins/skvn-marine-blocks/**` only for concrete launch blockers

## Files forbidden to change

- `wp-content/themes/generatepress/**`
- External plugins:
  - WooCommerce
  - Contact Form 7
  - CFDB7
  - Rank Math
  - Polylang
  - WindPress
  - ASE
  - Ultra Addons for Contact Form 7
- Do not rename:
  - plugin slug `skvn-marine-blocks`
  - theme slug `skvn-marine`
  - block namespace `skvn-marine`
  - option keys `skvn_footer_page_id`, `skvn_footer_background_preset`, `skvn_header_actions`
  - text domains
  - namespace/prefixes

## Required milestone transition handling

Because human approved moving from `0.12.0` to `1.0.0`:

1. Update `AGENTS.md` current milestone to `V1 / 1.0.0 — V1 Launch Ready`.
2. Update `.context/MILESTONES.md` current milestone to `V1 / 1.0.0 — V1 Launch Ready`.
3. Mark 0.11/0.12 checklist status based on human onsite evidence.
4. Move completed milestone notes into `.context/MILESTONES_HISTORY.md` if appropriate under project protocol.
5. Do not archive unresolved OPEN tensions.
6. Only move `RESOLVED_ACTIVE` tensions to history if protocol requires it for the milestone transition.
7. Do not run `node tools/bump-project-version.mjs 1.0.0` unless human confirms this is the release/deploy boundary for version metadata sync.

## Acceptance checklist

- [ ] Human onsite evidence from `docs/testing/onsite-0.11-0.12-completion-checklist.md` is reviewed if available.
- [ ] If onsite evidence is not available, 0.11/0.12 remains carried with test debt; no fake DONE.
- [ ] Accessibility pass documented.
- [ ] Mobile QA pass documented.
- [ ] SEO/GEO structure pass documented.
- [ ] Performance and asset loading review documented.
- [ ] Admin and Site Enhancements (ASE) is reviewed/active onsite if needed for admin workflow stability.
- [ ] Ultra Addons for Contact Form 7 is reviewed/active onsite if needed for CF7 form UI/stability.
- [ ] No GeneratePress parent files edited.
- [ ] No external plugins committed to source repo.
- [ ] n8n remains deferred/unexposed.
- [ ] CF7 data-flow and map/display debt remains deferred to `1.1.2`.
- [ ] Any launch blockers are listed with file/line/source evidence.
- [ ] If code changes are made, run relevant PHP lint/build checks.
- [ ] If deploy artifact is rebuilt, verify runtime modules and zip contents.
- [ ] Human approves V1 launch readiness.

## Verification commands

Run source checks as needed:

```bash
wsl -d Debian -- php -l /mnt/d/Github/skvn-marine/wp-content/themes/skvn-marine/functions.php
wsl -d Debian -- php -l /mnt/d/Github/skvn-marine/wp-content/themes/skvn-marine/inc/header-actions.php
wsl -d Debian -- php -l /mnt/d/Github/skvn-marine/wp-content/themes/skvn-marine/inc/footer.php
wsl -d Debian -- php -l /mnt/d/Github/skvn-marine/wp-content/themes/skvn-marine/search.php
wsl -d Debian -- php -l /mnt/d/Github/skvn-marine/wp-content/plugins/skvn-marine-blocks/skvn-marine-blocks.php
wsl -d Debian -- php -l /mnt/d/Github/skvn-marine/wp-content/plugins/skvn-marine-blocks/modules/footer-settings/footer-settings.php
wsl -d Debian -- php -l /mnt/d/Github/skvn-marine/wp-content/plugins/skvn-marine-blocks/modules/header-settings/header-settings.php
git diff --check
```

If building deploy artifacts:

```bash
wsl -d Debian -- bash -lc "source /home/shinkuro/.nvm/nvm.sh && nvm use 20 && cd /mnt/d/Github/skvn-marine && node tools/build-deploy-artifact.mjs"
wsl -d Debian -- bash -lc "cd /mnt/d/Github/skvn-marine && bash tools/package-theme-zip.sh"
wsl -d Debian -- bash -lc "cd /mnt/d/Github/skvn-marine && bash tools/package-plugin-zip.sh"
wsl -d Debian -- bash -lc "cd /mnt/d/Github/skvn-marine && unzip -l build/skvn-marine-blocks.zip | grep 'skvn-marine-blocks/modules/'"
```

## Tensions / Conflicts

If launch-readiness work would require:

- editing GeneratePress parent theme
- renaming plugin/theme/option keys
- custom-coding CF7 form handling
- exposing n8n
- activating Polylang without explicit human decision
- adding Elastic/OpenSearch/custom query cache
- adding a dependency without rationale
- closing onsite test debt without evidence

then record a tension in `.context/TENSIONS_OPEN.md` and stop for human decision.
```
