# Meta AI Tailwind Artifacts Review

Status: working review.
Reviewed: 2026-05-27.
Scope: `.local/test-artifacts/` HTML artifacts generated or translated from Meta AI/Tailwind experiments.

## Purpose

This review turns local Meta AI/Tailwind artifacts into a controlled SKVN Marine input backlog.

These files are useful as layout references, but they are not production content. Raw Tailwind HTML, inline scripts, inline styles, prototype colors, headers, and footers must not be pasted directly into Gutenberg pages.

## Source Files

| File | Type | Use |
|---|---|---|
| `.local/test-artifacts/Chuyen-trang-Tailwind-HTML*.html` | Raw Tailwind prototypes | Visual/layout moodboard only |
| `.local/test-artifacts/HTML-2-Gu-Test-01.html` | Raw Tailwind business landing page | Section structure reference |
| `.local/test-artifacts/HTML-2-Gu-Test-01.gutenberg.html` | Translated Gutenberg draft | Contract review, not paste-ready yet |
| `.local/test-artifacts/HTML-2-Gu-Test-01.gutenberg-native.html` | More native Gutenberg draft | Best candidate for future pattern work |
| `.local/test-artifacts/HTML-2-Gu-Test-01.translation.md` | Translation report | Keep as the preferred report format |
| `.local/test-artifacts/pattern-ui-test-content.html` | Existing SKVN pattern test content | Paste-ready baseline; all checked classes exist |

Note: filenames containing Vietnamese accents remain local-only artifacts. The table uses simplified names for readability.

## What Is Worth Keeping

### 1. Landing Page Flow

The strongest reusable idea is the page sequence:

```text
Hero
-> KPI / trust strip
-> Product or service cards
-> Factory / cold-chain / process split
-> Certification / buyer trust strip
-> Quote CTA
```

For SKVN Marine, this should become seafood/export-specific content instead of generic agency/business copy.

### 2. Hero Rhythm

Useful cues:

- Strong dark or image-backed first viewport.
- Small eyebrow above the headline.
- Short lead paragraph.
- Two clear CTAs.
- Stable full-width layout with constrained inner content.

SKVN target:

```text
Eyebrow: Est. 2018 - Binh Dinh, Vietnam
Headline: Frozen seafood export / trusted seafood manufacturer
Lead: capacity, certification, cold-chain, buyer confidence
CTA: Request Quotation / Explore Products
```

### 3. KPI Cards

Counter sections are useful as static KPI blocks. Do not ship the prototype counter JavaScript in Gutenberg content.

Possible SKVN KPIs:

- Years experience.
- Monthly capacity.
- Product categories.
- Export markets.
- Certification/quality checks.

### 4. Cards And Trust Sections

The service/card grid, logo grid, and testimonial grid are structurally useful, but should map to SKVN content:

- Service cards -> product categories or export capabilities.
- Logo grid -> certifications, buyer trust, export markets, or quality programs.
- Testimonials -> only if real buyer proof exists; otherwise omit or replace with "Why buyers work with us".

### 5. Translation Report Format

`HTML-2-Gu-Test-01.translation.md` is valuable because it records:

- required classes
- theme CSS contract
- animation contract
- assets needed
- not translated
- risks

This report format matches the project HTML-2-Gutenberg workflow and should be reused.

## What Must Not Ship

- Tailwind CDN: `https://cdn.tailwindcss.com`.
- Raw Tailwind utility-heavy class trees as the production contract.
- Raw `<style>` or `<script>` in Gutenberg content.
- Prototype mobile menus, sticky headers, or pasted footers.
- Counter, scroll, or sticky-header JavaScript from artifacts.
- Prototype colors such as dominant indigo/violet/purple systems.
- Generic agency/business copy.
- External placeholder images as final production assets.
- Inline SVG icon sets unless converted to a theme/icon contract.

## Class Contract Audit

### Paste-Ready Baseline

