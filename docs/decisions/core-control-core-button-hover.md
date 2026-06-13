# Core Control and Core Button Hover

Status: DECIDED, implementation not started  
Target: V1 / 1.3.4
Date: 2026-06-12

## Decision

`SKVN Marine` will contain a submenu named `Core Control`.

This screen is the shared registry and settings surface for optional
enhancements to WordPress core blocks and the Gutenberg editor. V1 / 1.3.4
ships `Core Button Hover Colors` and `Block Copy/Paste`.

The initial admin UI may be plain. Stable settings, compatibility, and an
extensible feature registry take priority over visual polish.

## Ownership and boundaries

- The current `skvn-marine-blocks` plugin owns the settings page, Gutenberg
  extension, saved compatibility data, and frontend behavior.
- Do not create or rename a plugin for this feature.
- Do not modify WordPress core or GeneratePress.
- Theme palettes may supply color choices, but the feature must remain portable
  plugin behavior.

## Architecture

Core Control uses two coordinated layers:

- `modules/core-control/` owns PHP bootstrap, the feature registry, Settings
  API, admin page, editor configuration, frontend rendering, and asset loading.
- `src/core-controls/` owns Gutenberg attribute registration, Inspector UI,
  editor preview, and feature-local TypeScript/CSS.

The planned structure is:

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

Responsibilities:

- `core-control.php` is the only module bootstrap. It loads the registry,
  settings/admin code, feature PHP adapters, and approved assets.
- `registry.php` returns the complete whitelist of supported enhancements and
  their defaults. It is the PHP source of truth for settings sanitization and
  admin rendering.
- `admin-page.php` registers `SKVN Marine -> Core Control` and renders all
  registered feature toggles through the Settings API.
- `features/<feature-id>.php` owns feature-specific server behavior such as
  frontend block rendering and conditional style loading.
- `src/core-controls/index.ts` initializes every compatibility module from one
  editor entry point.
- `src/core-controls/config.ts` reads the server-provided enabled-state map. It
  does not define defaults independently from the PHP registry.
- `src/core-controls/<feature-id>/` owns attributes, Inspector UI, editor
  preview behavior, and feature-local styles.
- `src/core-controls/shared/` is only for helpers used by at least two Core
  Control features or for a mandatory invariant. Feature-specific behavior
  must stay in its feature folder.

Core Control code must not be placed in custom block folders such as
`src/slider/`, because these features extend WordPress-owned blocks rather than
registering new `skvn-marine/*` blocks. It must not be placed in the theme.

## Registry contract

The registry is a keyed array. The initial conceptual shape is:

```php
[
    'button_hover_colors' => [
        'label'        => 'Core Button Hover Colors',
        'description'  => 'Adds governed hover colors to core buttons.',
        'default'      => false,
        'block_types'  => [ 'core/button' ],
        'editor_module'=> 'button-hover',
        'style_handle' => 'skvn-marine-core-button-hover',
    ],
    'block_clipboard' => [
        'label'         => 'Block Copy/Paste',
        'description'   => 'Adds explicit copy and paste actions for serialized Gutenberg blocks.',
        'default'       => false,
        'block_types'   => [],
        'editor_module' => 'block-clipboard',
    ],
]
```

The exact internal keys may be refined during implementation, but every
feature definition must provide:

- Stable namespaced feature ID
- Translatable label and description
- Boolean default
- Supported core block types
- Editor module identifier
- Frontend style handle when needed
- Optional PHP feature adapter

Only registry keys may be stored in `skvn_core_controls`. Unknown submitted
keys are discarded. Adding a feature must not require another settings page or
another top-level option.

## Runtime lifecycle

The architecture separates compatibility from feature activation.

Always active while the plugin is active:

- Register namespaced Gutenberg attributes.
- Parse existing saved values.
- Preserve serialization compatibility.
- Load the lightweight editor compatibility entry point.

Only active when the feature toggle is enabled:

- Render the feature Inspector panel.
- Enable editor visual preview.
- Apply frontend rendering changes.
- Enqueue feature frontend CSS.

The preferred `core/button` strategy is:

1. Store namespaced values as block attributes.
2. Keep the normal WordPress `core/button` saved HTML structure unchanged.
3. Use a PHP `render_block_core/button` adapter to add the scoped class and CSS
   variables at frontend render time when the feature is enabled.
4. Use editor wrapper props only for editor preview.

This strategy reduces invalid-block risk and allows disabling or deactivating
the enhancement without leaving required frontend runtime behavior in saved
HTML. The compatibility prototype must verify this assumption against the
project's supported WordPress version before full implementation.

PHP passes only the sanitized enabled-state map and required feature config to
the editor script. TypeScript must not query arbitrary options or duplicate
the PHP defaults.

## Adding another enhancement

To add a future Core Control feature:

1. Add one feature definition to `registry.php`.
2. Add `modules/core-control/features/<feature-id>.php` only when server-side
   rendering or feature-specific asset logic is required.
3. Add `src/core-controls/<feature-id>/` for its attributes, controls, preview,
   and styles.
4. Import its compatibility initializer from `src/core-controls/index.ts`.
5. Keep attribute registration unconditional and gate only UI/behavior by the
   enabled-state map.
6. Add feature-specific compatibility, accessibility, editor, frontend, and
   disable/re-enable tests.
7. Update this decision or a feature-specific decision when the new feature
   changes the shared Core Control contract.

Adding a feature must not duplicate menu registration, Settings API wiring,
option sanitization, or the editor configuration transport.

## Settings contract

Use the option `skvn_core_controls` with this initial shape:

