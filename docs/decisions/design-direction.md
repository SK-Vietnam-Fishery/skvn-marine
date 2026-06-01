# SKVN Marine Design Direction

## Current Decision

Use a blue-first system with soft mint as the main support accent.

Core colors after the 0.5.1 visual hotfix:

- Deep navy: `#073b5a`
- Trust blue: `#0f5c8c`
- Primary blue: `#1e79be`
- Fresh sky: `#eaf7ff`
- Support mint: `#ddfaf4`
- Premium gold: `#e9c766`
- Text slate: `#0f172a`

## Usage Rules

Use blue for primary structure:

- Header and footer surfaces
- Hero headings
- Primary buttons
- Important navigation and B2B trust signals

Use fresh sky and white for first-viewport surfaces:

- Translated artifact hero sections
- Light campaign/page introductions
- Alternating page sections that should read fresh and food-safe

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

Do not use gold as a dominant background. Do not use mint for the primary CTA. Do not use deep navy as a full hero background unless the page has a specific approved campaign reason.

## Reason

The 0.5.1 visual hotfix lightens the first viewport after stakeholder feedback that the navy translated hero felt too dark. Mint still fits the seafood category as the support color because it reads clean, fresh, cold, and process-oriented. Gold is still useful, but only in small places where the page needs a premium signal.

## Implementation Notes

Theme tokens:

- `skvn-blue-950`: `#073b5a`
- `skvn-blue-900`: `#0f5c8c`
- `skvn-blue-700`: `#1e79be`
- `skvn-sky-50`: `#eaf7ff`
- `skvn-mint-100`: `#ddfaf4`
- `skvn-gold-300`: `#e9c766`
- `skvn-slate-700`: `#0f172a`

CSS variables:

- `--skvn-accent-support: var(--skvn-color-mint-100)`
- `--skvn-accent-premium: var(--skvn-color-gold-300)`

Primary CTA remains blue:

- `--skvn-color-blue-700`

Brand profile/sidebar color controls remain V1 / 0.7.0 scope. This hotfix updates existing theme tokens and translated hero surfaces only; it does not introduce a new token system or editor UI.
