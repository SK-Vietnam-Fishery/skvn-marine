# Deploy Artifact Workflow

## Purpose

Build the deployable SKVN theme and plugin locally, then upload only the required folders to a test WordPress site.

Server does not need Node/npm for this artifact.

## Build Command

Run from Windows using WSL Debian:

```bash
wsl -d Debian bash -lc "source /home/shinkuro/.nvm/nvm.sh && nvm use 20 && cd /mnt/d/Github/skvn-marine && node tools/build-deploy-artifact.mjs"
```

Run directly inside the Debian shell:

```bash
source /home/shinkuro/.nvm/nvm.sh && nvm use 20 && cd /mnt/d/Github/skvn-marine && node tools/build-deploy-artifact.mjs
```

## Upcoming Toolchain Check

Status: planned small task, not required for the immediate onsite zip build.

Current recorded local toolchain:

```text
Node.js: 20.20.2
npm:     10.8.2
```

npm 11 is stable enough to evaluate, but treat the npm 10 -> npm 11 move as
a toolchain validation task instead of mixing it into a normal deploy build.

Planned check:

```bash
source /home/shinkuro/.nvm/nvm.sh
nvm use 20
npm install -g npm@11.15.0
cd /mnt/d/Github/skvn-marine/wp-content/plugins/skvn-marine-blocks
npm ci
npm run build
```

Acceptance:

```text
[ ] npm reports 11.15.0.
[ ] npm ci completes.
[ ] npm run build completes.
[ ] package-lock.json diff is reviewed.
[ ] Deploy artifact build still completes from repo root.
[ ] Editor and frontend smoke tests still pass.
[ ] If accepted, update local environment docs/toolchain notes.
```

## Package Zip Files

After the build command succeeds, run these inside Debian from the repo root:

```bash
bash tools/package-theme-zip.sh
bash tools/package-plugin-zip.sh
```

Zip output:

```text
build/skvn-marine.zip
build/skvn-marine-blocks.zip
```

Each zip contains one top-level folder, so WordPress can unpack it into the
matching theme/plugin directory.

## Output

The deployable output is gitignored:

```text
build/wp-content/themes/skvn-marine/
build/wp-content/plugins/skvn-marine-blocks/
```

Upload the folders above to the online site. Do not upload directly from
`wp-content/themes/skvn-marine/` or `wp-content/plugins/skvn-marine-blocks/`
because the build step strips development-only files from the plugin artifact.

## Upload

Copy these two folders into the target site's matching WordPress folders:

```text
build/wp-content/themes/skvn-marine/
-> target-site/wp-content/themes/skvn-marine/

build/wp-content/plugins/skvn-marine-blocks/
-> target-site/wp-content/plugins/skvn-marine-blocks/
```

## Artifact Contents

Theme artifact:

```text
functions.php
style.css
theme.json
assets/
inc/
patterns/
```

Plugin artifact:

```text
skvn-marine-blocks.php
build/
```

The plugin artifact intentionally does not include:

```text
src/
node_modules/
tests/
docs/
.context/
```

## Target Site Requirements

Before activating SKVN code on the target site:

```text
[ ] WordPress is installed.
[ ] GeneratePress parent theme is installed.
[ ] WooCommerce is installed and active if testing product sections.
[ ] External plugins are installed in the target site, not copied from this source repo.
```

Then:

```text
1. Activate `skvn-marine` child theme.
2. Activate `skvn-marine-blocks` plugin.
3. Open editor and confirm SKVN blocks/patterns are available.
4. Run runtime smoke tests from `docs/testing/testing-checklist.md`.
```
