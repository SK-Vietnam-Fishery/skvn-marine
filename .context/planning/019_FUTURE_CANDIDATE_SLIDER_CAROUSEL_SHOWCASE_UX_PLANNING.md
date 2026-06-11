# Future Candidate - Slider Carousel Showcase UX Planning

Status: FUTURE_CANDIDATE
Created: 2026-06-11
Version: unassigned
Dependency: V1 / 1.3.0 Slider Dynamic Rendering Architecture
Decision contract: `docs/decisions/slider-carousel-showcase-ux.md`

## 1. Goal

Turn the current Card Carousel/flow presets into a reusable governed showcase
system without creating a second Slider block family.

The implementation must improve both:

- Gutenberg editor management.
- Frontend carousel geometry and interaction.

## 2. Why This Is Not A Small Patch

The approved UX adds or changes:

- 5x2 editor grid.
- Visible card management with a 10-card cap.
- General-purpose card content fields.
- Card layout modes.
- Mobile visibility rules.
- Motion mode dependencies.
- Native link selection and card-level navigation.
- Visual preset previews.
- Responsive token spacing.
- Marquee and centered carousel frontend behavior.

Frontend HTML changes must use the dynamic rendering foundation planned for
1.3.0. Implementing all of this through another static `save.tsx` markup
migration would increase compatibility debt.

## 3. Recommended Delivery Phases

### Phase A - Architecture Reconciliation

Before feature code:

1. Complete or approve enough of the 1.3.0 dynamic render contract.
2. Inventory current Slider/Slide attributes and saved InnerBlocks variants.
3. Define which attributes belong to Slider versus Slide.
4. Define migration defaults for existing Hero, Product Showcase, and Card
   Carousel content.
5. Preserve the current block names and InnerBlocks.

Deliverable:

- Attribute/schema matrix.
- PHP render responsibility.
- Legacy compatibility test cases.

### Phase B - Editor Grid And Card Management

Implement carousel/showcase editor behavior:

- Grid preview up to 5 columns by 2 rows.
- Responsive reduction on narrow editor canvases.
- Helper text explaining editor grid versus frontend carousel.
- Add, duplicate, remove, and move earlier/later actions.
- Hard maximum of 10 Slides.
- Disabled gray Add control and visible limit message at 10.
- Keep native List View operations functional.

Do not run Swiper in the editor.

### Phase C - Content And Link Contract

Define a stable, general card structure:

- Image.
- Heading.
- Subheading.
- Body text.
- Meta.
- Button.

Add native Gutenberg LinkControl to each Slide sidebar.

Add Slider-level `openLinksInNewTab`, applied consistently to all linked cards.
Guard click versus drag/swipe in frontend runtime.

### Phase D - Layout And Visibility

Add Slider-level governed settings:

- Desktop layout: Side by side or Stacked.
- Mobile always Stacked.
- Show subheading on mobile.
- Show body text on mobile.
- Show meta on mobile.
- Show button on mobile.

Heading and image remain mandatory/visible.

### Phase E - Motion Modes

Add:

- Custom Carousel.
- Product Showcase Marquee.
- Centered Carousel.

Create one dependency map for controlled settings. Disabled controls remain
visible, gray, semantically disabled, and accompanied by helper text.

Reuse Swiper. Continuous marquee must use a linear, loop-safe configuration
without creating an independent timer/controller.

Pause behavior:

- Pointer hover.
- Keyboard focus within.
- Resume on leave/blur when reduced motion is not active.

### Phase F - Visual Presets And Spacing

Add a small set of hard-coded card style presets with image/thumbnail previews.
Add approved background color/gradient packages.

Add token-based responsive spacing:

- Section top/bottom.
- Content left/right inset with sync.
- Card padding four sides with sync.
- Card gap.

No raw pixels, raw CSS, arbitrary gradients, negative margins, or per-Slide
margin.

### Phase G - Pattern And QA

Add a two-row marquee pattern only after the single Slider marquee mode is
stable:

