# QUOTE FLOW

## [manual] Decision

Use same-site Request a Quote page.

Quote UI/page surface was completed in V1 / 0.6.0.

Current 0.7.0 scope is basic CF7/CFDB7 quote form implementation.

Onsite hidden/context field and full UX smoke test debt is deferred to V1 / 0.10.0.

n8n automation is deferred until after version 1.0.0.

## [manual] Stack

- 0.6.0: visual quote path, request quote page surface, CTA styling, editor/sidebar-controlled UI.
- 0.7.0: Contact Form 7 + CFDB7.
- 0.10.0: onsite hidden/context field and UX smoke test debt resolution.
- After 1.0.0: n8n webhook automation.

## [manual] Flow

0.6.0:

Product CTA → `/request-a-quote/?product_id=123` → same-site quote UI/page surface.

0.7.0:

Product CTA → `/request-a-quote/?product_id=123` → CF7 → CFDB7 → thank-you page.

Future after 1.0.0:

CFDB7/CF7 submission → n8n automation.

## [manual] Cache Rule

Do not cache request quote or thank-you pages.
