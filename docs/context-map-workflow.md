# Context Mapping Workflow

## Purpose

`.context/` is the AI working memory and constraint layer.

`docs/` is the human architecture memory.

## V1 Decision

`.context/` may temporarily live in `main`.

This is acceptable because V1 is local/dev-oriented.

## V2 Decision

Separate branches:

```txt
main = production clean
dev = active development + context
feature/* = small tasks
```

Move `.context/` out of production branch if needed.

## Recommended Context Files

```txt
.context/
  PROJECT.md
  THEME_SKVN_MARINE.md
  PLUGIN_SKVN_MARINE_BLOCKS.md
  BLOCK_SLIDER.md
  QUOTE_FLOW.md
  TENSIONS.md
```

## Tension Register

Use `.context/TENSIONS.md` whenever AI finds a conflict, such as:

- Wanting to edit GeneratePress parent theme.
- Wanting to add a dependency without approval.
- Wanting to place custom blocks in the theme.
- Wanting to overwrite editor-provided ALT text.
