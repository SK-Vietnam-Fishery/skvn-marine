# Product Category Grid — 1.3.20 onsite runbook

Created 2026-11-04. **Chờ kiểm chứng**: source-only implementation, no runtime
PASS yet. Plan: `.context/planning/049_VER_1_3_20_PRODUCT_CATEGORY_GRID_EXECUTION_PLAN.md`.
Spec/acceptance: `docs/ideations/category-grid-spec.md` (CG-*).
Implementation contract: `docs/artifacts/editor-ui/product-category-grid-1.3.20.md`.
Baseline UI stays `docs/artifacts/editor-ui/category-grid-picker-prototype.md`.

## 0. Missing inputs — human must provide before the run

| Needed | Status |
|---|---|
| Onsite editor URL of a test page using the block | **Cần human cung cấp** |
| Onsite frontend URL of the same page | **Cần human cung cấp** |
| WP / Gutenberg / WooCommerce versions on that site | **Cần human cung cấp** |
| Deployed build/commit of `skvn-marine-blocks` | **Cần human cung cấp** |
| Authorized test environment for failures and role tests | **Cần human cung cấp** |
| Store setting `woocommerce_hide_out_of_stock_items` (yes/no) | **Cần human cung cấp** |

Do not invent URLs or versions. If the local site differs from onsite content,
the local page is not the source of truth (onsite-first rule).

## 1. Preconditions / fixtures

Build the plugin first (see §6). Then, in WooCommerce → Products → Categories:

| Fixture | Purpose |
|---|---|
| `Cat-Direct` with 2+ published products | baseline card |
| `Cat-Child` (child of `Cat-Parent`) with products only in the child | descendant counting, path display |
| `Cat-Empty` with no products | hide-empty |
| `Cat-Hidden` whose only products are catalog-hidden (`exclude-from-catalog`) | visibility counting |
| `Cat-OOS` whose only products are out of stock | store-policy counting |
| `Cat-Draft` whose only product is a draft | publish-only counting |
| One category with **no** thumbnail, one with portrait, one with transparent PNG | placeholder + `Contain` vs `Cover` |
| One category with long name + long HTML description | wrapping, 3-line excerpt, description round-trip |

Roles: one administrator, one role without `edit_term`/`upload_files`
(e.g. Contributor) if the environment allows safe role testing.

## 2. Editor cases

| ID | Steps | Expected / pass criteria | Evidence |
|---|---|---|---|
| CG-UX-01 | Insert the block → “Add categories” → search, toggle parent and child with identical names, Apply; reopen picker and confirm state; uncheck then recheck an existing term in the picker and Apply once more; remove one, reorder | Dialog matches locked baseline (search, hierarchy path, Apply/Cancel, selected count); parent selection does **not** auto-select children; Apply keeps the existing order and only appends new picks (re-checking a term must not move its card); order is the render order; no duplicate term; keyboard-only path works | Video + serialized `categories` attribute |
| CG-UX-01b | Drag handle reorder (desktop), Up/Down arrow keys, “Move up/Move down” buttons (touch/coarse pointer) | All three reorder the same list; arrow buttons are focusable but visually clipped on fine pointers; focus stays on the moved item | Video + DOM focus log |
| CG-UX-02 | Two blocks on one page using the same term; change its image in block A → Save category; open block B details | Details are shared (same term), design/link are local per block; B shows the new image after refetch; A's card unchanged in design | Screenshots of both blocks + term screen |
| CG-UX-02b | Change only the thumbnail; before/after compare the stored description in WooCommerce category screen | Description is byte-identical (not normalized, trimmed or re-escaped) | Before/after description text |
| CG-UX-03 | Edit a description → Page Save; then edit again and trigger a periodic/background autosave (wait for autosave) | Explicit Save/Update/Publish flushes drafts; background autosave does **not**; per-category Save works on its own; a failure keeps the draft and shows a per-item error + notice | Network panel (request order), notices, persisted values |
| CG-UX-03b | While a save is in flight, keep typing; let the response land | Newer text stays dirty; the stale response never overwrites it; status returns to “Unsaved changes”, not “No unsaved changes” | Network + textarea content |
| CG-UX-03c | Simulate conflict: change the same term in another tab/session, then save the stale draft | Item reports conflict; “Reload current value” adopts the value returned by the conflict (draft and base move forward, cards refetch); “Keep my draft” re-bases on that value and only keeps the fields that still differ dirty; no silent overwrite | Two sessions + responses |
| DS-01…DS-07 | Carry-over sidebar cases from `plugin-design-sidebar-1.3.19.md`; this block must appear as a supported target with its own title/summary and additive reset keys | Shell is the 1.3.19 one (no second sidebar); reset restores only the current group's category keys; query/selection untouched | Screenshots + attribute diff |

## 3. Frontend cases

