# Task — Slider Parallax Timing Fix + view.ts Concern Split (1.3.8 patch)

**Milestone:** V1 / 1.3.8 patch (hotfix — không tốn slot roadmap mới)
**Routing:** CODE_NOW — hướng đã chốt, agent implement ngay
**Date:** 2026-07-01

---

## Context

`1.3.8 Slider Parallax` vừa ship (commit `9ddcdde`) lộ 2 bug runtime khi bật parallax:

1. **Pre-scale:** ảnh background bị `scale()` ngay khi trang load, trước bất kỳ transition nào.
2. **Stutter:** slide 2/3 (loop clones) khựng 1 nhịp khi click arrow; ~1 phút trang mới ổn định.

Điều tra codebase (`src/slider/view.ts`) xác định **root cause chung**: parallax data-attributes (`data-swiper-parallax-x/-y/-scale`) và CSS var `--skvn-parallax-inset` được inject **trước** `new Swiper()`. Hệ quả:
- Swiper parallax module đọc `-scale` và áp scale lên active slide trước khi tính progress=0 → pre-scale.
- Swiper tạo loop clones bằng `cloneNode()` bên trong `new Swiper()`, nhưng thời điểm inject chỉ chạm original slides → clone thiếu attrs → stutter khi cycle tới.

**Quyết định kiến trúc kèm theo (đã phản biện, KHÔNG làm):**
- KHÔNG tạo `src/shared/swiper-adapter.ts`. Bằng chứng: 2 consumer Swiper thật (`slider/view.ts`, `collection-view.ts`) đã chia sẻ phần chung phức tạp qua `shared/autoplay.ts` + `shared/motion.ts` (3 block dùng). Phần `new Swiper()` còn lại phân kỳ hoàn toàn (slider: parallax/effects/timed-pagination; collection: breakpoints/spaceBetween). Adapter thống nhất = wrapper pass-through, abstraction theater, regress kiến trúc. Chi tiết: phần "Rejected approaches" cuối file.
- Debt thật duy nhất còn lại: `initSlider()` ~500 dòng. Giải bằng **refactor nội bộ theo concern trong `src/slider/`**, KHÔNG bằng file `shared/` mới.

---

## Goal

1. **Fix bug (bắt buộc, ưu tiên):** parallax attrs inject SAU `new Swiper()` + `swiper.update()`, để active slide không pre-scale và loop clones nhận đủ attrs.
2. **Refactor debt (kèm theo, chỉ trong `src/slider/`):** chẻ `initSlider()` theo concern để injection point sạch và test được. KHÔNG đổi hành vi ngoài fix bug.

---

## Files allowed to change

```
wp-content/plugins/skvn-marine-blocks/src/slider/view.ts   (fix timing + concern split)
docs/decisions/slider-parallax-both-1.3.8.md               (ghi rev 1.2 — timing contract)
```

Nếu refactor cần tách helper: được tạo file mới **trong `src/slider/`** (ví dụ `src/slider/parallax.ts`) — KHÔNG tạo trong `src/shared/`.

## Files forbidden to change

```
wp-content/themes/generatepress/**
wp-content/themes/skvn-marine/**            (fix thuần plugin, không đụng theme CSS)
src/shared/**                               (KHÔNG tạo swiper-adapter; shared đã đúng)
src/collection-view.ts                      (consumer thứ 2, ngoài scope)
src/feature-showcase/**
src/slider/block.json, save.tsx, edit.tsx   (attrs đã đúng; bug thuần runtime)
modules/slider-render/slider-render.php     (PHP render đã đúng)
```

---

## Implementation — Bug Fix (Phần 1, bắt buộc)

File: `src/slider/view.ts`. Vấn đề nằm ở block `if ( shouldParallax )` (hiện lines ~462–513) chạy TRƯỚC `new Swiper()` (hiện line ~540).

### Step 1 — Biến injection thành closure hoãn thi hành

