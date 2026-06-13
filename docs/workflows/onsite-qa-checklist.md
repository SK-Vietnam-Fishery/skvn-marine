# Onsite QA Checklist

Manual QA checklist for SKVN Marine onsite testing.

Scope:

```text
GeneratePress parent theme
skvn-marine child theme
skvn-marine-blocks plugin
WooCommerce runtime smoke
SKVN Full Width template
```

Do not edit code, SSH, or change source repo files while running this checklist.

If any step fails, stop, capture evidence, and follow rollback steps before trying again.

## 1. Preflight Server

- [ ] Log in to WordPress Admin.
- [ ] Confirm WordPress is version 6.5 or newer.
- [ ] Confirm PHP is version 7.4 or newer. PHP 8.1+ is recommended.
- [ ] Go to Tools > Site Health > Info.
- [ ] Confirm memory is at least 256M for WordPress.
- [ ] Confirm admin/max memory is 512M where available.
- [ ] Copy the Site Health "WordPress constants" section into the QA note.
- [ ] Confirm GeneratePress parent theme is installed.
- [ ] Confirm WooCommerce is installed if product sections will be tested.
- [ ] Create a full hosting backup of files and database before upload.
- [ ] Name the backup `skvn-pre-deploy-YYYYMMDD`.

Required memory constants if the host allows `wp-config.php` edits:

```php
define( 'WP_MEMORY_LIMIT', '256M' );
define( 'WP_MAX_MEMORY_LIMIT', '512M' );
```

## 2. Prepare Upload Zips

The build artifact is folder-based:

```text
build/wp-content/themes/skvn-marine/
build/wp-content/plugins/skvn-marine-blocks/
```

For WordPress Admin upload, zip each folder separately:

```text
skvn-marine.zip
skvn-marine-blocks.zip
```

The zip root must contain the plugin/theme folder itself:

```text
skvn-marine/style.css
skvn-marine/functions.php
skvn-marine-blocks/skvn-marine-blocks.php
skvn-marine-blocks/build/
skvn-marine-blocks/modules/
```

Before upload:

- [ ] If the plugin bootstrap loads `modules/`, `includes/`, or another runtime folder, confirm that folder exists inside `skvn-marine-blocks.zip`.
- [ ] Confirm `skvn-marine-blocks.zip` contains runtime PHP files required by the current milestone.
- [ ] If activation reports `failed opening required`, stop and compare the required path against the zip contents before re-uploading.

## 3. Upload Artifact

- [ ] Appearance > Themes > Add New > Upload Theme.
- [ ] Upload `skvn-marine.zip`.
- [ ] Do not activate yet if WordPress allows installing without activation.
- [ ] Plugins > Add New > Upload Plugin.
- [ ] Upload `skvn-marine-blocks.zip`.
- [ ] Do not activate yet if WordPress allows installing without activation.

## 4. Activate In Order

- [ ] Activate GeneratePress parent theme first if it is not active.
- [ ] Activate `skvn-marine` child theme.
- [ ] Activate `skvn-marine-blocks` plugin.
- [ ] Open Appearance > Themes and confirm SKVN Marine appears without missing parent warnings.
- [ ] Open the frontend homepage and confirm the site loads.
- [ ] Open WP Admin and confirm the dashboard loads.

GeneratePress Premium is optional and not required by this project checklist.

## 5. Full Width Template Test

- [ ] Pages > Add New.
- [ ] Title: `QA Fullwidth`.
- [ ] Template: `SKVN Full Width`.
- [ ] Add a Group block.
- [ ] Set Group alignment to Full width.
- [ ] Add Heading and Paragraph inside the Group.
- [ ] Publish.
- [ ] View page on desktop around 1440px wide.
- [ ] Confirm the full-width block reaches the browser edges.
- [ ] Confirm no unwanted white padding from GeneratePress wrappers.
- [ ] Confirm inner content remains readable and aligned.
- [ ] View page on mobile.
- [ ] Confirm there is no horizontal scroll.
- [ ] Switch the page template back to Default.
- [ ] Publish again.
- [ ] Confirm default layout padding returns.

If `.inside-article` padding still affects the full-width template, record screenshot and URL. Do not add CSS during QA.

## 6. WooCommerce Product Smoke

- [ ] Products > Add New.
- [ ] Create product: `SP Test Marine`.
- [ ] Set price.
- [ ] Set featured image.
- [ ] Set product category.
- [ ] Publish.
- [ ] View Product.
- [ ] Confirm single product layout does not break.
- [ ] Confirm Request a Quote button is visible.
- [ ] Confirm Request a Quote button links to `/request-a-quote/?product_id=...`.
- [ ] Open Shop page.
- [ ] Confirm product grid is readable and has no strange spacing.
- [ ] Confirm product grid CTA is visible on mobile.
- [ ] Confirm product grid CTA links to `/request-a-quote/?product_id=...`.
- [ ] Do not test cart/checkout/order flow for the quote-first V1 / 0.6.0 smoke.

## 7. Error Log Capture

- [ ] Check `wp-content/debug.log` if it exists.
- [ ] Check hosting `error_log` if available.
- [ ] Check browser console for serious frontend errors.
- [ ] If a white screen or fatal error appears, record:
  - URL.
  - Time.
  - Last action.
  - Screenshot.
  - Log excerpt.

Do not enable WordPress Troubleshooting Mode by default for this smoke test. It can change the active theme/plugin environment and hide the real issue.

## 8. Rollback

Use this if activation or QA breaks the test site:

```text
1. Deactivate skvn-marine-blocks.
2. Activate the previous theme or GeneratePress parent theme.
3. If wp-admin is unavailable, use hosting File Manager:
   - rename wp-content/themes/skvn-marine to skvn-marine.off
   - rename wp-content/plugins/skvn-marine-blocks to skvn-marine-blocks.off
4. Restore the `skvn-pre-deploy-YYYYMMDD` backup if needed.
5. Confirm WooCommerce is still active.
6. Confirm products/orders still exist.
7. Send screenshot and logs back to dev.
```

## 9. Pass Evidence

- [ ] WordPress/PHP/memory values recorded.
- [ ] Theme/plugin uploaded.
- [ ] Theme/plugin activated.
- [ ] Frontend loads.
- [ ] Admin loads.
- [ ] Full Width template test result recorded.
- [ ] WooCommerce smoke result recorded.
- [ ] Error log status recorded.
- [ ] Screenshots captured for pass/fail.
