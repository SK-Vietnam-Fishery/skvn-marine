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

## Bump Command

From the repo root:

```bash
node tools/bump-project-version.mjs 0.9.0
```

Use the milestone or release version approved by the human.

After bumping:

```bash
wsl -d Debian -- bash -lc "source /home/shinkuro/.nvm/nvm.sh && nvm use 20 && cd /mnt/d/Github/skvn-marine/wp-content/plugins/skvn-marine-blocks && npm run build"
```

Then inspect:

```bash
git diff -- wp-content/themes/skvn-marine/style.css wp-content/plugins/skvn-marine-blocks
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
[ ] Run node tools/bump-project-version.mjs <version>.
[ ] Run plugin build after bump.
[ ] Run PHP syntax checks for touched PHP files.
[ ] Run git diff --check.
[ ] Build deploy artifact.
[ ] Audit deploy artifact contents for runtime PHP include/require paths added in this milestone.
[ ] Package theme/plugin zip if needed.
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
