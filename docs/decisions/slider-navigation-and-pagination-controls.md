# Slider Navigation And Pagination Controls

Status: APPROVED UX DIRECTION
Target: V1 / 1.3.1 — Slider Navigation & Pagination Controls UX
Decision date: 2026-06-11

## 1. Goal

Define a governed Slider control system for arrows, pagination, autoplay timing,
placement, responsive fallback, and accessibility without creating another
movement or timer controller beside Swiper.

This decision extends the dynamic Slider foundation from
`docs/decisions/slider-completion-spec-1.3.0.md`. It is not part of the active
V1 / 1.3.0 implementation scope. The failed 1.3.0 media, width, pagination
geometry, and memory behavior must be corrected before adding live timed
pagination.

## 2. Attribute Direction

Replace the old dots-specific concept with pagination terminology:

```json
{
  "autoplay": true,
  "autoplayDelay": 7000,
  "showArrows": true,
  "arrowStyle": "circle",
  "arrowPosition": "bottom-left",
  "showPagination": true,
  "paginationStyle": "timed-fraction",
  "paginationPosition": "bottom-left"
}
```

Do not keep `dots` as a permanent parallel attribute. Before implementation,
define one explicit Gutenberg migration/deprecation path so existing Slider
content remains editable and renders with its previous pagination state.

When the Slider contains zero or one real Slide:

- Do not initialize autoplay.
- Do not render or display arrows.
- Do not render or display pagination.

## 3. Arrow Controls

Approved arrow styles:

- `minimal`: chevrons without a surrounding surface.
- `circle`: separate circular controls.
- `pill`: previous and next controls grouped in one capsule.

Approved arrow positions:

- `side-center`
- `bottom-left`
- `bottom-center`
- `bottom-right`

`pill` is incompatible with `side-center`. The editor must disable the Pill
option while Side center is selected and explain why. Do not silently replace
the user's chosen style.

## 4. Pagination Controls

Approved pagination styles:

- `dots`
- `fraction`
- `timed-fraction`
- `timed-segments`

Approved pagination positions:

- `bottom-left`
- `bottom-center`
- `bottom-right`

`timed-fraction` uses this semantic order:

```text
01  [progress timer]  05
```

The first number is the current real Slide and the last number is the total
number of real Slides. The timer fills from left to right.

`timed-segments` is a separate pagination style. Do not show timed fraction in
one location and timed segments in another location at the same time.

## 5. Shared Position Clustering

Arrow and pagination attributes remain independent. Do not add a controls
preset attribute.

When both controls use the same bottom position, the renderer adds one
conditional cluster modifier:

```text
bottom-left
bottom-center
bottom-right
```

The default cluster order is always:

```text
arrows | pagination
```

### 5.1 Bottom-center flank exception (V1 / 1.3.6)

When **all** of the following are true:

- arrows and pagination are both visible,
- `arrowPosition` and `paginationPosition` are both `bottom-center`,
- `arrowStyle` is not `pill`,

the renderer uses an additional flank modifier instead of the default
grouped-arrow cluster:

```text
previous arrow | pagination | next arrow
```

Pagination **style** (`dots`, `fraction`, `timed-fraction`, `timed-segments`)
does not change flank eligibility. Pagination **position** must still be
`bottom-center`.

`pill` always keeps the default cluster order from §5 because both controls
live inside one capsule surface.

Planning: `.context/planning/archives/031_VER_1_3_6_SLIDER_BOTTOM_CENTER_FLANK_CONTROLS_PLANNING.md`

When the positions differ, arrows and pagination retain their independent
positions. The implementation may use one stable controls wrapper and
conditional layout classes, but must not duplicate Slider movement state or
create separate navigation markup models for each combination.

The implementation must verify that any `display: contents` approach preserves
the intended accessibility tree. Fixed left, center, and right control zones
remain an acceptable fallback if that verification fails.

## 6. Autoplay Delay

One delay applies to the entire Slider. Do not add per-Slide duration.

Approved editor choices:

```text
5s | 7s | 9s | 12s
```

- Default: `7s`.
- `5s` is the minimum governed duration.
- Use the shared magnetic time control with equal-spaced indexes mapped to the
  Slider-owned `5s`, `7s`, `9s`, and `12s` values.
- Hide arbitrary numeric input and expose only human-readable duration marks.
- Do not expose arbitrary duration input.
- Slider owns its allowed values and `7s` default. Missing, malformed, or
  disallowed values normalize to `7s` in both the editor and frontend.
- Legacy Slider duration preservation is not part of this control contract.

The sidebar duration selector and the frontend progress timer are different
controls:

- Sidebar: chooses Slider duration.
- Frontend timer: displays progress through the active Slide duration.

The shared magnetic control accepts an ordered TypeScript configuration object
whose entries contain a numeric millisecond value and a human-readable label,
plus one default value that must exist in the entries. The shared layer owns
rendering, index/value mapping, keyboard stepping, accessible value text, and
normalization. Slider owns the configuration itself.

Other SKVN blocks may reuse the same control without inheriting Slider's
allowed values. Dropdowns remain appropriate for semantic choices; ordered
governed durations use the magnetic time control.

## 7. Swiper And Timer Ownership

Swiper remains the only Slider movement and autoplay controller.

