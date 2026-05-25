# Deploy Artifact Workflow

## Purpose

Build the deployable SKVN theme and plugin locally, then upload only the required folders to a test WordPress site.

Server does not need Node/npm for this artifact.

## Build Command

Run from Windows using WSL Debian:

```bash
wsl -d Debian bash -lc "source /home/shinkuro/.nvm/nvm.sh && nvm use 20 && cd /mnt/d/Github/skvn-marine && node tools/build-deploy-artifact.mjs"
```

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
