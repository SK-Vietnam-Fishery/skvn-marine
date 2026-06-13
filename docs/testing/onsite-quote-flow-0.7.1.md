# Onsite Quote Flow Runtime Handoff — 0.7.1

Status:

```text
PARTIAL_ONSITE_UI_PASS_DATA_FLOW_PENDING
```

Latest human feedback:

```text
2026-06-05: CF7/Request Quote interface has been tested onsite. Runtime data flow is still pending: submit one quote, verify CFDB7 row, verify visible and hidden/context fields, and confirm success/thank-you behavior.
```

Purpose:

```text
Verify the real onsite/admin setup for the basic CF7/CFDB7 Request a Quote flow.
This is a handoff/runtime verification checklist, not a new feature build.
Do not run this against local WordPress unless the human explicitly asks.
```

Reason:

```text
Human has completed the onsite CF interface check and still needs to run the runtime data-flow portion.
Agent must keep this checklist open until V1 / 1.1.2 submission and CFDB7 evidence are reported.
```

## Scope

In scope:

```text
- Confirm Contact Form 7 form exists.
- Confirm the Request a Quote page embeds the CF7 form.
- Confirm the thank-you page exists.
- Submit one test quote from the onsite frontend.
- Confirm CFDB7 stores the submitted row.
- Confirm hidden/context fields are present, or record the mismatch for later handling.
- Confirm no n8n webhook URL or secret is publicly exposed.
```

Out of scope:

```text
- Local runtime testing.
- Custom PHP form handler.
- n8n automation.
- New admin setup UI.
- Editor controls milestone 0.8.0.
- Footer settings milestone 0.9.0.
- Full product CTA/query-param data-flow debt is deferred to V1 / 1.1.2.
```

## Related Files

Open these before testing:

- `docs/artifacts/cf7-quote-form-0.7.0.md`
- `docs/decisions/quote-flow.md`
- `.context/modules/QUOTE_FLOW.md`
- `docs/testing/onsite-quote-flow-0.7.0.md`

## Target URLs

Use the real onsite domain.

```text
Request Quote page:
https://<onsite-domain>/request-a-quote/

Request Quote test URL:
https://<onsite-domain>/request-a-quote/?product_id=123&product_sku=TEST-SKU&product_name=Test%20Fish&product_url=https%3A%2F%2F<onsite-domain>%2Ftest-product%2F&source_url=https%3A%2F%2F<onsite-domain>%2Fshop%2F&utm_source=test&utm_medium=onsite&utm_campaign=quote-flow-071

Thank-you page:
https://<onsite-domain>/quote-thank-you/
```

## Preconditions

Record before submitting:

```text
[ ] Onsite domain:
[ ] Tester:
[ ] Test date/time:
[ ] Browser:
[ ] Contact Form 7 is active.
[ ] CFDB7 is active.
[ ] `skvn-marine` child theme is active.
[ ] No production/customer data will be changed except one test quote submission.
```

## Admin Setup Checks

### 1. CF7 Form Exists

Steps:

```text
1. Go to WP Admin > Contact.
2. Find the Request a Quote CF7 form.
3. Open the form editor.
4. Compare the form markup against `docs/artifacts/cf7-quote-form-0.7.0.md`.
```

Expected:

```text
[ ] Form exists.
[ ] Wrapper includes `skvn-form` and `skvn-quote-form`.
[ ] Submit button includes `skvn-button` and `skvn-button--primary`.
[ ] Required visible fields exist: full_name, company_name, email, country, product_interest, quantity, message.
[ ] Optional visible fields exist or intentional omission is recorded: phone, destination_port, packaging_requirement.
[ ] Hidden fields exist: product_id, product_sku, product_name, product_url, source_url, utm_source, utm_medium, utm_campaign, utm_content, utm_term.
```

Evidence to report:

```text
[ ] CF7 form title:
[ ] CF7 form ID:
[ ] Screenshot of form editor showing markup/classes.
[ ] Any mismatch:
```

### 2. Request Quote Page Embeds CF7

Steps:

```text
1. Go to WP Admin > Pages.
2. Open the page with slug `/request-a-quote/`.
3. Confirm the page contains the CF7 shortcode or CF7 block for the Request a Quote form.
4. Open the frontend page.
```

Expected:

```text
[ ] `/request-a-quote/` exists.
[ ] Page embeds the correct CF7 form.
[ ] Frontend renders the form.
[ ] Form is visually styled by SKVN classes, not plain unstyled CF7 defaults.
[ ] Page does not expose any n8n webhook URL or secret in visible content.
```

Evidence to report:

```text
[ ] Page editor screenshot showing shortcode/block.
[ ] Frontend desktop screenshot.
[ ] Frontend mobile screenshot if possible.
[ ] Page source check: no n8n webhook URL/secret found.
```

### 3. Thank-you Page Exists

Steps:

```text
1. Go to WP Admin > Pages.
2. Confirm a page exists with slug `/quote-thank-you/`.
3. Open the frontend page.
```

Expected:

```text
[ ] `/quote-thank-you/` exists.
[ ] Page loads without 404 or fatal error.
[ ] Message is suitable for a submitted quote request.
[ ] Page does not expose any n8n webhook URL or secret.
```

Evidence to report:

```text
[ ] Thank-you page editor or frontend screenshot.
[ ] Any mismatch:
```

## Frontend Submission Test

Open the Request Quote test URL from the Target URLs section.

Submit this test data:

```text
Full name: SKVN 071 Test Buyer
Company name: SKVN Runtime Test Company
Email: <real test inbox>
Country: Vietnam
Product interest: Test Fish
Quantity / estimated volume: 1 FCL
Phone / WhatsApp: +84000000000
Destination port: Test Port
Packaging requirement: Carton
Message: 0.7.1 onsite quote flow runtime handoff test
```

Expected:

```text
[ ] Required-field validation works if required fields are left empty first.
[ ] Filled submission sends successfully.
[ ] User sees a clear success message or reaches `/quote-thank-you/`.
[ ] Browser console has no serious CF7/theme JavaScript errors.
[ ] No n8n webhook is required for the submission to succeed.
```

Evidence to report:

```text
[ ] Exact test URL used:
[ ] Submission time:
[ ] CF7 result message or thank-you URL:
[ ] Browser console notes:
[ ] Screenshot after submit:
```

## CFDB7 Storage Check

Steps:

```text
1. Go to WP Admin > Contact Data or CFDB7 submissions area.
2. Find the row submitted during the Frontend Submission Test.
3. Open the row/detail view.
4. Compare visible and hidden fields against the expected field list.
```

Expected:

```text
[ ] CFDB7 stores one row for the test submission.
[ ] Visible fields are stored with the submitted values.
[ ] Hidden/context fields appear in the row, or the exact missing fields are recorded.
[ ] No n8n webhook URL/secret appears in stored public-facing content.
```

Evidence to report:

```text
[ ] CFDB7 submission ID or row identifier:
[ ] CFDB7 submission time:
[ ] Screenshot of visible fields:
[ ] Screenshot of hidden/context fields:
[ ] Missing/mismatched fields:
```

## Pass Criteria

0.7.1 can pass only when:

```text
[ ] CF7 form exists.
[ ] `/request-a-quote/` embeds the CF7 form.
[ ] `/quote-thank-you/` exists.
[ ] CF7 markup matches the approved contract, or mismatch is documented.
[ ] One onsite frontend submission succeeds.
[ ] CFDB7 stores the submitted row.
[ ] Hidden/context fields are present, or mismatch is documented for V1 / 1.1.2.
[ ] No n8n webhook URL or secret is exposed in page content/source.
[ ] Evidence is reported back to the agent.
```

## Fail Criteria

Fail or block 0.7.1 if:

```text
[ ] CF7 form is missing.
[ ] Request Quote page is missing or does not embed the form.
[ ] Thank-you page is missing.
[ ] Submission fails without a clear user message.
[ ] CFDB7 does not store the test submission.
[ ] Any custom PHP form handler is being used instead of CF7.
[ ] Any n8n webhook URL or secret appears publicly.
[ ] A production-only issue prevents safe testing.
```

## Result Template

Send this back after testing:

```text
Result: PASS / FAIL / BLOCKED
Tester:
Date/time:
Onsite domain:
CF7 form title:
CF7 form ID:
Request Quote page URL:
Thank-you page URL:
CFDB7 submission ID/time:
Hidden fields present: YES / NO / PARTIAL
Missing fields:
Success behavior: CF7 message / thank-you redirect / other
No n8n webhook exposed: YES / NO
Console/log notes:
Screenshots attached:
Notes:
```