Trong block `if ( shouldParallax )`: giữ nguyên phần **compute** values (preset/advanced resolution, `axis`, `effectiveInset`...), nhưng **bọc phần side-effect** (`querySelectorAll(...).forEach` set attrs + `slider.style.setProperty('--skvn-parallax-inset', ...)`) vào một closure chưa gọi:

```typescript
let injectParallaxAttrs: (() => void) | null = null;

if ( shouldParallax ) {
    // ... GIỮ NGUYÊN toàn bộ phần compute (presetTranslate/Scale/Inset,
    //     intensity, advanced, depth, direction, translateMag, scaleVal,
    //     insetMag, includeTranslate, includeScale, effectiveInset, axis) ...

    injectParallaxAttrs = () => {
        slider
            .querySelectorAll< HTMLElement >( '.skvn-slide__bg' )
            .forEach( ( bg ) => {
                bg.removeAttribute( 'data-swiper-parallax' );   // NEW: clear generic form (stale-attr guard)
                bg.removeAttribute( 'data-swiper-parallax-x' );
                bg.removeAttribute( 'data-swiper-parallax-y' );
                bg.removeAttribute( 'data-swiper-parallax-scale' );

                if ( includeTranslate ) {
                    bg.setAttribute( `data-swiper-parallax-${ axis }`, `-${ translateMag }%` );
                }
                if ( includeScale ) {
                    bg.setAttribute( 'data-swiper-parallax-scale', String( scaleVal ) );
                }
            } );
        slider.style.setProperty( '--skvn-parallax-inset', `-${ effectiveInset }%` );
    };
}
```

Thay đổi so với hiện tại:
- Toàn bộ forEach block + setProperty → move vào `injectParallaxAttrs`.
- Thêm `bg.removeAttribute('data-swiper-parallax')` (generic form — code cũ chỉ remove `-x/-y/-scale`).
- `let injectParallaxAttrs` khai báo ngoài `if` để step 2 gọi được.

### Step 2 — Gọi inject SAU `new Swiper()`

Ngay sau `syncViewportHeight( swiper );` (hiện line ~610), trước `let pauseCoordinator`:

```typescript
if ( injectParallaxAttrs ) {
    injectParallaxAttrs();   // query ALL .skvn-slide__bg — bao gồm loop clones Swiper vừa tạo
    swiper.update();          // ép Swiper re-read attrs, recalc transform đúng cho mọi slide
}
```

### Thứ tự đúng sau fix

```
1. compute parallax values        (trước new Swiper — an toàn, chỉ tính toán)
2. new Swiper(...)                 (init + cloneNode tạo loop clones)
3. injectParallaxAttrs()           (chạm original + clone slides)
4. swiper.update()                 (progress=0 → active: translate=0/scale=1; clones có attrs đầy đủ)
```

Kết quả: active slide không pre-scale; loop clones không stutter; inset var áp sau init nên không flash.

---

## Implementation — Concern Split (Phần 2, kèm theo)

Chỉ làm SAU khi bug fix verify pass. Mục tiêu: `initSlider()` dễ đọc, injection point rõ. Chẻ theo concern, giữ nguyên hành vi:

- `parseSliderConfig()` — đã tách sẵn, giữ nguyên.
- `applyControlClasses( slider, config )` — nhấc khối apply arrow/pagination/cluster class (hiện ~lines 352–400) ra hàm riêng.
- `resolveParallax( config )` → trả về `{ inject, insetVar }` hoặc `null` — đóng gói compute + closure ở Phần 1. Có thể đặt trong `src/slider/parallax.ts`.
- `initSlider()` còn lại: orchestrate — parse → applyClasses → resolveParallax → new Swiper → inject → wire events.

Ràng buộc:
- KHÔNG đổi event wiring, cleanup, autoplay logic — chỉ di chuyển, không sửa hành vi.
- KHÔNG đụng `shared/`. Nếu tạo `src/slider/parallax.ts` thì chỉ slider import nó.
- Nếu Phần 2 rủi ro vượt thời gian, ship Phần 1 trước, Phần 2 commit riêng.

---

## Acceptance checklist

