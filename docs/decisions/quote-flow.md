# Request a Quote Flow

## Decision

The Request a Quote flow stays on the same website.

Do not redirect users to a separate contact site in V1.

Human scope update on 2026-05-26:

- 0.5.1 focuses on page display/sidebar controls.
- 0.6.0 focuses on quote UI and editor/sidebar-controlled visual editing.
- CF7/CFDB7 implementation is deferred until after 0.6.0.
- n8n automation is deferred until after version 1.0.0.

## 0.6.0 Flow

```txt
Product page / Product grid
→ Request a Quote CTA
→ /request-a-quote/?product_id=123
→ Same-site quote UI/page surface
```

## Future Flow

```txt
Product page / Product grid
→ Request a Quote CTA
→ /request-a-quote/?product_id=123
→ Contact Form 7 form
→ CFDB7 stores submission
→ /quote-thank-you/
→ n8n webhook processes lead after 1.0.0
```

## Future Recommended Form Fields

Required:

- Full name
- Company name
- Email
- Country
- Product interest
- Quantity / estimated volume
- Message

Optional:

- Phone / WhatsApp
- Destination port
- Packaging requirement
- Certification requirement

Hidden/context fields:

- product_id
- product_sku
- product_name
- product_url
- source_url
- utm_source
- utm_medium
- utm_campaign
- utm_content
- utm_term

## Caching

Cache product pages.

Do not cache:

- `/request-a-quote/`
- `/quote-thank-you/`
- CF7 AJAX endpoints if caching causes issues

## V2/V3 Ideas

- Quote modal on desktop
- Multi-product quote form
- Add-to-quote basket
- CRM integration
- Sales dashboard
