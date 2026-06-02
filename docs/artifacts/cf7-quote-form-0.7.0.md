# CF7 Quote Form 0.7.0

Use this markup for the basic Request a Quote form in Contact Form 7.

```text
<div class="skvn-form skvn-quote-form">
  <div class="skvn-quote-form__grid">
    <label>Full name
      [text* full_name class:skvn-form__control autocomplete:name]
    </label>

    <label>Company name
      [text* company_name class:skvn-form__control]
    </label>

    <label>Email
      [email* email class:skvn-form__control autocomplete:email]
    </label>

    <label>Country
      [text* country class:skvn-form__control autocomplete:country-name]
    </label>

    <label>Product interest
      [text* product_interest class:skvn-form__control default:get]
    </label>

    <label>Quantity / estimated volume
      [text* quantity class:skvn-form__control]
    </label>

    <label>Phone / WhatsApp
      [tel phone class:skvn-form__control autocomplete:tel]
    </label>

    <label>Destination port
      [text destination_port class:skvn-form__control]
    </label>

    <label>Packaging requirement
      [text packaging_requirement class:skvn-form__control]
    </label>

    <label>Message
      [textarea* message class:skvn-form__control]
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
    <p class="skvn-quote-form__note">We will review your request and reply by email.</p>
  </div>
</div>
```

0.7.0 scope:

- CF7 handles the form.
- CFDB7 stores submissions.
- No n8n webhook is configured.
- No custom PHP form handler is allowed.
