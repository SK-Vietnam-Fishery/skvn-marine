# Slider Carousel Showcase UX Decision

Date: 2026-06-11
Status: approved UX direction; implementation not started
Target: Future Candidate after or alongside the V1 / 1.3.0 Slider dynamic rendering foundation

## Decision Source

Human reviewed the current Card Carousel editor and frontend output onsite.
The current stacked editor, duplicate background/content image controls, limited
card management, and unstable frontend card geometry do not match the intended
carousel workflow.

This decision supersedes the stacked-editor assumption for carousel/showcase
presets. Hero may keep a vertically readable editor surface where appropriate,
but carousel/showcase editing uses a governed grid.

## Block Boundary

Keep one block family and one movement runtime:

```text
skvn-marine/slider
└── skvn-marine/slide
```

- Reuse Swiper.
- Keep Gutenberg InnerBlocks and native List View.
- Do not create a second Slider engine.
- Do not replace Slide children with a custom `slides` array.
- Do not query WooCommerce automatically in this scope.
- Two opposite-direction marquee rows are composed by a pattern containing two
  Slider blocks, not one special two-row runtime.

## Editor Canvas

Carousel/showcase presets use a non-moving editor grid:

- Maximum layout: 5 columns by 2 rows.
- Maximum item count: 10 Slides.
- Reading order: left to right, then top to bottom.
- Fewer than five cards use only the columns needed.
- Narrow editor viewports reduce the column count responsively.
- Swiper, autoplay, and continuous motion do not run in the editor.
- Helper text explains that the editor uses a grid while the frontend renders a
  carousel.

Card management must be visible without relying only on List View:

- Add card.
- Duplicate card.
- Remove card.
- Move earlier/later.
- At 10 cards, Add is disabled, visually gray, and accompanied by:
  `Maximum 10 cards. Remove one to add another.`

## Media And Content

Carousel/showcase Slides use one content image. They do not expose the Hero
background-image control.

General-purpose card content:

- Image: required/always shown.
- Heading: required/always shown.
- Subheading: optional.
- Body text: optional.
- Meta: optional supporting information.
- Button: optional.

The naming stays general so the same card can represent products, services,
projects, posts, portfolio items, or case studies.

Mobile visibility is controlled at Slider level and applies consistently:

- Show subheading on mobile.
- Show body text on mobile.
- Show meta on mobile.
- Show button on mobile.

Image and Heading cannot be hidden.

## Card Layout

Desktop card layout has two governed choices with WordPress icons plus text:

- `Side by side` with a right-direction icon.
- `Stacked` with a down-direction icon.

Mobile is always Stacked. Individual Slides cannot override the parent layout.

## Motion Modes

Approved modes:

1. Custom Carousel
   - Editor may change autoplay, direction, speed, arrows, dots, loop, and other
     supported Swiper options.
2. Product Showcase Marquee
   - Continuous linear motion.
   - Loop on.
   - Pause on hover and keyboard focus.
   - Arrows and dots off.
   - Direction can support left or right as a preset-controlled value.
3. Centered Carousel
   - Snap one card at a time.
   - Active card is emphasized.
   - Previous and next cards may peek at the container edges.

When a motion preset controls a setting:

- Keep the dependent control visible.
- Apply the real HTML `disabled` state.
- Render it gray with reduced contrast.
- Show helper text explaining which preset controls it.
- Do not wait for a failed click before explaining the lock.
- A future `Customize motion` action may copy current values into Custom
  Carousel and unlock them.

All modes must respect `prefers-reduced-motion`. Continuous motion stops and
content remains manually scrollable/readable.

## Links

Each Slide uses the native Gutenberg LinkControl in its sidebar:

- Search Pages, Posts, Products, and other registered content.
- Allow an external URL.
- Do not build a custom autocomplete.

The Slider owns one shared setting:

```text
Open links in new tab
```

For showcase presets it may default to enabled. When enabled, rendered links
use `target="_blank"` and `rel="noopener noreferrer"`.

Interaction requirements:

- Hover pauses autoplay/continuous motion.
- Keyboard focus pauses motion.
- Enter activates the focused card link.
- Drag/swipe must not accidentally activate a link.
- Mobile tap opens the link; there is no hover-only interaction.

## Visual Presets

Per-Slide style selection uses a small approved set with rendered thumbnail
previews. Candidate families include Clean, Dark, and Glow.

Background choices use governed color/gradient presets such as White, Navy, and
Ocean Gradient.

Do not expose raw CSS, raw class input, arbitrary gradients, or a free-form
layout builder.

## Spacing

Do not copy Elementor's unrestricted pixel margin/padding system.

Use governed tokens:

```text
0 / XS / S / M / L / XL
```

Controls:

- Section spacing: Top and Bottom.
- Content inset: Left and Right, linkable.
- Card padding: Top, Right, Bottom, Left, linkable.
- Card gap: one shared token.
- Responsive tabs: Desktop, Tablet, Mobile.
- Smaller breakpoints inherit until explicitly overridden.

No per-Slide margin, negative margin, or arbitrary pixel input.

## Frontend Geometry

The frontend must reserve distinct ownership for:

- Carousel viewport.
- Slide/card width.
- Card internal layout.
- Arrow hit areas.
- Pagination/progress rail.

Arrows and pagination must not overlap card content. Equal card height, readable
text, stable CTA alignment, edge peeking, and clipping must be tested against
the real container width.

Dynamic frontend markup work must align with the V1 / 1.3.0 Slider rendering
architecture rather than adding another static save migration.

## Non-Goals

- WooCommerce automatic product query.
- Arbitrary per-card breakpoint layouts.
- Per-Slide margin controls.
- Raw CSS or unrestricted spacing values.
- Video support.
- A second carousel library or custom movement controller.
- A proprietary slide manager replacing Gutenberg blocks.

