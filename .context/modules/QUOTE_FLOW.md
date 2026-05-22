# QUOTE FLOW

## [manual] Decision

Use same-site Request a Quote page.

## [manual] Stack

- Contact Form 7
- CFDB7
- n8n webhook

## [manual] Flow

Product CTA → `/request-a-quote/?product_id=123` → CF7 → CFDB7 → n8n → thank-you page.

## [manual] Cache Rule

Do not cache request quote or thank-you pages.
