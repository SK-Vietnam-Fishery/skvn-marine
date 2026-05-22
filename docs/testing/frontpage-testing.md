# Frontpage Testing

This document stores reusable frontpage test prompts for SKVN Marine.

Use these tests to build WordPress editor pages, validate layout/CSS, and then extract stable sections into theme patterns.

## Current Status — Test Debt

- Runtime/page-editor test is intentionally pending.
- Source has a reusable homepage test pattern: `wp-content/themes/skvn-marine/patterns/homepage-test.php`.
- The milestone item "Homepage test page assembled in WP editor" remains unchecked until the pattern is inserted into an actual WordPress page and visually reviewed.
- The milestone item "Runtime smoke test passed" remains unchecked until the local WP server responds and the page is inspected.
- Human approved continuing to V1 / 0.3.0 on 2026-05-21 with this test debt carried forward.

Rules for all tests:

- Use English placeholder copy.
- Use placeholder or remote images while testing.
- Do not hardcode image URLs in CSS.
- Use core blocks, theme patterns, WooCommerce-native blocks, and SKVN classes first.
- Do not add builder plugins.
- Do not create custom blocks unless the pattern approach proves insufficient.
- Verify desktop and mobile before marking a section stable.

---

## How To Test The Carried 0.2.0 Debt

### Preconditions

Runtime must be the WordPress runtime, not the source repo.

```text
WP URL: http://localhost:8080
WP runtime Windows: D:\Github\minhhaifish
WP runtime WSL: /mnt/d/Github/minhhaifish
Theme source: D:\Github\skvn-marine\wp-content\themes\skvn-marine
```

Before visual testing:

```text
[ ] WordPress runtime responds at http://localhost:8080/wp-login.php.
[ ] GeneratePress parent theme is installed in runtime.
[ ] skvn-marine child theme is active.
[ ] Custom theme files are deployed or symlinked from source repo to runtime.
[ ] Browser is logged into WP Admin.
```

### Create The Page

1. Go to WP Admin > Pages > Add New.
2. Set title:

```text
Homepage Pattern Test 0.2 Debt
```

3. Insert pattern:

```text
SKVN Homepage Test Layout
```

4. Page setup:

```text
[ ] Full-width content if GeneratePress page layout option exists.
[ ] No sidebar.
[ ] Hide default page title if option exists.
[ ] Do not add builder plugin.
[ ] Do not add extra widgets just to make the test pass.
```

5. Publish or preview the page.

### Visual Checks

Desktop:

```text
[ ] Header is readable and not visually broken.
[ ] Hero content and media do not overlap.
[ ] Trust strip presents 4 clear items.
[ ] Why choose cards form a clean grid.
[ ] Factory/process areas use blue-first direction with soft mint support accents.
[ ] Newsletter image is visible and replaceable.
[ ] Footer appears as a real site footer, not raw unstyled content.
[ ] No default Search/Recent Posts/Recent Comments sidebar appears.
```

Mobile:

```text
[ ] Sections stack in the same intended order.
[ ] Buttons remain visible without hover.
[ ] Text does not overflow containers.
[ ] Media does not cover text.
[ ] Newsletter image does not create horizontal scroll.
[ ] Footer columns stack cleanly.
```

Evidence:

```text
[ ] Desktop screenshot saved/reviewed.
[ ] Mobile screenshot saved/reviewed.
[ ] Result recorded as PASS / FAIL / NEEDS REVISION.
[ ] If FAIL, note exact section and reason.
```

Pass rule:

```text
No screenshot, no UI pass.
Source/code pass does not equal visual pass.
```

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

---

## Test Method 3 — Exporter Corporate Homepage

### Source Reference

Local prototype file:

```text
C:/Users/VPF-Champion/Downloads/Dựng-layout-trang-HTML.html
```

Provided MetaAI structure shows a B2B seafood exporter homepage with:

```text
Sticky header
Hero with seafood image
Overlapping feature cards
About/factory section
Product category cards
Certification band
Capacity stat cards
Map/contact split section
Footer
```

### Critique

What works:

- Strong B2B/export positioning.
- Section order is close to SKVN V1 needs.
- Feature cards, factory/about, certification, capacity, map, and footer are reusable pattern candidates.
- Visual language fits seafood export better than a retail seafood shop.
- Contact section gives a clear lead-generation path.

What must change before SKVN implementation:

