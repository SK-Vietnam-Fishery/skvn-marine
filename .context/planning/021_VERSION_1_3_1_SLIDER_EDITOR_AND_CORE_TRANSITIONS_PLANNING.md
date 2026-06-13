# V1 / 1.3.1 — Slider Editor And Core Transitions Planning

Status: APPROVED DIRECTION
Created: 2026-06-12
Human direction: finish the current lightweight Slider editor and core
transition surface before building the future extension ecosystem.

## 1. Goal

Expand the current V1 / 1.3.1 Slider scope with:

- an editor-only Add Slide action centered in the static controls preview bar
- governed and hidden `slidesPerView` controls where the preset owns layout
- three lightweight transitions: Directional Wipe, Fade, and Zoom Out
- governed transition duration and reduced-motion fallback

Portal Zoom, Timed Tabs, and the public Transition/Pagination Extension API are
excluded. They belong to the separate Future Candidate / 2.x.x planning.

## 2. Editor Add Slide Action

The static editor controls bar becomes:

```text
[ arrows preview ]      [ + Add slide ]      [ pagination preview ]
```

Behavior:

- Use a visible WordPress editor button with an add icon and translated label.
- Insert one `skvn-marine/slide` at the end of the current Slider.
- Select/focus the new Slide when Gutenberg APIs permit this reliably.
- Respect the existing five-Slide maximum for Hero and Product Showcase.
- Disable or hide the action at the maximum and explain why.
- Keep native List View insertion available.
- Remove the small corner appender only after the centered action fully replaces
  its insertion behavior.
- Do not save or render this editor action on the frontend.
- Do not initialize Swiper or live motion in Gutenberg.

## 3. Slides Per View

`slidesPerView` is the number of real Slides visible at once.

```text
Hero Slider       -> fixed at 1; hide the control
Product Showcase  -> fixed at 1; hide the control
Card Carousel     -> fixed responsive 3/2/1; hide the generic control
Unclassified/old  -> retain the compatible 1–4 control
```

Requirements:

- Normalize Hero and Product Showcase frontend configuration to one Slide.
- Preserve Card Carousel's existing `responsiveSlides: 3-2-1`.
- Do not silently rewrite ignored legacy attributes.
- Existing unclassified Slider content remains editable.
- The three core transitions are one-Slide-at-a-time effects. Do not expose
  them on an incompatible multi-view configuration without normalization.

## 4. Time Controls

Keep two distinct settings.

Autoplay duration controls how long a Slide remains active:

```text
5s | 7s | 9s | 12s
```

Default remains `7s`. Existing legacy-value handling remains governed by the
current V1 / 1.3.1 decision.

Transition duration controls the movement between Slides:

```text
600ms | 700ms | 800ms | 900ms | 1000ms
```

Proposed attribute and default:

```text
transitionDuration: 800
```

Use only the approved values. Normalize missing or malformed values to `800`.
Map the value to Swiper speed and any coordinated CSS custom property. Do not
create a second timer.

## 5. Core Transitions

### Directional Wipe

- Next navigation wipes media and moves content toward the left.
- Previous navigation wipes media and moves content toward the right.
- Direction comes from Swiper lifecycle/navigation state.
- Arrows, pagination, autoplay, keyboard, and swipe use the same movement
  controller.

### Fade

- Cross-fade outgoing and incoming Slides.
- Keep media, overlay, and content layers stable.
- Use Swiper fade support where it satisfies the contract.
- This is the compatibility and reduced-motion fallback.

### Zoom Out

- Use a restrained conventional zoom, not Portal Zoom.
- Incoming media settles approximately from `scale(1.08)` to `scale(1)`.
- Content uses a light fade/translate treatment, not the full media scale.
- Keep transforms clipped to the Slider frame without changing page geometry.
- Focal-point authoring is not part of this milestone.

## 6. Attribute And Compatibility Direction

Proposed active attributes:

```text
transitionStyle: directional-wipe | fade | zoom-out
transitionDuration: 600 | 700 | 800 | 900 | 1000
```

Before implementation, reconcile the existing `effect` attribute:

