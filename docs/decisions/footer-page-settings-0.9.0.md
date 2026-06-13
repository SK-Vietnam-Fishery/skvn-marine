# Footer Page Settings — 0.9.0 Report

Date: 2026-06-03
Status: Implemented, online smoke evidence carried to V1 / 0.10.0.

## Scope

0.9.0 adds a plugin-owned settings page for choosing a published WordPress page as the reusable site footer. The theme renders the selected page through GeneratePress' existing `generate_footer` surface.

## Implementation

- Plugin module: `wp-content/plugins/skvn-marine-blocks/modules/footer-settings/footer-settings.php`
- Plugin bootstrap: `wp-content/plugins/skvn-marine-blocks/skvn-marine-blocks.php`
- Theme render module: `wp-content/themes/skvn-marine/inc/footer.php`
- Theme bootstrap: `wp-content/themes/skvn-marine/functions.php`
- Runtime test checklist: `docs/testing/footer-page-settings-0.9.0.md`

## Decisions

- Keep the current plugin slug, text domain, bootstrap path, and option key.
- Store the selected page ID in `skvn_footer_page_id`.
- Allow only published WordPress pages from the admin selector.
- Sanitize invalid, non-page, non-published, or empty values to `0`.
- Replace GeneratePress default footer callbacks only when a valid selected footer page exists.
- Fall back to the default GeneratePress footer when no valid page is selected.

## Verification

Source checks run:

```bash
wsl -d Debian -- php -l /mnt/d/Github/skvn-marine/wp-content/themes/skvn-marine/functions.php
wsl -d Debian -- php -l /mnt/d/Github/skvn-marine/wp-content/themes/skvn-marine/inc/footer.php
wsl -d Debian -- php -l /mnt/d/Github/skvn-marine/wp-content/plugins/skvn-marine-blocks/skvn-marine-blocks.php
wsl -d Debian -- php -l /mnt/d/Github/skvn-marine/wp-content/plugins/skvn-marine-blocks/modules/footer-settings/footer-settings.php
```

Result:

```text
PASS — no PHP syntax errors detected in all four files.
```

Online smoke checklist:

```text
docs/testing/footer-page-settings-0.9.0.md
```

Runtime note:

```text
Human asked not to deploy or test through the local WordPress runtime for 0.9.0. Online/onsite smoke evidence is carried to V1 / 0.10.0.
```
