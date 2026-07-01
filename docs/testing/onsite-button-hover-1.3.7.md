# Onsite QA — Button Hover Verification (Core Button Hover + Collection CTAs + Slider Compatibility) — V1 / 1.3.7

**Planning:** `.context/planning/028_VER_1_3_7_COLLECTION_UI_CARD_STYLES_PLANNING.md` (for collection CTA hovers) + `.context/planning/archives/033_VER_1_3_8_CORE_BUTTON_HOVER_FIX_PLAN.md` (core feature + slider case)  
**Contracts/Decisions:** `docs/standards/gutenberg-block-extension-css-contract.md`, `docs/decisions/core-control-core-button-hover.md`, `docs/decisions/collection-card-system-1.3.7.md`  
**Milestone:** V1 / 1.3.7 — Collection Block UI & Card Styles (includes hover button work per user request for slider case + collection polish)  
**Status:** READY — chạy sau khi 1.3.7 source + build (collection CTAs + any slider hover adjustments for compatibility). Use on live site + test page.

---

## Target / Repro Pages

1. **Live repro for Slider case (primary for the stuck "button trong slider" issue):**
   - https://minhhaifishery.com/home-page/
   - Look for buttons/links inside the featured marine solution / hero-style Slider (e.g. "View product", "Request information", "Explore solutions").

2. **Test page for full verification (create onsite):**
   - "Button Hover Test 1.3.7"
   - Include:
     - SKVN Slider (hero preset) with core/button blocks inside slides (one with custom hover colors via Core Control, one without).
     - SKVN Product Collection (Grid + Carousel) showing cards with the new CTAs (quote/view, catalog CTA, etc.).
     - SKVN Post Collection (Grid + Carousel) with read-more, etc.
     - Standalone core/button blocks outside any slider/collection for baseline comparison.
     - A regular Gutenberg paragraph link or button for the `a:hover { color: var(--contrast); }` reference.

Optional: Separate mobile test page if needed.

## Preconditions

- `skvn-marine-blocks` active.
- **Core Control → Core Button Hover Colors** feature **enabled** (in SKVN Marine admin menu). This is the opt-in that adds the `has-skvn-button-hover` class + `--skvn-btn-hover-*` vars.
- At least one core/button block configured with custom "Hover Colors" (text + background, different from default/theme).
- At least one core/button without custom hover (to test fallback).
- WooCommerce active for product collections.
- Test products/posts with images.
- SKVN Slider and Collection blocks available (inserter).
- Browser DevTools (Chrome/Edge/Firefox recommended for :hover force state and Styles tab).
- Reduced-motion testing: Use DevTools Rendering tab → "Emulate CSS prefers-reduced-motion: reduce".
- Build done: `npm run build` in plugin dir (for latest styles).

**Note on --contrast reference (Gutenberg standard):** On the site, regular content links often use `a:hover, a:focus, a:active { color: var(--contrast); }` (or equivalent from GP/theme). We verify that buttons inside Slider/collection do **not** break this base behavior, and that the Core feature (when used) takes precedence appropriately.

---

## Test 1 — Core Button Hover Feature Baseline (outside Slider/Collection)

### Steps (DevTools heavy)
1. On the test page, find a standalone core/button that **has custom hover colors set** (via Core Control inspector).
2. Hard refresh.
3. DevTools → Elements: select the `<div class="wp-block-button ...">` wrapper.
   - Confirm it has class `has-skvn-button-hover`.
   - Confirm inline `style` contains `--skvn-btn-hover-text: ...` and/or `--skvn-btn-hover-bg: ...`.
4. Select the inner `<a class="wp-block-button__link">`.
5. In Styles tab (or Elements > :hov), force `:hover` state.
6. Observe:
   - Computed `color` and `background` change to the configured hover values.
   - In Styles tab: the feature rule `.wp-block-button.has-skvn-button-hover .wp-block-button__link:hover` (or similar) should be active **without strikethrough**.
   - Compare specificity: the feature rule should match or beat any theme rule (e.g. `.wp-block-button.skvn-button--primary ...:hover`).
7. Repeat for a button **without** custom hover colors: should use theme/default hover (not feature vars).
8. Force reduced-motion (Rendering tab) + re-hover: transitions should be removed (no animation).

### Pass criteria
- [ ] Wrapper has `has-skvn-button-hover` + correct inline vars (only when configured).
- [ ] On :hover, computed colors exactly match the set hover values (use eyedropper or computed tab).
- [ ] Feature CSS rule is applied (no strikethrough, higher or equal specificity to theme).
- [ ] Fallback works when no custom hover set.
- [ ] Reduced-motion: no transition on the link.
- [ ] No console errors.

