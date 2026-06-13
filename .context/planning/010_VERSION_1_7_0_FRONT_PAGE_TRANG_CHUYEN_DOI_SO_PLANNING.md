# VERSION 1.7.0 — Front page trang Chuyển đổi số Planning

Status: future planning.
Source artifact: `.local/test-artifacts/ChuyenDoiSo.html`.

## Goal

Build a front page inspired by the external Chuyển đổi số document hub UI.

The page is not a direct copy of the artifact. It is a SKVN-governed front page pattern/module that can show real WordPress resource/document data.

## Intended Surface

- Hero with whole-site search form.
- Resource/post list where each item can show thumbnail or fallback icon.
- Tags from the external plugin-owned taxonomy.
- Category list with real counts.
- Document/guide/stat counts from real data.
- Access/login CTA card where needed.
- Footer handled by existing SKVN/GeneratePress footer path.

## Ownership Boundary

- External plugin owns custom taxonomy and search/data logic.
- WordPress/search plugin owns whole-site search behavior.
- `skvn-marine-blocks` may own wrapper blocks, resource list rendering, editor controls, and integration hooks.
- `skvn-marine` theme owns `skvn-*` visual classes, spacing, badges, cards, search form styling, and editor/frontend parity.

Do not recreate the external plugin taxonomy in `skvn-marine-blocks`.

Do not write a custom search engine in SKVN unless human explicitly changes scope.

## 1.1.0 Brainstorm Trigger

When `1.1.0 — Layout Blocks` is active, review this front-page concept before implementing unrelated layout blocks.

Brainstorm questions:

- Which pieces can be solved by `skvn-marine/card-grid` and `skvn-marine/card`?
- Which pieces need a separate resource/document list contract?
- Can core Query Loop plus theme CSS cover the resource list well enough?
- Do tag badges and category-count rows need theme-only patterns or plugin block controls?
- What frontend behavior is purely styling vs. real plugin/search integration?

Review result during `1.1.0 — Layout Blocks`:

- `skvn-marine/card-grid` and `skvn-marine/card` can cover static or editor-curated card areas such as access CTA cards, KPI/support cards, and non-query feature/resource teaser cards.
- The main resource/document list should not be forced into generic cards if it needs real query data, thumbnails/fallback icons, taxonomy badges, or access/status semantics. Keep a separate `resource-list` / Query Loop evaluation path.
- Category/count rows need real WordPress data and should stay in the future taxonomy-count/search integration boundary, not the generic card-grid implementation.
- Search styling can reuse theme-owned visual classes later, but search behavior remains WordPress/search-plugin owned.
- Tag/status badges need governed classes or block controls later; do not solve them through raw Tailwind or generic card variants in 1.1.0.

## Candidate Contracts

Theme classes to evaluate:

- `skvn-resource-front`
- `skvn-resource-hero`
- `skvn-search-form`
- `skvn-resource-list`
- `skvn-resource-item`
- `skvn-resource-item__icon`
- `skvn-resource-item__thumb`
- `skvn-badge`
- `skvn-badge--free`
- `skvn-badge--internal`
- `skvn-badge--locked`
- `skvn-taxonomy-list`
- `skvn-taxonomy-list__count`
- `skvn-access-card`
- `skvn-kpi-strip--light`

Plugin block candidates:

- `skvn-marine/resource-list`
- `skvn-marine/resource-search`
- `skvn-marine/taxonomy-count-list`

Custom block decision is deferred until the core Query Loop and pattern alternative is evaluated.

## Risks

- External taxonomy/search plugin may be inactive; SKVN output must degrade safely.
- Counts must use real data, not static artifact numbers.
- Search styling can be theme-owned, but search behavior should remain owned by WordPress/search plugin.
- Raw Tailwind, inline scripts, and artifact runtime scripts must not enter Gutenberg content.
- Badge/status semantics need editor-safe presets before marketing users rely on them.

## Acceptance Draft

- Human confirms external plugin taxonomy names and search integration boundary.
- Front-page information architecture is documented before code.
- Theme visual contracts are defined before plugin output depends on them.
- Post/resource list supports thumbnail or fallback icon.
- Tags and status badges use governed classes/presets.
- Category and document counts come from real WordPress data.
- Whole-site search uses WordPress/search-plugin behavior or documented hook fallback.
- Core Query Loop/pattern alternative is evaluated before custom block implementation.
- Custom block, if approved, lives in `skvn-marine-blocks`.
- No raw Tailwind, inline CSS, or inline scripts from the benchmark artifact are used as production content.
