# Versioning And Release Workflow

## Purpose

Keep milestone planning versions and WordPress release artifact versions from drifting.

`.context/MILESTONES.md` controls scope and planning. It does not automatically update WordPress theme/plugin headers.

When the human explicitly starts a milestone, the agent may sync working metadata with the target milestone version. Treat that as a milestone development build, not release approval.

Before packaging or deploying a milestone release, verify release metadata with the target milestone version.

## Version Sources

Update these together for a release:

```text
wp-content/themes/skvn-marine/style.css
wp-content/plugins/skvn-marine-blocks/skvn-marine-blocks.php
wp-content/plugins/skvn-marine-blocks/package.json
wp-content/plugins/skvn-marine-blocks/package-lock.json
wp-content/plugins/skvn-marine-blocks/src/*/block.json
wp-content/plugins/skvn-marine-blocks/build/*/block.json
```

Theme and plugin asset cache-busting still uses `filemtime()` where available. The `Version:` headers are for WordPress release identity, update screens, zip metadata, and human audit.

## Release Command

Preferred one-command workflow from the repo root (WSL Debian):

```bash
bash tools/release-artifact.sh 1.3.4
```

This runs in order:

1. `node tools/bump-project-version.mjs <version>`
2. `node tools/build-deploy-artifact.mjs`
3. `bash tools/package-theme-zip.sh`
4. `bash tools/package-plugin-zip.sh`

Use the milestone or release version approved by the human.

Preview version file changes without writing:

```bash
bash tools/release-artifact.sh 1.3.4 --dry-run
```

Bump versions only, without build or zip:

```bash
bash tools/release-artifact.sh 1.3.4 --bump-only
```

From Windows terminal:

```bash
wsl -d Debian -- bash -lc "cd /mnt/d/Github/skvn-marine && bash tools/release-artifact.sh 1.3.4"
```

After release, inspect:

```bash
git diff -- wp-content/themes/skvn-marine/style.css wp-content/plugins/skvn-marine-blocks
```

## Bump-Only Command

If you only need metadata sync without packaging:

```bash
node tools/bump-project-version.mjs 1.3.4
```

Equivalent to `bash tools/release-artifact.sh 1.3.4 --bump-only`.

Node-only release chain (same steps as the shell wrapper):

```bash
node tools/bump-project-version.mjs 1.3.4 --release
```

## When To Bump

Bump when:

- human explicitly starts a milestone and wants the WordPress admin/files to advertise the current milestone working version
- packaging a theme/plugin zip for a milestone release
- human approves moving from one milestone to the next
- deploy artifact should advertise a new version in WordPress admin

Do not bump for every small local edit.

If the bump happens at milestone start, rebuild plugin assets after bumping and keep the milestone acceptance checklist as the release gate.

## Release Checklist

```text
[ ] Human approved target milestone/release version.
[ ] Run bash tools/release-artifact.sh <version> (or bump/build/zip steps separately if needed).
[ ] Run PHP syntax checks for touched PHP files.
[ ] Run git diff --check.
[ ] Audit deploy artifact contents for runtime PHP include/require paths added in this milestone.
[ ] Confirm plugin zip contains required runtime folders such as build/, modules/, and assets/ before upload.
[ ] Confirm WordPress admin shows the expected theme/plugin version after upload.
```

## Guardrails

- Do not treat build asset hashes as project versions.
- Do not rely on `npm run build` to update WordPress `Version:` headers.
- Do not rely on `npm run build` to package arbitrary runtime PHP folders.
- If a milestone adds plugin PHP outside `build/`, update `tools/build-deploy-artifact.mjs` before packaging.
- Do not change plugin/theme slug, text domain, namespace, or option keys while bumping version.
- Do not bump future planning candidates such as `Gutenberg Supercharger` into the current SKVN Marine plugin identity.
