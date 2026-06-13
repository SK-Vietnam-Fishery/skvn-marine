# Future Candidate / 2.x.x — Slider Transition And Pagination Extension API

Status: FUTURE CANDIDATE
Created: 2026-06-12
Version note: the human selected the 2.x.x phase but has not assigned a specific
minor version. Do not assign `2.n.0` or rename this file without confirmation.

## 1. Goal

Create a WordPress-native extension contract that keeps the base Slider light
while independent plugins or themes can register additional:

- transitions
- pagination renderers and editor previews
- pagination text capabilities
- mobile, fallback, and reduced-motion behavior

The core plugin must not require source changes whenever an extension adds a
fifth, sixth, or later choice.

Portal Zoom and Timed Tabs are reference extensions used to prove the API. They
are not part of the lightweight core transition/pagination implementation.

## 2. Ownership Boundary

Core owns:

- Slider/Slide schema and saved selection IDs
- PHP metadata registry and validation
- editor registry and available-choice UI
- the existing Swiper instance, movement state, and autoplay state
- Extension API versioning and compatibility
- safe fallback when an extension is absent

Extensions own:

- their own transition or pagination implementation
- their editor preview where applicable
- their frontend JavaScript and CSS
- local setup and cleanup
- declared preset support, fallback, and reduced-motion behavior
- visual quality and performance beyond core guarantees

An extension must not create another Swiper, autoplay controller, or
authoritative Slide index.

## 3. WordPress Integration Model

WordPress supplies actions, filters, script/style handles, dependencies, and
block editor APIs. It does not supply a standard Slider registry.

SKVN should expose a documented contract built on those primitives:

```text
PHP registry/filter
        -> validates metadata and asset handles
Editor registry/filter
        -> lists installed choices and previews
Frontend registry
        -> connects behavior to the existing Swiper instance
Saved block attributes
        -> store namespaced IDs and governed values only
```

Possible public names are illustrative until implementation review:

```text
skvn_marine_blocks_register_slider_transition()
skvn_marine_blocks_register_slider_pagination()
skvn_marine_blocks_slider_transitions
skvn_marine_blocks_slider_paginations
skvnMarine.sliderTransitions
skvnMarine.sliderPaginations
```

Final names must follow the current plugin prefix and text-domain rules.

## 4. Namespaced IDs

Examples:

```text
skvn/fade
skvn/directional-wipe
skvn/zoom-out
vendor/curtain
vendor/portal-zoom
vendor/timed-tabs
```

Rules:

- Reject IDs without a namespace.
- Reserve `skvn/*` for SKVN-owned packages.
- Do not allow extensions to overwrite a core ID.
- Detect duplicate third-party IDs and report the conflict.
- Store selected IDs unchanged so reinstalling an extension restores them.

## 5. Transition Registration

Conceptual metadata:

```text
id
label
provider
apiVersion
supportedPresets
defaultDuration
durationOptions
editorPreview
viewScriptHandle
styleHandle
fallback
reducedMotionFallback
capabilities
```

Conceptual frontend lifecycle:

```ts
registerTransition({
	id: 'vendor/portal-zoom',
	setup(context) {},
	destroy(context) {},
});
```

The bounded context may expose:

- Slider root
- existing Swiper instance
- normalized configuration
- real Slide collection
- transition direction and real indexes
- reduced-motion state
- event subscription/cleanup helpers

Do not expose unrestricted block editor mutation or transfer autoplay ownership
to an extension.

## 6. Pagination Registration

Conceptual metadata:

```text
id
label
provider
apiVersion
editorPreview
viewScriptHandle
styleHandle
fallback
mobileFallback
reducedMotionFallback
supportsText
supportsDescription
supportsProgress
recommendedMaximumSlides
```

Pagination may read Swiper state and autoplay progress but must not create a
second movement or autoplay timer.

Before implementation, decide whether extension markup uses:

1. a registered, escaped PHP renderer, or
2. one safe generic shell enhanced by extension JavaScript.

Prefer the generic shell when it preserves accessibility and no-JavaScript
fallback. Never store an arbitrary HTML callback in block attributes.

## 7. Saved Data

Slider attributes store only IDs and governed values:

```json
{
  "transitionStyle": "vendor/portal-zoom",
  "transitionDuration": 900,
  "paginationStyle": "vendor/timed-tabs"
}
```

Slide attributes may store plain pagination text:

```json
{
  "paginationLabel": "Preparing the land",
  "paginationDescription": ""
}
```

Do not store:

- JavaScript callbacks
- CSS source
- executable code
- runtime progress
- asset paths supplied through post content
- raw pagination HTML

## 8. Extension Packaging And Storage

Reusable extensions should be independent WordPress plugins:

```text
wp-content/plugins/vendor-slider-effects/
  vendor-slider-effects.php
  build/
    editor.js
    view.js
    style.css
```

A site-specific theme may register an extension from theme assets, but changing
the theme will make it unavailable. Plugin packaging is preferred for reusable
or product behavior.

Third-party extensions belong in the WordPress runtime, not in the SKVN source
repository. An SKVN-owned reference add-on should use a separately governed
package/repository or an explicitly approved monorepo package boundary.

## 9. Missing Extensions And Fallback

When saved content selects an unavailable extension:

1. Keep the saved ID unchanged.
2. Frontend uses its registered fallback, then the core fallback.
3. Editor marks the saved choice as unavailable.
4. Editor explains the active fallback.
5. Do not trigger invalid-block recovery.
6. Reinstalling the provider restores the old selection.