- `effect: fade` maps to `transitionStyle: fade`.
- `effect: slide` maps to Directional Wipe only if human accepts that as
  compatible; otherwise retain a legacy slide path until resave.
- Do not keep two permanent editor controls for the same concept.
- Existing content must open without invalid-block recovery.
- Dynamic PHP rendering must not require a bulk resave.

Record the final compatibility path in the active Slider decision before source
implementation.

## 7. Reduced Motion

Under `prefers-reduced-motion: reduce`:

- disable autoplay
- disable Directional Wipe translation and Zoom Out scaling
- render those choices through the Fade fallback
- keep arrows, pagination, keyboard, and manual navigation available
- disable live timed-pagination animation per the current 1.3.1 contract

The editor remains static for every motion preference.

## 8. Runtime Ownership

PHP renderer owns normalized transition configuration and legacy mapping.

Swiper owns real index, direction, swipe, arrows, pagination, keyboard, loop,
autoplay, lifecycle, and speed.

Plugin CSS owns wipe masks/transforms, Zoom Out media transforms, restrained
content motion, and reduced-motion fallback.

Do not introduce:

- another Swiper or autoplay controller
- `setInterval` for movement/progress
- frontend animation updates through block attributes
- mutable transition state shared between Slider instances
- required theme JavaScript for plugin-owned effects

## 9. CSS And Geometry Safety

- Slider root remains the positioning owner.
- Media clipping stays local to the component.
- Do not add `100vw`, negative viewport margins, or page-level
  `overflow-x: hidden/clip`.
- Verify transforms do not enlarge the layout bounding box.
- Keep Add Slide and control previews readable at desktop, tablet, and mobile
  editor widths.

## 10. Expected Implementation Surface

Likely files:

```text
wp-content/plugins/skvn-marine-blocks/src/slider/block.json
wp-content/plugins/skvn-marine-blocks/src/slider/edit.tsx
wp-content/plugins/skvn-marine-blocks/src/slider/view.ts
wp-content/plugins/skvn-marine-blocks/src/slider/style.css
wp-content/plugins/skvn-marine-blocks/modules/slider-render/slider-render.php
```

Compatibility fixtures/tests may require one focused test file. Split source
implementation into smaller tasks if the change would exceed the normal file
limit.

## 11. Testing

Editor:

- Add a Slide from the centered button.
- Confirm preset maximums remain enforced.
- Confirm Hero/Product Showcase hide Slides Per View and stay at one.
- Confirm Card Carousel hides the generic control and remains 3/2/1.
- Confirm unclassified legacy Sliders retain compatible controls.
- Confirm transition duration exposes only approved values.
- Confirm no live Slider transition runs in Gutenberg.

Frontend:

- Test all transitions through autoplay, arrows, pagination, keyboard, and
  touch swipe.
- Test all approved durations.
- Verify both Directional Wipe directions.
- Verify Zoom Out stays inside the Slider frame.
- Verify reduced motion disables autoplay and falls back to Fade.
- Verify multiple Sliders own independent state and cleanup.
- Verify zero/one real Slide still disables controls and autoplay.

Compatibility:

- Open legacy `effect: slide` and `effect: fade` fixtures without recovery.
- Render old content through PHP without bulk resave.
- Confirm Card Carousel responsive behavior is unchanged.

## 12. Acceptance Draft

- [ ] Centered Add Slide action works and is editor-only
- [ ] Existing maximum-Slide rules remain enforced
- [ ] Hero and Product Showcase are fixed to one Slide per view
- [ ] Generic Slides Per View is hidden for governed presets
- [ ] Card Carousel remains responsive 3/2/1
- [ ] Directional Wipe, Fade, and Zoom Out are implemented
- [ ] Transition duration is limited to `600/700/800/900/1000ms`
- [ ] Autoplay duration remains a separate governed control
- [ ] Reduced motion disables autoplay and falls back to Fade
- [ ] Swiper remains the only movement/autoplay controller
- [ ] Existing Slider content remains valid and editable
- [ ] No horizontal overflow or control overlap is introduced
- [ ] Build, PHP lint, compatibility tests, and onsite QA pass
- [ ] Human approves the expanded V1 / 1.3.1 scope