### Evidence to report
- Screenshot: Elements tree showing wrapper + vars + class.
- Screenshot: Styles tab on forced :hover (rule highlighted, computed color).
- Note: "Feature rule wins over theme? yes/no" + any strikethroughs.
- Reduced-motion test result.

---

## Test 2 — Button Hover Inside SKVN Slider (the stuck "button trong slider" case)

### Setup on live page (https://minhhaifishery.com/home-page/) or test page
- Slider with hero (or product-showcase/card) preset.
- Inside a slide: at least one core/button with custom hover colors enabled in Core Control.
- Another button/link without (or regular content link for --contrast baseline).

### Steps (DevTools)
1. Hard refresh the slider page.
2. DevTools → Elements: find the button/link **inside** `.skvn-slide__content` or `.skvn-slider--hero .wp-block-button__link`.
3. Note the ancestor classes (e.g. `skvn-slider--hero`).
4. Select the link (`a.wp-block-button__link` or plain `a`).
5. Force `:hover`.
6. In Styles/Computed:
   - What is the computed `color` / `background` on hover?
   - Which rule(s) are active for the hover state? (Look for `.skvn-slider--hero ...:hover`, `.wp-block-button.skvn-button--primary ...:hover`, the feature rule with `has-skvn-button-hover`, or `a:hover` using `--contrast`).
   - Is the feature rule (if applicable) crossed out? Does the var `--skvn-btn-hover-*` resolve correctly?
   - Compare to the global `a:hover { color: var(--contrast); }` — is it being overridden?
7. Check specificity: right-click the rule → "Copy rule" or note selector weight.
8. Repeat outside the slider (same button style) for comparison.
9. Test focus (Tab key) and reduced-motion.
10. If using the Core feature inside slide: confirm the injected class + vars are present on the wrapper (even inside slide render).

### Expected (per contract + user hypothesis)
- When Core feature configured on the button inside slide: hover should use the custom `--skvn-btn-hover-*` colors (feature rule wins or is not overridden by slider hero hard-codes).
- Without feature: falls back to intended preset/theme hover (e.g. the hard-coded hero one), **or** respects base `a:hover var(--contrast)` if no preset overrides.
- The "force :hover shows change" is true, but the **desired** color (from feature or --contrast) must be the computed one.
- No breakage of native hover computation.
- Reduced-motion respected.
- Slider hero preset still looks correct by default (fallback).

### Pass criteria
- [ ] Inside slider, custom hover colors (from feature) apply correctly on :hover (matches outside behavior).
- [ ] DevTools: feature rule active without strikethrough; vars resolve; specificity >= any `.skvn-slider--hero ...:hover` or theme primary rules.
- [ ] Base `--contrast` (or intended hover) works for non-custom buttons.
- [ ] Computed colors match expectation (screenshot comparison inside vs outside).
- [ ] Focus-visible and reduced-motion behave as in Test 1.
- [ ] No layout shift or pointer issues on hover inside slide.

### Evidence to report
- Page URL + which slide/preset.
- Screenshots:
  - Elements: wrapper inside slide with class/vars.
  - Styles tab on :hover inside slider (winning rule, computed color).
  - Same for equivalent button outside slider.
- Console notes if any (e.g. `getComputedStyle(link).color` on hover).
- "Does it now match Gutenberg --contrast behavior when expected? yes/no + notes"
- Reduced-motion result.

---

## Test 2.5 — Specific Repro from Live Site (your "Explore solutions" button example)

This is the exact markup you provided from https://minhhaifishery.com/home-page/ (likely inside the hero/feature slider slide).

```html
<div class="wp-block-button has-skvn-button-hover" style="--skvn-btn-hover-text:#ffffff;--skvn-btn-hover-bg:#ffe101;"><a class="wp-block-button__link has-skvn-white-color has-midnight-gradient-background has-text-color has-background has-link-color wp-element-button" style="border-top-left-radius:100px;border-top-right-radius:100px;border-bottom-left-radius:100px;border-bottom-right-radius:100px">Explore solutions</a></div>
```

**Key point you raised**: The data (`has-skvn-button-hover` class + inline `--skvn-btn-hover-*` vars) is static (injected by PHP render at page load). Hover is **not** in the HTML — it is powered by a separate CSS rule that reacts to the `:hover` state on the child `<a>` and consumes the vars.

