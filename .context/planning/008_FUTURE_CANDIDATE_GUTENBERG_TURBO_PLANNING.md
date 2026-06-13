# Future Candidate Planning - Gutenberg Supercharger / Gutenberg Remap

> Planning reference for the future umbrella plugin that may consolidate smaller Gutenberg enhancement plugins after they are stable.
> Load this file when planning article templates, Gutenberg content templates, editor supercharger features, reusable post-type layout systems, or plugin consolidation.

---

## Status

Status: **FUTURE_CANDIDATE**

This file does not change the current milestone. Current milestone remains managed by `.context/MILESTONES.md`.

Candidate target:

```text
V4 / 4.0.0
```

This is a planning candidate only. Do not rename current plugins, namespaces, option keys, or build paths unless the human explicitly moves the project into that future milestone.

Candidate project name:

```text
Gutenberg Supercharger
```

Primary product name:

```text
Gutenberg Supercharger
```

Pro / commercial stage name:

```text
Gutenberg Supercharger Stage 2
```

Retained naming candidate:

```text
Gutenberg Remap
```

Tagline:

```text
Remap Gutenberg. Turbocharge your site.
```

Community tagline:

```text
Make your site feel more "Á đù VTEC".
```

Naming notes:

```text
Gutenberg Supercharger is the project direction/name for the future umbrella plugin.
The normal/core product edition should be named Gutenberg Supercharger.
The pro/commercial edition should be named Gutenberg Supercharger Stage 2.
Gutenberg Remap remains a researched alternate/redirect candidate, not the preferred current product name.
Gutenberg Turbo remains a retired/legacy codename and should not be used as the current preferred name.
```

---

## Product Packaging Stages

Use stage naming for product packaging, not SemVer.

Stage 1:

```text
Gutenberg Supercharger
```

Purpose:

- Core/free or standard edition.
- Proves the foundation: safe Gutenberg layout remapping, template presets, editor governance, and migration-ready modules.
- Must be useful on real sites, not only a demo or teaser.
- Should avoid raw CSS/class workflows for normal editors.

Stage 2:

```text
Gutenberg Supercharger Stage 2
```

Purpose:

- Pro/commercial edition.
- Adds advanced governance, reusable template libraries, site/client presets, import/export, compatibility modules, and workflow automation.
- Should sell workflow depth and control, not just more sliders or arbitrary styling controls.

Community tagline remains:

```text
Make your site feel more "Á đù VTEC".
```

This tagline is for community/dev flavor. It should not become the formal plugin header name.

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
gutenberg-supercharger
```

The future umbrella plugin should behave like a modular Gutenberg enhancement suite, not a visual freeform page builder.

Current application rule:

```text
Use the module-shaped architecture now, but do not create the umbrella plugin now.
```

For 0.9.0 Footer Page Settings, this means:

- Implement `footer-settings` as a module inside the current `skvn-marine-blocks` plugin.
- Keep plugin slug `skvn-marine-blocks`.
- Keep text domain `skvn-marine-blocks`.
- Keep option key `skvn_footer_page_id`.
- Do not create a `gutenberg-supercharger` or `gutenberg-turbo` plugin.
- Do not rename namespaces, plugin headers, build entrypoints, activation paths, or option keys.
- The module should be easy to move later into `gutenberg-supercharger/modules/footer-settings/`.

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
gutenberg-supercharger/
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

Small plugins should be built so they can later move into `gutenberg-supercharger/modules/*` with minimal rewrite.

For current SKVN Marine V1 work, "migration-ready" means folder/module separation only. It does not mean migrating the live plugin identity.

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
PHP prefix:    gutenberg_supercharger_
Option prefix: gutenberg_supercharger_
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

- Decide whether to keep standalone or merge into `Gutenberg Supercharger`.
- If merged, move code into `modules/article-templates/`.
- Keep backward-compatible option migration.
- Keep the standalone plugin installable until migration is verified.

---

## Acceptance For Planning

- [x] Split-first, merge-later strategy is documented.
- [x] Future umbrella plugin and product/marketing names are documented.
- [x] Tagline and community tagline are documented.
- [x] V4 / 4.0.0 is documented as candidate timing, not current scope.
- [x] Article template module candidate is documented.
- [x] Template customization model is documented.
- [x] Source-of-truth boundaries are documented.
- [x] Migration direction is documented.
- [x] Current 0.9.0 footer settings guardrail is documented: module-shaped implementation inside `skvn-marine-blocks`, no umbrella plugin rename.
- [x] Current milestone remains unchanged.
