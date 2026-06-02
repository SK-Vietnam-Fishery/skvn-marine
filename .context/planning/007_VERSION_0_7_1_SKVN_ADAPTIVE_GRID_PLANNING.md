# Version 0.7.1 Planning - SKVN Adaptive Grid Contract

> Planning reference for the SKVN-owned adaptive grid contract.
> Load this file when planning adaptive item grids, Gutenberg Columns/Gallery behavior, fixed layout preset governance, or editor-control hiding/labeling for grid layouts.

---

## Status

Status: **PENDING**
Target: **V1 / 0.7.1**

This file does not change the current milestone. Current milestone remains managed by `.context/MILESTONES.md`.

---

## Problem

The project currently has two different layout needs that can look similar in markup but should not behave the same:

1. Gutenberg-native dynamic grids where the editor's Columns setting is the user's source of truth.
2. SKVN-owned responsive layouts where the theme or a future SKVN block controls the layout using adaptive item sizing.

When these two models are mixed, editors see a Columns control but the frontend may ignore it because SKVN CSS overrides the block with `display: grid` and `auto-fit`. That creates a UX bug: the editor setting appears broken even if the visual layout is technically responsive.

---

## Goal

Define a clear contract before implementation:

- Gutenberg-native dynamic grids respect Gutenberg column controls.
- Fixed SKVN presets are clearly theme-controlled and do not expose misleading column-count controls.
- `SKVN Adaptive Grid` is a separate SKVN-owned layout model for auto-responsive item grids.
- Editor controls for the future adaptive grid are planned but not implemented in 0.7.1.

---

## Non-Goals

0.7.1 should not implement:

- A new custom Gutenberg block with sidebar UI.
- A generic page-builder container.
- Arbitrary raw column count controls for adaptive grids.
- Raw class input as the primary editor workflow.
- Inline CSS or raw `<style>` in Gutenberg content.
- Any GeneratePress parent change.

Custom block/editor-control work remains deferred to `0.8.0 - SKVN Editor Controls` unless the human explicitly changes scope.

---

## Ownership

Theme `skvn-marine` owns:

- CSS contract for adaptive grid classes.
- Visual tokens, spacing, density, gap, and responsive behavior.
- Pattern CSS and editor/frontend parity.
- Rules for fixed layout presets that should hide, disable, or label irrelevant column controls.

Plugin `skvn-marine-blocks` owns, when implementation starts in 0.8.0:

- Any custom `SKVN Adaptive Grid` block.
- Sidebar controls and block attributes.
- Saved markup and class composition for the block.
- Editor UI that prevents misleading raw column controls.

Gutenberg-native blocks own:

- Their native attributes and expected behavior.
- Columns count when the block exposes a stable saved class or attribute such as Gallery `columns-N`.

Gutenberg content must not own:

- Raw CSS grid declarations.
- Raw min-width values.
- Raw class strings as the main marketing workflow.

---

## Layout Taxonomy

### 1. Fixed Theme Presets

These are not editor-count grids. Their layout is part of the designed section.

Examples:

- `skvn-stat-grid`: fixed 2x2 stat layout.
- Semantic hero/split/map grids: fixed content/media layouts.
- Footer grid: fixed brand and navigation regions.
- Process layouts with a known process count.

Rules:

- Do not make these follow Gutenberg Columns count.
- Hide or disable column-count controls where possible.
- If controls cannot be hidden in the current milestone, label the pattern or editor surface as theme-controlled.
- Frontend CSS may use fixed `repeat(n, ...)` when the design contract is fixed.

Recommended editor label:

```text
Theme layout controls this grid.
```

### 2. Gutenberg-Native Dynamic Grids

These are core blocks where editors expect core controls to work.

Examples:

- Core Gallery with `columns-N`.
- Core Columns used without SKVN adaptive-grid ownership.
- Future core-list style grids where WordPress exposes stable column count.

Rules:

- Columns setting is source of truth.
- If the editor selects 3 columns, item 4 and item 5 wrap to the next row.
- Theme CSS may polish gap, card styling, image fit, and responsive reduction, but must not silently replace the column count with `auto-fit` on wide screens.

### 3. SKVN Adaptive Grid

This is the planned SKVN-owned auto-responsive grid model.

Rules:

- Source of truth is SKVN preset/density, not raw column count.
- The grid adapts by item min width, container width, and gap.
- It should not expose Gutenberg's raw Columns count as the main control.
- It may expose controlled presets such as density or min item width.

Candidate user-facing name:

```text
SKVN Adaptive Grid
```

Candidate internal CSS family:

```text
skvn-adaptive-grid
skvn-adaptive-grid--compact
skvn-adaptive-grid--balanced
skvn-adaptive-grid--roomy
```

---

## CSS Contract

Initial CSS direction:

```css
.skvn-adaptive-grid {
	display: grid;
	gap: var(--skvn-grid-gap, 1.5rem);
	grid-template-columns: repeat(auto-fit, minmax(var(--skvn-grid-min, 16rem), 1fr));
}

.skvn-adaptive-grid--compact {
	--skvn-grid-gap: 1rem;
	--skvn-grid-min: 12rem;
}

.skvn-adaptive-grid--balanced {
	--skvn-grid-gap: 1.5rem;
	--skvn-grid-min: 16rem;
}

.skvn-adaptive-grid--roomy {
	--skvn-grid-gap: 2rem;
	--skvn-grid-min: 20rem;
}
```

