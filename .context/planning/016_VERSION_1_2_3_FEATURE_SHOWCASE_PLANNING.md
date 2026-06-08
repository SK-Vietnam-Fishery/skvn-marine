# Version 1.2.3 — SKVN Feature Showcase Planning

Status: implemented; onsite QA pending
Created: 2026-06-08
Human direction: activate implementation in 1.2.3

## Purpose

Create an editorial `Feature Showcase` experience inspired by the local
accordion artifacts, without expanding the active 1.2.1 Slider preset scope.

The block should support a premium feature section with:

- fixed intro/copy surface
- expanding image panels on desktop
- intentionally split mobile layout: intro first, panel rail second
- editable panel images and text
- no dependency on Tailwind, CDN scripts, or raw arbitrary classes

## Source Artifacts

Reference only:

- `.local/test-artifacts/Accordion/gemini-code-1780894501520.html`
- `.local/test-artifacts/Accordion/gemini-code-1780894221603.html`
- Screenshots captured by human on 2026-06-08 showing desktop and mobile states

The artifact is not production source. It contains Tailwind CDN and inline
prototype CSS that must be translated into plugin-owned CSS and editor controls.

## Approved Name

User-facing block title:

```text
SKVN Feature Showcase
```

Block slug:

```text
skvn-marine/feature-showcase
```

## Implementation Direction

Build as a separate SKVN-owned block, not as the existing `SKVN Accordion`.

Reasons:

- Existing Accordion is a content accordion with heading/panel semantics.
- Feature Showcase is an editorial visual component with image panels.
- It needs a different saved structure and responsive behavior.
- Naming it Accordion would confuse editors and future QA.

## Activated Code

Initial source was drafted during 1.2.1, parked, then activated when V1 / 1.2.3
started:

- `wp-content/plugins/skvn-marine-blocks/src/feature-showcase/block.json`
- `wp-content/plugins/skvn-marine-blocks/src/feature-showcase/edit.tsx`
- `wp-content/plugins/skvn-marine-blocks/src/feature-showcase/save.tsx`
- `wp-content/plugins/skvn-marine-blocks/src/feature-showcase/style.css`

Activation completed:

- metadata is named `block.json`
- `src/index.ts` imports/registers the block
- `style.css` is included in the editor/frontend style bundle
- `build/feature-showcase/block.json` is emitted by the build

## Responsive Contract

Desktop:

- two-column layout
- intro/copy on the left
- panel group on the right
- last panel open by default
- hover and keyboard focus can expand another panel

Mobile:

- split into two stacked surfaces
- intro/copy becomes a full-width section
- panel rail appears after intro
- rail does not depend on hover
- vertical labels remain visible as a deliberate mobile visual state

## Accessibility Contract

- Do not rely on hover only.
- Keyboard focus must reveal panel content or provide an equivalent accessible
  path.
- Respect `prefers-reduced-motion`.
- Images require editable alt text or safe empty alt when decorative.
- Mobile content must remain readable without fine pointer hover.

## Non-Scope For 1.2.3

- Dynamic product/post queries
- Arbitrary panel counts beyond the approved MVP count
- Raw Tailwind utility input
- Raw CSS input
- Free absolute layer editor
- Replacing the existing Accordion block
- Shipping Tailwind CDN or external image dependencies

## Acceptance Draft

- [x] Human approves activating the parked block source.
- [x] `SKVN Feature Showcase` appears under `SKVN Marine`.
- [x] Block inserts useful editable sample content.
- [x] Intro fields are editable.
- [x] Four panel labels, headings, copy, and images are editable.
- [x] Desktop expanding panel behavior supports hover and keyboard focus in source.
- [x] Mobile uses the split intro plus panel rail state in source.
- [x] Reduced-motion users do not receive forced panel animation.
- [x] No Tailwind CDN, raw class input, or raw CSS input is required.
- [x] Existing `SKVN Accordion` behavior remains unchanged.
- [x] Plugin build passes.
- [x] Onsite QA target is documented before milestone completion.
