# Onsite — Parallax stabilize (1.3.8 Rev 1.3)

**Status:** pending human verify after build  
**Date:** 2026-07-20  
**Source fix:** PHP inset first paint + pre-init Swiper 11 attrs + duration match

## Preconditions

1. Plugin build đã chạy (xem lệnh dưới).
2. Trang demo có Slider: **Enable parallax ON**, ≥3 slides, background images, loop ON, intensity medium (hoặc advanced tương đương).
3. Hard reload (cache disabled) trên frontend.

## Build (human)

```bash
source /home/shinkuro/.nvm/nvm.sh && nvm use 20 && cd /mnt/d/Github/skvn-marine/wp-content/plugins/skvn-marine-blocks && npm run build && find modules/ -name "*.php" -exec php -l {} \;
```

## Steps + expected

| # | Action | Pass |
|---|---|---|
| 1 | Load trang, quay/video 2s đầu | Bg **không** phình/zoom đột ngột; View Source/Elements: root có `style="--skvn-parallax-inset:…"` và class `skvn-slider--parallax` **trước** JS |
| 2 | Click arrow qua hết slides ×3 vòng (loop) | Không giật/stutter ở bất kỳ slide |
| 3 | Autoplay 2 vòng loop | Mượt qua biên vòng lặp |
| 4 | DevTools: `.skvn-slide__bg` | Có `data-swiper-parallax-x` (hoặc y), optional scale, `data-swiper-parallax-duration` = speed (vd 800) |
| 5 | Toggle loop OFF, retest arrows | Vẫn mượt (so sánh baseline) |
| 6 | `prefers-reduced-motion: reduce` | Không parallax transform; inset forced 0 |
| 7 | Console | Sạch (không error slider/Swiper) |

## Evidence to report

- Video load 2s + arrow navigation (trước nếu còn clip cũ thì so)
- Screenshot Elements: root `style` + one `.skvn-slide__bg` data attrs
- Pass/fail matrix rows 1–7
- Console notes

## Fail → next

- B1 còn (scale load) → confirm PHP style có trên HTML raw (View Source), không chỉ sau JS
- B2 còn (stutter) → chạy instrumentation trong `docs/demo-prep/04-parallax-debug-protocol.md` §2; ghi loop on/off
