# Slider Competitive Research — V1 / 1.2.0

Date: 2026-06-07
Status: research and recommendation; not automatically approved scope
Target: `skvn-marine/slider` and `skvn-marine/slide`

> **MVP decision update — 2026-06-07:** Human rejected the proposed custom/hybrid
> slide manager because it adds UX complexity. The active 1.2.1 direction is an
> add-and-see workflow using a dedicated `SKVN Marine` inserter category and three
> hardcoded Slider presets. See
> `docs/decisions/slider-presets-and-inserter-1.2.1.md`.

## 1. Research Question

What do mature WordPress slider products provide, what is missing from the current
SKVN implementation, and which features should SKVN adopt without turning the
Gutenberg block into a heavyweight visual-animation builder?

The phrase "Evolution Slider" is interpreted here as **Slider Revolution**.

## 2. Products Reviewed

Primary and official sources:

1. [Elementor Slides widget](https://elementor.com/help/slides-widget-pro/)
2. [Slider Revolution editor overview](https://www.sliderrevolution.com/documentation/editor-overview)
3. [Slider Revolution slides sidebar](https://www.sliderrevolution.com/help/slides-sidebar-tour/)
4. [Slider Revolution slide backgrounds](https://www.sliderrevolution.com/documentation/slide-background)
5. [Slider Revolution layer animations](https://www.sliderrevolution.com/documentation/layer-animations)
6. [Slider Revolution lazy-loading settings](https://www.sliderrevolution.com/documentation/global-settings)
7. [Smart Slider 3 layers](https://smartslider3.com/blog/what-is-a-layer-and-how-to-use-it-in-smart-slider-3/)
8. [Smart Slider 3 default versus absolute positioning](https://smartslider3.com/blog/what-is-default-and-absolute-positioning/)
9. [Smart Slider 3 autoplay](https://smartslider3.com/blog/slider-autoplay/)
10. [Smart Slider 3 slider practices](https://smartslider3.com/blog/good-and-bad-slider-practices/)
11. [Kadence Advanced Slider](https://www.kadencewp.com/kadence-blocks/pro/advanced-slider/)
12. [Kadence Advanced Gallery](https://www.kadencewp.com/help-center/docs/kadence-blocks/advanced-gallery-block/)
13. [Stackable Carousel block](https://docs.wpstackable.com/article/506-how-to-use-the-carousel-block)
14. [Swiper API](https://swiperjs.com/swiper-api)

## 3. Current SKVN Baseline

### Editor

- Parent Slider renders all Slide children in one stacked editor view.
- Each Slide supports:
  - WordPress Media Library background image.
  - Replace and remove image.
  - Overlay opacity.
  - InnerBlocks content with heading, paragraph, and button template.
- Slider inspector currently exposes:
  - autoplay
  - delay
  - loop
  - arrows
  - dots
  - slide/fade effect
  - one global `slidesPerView`

### Frontend

- Swiper owns touch, loop, keyboard, navigation, pagination, autoplay, and fade.
- Autoplay pauses on pointer hover.
- Autoplay is disabled for `prefers-reduced-motion`.
- Plugin ships Swiper and structural CSS.

### Architectural Strengths

- Uses native Gutenberg content rather than proprietary serialized layer data.
- Slide content remains searchable and editable as blocks.
- Swiper is modular and scoped to the Slider block.
- No dependency on GeneratePress markup.
- No arbitrary CSS, arbitrary animation timing, or raw class controls.
- Flow-based InnerBlocks are naturally safer for responsive layouts than free
  absolute positioning.

## 4. Feature Comparison

Legend:

- **Yes**: currently implemented.
- **Partial**: foundation exists, but editor or runtime is incomplete.
- **No**: not implemented.
- **Avoid**: mature products provide it, but it conflicts with SKVN's intended scope.

| Capability | SKVN | Elementor | Slider Revolution | Smart Slider 3 | Kadence / Stackable |
|---|---:|---:|---:|---:|---:|
| Add/remove slides | Partial | Yes | Yes | Yes | Yes |
| Reorder slides through a dedicated UI | No | Yes | Yes | Yes | Yes |
| Duplicate slide | No | Yes | Yes | Yes | Varies |
| Rename/admin-label slides | No | Partial | Yes | Yes | Varies |
| Selected-slide editing view | No | Yes | Yes | Yes | Partial |
| All slides stacked in editor | Yes | No | No | No | Varies |
| Background image from Media Library | Yes | Yes | Yes | Yes | Yes |
| Background color fallback | No | Yes | Yes | Yes | Yes |
| Image cover/contain/auto | No | Yes | Yes | Yes | Yes |
| Image focal point/position | No | Partial | Yes | Yes | Partial |
| Responsive image source/srcset | No | Platform-dependent | Yes | Yes | Yes |
| Background video | No | Limited/widget-dependent | Yes | Yes | Varies |
| Overlay opacity | Yes | Yes | Yes | Yes | Yes |
| Overlay color/blend mode | No | Yes | Yes | Yes | Varies |
| Heading/description/button | Yes | Yes | Yes | Yes | Yes |
| Arbitrary nested blocks | Yes | No | Layer system | Layer system | Yes |
| Whole-slide link | No | Yes | Yes | Yes | Varies |
| Dynamic content | No | Yes | Streams/dynamic | Dynamic | Pro variants |
| Content horizontal position | No | Yes | Yes | Yes | Yes |
| Content vertical position | No | Yes | Yes | Yes | Yes |
| Content width | No | Yes | Yes | Yes | Yes |
| Responsive per-device controls | No | Yes | Yes | Yes | Yes |
| Responsive height/aspect presets | No | Yes | Yes | Yes | Yes |
| Hide selected content by device | No | Yes | Yes | Yes | Yes |
| Drag/free absolute layers | Avoid | Limited | Yes | Yes | Limited |
| Timeline/keyframe editor | Avoid | No | Yes | Yes | No |
| Slide/fade transition | Yes | Yes | Yes | Yes | Yes |
| Transition speed control | No | Yes | Yes | Yes | Varies |
| Content entrance animation | No | Yes | Yes | Yes | Yes |
| Ken Burns background effect | No | Yes | Yes | Yes | Varies |
| Arrows and dots | Yes | Yes | Yes | Yes | Yes |
| Progress/numbers navigation | No | Limited | Yes | Yes | Varies |
| Thumbnail navigation | No | No | Yes | Yes | Kadence Gallery |
| Arrow style/position presets | No | Yes | Yes | Yes | Yes |
| Per-slide duration | No | No/limited | Yes | Yes | Varies |
| Pause on hover | Yes | Yes | Yes | Yes | Yes |
| Pause after interaction | Implicit Swiper default | Yes | Yes | Yes | Varies |
| Visible play/pause control | No | No | Yes | Yes | Varies |
| Pause on keyboard focus | No | Not confirmed | Configurable | Accessibility-oriented | Varies |
| Keyboard navigation | Yes | Yes | Yes | Yes | Yes |
| Screen-reader A11y module/messages | No | Platform/widget support | Yes | Yes | Varies |
| Reduced-motion handling | Partial | Platform/widget support | Configurable | Configurable | Varies |
| Native lazy loading | No | Platform-dependent | Yes | Yes | Yes |
| First-slide LCP strategy | No | Platform-dependent | Optimization tools | Optimization tools | Platform-dependent |
| Schedule a slide | Avoid | No | Yes | Pro/dynamic | No |
| Static overlay across all slides | Avoid for V1 | No | Yes | Yes | No |

## 5. What Mature Sliders Do Better

### 5.1 Slide Management Is a First-Class Workflow

Elementor provides a dedicated slide list with drag-and-drop reorder, duplicate,
delete, add, and per-slide settings. Slider Revolution uses a slide sidebar with
add, delete, sort, duplicate, preview, rename, and settings.

SKVN currently delegates structure management to nested Gutenberg blocks. This is
technically valid but weak for marketing editors:

- It is difficult to understand the slide order at a glance.
- A long stacked canvas becomes cumbersome after three or four slides.
- Duplicate and rename are hidden inside generic Gutenberg block operations.
- Selecting the correct child Slide to open its settings is not obvious.

### 5.2 Responsive Editing Is Explicit

Elementor, Slider Revolution, Smart Slider, and Kadence expose device-specific
editing or preview. Mature tools treat these as separate concerns:

- module/slide height
- image fit and focal position
- content width
- horizontal and vertical alignment
- spacing
- visibility
- slides per view

SKVN has one fixed `min-height`, one `slidesPerView`, and no focal point or
content-position controls.

### 5.3 Image Handling Goes Beyond Selecting a File

Common controls include:

- cover / contain / auto
- background position or focal point
- source image size
- color fallback
- poster image for video
- lazy loading
- responsive image variants

SKVN stores a direct attachment URL and renders a normal `<img>`, but does not
currently emit WordPress responsive image metadata (`srcset` and `sizes`) or
distinguish the first/LCP slide from later slides.

### 5.4 Autoplay Includes Visitor Control

Mature tools typically support some combination of:

- pause on hover
- pause on interaction
- pause when video plays
- visible play/pause control
- progress/time indicator
- per-slide duration

Smart Slider explicitly recommends giving visitors a way to stop and resume
rotation. SKVN pauses on hover but has no visible pause/play button and does not
pause when keyboard focus enters interactive slide content.

### 5.5 Accessibility Is More Than Keyboard Arrows

Swiper already provides an A11y module with:

- previous/next messages
- slide labels such as `2 / 5`
- container role descriptions
- pagination bullet labels
- live-region behavior that changes when autoplay is active

SKVN imports the Keyboard module but not Swiper's A11y module. Its arrow buttons
have no authored visible text and currently depend on visual styling without an
explicit SKVN accessibility contract.

### 5.6 Performance Is Designed Per Slide

Slider Revolution and Smart Slider expose lazy-loading strategies. Swiper relies
on native image lazy loading and can preload a limited number of adjacent slides.

SKVN currently has no policy for:

- first slide image: eager loading and possible high fetch priority
- later slide images: native lazy loading
- image dimensions to prevent layout shift
- responsive attachment sizes
- preload-next count
- avoiding autoplay initialization before media is ready

## 6. Important Lesson From Smart Slider

Smart Slider distinguishes normal flow/default positioning from absolute
positioning and recommends default positioning for most layouts. Absolute layers
provide visual freedom but create overlap and responsive maintenance problems.

This strongly supports the current SKVN architectural direction:

- Keep InnerBlocks and document flow.
- Do not build a Photoshop-like free-position layer canvas.
- Add governed alignment, width, and responsive presets around flow content.
- Use absolute positioning only for known decorative elements, never as the
  general content model.

## 7. Recommended SKVN Editor Direction

### Superseded Research Recommendation: Hybrid Slide Manager + Gutenberg Canvas

Do not keep every slide fully expanded forever, and do not run Swiper in the
editor.

Recommended editor structure:

```text
Slider toolbar / sidebar
┌──────────────────────────────────────────────────────────┐
│ Slides: [1 Hero] [2 Factory] [3 Products]  [+ Add]       │
│         drag reorder · duplicate · delete · rename       │
└──────────────────────────────────────────────────────────┘

Selected Slide canvas
┌──────────────────────────────────────────────────────────┐
│ Background image preview                                 │
│                                                         │
│ Heading                                                 │
│ Lead                                                    │
│ CTA                                                     │
└──────────────────────────────────────────────────────────┘

Inspector
Content · Background · Layout · Playback · Navigation
```

Why:

- Matches the slide-management clarity of Elementor and Slider Revolution.
- Keeps Gutenberg InnerBlocks for content editing.
- Avoids autoplay and carousel focus problems inside the editor.
- Scales better than a permanently expanded stacked view.
- Avoids a proprietary timeline/layer engine.

This recommendation is not approved for the MVP. The current stacked view remains,
with native Gutenberg List View/actions handling navigation and structure.

## 8. Recommended Scope

### Superseded P0 Proposal

#### Editor workflow

- Add a compact slide navigator/list.
- Select one Slide for primary canvas editing.
- Add explicit Add, Duplicate, Delete, and Move/Reorder actions.
- Show slide number and editable admin label.
- Enforce or clearly communicate the planned maximum of five hero slides.
- Preserve Gutenberg List View compatibility.

#### Background media

- Add image fit preset: Cover / Contain.
- Add focal point or governed position preset.
- Add background color fallback.
- Decide background-image semantics:
  - decorative image: empty alt and hidden from assistive technology
  - meaningful image: explicit alt contract
- Render WordPress attachment metadata instead of URL-only output where possible.

#### Layout

- Add slide height presets: Compact / Standard / Hero / Viewport.
- Add content horizontal alignment: Left / Center / Right.
- Add content vertical alignment: Top / Middle / Bottom.
- Add content width presets.
- Define mobile behavior for height, padding, and alignment.

#### Accessibility

- Import and enable Swiper A11y.
- Add translated previous/next and pagination labels.
- Add a carousel role/description.
- Pause autoplay while keyboard focus is inside the Slider.
- Add a visible Pause/Play control whenever autoplay is enabled.
- Confirm hidden slides do not expose confusing focus targets.
- Keep reduced-motion behavior.

#### Runtime correctness

- Force Fade to one slide per view.
- Define `disableOnInteraction` intentionally rather than relying on a library
  default.
- Add resize and content-height behavior policy.
- Prevent duplicate initialization.
- Handle a one-slide Slider without useless navigation/autoplay.

#### Performance

- First visible hero image: eager, dimensions present, optional high fetch priority.
- Later images: `loading="lazy"`.
- Use attachment-derived `srcset` and `sizes`.
- Set width/height or aspect ratio to prevent layout shift.
- Consider `lazyPreloadPrevNext: 1`.

### P1 — Valuable After P0

- Navigation style presets.
- Arrow position: Inside / Outside.
- Pagination style: Dots / Progress / Numbers.
- Transition speed-feel presets, not arbitrary milliseconds.
- Content entrance preset: None / Fade / Fade up.
- Per-slide duration preset.
- Whole-slide link option with safe semantics.
- Editor desktop/tablet/mobile preview.
- Undo-safe duplication and reorder polish.

### P2 — Only With a Real Site Use Case

- Background video with required poster image.
- Thumbnail navigation.
- Dynamic post/product slides.
- Product-highlight variant.
- Testimonial/logo variant.
- Static overlay shared across slides.
- Scheduling and conditional visibility.
- Per-layer timeline/keyframes.

## 9. Features SKVN Should Deliberately Avoid

### Free Absolute-Position Layer Canvas

Reason:

- Poor responsive behavior.
- High editor complexity.
- Creates proprietary layout data.
- Conflicts with Gutenberg's flow model.

### Unlimited Animation Timeline

Reason:

- Large implementation and QA surface.
- Hard to govern for marketing users.
- Easy to produce inaccessible or unstable output.

### Dozens of Transition Effects

Reason:

- Low business value.
- Increased CSS/JS and test matrix.
- Encourages decorative choices over content clarity.

Keep a small approved set such as Slide, Fade, and possibly one governed Zoom
variant.

### Arbitrary Raw Styling

Do not expose:

- raw CSS
- custom easing strings
- arbitrary z-index
- raw transform values
- unrestricted animation milliseconds
- external JavaScript

### Scheduling and External Streams in V1

These belong to campaign/dynamic-content systems, not the first reliable SKVN
hero Slider.

## 10. Current Technical Gaps Found in Source

1. Slider block description still calls itself a "skeleton".
2. There is no dedicated slide-management UI.
3. `slidesPerView` is not responsive.
4. Fade can currently be combined with multiple slides per view.
5. No A11y module is imported from Swiper.
6. No visible autoplay pause/play control exists.
7. Autoplay does not explicitly pause on keyboard focus.
8. Arrow buttons do not have an explicit SKVN label contract.
9. No background fit/focal-point controls exist.
10. No slide height/content-position controls exist.
11. Image output is URL-based without an explicit responsive-image policy.
12. All slide images currently share the same loading behavior.
13. No duplicate/rename/reorder workflow is surfaced.
14. No one-slide optimization exists.
15. No editor mobile/tablet preview contract exists.

## 11. Recommended Product Boundary

SKVN should aim to be:

> A governed, Gutenberg-native hero and content slider with excellent editing,
> responsive behavior, accessibility, and performance.

SKVN should not aim to be:

> A replacement for Slider Revolution's full visual composition, timeline,
> campaign, and multimedia engine.

The closest useful benchmark is a combination of:

- Elementor's simple slide-management workflow.
- Smart Slider's flow-first responsive philosophy.
- Kadence/Stackable's ability to retain block content.
- Swiper's modular runtime and accessibility APIs.

## 12. Proposed Next Decision

Human decision after review:

1. Keep stacked direct editing.
2. Do not build a custom slide manager for MVP.
3. Add one `SKVN Marine` inserter category.
4. Add Hero Slider, Product Showcase, and Card Carousel as immediate-use presets.
5. Keep one Slider/Slide runtime and flow-based InnerBlocks.
6. Move consolidated onsite verification to V1 / 1.2.9.