Core defaults:

```text
Missing transition -> skvn/fade
Missing pagination -> skvn/dots
```

Fallback resolution must detect invalid IDs and loops.

## 10. Reference Transition: Portal Zoom

Portal Zoom proves complex transition integration without taking over movement:

- outgoing media scales strongly toward the viewer and fades
- incoming Slide begins smaller behind it and scales to the stable frame
- content uses a restrained independent fade/translate treatment
- default duration is approximately `900ms`
- the governed `600–1000ms` range may be reused
- reduced motion falls back to `skvn/fade`
- transforms remain clipped by the Slider frame

Visual quality intentionally depends on designer-selected imagery and
composition. The API does not auto-design focal imagery.

The reference must prove registration, conditional assets, Swiper lifecycle
integration, per-instance cleanup, fallback, reduced motion, geometry, and
stable memory behavior.

## 11. Reference Pagination: Timed Tabs

Timed Tabs proves pagination text, progress, responsive fallback, and
accessibility:

```text
01
PREPARING THE LAND
------------------

02
GROWING & NURTURING
-------------------
```

Label fallback:

1. explicit Slide `paginationLabel`
2. first eligible Slide heading
3. translated `Slide 01`

Description is optional plain text.

Behavior:

- use real Slide order and exclude loop clones
- expose `aria-current="true"` on the active tab
- render real buttons with meaningful accessible labels
- read progress from Swiper autoplay
- do not create another autoplay timer/controller
- allow manual tab navigation
- declare a compact/mobile fallback
- remove animated progress under reduced motion
- preserve an understandable fallback without JavaScript where practical

## 12. Editor Experience

Group installed choices by provider:

```text
Core
- Fade
- Directional Wipe
- Zoom Out

Installed extensions
- Portal Zoom — SKVN Labs
- Timed Tabs — SKVN Labs
- Curtain — Vendor Name
```

Possible status badges:

```text
Core | Extension | Unavailable | Legacy | Incompatible
```

Editor rules:

- Show only choices compatible with the current Slider preset.
- Show pagination text fields only when supported.
- Do not run autoplay or full live transition effects in Gutenberg.
- Use a static or bounded lightweight preview.
- Never expose extension-provided raw CSS, classes, or JavaScript input.

## 13. Management

No separate admin page is required for the first API implementation.

If the ecosystem grows, a future read-only diagnostics page may live under:

```text
SKVN Marine -> Slider Extensions
```

It may report:

- registered IDs, labels, provider, and version
- API compatibility
- asset handles and missing assets
- duplicate IDs
- fallback and reduced-motion declarations
- active/inactive availability

It must not become a marketplace installer, raw code editor, or database for
extension source.

## 14. API Versioning And Governance

Define a Slider Extension API version independent from the plugin release:

```text
sliderExtensionApiVersion: 1
```

Requirements:

- document required and optional metadata
- validate registration and produce actionable developer notices
- provide a deprecation window before removing fields
- keep fallback stable across compatible API versions
- require setup/destroy cleanup and reduced-motion behavior
- require translated labels and accessible controls
- define support boundaries for third-party code

Core guarantees validation, fallback, and integration boundaries, not the
performance or design quality of arbitrary extensions.

## 15. Security And Performance

- Validate IDs, enums, versions, and known asset handles.
- Escape renderer output.
- Never evaluate code stored in post meta or block attributes.
- Load selected extension assets conditionally where WordPress timing permits.
- Avoid loading every installed extension on every page.
- Keep state and cleanup local to each Slider.
- Test repeated navigation, editor remounts, and fallback for leaked listeners,
  observers, or animation frames.

## 16. Delivery Phases

### Phase A — Contract

- Freeze PHP and JavaScript schemas.
- Define API version and names.
- Define runtime context, lifecycle, fallback, and diagnostics.

### Phase B — Core Registry

- Register core transitions/paginations through the internal contract.
- Add editor grouping and unavailable-choice reporting.
- Add validation and conditional asset loading.

### Phase C — Reference Add-ons

- Build Portal Zoom.
- Build Timed Tabs.
- Package them outside the lightweight core boundary.
- Test install, deactivate, remove, reinstall, and fallback behavior.

### Phase D — Documentation And QA

- Publish extension-author documentation and examples.
- Add API compatibility fixtures.
- Add diagnostics only if editor/runtime reporting is insufficient.
- Run onsite performance, accessibility, responsive, and memory QA.

## 17. Acceptance Draft

- [ ] Human assigns the specific 2.x.x milestone before implementation
- [ ] Transition and Pagination APIs are documented
- [ ] Core choices use the same registry model exposed to extensions
- [ ] Extensions use stable namespaced IDs
- [ ] Saved blocks store IDs/configuration, not executable code
- [ ] Missing extensions fallback without invalidating content
- [ ] Reinstalling an extension restores its saved selection
- [ ] Setup/destroy prevents leaked runtime state
- [ ] Reduced-motion fallback is mandatory
- [ ] Portal Zoom proves transition integration without another controller
- [ ] Timed Tabs proves text/progress pagination integration
- [ ] Pagination progress remains owned by Swiper autoplay state
- [ ] Theme and plugin extensions follow the same contract
- [ ] Extension assets are conditionally loaded where feasible
- [ ] Accessibility, no-JavaScript, and mobile fallback pass review
- [ ] Human approves the API and provider responsibility boundary

