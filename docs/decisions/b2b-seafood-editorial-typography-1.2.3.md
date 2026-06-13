# B2B Seafood Editorial Typography — 1.2.3 Note

Date: 2026-06-08
Status: preserve for reuse
Source: Feature Showcase draft before panel-group refactor

## Decision

Preserve the typography/tone treatment from the mistaken fixed Feature Showcase
draft because it fits SKVN's B2B seafood positioning.

This note preserves the visual language only. It does not preserve the old block
structure with fixed intro/meta plus four panels.

## Treatment

The useful pattern is an editorial dark-panel type system:

```text
Small uppercase eyebrow
Large thin/light heading line
Large bold/accent product/category word
Large thin/light final heading line
Hairline divider
Calm medium-length B2B paragraph
Small uppercase compliance label
Bold compact compliance text
```

Example copy shape:

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

## Visual Attributes

- Dark navy/near-black background.
- Cool blue uppercase eyebrow.
- Large high-contrast display heading.
- One emphasized word in heavier weight and pale blue.
- Wide line-height for body copy.
- Thin divider before body copy.
- Compliance text appears as quiet trust proof, not a CTA.
- Letter spacing is useful only for small uppercase labels, not for body or
  display headings.

## CSS Recipe To Reuse

```css
.skvn-b2b-editorial {
  background: #0d151d;
  color: #f8fbff;
}

.skvn-b2b-editorial__eyebrow,
.skvn-b2b-editorial__meta-label {
  color: #59a8ff;
  font-size: 0.75rem;
  letter-spacing: 0.18em;
  line-height: 1.4;
  text-transform: uppercase;
}

.skvn-b2b-editorial__heading {
  color: #fff;
  font-size: clamp(2.75rem, 5vw, 4.75rem);
  font-weight: 300;
  line-height: 0.98;
}

.skvn-b2b-editorial__heading-accent {
  color: #dcecff;
  font-weight: 700;
}

.skvn-b2b-editorial__copy {
  border-top: 1px solid rgba(148, 163, 184, 0.45);
  color: #a8b6c8;
  font-size: 0.95rem;
  line-height: 1.8;
}

.skvn-b2b-editorial__meta-text {
  color: #a8b6c8;
  font-size: 0.85rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
}
```

## Reuse Guidance

Good future homes:

- A B2B seafood hero/card/panel preset.
- A governed typography preset in V1 / 1.6.0 SKVN Surface Presets.
- A sidebar/intro pattern placed next to Feature Panels.
- A homepage or landing-page trust proof section.

Do not hardwire this treatment into the refactored Feature Panels block. Feature
Panels should remain a flexible panel group; this typography can be offered as a
separate surrounding pattern or future visual preset.
