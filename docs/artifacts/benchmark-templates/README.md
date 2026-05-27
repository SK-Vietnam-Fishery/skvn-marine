# SKVN Theme Benchmark Templates

Status: test fixtures.
Purpose: benchmark the theme visual contract with Gutenberg-safe content.

These files are not PHP templates. They are Gutenberg Code Editor fixtures that can be pasted into test pages.

Known render issues and the full-width control discussion are tracked in `render-notes.md`.

Rules:

- No raw Tailwind CDN.
- No raw `<style>`.
- No raw `<script>`.
- No hardcoded header/footer.
- Use implemented `skvn-*` classes only.
- Replace placeholder images before production use.

## Files

| File | Purpose |
|---|---|
| `001-full-width-canvas.gutenberg.html` | Tests full-width canvas, hero, KPI strip, split section, and long CTA text |
| `002-buttons-cards-typography.gutenberg.html` | Tests buttons, hover surfaces, cards, headings, text wrapping, and trust strip |
| `003-online-page-candidate.gutenberg.html` | Tests a realistic online page flow for seafood export content |

## How To Use

1. Create a test page.
2. Open Gutenberg Code Editor.
3. Paste one fixture.
4. Use SKVN Page Display controls:
   - Hide site header: optional
   - Hide site footer: optional
   - Hide page title: recommended for full-page tests
   - Full width canvas: recommended
5. Check desktop and mobile.

## Pass Criteria

- No horizontal overflow.
- CTA remains visible on mobile.
- Text does not overlap.
- Buttons do not show wrapper-background artifacts.
- Full-width sections reach the viewport edge.
- Inner content remains visually constrained.
- Editor preview remains usable.
