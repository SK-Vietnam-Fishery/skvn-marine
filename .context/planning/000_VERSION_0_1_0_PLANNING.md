# Version 0.1.0 Planning Summary — SKVN Marine

> Planning snapshot for the current V1 direction.
> This file records design and implementation choices discussed before moving into design-system and pattern work.
> Load this file when working on homepage layout, Gutenberg patterns, WooCommerce product sections, map/contact sections, or block scope decisions.

---

## Status

Status: **DECIDED**
Applies to: **V1 / 0.1.0 planning baseline**

This file does not replace `.context/MILESTONES.md`. Milestone transition remains managed there.

---

## Core Direction

V1 should stay lean and WordPress-native:

- Use GeneratePress parent theme with the `skvn-marine` child theme.
- Use Gutenberg patterns, core blocks, WooCommerce native blocks, block styles, and theme CSS for most layout sections.
- Reserve custom Gutenberg blocks for interaction-heavy features or places where editor governance becomes necessary.
- Use WooCommerce native products, categories, and attributes as the product data model.
- Avoid custom CPTs for products in V1.

---

## Theme vs Plugin Boundary

Theme `skvn-marine` owns:

- Visual system
- Design tokens
- CSS utilities
- Block styles
- Patterns
- WooCommerce visual overrides
- Section layout and presentation

Plugin `skvn-marine-blocks` owns:

- Custom blocks with real logic
- Slider / Slide block
- Accordion block
- Future product or layout blocks only if native Gutenberg/WooCommerce patterns become insufficient

External plugins remain runtime dependencies and must not be committed into the source repo.

---

## Gutenberg Layout Strategy

Default V1 strategy:

```text
Core Gutenberg blocks
+ WooCommerce native blocks
+ theme patterns
+ CSS utility classes
+ WindPress/Tailwind where useful
```

Custom block strategy:

- Do not create custom blocks for purely visual sections by default.
- Create a custom block only when logic, repeatable editor governance, or complex controls justify it.
- Prefer theme patterns first, then evaluate whether the editing experience is too fragile.

---

## Homepage Test Page Workflow

V1 layout development should use a WordPress test page before finalizing reusable patterns.

Test prompts are stored in `docs/frontpage-testing.md`.

Method:

```text
Create a WP test page
→ assemble the full homepage using core blocks, WooCommerce native blocks, OpenStreetMap iframe embed, placeholder/network images, and English placeholder copy
→ apply SKVN wrapper classes and Tailwind/WindPress utility classes
→ code/refine child theme CSS and theme patterns
→ refresh desktop/mobile views
→ compare against the reference layout and acceptance checklist
→ extract stable sections into reusable theme patterns
```

Test page should cover:

- Top utility bar and main header.
- Hero panel with image/media composition.
- Category strip.
- Combo/product card section.
- Why-choose/trust strips.
- Featured product section using WooCommerce-native direction or placeholders.
- Promo/cold-chain banner.
- Process steps.
- Map/contact section.
- Newsletter signup band with replaceable image block.
- Footer.

Rules:

- Use English placeholder copy; exact translation is not required.
- Images may be placeholders or remote images, but final CSS must not hardcode image URLs.
- Tailwind/WindPress class choices should be explicit enough to verify layout behavior.
- Do not create custom blocks until the pattern approach proves insufficient.
- Do not add new builder plugins for this workflow.
- Mobile checks are mandatory before marking a section stable.

---

## Header / Footer

V1 decision:

- Use GeneratePress header/footer baseline plus SKVN child theme CSS.
- Do not add a header/footer builder plugin by default.
- If a template reference looks good, translate its structure into theme CSS, WordPress menus, and theme pattern/template code.

Header should include:

- Logo/site identity.
- Native WordPress menu.
- Primary CTA such as Request a Quote when needed.
- Responsive mobile navigation using GeneratePress baseline behavior unless a clear gap appears.

Footer should include:

- Company information.
- Product/category links.
- Contact information.
- Optional trust feature strip above footer.
- Optional newsletter signup band above footer.
- Optional certification/social area if content is available.

Future plugin/builder condition:

- Reconsider a builder plugin only if non-technical editors must frequently redesign global header/footer layouts without developer support.

---

## Trust Strip / Newsletter Band

V1 decision:

- Implement as reusable theme patterns with SKVN child theme CSS.
- Do not create a custom block by default.
- Do not add a new newsletter plugin by default.
- If newsletter submission must work in V1, use CF7 markup/shortcode and keep form classes aligned with the project form system.
- Do not custom-code a newsletter form handler in the theme.

Trust feature strip should include:

- Icon/image.
- Short headline.
- Supporting text.
- Four-column desktop layout.
- Responsive two-column/tablet and single-column/mobile layout.

