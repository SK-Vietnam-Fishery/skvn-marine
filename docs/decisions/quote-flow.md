# Request a Quote Flow

## Decision

The Request a Quote flow stays on the same website.

Do not redirect users to a separate contact site in V1.

Human scope update on 2026-05-26:

- 0.5.1 focuses on page display/sidebar controls.
- 0.6.0 focuses on quote UI and editor/sidebar-controlled visual editing.
- 0.7.0 focuses on basic CF7/CFDB7 quote form implementation.
- 0.10.0 closed onsite UI/test debt for footer and the CF7 Request Quote interface.
- 1.1.2 resolves remaining quote data-flow evidence: product/page block origin, form submission, CFDB7 storage, hidden/context fields, and thank-you behavior.
- 1.1.2 also checks the map block/display surface because the current onsite map is not viewable.
- n8n automation is deferred until after version 1.0.0.
- 1.4.0 is reserved for a deeper discussion of an wp-admin "SKVN Theme Init Setup UI" that can load reviewed setup templates from Admin.

## 0.6.0 Flow

```txt
Product page / Product grid
→ Request a Quote CTA
→ /request-a-quote/?product_id=123
→ Same-site quote UI/page surface
```

## 0.7.0 Flow

```txt
Product page / Product grid
→ Request a Quote CTA
→ /request-a-quote/?product_id=123
→ Contact Form 7 form
→ CFDB7 stores submission
→ /quote-thank-you/
```

## Future After 1.0.0

```txt
CF7 / CFDB7 submission
→ n8n webhook processes lead
```

## Future 1.4.0 Admin Setup UI Candidate

The admin setup UI idea is deferred to milestone 1.4.0.

Initial wireframe:

```text
Admin sidebar
└── SKVN Theme init setup
    ├── [Request A Quote Workflow]
    ├── [Future setup card]
    ├── [Future setup card]
    └── [Nạp setup]
```

Intent:

- Give site admins a reviewed wp-admin screen for loading setup templates.
- Reduce reliance on WP-CLI for repeated setup tasks.
- Keep future onsite testing possible through normal WP Admin steps.

Constraints:

- Do not build this UI before 1.4.0.
- Discuss exact UX, permissions, idempotency, rollback, and audit behavior before coding.
- The Request A Quote setup may create/update CF7 markup and WordPress pages, but must not replace CF7 handling with a custom PHP form handler.
- n8n remains out of scope here unless a later human-approved milestone moves it into scope.

## 0.7.0 Recommended Form Fields

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