- Prototype uses global CSS and custom selectors. Test implementation should express layout using Tailwind/WindPress utilities plus stable SKVN wrapper classes.
- Prototype imports Google Fonts inside HTML. Production should not assume fonts are loaded unless added through theme policy.
- Prototype uses Vietnamese content. Test content should be English placeholder copy.
- Prototype uses `clamp()` for hero title. Production test should use Tailwind breakpoint text sizes.
- Prototype uses `border-radius: 1rem` and pill buttons broadly. SKVN cards/buttons should use max 8px radius unless the design system changes.
- Header/footer should remain GeneratePress + child theme CSS/pattern direction, not a builder plugin.
- Product cards should map toward WooCommerce categories/products, not static product logic.
- Certification badges can be static placeholders in V1, but should be replaceable images/content.
- The map is currently a static image. SKVN test 3 must use **Out of the Block: OpenStreetMap**, wrapped by SKVN map/contact classes.
- Inline styles and raw SVG repetition are acceptable in a prototype only; final pattern should move repeated visuals into theme styles or reusable block composition.

### Test Goal

Create a full homepage test page variant for a seafood exporter corporate site.

This test validates:

- Corporate B2B homepage information architecture.
- GeneratePress-compatible header/footer direction.
- Hero + overlapping feature-card pattern.
- About/factory media stack with stat overlay.
- Product category section suitable for WooCommerce-native categories.
- Certification strip.
- Capacity/stat card pattern.
- OpenStreetMap contact section, not static map imagery.
- Responsive stacking without overlap.

### Page Structure

1. Main header
2. Hero exporter section
3. Overlapping feature cards
4. About/company section
5. Product categories
6. Certification band
7. Capacity stats
8. OpenStreetMap/contact split section
9. Footer

### Tailwind Layout Spec

Header:

```html
<header class="sticky top-0 z-50 border-b border-slate-100 bg-white/95 backdrop-blur">
  <div class="mx-auto flex h-16 max-w-7xl items-center justify-between gap-6 px-4 md:px-6 lg:px-8">
```

Header nav:

```html
<nav class="hidden items-center gap-7 text-xs font-semibold uppercase tracking-wide text-slate-700 lg:flex">
  <a class="text-skvn-blue-700" href="/">Home</a>
  <a href="/about/">About</a>
  <a href="/products/">Products</a>
  <a href="/capabilities/">Capabilities</a>
  <a href="/news/">News</a>
  <a href="/contact/">Contact</a>
</nav>
```

Hero:

```html
<section class="relative overflow-hidden bg-sky-50">
  <div class="absolute inset-0 bg-[linear-gradient(180deg,rgba(255,255,255,0)_60%,rgba(255,255,255,1)_100%)]"></div>
  <div class="relative mx-auto grid min-h-[520px] max-w-7xl grid-cols-12 items-end gap-8 px-4 py-12 md:px-6 lg:px-8 lg:py-16">
    <div class="col-span-12 pb-4 lg:col-span-6">
    <div class="col-span-12 lg:col-span-6">
```

Hero title:

```html
<p class="mb-4 text-sm font-semibold italic text-skvn-blue-700">
  Premium Quality - Reliable Supply
</p>
<h1 class="max-w-xl text-4xl font-extrabold uppercase leading-tight text-skvn-blue-950 md:text-5xl lg:text-6xl">
  Trusted Seafood Exporter From Viet Nam
</h1>
<p class="mt-5 max-w-lg text-base leading-7 text-slate-600">
  Supplying fresh, frozen, and value-added seafood with verified origin, strict quality control, and reliable export handling.
</p>
```

Hero CTAs:

```html
<div class="mt-7 flex flex-wrap gap-3">
  <a class="inline-flex items-center justify-center rounded-md bg-skvn-blue-700 px-5 py-3 text-sm font-semibold text-white hover:bg-skvn-blue-800" href="/products/">
    Explore Products
  </a>
  <a class="inline-flex items-center justify-center rounded-md border border-skvn-blue-200 bg-white px-5 py-3 text-sm font-semibold text-skvn-blue-700 hover:border-skvn-blue-700" href="/about/">
    About Us
  </a>
</div>
```

Hero image:

```html
<figure class="relative mx-auto max-w-xl">
  <img class="h-auto w-full object-contain drop-shadow-2xl" src="https://images.unsplash.com/photo-1615141982883-c7ad0e69fd62?auto=format&fit=crop&w=1200&q=80" alt="Fresh seafood export selection">
</figure>
```

Overlapping feature cards:

```html
<section class="relative z-10 -mt-10 px-4 md:px-6 lg:px-8">
  <div class="mx-auto grid max-w-7xl gap-4 rounded-lg bg-white p-4 shadow-lg md:grid-cols-2 lg:grid-cols-4">
    <article class="flex gap-4 rounded-lg border border-slate-100 p-4">
```

About/factory:

```html
<section class="mx-auto grid max-w-7xl grid-cols-12 items-center gap-10 px-4 py-16 md:px-6 lg:px-8">
  <div class="col-span-12 lg:col-span-5">
  <div class="col-span-12 lg:col-span-7">
```

About media stack:

