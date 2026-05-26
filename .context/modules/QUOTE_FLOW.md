# QUOTE FLOW

## [manual] Decision

Use same-site Request a Quote page.

Current 0.5.1 scope is quote UI and editor controls only.

CF7/CFDB7 implementation is deferred to the next milestone after 0.5.1.

n8n automation is deferred until after version 1.0.0.

## [manual] Stack

- 0.5.1: visual quote path, request quote page surface, CTA styling, editor/sidebar-controlled UI.
- Next milestone after 0.5.1: Contact Form 7 + CFDB7.
- After 1.0.0: n8n webhook automation.

## [manual] Flow

0.5.1:

Product CTA → `/request-a-quote/?product_id=123` → same-site quote UI/page surface.

Future:

Product CTA → `/request-a-quote/?product_id=123` → CF7 → CFDB7 → thank-you page → n8n after 1.0.0.

## [manual] Cache Rule

Do not cache request quote or thank-you pages.
