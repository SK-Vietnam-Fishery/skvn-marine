# Build + onsite test debt (1.3.x)

**Status:** waiting human build + onsite  
**Date:** 2026-07-20  

Closes the “A. Chờ Dev build + onsite test” list from `docs/ideations/pending-debts-1.3.x.md`, plus parallax stabilize after Rev 1.3 source fix.

## 0. One build for everything

```bash
source /home/shinkuro/.nvm/nvm.sh && nvm use 20 && cd /mnt/d/Github/skvn-marine/wp-content/plugins/skvn-marine-blocks && npm run build && find modules/ -name "*.php" -exec php -l {} \;
php -l /mnt/d/Github/skvn-marine/wp-content/themes/skvn-marine/functions.php
php -l /mnt/d/Github/skvn-marine/wp-content/themes/skvn-marine/single.php
```

Expect: build exit 0; every PHP lint “No syntax errors”.

Deploy/sync theme + plugin to the runtime site you use for QA (local or onsite), then hard reload.

---

## 1. Parallax stabilize (do first)

→ Full steps: `docs/testing/onsite-parallax-stabilize-1.3.8.md`

Quick gate:

- [ ] First paint: no bg zoom/jump
- [ ] Arrow ×3 loops: no stutter
- [ ] Reduced-motion: no parallax
- [ ] Evidence: video + Elements attrs

---

## 2. Blog sidebar content (1.3.12)

Refs: `docs/decisions/blog-sidebar-content-settings-1.3.12.md`

- [ ] Admin: SKVN Marine → Blog sidebar settings loads
- [ ] Single post: CTA + related/category surface per settings order
- [ ] TOC: metabox HTML per-post **or** global field fallback
- [ ] Mobile: only CTA (no full desktop sidebar stack)
- [ ] Sticky: note if CTA goes under sticky header (known debt, do not block)

---

## 3. Loading screen (1.3.13)

Refs: `docs/decisions/loading-screen-settings-1.3.13.md`

- [ ] Each type: preloader / splash / skeleton / brandbar / off
- [ ] Dismiss: hero/window/time + min-display + safety ~3.5s
- [ ] Reduced-motion: no long animation hang
- [ ] Hero LCP: no ThumbPress lazy on hero (already config-fixed)

---

## 4. Heading numbering (1.3.14)

Refs: `docs/decisions/post-heading-numbering-1.3.14.md`  
**Requires** `npm run build` (TS core-controls).

- [ ] Core Control: Post heading numbers toggle on
- [ ] Frontend: H2+ show numbers per depth/style
- [ ] Editor preview matches (or acceptable approx)
- [ ] `.no-number` heading skips
- [ ] Toggle off: no numbers, no invalid blocks
- [ ] Skip-level “1.0.1” may still happen (known debt → 1.5.3 TOC)

---

## 5. Report back (paste to agent)

```text
Build: pass/fail + any error lines
Parallax: pass/fail + matrix notes
Sidebar 1.3.12: pass/fail + URL
Loading 1.3.13: pass/fail + type tested
Heading 1.3.14: pass/fail + depth/style
Blockers: …
```

Agent continues only on fail with evidence; does not re-guess without build/onsite result.