`pattern-ui-test-content.html` is the cleanest baseline. Its checked `skvn-*` classes are already implemented in theme CSS:

```text
is-style-skvn-primary
is-style-skvn-rounded-media
skvn-newsletter-band
skvn-newsletter-band__inner
skvn-newsletter-media
skvn-newsletter-media--overhang
skvn-pattern-hero
skvn-pattern-hero__eyebrow
skvn-pattern-hero__grid
skvn-pattern-hero__media
skvn-pattern-test-page
skvn-section
skvn-trust-strip
skvn-trust-strip__grid
skvn-trust-strip__icon
skvn-trust-strip__item
```

### Useful But Not Paste-Ready

`HTML-2-Gu-Test-01.gutenberg.html` and `HTML-2-Gu-Test-01.gutenberg-native.html` use some implemented classes:

```text
skvn-button
skvn-button--primary
skvn-button--secondary
skvn-card
skvn-section
skvn-section__eyebrow
skvn-section__heading
skvn-section__lead
```

They also introduce missing layout-critical class families:

```text
skvn-hero*
skvn-card-grid*
skvn-logo-grid / skvn-logo-columns
skvn-logo-card*
skvn-service-card / skvn-service-columns
skvn-cta-band*
skvn-split*
skvn-testimonial-grid / skvn-testimonial-columns
skvn-testimonial-card*
skvn-check-list
skvn-motion-reveal
```

These must not be treated as production-ready until either:

1. They are mapped back to existing implemented families such as `skvn-translated-*`, `skvn-kpi-strip*`, `skvn-section__*`, `skvn-card`, and `skvn-button*`.
2. Or the missing class families are implemented in the theme CSS in a future approved task.

## Recommended Mapping

| Artifact concept | SKVN/Gutenberg target |
|---|---|
| Raw full-screen hero | `core/group alignfull` + `skvn-translated-hero` |
| Hero two columns | `core/columns` + `skvn-translated-hero__grid` |
| Eyebrow | `skvn-translated-hero__eyebrow` or `skvn-section__eyebrow` |
| KPI/stat cards | `skvn-kpi-strip` + `skvn-kpi-strip__grid` |
| Generic services | Product categories / export capabilities |
| Generic logo grid | Certifications / export markets / buyer trust |
| Generic testimonials | Real buyer proof only, otherwise replace |
| CTA band | Quote CTA section for 0.6.0 |
| Reveal animation | Shared theme animation runtime only; no page script |

## Milestone Backlog

### V1 / 0.5.1

- Keep this review as documentation only.
- Do not implement new section families.
- Do not paste raw Tailwind output into pages.
- Use the review to validate HTML-2-Gutenberg workflow quality.

### V1 / 0.6.0

Use the layout recipe for Quote UI:

```text
Request quote intro
-> product/category context
-> buyer trust/certification strip
-> quote CTA surface
-> contact/next step explanation
```

Candidate implementation should use existing classes first:

```text
skvn-translated-hero
skvn-kpi-strip
skvn-translated-split
skvn-card
skvn-button
```

### V1 / 0.7.0

If visual parity requires it, implement a theme-owned section family backlog:

```text
skvn-card-grid
skvn-logo-grid
skvn-cta-band
skvn-check-list
skvn-testimonial-card
```

Token work belongs here if the artifact exposes missing visual tokens for radius, shadow, surface, section spacing, or proof/certification treatments.

## Recommended Next Action

Create SKVN-specific Gutenberg patterns derived from the safe layout recipe, not from raw Tailwind:

```text
docs/artifacts/benchmark-templates/
docs/artifacts/page-blueprints/
```

Before implementation:

1. Replace all generic agency copy.
2. Use media-library-ready images.
3. Reuse existing `skvn-*` families.
4. List missing classes if visual parity requires new theme CSS.
5. Keep quote form behavior deferred according to current milestone rules.
