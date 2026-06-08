# Feature Showcase — 1.2.3 Decision

Date: 2026-06-08
Status: planned; source parked
Milestone: V1 / 1.2.3

## Decision

The artifact-inspired expanding visual component will be named:

```text
SKVN Feature Showcase
```

It will be implemented as a separate block:

```text
skvn-marine/feature-showcase
```

It will not be implemented by changing the existing `SKVN Accordion` block.

## Rationale

The reference artifact behaves like a premium editorial showcase, not a normal
FAQ/content accordion. It combines intro copy, image panels, vertical labels,
and desktop expansion behavior. Keeping it separate prevents the existing
Accordion contract from becoming visually overloaded.

The mobile "broken" state from the artifact is useful as an intentional
responsive design:

- intro/copy becomes its own full-width section
- image labels become a compact panel rail
- the layout avoids forcing hover-only behavior onto mobile users

## Parking Decision

Initial source exists in `src/feature-showcase/`, but it is parked until V1 /
1.2.3.

For V1 / 1.2.1:

- the block must not be registered from `src/index.ts`
- it must not appear in the Block Inserter
- its metadata must stay named `block.parked.json`, not `block.json`, because
  the build pipeline auto-copies `block.json` files under `src/`
- generated build metadata for the block must not be shipped as active runtime
  metadata

This preserves the implementation draft without expanding the active 1.2.1
milestone.

## Implementation Rules

- Use plugin-owned TypeScript and CSS.
- Do not use Tailwind CDN.
- Do not save raw Tailwind classes as production content.
- Do not expose raw CSS or arbitrary class input to editors.
- Preserve `prefers-reduced-motion`.
- Support keyboard focus, not only hover.
- Keep existing Accordion behavior unchanged.

## Future Activation

When V1 / 1.2.3 becomes active, review the parked source against this decision
and the planning file before registering the block.

Activation starts by renaming:

```text
wp-content/plugins/skvn-marine-blocks/src/feature-showcase/block.parked.json
-> wp-content/plugins/skvn-marine-blocks/src/feature-showcase/block.json
```

Planning source:

```text
.context/planning/016_VERSION_1_2_3_FEATURE_SHOWCASE_PLANNING.md
```
