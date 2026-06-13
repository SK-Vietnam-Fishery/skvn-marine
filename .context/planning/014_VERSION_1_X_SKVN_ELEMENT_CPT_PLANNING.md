# Version 1.x Planning — SKVN Element CPT Foundation

Status: planning
Target: V1.x after 1.0.0, before 2.0.0
Scope owner: `skvn-marine-blocks` plugin for data/admin, `skvn-marine` theme for rendering

## Direction

Human decision:

- Header/footer element management should start between `1.0.0` and `2.0.0`.
- This is important for SEO and should not wait until the GeneratePress migration is complete.
- The current footer-as-Page approach is a V1 bridge, not the long-term content model.

## Problem

Using normal WordPress Pages as reusable header/footer content is convenient, but it creates SEO/content-model risk:

- Footer/header template pages may appear as standalone URLs.
- They may enter sitemap/search/archive flows unless manually excluded.
- They are site elements, not user-facing content pages.
- Future display rules need a more precise data model than normal pages.

## Goal

Create a plugin-owned custom post type for reusable site elements before the 2.0.0 migration starts.

The theme then renders those elements into site chrome:

```text
skvn_element
  -> site_header
  -> site_footer
  -> banner
  -> sidebar
```

## Non-Goals

- Do not remove GeneratePress in this milestone.
- Do not build a full visual header/footer builder yet.
- Do not replace all Page-based footer behavior immediately.
- Do not create public archives, public URLs, or SEO-indexable element pages.
- Do not edit GeneratePress parent files.

## CPT Contract

Post type:

```text
skvn_element
```

Registration intent:

```php
'public'              => false,
'show_ui'             => true,
'show_in_rest'        => true,
'exclude_from_search' => true,
'publicly_queryable'  => false,
'has_archive'         => false,
'rewrite'             => false,
'show_in_nav_menus'   => false,
```

Required support:

```text
title
editor
revisions
```

Element meta:

```text
_skvn_element_type: site_header | site_footer | banner | sidebar
_skvn_element_priority: integer
_skvn_element_display_rules: structured array/json, later phase
```

Initial phase may support only:

```text
site_header
site_footer
```

## SEO Exclusion Contract

`skvn_element` must not behave like content pages:

- No public single URL.
- No archive.
- Excluded from search.
- Hidden from nav menus.
- Excluded from SEO sitemap if the SEO plugin includes private/editor-visible CPTs.
- No canonical public page expected.
- No frontend direct render except through SKVN theme/header/footer render functions.

If Rank Math detects the CPT, configure or document exclusion:

```text
Rank Math -> Sitemap Settings -> Post Types -> skvn_element disabled
Rank Math -> Titles & Meta -> skvn_element noindex / disabled if exposed
```

The preferred implementation is to register the CPT so public SEO exposure is already false.

## Ownership

Plugin owns:

- `skvn_element` registration.
- Element type meta.
- Admin UI / list table labels.
- REST/editor availability.
- Future display rules and priority model.

Theme owns:

- Rendering selected elements into header/footer surfaces.
- CSS and visual contract.
- GeneratePress adapter while GeneratePress still exists.
- Standalone render path for 2.0.0 -> 3.0.0 migration.

## Migration Path From Footer Page Settings

Current V1 bridge:

```text
skvn_footer_page_id -> normal WP Page -> theme renders through GeneratePress footer hook
```

Future 1.x model:

```text
skvn_footer_element_id -> skvn_element(site_footer) -> theme renders through footer surface
```

Bridge rules:

- Keep `skvn_footer_page_id` working until migration is complete.
- If a valid footer element exists, it may take priority over footer page.
- If no footer element exists, fallback to selected footer page.
- If neither exists, fallback to default GeneratePress footer while GeneratePress is still present.
- Do not delete or rewrite existing Footer Page content automatically without an explicit migration action.

## Implementation Phases

### Phase 1 — CPT Foundation

Acceptance:

- [ ] `skvn_element` CPT exists in `skvn-marine-blocks`.
- [ ] CPT is visible in wp-admin but not public.
- [ ] CPT supports Gutenberg editing.
- [ ] CPT supports `site_header` and `site_footer` element type meta.
- [ ] CPT is excluded from public URLs, search, archives, nav menus, and sitemap exposure.
- [ ] No GeneratePress parent files are edited.

### Phase 2 — Footer Element Bridge

Acceptance:

- [ ] Admin can create a `site_footer` element in Gutenberg.
- [ ] Theme can render the selected `site_footer` element.
- [ ] Existing `skvn_footer_page_id` fallback still works.
- [ ] SEO exclusion remains true for the element.
- [ ] Human can migrate existing Footer Page content manually or through a reviewed tool.

### Phase 3 — Header Element Preparation

Acceptance:

- [ ] Admin can create a `site_header` element.
- [ ] Header element does not replace GeneratePress header until header rendering is approved.
- [ ] Header Actions planning remains compatible with the element model.
- [ ] Mobile nav/accessibility behavior is explicitly reviewed before rendering a custom header.

### Phase 4 — 2.0.0 Migration Start

Acceptance:

- [ ] `skvn_element` becomes the foundation for header/footer migration away from GeneratePress.
- [ ] GeneratePress adapters are isolated and temporary.
- [ ] Display rules and priority can be expanded if real use cases require multiple elements.

## Display Rules — Deferred

Do not build complex display rules in the first CPT phase unless needed.

Future rule schema may include:

```text
scope: all | post_type | page_id | taxonomy | template
post_type: product | post | page
page_id: number[]
taxonomy: taxonomy + terms
priority: integer
```

Rules must be deterministic:

- highest priority wins
- exact page match beats post type
- fallback all-site element if no specific match

## Risks

- Building CPT too early can add admin complexity.
- Header rendering can become a large accessibility/mobile navigation project.
- Display rules can balloon into a page builder if not constrained.
- SEO exclusion must be verified onsite with Rank Math and sitemap output.

Mitigation:

- Start with footer bridge first.
- Keep header element as preparation until rendering is approved.
- Keep display rules deferred.
- Verify sitemap/search/direct URL behavior during onsite testing.

## Related Planning

- `.context/planning/005_VERSION_2_0_0_FOOTER_BUILDER_PLANNING.md`
- `.context/planning/012_VERSION_3_0_0_PAGE_DISPLAY_DECOUPLING_PLANNING.md`
- `.context/planning/013_VERSION_2_0_0_TO_3_0_0_STANDALONE_BLOCK_THEME_PLANNING.md`

