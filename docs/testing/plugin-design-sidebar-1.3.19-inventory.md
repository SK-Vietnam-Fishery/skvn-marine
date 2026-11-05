# Sidebar 1.3.19 — P0 control inventory

2026-11-04; baseline edae8f3813b0200ad39b686c1bc1d4a3e43435be + dirty working tree. Generated from current registration, then classified by placement. Source evidence only; no runtime PASS. Schema defaults below are not a production approval for media ranges.

Paths relative to plugin. Controls/preview: src/collection/controls.tsx; registration: src/{product,post}-collection/block.json; card consumers: modules/collection-render/cards.php; query consumers: modules/collection-render/query-{products,posts}.php. Every local value stays a block attribute, uses Page Save/Undo, and keeps its existing key/type. Reset in Selected block touches only its component; query/URLs/layout are preserved. Missing old attrs keep registered defaults; missing/empty mediaLayout keeps legacy frame rather than migrating.

| Key / label | Type; registered default Product / Post | Surface / condition | PHP consumer; test IDs |
|---|---|---|---|
| `layout` · layout | string; "grid" / "grid" | Inspector / basic | cards.php ; E06, DS-04/06 |
| `contentWidth` · content width | string; unset / unset | Inspector / basic | cards.php ; E06, DS-04/06 |
| `specValuesLimit` · spec values limit | number; unset / N/A | Inspector / basic | cards.php ; E06, DS-04/06 |
| `eyebrow` · eyebrow | string; "" / "" | Inspector / basic | cards.php ; E06, DS-04/06 |
| `heading` · heading | string; "Featured products" / "Latest articles" | Canvas / content | cards.php ; E06, DS-04/06 |
| `showHeading` · show heading | boolean; true / true | Inspector / basic | collection wrapper/runtime or legacy; verify exact consumer in E06; E06, DS-04/06 |
| `intro` · intro | string; "" / "" | Canvas / content | cards.php ; E06, DS-04/06 |
| `accessibleLabel` · accessible label | string; "" / "" | Inspector / basic | collection wrapper/runtime or legacy; verify exact consumer in E06; E06, DS-04/06 |
| `categories` · categories | array; [] / [] | Inspector / Query | query APIs ; E04/05, TECH-01/12 |
| `tags` · tags | array; [] / [] | Inspector / Query | query APIs ; E04/05, TECH-01/12 |
| `relation` · relation | string; "OR" / "OR" | Inspector / Query | query APIs ; E04/05, TECH-01/12 |
| `orderMode` · order mode | string; "newest" / "newest" | Inspector / Query | query APIs ; E04/05, TECH-01/12 |
| `itemsToShow` · items to show | number; 3 / 3 | Inspector / Query | query APIs ; E04/05, TECH-01/12 |
| `responsivePreset` · responsive preset | string; "3-2-1" / "3-2-1" | Inspector / basic | cards.php ; E06, DS-04/06 |
| `showImage` · show image | boolean; true / true | Inspector / basic | collection wrapper/runtime or legacy; verify exact consumer in E06; E06, DS-04/06 |
| `imageRatio` · image ratio | string; "1:1" / "16:9" | Inspector / basic | collection wrapper/runtime or legacy; verify exact consumer in E06; E06, DS-04/06 |
| `cardStyle` · card style | string; "default" / "default" | Inspector / basic | collection wrapper/runtime or legacy; verify exact consumer in E06; E06, DS-04/06 |
| `equalHeight` · equal height | boolean; true / true | Inspector / basic | collection wrapper/runtime or legacy; verify exact consumer in E06; E06, DS-04/06 |
| `badgeBehavior` · badge behavior | string; "display" / "display" | Inspector / basic | cards.php ; E06, DS-04/06 |
| `showPrice` · show price | boolean; true / N/A | Inspector / basic | collection wrapper/runtime or legacy; verify exact consumer in E06; E06, DS-04/06 |
| `showSku` · show sku | boolean; false / N/A | Inspector / basic | collection wrapper/runtime or legacy; verify exact consumer in E06; E06, DS-04/06 |
| `showStock` · show stock | boolean; false / N/A | Inspector / basic | collection wrapper/runtime or legacy; verify exact consumer in E06; E06, DS-04/06 |
| `showProductCategories` · show product categories | boolean; true / N/A | Inspector / basic | collection wrapper/runtime or legacy; verify exact consumer in E06; E06, DS-04/06 |
| `showProductTags` · show product tags | boolean; false / N/A | Inspector / basic | collection wrapper/runtime or legacy; verify exact consumer in E06; E06, DS-04/06 |
| `productActionMode` · product action mode | string; "quote" / N/A | Inspector / basic | cards.php ; E06, DS-04/06 |
| `customActionUrl` · custom action url | string; "" / "" | Inspector / basic | cards.php ; E06, DS-04/06 |
| `appendQuoteContext` · append quote context | boolean; true / N/A | Inspector / basic | collection wrapper/runtime or legacy; verify exact consumer in E06; E06, DS-04/06 |
| `showArrows` · show arrows | boolean; true / true | Inspector / basic | collection wrapper/runtime or legacy; verify exact consumer in E06; E06, DS-04/06 |
| `showPagination` · show pagination | boolean; true / true | Inspector / basic | collection wrapper/runtime or legacy; verify exact consumer in E06; E06, DS-04/06 |
| `autoplay` · autoplay | boolean; false / false | Inspector / Layout; Post Carousel; delay requires autoplay | collection wrapper/runtime or legacy; verify exact consumer in E06; TECH-04/05 |
| `autoplayDelay` · autoplay delay | number; 5000 / 5000 | Inspector / Layout; Post Carousel; delay requires autoplay | cards.php ; TECH-04/05 |
| `archiveUrl` · archive url | string; "" / "" | Inspector / basic | cards.php ; E06, DS-04/06 |
| `archiveLabel` · archive label | string; "" / "" | Inspector / basic | cards.php ; E06, DS-04/06 |
| `catalogCtaUrl` · catalog cta url | string; "" / "" | Inspector / basic | cards.php ; E06, DS-04/06 |
| `showCatalogCta` · show catalog cta | boolean; false / false | Inspector / basic | collection wrapper/runtime or legacy; verify exact consumer in E06; E06, DS-04/06 |
| `catalogCtaLabel` · catalog cta label | string; "Download catalog" / "Download catalog" | Inspector / basic | cards.php ; E06, DS-04/06 |
| `showSpecChips` · show spec chips | boolean; true / N/A | Inspector / basic | collection wrapper/runtime or legacy; verify exact consumer in E06; E06, DS-04/06 |
| `chipStyle` · chip style | string; "tag" / N/A | Selected / Details; chips Product + showSpecChips | cards.php ; E09/10/11, DS-04/06 |
| `chipColorScheme` · chip color scheme | string; "" / N/A | Selected / Details; chips Product + showSpecChips | cards.php ; E09/10/11, DS-04/06 |
| `titleFontFamily` · title font family | string; "" / "" | Selected / Title | cards.php ; E08/09/11, DS-04/06 |
| `titleFontSize` · title font size | string; "" / "" | Selected / Title | cards.php ; E08/09/11, DS-04/06 |
| `titleColor` · title color | string; "" / "" | Selected / Title | cards.php ; E08/09/11, DS-04/06 |
| `detailsFontFamily` · details font family | string; "" / "" | Selected / Details; chips Product + showSpecChips | cards.php ; E09/10/11, DS-04/06 |
| `detailsFontSize` · details font size | string; "" / "" | Selected / Details; chips Product + showSpecChips | cards.php ; E09/10/11, DS-04/06 |
| `detailsColor` · details color | string; "" / "" | Selected / Details; chips Product + showSpecChips | cards.php ; E09/10/11, DS-04/06 |
| `actionFontFamily` · action font family | string; "" / "" | Selected / Action; backgrounds Product only | cards.php ; E07/09/11/14, DS-04/06 |
| `actionFontSize` · action font size | string; "" / "" | Selected / Action; backgrounds Product only | cards.php ; E07/09/11/14, DS-04/06 |
| `actionBackgroundColor` · action background color | string; "" / "" | Selected / Action; backgrounds Product only | cards.php ; E07/09/11/14, DS-04/06 |
| `actionTextColor` · action text color | string; "" / "" | Selected / Action; backgrounds Product only | cards.php ; E07/09/11/14, DS-04/06 |
| `actionHoverBackgroundColor` · action hover background color | string; "" / N/A | Selected / Action; backgrounds Product only | cards.php ; E07/09/11/14, DS-04/06 |
| `actionHoverTextColor` · action hover text color | string; "" / N/A | Selected / Action; backgrounds Product only | cards.php ; E07/09/11/14, DS-04/06 |
| `mediaLayout` · media layout | string; unset / N/A | Inspector / preset; Product + showImage | cards.php ; E12a/13a, G-MEDIA |
| `mediaWidthPercent` · media width percent | number; unset / N/A | Selected / Image; Product + showImage + matching preset; shadow fields require shadow toggle | cards.php ; E12a/13a, G-MEDIA |
| `mediaOffset` · media offset | number; unset / N/A | Selected / Image; Product + showImage + matching preset; shadow fields require shadow toggle | cards.php ; E12a/13a, G-MEDIA |
| `mediaShadowToggle` · media shadow toggle | boolean; unset / N/A | Selected / Image; Product + showImage + matching preset; shadow fields require shadow toggle | collection wrapper/runtime or legacy; verify exact consumer in E06; E12a/13a, G-MEDIA |
| `mediaShadowOffsetX` · media shadow offset x | number; unset / N/A | Selected / Image; Product + showImage + matching preset; shadow fields require shadow toggle | cards.php ; E12a/13a, G-MEDIA |
| `mediaShadowOffsetY` · media shadow offset y | number; unset / N/A | Selected / Image; Product + showImage + matching preset; shadow fields require shadow toggle | cards.php ; E12a/13a, G-MEDIA |
| `mediaShadowUnit` · media shadow unit | string; unset / N/A | Selected / Image; Product + showImage + matching preset; shadow fields require shadow toggle | cards.php ; E12a/13a, G-MEDIA |
| `mediaShadowBlur` · media shadow blur | number; unset / N/A | Selected / Image; Product + showImage + matching preset; shadow fields require shadow toggle | cards.php ; E12a/13a, G-MEDIA |
| `mediaShadowOpacity` · media shadow opacity | number; unset / N/A | Selected / Image; Product + showImage + matching preset; shadow fields require shadow toggle | cards.php ; E12a/13a, G-MEDIA |
| `mediaPadding` · media padding | number; unset / N/A | Selected / Image; Product + showImage + matching preset; shadow fields require shadow toggle | cards.php ; E12a/13a, G-MEDIA |
| `mediaPaddingUnit` · media padding unit | string; unset / N/A | Selected / Image; Product + showImage + matching preset; shadow fields require shadow toggle | cards.php ; E12a/13a, G-MEDIA |
| `showDate` · show date | boolean; N/A / true | Inspector / basic | collection wrapper/runtime or legacy; verify exact consumer in E06; E06, DS-04/06 |
| `showAuthor` · show author | boolean; N/A / false | Inspector / basic | collection wrapper/runtime or legacy; verify exact consumer in E06; E06, DS-04/06 |
| `showPostCategories` · show post categories | boolean; N/A / true | Inspector / basic | collection wrapper/runtime or legacy; verify exact consumer in E06; E06, DS-04/06 |
| `showPostTags` · show post tags | boolean; N/A / false | Inspector / basic | collection wrapper/runtime or legacy; verify exact consumer in E06; E06, DS-04/06 |
| `showExcerpt` · show excerpt | boolean; N/A / true | Inspector / basic | collection wrapper/runtime or legacy; verify exact consumer in E06; E06, DS-04/06 |
| `postActionMode` · post action mode | string; N/A / "read" | Inspector / basic | cards.php ; E06, DS-04/06 |
| `readMoreLabel` · read more label | string; N/A / "Read more →" | Inspector / basic | cards.php ; E06, DS-04/06 |

