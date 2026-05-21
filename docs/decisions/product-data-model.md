# Product Data Model

## Decision

Use WooCommerce native products, categories, and attributes for V1.

Do not create a custom product CPT unless WooCommerce becomes unsuitable.

## Product Categories

Initial examples:

- Shrimp
- Fish
- Squid
- Crab
- Value-added Seafood
- Frozen Seafood

## WooCommerce Attributes

Initial examples:

- Origin
- Size / Grade
- Packaging
- Certification
- Processing Type
- Freezing Type
- Storage Temperature
- Shelf Life

## Product Fields

Prefer WooCommerce native fields and attributes:

- Product name
- Short description
- Long description
- Product image/gallery
- SKU
- Categories
- Attributes

## Future Fields

If required in V2/V3, evaluate ACF/Meta Box for:

- Scientific name
- HS code
- Export market
- Incoterms
- MOQ
- Factory/certification metadata

## Product Card Variants

### Basic

- Image
- Product name
- Short specs
- Request a Quote CTA

### Overlay

- Image
- Hover overlay
- Short marketing text
- Request a Quote CTA

Mobile rule: CTA must always be visible; do not rely on hover.

### Technical

- Image
- Specs table
- Request a Quote CTA

Technical variant can be V2.
