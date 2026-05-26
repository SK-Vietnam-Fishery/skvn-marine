# Security Guidelines

## PHP

Input must be sanitized:

```php
$product_id = isset($_GET['product_id']) ? absint($_GET['product_id']) : 0;
$sku = isset($_GET['sku']) ? sanitize_text_field(wp_unslash($_GET['sku'])) : '';
```

Output must be escaped:

```php
echo esc_html($title);
echo esc_attr($value);
echo esc_url($url);
echo wp_kses_post($content);
```

## Forms

Do not implement a custom quote form handler.

Current 0.5.1 scope is page display/sidebar controls only. Quote UI moves to 0.6.0.

Future form milestones use:

- Contact Form 7 for form handling
- CFDB7 for database table/submission storage
- n8n webhook for lead automation after 1.0.0

## n8n Webhook

n8n is deferred until after 1.0.0. If/when added, webhook must not be left completely unprotected.

Recommended:

- Hard-to-guess webhook URL
- Optional shared secret header or hidden token
- Timeout on WordPress-side webhook calls if custom PHP is used
- Avoid logging full personal data

## Spam

- Antispam Bee is for comment spam.
- Future CF7 form should use dedicated protection:
  - honeypot
  - Cloudflare Turnstile or reCAPTCHA if spam increases
  - rate limiting later if needed

## File Uploads

Avoid quote file upload in V1 unless required.

If enabled later:

- Restrict file types
- Limit file size
- Do not allow executable uploads
- Avoid public exposure of sensitive files

## Roles

V1 may be dev-only.

V3 should separate:

- Admin/dev: full configuration
- Marketing/editor: content, patterns, block controls
- No raw Tailwind/class editing for non-technical users by default

## Dependency Policy

Before adding a dependency, document:

- Purpose
- Alternative
- Bundle/performance impact
- Whether it loads globally
- Removal plan
