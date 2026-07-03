# Decision — Swiper Architecture

**Version:** 1.0  
**Status:** DECIDED  
**Date:** 2026-07-02  

---

## Usage Map

Toàn plugin có **2 runtime consumer** của `new Swiper()`:

| File | Block | Modules đăng ký | Đặc điểm |
|---|---|---|---|
| `src/slider/view.ts` | Hero Slider | A11y, Autoplay, EffectFade, Keyboard, Navigation, Pagination, Parallax | `slidesPerView=1`, parallax injection, fade/zoom/wipe effects, timed-fraction/timed-segments pagination, viewport-height sync, MutationObserver cho editor re-render |
| `src/collection-view.ts` | Product/Post Carousel | A11y, Autoplay, Navigation, Pagination | `slidesPerView` 1–4, breakpoints (640/1024), `spaceBetween`, pause button UI, DOMContentLoaded init |

`src/feature-showcase/view.ts` dùng `shared/autoplay` + `shared/motion` nhưng **không** dùng Swiper — nó là tab/step component CSS riêng.

---

## Shared Layer (đúng kiến trúc, đang hoạt động)

Phần **thực sự dùng chung** giữa 3 block đã được extract vào `src/shared/`:

| File | Export | Consumers |
|---|---|---|
| `shared/autoplay.ts` | `createAutoplayPauseCoordinator` | slider, collection-view, feature-showcase |
| `shared/motion.ts` | `prefersReducedMotion` | slider, collection-view, feature-showcase |
| `shared/governed-time.ts` | `normalizeGovernedTime` | slider |

**Rule áp dụng:** extract vào `shared/` khi có ≥2 call-site thật trong code, không extract sớm cho consumer giả định.

---

## Parallax Timing Contract (Rev 1.2 — 2026-07-02)

Liên quan: `src/slider/view.ts`, `docs/decisions/slider-parallax-both-1.3.8.md`.

### Bug gốc (trước fix)

`data-swiper-parallax-*` và `--skvn-parallax-inset` được inject **trước** `new Swiper()`:

```
inject attrs → new Swiper() → (Swiper tạo loop clones) → parallax module init
```

Hệ quả:
- **Pre-scale:** active slide bị `scale(parallaxScale)` ngay khi load vì parallax module chưa set progress=0→scale=1 kịp.
- **Clone stutter:** Swiper tạo loop clones bên trong `new Swiper()` — clones được tạo SAU khi inject → clone không có attrs → stutter khi cycle tới slide 2/3.

### Fix (commit sau `9ddcdde`)

Wrap injection vào closure `injectParallaxAttrs`, gọi SAU `new Swiper()`:

```
new Swiper() → (clones tạo xong) → injectParallaxAttrs() → swiper.update()
```

`swiper.update()` ép parallax module recalculate transforms đúng cho tất cả slides (original + clones).

Thêm `removeAttribute('data-swiper-parallax')` (generic form) vào cleanup block — guard stale attr từ code cũ.

### Invariant

> **Parallax attrs phải luôn được inject SAU `new Swiper()` và kết thúc bằng `swiper.update()`.**  
> Bất kỳ future code nào muốn set `data-swiper-parallax-*` trên `view.ts` đều phải theo pattern này.

---

## Rejected: `src/shared/swiper-adapter.ts`

**Đề xuất:** tạo wrapper thống nhất `new Swiper()` cho slider + collection.

**Reject vì:**

1. **Hai consumer phân kỳ hoàn toàn** ở phần init còn lại:
   - Slider cần: Parallax module, EffectFade, Keyboard, timed pagination custom render, MutationObserver, viewport-height sync.
   - Collection cần: breakpoints, `spaceBetween`, pause button UI, `renderBullet`, DOMContentLoaded init.
   - Một adapter bao cả hai = nhận config object rồi pass thẳng vào `new Swiper()` — đó không phải abstraction, là wrapper rỗng (abstraction theater).

2. **Phần dùng chung đã được extract đúng** vào `shared/autoplay` + `shared/motion`. Không còn logic nào đủ điều kiện để extract thêm.

3. **API bị lock sai thời điểm:** nếu extract trước khi consumer #2 thật xuất hiện, API có thể sai khi consumer #2 thực sự implement → nợ mới.

---

## Scope mở rộng tương lai

Nếu có block thứ 3 dùng Swiper (ví dụ: gallery, testimonials):

1. Xem xét liệu init options có trùng đủ để extract không (≥2 call-site thật, không giả định).
2. Nếu có, extract concern cụ thể (ví dụ: lazy-init bằng IntersectionObserver nếu ≥2 block cần) vào `shared/`, **không** wrap toàn bộ `new Swiper()`.
3. Không tạo `swiper-adapter.ts` chỉ để "chuẩn bị nền tảng".

---

## Related

- `docs/decisions/slider-parallax-both-1.3.8.md` — parallax attribute contract (Rev 1.1 Advanced controls)
- `docs/tasks/slider-parallax-timing-fix-1.3.8-patch.md` — task plan + rejected approaches chi tiết
- `src/shared/autoplay.ts` — pause coordinator (3 consumers)
- `src/shared/motion.ts` — reduced-motion helper (3 consumers)
