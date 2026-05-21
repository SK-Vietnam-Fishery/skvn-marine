# Caching Strategy

## V1

Local/dev:

- Disable full page cache while developing.
- Use asset versioning with `filemtime()`.
- Clear WindPress/browser cache when debugging CSS.

Production/staging later:

- Full page cache for guest/static pages.
- Exclude WooCommerce dynamic pages.
- Exclude quote pages.
- Conditional-load Swiper/block assets.
- Use transient cache for custom product grid/list only if query becomes heavy.

## Exclusions

Do not cache:

- `/cart/`
- `/checkout/`
- `/my-account/`
- `/request-a-quote/`
- `/quote-thank-you/`

## WooCommerce

If the site is quote-first, avoid unnecessary mini-cart/cart-fragment behavior.

## V2/V3

- Redis object cache if product/query volume grows.
- CDN for static assets.
- Product grid/list cache invalidation on product/category updates.
