# Onsite Quote Flow Test — Deferred To 0.10.0

Status:

```text
DEFERRED_TO_0.10.0
```

Reason:

```text
Human is working under time pressure and will test onsite later.
Agent must remind human to run this checklist when V1 / 0.10.0 becomes current.
```

Scope:

```text
Basic CF7/CFDB7 Request a Quote flow.
No n8n automation.
No custom PHP form handler.
```

## Preconditions

- Onsite WordPress page exists: `/request-a-quote/`.
- Onsite thank-you page exists: `/quote-thank-you/`.
- Contact Form 7 is active.
- CFDB7 is active.
- Request quote page contains the CF7 quote form.
- Theme CSS includes the form classes:
  - `skvn-form`
  - `skvn-quote-form`
  - `skvn-button`
  - `skvn-button--primary`

## Expected Form Fields

Visible required fields:

- Full name
- Company name
- Email
- Country
- Product interest
- Quantity / estimated volume
- Message

Visible optional fields:

- Phone / WhatsApp
- Destination port
- Packaging requirement

Hidden/context fields:

- `product_id`
- `product_sku`
- `product_name`
- `product_url`
- `source_url`
- `utm_source`
- `utm_medium`
- `utm_campaign`
- `utm_content`
- `utm_term`

## UX Checks

- Page loads without broken layout.
- Header/footer visibility follows the page settings used onsite.
- Form appears as a SKVN-styled card or section, not an unstyled default CF7 form.
- Inputs have readable labels, adequate spacing, and visible focus state.
- Submit button uses SKVN primary button styling.
- Mobile layout stacks cleanly with no horizontal scroll.
- Error messages are readable and do not overlap fields.
- Success/redirect behavior is clear after submit.

## Functional Test

Open:

```text
/request-a-quote/?product_id=123&product_sku=TEST-SKU&product_name=Test%20Fish&product_url=https%3A%2F%2Fminhhaifishery.com%2Ftest-product%2F&source_url=https%3A%2F%2Fminhhaifishery.com%2Fshop%2F&utm_source=test&utm_medium=onsite&utm_campaign=quote-flow-070
```

Submit test data:

```text
Full name: SKVN Test Buyer
Company name: SKVN Test Company
Email: use a real test inbox
Country: Vietnam
Product interest: Test Fish
Quantity / estimated volume: 1 FCL
Phone / WhatsApp: +84000000000
Destination port: Test Port
Packaging requirement: Carton
Message: 0.7.0 onsite quote flow smoke test
```

## Pass Criteria

- Request quote page renders.
- CF7 form renders.
- Required-field validation works when fields are empty.
- Filled submission sends successfully.
- CFDB7 records the submission.
- Hidden/context fields are present in the stored submission.
- Thank-you page is reached or success message is visible, depending on onsite CF7 setup.
- No n8n webhook is exposed or required.

## Fail Criteria

- Page layout breaks.
- Form is missing or unstyled.
- Required fields do not validate.
- Submission fails without clear user message.
- CFDB7 does not store the submission.
- Hidden/context fields are missing from stored data.
- Browser console shows serious JavaScript errors related to CF7 or theme scripts.
- Any n8n webhook URL/secret appears in page source or public output.

## Evidence To Report Back

Record:

- Test URL used.
- Screenshot desktop.
- Screenshot mobile.
- CF7 result message or thank-you URL.
- CFDB7 submission time and row/detail screenshot.
- Whether hidden fields appeared in CFDB7.
- Browser console errors, if any.
- Any mismatch in spacing, labels, button style, or mobile layout.