- Row 1 moves left.
- Row 2 moves right.
- Both pause independently on hover/focus.
- Both stop under reduced motion.

Update onsite tests for:

- Existing Slider migration.
- Editor 5x2 order and 10-card limit.
- Link search, new-tab behavior, keyboard use, and drag protection.
- Side-by-side and stacked desktop modes.
- Forced stacked mobile mode and visibility toggles.
- Custom, marquee, and centered movement.
- Arrow/pagination ownership.
- Equal heights and CTA alignment.
- No horizontal page overflow.

## 4. Attribute Ownership Draft

Slider candidates:

```text
motionMode
cardLayout
showSubheadingMobile
showBodyMobile
showMetaMobile
showButtonMobile
openLinksInNewTab
autoplay
direction
speedPreset
pauseOnInteraction
arrows
dots
loop
sectionSpacing
contentInset
cardPadding
cardGap
```

Slide candidates:

```text
linkUrl
linkLabel
cardStyle
backgroundPreset
```

The final schema must be reconciled with existing InnerBlocks. Do not duplicate
content into attributes if native editable blocks remain the source of truth.

## 5. Likely Source Areas

Expected to exceed five files, so implementation should be split by phase:

```text
wp-content/plugins/skvn-marine-blocks/src/slider/block.json
wp-content/plugins/skvn-marine-blocks/src/slider/edit.tsx
wp-content/plugins/skvn-marine-blocks/src/slider/variations.ts
wp-content/plugins/skvn-marine-blocks/src/slider/view.ts
wp-content/plugins/skvn-marine-blocks/src/slider/style.css
wp-content/plugins/skvn-marine-blocks/src/slide/block.json
wp-content/plugins/skvn-marine-blocks/src/slide/edit.tsx
wp-content/plugins/skvn-marine-blocks/src/slide/types.ts
wp-content/plugins/skvn-marine-blocks/modules/slider-render/
```

Theme files should only be touched if approved visual tokens/classes are
missing. Plugin-owned block behavior and fallback styling must remain portable.

## 6. Compatibility Rules

- Do not rename `skvn-marine/slider` or `skvn-marine/slide`.
- Do not invalidate existing posts.
- Do not remove InnerBlocks or native List View.
- Existing background-image data must remain compatible with Hero.
- Carousel/showcase presets must not expose or render an accidental second
  background image.
- Existing three presets must receive explicit migration/default behavior.
- Generated build files must be rebuilt after source changes.

## 7. Acceptance Draft

- [ ] Architecture/schema matrix approved before broad implementation.
- [ ] Carousel editor uses a maximum 5x2 grid and does not run Swiper.
- [ ] Add/duplicate/remove/reorder actions are visible.
- [ ] The 11th card is blocked with a disabled gray Add control and message.
- [ ] Image and Heading remain visible on every card.
- [ ] Optional content uses general labels: Subheading, Body, Meta, Button.
- [ ] Desktop supports Side by side and Stacked; mobile is always Stacked.
- [ ] Mobile visibility toggles apply consistently across Slides.
- [ ] Native LinkControl searches WordPress content.
- [ ] Parent new-tab setting applies safely to all card links.
- [ ] Drag/swipe does not trigger accidental navigation.
- [ ] Custom, Marquee, and Centered modes reuse one Swiper runtime.
- [ ] Preset-controlled settings are visibly gray, disabled, and explained.
- [ ] Hover and keyboard focus pause applicable motion.
- [ ] Reduced motion stops continuous movement and leaves content usable.
- [ ] Visual presets use governed previews and no raw CSS.
- [ ] Responsive spacing uses tokens and inheritance.
- [ ] Arrows, pagination, cards, and footer do not overlap.
- [ ] Existing Slider content opens without invalid-block recovery.
- [ ] Plugin build, PHP lint, deploy artifact audit, and onsite QA pass.

## 8. Out Of Scope

- Automatic WooCommerce product data/query.
- Video.
- Arbitrary layout builder controls.
- Raw color/gradient/CSS input.
- Unlimited Slides.
- A custom slide database/repeater.
- A second movement runtime.

