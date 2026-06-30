> Archived: 2026-06-30 — prefix giữ nguyên, không renumber.
> Decision: docs/decisions/feature-showcase-1.2.3.md
> Onsite QA defer: 1.2.9 (carried)

# Version 1.2.3 — SKVN Feature Showcase Planning

Status: milestone complete; onsite QA carried to V1 / 1.2.9
Created: 2026-06-08
Human direction: activate implementation in 1.2.3

## Purpose

Create a configurable `Feature Showcase` panel group inspired by the local
accordion artifacts, without expanding the active 1.2.1 Slider preset scope.

The block should support a reusable panel/card set with:

- expanding image panels on desktop
- mobile progressive disclosure: compact headers first, focused/tapped card body
  second
- configurable item count
- configurable desktop direction
- editable panel images and text
- no dependency on Tailwind, CDN scripts, or raw arbitrary classes

The surrounding B2B editorial text stack is not part of the block. It belongs to
a pattern/composition documented in:

```text
docs/artifacts/page-blueprints/b2b-seafood-editorial-showcase-pattern.md
```

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
- Feature Showcase is a governed visual panel group with image cards.
- It needs a different saved structure and responsive behavior.
- Naming it Accordion would confuse editors and future QA.
- The B2B text stack should be supplied by a pattern, not by required block
  attributes.

## Activated Code

Initial source was drafted during 1.2.1, parked, then activated when V1 / 1.2.3
started:

- `wp-content/plugins/skvn-marine-blocks/src/feature-showcase/block.json`
- `wp-content/plugins/skvn-marine-blocks/src/feature-showcase/edit.tsx`
- `wp-content/plugins/skvn-marine-blocks/src/feature-showcase/save.tsx`
- `wp-content/plugins/skvn-marine-blocks/src/feature-showcase/deprecated.tsx`
- `wp-content/plugins/skvn-marine-blocks/src/feature-showcase/style.css`
- `wp-content/plugins/skvn-marine-blocks/src/feature-showcase/types.ts`

Activation completed:

- metadata is named `block.json`
- `src/index.ts` imports/registers the block
- `style.css` is included in the editor/frontend style bundle
- `build/feature-showcase/block.json` is emitted by the build

## Responsive Contract

Desktop:

- panel group only
- horizontal or vertical direction
- configurable default open panel
- hover and keyboard focus can expand another panel

Mobile:

- compact horizontal card headers by default
- focused/tapped card reveals image and body content
- mobile must not depend on hover
- editor can hide the group on mobile
- a future carousel behavior remains deferred because it would add a separate
  runtime concern

Saved markup:

- native `details`/`summary` provides no-JS disclosure and keyboard semantics
- a minimal block-local controller enforces exactly one active panel when
  JavaScript is available
- clicking the active/only panel cannot leave the showcase with no visible body
- desktop presentation changes through governed classes, not separate markup
- legacy 1.2.3 markup has a Gutenberg deprecation and compatibility CSS

## Accessibility Contract

- Do not rely on hover only.
- Keyboard focus must reveal panel content or provide an equivalent accessible
  path.
- Respect `prefers-reduced-motion`.
- Images require editable alt text or safe empty alt when decorative.
- Mobile content must remain readable without fine pointer hover.
- If semantic `details`/`summary` is used, styling must preserve keyboard
  accessibility and no-JS readability.

## Non-Scope For 1.2.3

- Dynamic product/post queries
- Free absolute layer editor
- Raw Tailwind utility input
- Raw CSS input
- Replacing the existing Accordion block
- Shipping Tailwind CDN or external image dependencies

## Acceptance Draft

- [x] Human approves activating the parked block source.
- [x] `SKVN Feature Showcase` appears under `SKVN Marine`.
- [x] Block inserts useful editable sample content.
- [x] Block contract is refactored to panel-only, with no required intro/meta
  text stack.
- [x] Panel count can be changed by editors.
- [x] Desktop layout direction can be changed by editors.
- [x] Panel labels, headings, copy, and images are editable.
- [x] Desktop expanding panel behavior supports hover and keyboard focus in source.
- [x] Mobile uses compact headers with focus/tap reveal behavior.
- [x] Reduced-motion users do not receive forced panel animation.
- [x] No Tailwind CDN, raw class input, or raw CSS input is required.
- [x] Existing `SKVN Accordion` behavior remains unchanged.
- [x] Plugin build passes.
- [x] Onsite QA target is documented before milestone completion.