| ID | Steps | Expected / pass criteria | Evidence |
|---|---|---|---|
| CG-UI-01 | 1, 5 and 10 categories; wide column, narrow column on desktop, tablet, mobile | Columns 3/2/1 by **container** width; rows grow automatically; nothing is clipped, no horizontal overflow with a visible vertical scrollbar; card heights align | Frontend screenshots + `getBoundingClientRect()` for the wrapper and one card at each width |
| CG-UI-02 | Long name, long HTML description, missing image, all display toggles on/off | Name wraps in full; description is plain text, max 3 lines; missing image shows the WooCommerce placeholder; count/description/CTA off by default; toggles apply per block only | Paired editor/frontend screenshots |
| CG-UI-03 | `Cat-Empty`, `Cat-Hidden`, `Cat-OOS`, `Cat-Draft`, parent with products only in child; then make an empty category non-empty and reload | Empty categories are hidden on the frontend; parent counts descendants; visibility/stock follow store policy; the card reappears in the same position when it qualifies; all-empty selection renders nothing at all (no empty shell, no heading) | Fixture data + before/after screenshots |
| CG-UI-04 | Change a category slug; use a custom URL on another card; keyboard Tab through cards; check focus ring and contrast | Archive links follow the new slug; custom URL is used for that card only; **one** tab stop per card (CTA is not a second stop); focus ring visible; contrast readable | DOM snippet + screenshots |
| CG-UI-04b | Mobile/coarse pointer | Card is tappable everywhere; CTA never hover-only | Device emulation screenshot |

## 4. Technical cases

| ID | Steps | Expected / pass criteria | Evidence |
|---|---|---|---|
| CG-TECH-01 | Save description + thumbnail as admin; then as a role without `edit_term`; then as a role without `upload_files`; try a non-image attachment ID | Native `product_cat` description/`thumbnail_id` are updated; denied role gets a per-item forbidden error and nothing is written; invalid attachment is rejected; bold/italic/link survive; paragraph/heading/list are stripped **by WordPress** with a notice | REST responses + WooCommerce category screen |
| CG-TECH-02 | Slow/overlapping saves, two blocks, two tabs; duplicate block | Revisions prevent an older response from cleaning a newer draft; no duplicate or contradictory term writes; no stuck “Saving…” | Request order + persisted values |
| CG-TECH-03 | Reload editor; Page Undo/Redo; rename a term; delete an attachment used as thumbnail; delete a selected term | Block settings survive reload; no name/image/description snapshot in saved markup; renamed term shows the new name; deleted attachment falls back to placeholder; deleted term is skipped on frontend and flagged in editor | Saved post content + entity changes |
| CG-TECH-04 | Open the page with WooCommerce inactive; add many categories; switch Inspector panels repeatedly | No fatal/console errors; block renders nothing and the editor keeps the selection with an explanation; Grid loads **no** Swiper/carousel asset; no repeated REST storm on panel switching | Network list + console |
| CG-TECH-05 | Existing Product Grid, Product Carousel, Post Grid, Post Carousel pages | No visual/behaviour regression; existing serialized content untouched | Before/after screenshots |
| Missing-term case | Delete a selected term in another tab, then reopen the page | Editor lists it as missing with a remove hint; frontend skips it; saving the page keeps other cards intact | Screenshots + response |

## 5. Provisional values to measure

Container breakpoints `480px` / `880px` are C1 placeholders. For each of the 1/5/10
category sets, record the wrapper width where the column count changes and confirm
it matches the intended 3/2/1 behaviour with a vertical scrollbar present. Report
the measured widths so the values can be approved or corrected.

## 6. Commands (human runs)

```bash
php -l wp-content/themes/skvn-marine/functions.php
source /home/shinkuro/.nvm/nvm.sh && nvm use 20 && cd /mnt/d/Github/skvn-marine/wp-content/plugins/skvn-marine-blocks && npm run build && find modules/ -name "*.php" -exec php -l {} \;
```

This milestone **adds a PHP runtime include**
(`modules/category-grid/category-grid.php`), so the deploy artifact must be
audited before any zip/upload:

```bash
node tools/build-deploy-artifact.mjs
bash tools/package-plugin-zip.sh
unzip -l build/skvn-marine-blocks.zip | grep 'skvn-marine-blocks/modules/category-grid/'
```

Do not deploy or commit implicitly. Send back: build/lint output, the zip audit
result, the missing inputs from §0, and per-case actual/status/evidence.

## 7. Status ledger

| Area | Status |
|---|---|
| CG-UI-01…04 | Chờ kiểm chứng |
| CG-UX-01…03 (incl. b/c) | Chờ kiểm chứng |
| CG-TECH-01…05 | Chờ kiểm chứng |
| DS-01…07 carry-over | Chờ kiểm chứng (owner 1.3.19) |
| Container thresholds 480/880 | Chờ đo onsite |
| Build / `php -l` / artifact audit | Chưa chạy |
