# Init Prompt — V1 / 1.3.4 Core Control

Use this prompt after the human explicitly approves starting V1 / 1.3.4.

```markdown
## Context

You are working in:

`D:\Github\skvn-marine`

Target milestone:

`V1 / 1.3.4 — Core Control Foundation & Core Button Hover`

Before implementation, verify that `.context/MILESTONES.md` and `AGENTS.md`
name V1 / 1.3.4 as the current milestone. If they still name another current
milestone, stop and ask the human to approve the milestone transition. Do not
self-transition.

Read first, in this exact order:

1. `AGENTS.md`
2. `.context/GLOBAL.md`
3. `.context/MILESTONES.md`
4. `.context/TENSIONS_OPEN.md`
5. `.context/TENSIONS_ACTIVE.md`
6. `.context/modules/PLUGIN_SKVN_MARINE_BLOCKS.md`
7. `docs/decisions/core-control-core-button-hover.md`
8. `docs/workflows/deploy-artifact-workflow.md`
9. `docs/workflows/versioning-release-workflow.md`

Read `.local/ENVIRONMENT.md` before using WSL, WP-CLI, the WordPress runtime,
or the local server.

Inspect `git status` and relevant diffs before editing. The worktree may contain
human changes. Do not revert, overwrite, clean, or reformat unrelated work.

## Goal

Implement the approved Core Control foundation inside the existing
`skvn-marine-blocks` plugin and prove it with two optional enhancements:
`Core Button Hover Colors` and `Block Copy/Paste`.

Deliver:

- `SKVN Marine -> Core Control`
- one reusable PHP feature registry
- one namespaced option: `skvn_core_controls`
- registry-driven Settings API sanitization and admin toggles
- one Core Control editor entry point
- one registry-governed editor-only Block Copy/Paste utility
- unconditional `core/button` compatibility attributes
- conditional Button Hover Inspector UI and styling
- frontend hover and `:focus-visible` colors
- preserved values and valid blocks across disable/re-enable and plugin
  deactivate/reactivate cycles

Do not implement another Core Control enhancement beyond Button Hover and
Block Copy/Paste in this milestone.

## Mandatory Architecture And Compatibility Gate

Do not implement the complete feature during the first pass.

First:

1. Create
   `.context/planning/023_VERSION_1_3_4_CORE_CONTROL_PLANNING.md`.
2. Inspect the existing plugin bootstrap, editor build entries, admin menu
   ownership, deploy packaging, and supported WordPress APIs.
3. Produce a concise checkpoint containing:
   - exact file tree to create or modify
   - PHP registry schema
   - option sanitization behavior
   - admin menu and Settings API hooks
   - server-to-editor configuration transport
   - exact Gutenberg filters used to register `core/button` attributes and
     render Inspector controls
   - exact frontend `render_block_core/button` strategy
   - proof that normal `core/button` saved markup remains unchanged
   - style registration and conditional enqueue strategy
   - deploy artifact changes required by new runtime PHP files
   - risks, unresolved API details, and proposed compatibility tests
4. Build the smallest compatibility prototype needed to prove:
   - namespaced attributes survive editor save/reload
   - normal `core/button` markup remains valid
   - disabled UI does not delete existing values
   - PHP can read the attributes during frontend block rendering
5. Report prototype evidence and stop for human approval before completing the
   admin page, visual controls, or production styling.

Do not reopen approved product scope. Ask only when repository evidence or the
WordPress public API cannot resolve a real technical choice.

## Approved Architecture

Required shape:

```text
wp-content/plugins/skvn-marine-blocks/
├── modules/
│   └── core-control/
│       ├── core-control.php
│       ├── registry.php
│       ├── admin-page.php
│       └── features/
│           └── button-hover.php
└── src/
    └── core-controls/
        ├── index.ts
        ├── config.ts
        ├── shared/
        │   └── types.ts
        └── button-hover/
            ├── index.tsx
            ├── attributes.ts
            └── style.css
