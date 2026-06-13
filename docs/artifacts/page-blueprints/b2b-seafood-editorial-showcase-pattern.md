# B2B Seafood Editorial Showcase Pattern

Status: implemented as WordPress pattern
Created: 2026-06-08
Related milestone: V1 / 1.2.3

Implementation:

```text
wp-content/themes/skvn-marine/patterns/b2b-seafood-feature-showcase.php
```

## Purpose

Preserve the useful composition from the first Feature Showcase draft without
making the text stack part of the Feature Showcase block contract.

This pattern combines:

- a B2B seafood editorial text stack
- a `SKVN Feature Showcase` panel-only block

The typography treatment is documented in:

```text
docs/decisions/b2b-seafood-editorial-typography-1.2.3.md
```

## Composition

```text
Core Group / section
└── Core Columns or Group layout
    ├── Text column
    │   ├── Eyebrow paragraph
    │   ├── Display heading with one accent word
    │   ├── Divider
    │   ├── Body paragraph
    │   ├── Compliance label
    │   └── Compliance text
    └── SKVN Feature Showcase
        └── Panel-only block
```

## Example Content

```text
PREMIUM OCEAN CATCH

Global
Seafood
Exporter

Premium wild-caught seafood prepared for demanding buyers in the United States,
Japan, and the European Union.

COMPLIANCE STANDARDS
HACCP - BRC GLOBAL - IFS
```

## Intended Desktop Layout

```text
| Text stack                      | Feature panels                 |
|---------------------------------|--------------------------------|
| PREMIUM OCEAN CATCH            | 01 . OCEAN GROUPER             |
| Global / Seafood / Exporter     | 02 . IQF TUNNEL FREEZING       |
| Body copy + compliance proof    | 03 . BARRAMUNDI                |
|                                 | 04 . EXPORT PROCESSING PLANT   |
```

## Intended Mobile Layout

The text stack may appear above the panel block.

The panel block itself should render as mobile accordion/collapsed card rows:

```text
01 - Ocean Grouper
02 - IQF Tunnel Freezing
03 - Barramundi
04 - Export Processing Plant
     [image]
     [copy]
```

## WordPress Pattern Contract

The implemented WordPress pattern must:

- Use core Group/Columns/Heading/Paragraph blocks for the text stack.
- Use governed SKVN classes or future typography/surface presets.
- Do not duplicate Feature Showcase item data in the text column.
- Do not require raw class input for marketing editors.
- Keep the pattern optional; `SKVN Feature Showcase` must still work alone.
- Use an outer `alignfull` Group with `layout: default`.
- Let the outer Group own the background and section padding.
- Let `.skvn-b2b-showcase-pattern__grid` own wide max-width and centering.
- Do not use a constrained outer Group because it applies `contentSize` to the
  Columns child.

## Non-Goals

- This is not the block schema.
- This is not a dynamic PHP render contract.
- This is not a replacement for Feature Showcase.
- This does not reintroduce fixed intro/meta attributes into the panel block.
