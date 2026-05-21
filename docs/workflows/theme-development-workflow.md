# SKVN Marine Theme Development Workflow

## Current Strategy

V1 uses a GeneratePress child theme instead of forking GeneratePress.

Reason:

- Lower PHP maintenance risk.
- Reuse GeneratePress performance and WooCommerce compatibility.
- Avoid AI modifying parent theme internals.
- Ship the first website faster.

## Folder Structure

```txt
wp-content/
  themes/
    generatepress/
    skvn-marine/
      style.css
      functions.php
      theme.json
      inc/
        setup.php
        enqueue.php
        block-styles.php
        media.php
        woocommerce.php
      assets/
        css/
          editor.css
          frontend.css
        js/
          animations.js
      patterns/

  plugins/
    skvn-marine-blocks/
      skvn-marine-blocks.php
      package.json
      src/
        slider/
        slide/
        accordion/
        product-grid/
        product-list/
```

## Version Plan

### V1

- One production-intended site.
- GeneratePress child theme.
- WooCommerce catalog.
- CF7 quote form.
- CFDB7 submission table.
- n8n lead automation.
- Basic product grid/list.
- Swiper slider.
- English-first content.
- `.context/` may live in main temporarily.

### V2

- Staging branch.
- Separate dev/main workflow.
- Cache strategy tested.
- More formal CI/build.
- Better product grid/list blocks.
- Context moved away from production branch if needed.

### V3

- Evaluate reusable base theme.
- Potential child theme support.
- Better governance for marketing team.
- More reusable block system.
