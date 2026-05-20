# Frontpage Testing

This document stores reusable frontpage test prompts for SKVN Marine.

Use these tests to build WordPress editor pages, validate layout/CSS, and then extract stable sections into theme patterns.

Rules for all tests:

- Use English placeholder copy.
- Use placeholder or remote images while testing.
- Do not hardcode image URLs in CSS.
- Use core blocks, theme patterns, WooCommerce-native blocks, and SKVN classes first.
- Do not add builder plugins.
- Do not create custom blocks unless the pattern approach proves insufficient.
- Verify desktop and mobile before marking a section stable.

---

## Test Method 1 — Full Homepage Composite

### Purpose

Validate the full SKVN Marine homepage structure before extracting reusable patterns.

### Goal

Build a reusable homepage test page that validates SKVN Marine visual system, layout patterns, Tailwind utility strategy, and responsive behavior.

Use:

```text
Theme patterns + core blocks + WooCommerce native/product placeholders
No custom Product Grid/List block
No new page builder plugin
Images can use placeholder/network URLs
Text in English
```

### Page Structure

1. Top utility bar
2. Main header
3. Hero seafood landing section
4. Category strip
5. Seafood combo/product cards
6. Why choose us strip
7. Featured product carousel/grid placeholder
8. Cold-chain promo banner
9. Process steps
10. Trust strip
11. Newsletter signup band
12. Footer

### Tailwind Layout Spec

Top bar:

```html
<div class="bg-skvn-blue-900 text-white text-xs">
  <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-2">
```

Header:

```html
<header class="bg-white shadow-sm">
  <div class="mx-auto flex max-w-7xl items-center gap-6 px-4 py-4">
```

Hero:

```html
<section class="relative overflow-hidden bg-sky-50">
  <div class="mx-auto grid max-w-7xl grid-cols-12 gap-6 px-4 py-10 lg:py-14">
    <div class="col-span-12 lg:col-span-7">
    <div class="col-span-12 lg:col-span-5">
```

Hero title:

```html
<h1 class="text-4xl font-bold leading-tight text-skvn-blue-900 lg:text-5xl">
  Fresh Seafood From Ninh Thuan To Your Table
</h1>
```

Feature icons under hero:

```html
<div class="grid grid-cols-2 gap-4 pt-6 md:grid-cols-4">
  <div class="flex flex-col items-center text-center text-sm text-slate-700">
```

Category strip:

```html
<section class="-mt-6 relative z-10">
  <div class="mx-auto max-w-7xl rounded-lg bg-white px-4 py-5 shadow-lg">
    <div class="grid grid-cols-3 gap-4 md:grid-cols-6 lg:grid-cols-8">
```

Product/combo cards:

```html
<section class="mx-auto max-w-7xl px-4 py-10">
  <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-4">
    <article class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
```

Buttons:

```html
<a class="inline-flex items-center justify-center rounded-md bg-skvn-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-skvn-blue-800">
```

Why choose strip:

```html
<section class="bg-sky-50">
  <div class="mx-auto grid max-w-7xl gap-4 px-4 py-6 md:grid-cols-4">
```

Promo banner:

```html
<section class="mx-auto max-w-7xl px-4 py-8">
  <div class="grid overflow-hidden rounded-lg bg-skvn-blue-900 text-white md:grid-cols-2">
```

Process steps:

```html
<section class="bg-white">
  <div class="mx-auto grid max-w-7xl gap-4 px-4 py-8 md:grid-cols-4">
```

Newsletter/footer top:

```html
<section class="relative bg-sky-50">
  <div class="mx-auto grid max-w-7xl items-center gap-6 px-4 py-6 md:grid-cols-12">
    <div class="md:col-span-8">
    <figure class="md:col-span-4 md:-my-10">
```

Footer:

```html
<footer class="bg-skvn-blue-900 text-white">
  <div class="mx-auto grid max-w-7xl gap-8 px-4 py-10 md:grid-cols-5">
```

### Placeholder Content

Hero:

```text
Fresh Seafood From Ninh Thuan To Your Table
Selected daily, chilled professionally, delivered fast to restaurants, hotels, and seafood lovers.
```

Categories:

```text
Lobster
Crab
Squid
Mackerel
Clams
Mixed Seafood
Shrimp
View All
```

Product cards:

```text
Family Seafood Box
Premium Crab Combo
Gift Seafood Set
Luxury Lobster Box
```

Why choose:

```text
Daily Fresh Catch
Clear Origin
Cold Chain Delivery
Flexible Payment
```

Newsletter:

```text
Get Fresh Seafood Updates
Receive weekly catches, seasonal offers, and sourcing stories from the coast.
```

### Acceptance Checklist

```text
[ ] Desktop matches overall structure of reference image.
[ ] Mobile stacks cleanly without overlap.
[ ] Header/footer are theme-owned, no builder plugin.
[ ] Category strip is reusable pattern.
[ ] Newsletter image is replaceable Image block.
[ ] No image URL hardcoded in CSS.
[ ] CTA buttons always visible on mobile.
[ ] Cards use max 8px radius unless design system changes.
[ ] Text does not overflow containers.
[ ] Uses Tailwind-style utility classes or matching WindPress utilities.
[ ] Placeholder/network images load or degrade cleanly.
```

---

## Test Method 2 — Editorial Typography Hero

### Source Reference

Reference URL: https://the7.io/fse-nutrition/

Provided implementation sample:

```html
<section class="relative bg-[#FAF7F2] min-h-[90vh] flex items-center">
  <div class="w-full max-w-7xl mx-auto px-6 lg:px-8 py-20 lg:py-28">
    <div class="space-y-1">
      <h1 class="font-serif text-[clamp(3rem,10vw,9rem)] leading-[0.8] tracking-tight text-[#1A1A1A] font-[Fraunces]">Healthy</h1>
      <h1 class="font-serif text-[clamp(2.8rem,9vw,8.5rem)] leading-[0.85] tracking-tight text-[#1A1A1A] font-[Fraunces]">Smart Meal Planning</h1>
      <div class="flex items-baseline gap-6 md:gap-10 mt-2">
        <span class="font-serif text-[clamp(3.5rem,11vw,9.5rem)] leading-none text-[#5A7D5A] font-[Fraunces]">&</span>
        <h1 class="font-serif text-[clamp(3rem,10vw,9rem)] leading-[0.8] tracking-tight text-[#1A1A1A] font-[Fraunces]">Easy</h1>
      </div>
    </div>
  </div>
</section>
```

### Critique

What works:

- Strong editorial hierarchy.
- Simple section structure.
- Large type creates a memorable first viewport.
- CTA is secondary and quiet.
- Good candidate for testing an alternate brand/editorial hero.

What does not fit SKVN production rules yet:

- `text-[clamp(...vw...)]` scales with viewport width. For this project, production CSS should prefer Tailwind breakpoints and avoid viewport-scaled font sizing.
- `font-[Fraunces]` and `font-[Inter]` only work if those fonts are loaded. Do not assume external fonts.
- `min-h-[90vh]` can hide the next section. SKVN landing heroes should leave a hint of next content visible.
- The nutrition palette is warm beige/green. SKVN should adapt to marine/export tones.
- Multiple `h1` elements are not ideal for page semantics. Use one `h1` and spans inside it.
- The sample has no seafood/product signal. SKVN hero should include either product imagery, vessel/cold-chain imagery, or a clear seafood/export copy signal.

### Test Goal

Create a hero-only test page section that adapts the editorial typography idea to SKVN Marine.

This test validates:

- Oversized editorial typography without viewport `clamp()`.
- A single semantic `h1`.
- Marine/export brand color adaptation.
- Responsive line wrapping.
- Optional hero image/media that does not dominate the typography.
- A visible hint of the next section.

### Tailwind Layout Spec

Section:

```html
<section class="relative overflow-hidden bg-sky-50">
  <div class="mx-auto grid min-h-[78vh] max-w-7xl grid-cols-12 items-center gap-8 px-4 py-16 md:px-6 lg:px-8 lg:py-20">
```

Typography column:

```html
<div class="col-span-12 lg:col-span-8">
  <p class="mb-5 text-xs font-semibold uppercase tracking-[0.18em] text-skvn-blue-700">
    Ninh Thuan Seafood Export
  </p>
  <h1 class="max-w-5xl font-serif text-6xl font-bold leading-[0.9] text-skvn-blue-950 md:text-7xl lg:text-8xl xl:text-9xl">
    <span class="block">Fresh</span>
    <span class="block">Seafood Sourcing</span>
    <span class="flex items-baseline gap-4 md:gap-6">
      <span class="text-skvn-teal-600">&amp;</span>
      <span>Delivery</span>
    </span>
  </h1>
  <p class="mt-8 max-w-2xl text-base leading-7 text-slate-600 md:text-lg">
    Daily catches, verified origin, and cold-chain handling for restaurants, hotels, and seafood distributors.
  </p>
</div>
```

Media column:

```html
<div class="col-span-12 lg:col-span-4">
  <figure class="relative mx-auto aspect-[4/5] max-w-sm overflow-hidden rounded-lg bg-white shadow-xl">
    <img class="h-full w-full object-cover" src="https://images.unsplash.com/photo-1544943910-4c1dc44aab44?auto=format&fit=crop&w=900&q=80" alt="Fresh seafood on ice">
  </figure>
</div>
```

CTA row:

```html
<div class="mt-8 flex flex-wrap items-center gap-4">
  <a href="/request-a-quote/" class="inline-flex items-center justify-center rounded-md bg-skvn-blue-700 px-5 py-3 text-sm font-semibold text-white hover:bg-skvn-blue-800">
    Request a Quote
  </a>
  <a href="#products" class="inline-flex items-center justify-center text-sm font-semibold text-skvn-blue-900 underline decoration-skvn-teal-500/50 underline-offset-4 hover:decoration-skvn-teal-500">
    View seafood range
  </a>
</div>
```

Next-section hint:

```html
<div class="absolute inset-x-0 bottom-0 h-8 bg-white"></div>
```

### Placeholder Content

```text
Ninh Thuan Seafood Export

Fresh
Seafood Sourcing
& Delivery

Daily catches, verified origin, and cold-chain handling for restaurants, hotels, and seafood distributors.

Request a Quote
View seafood range
```

### Acceptance Checklist

```text
[ ] Uses one h1 only.
[ ] Does not use viewport clamp font sizing.
[ ] Uses Tailwind breakpoint font sizes: text-6xl md:text-7xl lg:text-8xl xl:text-9xl.
[ ] Hero height is less than full viewport and leaves next-section hint visible.
[ ] Text does not overlap media at desktop, tablet, or mobile.
[ ] Media image is replaceable and not hardcoded in CSS.
[ ] CTA is visible on mobile.
[ ] Palette is adapted to SKVN marine/export colors, not beige/green nutrition colors.
[ ] Section can be converted into a reusable theme pattern if accepted.
```
