# Block Animation Strategy

Date: 2026-06-04
Status: active for V1 / 1.2.0 — promoted 2026-06-07

## Summary

SKVN Marine builds its own animation system when animation scope opens, split between portable block-level behavior in `skvn-marine-blocks` and page/theme-level behavior in `skvn-marine`.

External plugin route is closed for the current shared-host runtime because the reviewed candidates are not acceptable under the site's PHP 8.0 constraint. The host has declined to upgrade. Do not use an external Gutenberg animation plugin unless PHP compatibility is verified and the human explicitly approves.

Animation implementation is active in V1 / 1.2.0. This document defines the architecture boundary for that milestone.

---

## External Plugin Route — Closed For Current Runtime

The shared host runs PHP 8.0. The best available candidate reviewed during the brainstorm, `Animations for Blocks`, requires PHP 8.1. Host has been asked and has declined to upgrade.

**Decision: external plugin route is closed for the current hosting environment. Do not revisit unless hosting/runtime changes or a specific candidate is verified against PHP 8.0 and approved by the human.**

Reference plugins (for future reference only, not actionable):

- Blocks Animation / Otter: https://wordpress.org/plugins/blocks-animation/
- Animations for Blocks: https://wordpress.org/plugins/animations-for-blocks/
- Animated Blocks on Scroll: https://wordpress.org/plugins/animated-blocks/
- Simple Block Animations: https://wordpress.org/plugins/simple-block-animations/
- Greenshift: https://wordpress.org/plugins/greenshift-animation-and-page-builder-blocks/

---

## Ownership Model

### Mandatory portability rule

`skvn-marine-blocks` must remain portable. Future scope may rename or migrate this plugin toward `Gutenberg Supercharger`, so any animation feature exposed by plugin-owned blocks must run without the `skvn-marine` theme.

If an animation belongs to any custom block, the plugin must ship everything required for that behavior:

- block attributes and editor controls
- frontend JS runtime
- CSS classes, keyframes, transitions, and reduced-motion fallback
- device targeting behavior
- no-JS visible fallback

The theme may skin or override plugin motion with tokens later, but theme CSS/JS must not be required for plugin block animation to work.

### Boundary rule

```
If behavior is tied to a specific custom block → view.ts of that block.
If behavior is tied to page sections built with core blocks → animations.js of theme.
```

This boundary keeps block-level behavior portable while still allowing the SKVN theme to own page-level animation for core blocks and non-block decoration.

### Custom block animation (`view.ts`)

- Entrance animation for slider first slide.
- Stagger for items inside a block (KPI strip, stat counter, card grid).
- Counter/stat number animation logic.
- Accordion expand/collapse animation.
- Pause ambient animation when the ambient element belongs to a custom block.
- Plugin-owned animation CSS/keyframes required by these behaviors.

### Theme animation (`animations.js`)

- Scroll reveal for core blocks and sections (groups, columns, headings).
- Ambient/loop animation for theme decorative elements.
- Global IntersectionObserver setup for non-block content.

`animations.js` does not exist yet. It will be created when there is a real use case after 1.0.0.

### Blocking external plugins from custom blocks

If any external animation plugin is ever used in the future (after hosting changes), SKVN custom blocks must be excluded to prevent conflict with their own animation runtime:

```php
// inc/setup.php or functions.php
add_filter( 'anfb_unsupported_blocks', function( $blocks ) {
    $blocks[] = 'skvn-marine/slider';
    $blocks[] = 'skvn-marine/accordion';
    return $blocks;
} );
```

Reason: external plugins use `render_block` filter to inject `data-aos` or similar attributes into the outer element. For slider, this outer element is the Swiper wrapper — if AOS sets `opacity: 0` on it, Swiper initializes invisibly and causes layout shift or flash. Block early, not after the fact.

---

## Animation Types

Two distinct types. Do not mix implementation approach.

### Entrance animation (scroll reveal)

Triggered once when element enters viewport. Uses IntersectionObserver in JS.

Frontend implementation may start with a hidden pre-animation state only when there is a visible no-JS fallback. Editor content must remain visible and editable; do not set editor output to `opacity: 0`.

Examples: section fade up, KPI items stagger in, stat counter start.

Recommended fallback contract:

```css
.skvn-motion-reveal {
  opacity: 1;
  transform: none;
}

.js .skvn-motion-reveal {
  opacity: 0;
  transform: translateY(16px);
}

.js .skvn-motion-reveal.is-visible {
  opacity: 1;
  transform: none;
}

@media (prefers-reduced-motion: reduce) {
  .js .skvn-motion-reveal {
    opacity: 1;
    transform: none;
  }
}
```

