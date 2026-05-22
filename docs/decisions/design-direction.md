# SKVN Marine Design Direction

## Current Decision

Use a blue-first system with soft mint as the main support accent.

Core colors:

- Navy: `#082f49`
- Primary blue: `#0369a1`
- Soft blue: `#f0f9ff`
- Support mint: `#ddfaf4`
- Premium gold: `#e9c766`

## Usage Rules

Use blue for primary structure:

- Header and footer surfaces
- Hero headings
- Primary buttons
- Important navigation and B2B trust signals

Use mint as the main accent:

- Support cards
- Trust strip icons
- Factory/process/QA sections
- Form and newsletter support surfaces
- Freshness, hygiene, cold-chain, and operational clarity cues

Use gold only as a small premium cue:

- Certification highlights
- Premium species badges
- Buyer-value callouts
- Black-label or top-grade labels

Do not use gold as a dominant background. Do not use mint for the primary CTA.

## Reason

Mint fits the seafood category better for the main support color because it reads clean, fresh, cold, and process-oriented. Gold is still useful, but only in small places where the page needs a premium signal.

## Implementation Notes

Theme tokens:

- `skvn-mint-100`: `#ddfaf4`
- `skvn-gold-300`: `#e9c766`

CSS variables:

- `--skvn-accent-support: var(--skvn-color-mint-100)`
- `--skvn-accent-premium: var(--skvn-color-gold-300)`

Primary CTA remains blue:

- `--skvn-color-blue-700`