```

The exact number of files may be reduced when a file would contain no real
responsibility. Preserve these ownership boundaries:

- `core-control.php`: one module bootstrap
- `registry.php`: feature whitelist and defaults
- `admin-page.php`: menu, Settings API, and registry-driven form
- `features/<feature-id>.php`: feature-specific server rendering/assets
- `src/core-controls/index.ts`: one editor compatibility entry point
- `src/core-controls/config.ts`: sanitized server-provided enabled states
- `src/core-controls/<feature-id>/`: feature attributes, UI, preview, styles

The plugin root may add one `require_once` for the Core Control bootstrap.
`src/index.ts` may add one import for the Core Control editor entry. Do not turn
either root file into a list of individual Core Control features.

## Registry And Settings Contract

Use one option:

```text
skvn_core_controls
```

Initial stored shape:

```php
[
    'button_hover_colors' => true,
    'block_clipboard'      => true,
]
```

Rules:

- `button_hover_colors` defaults to `false`.
- `block_clipboard` defaults to `false`.
- The PHP registry is the source of truth for known feature IDs and defaults.
- Save with the WordPress Settings API.
- Require `manage_options` and normal nonce protection.
- Sanitize every known value to boolean.
- Discard unknown submitted keys.
- Adding another feature later must not duplicate the menu, Settings API,
  sanitization, option, or editor configuration transport.
- PHP passes only sanitized enabled states and required configuration to the
  editor. TypeScript must not query arbitrary options or define conflicting
  defaults.

## Compatibility And Toggle Contract

Always register compatibility support while the plugin is active:

- `skvnHoverTextColor`
- `skvnHoverBackgroundColor`

The exact final attribute names may change only if the Gate proves a WordPress
API requirement. Names must remain namespaced.

The feature toggle controls:

- Inspector UI visibility
- editor visual preview
- frontend rendering changes
- frontend CSS loading

The toggle must not control:

- attribute registration
- parsing saved values
- value preservation
- block validity

For `block_clipboard`, the toggle controls whether its two editor menu actions
are registered. The feature stores no attributes and requires no compatibility
registration while disabled.

When disabled:

- no Hover Colors panel
- no frontend hover styling
- configured values remain stored
- existing buttons remain valid

When re-enabled:

- previous values reappear
- no resave or block recovery is required

## Core Button Rendering Contract

Prefer this strategy unless the compatibility Gate disproves it:

1. Keep WordPress `core/button` saved HTML structure unchanged.
2. Store hover choices as namespaced block attributes.
3. Use `render_block_core/button` to add a scoped class and CSS variables only
   when the feature is enabled and a hover value exists.
4. Use editor-only wrapper props or equivalent public Gutenberg filters for
   preview.
5. Do not require frontend JavaScript.

Proposed output contract:

```text
has-skvn-button-hover
--skvn-button-hover-color
--skvn-button-hover-background
```

Do not manually rebuild or regex-rewrite arbitrary button HTML. Use WordPress
HTML APIs such as `WP_HTML_Tag_Processor` when markup mutation is required and
supported by the project's WordPress baseline.

All PHP input must be normalized. All output and style values must be escaped
for their output context.

## Editor UX

For every selected `core/button`, when enabled:

- Add a separate Inspector panel named `Hover Colors`.
- Do not inject a tab into Gutenberg's private native Color panel.
- Provide Text color.
- Provide Background color.
- Use Gutenberg palette-based controls.
- Provide clear/reset behavior.
- Show an editor preview matching the frontend closely.

Existing buttons with no hover values must remain visually unchanged.

## CSS And Accessibility

- Apply configured values to `:hover` and `:focus-visible`.
- Transition only `color`, `background-color`, and `border-color`.
- Use a governed duration in the `150-200ms` range.
- Remove the transition under `prefers-reduced-motion: reduce`.
- Do not use `!important`.
- Keep selectors scoped to the SKVN class.
- Do not add viewport-width layout rules or overflow masking.
- Preserve usable keyboard focus.

## Conflict Policy

- The feature defaults to disabled.
- Explain on the Core Control page that another plugin may provide similar
  behavior.
- Do not attempt automatic detection of every competing plugin.
- When disabled, this feature must not apply frontend hover behavior.
- Prefix option keys, attributes, classes, CSS variables, PHP functions,
  script handles, and style handles with the approved SKVN namespace.

## Delivery Phases

### Phase A — Planning And Compatibility Gate

- Create the V1 / 1.3.4 planning document.
- Inspect current admin menu and editor build architecture.
- Define exact hooks and files.
- Build only the minimum compatibility prototype.
- Report evidence and stop for human approval.

### Phase B — Core Control Foundation

After approval:

- Add the PHP module bootstrap.
- Add the registry.
- Add the admin submenu and Settings API.
- Add the editor configuration transport.
- Add conditional asset registration/loading.
- Move `src/editor/block-clipboard.tsx` to
  `src/core-controls/block-clipboard/index.tsx` and register it only when
  `block_clipboard` is enabled.
- Keep the admin UI functional and clear; visual redesign is out of scope.

Before editing, tell the human exactly which files will change.

### Phase C — Core Button Hover

- Add unconditional namespaced attributes.
- Add the conditional Hover Colors Inspector panel.
- Add editor preview.
- Add frontend `render_block_core/button` integration.
- Add scoped frontend/editor CSS.
- Add focus-visible and reduced-motion behavior.

### Phase D — Verification And Handoff

- Run PHP syntax checks.
- Build plugin assets.
- Verify option sanitization.
- Verify disabled, enabled, disable/re-enable, editor reload, plugin
  deactivate/reactivate, and no-value behavior.
- Build the deploy artifact and plugin ZIP.
- Confirm every Core Control runtime PHP file is packaged.
- Create or update an onsite test document under `docs/testing/`.
- Do not mark V1 / 1.3.4 complete without human onsite evidence.

## Expected Source Areas

- `wp-content/plugins/skvn-marine-blocks/skvn-marine-blocks.php`
- `wp-content/plugins/skvn-marine-blocks/modules/core-control/**`
- `wp-content/plugins/skvn-marine-blocks/src/index.ts`
- `wp-content/plugins/skvn-marine-blocks/src/core-controls/**`
- `wp-content/plugins/skvn-marine-blocks/package.json` only if a distinct build
  entry is proved necessary
- `tools/build-deploy-artifact.mjs`
- `docs/workflows/deploy-artifact-workflow.md` when packaging assertions change
- `docs/testing/onsite-core-control-1.3.4.md`
- `.context/planning/023_VERSION_1_3_4_CORE_CONTROL_PLANNING.md`
- `.context/modules/PLUGIN_SKVN_MARINE_BLOCKS.md`
- `.context/MILESTONES.md`
- `.context/TENSIONS_OPEN.md` only when a real conflict is found

Generated `build/` files may change after source implementation.

This milestone may exceed five files because it introduces a PHP module,
registry, admin settings surface, editor extension, styling, build output,
packaging assertions, and testing documentation. Keep each phase narrowly
scoped and state the files before editing.

## Files Forbidden To Change

- `wp-content/themes/generatepress/**`
- WordPress core
- External plugin source
- Slider, Slide, Feature Showcase, or unrelated custom block behavior
- Theme files unless a separate human-approved theme decision is made
- Gutenberg private component internals

Do not rename:

- plugin slug `skvn-marine-blocks`
- block namespace `skvn-marine`
- text domain `skvn-marine-blocks`
- PHP prefix `skvn_marine_blocks_`
- CSS prefix `skvn-`
- option `skvn_core_controls`

## Acceptance Checklist

- [ ] Human approved starting V1 / 1.3.4.
- [ ] Planning document exists and matches the approved decision.
- [ ] Human approved the compatibility Gate before full implementation.
- [ ] `Core Control` appears under the existing `SKVN Marine` menu.
- [ ] One registry owns feature IDs and defaults.
- [ ] One option stores Core Control toggle states.
- [ ] Unknown option keys are discarded.
- [ ] Button Hover defaults to disabled.
- [ ] Block Copy/Paste defaults to disabled.
- [ ] Compatibility attributes remain registered while disabled.
- [ ] Disabled state exposes no UI and applies no frontend styling.
- [ ] Enabled state exposes Text and Background hover colors.
- [ ] Values survive editor reload and disable/re-enable.
- [ ] Plugin deactivate/reactivate does not invalidate configured buttons.
- [ ] Existing buttons without values remain unchanged.
- [ ] Saved `core/button` HTML structure remains WordPress-compatible.
- [ ] Hover and `:focus-visible` use configured values.
- [ ] Reduced motion removes transitions.
- [ ] No `!important` or frontend JavaScript is introduced.
- [ ] No WordPress core, GeneratePress, or private Gutenberg UI is modified.
- [ ] Block Copy/Paste does not override native clipboard events.
- [ ] Enabled Block Copy/Paste exposes exactly two actions; disabled exposes neither.
- [ ] Slider and nested block hierarchy survive cross-page visual-editor paste.
- [ ] PHP sanitization, escaping, and capability checks pass review.
- [ ] Plugin build and PHP lint pass.
- [ ] Deploy artifact and ZIP contain every Core Control runtime file.
- [ ] Onsite test evidence is recorded.
- [ ] Human approves milestone completion.

## Verification Commands

Run only after the relevant phase is implemented.

Plugin build:

```bash
wsl -d Debian -- bash -lc "source /home/shinkuro/.nvm/nvm.sh && nvm use 20 && cd /mnt/d/Github/skvn-marine/wp-content/plugins/skvn-marine-blocks && npm run build"
```

PHP syntax:

```bash
wsl -d Debian -- php -l /mnt/d/Github/skvn-marine/wp-content/plugins/skvn-marine-blocks/skvn-marine-blocks.php
wsl -d Debian -- php -l /mnt/d/Github/skvn-marine/wp-content/plugins/skvn-marine-blocks/modules/core-control/core-control.php
wsl -d Debian -- php -l /mnt/d/Github/skvn-marine/wp-content/plugins/skvn-marine-blocks/modules/core-control/registry.php
wsl -d Debian -- php -l /mnt/d/Github/skvn-marine/wp-content/plugins/skvn-marine-blocks/modules/core-control/admin-page.php
wsl -d Debian -- php -l /mnt/d/Github/skvn-marine/wp-content/plugins/skvn-marine-blocks/modules/core-control/features/button-hover.php
```

Deploy artifact and ZIP:

```bash
wsl -d Debian -- bash -lc "source /home/shinkuro/.nvm/nvm.sh && nvm use 20 && cd /mnt/d/Github/skvn-marine && node tools/build-deploy-artifact.mjs"
wsl -d Debian -- bash -lc "cd /mnt/d/Github/skvn-marine && bash tools/package-plugin-zip.sh"
wsl -d Debian -- bash -lc "cd /mnt/d/Github/skvn-marine && unzip -l build/skvn-marine-blocks.zip | grep 'skvn-marine-blocks/modules/core-control/'"
```

Source checks:

```bash
rg -n "skvn_core_controls|button_hover_colors|block_clipboard|skvnHover|render_block_core/button" wp-content/plugins/skvn-marine-blocks
rg -n "!important|100vw|50vw|overflow-x" wp-content/plugins/skvn-marine-blocks/src/core-controls
git diff --check
git status --short
```

Apply the Command Responsiveness rule strictly:

- If WSL, build, packaging, WP-CLI, server, or another command returns no
  output, hangs, or times out once, stop immediately.
- Do not retry, poll, or run a second version of the command.
- Give the human the exact command to run manually and state what output is
  needed before continuing.

## Tensions / Conflicts

Record a HIGH tension and stop if implementation would require:

- modifying WordPress core or GeneratePress
- renaming the plugin, namespace, text domain, prefixes, or approved option
- changing normal `core/button` saved markup in a way that invalidates content
- deleting hover values when the feature is disabled
- requiring bulk resave or block recovery
- depending on Gutenberg private UI internals
- creating another plugin or moving this feature into the theme

Record a LOW tension and proceed conservatively only when it matches the
project's LOW routing rules.

At each phase report:

- files changed
- architecture or behavior completed
- exact checks and results
- compatibility evidence
- remaining human evidence
- whether the next phase requires approval
```