```html
<div class="relative grid grid-cols-2 gap-4">
  <figure class="col-span-2 aspect-[16/9] overflow-hidden rounded-lg bg-slate-100">
  <figure class="aspect-[4/3] overflow-hidden rounded-lg bg-slate-100">
  <figure class="aspect-[4/3] overflow-hidden rounded-lg bg-slate-100">
  <div class="absolute left-1/2 top-1/2 w-48 -translate-x-1/2 -translate-y-1/4 rounded-lg bg-skvn-blue-900 p-5 text-center text-white shadow-xl">
</div>
```

Product categories:

```html
<section class="bg-sky-50">
  <div class="mx-auto max-w-7xl px-4 py-16 md:px-6 lg:px-8">
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
      <article class="overflow-hidden rounded-lg border border-sky-100 bg-white shadow-sm">
```

Certification band:

```html
<section class="bg-skvn-blue-800 text-white">
  <div class="mx-auto max-w-7xl px-4 py-12 text-center md:px-6 lg:px-8">
    <div class="flex flex-wrap items-center justify-center gap-5">
      <div class="grid h-20 w-20 place-items-center rounded-full bg-white text-xs font-extrabold text-skvn-blue-800 shadow-md">
```

Capacity stats:

```html
<section class="mx-auto max-w-7xl px-4 py-16 md:px-6 lg:px-8">
  <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
    <article class="rounded-lg border border-slate-200 bg-white p-6 text-center">
```

OpenStreetMap/contact split:

```html
<section class="bg-white px-4 py-12 md:px-6 lg:px-8">
  <div class="mx-auto grid max-w-7xl overflow-hidden rounded-lg border border-slate-200 shadow-lg lg:grid-cols-[1.15fr_0.85fr]">
    <div class="relative min-h-[420px] bg-slate-100">
      <!-- Insert Out of the Block: OpenStreetMap block here. Do not use a static map image. -->
      <div class="absolute left-6 bottom-6 max-w-xs rounded-lg bg-white p-5 shadow-xl">
    </div>
    <aside class="bg-skvn-blue-800 p-8 text-white lg:p-10">
```

Footer:

```html
<footer class="bg-skvn-blue-950 text-slate-300">
  <div class="mx-auto grid max-w-7xl gap-8 px-4 py-12 md:grid-cols-2 md:px-6 lg:grid-cols-4 lg:px-8">
```

### Placeholder Content

Hero:

```text
Premium Quality - Reliable Supply
Trusted Seafood Exporter From Viet Nam
Supplying fresh, frozen, and value-added seafood with verified origin, strict quality control, and reliable export handling.
Explore Products
About Us
```

Feature cards:

```text
Strict Quality Control
Verified Sourcing
Global Cold-Chain Delivery
Experienced Export Team
```

About:

```text
About SKVN Marine
With years of seafood sourcing and export experience, SKVN Marine connects reliable Vietnamese producers with restaurants, distributors, and food-service partners worldwide.
```

Products:

```text
Squid
Octopus
Shrimp
Fish
Shellfish
Crab
```

Certifications:

```text
HACCP
BRCGS
ISO
FDA
HALAL
ASC
```

Capacity:

```text
3 Factories
7,000+ Tons / Year
8,000+ Tons Cold Storage
650+ Team Members
```

Contact:

```text
SKVN Marine Co., Ltd
Ninh Thuan, Viet Nam
+84 000 000 000
hello@skvn-marine.example
Send a Quote Request
```

### OpenStreetMap Requirement

The map area must be built with:

```text
Out of the Block: OpenStreetMap block
+ SKVN wrapper/card classes
+ Contact card overlay or split panel
```

Do not use:

```text
Static map image
Hardcoded map screenshot
Custom map block
New map plugin
```

Desktop behavior:

```text
Map left
Contact panel right
Small floating company card over map bottom-left
Minimum map height: 420px
```

Mobile behavior:

```text
Map stacks above contact panel
Floating card becomes normal readable card if overlay reduces legibility
Phone/email remain visible without hover
```

### Acceptance Checklist

```text
[ ] Header/footer follow GeneratePress + SKVN child theme direction.
[ ] Layout uses Tailwind/WindPress utility classes plus stable SKVN wrapper classes.
[ ] No builder plugin is introduced.
[ ] Global CSS reset from prototype is not copied into theme unchanged.
[ ] Hero uses breakpoint text sizes, not viewport clamp font sizing.
[ ] Cards/buttons use max 8px radius unless design system changes.
[ ] Feature cards overlap hero cleanly on desktop and stack safely on mobile.
[ ] About media stack does not hide important text or image content.
[ ] Product section can map to WooCommerce native categories/products.
[ ] Certification badges are replaceable content/images.
[ ] Capacity cards use reusable stat-card direction.
[ ] Contact map uses Out of the Block: OpenStreetMap, not static image.
[ ] No image URL is hardcoded in CSS.
[ ] CTA buttons remain visible on mobile.
[ ] Text does not overflow containers.
[ ] Desktop and mobile screenshots match the intended section order.
```
