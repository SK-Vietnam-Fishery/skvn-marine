# Security Guidelines

Source of truth for SKVN Marine theme/plugin security rules. Agents and human devs must follow this file before shipping PHP, block, or tooling changes.

---

## PHP — Input and output

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

---

## Output path rules (mandatory)

These rules close real audit findings (T-1, B-1, T-2). Apply on every task that reads stored data and renders HTML, CSS, URLs, or inline styles.

### 1. Sanitize on read, not only on save

`register_setting()` / `sanitize_callback` is not enough if another code path reads the same option or attribute for output.

| Pattern | Required |
|---|---|
| Plugin owns option write + sanitize | Expose `*_get_*()` that always runs sanitize before return |
| Theme reads plugin option for output | Call plugin getter when `function_exists()`; do not merge raw `get_option()` |
| Plugin inactive fallback | Return safe defaults only; **do not** read poisoned DB values (C1) |

**Typography example (implemented):** `skvn_marine_get_typography()` delegates to `skvn_marine_blocks_get_typography()` when the plugin is active; otherwise returns `skvn_marine_get_default_typography()` only.

### 2. Public `href`, `src`, and inline CSS need a server gate

| Block type | Rule |
|---|---|
| Interactive / marketing blocks with links or media | Prefer `render_callback` in PHP with `esc_url()`, `wp_get_attachment_image()`, allowlists |
| Static `save()` with raw `href` / `src` | **Do not add new blocks this way** |
| Rich text in PHP render | `wp_kses_post()` |
| Plain labels in PHP render | `sanitize_text_field()` + `esc_html()` |

**Feature Showcase example (implemented):** dynamic PHP render; `imageId` only on frontend; `save()` returns `null`.

Reference implementations: `modules/slider-render/`, `modules/collection-render/`, `modules/feature-showcase-render/`.

### 3. Block attributes → allowlist at PHP render

Editor UI and `block.json` `enum` are not sufficient alone. PHP `render_callback` must normalize with `in_array()` / `sanitize_key()` before building class names or config JSON.

### 4. Trusted site URLs — never use `$_SERVER['HTTP_HOST']`

Do not build quote context, redirects, or canonical links from `HTTP_HOST` + `REQUEST_URI`.

Use WordPress routing instead:

- `get_permalink()` on singular content
- `home_url()` + `$wp->request` or archive/term APIs
- `add_query_arg()` on `home_url('/')` for search

**Quote `source_url` example (implemented):** `skvn_marine_blocks_get_current_source_url()` in `modules/collection-render/cards.php`.

### 5. Dev tools that read/write files

CLI scripts under `tools/` must resolve paths and refuse input/output outside the repo root (`assertInsideRoot()`).

Reference: `tools/build-deploy-artifact.mjs`, `tools/layout-translator/translate-layout.mjs`.

---

## Agent and dev checklist (before submit)

```
[ ] Every get_option() / block attribute used for output has a sanitize-at-read path
[ ] New public links/images use PHP render_callback (or documented escape filter), not static save URLs
[ ] Block class/layout attributes allowlisted in PHP render
[ ] No $_SERVER['HTTP_HOST'] for trusted URLs
[ ] New tools/file IO use assertInsideRoot(repo)
[ ] Input sanitized, output escaped (esc_* / wp_kses_post)
[ ] Animation respects prefers-reduced-motion
```

When adding a block with URL or media output, copy the pattern from slider/collection/feature-showcase PHP render — not from legacy static `save.tsx` alone.

---

## Forms and quote flow

Do not implement a custom quote form handler.

Use:

- Contact Form 7 for form handling
- CFDB7 for database table/submission storage
- n8n webhook for lead automation — **deferred** (not in current repo scope)

### Quote flow test status

| Area | Status |
|---|---|
| CF7 form UI / patterns in repo | Documented |
| CF7 submission, CFDB7 rows, hidden fields, thank-you | **Not verified onsite** — deferred to milestone 1.1.2 |
| `source_url` / product context in collection CTA URLs | Implemented in plugin; full chain depends on CF7 onsite test |
| Custom PHP form handler | **Forbidden** |

Hidden field contract (CF7): `product_id`, `product_sku`, `product_name`, `product_url`, `source_url`, UTM fields. See `docs/decisions/quote-flow.md` and `docs/testing/onsite-quote-flow-0.7.1.md`.

---

## n8n webhook

n8n is **not implemented** in this repo and remains deferred until V1 → V2 boundary review unless human moves it earlier.

If/when added:

- Hard-to-guess webhook URL
- Optional shared secret header or hidden token
- Timeout on WordPress-side webhook calls if custom PHP is used
- Avoid logging full personal data

---

## Spam

| Surface | Protection |
|---|---|
| Comment spam | **Antispam Bee** (external plugin on runtime site) |
| CF7 forms | Honeypot + optional Cloudflare Turnstile if spam increases; rate limiting later |

Antispam Bee and CF7 are runtime dependencies, not vendored in this source repo.

---

## File uploads

Avoid quote file upload in V1 unless required.

If enabled later:

- Restrict file types
- Limit file size
- Do not allow executable uploads
- Avoid public exposure of sensitive files

---

## Roles

V1 may be dev-only.

V3 should separate:

- Admin/dev: full configuration
- Marketing/editor: content, patterns, block controls
- No raw Tailwind/class editing for non-technical users by default

---

## Dependency policy

Before adding a dependency, document:

- Purpose
- Alternative
- Bundle/performance impact
- Whether it loads globally
- Removal plan

---

## Audit reference (2026)

| ID | Issue | Resolution |
|---|---|---|
| T-1 | Theme typography merged raw option into inline CSS | A+C1: delegate to plugin getter; plugin-off → defaults only |
| B-1 | Feature Showcase static save with raw URLs | E: PHP `render_callback`, `imageId`, `save()` null |
| T-2 | `source_url` used `HTTP_HOST` | Source fixed 1.3.6 (`cards.php`); **onsite verify + quote-chain close → milestone 1.4.1** |
| D-1 | `translate-layout.mjs` arbitrary file read | Fixed: `assertInsideRoot()` |
| Q-2 | CF7/n8n production surface | Documented above; quote onsite test deferred |