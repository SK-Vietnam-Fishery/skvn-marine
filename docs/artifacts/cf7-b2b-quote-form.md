# CF7 B2B Request Quote Form

Status: paste-ready Contact Form 7 artifact

Use this short first-contact form for a B2B seafood/marine quote request. CF7 handles submission and CFDB7 stores the row. Do not add a custom PHP form handler.

Design intent:

- Keep the first screen short enough for real buyers to complete.
- Collect only what sales needs to start a reply.
- Move packaging, certification, timeline, and destination details into the message or follow-up email.

## Form Tab

```text
<div class="skvn-form skvn-quote-form">
  <div class="skvn-quote-form__grid">
    <div class="skvn-quote-form__row">
      <label>Full name
        [text* full_name class:skvn-form__control autocomplete:name]
      </label>

      <label>Company name
        [text* company_name class:skvn-form__control autocomplete:organization]
      </label>
    </div>

    <label>Business email
      [email* email class:skvn-form__control autocomplete:email]
    </label>

    <div class="skvn-quote-form__row">
      <label>WhatsApp / Viber
        [tel phone class:skvn-form__control autocomplete:tel]
      </label>

      <label>Country / market
        [text* country class:skvn-form__control autocomplete:country-name]
      </label>
    </div>

    <label>Product interest
      [text* product_interest class:skvn-form__control default:get]
    </label>

    <label>Quantity / requirement
      [textarea* message class:skvn-form__control placeholder "Tell us quantity, size/grade, destination, packing, or certificates needed."]
    </label>
  </div>

  [hidden product_id default:get]
  [hidden product_sku default:get]
  [hidden product_name default:get]
  [hidden product_url default:get]
  [hidden source_url default:get]
  [hidden utm_source default:get]
  [hidden utm_medium default:get]
  [hidden utm_campaign default:get]
  [hidden utm_content default:get]
  [hidden utm_term default:get]

  <div class="skvn-quote-form__actions">
    [submit class:skvn-button class:skvn-button--primary "Submit quote request"]
    <p class="skvn-quote-form__note">We will reply with availability and next steps.</p>
  </div>
</div>
```

## Mail Tab

Recommended mail subject:

```text
New B2B quote request: [product_interest] — [company_name]
```

Recommended mail body:

```text
New B2B quote request

Buyer
- Full name: [full_name]
- Company: [company_name]
- Email: [email]
- WhatsApp / Viber: [phone]
- Country / market: [country]

Product request
- Product interest: [product_interest]
- Quantity / requirement:
[message]

Context
- Product ID: [product_id]
- Product SKU: [product_sku]
- Product name: [product_name]
- Product URL: [product_url]
- Source URL: [source_url]

UTM
- Source: [utm_source]
- Medium: [utm_medium]
- Campaign: [utm_campaign]
- Content: [utm_content]
- Term: [utm_term]
```

Recommended additional headers:

```text
Reply-To: [email]
```

## Messages Tab

Use concise B2B wording:

```text
Sender's message was sent successfully
Thank you. Our team will review your request and reply with availability and next steps.

Validation errors occurred
Please check the required fields and submit again.

There is a field that the sender must fill in
Please fill in this required field.

Email address that the sender entered is invalid
Please enter a valid business email address.
```

## Page Embed

Insert the created CF7 form into `/request-a-quote/` using the CF7 shortcode or CF7 block.

Expected flow:

```text
Product CTA
→ /request-a-quote/?product_id=123&product_sku=...&product_name=...&product_url=...
→ CF7 B2B Request Quote form
→ CFDB7 row
→ thank-you message or /quote-thank-you/
```

## Required Checks

- Wrapper includes `skvn-form` and `skvn-quote-form`.
- Submit button includes `skvn-button` and `skvn-button--primary`.
- Required visible fields are present: `full_name`, `company_name`, `email`, `country`, `product_interest`, `message`.
- Optional visible field is present: `phone` for WhatsApp / Viber contact.
- Hidden fields are present: `product_id`, `product_sku`, `product_name`, `product_url`, `source_url`, UTM fields.
- CFDB7 stores both visible and hidden fields.
- No custom PHP handler is added.
