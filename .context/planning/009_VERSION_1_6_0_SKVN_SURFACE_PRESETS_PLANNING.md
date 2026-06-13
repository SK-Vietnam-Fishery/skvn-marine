# VERSION 1.6.0 PLANNING - SKVN Surface Presets

> SKVN-local hardening plan for approved flat/soft/glass/elevated/outlined surface styles.
> This is not V1 launch scope and not a WindPress dependency plan.

---

## Status

Status: **PENDING**

Target:

```text
V1.x / 1.6.0
```

This milestone is SKVN-local hardening. It prepares the design system for richer visual presets while keeping the current `skvn-marine` theme and `skvn-marine-blocks` plugin independent from WindPress.

---

## Problem

Marketing layouts repeatedly need surface treatments such as flat cards, soft panels, glass hero cards, elevated quote panels, and outlined certification boxes.

If each section invents its own CSS, the site becomes hard to audit and future editor controls cannot safely expose those styles.

If WindPress/Tailwind utilities become the production contract, the future plugin direction is less portable and agents may treat utility classes as the visual source of truth.

---

## Direction

Create SKVN-owned surface presets.

Theme owns:

- CSS classes
- design tokens
- fallbacks
- contrast/readability rules
- editor/frontend visual parity

Plugin owns:

- future sidebar control values
- saved attributes
- output class selection

WindPress may remain a prototype aid, but production output should use `skvn-*` classes.

---

## Typography Preset And Delivery Mode

1.6.0 should also design the first governed typography delivery surface instead of leaving font changes as code-only tokens.

Goal:

- Let admins choose approved font families and delivery mode without raw CSS, raw URLs, or arbitrary font names.
- Keep font decisions theme-owned because typography is part of the SKVN visual system.
- Keep the implementation compatible with GeneratePress during V1, but avoid GeneratePress parent-theme edits.

Recommended admin surface:

```text
SKVN Marine -> Typography
```

If the SKVN Marine admin surface is not ready, use a temporary Customizer section:

```text
Customizer -> SKVN Typography
```

Font family presets:

```text
Inter
Roboto
Open Sans
Lato
Montserrat
Noto Sans
Source Sans 3
System stack
```

Font role controls:

```text
Body font: approved preset
Heading font: approved preset or Same as body
UI font: approved preset or Same as body
```

Delivery mode:

```text
Google CDN
Local self-hosted
```

Design decision:

- Implement a mode switch, not simultaneous CDN plus local loading by default.
- Google CDN mode prioritizes fast global delivery and simple operations.
- Local self-hosted mode prioritizes privacy/control and avoids frontend requests to Google.
- When possible, prepare or validate local font files after a preset is selected, but only enqueue the selected delivery mode on the frontend.
- Do not allow arbitrary Google Fonts URLs, arbitrary font-family strings, uploaded unknown font files, or dafont-style sources without explicit license review.
- Use `font-display: swap` for both delivery modes.
- Keep fallback stacks behind every selected webfont, for example `"Inter", system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif`.

Option keys to evaluate:

```text
skvn_font_body_preset
skvn_font_heading_preset
skvn_font_ui_preset
skvn_font_delivery_mode
```

Theme output contract:

```text
--skvn-font-body
--skvn-font-heading
--skvn-font-ui
```

Implementation note:

- The first implementation may use a curated preset map rather than a full Google Fonts browser.
- If local self-hosting downloads files, store generated/cache assets in a machine/site-owned runtime location, not committed source, unless a specific bundled font is approved.
- Document cache invalidation, missing font fallback, and shared-host outbound request failure behavior before shipping local download logic.

---

## Candidate Presets

```text
flat
soft
glass
elevated
outlined
```

Candidate CSS contract:

```text
skvn-surface--flat
skvn-surface--soft
skvn-surface--glass
skvn-surface--elevated
skvn-surface--outlined
```

Candidate editor attribute:

```text
surfaceStyle: flat | soft | glass | elevated | outlined
```

---

## Allowed Scopes

- `skvn-card`
- section inner panels
- hero content/media panels
- CTA bands
- form shells
- quote/context cards

Do not apply glass or elevated treatments to long article body content by default.

---

## Guardrails

- Flat or soft remains the default for B2B/content-heavy pages.
- Glass requires a background with enough contrast.
- Glass must have a fallback when `backdrop-filter` is unsupported.
- Text contrast must remain readable without relying on blur.
- No raw CSS, raw color, arbitrary blur, or arbitrary shadow input in editor controls.
- No Tailwind/WindPress utility classes in saved production content.
- Keep the system independent enough to migrate into a future standalone Gutenberg plugin.

---

## Relationship To Future Gutenberg Supercharger

This 1.6.0 milestone is SKVN-local hardening.

The standalone/future plugin version of surface presets belongs later, likely after the future Gutenberg Supercharger direction is active.

Current naming guardrail:

```text
Standard/core product: Gutenberg Supercharger
Pro/commercial stage: Gutenberg Supercharger Stage 2
Community tagline: Make your site feel more "Á đù VTEC".
```

---

## Acceptance Draft

- [ ] Surface preset contract is documented before code.
- [ ] Typography preset and delivery-mode contract is documented before code.
- [ ] Admin can choose approved body, heading, and UI font presets without raw CSS or raw URLs.
- [ ] Admin can choose Google CDN or local self-hosted delivery mode.
- [ ] Frontend enqueues only the selected font delivery mode by default.
- [ ] Local self-hosted mode documents cache path, failure fallback, and license/source constraints.
- [ ] Theme CSS defines the approved `skvn-surface--*` classes.
- [ ] Surface presets use SKVN tokens and do not depend on WindPress.
- [ ] Glass has a non-blur fallback.
- [ ] Editor/frontend parity is checked for the first supported surface.
- [ ] At least one SKVN-owned block or layout surface can select a preset through safe controls.
- [ ] No raw class, raw CSS, or arbitrary color/blur/shadow input is required.
- [ ] Human approves milestone completion.