Do not add:

- `setInterval` beside Swiper.
- A React state loop for frontend progress.
- `setAttributes()` calls driven by runtime timer progress.
- A second autoplay controller.

Timed pagination reads Swiper autoplay state and updates only the local Slider
DOM or CSS custom property. Current numbering uses `realIndex + 1`, not
`activeIndex`. Total count must exclude loop clones.

Manual arrow, pagination, or swipe navigation resets progress for the newly
active Slide. Activating the already-current pagination item does not reset it.

When loop is off, autoplay and progress stop at the final Slide. Returning to
an earlier Slide may resume autoplay according to the normal pause policy.

## 8. Pause And Resume Policy

Pause autoplay and timer progress for:

- Pointer hover.
- Keyboard focus within the Slider.
- Hidden document/tab.
- Active drag or swipe interaction.

Resume only when no pause reason remains. Use a Slider-local set of pause
reasons or an equivalent state coordinator so leaving hover cannot resume while
keyboard focus or document visibility still requires a pause.

Use Swiper pause/resume behavior for temporary pauses. Do not use permanent
stop behavior for hover or focus.

On manual navigation:

- Complete the requested navigation.
- Clear the interaction pause reason.
- Reset progress for the new Slide.
- Resume only if no other pause reason remains.

Each Slider instance owns its own pause state, progress value, listeners, and
cleanup. Multiple Sliders on one page must not share mutable runtime state.

## 9. Responsive And Reduced-Motion Behavior

Desktop and wider layouts retain the selected pagination style.

On narrow mobile layouts:

- Shorten the `timed-fraction` progress rail while it remains readable.
- If the available width is insufficient, fall back to static `fraction`.
- `timed-segments` falls back to static segments that indicate Slide position.
- Controls must remain on one readable line and must not overlap Slide content.

Under `prefers-reduced-motion`:

- Disable autoplay.
- Disable timer animation.
- Keep manual navigation available.
- Render timed pagination as its static fallback.

The Gutenberg editor does not run Swiper autoplay or a live timer. It shows a
static preview of the selected style.

## 10. Accessibility

No dedicated Pause/Play control is required for this scope.

Required behavior:

- Previous and next buttons have translated, meaningful labels.
- Pagination buttons are named, for example `Go to slide 2`.
- The current pagination item exposes `aria-current="true"`.
- Timer visuals are hidden from assistive technology and do not announce every
  progress update.
- Autoplay pauses while keyboard focus remains anywhere inside the Slider.
- Autoplay is disabled for reduced motion.
- Automatic Slide changes do not force repetitive screen-reader announcements.
- The Slider has a meaningful accessible label, not only a generic
  `Carousel`.

The governed minimum delay is five seconds. Content authors remain responsible
for choosing a duration appropriate to the amount of copy and CTA content.

## 11. Geometry And Layering

The Slider root owns positioning for arrows and pagination.

- Controls remain inside the Slider frame.
- Pagination must not be pushed into normal flow below the Slide.
- Controls must not overlap content, the page footer, or each other.
- Full-width behavior uses the existing SKVN canvas/content-width ownership.
- Do not introduce `100vw`, negative viewport margins, or `overflow-x` masking
  as a control-placement fix.

Before implementation, follow
`docs/standards/css-layout-safety-contract.md` and prove affected geometry with
desktop scrollbar, tablet, and mobile tests.

## 12. Implementation Gates

Do not implement this decision until:

- V1 / 1.3.0 dynamic rendering is stable enough to provide one Slider shell.
- Media layering, full-width geometry, existing pagination placement, and
  reported idle RAM growth are corrected or bounded.
- The `dots` to `showPagination` migration/deprecation path is documented.
- The shared magnetic-time configuration and default normalization rule are
  implemented.

Final onsite acceptance belongs to V1 / 1.3.2.

## 13. Transition Compatibility Addendum

The approved V1 / 1.3.1 editor and transition expansion uses this compatibility
path:

- Legacy `effect: fade` maps to `transitionStyle: fade`.
- Legacy `effect: slide` maps to `directional-wipe` only when the Slider shows
  one real Slide per view.
- Card Carousel and unclassified legacy Sliders configured above one Slide per
  view retain Swiper's standard `slide` movement at runtime.
- Directional Wipe, Fade, and Zoom Out are not exposed while an unclassified
  Slider uses a multi-view configuration.
- Ignored transition attributes remain stored and become available again if
  the Slider returns to one Slide per view.
- Dynamic PHP rendering applies this mapping without requiring a bulk resave.

Implementation acceptance must include:

- Zero/one Slide hides both control families and disables autoplay.
- All approved arrow styles and positions render without overlap.
- Pill is disabled for Side center in the editor.
- All pagination styles and positions render correctly.
- Matching bottom positions cluster in the fixed order.
- Different positions remain independent.
- Timed pagination follows Swiper without a second timer controller.
- Loop numbering uses real Slides and does not count clones.
- Hover, focus, visibility, and interaction pause reasons compose correctly.
- Mobile and reduced-motion fallbacks work.
- Multiple Sliders initialize and clean up independently without increasing
  memory use over repeated navigation or editor updates.
- Existing Slider content remains editable without invalid-block recovery.