### DevTools Verification Steps (exactly for this element)
1. Go to https://minhhaifishery.com/home-page/ and hard refresh (Ctrl+Shift+R).
2. Find the "Explore solutions" button (in the featured marine solution / hero slider area).
3. DevTools → Elements tab:
   - Select the outer `<div class="wp-block-button has-skvn-button-hover">`.
   - Confirm `has-skvn-button-hover` class is present.
   - Confirm `style` attribute has `--skvn-btn-hover-text:#ffffff` and `--skvn-btn-hover-bg:#ffe101`.
   - Expand to the inner `<a class="wp-block-button__link ...">`.
4. Select the `<a>` element.
5. In the Styles pane, click the `:hov` button (or right-click the element → Force state → :hover).
6. While forced hover is active:
   - Look in the Styles list for the rule containing `has-skvn-button-hover` + `:hover` (e.g. `.wp-block-button.has-skvn-button-hover .wp-block-button__link:hover`).
   - It should appear **active** (usually at the top or without strikethrough).
   - Check the Computed tab (or Styles → Computed):
     - `color` should be `rgb(255, 255, 255)` or the #ffffff.
     - `background-color` or `background` should be `#ffe101` or the var value (the rule should show `background: var(--skvn-btn-hover-bg) ...`).
   - If a `.skvn-slider--hero .wp-block-button__link:hover` rule is also listed, note its position (later rules win if same specificity) and whether it is overriding (strikethrough on the feature rule?).
7. Run this in Console (while the <a> is selected or copy the selector):
   ```js
   const link = document.querySelector('.wp-block-button.has-skvn-button-hover .wp-block-button__link');
   const wrapper = link.closest('.wp-block-button');
   console.log('Wrapper class:', wrapper.className);
   console.log('Vars on wrapper:', wrapper.style.cssText);
   // Force hover simulation isn't direct, but check computed after manual hover or use:
   link.addEventListener('mouseover', () => {
     console.log('On hover computed color:', getComputedStyle(link).color);
     console.log('On hover computed bg:', getComputedStyle(link).backgroundColor);
   }, {once: true});
   console.log('Manually hover the button now to see logs');
   ```
8. Turn off forced hover. Force reduced-motion in Rendering tab (Emulate CSS media feature prefers-reduced-motion: reduce). Re-force hover on the link and verify no transition (instant change).
9. Compare to a similar button **outside** any slider on the same page (if present) or create one in editor for baseline.

### What "should" happen if working
- The feature rule must be visible and not crossed out.
- Computed values must match the static vars you see in the HTML (`#ffffff` text, `#ffe101` bg).
- The slider's hero hover rule must **not** win (or must itself use `var(--skvn-btn-hover-*)` with fallback so your custom colors take effect).
- Native `a:hover { color: var(--contrast); }` (or equivalent) should be the base for plain links, but this button uses the custom vars because it has the scoping class.

### Pass/Fail + Evidence
- [ ] Vars + class present on the div (static data confirmed).
- [ ] On forced :hover, computed color/bg exactly match #fff / #ffe101.
- [ ] Feature `:hover` rule is active in Styles (no strikethrough, higher or equal specificity to any `.skvn-slider--hero...` rule).
- [ ] If slider rule is present, it does not prevent the var from applying.
- [ ] Reduced-motion test passes.
- [ ] Matches the "Gutenberg button hover" expectation when using the feature.

**Evidence to report** (add to the template below):
- Screenshot of the exact div + <a> in Elements.
- Screenshot of Styles tab with forced :hover (highlight the has-skvn rule and any slider rule).
- Screenshot of Computed tab showing the final color/bg.
- Console output from the script above.
- Note: "Does the feature rule win over slider hero? yes/no. Which rule is last in the cascade?"

---

## Analysis of live site run (user-provided console output from https://minhhaifishery.com/home-page/)

**User output (exact):**
```
Wrapper classes: wp-block-button has-skvn-button-hover
Inline vars: --skvn-btn-hover-text: #ffffff; --skvn-btn-hover-bg: #ffe101;
Computed on hover - color: rgb(255, 255, 255)
Computed on hover - bg: rgba(0, 0, 0, 0)
Slider hero hover rule: .skvn-slider--hero .wp-block-button__link:focus, .skvn-slider--hero .wp-block-button__link:hover
Feature hover rule found in sheet 18 : .has-skvn-button-hover .wp-block-button .wp-block-button__link:focus-visible, .has-skvn-button-hover .wp-block-button .wp-block-button__link:hover
Feature hover rule found in sheet 48 : .wp-block-button.has-skvn-button-hover .wp-block-button__link:hover, .wp-block-button.has-skvn-button-hover .wp-block-button__link:focus-visible
```

