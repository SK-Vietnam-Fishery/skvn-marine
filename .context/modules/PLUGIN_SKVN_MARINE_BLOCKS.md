# PLUGIN — skvn-marine-blocks

## [manual] Role

Custom Gutenberg blocks with logic.

## [manual] Architecture

Architecture: **Block-centric feature modules with minimal shared services**.

Each block owns feature-specific logic:

- `block.json` — block metadata and attributes
- `edit.tsx` — editor UI
- `save.tsx` — saved markup
- `view.ts` — frontend runtime only when needed
- `types.ts` — block-local TypeScript types when useful

Shared services are allowed only for cross-block invariants/utilities:

- `src/shared/motion.ts` — `prefers-reduced-motion` helpers
- `src/shared/a11y.ts` — keyboard/ARIA helpers
- `src/shared/dom.ts` — safe DOM/config parsing helpers
- `src/shared/types.ts` — truly shared TypeScript types

Do not move block-specific behavior into `shared/`.

Rule: put logic in `shared/` only when it has at least 2 consumers, or when it enforces a project invariant such as reduced motion, keyboard accessibility, or safe JSON parsing.

## [manual] Architecture Decision Rationale

Decision type: architecture/design-pattern decision.
Status: accepted.
Pattern: **Block-centric feature modules with minimal shared services**.

Why:

- Gutenberg block plugin naturally uses block as the primary unit, not MVC controller/view.
- V1 has interactive blocks (`slider`, `accordion`) that need consistent frontend runtime rules.
- Shared services prevent duplication for project invariants: reduced motion, keyboard accessibility, safe DOM/config parsing.
- Keeping feature logic inside each block avoids over-abstracting early.

Rejected patterns:

- **MVC**: rejected because Gutenberg blocks already define their own editor/save/frontend lifecycle. For this plugin, MVC would add artificial layers without clear benefit.
- **Service-heavy architecture**: rejected for V1 because the plugin is small and block-focused.
- **All logic inside each block with no shared layer**: rejected because accessibility, motion, and config parsing would be duplicated and drift between blocks.

Rules:

- Block-specific behavior stays in the block folder.
- Shared code must be cross-block utility or project invariant.
- Do not create shared abstractions for a single consumer unless it enforces a mandatory invariant.

## [manual] Folder Convention

```text
src/
  index.ts
  shared/
    a11y.ts
    dom.ts
    motion.ts
    types.ts
  slider/
    block.json
    edit.tsx
    save.tsx
    view.ts
    types.ts
  slide/
    block.json
    edit.tsx
    save.tsx
    types.ts
  accordion/
    block.json
    edit.tsx
    save.tsx
    view.ts
    types.ts
```

## [manual] Planned Blocks

- `skvn-marine/slider`
- `skvn-marine/slide`
- `skvn-marine/accordion`

## [manual] Deferred Blocks

- V1 KHÔNG build custom `skvn-marine/product-grid`.
- V1 KHÔNG build custom `skvn-marine/product-list`.
- V1 dùng WooCommerce native blocks/patterns cho product grid/list.
- V2 mới thêm Product Grid/List custom hoặc style blocks liên quan nếu cần.

## [manual] Rules

- Use TypeScript.
- Use `block.json`.
- Use `skvn-marine` block namespace.
- Do not depend on GeneratePress-specific markup.