## Token and legacy truth table

- Typography: title/action size base/lg/xl/2xl; Details sm/base/lg; font roles empty/skvn-system/skvn-heading/skvn-ui. Empty inherits; unsupported slugs have no matching CSS and fall back. Font/color CSS has plugin fallbacks so another theme does not require SKVN variables.
- Colors: ten existing COLOR_PALETTE_OPTIONS slugs; chip dropdown now uses only consumed slugs. Existing custom/removed chip slug is retained on open, shown as unavailable in the UI, and not silently rewritten. No arbitrary color input for block overrides.
- New Product Carousel inserts mediaLayout=flush; old missing/empty keeps imageRatio/cover. Explicit flush/padded/floating opts in; Post does not get Product presets; Grid remains a shared regression fixture. Reset tuning keeps the chosen preset/ratio.
- Fonts global: theme registry and theme_mod only; palette/heading global: skvn_typography option only. The sidebar holds temporary drafts, not a second persisted store.
- No active control for a legacy registered attribute does not authorize deleting saved values; E06 must verify consumer/visibility before exposing it.

## API and deployment gates

Plugin header minimum WP6.5. No WP core or node_modules in this checkout; onsite version/build remains unknown. Public APIs checked against Gutenberg wp/6.5 source for PluginSidebar (edit-post SlotFill), openGeneralSidebar, block selection/attributes. No private DOM injection. @wordpress/scripts lock is build tooling, not proof of onsite editor APIs. Core data resolution error selector is guarded; runtime E04 must confirm error state on deployed WP. New imported packages are standard WordPress script handles resolved by wp-scripts, not package.json dependencies.

Sources: [WP6.5 PluginSidebar](https://raw.githubusercontent.com/WordPress/gutenberg/wp/6.5/packages/edit-post/src/components/sidebar/plugin-sidebar/index.js), [WP6.5 edit-post actions](https://raw.githubusercontent.com/WordPress/gutenberg/wp/6.5/packages/edit-post/src/store/actions.js), [WP6.5 selection selectors](https://raw.githubusercontent.com/WordPress/gutenberg/wp/6.5/packages/block-editor/src/store/selectors.js), [WP6.5 native query resolver](https://raw.githubusercontent.com/WordPress/gutenberg/wp/6.5/packages/core-data/src/resolvers.js).

Native query preview uses newest sample and explicitly discloses unsupported featured/manual/shuffle/OR semantics, Post cap5, and unavailable terms/Woo. Exact parity is an outstanding E05 gate, not a new query endpoint.
