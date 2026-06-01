# Page Display Controls

Status: working decision.
Milestone: V1 / 0.5.1.
Layer: `skvn-marine` theme.

## Decision

Marketing pages should not require editors to repeat the same GeneratePress/SKVN setup every time a new landing-style page is created.

The standard setup is exposed as a single page preset:

```text
SKVN Landing Canvas
- Hide page title
- Enable SKVN full-width canvas
- Force no-sidebar behavior through the SKVN canvas contract
- Let full-width sections reach the viewport edge
```

## Ownership

The theme owns page display controls because the behavior is page chrome/layout, not block logic.

Relevant source files:

- `wp-content/themes/skvn-marine/inc/page-display-controls.php`
- `wp-content/themes/skvn-marine/assets/js/page-display-controls.js`
- `wp-content/themes/skvn-marine/assets/css/page-display-controls-editor.css`

## Storage

The preset is stored as page meta:

```text
_skvn_page_display_preset = skvn_landing_canvas
```

Direct toggles remain page meta as well:

```text
_skvn_hide_header
_skvn_hide_footer
_skvn_hide_title
_skvn_full_width_canvas
```

The preset intentionally does not hide the site header or footer. Those remain explicit page decisions.

## GeneratePress Boundary

Do not edit GeneratePress parent files.

Do not blindly write GeneratePress layout metabox meta keys. The SKVN preset applies the layout through SKVN-owned meta/classes and safe theme filters where audited. The GeneratePress `generate_sidebar_layout` filter is used to force `no-sidebar` when the SKVN full-width canvas is active.

## UX Rule

Editors should start with the preset:

```text
SKVN Page Display -> Page preset -> SKVN Landing Canvas
```

Individual toggles stay visible for override/debugging. If the combination no longer matches a known preset, the sidebar should report `Custom settings`.

## Design Debt For 0.8.0

Current page display controls can hide the frontend title. That is correct for marketing pages, but it creates an editor UX debt if the Gutenberg title field or page identity surface effectively disappears while editing.

Future 0.8.0 editor controls should separate these two concepts:

```text
Frontend page title visibility: hidden
Editor title/identity surface: still visible and editable
```

The editor may show the title with a state label such as `Hidden on frontend`, but it should not make editors hunt for the page title or lose the page identity while composing content.
