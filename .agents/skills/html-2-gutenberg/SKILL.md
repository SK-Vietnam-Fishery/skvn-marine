---
name: html-2-gutenberg
description: Translate HTML/CSS artifacts, AI layout mockups, or screenshots into SKVN Marine-safe Gutenberg block markup and implementation contracts. Use when Codex must convert external HTML/CSS into paste-ready WordPress block comment syntax, identify skvn-* theme classes, separate editable content from theme-controlled decoration/motion, validate markup safety, or plan future HTML-2-Gutenberg tooling in skvn-marine-blocks.
---

# HTML-2-Gutenberg

## Core Rule

Convert artifacts into WordPress-native Gutenberg output. Do not paste raw `<style>`, `<script>`, canvas content, SVG decoration, dynamic Tailwind-only class contracts, or base64/data URI images into page content.

For SKVN Marine:

- Plugin `skvn-marine-blocks` owns HTML-2-Gutenberg tooling: artifact intake, translation workflow, validation, future admin publisher/create-page flow.
- Theme `skvn-marine` owns visual output contract: `skvn-*` classes, CSS, tokens, block styles, patterns, editor/frontend parity, decorative/background animation, and shared animation runtime.
- Core blocks own editable content: groups, columns, headings, paragraphs, buttons, images, lists.
- Custom blocks are last resort: only if core blocks plus theme patterns are insufficient.

## Required Project Context

When working inside `D:\Github\skvn-marine`, load these before acting:

1. `AGENTS.md`
2. `.context/GLOBAL.md`
3. `.context/MILESTONES.md`
4. `.context/TENSIONS_OPEN.md`
5. `.context/TENSIONS_ACTIVE.md`
6. `.context/modules/THEME_SKVN_MARINE.md`
7. `.context/modules/PLUGIN_SKVN_MARINE_BLOCKS.md`
8. `docs/standards/site-branding-guideline.md`

If the workflow doc exists, read `docs/workflows/html-2-gutenberg-workflow.md`; otherwise read `docs/workflows/layout-translator-workflow.md` and treat it as the legacy name.

## Translation Workflow

1. Inspect the artifact.
2. Extract semantic content: heading, paragraph, CTA, image, list, card, product/category text.
3. Classify presentation-only parts: wrappers, grids, spacing, colors, decorative SVG/waves/particles, background motion.
4. Emit Gutenberg block markup using core blocks and stable `skvn-*` classes.
5. Emit implementation contracts, not inline CSS/JS.
6. Validate paste-safety before returning output.

## Mapping Rules

| Artifact | Gutenberg target |
|---|---|
| `section`, wrapper `div` | `core/group` |
| Grid / 2 columns | `core/columns` |
| `h1`-`h6` | `core/heading` |
| `p` | `core/paragraph` |
| CTA link/button | `core/buttons` + `core/button` |
| Content image | `core/image` |
| Repeated cards | `core/group`/`core/columns` card pattern |
| Feature/stat list | `core/list` or repeated groups |
| Decorative SVG/waves/particles | `not_translated` + theme CSS contract |
| Motion/hover/loop/parallax | `animation_contract` + shared runtime |
| Complex state/control | plugin block candidate later |

## Output Shape

For formal translation tasks, return:

```text
gutenberg_markup
required_classes
theme_css_contract
animation_contract
assets_needed
not_translated
risks
```

For user requests asking only for paste-ready markup, lead with the `gutenberg_markup` and keep contracts short.

## Paste-Safe Markup Rules

- Use full WordPress block comment syntax.
- Never emit empty/self-closing image blocks such as `<!-- wp:image ... /-->`.
- Image blocks must include opening comment, `<figure>`, `<img src="..." alt="..."/>`, and closing comment.
- Replace missing, `data:` URI, or oversized image sources with a placeholder URL.
- Do not include raw `<style>`, `<script>`, or inline event handlers.
- Escape visible text for HTML when needed.
- Keep primary text and CTA editable.
- Use `skvn-*` classes only for project contracts.

Safe image pattern:

```html
<!-- wp:image {"sizeSlug":"large","className":"skvn-hero__image"} -->
<figure class="wp-block-image size-large skvn-hero__image"><img src="https://placehold.co/900x650/eef6ff/0a2a4a?text=Replace+Image" alt="Replace with project image"/></figure>
<!-- /wp:image -->
```

## Motion Contract Rules

All motion must be contract-only unless implementing theme runtime:

- Trigger: load, scroll, hover, loop.
- Initial state: visible in editor; no hidden editing content.
- Final state: visible/static fallback.
- Duration/easing/stagger.
- Reduced-motion fallback.
- Editor behavior: static or simplified.

Implementation must use the shared theme runtime and respect `prefers-reduced-motion`.

## Validation Checklist

Before final answer or file output:

```text
[ ] No raw <style> in Gutenberg markup.
[ ] No raw <script> in Gutenberg markup.
[ ] No self-closing image blocks.
[ ] No src="data: images.
[ ] Text/CTA remains editable.
[ ] Uses core blocks first.
[ ] Uses skvn-* classes.
[ ] Decorative/motion items are in contracts, not content.
[ ] Theme/plugin ownership boundary is respected.
[ ] No GeneratePress parent change.
```

## Useful Commands

Existing repo CLI may be available:

```bash
node tools/layout-translator/translate-layout.mjs --input path/to/artifact.html
node tools/layout-translator/translate-layout.mjs --input path/to/artifact.html --output translated.md
```

On this machine, use WSL Node if Windows `node.exe` is blocked:

```bash
wsl -d Debian -- bash -lc "source /home/shinkuro/.nvm/nvm.sh && nvm use 20 && cd /mnt/d/Github/skvn-marine && node tools/layout-translator/translate-layout.mjs --input path/to/artifact.html"
```