The `.js` class must be added early by the owner runtime before applying hidden animation states. If JS fails, content remains visible.

### Ambient animation (loop)

Runs continuously regardless of scroll. Uses CSS `animation: infinite`. No JS trigger needed unless pausing out of viewport for battery saving.

Examples: `wave-float`, `slow-drift`, `marine-hover-lift`.

Pause out of viewport: use IntersectionObserver to add/remove a class that plays/pauses the CSS animation. This saves battery on mobile. Do not skip this for ambient animations.

If the ambient animation belongs to a plugin block, the plugin owns the observer and `.skvn-paused` CSS. If the ambient animation is theme decoration, the theme owns them.

```javascript
const observer = new IntersectionObserver(
  ([entry]) => {
    el.classList.toggle('skvn-paused', !entry.isIntersecting);
  },
  { threshold: 0, rootMargin: '0px' }
);
```

```css
.skvn-paused {
  animation-play-state: paused;
}
```

---

## IntersectionObserver Trigger Points

Implemented in JS. Not using AOS or any external library.

| Use case | threshold | rootMargin bottom | Notes |
|---|---|---|---|
| Scroll reveal / entrance | 0.1 | -80px | Element must be 10% visible, 80px from bottom edge |
| Counter / stat animation | 0.5 | 0px | Counter starts when element is 50% visible |
| Ambient pause/resume | 0 | 0px | 1px in viewport = resume, fully out = pause |

The `-80px` rootMargin for entrance animation may need adjustment for short sections on small screens. Fine-tune after testing on real device.

---

## Accordion Animation

Use JS `scrollHeight` for accurate height animation. Do not use CSS `max-height` transition.

Reason: `max-height` set larger than actual content height causes easing to feel wrong — the visible portion of the animation happens in the first 20% of the transition, making `ease-out` look like `linear`.

```javascript
// Open
el.style.height = el.scrollHeight + 'px';
el.addEventListener('transitionend', () => {
  el.style.height = 'auto';
}, { once: true });

// Close — must set px before animating to 0
el.style.height = el.scrollHeight + 'px';
requestAnimationFrame(() => {
  el.style.height = '0';
});
```

CSS:

```css
.skvn-accordion__body {
  height: 0;
  overflow: hidden;
  transition: height 0.3s ease-out;
}
```

Implementation lives in `src/accordion/view.ts`.

Implemented in 1.2.0 as progressive enhancement. Saved Accordion content remains
visible without JavaScript; the frontend runtime adds the button, ARIA state,
collapsed state, real-height transition, and Arrow/Home/End navigation.

---

## Slider Entrance Animation

Slider block (`skvn-marine/slider`) does not use an outer entrance animation wrapper. Swiper owns all timing.

The first slide fades in after Swiper initializes:

```javascript
const swiper = new Swiper(el, {
  // ...existing config
  on: {
    init: function () {
      this.slides[0].classList.add('skvn-slide--entered');
    }
  }
});
```

```css
.skvn-slide {
  opacity: 0;
  transition: opacity 0.6s ease-out;
}
.skvn-slide--entered {
  opacity: 1;
}
```

Hero sections do not get entrance animation by default because it can delay first-content perception, create visual instability, and make the first viewport feel less direct. Exceptions require explicit design approval.

---

## Stat Counter Block

This is a dedicated custom block: `skvn-marine/stat-counter` (or a variant of KPI strip).

Counter logic requires JS state and cannot be done with CSS or external animation plugins. This is the clearest justification for a custom block in the animation scope.

### Sidebar controls (non-dev friendly)

```
── Counter ──────────────────────────────
Target number      [ 1000       ]
Duration           [ 2.0s ▼    ]   (0.5 / 1.0 / 1.5 / 2.0 / 3.0)
Easing             [ Ease out ▼ ]   (Linear / Ease out / Ease out fast)
Suffix             [ +          ]
Prefix             [            ]

── Label ────────────────────────────────
Text               [ Khách hàng đã tin tưởng ]
Appear             [ After counter ▼ ]   (With counter / After counter)

── Trigger ──────────────────────────────
Start when         [ Scrolls into view ▼ ]   (Page loads / Scrolls into view)
```

No raw CSS input. No millisecond input. Dropdown and text field only. Non-dev editors can configure without breaking layout.

Typography (font, size, weight) is controlled by theme CSS and `theme.json` presets, not block controls.

IntersectionObserver trigger for counter: `threshold: 0.5`, no rootMargin offset.

Counter fires once. If user scrolls back, counter does not replay.

---