```
[ ] BUG: bật parallax, load trang — active slide KHÔNG bị scale khi đứng yên (chưa click)
[ ] BUG: click arrow tới slide 2, 3 — KHÔNG khựng/stutter; parallax mượt ngay lần đầu
[ ] BUG: DOM inspect .swiper-slide-duplicate .skvn-slide__bg — CÓ data-swiper-parallax-x (hoặc -y/-scale)
[ ] BUG: .skvn-slide__bg KHÔNG còn đồng thời data-swiper-parallax (generic) và -x
[ ] Preset mode (Advanced OFF) và Advanced mode đều đúng
[ ] Scale-only depth: không set -x/-y, inset = 0 (edge guard ẩn) — không regress
[ ] prefers-reduced-motion vẫn disable parallax hoàn toàn
[ ] slidesPerView > 1 vẫn disable parallax
[ ] Editor (edit.tsx) không đổi — vẫn stacked, badge, không parallax runtime
[ ] REFACTOR (nếu làm): view.ts hành vi identical, chỉ tổ chức lại; không đụng shared/
[ ] npm run build pass (chạy 1 lần, không retry nếu treo)
[ ] Không tạo src/shared/swiper-adapter.ts
[ ] Files sửa nằm trong "allowed", không chạm "forbidden"
[ ] Human test onsite xác nhận cả 2 bug hết (test/QA là việc Dev)
```

---

## Build command

```
wsl -d Debian -- bash -lc ". /home/shinkuro/.nvm/nvm.sh && nvm use 20 && cd /mnt/d/Github/skvn-marine/wp-content/plugins/skvn-marine-blocks && npm run build"
```

PHP lint không cần — task này không đụng file `.php`.

---

## Tension / Conflict

- **Severity: none (bug fix).** Fix timing là thay đổi tối thiểu, đúng kiến trúc.
- **Severity: low (refactor Phần 2).** Chẻ `initSlider()` có rủi ro chạm event wiring. Giảm thiểu: commit riêng khỏi bug fix, giữ hành vi identical, chỉ di chuyển code. Nếu quá giờ → hoãn Phần 2, ship Phần 1.

---

## Rejected approaches (đã phản biện — KHÔNG làm)

| Đề xuất | Lý do reject |
|---|---|
| Tạo `src/shared/swiper-adapter.ts` gom `new Swiper()` | 2 consumer thật (slider + collection-view.ts) đã share phần chung qua `shared/autoplay` + `shared/motion` (3 block dùng). Phần init còn lại phân kỳ hoàn toàn → adapter = wrapper pass-through, không giảm nợ, thêm tầng gián tiếp. |
| "Chuẩn bị adapter cho milestone 1.3.3 tương lai" | 1.3.3 (Product/Post Carousel) ĐÃ ship = `collection-view.ts`. Consumer thứ 2 không phải tương lai. Extract abstraction từ requirement chưa biết = đoán, tạo nợ mới. |
| Lazy-init IntersectionObserver + baseline audit | Khác bài toán (performance opt, không phải timing bug). Không được block bug fix. Tách task riêng nếu thật sự cần. |
| "Fix modular import / add cleanup" | Sai tiền đề: view.ts ĐÃ modular import (`swiper/modules`) và ĐÃ có cleanup (`skvnSliderCleanup` + MutationObserver + `swiper.destroy`). Debt này không tồn tại. |
| Thêm `will-change` tĩnh / `contain: paint` vào CSS | Không kiểm chứng, rủi ro visual regression (box-shadow/badge tràn). Ngoài scope timing fix. |

---

## Related

- `docs/ideations/fix-parallax-pre-scale-stutter.md` — fix plan chi tiết (scratchpad, gitignored)
- `docs/ideations/debug-parallax-stutter.md` — 4 hypotheses gốc
- `docs/decisions/slider-parallax-both-1.3.8.md` — parallax contract (cần thêm rev 1.2 timing note)
- `src/collection-view.ts` — consumer Swiper thứ 2 (bằng chứng anti-adapter)
- `src/shared/autoplay.ts`, `src/shared/motion.ts` — abstraction đúng (3 consumer thật)
