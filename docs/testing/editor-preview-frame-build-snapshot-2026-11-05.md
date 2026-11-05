# Editor frame generated snapshot — 2026-11-05

Status: **existing generated assets preserved; not runtime approval**. Agent did not run build/lint, modify compiled files by hand, deploy or push. Source baseline is `b4dba14`; previous tracking checkpoint `9f3d9a7`.

## Observed provenance

Before this commit task, working tree contained changes to `build/index.ts.js`, `index.ts.asset.php`, `style-index.ts.css`, `style-index.ts-rtl.css`, and new `index.ts.css`/`index.ts-rtl.css`. Filesystem timestamps observed for JS and CSS: 2026-11-05 07:31:20 UTC; this does not identify who built them or prove source parity/build success. Script asset version is `822878052e883c9b90e3`.

Text inspection found frame/overview labels in JS and `.skvn-editor-preview-frame` rules in the new 585-byte `index.ts.css`/RTL counterpart. Registered `style-index.ts.css` lacks those shared frame rules. Scoped Git whitespace check PASS (CRLF normalization warnings only).

## Known stylesheet integration gap

Prior source `src/shared/editor-preview-frame.ts` imported `./editor-preview-frame.css`. The observed build extracts that file to `build/index.ts.css`, separate from existing `style-index.ts.css`. Bootstrap `skvn-marine-blocks.php:86–96` registers only `build/style-index.ts.css` for the editor; no registration/reference for `build/index.ts.css` was found in that bootstrap. Therefore preserving the new generated file alone does **not** establish that frame styling is loaded in WordPress.

Root authorized correcting this integration boundary in the commit task: move the unchanged CSS to `src/shared/editor-preview-frame/style.css` and import `./editor-preview-frame/style.css`, following the existing `style.css` extraction path used by registered editor styles. Import resolution, TypeScript syntax and CSS parse checked; no build was run, so actual extraction into `style-index.ts.css` still needs the human rebuild below. The existing generated snapshot predates this source correction and retains its separate, unregistered `index.ts.css` frame rules.

Keep EF/CP status **Chờ kiểm chứng**. Before claiming appearance PASS, rebuild the corrected source through the human workflow, verify frame rules are now in the registered editor stylesheet (including iframe), and run `docs/testing/editor-preview-frames-1.3.19.md`. This snapshot is not a deploy candidate or a replacement for that integration check. No PHP runtime include/require changed in this task.