## Page-Level Animation Control

A toggle is added to the SKVN Page Display panel in the Gutenberg editor sidebar (Page tab), at the bottom of the panel below "Full width canvas".

```
[toggle] Full width canvas
         Remove the default content container and sidebar
         for full-bleed page content.

[toggle] No animations
         Disable all motion effects on this page.
```

Default: off (animations run normally). Editor turns it on to disable all animation on a specific page.

### Technical implementation

Post meta key: `_skvn_disable_animations`

The disable meta is a kill switch, not the primary loading condition.

Recommended enqueue strategy:

1. If `_skvn_disable_animations` is true, do not enqueue theme `animations.js` and add `skvn-no-animations` body class.
2. If false, enqueue theme `animations.js` only when the page likely contains theme-level animation targets.
3. Detection can start conservative: page template/preset, registered pattern markers, or saved content containing SKVN theme motion classes.
4. If detection becomes too brittle, accept one small global theme runtime only after measuring payload and documenting the tradeoff.

Example content detection:

```php
$disable = get_post_meta( get_the_ID(), '_skvn_disable_animations', true );
$content = get_post_field( 'post_content', get_the_ID() );
$has_motion = has_block( 'core/group' ) && false !== strpos( $content, 'skvn-motion-' );

if ( ! $disable && $has_motion ) {
    wp_enqueue_script( 'skvn-animations', ... );
}
```

Plugin block animation assets are separate. A plugin block must enqueue its own `viewScript`/style through `block.json` when its animation behavior is required.

Body class for CSS fallback:

```php
// Applied via body_class filter
'skvn-no-animations'
```

CSS nuclear option:

```css
.skvn-no-animations *,
.skvn-no-animations *::before,
.skvn-no-animations *::after {
  animation: none !important;
  transition: none !important;
}
```

Use case: quote form page, terms page, or any page where motion is unwanted.

---

## Stagger Pattern

Stagger lives in `view.ts` of the block that contains the items, not in `animations.js`.

Each item gets a delay based on its index:

```javascript
items.forEach((item, i) => {
  item.style.transitionDelay = `${i * 100}ms`;
  item.classList.add('skvn-reveal');
});
```

The IntersectionObserver then triggers the parent container. When the container is in view, all children animate in sequence via their CSS delay.

---

## Device Targeting

Animation controls use independent device checkboxes, not grouped dropdown presets.

```
Devices
[x] Desktop
[x] Tablet
[ ] Mobile
```

Saved as block attribute:

```json
{
  "motionDevices": {
    "desktop": true,
    "tablet": true,
    "mobile": false
  }
}
```

Or compact data attribute:

```html
data-skvn-motion-devices="desktop tablet"
```

Hover animation must account for touch devices:

```css
@media (hover: hover) and (pointer: fine) {
  /* hover animation only on devices that support hover */
}
```

---

## Accessibility And Performance Rules

Any SKVN-owned animation must:

- Respect `prefers-reduced-motion`.
- Avoid hiding editor content with `opacity: 0` unless there is a safe editor fallback.
- Keep frontend content visible when JS fails.
- Avoid layout-shifting animation properties — use `transform` and `opacity` only.
- Pause ambient/loop animations when element is out of viewport (battery saving).
- Not load theme `animations.js` globally unless a measured lightweight runtime is explicitly accepted.
- Keep plugin block animation assets self-contained and portable.
- Keep editor sidebar controls preset-based, not raw CSS or code input.

---

## Tailwind And WindPress

Not relevant to this scope. Animation behavior for custom blocks lives in plugin `view.ts` plus plugin-owned CSS. Theme-level animation lives in theme JS/CSS. Tailwind/WindPress has no role in production animation contracts.

Allowed only for: prototyping motion intent before implementing in the owning plugin or theme CSS.

---

## 1.2.0 Implementation Placement

Implemented:

1. `src/accordion/view.ts` — accordion expand/collapse using real `scrollHeight`.
2. `src/shared/motion.ts` — reduced-motion, device targeting, and one-shot observer invariants.
3. `src/card/view.ts` — first governed block motion surface.
4. `src/motion.css` — plugin-owned motion and fallback styles.

Card controls use these exact combinations:

- Fade up: On scroll or Always.
- Fade in: On scroll or Always.
- Hover lift: On hover automatically.
- Devices: independent Desktop, Tablet, and Mobile toggles.

Deferred until a real content case justifies them:

- Slider first-slide entrance.
- Stat Counter block.
- Theme `assets/js/animations.js` for core/page/theme-level targets.
- Additional ambient presets beyond Card Hover lift.
