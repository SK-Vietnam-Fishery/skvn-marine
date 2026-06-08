# Feature Showcase — 1.2.3 Decision

Date: 2026-06-08
Status: implemented; onsite QA pending
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

## Activation Decision

Initial source existed in `src/feature-showcase/` and was parked during V1 /
1.2.1. It was activated when V1 / 1.2.3 started.

For V1 / 1.2.3:

- the block is registered from `src/index.ts`
- it appears under the `SKVN Marine` Block Inserter category
- metadata is named `block.json`
- generated build metadata is emitted at `build/feature-showcase/block.json`

The V1 / 1.2.1 parking decision is archived in tension history.

## Implementation Rules

- Use plugin-owned TypeScript and CSS.
- Do not use Tailwind CDN.
- Do not save raw Tailwind classes as production content.
- Do not expose raw CSS or arbitrary class input to editors.
- Preserve `prefers-reduced-motion`.
- Support keyboard focus, not only hover.
- Keep existing Accordion behavior unchanged.

## Implementation Source

Planning source:

```text
.context/planning/016_VERSION_1_2_3_FEATURE_SHOWCASE_PLANNING.md
```

Onsite QA:

```text
docs/testing/onsite-feature-showcase-1.2.3.md
```