Newsletter signup band should include:

- Title.
- Short description.
- Email field and submit button or CF7 form placeholder.
- Replaceable Image block for product/carton artwork.
- Image overhang outside the band on desktop.

Theme CSS should handle:

- Stable wrapper classes such as `skvn-trust-strip`, `skvn-newsletter-band`, `skvn-newsletter-media`, and `skvn-newsletter-media--overhang`.
- Image positioning and responsive fallback.
- Preventing overhanging image from covering text or form controls.
- Mobile stack where image no longer overhangs if space is limited.

Editor behavior:

- Image source must stay editable via the core Image block.
- User should be able to replace the artwork with Upload/Media Library without touching code.
- CSS must not hardcode the image URL.

Future custom block condition:

- Consider a custom block only if editors need locked controls for image position, overhang amount, mobile visibility, layout variant, or form source.

---

## Hero / Front Panel

The hero direction is a large visual front panel with possible layered composition:

- Background image or scene
- Text content
- Optional foreground seafood/company imagery
- Floating feature strip overlapping the bottom edge

V1 decision:

- Implement as a theme pattern with CSS utilities.
- Do not create a custom hero block yet.
- Use stable classes such as:
  - `skvn-hero-panel`
  - `skvn-overlap-wrap`
  - `skvn-overlap-strip`

Future custom block condition:

- Consider `skvn-marine/hero-panel` only if marketing/editor users need safe sidebar controls for background image, foreground image, overlap, responsive spacing, or layout variants.

---

## Feature Strip / Category Strip

There are two separate concepts:

1. Company feature strip
   - Icon
   - Heading or metric
   - Short text
   - Example: quality, export, cold chain, factory capability

2. Product category strip
   - Category image or icon
   - Product category name
   - Based on WooCommerce categories when it represents real product taxonomy

V1 decision:

- Company feature strip: theme pattern + CSS.
- Product category strip: WooCommerce native category/product blocks where possible.
- Do not create a custom block for these in V1 unless editor governance becomes necessary.

---

## Stat / Icon Card

The stat/icon card pattern covers cards like:

```text
Icon
Heading or metric: 8,000+
Label: TAN / NHAN SU
Description: Kho lanh bao quan / Lanh nghe va tan tam
```

Required layout variants:

- Horizontal: icon beside text stack
- Vertical: icon above text stack

V1 decision:

- Implement with Gutenberg Group/Image/Heading/Paragraph inside theme patterns.
- Use CSS classes or block styles for layout variants:
  - `skvn-stat-card`
  - `skvn-stat-card--horizontal`
  - `skvn-stat-card--vertical`

Do not create a custom `skvn-marine/stat-card` block in V1 by default.

Future custom block condition:

- Create `skvn-marine/stat-card` only if editors need locked controls for icon, heading, label, description, card style, and horizontal/vertical icon placement.
- The custom block may support:
  - Icon source or icon picker
  - Heading / metric
  - Label
  - Description
  - Layout: horizontal or vertical
  - Icon position: left, top, or right
  - Card style: plain, bordered, filled, compact

---

## Product Data Model

Products should be modeled as WooCommerce data, not static text.

Use WooCommerce Product for commercial product items, for example:

```text
Grouper Fish Fillet Skin-On
```

Use Product Category for product families, for example:

```text
Grouper Fish
Shrimp
Squid
Crab
Clam
```

Use WooCommerce attributes for specs that may need filtering, searching, comparing, or structured display:

- Scientific Name
- Pack Type
- Size
- Breaded/Battered
- Bones In/Out
- Shell On/Off
- Skin On/Off
- Tail On/Off
- Cooking Methods
- Seasonality
- Farmed/Wild
- Method of Catch

ACF or Meta Box can be considered later only when WooCommerce attributes are not enough for editorial fields or display ordering.

---

## Homepage Product List UX

The homepage product section should feel like a B2B seafood/export catalog, not a retail shop grid.

Product cards should prioritize:

- Product image
- Product or category name
- Processing type badge, such as fillet, whole, block, HOSO
- Key specs
- Available sizes
- CTA: View specifications / Request a quote
- Future chatbot hook

Do not prioritize price on the homepage unless the business explicitly wants retail-style commerce.

Mobile rule:

- Main CTA must always be visible.
- Do not make important actions hover-only.

Future chatbot hook can be prepared with data attributes:

```html
data-skvn-product-id
data-skvn-product-sku
data-skvn-product-name
data-skvn-product-url
data-skvn-chat-context="product-card"
```

---

## Product Grid / List

V1 decision:

- Use WooCommerce native blocks/patterns.
- Do not build custom Product Grid/List blocks in V1.
- Custom Product Grid/List or related style blocks are deferred to V2 or later.

Reason:

