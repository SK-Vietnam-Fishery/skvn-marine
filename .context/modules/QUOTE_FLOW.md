# QUOTE FLOW

## [manual] Decision

Use same-site Request a Quote page.

Quote UI/page surface is deferred to V1 / 0.6.0.

CF7/CFDB7 implementation is deferred until after 0.6.0.

n8n automation is deferred until after version 1.0.0.

## [manual] Stack

- 0.6.0: visual quote path, request quote page surface, CTA styling, editor/sidebar-controlled UI.
- After 0.6.0: Contact Form 7 + CFDB7.
- After 1.0.0: n8n webhook automation.

## [manual] Flow

0.6.0:

Product CTA → `/request-a-quote/?product_id=123` → same-site quote UI/page surface.

Future:

Product CTA → `/request-a-quote/?product_id=123` → CF7 → CFDB7 → thank-you page → n8n after 1.0.0.

## [manual] Cache Rule

Do not cache request quote or thank-you pages.