**Diagnosis from output + site markup you shared earlier:**
- Static data injection **correct**: class `has-skvn-button-hover` + exact vars present on the wrapper div.
- Text hover **working**: computed color = rgb(255,255,255) = #ffffff (the var value).
- Background hover **broken**: computed bg = rgba(0,0,0,0) (transparent) instead of #ffe101.
- Rules present:
  - Slider hero rule (the hard-coded one from src/slider/style.css lines ~696-700).
  - Editor-style feature rule (sheet 18, the `.has-skvn... .wp-block-button ...` version — this is lower specificity, for editor preview).
  - Correct frontend feature rule (sheet 48: `.wp-block-button.has-skvn-button-hover .wp-block-button__link:hover` — this is the one that should win per contract).

**Root cause (confirmed by your data):**
The link also carries `has-midnight-gradient-background has-background` (from theme/SKVN button styles), plus the ancestor `.skvn-slider--hero` context.
The slider hero `:hover` rule (or the gradient's own hover handling) is winning on `background`, resulting in transparent instead of the yellow var.
The feature rule is being found, but its `background: var(--skvn-btn-hover-bg)` declaration is being overridden in the cascade/specificity inside the hero slide.

This is exactly the "button trong slider không nhận hover như button của guttenberg" case: the static data is there, the rule is there, but the visual hover (especially bg) doesn't use the custom var because of higher-specificity slider/theme rules.

**Next DevTools steps to pinpoint (run these now on the same element):**
1. Force :hover on the `<a>` again.
2. In Styles tab (with :hover forced):
   - Scroll to find the **last** (winning) `background` or `background-color` declaration in the :hover rules.
   - Screenshot the entire :hover rules list, highlighting any `.skvn-slider--hero ...:hover`, the feature rule from sheet 48, and any gradient-related rule.
   - Note which declaration for `background` has no strikethrough.
3. In Computed tab: expand "background" or "background-color" and see the "Styles" source for the winning value.
4. Check the order of stylesheets: look for when "slider" styles vs "core-button-hover" inline style is loaded.

**Pass criteria for this specific run:**
- [ ] Feature rule (sheet 48) is present.
- [ ] But its background declaration is crossed out or not the computed value.
- [ ] Slider hero rule or gradient rule provides the final background.

Update the evidence template with:
- Winning background declaration source (e.g. "from .skvn-slider--hero rule")
- Computed background value

This data will let us precisely write the minimal CSS fix in 1.3.7 (make the hero rule respect the var when the has- class is present, using fallback to the original preset).

**Update applied in this session**: Removed the hard-coded `color: #073b5a;` from `.skvn-slider--hero .wp-block-button__link` (base and the non-scoped hover rule). This stops "tự dưng gán màu vào" and lets the button's color classes (e.g. has-skvn-white-color) or intended inheritance control the text color, while the scoped has-skvn rule (already present) handles custom hover colors on :hover. The bg for hero button look and hover fallback remain. 

The scoped hover rule now has proper preset fallbacks instead of `inherit`. 

After build, re-run the console test on the live site – the bg should now use the yellow on hover, and base color should respect the button's color class.

**Root cause confirmed (2026-06-23):**
Computed bg `rgba(0,0,0,0)` = transparent. Cause: WP global-styles generates `.wp-element-button:hover { background-color: rgba(0,0,0,0); }` at specificity `(0,2,0)`, loading before plugin inline style but winning because our rule was also `(0,2,0)` (same specificity, last-writer-wins per source order — WP's rule was actually inserted after ours in the cascade).

Text hover worked because WP does not set `color` in `.wp-element-button:hover`, so our `color` declaration was the only one and was applied.

**Fix applied (2026-06-23):**
Added a `(0,3,0)` rule to `skvn_marine_blocks_enqueue_button_hover_frontend_style()` inline CSS:
```css
.wp-block-button.has-skvn-button-hover .wp-block-button__link.wp-element-button:hover,
.wp-block-button.has-skvn-button-hover .wp-block-button__link.wp-element-button:focus-visible {
    color: var(--skvn-btn-hover-text, inherit);
    background: var(--skvn-btn-hover-bg, inherit);
}
```
`(0,3,0)` beats WP global-styles `(0,2,0)` AND slider hero base rule `(0,2,1)` — resolves both regular button and hero slider button in one rule.

Also fixed hero slider feature rule fallback: `var(--skvn-btn-hover-bg, #eaf7ff)` → `var(--skvn-btn-hover-bg, inherit)` and `var(--skvn-btn-hover-text, #073b5a)` → `var(--skvn-btn-hover-text, inherit)` so buttons without custom hover in hero context don't get forced colors.

**Re-verify after build + deploy:**
- [ ] Regular button: bg hover applies configured color (not transparent)
- [ ] Hero slider button: bg hover applies configured color
- [ ] Product showcase button: bg hover applies configured color
- [ ] Button without custom hover: no unintended color change from fallback

**CORRECTION — real root cause (2026-06-23, from in-editor `wp.data` dump + frontend Styles pane):**
The previous "(0,3,0) vs WP global-styles" cascade theory was a partial/wrong diagnosis. Editor attribute dump proved background **does save** (one button had `skvnHoverBgColor:#000000`); buttons that looked "broken" simply had only `skvnHoverTextColor` set, bg empty.

The actual bug was self-inflicted: the shared hover rule always declared `background: var(--skvn-btn-hover-bg, inherit)` for **every** `has-skvn-button-hover` button. On a text-only button the var is absent → `inherit` → the button's own `#fff`/gradient background is wiped to transparent on hover. Same for `color` on bg-only buttons.

**Fix (gated rules):**
- Render filter now adds a per-property marker class: `has-skvn-btn-hover-text` only when text is set, `has-skvn-btn-hover-bg` only when bg is set (plus the generic `has-skvn-button-hover` for transition/editor).
- Inline CSS + slider hero CSS split into two rules, each gated by its marker, with **no `inherit` fallback** (the rule only exists when the var exists):
  ```css
  .wp-block-button.has-skvn-btn-hover-text .wp-block-button__link.wp-element-button:hover { color: var(--skvn-btn-hover-text); }
  .wp-block-button.has-skvn-btn-hover-bg   .wp-block-button__link.wp-element-button:hover { background: var(--skvn-btn-hover-bg); }
  ```
- Net effect: a text-only button keeps its native background on hover; a bg-only button keeps its native text color; both-set works as before.

**Re-verify (supersedes the checklist above):**
- [ ] Text-only hover button: on hover, text color changes, **background stays the native `#fff`/gradient** (no transparent flash).
- [ ] Bg-only hover button: on hover, background changes, text color unchanged.
- [ ] Both-set button: both apply.
- [ ] Hero slider buttons + regular buttons behave identically.
- [ ] Confirm where the "192px / auto-center" layout glitch occurs (editor vs frontend) — not reproduced on the published hero in the provided screenshot; pending repro.

**CORRECTION 2 — background save bug was real (editor), not cascade (2026-06-23):**
The `#000000` seen in the `wp.data` dump was the color picker's default-black artifact, NOT a deliberate save. Real root cause: the Background control shared ONE attribute (`skvnHoverBgColor`) for both solid color and gradient. With `PanelColorGradientSettings`, the unused handler (`onGradientChange`) fires with `undefined` when picking solid (and vice versa), clobbering the value to `''`. So the picked background never stuck.

**Fix:** split into two attributes — `skvnHoverBgColor` (solid) + `skvnHoverBgGradient` (gradient). Each handler sets its own attribute and clears the other; no shared-attribute clobber. PHP render prefers gradient when present, else solid. Editor `BlockListBlock` + frontend filter both emit `has-skvn-btn-hover-bg` / `has-skvn-btn-hover-text` markers.

**Hero alignment fix (separate issue surfaced same session):** hero content was centered-by-default with left as per-block opt-in, so a left-aligned heading + un-aligned paragraph/buttons staggered. Changed hero `.skvn-slide__content` children + `.wp-block-buttons` to **default flush-left**; center/right are now the opt-in (`has-text-align-center` / `is-content-justification-center`).

**Re-verify save fix:**
- [ ] Pick a solid background hover color → it persists after reload (editor + Code editor markup shows `skvnHoverBgColor`).
- [ ] Pick a gradient → persists as `skvnHoverBgGradient`; switching solid↔gradient clears the other.
- [ ] Frontend: configured bg/gradient renders on hover.

---

## Test 3 — New Collection Card CTAs / Action Hovers (1.3.7 polish)

### Setup
- Product Collection and Post Collection (Grid + Carousel) on test page.
- Cards showing the new action elements:
  - Product: quote CTA (`.skvn-collection-card__cta`), catalog CTA (`.skvn-collection__catalog-cta`), view/custom if used.
  - Post: read-more (`.skvn-collection-card__read-more`), archive link, etc.
- Per 028 planning: hovers are bg color changes (navy → teal for CTA, ghost button, underline for links).

### Steps (DevTools)
1. Hard refresh.
2. For each CTA type:
   - Select the element (e.g. `.skvn-collection-card__cta` or the `<a>`).
   - Force `:hover`.
   - Computed: background/color changes to the planned value from 028 (e.g. teal #0D9488).
   - Styles tab: the rule from `src/collection/style.css` (e.g. `.skvn-collection-card__cta:hover`) is active.
3. Check transition: it should animate (unless reduced-motion).
4. Force reduced-motion → re-hover: no transition (instant change).
5. Test `:focus-visible` (Tab or force state): should have visible focus (not just hover).
6. Inside carousel: hover the CTA while carousel is paused on hover — CTA hover should still work independently.
7. Compare to "regular" link hover behavior (e.g. does it feel consistent with `a:hover var(--contrast)` pattern where text color changes? For CTAs it's mostly bg, but underline for read-more/archive should work).

### Pass criteria (from 028 + AGENTS + contract)
- [ ] Hovers trigger exactly as planned in 028 (bg change for CTAs, underline for links/pdf/read-more).
- [ ] Transition present on normal motion.
- [ ] `@media (prefers-reduced-motion: reduce)` removes transitions (no motion).
- [ ] `:focus-visible` has appropriate style (visible ring or color).
- [ ] No specificity issues (plugin collection rules win for these custom elements).
- [ ] Works in Grid and Carousel, desktop + mobile.
- [ ] Collection baseline CSS (no hard theme dependency).

### Evidence to report
- Screenshots: card CTA on forced :hover (computed + Styles rule).
- Reduced-motion test result.
- Notes on consistency with Gutenberg link hover feel.
- Carousel + CTA hover interaction: "CTA hover works while paused? yes/no"

---

## General / Cross Tests

### Editor Preview Hover
- In editor, select a core/button with hover colors inside a Slider slide or Collection card.
- Use DevTools inside the editor iframe (or force state).
- Confirm the outer wrapper (from BlockListBlock) has `has-skvn-button-hover` + style vars.
- Force hover on the preview button: color/bg should preview the hover state (using editor CSS selector `.has-skvn-button-hover .wp-block-button .wp-block-button__link:hover`).

### Specificity Audit (per gutenberg-block-extension-css-contract.md)
For any hovered element (core button or collection CTA):
1. Hover (force).
2. In Styles: list all matching rules for the element.
3. Identify the "theme rule mạnh nhất" or slider rule (e.g. with skvn-button--primary or skvn-slider--hero).
4. Confirm the relevant rule (feature or collection CSS) has specificity ≥ that rule.
5. No strikethrough on the desired rule.

### Other
- No console errors on hover.
- Works with reduced-motion global (OS + DevTools).
- If Core feature disabled: no `has-skvn-button-hover` class, no custom vars, falls back cleanly.
- Mobile touch: hover states don't interfere with tap.

---

## Evidence Template (report back)

```text
Page/URL: https://minhhaifishery.com/home-page/ (or test page)
Tested blocks: [list]
Core Button Hover feature enabled? yes/no + custom colors used: [describe]

Test 1 (baseline):
- Wrapper class + vars present? 
- Computed hover color matches config? 
- Rule active no strikethrough? 

Test 2 (inside Slider):
- Winning rule on hover inside slide: [paste selector]
- Computed color: 
- Matches outside / --contrast expectation? 
- Custom feature hover wins? yes/no + notes

Test 3 (collection CTAs):
- CTA hover color matches 028 plan? 
- Reduced-motion guard works? 
- Focus-visible ok?

Specificity audit passed? (plugin/feature rule ≥ theme/slider strongest) yes/no + details

Screenshots attached:
- [list with descriptions]

Console/ other notes:
```

---

**After running:** Update this doc with results or new steps. Human approves 1.3.7 hover verification before closing milestone.

This checklist directly supports the gutenberg-block-extension-css-contract (DevTools specificity, behavior verification, editor/frontend parity, reduced-motion) and the 1.3.7 scope (collection CTA polish + slider button hover compatibility for the reported stuck case).

Run after build. Use the live site for realistic slider context. Provide evidence as above.