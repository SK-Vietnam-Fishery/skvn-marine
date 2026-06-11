# Full-Width Alignfull Overflow Test

## Target

- Onsite page: `Test Page 3` (record the exact onsite URL with the evidence)
- Surfaces: top-level `.alignfull` blocks and the selected Gutenberg footer page

## Preconditions

- The page uses the `SKVN Full Width` template or SKVN full-width canvas.
- The page is tall enough to show a vertical browser scrollbar.
- The patched child-theme `style.css` is deployed onsite and browser cache is refreshed.

## Steps

1. Open the target page at desktop width with the vertical scrollbar visible.
2. Inspect `.entry-content`, the top-level `.alignfull` page wrapper, nested
   `.alignfull` blocks such as `.skvn-translated-hero`, `.skvn-section`, and
   `.skvn-trust-strip`, plus `.skvn-footer-page` and
   `.skvn-site-footer.alignfull`.
3. Record each element's `getBoundingClientRect()` and `window.innerWidth`.
4. Repeat at tablet and mobile widths.
5. Check the page for horizontal scrolling.

## Expected

- Direct full-width blocks and the custom footer start at `x = 0`.
- Nested legacy `.alignfull` blocks do not receive GeneratePress negative
  viewport margins.
- Their right edge does not exceed `document.documentElement.clientWidth`.
- No block uses the scrollbar-inclusive `100vw` width.
- Normal blocks remain constrained to the content width.
- `.alignwide` blocks remain constrained to the SKVN wide width.

## Pass/Fail

- **Pass:** no horizontal overflow, nested legacy `.alignfull` geometry stays
  inside its owning canvas, and normal/wide/full alignment semantics remain
  distinct.
- **Fail:** any tested element has negative `x`, extends beyond the client width, or creates horizontal scrolling.

## Evidence

- Desktop, tablet, and mobile screenshots.
- Geometry output for the tested selectors.
- Browser console notes and cache status.