- Native WooCommerce blocks provide a faster baseline.
- Product data model and homepage information architecture should stabilize before creating custom query/display blocks.

---

## Map / Contact Section

Selected direction:

- Use OpenStreetMap iframe embed for V1.
- SKVN theme provides the pattern and visual wrapper.
- Reason: target shared host supports PHP 8.0 only; `Out of the Block: OpenStreetMap` requires PHP 8.1.

V1 implementation should be:

```text
Core HTML block or pattern markup containing an OpenStreetMap iframe
+ SKVN contact/location pattern
+ SKVN CSS wrapper
```

Pattern should include:

- OSM iframe embed, not a map plugin block
- Contact card overlaid on the map on desktop, aligned to the right side.
- Card company name heading.
- Address
- Phone/email
- CTA button

Theme CSS should handle:

- Map height
- Border radius
- Contact card overlay positioning on desktop.
- Contact card white background, subtle shadow, compact spacing, and 8px or smaller radius unless the design system later changes it.
- Pin/marker emphasis near the left side of the map composition.
- Responsive stack
- Popup typography
- Spacing
- Visual framing

Target visual direction:

```text
Full-width map panel
+ large blue map pin emphasis on the left
+ floating white company contact card on the right
+ dark blue bottom band or surrounding section frame where needed
```

Mobile behavior:

- Keep map readable.
- Move contact card below or above the map if overlay would reduce legibility.
- Phone/email remain visible without hover.

Do not create a custom map block in V1.
Do not add a new map plugin in V1 unless iframe embed proves insufficient.

Future condition:

- If the site needs stronger brand control, fewer map POIs, custom tile styling, multiple markers, or richer editor controls, evaluate Mapbox/custom tile provider or a PHP-compatible map plugin later.

---

## About / Factory Capability Section

V1 decision:

- Implement factory/about/capability sections as theme patterns and CSS.
- Do not create a custom block unless the section becomes data-driven or requires complex editor controls.

Possible section parts:

- Intro text
- Factory image
- Image stack
- Stats cards
- Certification strip
- Capability highlights

---

## Slider / Swiper

Slider remains a custom block feature.

Requirements:

- Slider/Slide lives in `skvn-marine-blocks`.
- Swiper is used only for slider frontend runtime.
- Swiper should load only when a slider block exists on the frontend.
- Editor uses stacked or simplified preview.
- Do not run autoplay carousel in editor.
- Keyboard navigation is required.
- Pause on hover is required.
- Respect `prefers-reduced-motion`.

---

## Accessibility

Accessibility is required for interactive blocks and important CTAs.

V1 should account for:

- Keyboard navigation for slider and accordion
- Visible focus states
- ARIA states where useful
- Reduced motion support
- Mobile-visible CTAs
- No hover-only primary actions

---

## Plugin / Dependency Direction

External plugins discussed for V1 runtime:

- WooCommerce
- Contact Form 7
- CFDB7
- Rank Math
- Antispam Bee
- WindPress

Polylang:

- Prepare text domains and avoid hardcoded UI strings.
- Do not activate unless multilingual becomes a confirmed V1 requirement.

ACF / Meta Box:

- Do not install immediately.
- Re-evaluate if WooCommerce attributes are insufficient.

---

## V1 Scope

Do in V1:

- Child theme baseline
- Design system / tokens / typography
- Block styles
- Header/footer baseline using GeneratePress + SKVN child theme CSS
- Trust strip and newsletter signup reusable patterns
- Hero pattern
- Feature strip pattern
- Stat/icon card pattern
- About/factory pattern
- WooCommerce product/category sections using native blocks
- Map/contact pattern using OpenStreetMap iframe embed
- Slider block
- Accordion block
- Quote flow using CF7 + CFDB7 + n8n
- Product data entry using WooCommerce

Do not do in V1 by default:

- Custom Product Grid/List block
- Custom Hero block
- Custom Map block
- Custom Stat/Icon Card block
- Custom CPT for products
- Advanced product filter
- Quote cart
- Multi-product quote table
- Popup quote flow as primary UX
- Complex technical product card/spec table block

---

## Summary Decision

V1 uses Gutenberg patterns and theme CSS for most visual sections. Custom blocks are reserved for interaction-heavy logic or editor governance that cannot be safely handled with native blocks and patterns.

Product data uses WooCommerce native products, categories, attributes, and native WooCommerce blocks/patterns in V1.

Map uses OpenStreetMap iframe embed in V1, wrapped by SKVN patterns and CSS. A map plugin is deferred because the target shared host only supports PHP 8.0.

Hero, feature strip, stat/icon cards, about/factory, and map/contact are implemented as theme patterns first. Custom wrapper blocks may be considered later only if editor controls become necessary.
