# Version 1.2.3 — SKVN Feature Showcase Planning

Status: planned
Created: 2026-06-08
Human direction: park implementation until 1.2.3

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

## Parked Code

Initial source was drafted during 1.2.1 and intentionally parked:

- `wp-content/plugins/skvn-marine-blocks/src/feature-showcase/block.parked.json`
- `wp-content/plugins/skvn-marine-blocks/src/feature-showcase/edit.tsx`
- `wp-content/plugins/skvn-marine-blocks/src/feature-showcase/save.tsx`
- `wp-content/plugins/skvn-marine-blocks/src/feature-showcase/style.css`

For 1.2.1 it must remain inactive:

- do not import/register it from `src/index.ts`
- do not ship `build/feature-showcase/block.json`
- do not include its CSS in `style-index.ts.css`

Parking note:

- The metadata file is named `block.parked.json` during 1.2.1 because the
  WordPress build pipeline auto-copies every `block.json` under `src/`.
- When 1.2.3 starts, rename it back to `block.json`, import/register the block
  from `src/index.ts`, then rebuild.

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

- [ ] Human approves activating the parked block source.
- [ ] `SKVN Feature Showcase` appears under `SKVN Marine`.
- [ ] Block inserts useful editable sample content.
- [ ] Intro fields are editable.
- [ ] Four panel labels, headings, copy, and images are editable.
- [ ] Desktop expanding panel behavior works with hover and keyboard focus.
- [ ] Mobile uses the split intro plus panel rail state.
- [ ] Reduced-motion users do not receive forced panel animation.
- [ ] No Tailwind CDN, raw class input, or raw CSS input is required.
- [ ] Existing `SKVN Accordion` behavior remains unchanged.
- [ ] Plugin build passes.
- [ ] Onsite QA target is documented before milestone completion.
