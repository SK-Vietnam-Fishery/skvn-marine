# Decision — Collection Carousel Engine: Keep Swiper

Status: ACTIVE
Milestone: none
Lifecycle: active
Notes: decision chưa implement; scope carousel collection thuộc roadmap `039_ROADMAP_C_...` (milestone chưa bắt đầu).
**Date:** 2026-10-07
**Decided by:** human (option A — giữ Swiper, phân tích B/C giữ lại trong ideation)
**Scope:** `skvn-marine/product-collection` + `skvn-marine/post-collection`, layout mode `carousel`

---

## Decision

Giữ **Swiper 11** (`^11.1.15` trong `package.json`) cho collection carousel. Không chuyển
scroll-snap trong milestone hiện tại.

## Version pin (2026-10-07)

- Đang khóa: **11.2.10** (`package-lock.json`).
- Latest npm: **14.3.0** (release 28/09/2026) — sau 3 major.
- Giữ 11.x cho milestone hiện tại. Nâng 12+ là task riêng kèm QA slider-parallax
  đầy đủ, vì nhiều premise trong `swiper-architecture.md` (loop rearrangement /
  `loopFix`, `data-swiper-parallax-*`) đúng cho v11 và có thể đổi ở major mới.

## Evidence (đo thật, không ước)

- `build/collection-view.ts.js`: 91.5 KB raw → **~26.6 KB gzip**.
- `build/view.ts.js` (slider): 108 KB raw → **~31.3 KB gzip**.
- Hai entry bundle Swiper **riêng**, không share instance — trang có cả slider +
  collection tải ~200 KB raw (~58 KB gzip) JS chuyển động.
- Contract hiện hành bắt carousel reuse Slider/Swiper adapter:
  `.context/MILESTONES.md` §1.3.3.
- Đã ship và đang chạy: pause coordinator dùng chung (`src/shared/autoplay.ts`),
  reduced-motion guard (`src/shared/motion.ts`), teardown idempotent
  (`src/collection-view.ts:142-160`), autoplay tắt mặc định
  (`src/product-collection/block.json:126-129`).
- Chuẩn giao diện **engine-agnostic**: `.local/product-carousel-swiper-poc-03.html` —
  cùng một mặt tiền chạy được cả hai engine, nên giữ Swiper không khóa cửa đổi
  engine sau.

## Corrected premise

Swiper 9+ loop **không tạo DOM clone** — dùng rearrangement / `loopFix`
(`docs/decisions/swiper-architecture.md:40`). Rủi ro loop còn lại là loopFix
timing (bug B2 stutter slide 2/3 bên slider), không phải clone. Mọi phân tích
cũ viết "loop clone" cho Swiper 11 đều sai premise này.

## Why not B (scroll-snap) now

- Phải viết lại ~150–200 dòng controller (autoplay + governed delay, Pause/Play,
  pause khi hover/focus/tab-ẩn, dots-sync bằng `scroll` + `rAF`, keyboard,
  teardown) và QA lại toàn bộ matrix — đúng thứ T-DEMO-2 đang muốn gộp.
- Mất loop vô hạn; dots phải đổi nghĩa slide → page; phá contract §1.3.3.
- Giá trị B2B nằm ở quote funnel (card → quote URL → CF7 → CFDB7), không nằm ở
  26.6 KB gzip — nhỏ hơn nhiều so với ảnh product trên cùng trang.

## Revisit trigger (mở lại B/C khi)

Đo onsite: nếu `collection-view.ts.js` (cached một lần) chiếm ≥5% tổng page
weight, hoặc LCP đỏ do JS. Khi đó mở bảng deliberation trong
`docs/workflows/ideation-chom-revslider-theme-tra-phi-to-gutenberg.md` §1.

## Related

- `docs/decisions/swiper-architecture.md` — usage map + shared layer + parallax contract
- `docs/decisions/skvn-dynamic-collections-1.3.3.md` — collection contract gốc
- `.local/product-carousel-swiper-poc-03.html` — visual standard (không commit)
