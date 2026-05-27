# Benchmark Render Notes

Status: discussion note.
Reviewed: 2026-05-27.

## Full-Width Controls

The current editing flow exposes more than one way to express the same visual intent:

| Surface | Owner | Current role | Issue |
|---|---|---|---|
| `template-skvn-full-width.php` | SKVN theme | Older 0.5.0 full-width page template | Separate from the newer sidebar toggle, so editors can end up with two SKVN full-width concepts. |
| SKVN Page Display: Full width canvas | SKVN theme | 0.5.1 page-meta toggle using `_skvn_full_width_canvas` | Best candidate for the single editor-facing control, but editor/front-end parity still needs hardening. |
| GeneratePress Layout: Content Container = Full Width | GeneratePress/runtime setting | Parent-theme content container control | Currently still affects the editor preview and can make SKVN canvas tests look broken when left as `Contained`. |

Discussion position:

- Long term, do not ask editors to manage two or three full-width switches.
- Treat **SKVN Page Display > Full width canvas** as the primary source of truth for marketing pages.
- Keep the old SKVN full-width template as legacy/fallback until the sidebar toggle fully covers its behavior.
- Do not edit GeneratePress parent files.
- Do not blindly write GeneratePress meta until its stored meta keys and side effects are audited.

Short-term test rule:

- For benchmark and blueprint QA, enable SKVN **Full width canvas** and set GeneratePress **Content Container** to **Full Width** when checking layout parity.

Preferred implementation direction for later:

1. Harden SKVN full-width canvas CSS and editor CSS so the SKVN toggle alone produces the intended full-width page surface.
2. Decide whether the old `template-skvn-full-width.php` should become legacy-only or be internally mapped to the same CSS contract.
3. Only consider syncing GeneratePress layout meta after a runtime audit confirms the exact meta keys and no unwanted side effects.

## Render Issues Observed

| Fixture | Status | Evidence | Likely cause | Next discussion/action |
|---|---|---|---|---|
| `docs/artifacts/page-blueprints/seafood-export-home.gutenberg.html` | BROKEN | Frontend trust strip first item is clipped off the left edge. | Full-width wrapper plus `skvn-trust-strip__grid` is not safe under the current page/container combination, or the blueprint depends on a full-width setup that is not enforced by one control. | Re-test with SKVN Full width canvas plus GeneratePress Full Width. If still clipped, fix trust-strip CSS containment before promoting the blueprint. |
| `docs/artifacts/page-blueprints/seafood-export-home.gutenberg.html` | NEEDS_SETUP | GeneratePress footer is visible in visual QA screenshots. | Page display hide-footer toggle is not part of the fixture content; it depends on page meta. | Document expected page settings for benchmark pages, or add a test checklist row before approval. |
| `docs/artifacts/page-blueprints/request-quote-intro.gutenberg.html` | BROKEN_IN_EDITOR | Editor preview stays narrow when SKVN Full width canvas is enabled but GeneratePress Content Container remains `Contained`. | Two full-width controls are competing; SKVN toggle does not yet fully control editor canvas width. | Make SKVN toggle the editor-facing source of truth; add editor parity work to the 0.5.1 backlog before milestone completion. |
| `docs/artifacts/page-blueprints/request-quote-intro.gutenberg.html` | BROKEN_FRONTEND | Lower quote placeholder/newsletter band clips content off the left side; media block sits too far right. | `skvn-newsletter-band` overhang layout is unsafe for this blueprint and full-width setup. It was reused as a visual shortcut, not a dedicated quote-page section. | Replace this section with a safer `skvn-translated-split` or a future quote-intro section contract before using the blueprint online. |
| `docs/artifacts/page-blueprints/request-quote-intro.gutenberg.html` | POLISH_NEEDED | Product/Packing/Delivery cards look too small and visually underweighted. | Core columns plus base `.skvn-card` styling are technically safe but not enough for a polished quote-prep section. | Either tune the blueprint structure with existing classes or define a dedicated card-grid contract in theme CSS later. |

## Acceptance Gate Before Promotion

A benchmark fixture or page blueprint should not be promoted to an online page until:

- Full-width behavior is driven by one documented editor flow.
- Frontend and editor previews are checked with the target page-display settings.
- No section clips horizontally at desktop widths.
- Mobile stacking is checked.
- Header/footer/title visibility expectations are documented per page.
- Any layout-critical `skvn-*` class used by the blueprint is implemented in theme CSS.