```php
[
    'button_hover_colors' => true,
    'block_clipboard'      => true,
]
```

Requirements:

- `button_hover_colors` defaults to `false`.
- `block_clipboard` defaults to `false`.
- Save through the WordPress Settings API.
- Require `manage_options` and normal nonce protection.
- Sanitize only known boolean feature keys.
- The registry must allow later core enhancements to reuse the same menu,
  option, sanitization, and save flow.

The registry supplies the known keys and defaults used by this option.

## Toggle behavior

The toggle controls feature exposure, not data compatibility.

When disabled:

- Hide the hover controls in the block editor.
- Do not apply or enqueue the hover styling.
- Preserve previously configured hover values.
- Existing `core/button` blocks must remain valid and editable.

When enabled:

- Show the controls for every `core/button` block.
- Restore any previously configured values.
- Apply frontend and editor-preview hover styling.

Compatibility attributes or equivalent parsing support must remain registered
even while the feature is disabled. Disabling the feature must never discard
data or trigger Gutenberg block recovery.

## Block Copy/Paste

`Block Copy/Paste` is an editor-only utility with no saved attributes, frontend
rendering, CSS, or PHP feature adapter.

When enabled it registers two public Gutenberg menu actions:

- `Copy selected block(s)`
- `Paste block(s)`

It uses public WordPress packages and block-editor stores to serialize, parse,
check insertion permission, insert blocks, and show notices. It must not
override native clipboard events, patch Gutenberg internals, inspect private
editor DOM, introduce a custom clipboard format, or alter saved block markup.

When disabled, neither custom action is registered. Native WordPress
copy/paste remains unchanged. The feature can be removed without content
migration if WordPress core later provides an equivalent reliable workflow.

Detailed implementation and runtime gates:

- `.context/planning/023_VERSION_1_3_4_CORE_CONTROL_PLANNING.md`

## Editor UX

Add a separate, stable inspector panel named `Hover Colors`.

Do not inject a custom tab into Gutenberg's private native `Color` panel. That
UI is not a stable public extension boundary and would increase breakage risk
across WordPress releases.

The panel provides:

- Text color
- Background color
- Gutenberg palette-based color selection
- Clear/reset controls

Proposed namespaced attributes:

```text
skvnHoverTextColor
skvnHoverBackgroundColor
```

Names may change during a compatibility prototype, but they must remain
namespaced and must not alter the normal `core/button` markup structure.

## Frontend contract

Use a stable namespaced class and CSS custom properties, for example:

```text
has-skvn-button-hover
--skvn-button-hover-color
--skvn-button-hover-background
```

The plugin stylesheet applies these values to both `:hover` and
`:focus-visible`.

Additional rules:

- Existing buttons are unchanged until hover values are configured.
- Missing hover values fall back to the theme or WordPress default.
- Transition only `color`, `background-color`, and `border-color`.
- Use a duration in the 150-200ms range.
- Remove the transition for `prefers-reduced-motion: reduce`.
- Do not use `!important`.
- Do not require frontend JavaScript.

Frontend CSS should be tightly scoped. Load it only when the feature is enabled,
and conditionally for relevant content when that can be done reliably without
breaking nested blocks.

## Conflict policy

The feature is opt-in and defaults to disabled because other plugins may extend
`core/button` with similar behavior.

Automatic detection of every competing plugin is not a reliable contract.
`Core Control` should explain the possible overlap and let the administrator
enable or disable the enhancement manually.

All option keys, attributes, classes, CSS variables, PHP functions, and script
handles must use the SKVN namespace.

## Compatibility gate

Before full implementation, create a small prototype proving that:

1. A configured button remains valid after editor reload.
2. Turning the feature off hides controls and styling without deleting values.
3. Turning it back on restores the values.
4. Plugin deactivation and reactivation do not create an invalid block.
5. WordPress core/button serialization remains compatible.

Do not ship the editor extension until this gate passes.

## Acceptance criteria

- `Core Control` appears under the existing `SKVN Marine` admin menu.
- `Core Button Hover Colors` defaults to off.
- `Block Copy/Paste` defaults to off.
- The toggle saves and sanitizes correctly.
- Disabled state exposes no editor controls and applies no hover CSS.
- Enabled state supports text and background hover colors on all core buttons.
- Hover and keyboard focus-visible states use the configured colors.
- Existing values survive disable/re-enable cycles.
- Existing buttons remain unchanged when no hover values are set.
- No Gutenberg invalid-block recovery is introduced.
- Editor preview and frontend behavior match.
- The implementation uses no `!important` and no frontend JavaScript.
- Another hover plugin can remain authoritative while this feature is disabled.
- Enabling Block Copy/Paste exposes exactly two editor actions.
- Disabling Block Copy/Paste removes both actions without changing content.
- Complex block hierarchies survive visual-editor copy/paste.
- Native Gutenberg clipboard behavior is never overridden.

## Implementation workflow

Before implementation, the agent must load the plugin module context and create
the V1 / 1.3.4 planning document. The plan must map the architecture above to
the existing plugin bootstrap and `@wordpress/scripts` build entries without
turning the main plugin file or `src/index.ts` into feature registries.

The main plugin bootstrap may contain one `require_once` for
`modules/core-control/core-control.php`. The Core Control bootstrap owns the
remaining internal module loading.

The editor build may import one `src/core-controls/index.ts` entry from the
existing editor entry point. Feature modules are then registered from the Core
Control entry, not individually from the plugin root.

If implementation adds or changes PHP runtime `require` or `include` paths, the
deploy artifact and plugin ZIP runtime-file audit are mandatory.
