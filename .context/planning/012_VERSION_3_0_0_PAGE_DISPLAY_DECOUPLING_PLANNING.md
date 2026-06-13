# Version 3.0.0 Planning — Page Display Decoupling From GeneratePress

Status: planning
Target: V2 / 2.0.0 migration start → V3 / 3.0.0 completion
Scope owner: `skvn-marine` theme
Related current module: Page Display Controls

## Context

V1 uses GeneratePress as the parent theme. Page-level layout behavior currently comes from two places:

- SKVN Page Display panel in the Gutenberg page sidebar.
- GeneratePress Layout / Disable Elements panels.

This works for V1 because GeneratePress is still the shell, but it creates editor confusion:

- `Hide page title` in SKVN Page Display overlaps with GeneratePress `Disable Elements -> Content Title`.
- `Full width canvas` in SKVN Page Display overlaps with GeneratePress `Content Container = Full Width`.
- Footer/header reusable pages need a known setup, but the setup is currently split across SKVN and GeneratePress UI.

V2 direction: SKVN starts migrating away from GeneratePress dependency.

V3 direction: SKVN completes the migration and runs as a standalone/custom theme without depending on GeneratePress UI, GeneratePress meta keys, or GeneratePress hook surfaces.

## Version Alignment Decision

Human decision:

- `2.0.0`: start migrating away from GeneratePress.
- `3.0.0`: complete the GeneratePress removal and finish the standalone SKVN theme direction.

Existing `.context/planning/005_VERSION_2_0_0_FOOTER_BUILDER_PLANNING.md` remains valid as a `2.0.0` migration-start planning file. It should not imply that all GeneratePress removal work is complete in `2.0.0`.

## Problem

The editor should not require marketing users to understand which control belongs to SKVN and which control belongs to GeneratePress.

For V2, SKVN should begin mirroring or replacing the GeneratePress controls that matter to SKVN pages.

For V3, GeneratePress controls will disappear. Any behavior that matters to SKVN page layout must already be represented in SKVN-owned controls before the theme becomes standalone.

## Goals

- Move or mirror important GeneratePress page-layout controls into `SKVN Page Display`.
- Make SKVN Page Display the source of truth for page chrome and canvas behavior.
- Add dedicated presets for reusable Header and Footer pages.
- Keep V1 compatibility with GeneratePress while avoiding direct dependency on GeneratePress-specific UI in the editor workflow.
- Use V2 / 2.0.0 as the migration-start phase for SKVN-owned page display controls.
- Prepare V3 / 3.0.0 standalone theme behavior where SKVN controls apply without GeneratePress.

## Non-Goals

- Do not edit GeneratePress parent files.
- Do not blindly write GeneratePress private meta keys before auditing them.
- Do not build a full header/footer builder in this planning item.
- Do not expose raw classes or raw CSS to marketing editors.
- Do not remove GeneratePress in V1.

## Proposed SKVN Page Display Controls

Current controls:

- Page preset
- Hide site header
- Hide site footer
- Hide page title
- Full width canvas

Future controls to add or mirror:

- Hide content title
  - SKVN-owned replacement for GeneratePress `Disable Elements -> Content Title`.
  - For V1 compatibility, it may map to existing SKVN `Hide page title` behavior unless a separate content-title case is proven.
- Sidebar layout
  - Recommended values: `default`, `no-sidebar`.
  - SKVN should store its own meta and apply safe runtime filters.
- Content container
  - Recommended values: `default`, `full-width`.
  - SKVN `Full width canvas` should become the normal editor-facing setting.
- Footer widgets visibility
  - Optional only if needed by real pages.
  - Do not mirror every GeneratePress control unless SKVN has a real UX need.

## Proposed Page Presets

### SKVN Landing Canvas

Existing purpose: marketing landing-style pages.

Recommended state:

- Hide site header: optional, default off.
- Hide site footer: optional, default off.
- Hide page title: on.
- Full width canvas: on.
- Sidebar layout: no-sidebar.
- Content container: full-width.

### SKVN Request Quote Page

Existing purpose: quote workflow page.

Recommended state:

- Hide site header: off unless human wants a focused quote page.
- Hide site footer: off.
- Hide page title: on.
- Full width canvas: on.
- Sidebar layout: no-sidebar.
- Content container: full-width.

### SKVN Footer Page

Purpose: page used as reusable site footer content.

Recommended state:

- Hide site header: on for direct preview.
- Hide site footer: on for direct preview, so the footer page does not render the site footer under itself.
- Hide page title: on.
- Full width canvas: on.
- Sidebar layout: no-sidebar.
- Content container: full-width.
- Footer widgets: default or hidden, depending on whether SKVN gains its own footer-widget visibility control.

Runtime note:

- When this page is rendered through the reusable footer renderer, the theme owns the actual footer wrapper.
- These page settings mostly affect direct editor/preview of the Footer page.

### SKVN Header Page

Purpose: future page or pattern used as reusable header/header-action content.

Recommended state:

- Hide site header: on for direct preview.
- Hide site footer: on for focused preview unless footer context is needed.
- Hide page title: on.
- Full width canvas: on.
- Sidebar layout: no-sidebar.
- Content container: full-width.

Runtime note:

- Header implementation is still planned separately.
- In V1.x, GeneratePress header remains the shell.
- In V3, SKVN should own the header shell and these settings should no longer depend on GeneratePress.

## Implementation Phases

### Phase 1 — V1 Compatibility

- Keep GeneratePress installed and untouched.
- Add SKVN-owned page meta for any new controls.
- Apply runtime behavior through SKVN filters/classes.
- Use GeneratePress filters only as compatibility adapters.
- Do not write GeneratePress private meta unless audited and documented.

### Phase 2 — V2 / 2.0.0 Migration Start

- Add `SKVN Footer Page` preset.
- Add `SKVN Header Page` preset only when header implementation is approved.
- Presets should set SKVN-owned meta in one editor action.
- Direct toggles remain visible for debugging and override.
- Start treating SKVN Page Display as the primary editor-facing layout surface.
- Keep GeneratePress adapters only as compatibility shims while the parent theme is still present.

### Phase 3 — V3 / 3.0.0 Migration Complete

- Remove assumptions that GeneratePress panels exist.
- Keep SKVN Page Display as the single editor-facing source of truth.
- Replace GeneratePress hook adapters with SKVN-owned template/header/footer rendering.
- Audit any code paths that mention `GeneratePress`, `generate_*` hooks, or GeneratePress layout filters.

## Acceptance Checklist

- [x] Human confirms version alignment: `2.0.0` starts migration away from GeneratePress; `3.0.0` completes the migration.
- [ ] Existing `2.0.0` planning docs avoid implying the migration is complete in `2.0.0`.
- [ ] SKVN Page Display documents every page-layout behavior needed after GeneratePress removal.
- [ ] `SKVN Footer Page` preset exists and applies the recommended state.
- [ ] `SKVN Header Page` preset is deferred until header implementation is approved.
- [ ] Marketing editors do not need to use GeneratePress `Disable Elements -> Content Title` for SKVN-owned pages.
- [ ] Marketing editors do not need to use GeneratePress `Content Container = Full Width` for SKVN-owned full-width pages.
- [ ] Direct preview of Footer page does not duplicate the site footer under itself.
- [ ] Direct preview of Header page, when implemented, does not duplicate site header above itself.
- [ ] No GeneratePress parent files are edited.
- [ ] No raw class, raw CSS, or raw GeneratePress meta entry is required from editors.
- [ ] V3 standalone audit lists every remaining GeneratePress-specific dependency before removal.
