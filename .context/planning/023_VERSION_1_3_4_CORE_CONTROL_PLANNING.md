# V1 / 1.3.4 — Core Control Planning

Status: PLANNED, implementation not started
Target: V1 / 1.3.4
Created: 2026-06-13

## Goal

Build `SKVN Marine -> Core Control` and ship two opt-in enhancements:

1. `Core Button Hover Colors`
2. `Block Copy/Paste`

Both default to disabled and use the single option `skvn_core_controls`. This
plan does not start V1 / 1.3.4 or change the current milestone.

## Registry

Conceptual registry:

```php
[
    'button_hover_colors' => [
        'label'         => 'Core Button Hover Colors',
        'description'   => 'Adds governed hover colors to core buttons.',
        'default'       => false,
        'editor_module' => 'button-hover',
        'block_types'   => [ 'core/button' ],
    ],
    'block_clipboard' => [
        'label'         => 'Block Copy/Paste',
        'description'   => 'Adds explicit copy and paste actions for serialized Gutenberg blocks.',
        'default'       => false,
        'editor_module' => 'block-clipboard',
        'block_types'   => [],
    ],
]
```

Rules:

- Stable feature IDs are `button_hover_colors` and `block_clipboard`.
- Save only known registry keys and normalize values to boolean.
- Discard unknown submitted keys.
- A missing key uses its registry default.
- Do not create a second option or settings page for Block Copy/Paste.

## Source Shape

```text
wp-content/plugins/skvn-marine-blocks/
├── modules/core-control/
│   ├── core-control.php
│   ├── registry.php
│   ├── admin-page.php
│   └── features/button-hover.php
└── src/core-controls/
    ├── index.ts
    ├── config.ts
    ├── shared/types.ts
    ├── button-hover/
    │   ├── index.tsx
    │   ├── attributes.ts
    │   └── style.css
    └── block-clipboard/index.tsx
```

Move the existing utility:

```text
src/editor/block-clipboard.tsx
-> src/core-controls/block-clipboard/index.tsx
```

`src/index.ts` imports only `src/core-controls/index.ts`, not individual Core
Control features.

## Block Clipboard Contract

When enabled, register exactly:

- `Copy selected block(s)`
- `Paste block(s)`

Invariants:

- Use public WordPress packages and stores only.
- Copy standard Gutenberg markup with `serialize()`.
- Parse clipboard content only when it contains a Gutenberg block delimiter.
- Insert through the block-editor store and respect insertion permissions.
- Fall back to page canvas only when the current nested surface is incompatible.
- Reject non-block text without changing post content.
- Show notices for success, rejection, permission failure, or invalid markup.
- Do not override native copy/paste events.
- Do not patch Gutenberg internals or inspect private editor DOM.
- Do not create a custom clipboard MIME type or persistent payload.
- Do not alter saved block markup.
- Do not add frontend assets, PHP rendering, or CSS.

Toggle behavior:

- Disabled: register neither custom action.
- Enabled: register both actions through supported plugin/SlotFill APIs.
- Disable/re-enable does not affect post content or clipboard data.
- No compatibility attributes are required because the feature stores no data.

If WordPress later provides equivalent reliable actions, this feature can be
disabled or removed without content migration.

## Verification Gates

Button Hover retains its saved-content compatibility gate from
`docs/decisions/core-control-core-button-hover.md`.

Block Copy/Paste has a separate editor runtime gate:

1. Enable `block_clipboard`.
2. Copy an SKVN Slider parent selected through List View.
3. Paste into another page in Visual mode.
4. Verify Slider attributes and Slide InnerBlocks remain intact.
5. Repeat with multiple top-level blocks.
6. Verify incompatible nested insertion falls back to page canvas.
7. Verify ordinary text and clipboard denial do not change content.
8. Disable the feature and verify both actions disappear.
9. Verify native WordPress copy/paste remains unchanged.
10. Re-enable and verify both actions return.

Testing sources:

- `docs/testing/editor-block-clipboard-1.3.2.md`
- Create or merge into `docs/testing/onsite-core-control-1.3.4.md` during
  implementation.

## Implementation Phases

### Phase A — Foundation

- Create the PHP registry and registry-driven Settings API page.
- Add both feature IDs, off by default.
- Pass only sanitized enabled states to the editor.

### Phase B — Clipboard Migration

- Move the utility under `src/core-controls/block-clipboard/`.
- Gate registration with `block_clipboard`.
- Remove its old direct import from `src/index.ts`.

### Phase C — Button Hover Gate

- Run the mandatory Button Hover compatibility prototype.
- Stop for human approval before production controls and styling.

### Phase D — Completion

- Complete Button Hover after gate approval.
- Run PHP lint and plugin build.
- Audit deploy runtime files and plugin ZIP for `modules/core-control/`.
- Run onsite toggle, clipboard, compatibility, and disable/re-enable tests.

## Acceptance

- [ ] One registry and one option own both feature toggles
- [ ] Both features default to disabled
- [ ] Disabled Block Copy/Paste registers no custom actions
- [ ] Enabled Block Copy/Paste exposes exactly two actions
- [ ] Slider and InnerBlocks survive cross-page visual-editor copy/paste
- [ ] Non-block text and clipboard errors do not change post content
- [ ] Native Gutenberg clipboard behavior is not overridden
- [ ] Block Copy/Paste adds no saved attributes or frontend behavior
- [ ] Button Hover compatibility and styling criteria remain satisfied
- [ ] PHP lint, build, deploy audit, ZIP audit, and onsite QA pass
- [ ] Human approves milestone completion
