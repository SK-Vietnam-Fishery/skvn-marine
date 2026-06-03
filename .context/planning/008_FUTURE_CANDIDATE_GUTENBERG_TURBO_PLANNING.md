# Future Candidate Planning - Gutenberg Turbo / Gutenberg Supercharger

> Planning reference for the future umbrella plugin that may consolidate smaller Gutenberg enhancement plugins after they are stable.
> Load this file when planning article templates, Gutenberg content templates, editor supercharger features, reusable post-type layout systems, or plugin consolidation.

---

## Status

Status: **FUTURE_CANDIDATE**

This file does not change the current milestone. Current milestone remains managed by `.context/MILESTONES.md`.

Candidate product names:

```text
Gutenberg Turbo
Gutenberg Supercharger
```

Working preference:

```text
Gutenberg Turbo
```

Reason:

```text
Shorter, clearer, and easier to use as a plugin/product name. "Supercharger" remains a useful metaphor for the concept.
```

---

## Problem

Some Gutenberg workflows are bigger than a single block but smaller than a full theme or site builder.

Examples:

- Article/post-type templates with content column, TOC sidebar, metadata, comments, and related blocks.
- Safe editor controls for content templates.
- Layout presets that should apply across post types without requiring editors to paste shell markup.
- Future reusable Gutenberg workflow tools that are not specific to the SKVN Marine theme.

If these features are implemented directly inside `skvn-marine-blocks`, the plugin can become too broad before each feature is stable.

If they are implemented directly in the theme, they become hard to reuse on other sites and harder to test as standalone Gutenberg tooling.

---

## Direction

Build smaller plugins first, then consolidate once behavior and boundaries are proven.

Initial small-plugin candidate:

```text
skvn-article-templates
```

Future umbrella plugin:

```text
gutenberg-turbo
```

The future umbrella plugin should behave like a modular Gutenberg enhancement suite, not a visual freeform page builder.

---

## Architecture Principle

Start split:

```text
skvn-article-templates/
skvn-marine-blocks/
future-small-plugin/
```

Consolidate later:

```text
gutenberg-turbo/
  modules/
    article-templates/
    marine-blocks/
    footer-settings/
    layout-controls/
```

The consolidation point is reached only after each module has:

- clear responsibility
- stable admin/editor UX
- tested frontend behavior
- scoped CSS
- migration path from standalone plugin to umbrella module

---

## Article Templates Module Candidate

Purpose:

```text
Provide template-driven layouts for posts or custom post types while keeping Gutenberg content focused on article body content.
```

Responsibilities:

- Template selection by post type.
- Optional per-post template override.
- Article layout shell: content column, TOC/sidebar region, metadata, comments, related/CTA areas.
- Scoped CSS for article templates.
- Admin/editor controls for layout presets.
- Compatibility with core Gutenberg content.

Non-responsibilities:

- Do not rewrite article body content.
- Do not depend on one specific theme DOM as the data contract.
- Do not require raw CSS or raw class input for normal editors.
- Do not own global site header/footer.
- Do not become a full page builder.

---

## Template Customization Model

Use safe presets, not raw layout code.

Candidate settings:

```text
article_layout: content_only | right_toc | left_toc | wide_content
content_width: readable | comfortable | wide
sidebar_width: narrow | normal | wide
toc_mode: disabled | generated | block_plugin
toc_sticky: true | false
show_meta: true | false
show_author: true | false
show_categories: true | false
show_comments: true | false
show_related: true | false
```

Potential control levels:

1. Global plugin settings.
2. Post type defaults.
3. Per-post override.

Per-post override should be optional and simple.

---

## Data And Markup Boundary

Gutenberg content owns:

- heading/body text
- images
- inline links
- normal content blocks
- editorial CTA blocks when inserted by editors

Template owns:

- page shell
- article grid
- sidebar area
- TOC placement
- post metadata placement
- comments region
- related posts region

Plugin settings own:

- template preset
- post type default
- per-post override
- safe layout options

Avoid using full rendered theme DOM as source of truth.

Correct source for article body content:

```text
post_content / the_content()
```

Correct source for template shell:

```text
template loader + plugin options + post type context
```

---

## Compatibility Notes

GeneratePress support can be added as a compatibility layer, but should not be the core data contract.

Allowed:

- Scoped CSS for `body.single-post` or `body.single-{post_type}`.
- Template rendering that works inside normal WordPress theme surfaces.
- Optional GeneratePress-specific adjustments behind compatibility checks.

Avoid:

- CSS or JS that assumes the entire GeneratePress DOM tree always exists.
- Hard dependency on Essential Blocks TOC DOM.
- Parsing rendered sidebar HTML as source of truth.

---

## Migration To Umbrella Plugin

Small plugins should be built so they can later move into `gutenberg-turbo/modules/*` with minimal rewrite.

Rules:

- Use clear module prefixes.
- Keep options namespaced.
- Keep CSS scoped.
- Keep module bootstrap isolated.
- Avoid cross-plugin globals.

Standalone article plugin candidate prefixes:

```text
PHP prefix:    skvn_article_templates_
Option prefix: skvn_article_templates_
CSS prefix:    skvn-article-
```

Umbrella plugin candidate prefixes:

```text
PHP prefix:    gutenberg_turbo_
Option prefix: gutenberg_turbo_
CSS prefix:    gt-
```

Migration may preserve old option keys temporarily and map them into umbrella module settings.

---

## Implementation Phases

### Phase 1 - Standalone Article Templates Prototype

- Create a small plugin for article/post-type templates.
- Register settings for article layout presets.
- Add post type default layout selection.
- Add optional per-post override.
- Render a clean article template shell.
- Scope CSS to target post types.
- Test on a non-SKVN Marine site first.

### Phase 2 - Stabilization

- Confirm editor UX is understandable.
- Confirm template output works with real post content.
- Confirm TOC behavior.
- Confirm mobile stacking.
- Confirm comments/meta/related blocks can be toggled cleanly.
- Document template contracts.

### Phase 3 - Consolidation Candidate

- Decide whether to keep standalone or merge into `Gutenberg Turbo`.
- If merged, move code into `modules/article-templates/`.
- Keep backward-compatible option migration.
- Keep the standalone plugin installable until migration is verified.

---

## Acceptance For Planning

- [x] Split-first, merge-later strategy is documented.
- [x] Future umbrella plugin names are documented.
- [x] Article template module candidate is documented.
- [x] Template customization model is documented.
- [x] Source-of-truth boundaries are documented.
- [x] Migration direction is documented.
- [x] Current milestone remains unchanged.