Rules:

- Use tokens/custom properties for gap and min item width.
- Do not encode raw one-off values in Gutenberg content.
- Do not name adaptive classes with fixed-count suffixes such as `--3`.
- If a class name says `--3`, it must mean 3 columns or be renamed.

---

## Gutenberg-Native Contract

Gallery example:

```css
.skvn-portfolio-grid.columns-3 {
	grid-template-columns: repeat(3, minmax(0, 1fr));
}

.skvn-portfolio-grid.columns-4 {
	grid-template-columns: repeat(4, minmax(0, 1fr));
}

.skvn-portfolio-grid.columns-5 {
	grid-template-columns: repeat(5, minmax(0, 1fr));
}
```

Rules:

- Prefer WordPress-generated `columns-N` classes where available.
- Keep responsive reductions explicit and predictable.
- Do not use `auto-fit` on a Gutenberg-native grid if it makes the saved Columns value meaningless.

Core Columns caveat:

- Core Columns markup does not always preserve a stable `columns-N` class in frontend DOM.
- If SKVN needs controlled column counts for card grids, use a clear modifier class such as `skvn-card-grid--3`, `skvn-card-grid--4`, or future editor controls that generate those classes.
- Do not call a class `skvn-card-grid--3` if it renders as adaptive `auto-fit`.

---

## Editor Governance

For fixed theme presets:

- Hide or disable core Columns controls when technically possible.
- If hiding is not feasible, show a label/state that explains the theme controls the grid.
- Do not let editors believe a control affects frontend output when it does not.

For Gutenberg-native dynamic grids:

- Keep core column controls visible.
- Frontend must match the chosen column count where WordPress markup exposes it.

For SKVN Adaptive Grid:

- Hide raw column count.
- Expose controlled presets only.
- Initial candidate controls:
  - Density: Compact / Balanced / Roomy
  - Item width: Small / Medium / Large, if density alone is insufficient
  - Gap: Theme default / Tight / Normal / Spacious

No freeform px inputs in first implementation.

---

## Implementation Phases

### Phase 0.7.1 - Contract and CSS Audit

- Document the layout taxonomy.
- Audit existing `skvn-*grid*` classes and classify them as fixed, Gutenberg-native, or adaptive candidate.
- Rename or correct misleading fixed-count classes if needed.
- Adjust CSS so Gutenberg-native blocks respect saved column count where feasible.
- Keep custom block work out of scope.

### Phase 0.8.0 - Editor Controls or Block

- Decide whether `SKVN Adaptive Grid` is a custom block, a block variation, or a controlled wrapper pattern.
- Implement sidebar controls in `skvn-marine-blocks` if needed.
- Save controlled attributes and class modifiers.
- Ensure editor and frontend parity.

### Phase 1.1.0 - Visual Governance Layer

- Promote adaptive grid as part of the broader pattern governance system if it proves useful across pages.
- Add translator reporting so HTML-2-Gutenberg can decide whether a source layout maps to Gutenberg-native Columns or SKVN Adaptive Grid.

---

## Existing Class Audit Targets

Audit these classes before 0.7.1 implementation:

```text
skvn-card-grid
skvn-card-grid--3
skvn-kpi-strip__grid
skvn-product-card-grid
skvn-portfolio-grid
skvn-feature-grid
skvn-process-grid
skvn-stat-grid
skvn-trust-strip__grid
skvn-product-category-strip__list
```

Initial classification hypothesis:

```text
Fixed theme presets:
- skvn-stat-grid
- skvn-feature-grid
- skvn-process-grid
- semantic hero/split/footer grids

Gutenberg-native dynamic grids:
- skvn-portfolio-grid when used on core/gallery
- skvn-card-grid--N if modifier N is explicit and stable

Adaptive candidates:
- skvn-product-card-grid
- skvn-kpi-strip__grid when used as a variable-count item strip
- future skvn-adaptive-grid
```

This classification must be verified against real Gutenberg DOM before implementation.

---

## HTML-2-Gutenberg Guidance

Translator output should distinguish:

```text
layout_model: fixed-theme-preset | gutenberg-native-columns | skvn-adaptive-grid-candidate
column_source: theme | gutenberg | skvn-preset
editor_control_note: visible | hidden | label-required | deferred
```

Rules:

- Use Gutenberg-native Columns/Gallery when source layout requires explicit editor-selected column count.
- Use `SKVN Adaptive Grid` only when the desired behavior is responsive auto-fitting by item size.
- Report missing CSS rather than inventing new layout-critical classes silently.

---

## Acceptance

- [ ] Planning file exists and is listed in `.context/GLOBAL.md`.
- [ ] Layout taxonomy is documented: fixed presets, Gutenberg-native dynamic grids, SKVN Adaptive Grid.
- [ ] CSS contract for SKVN Adaptive Grid is documented.
- [ ] Gutenberg-native Columns/Gallery source-of-truth rule is documented.
- [ ] Fixed-preset control hiding/labeling rule is documented.
- [ ] Existing grid class audit target list is documented.
- [ ] 0.8.0 editor-control dependency is clearly deferred.
- [ ] GeneratePress parent remains untouched.

