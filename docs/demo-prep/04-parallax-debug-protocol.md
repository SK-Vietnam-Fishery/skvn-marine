# 04 — Parallax Debug Protocol (P0)

> 2 bug: (B1) ảnh scale ngay khi load trước transition; (B2) stutter slide 2/3 khi click arrow.
> Đã fix 2 lần (`ba03f08`, `406d42d`) không hết → human confirm 2026-07-12.
> Protocol này thay thế lối "đoán → vá": mỗi hypothesis có test cô lập, làm theo thứ tự.

---

## 0. Behavior chain (Rev 1.3 — 2026-07-20 source fix)

```
PHP render:
  .skvn-slide__bg in markup
  class skvn-slider--parallax when enable && slidesPerView===1
  style="--skvn-parallax-inset:-N%" on first paint (F1)
  → view.ts parse config
  → shouldParallax = enable && !reducedMotion && slidesPerView===1
  → BEFORE new Swiper (F2+F3):
      data-swiper-parallax-{x|y}, -scale, -duration(=transitionDuration)
      inset only if PHP missing (legacy fallback)
  → new Swiper({ parallax: true, speed: transitionDuration, ... })
```

CSS: inset var; `will-change` only under `.skvn-slider--parallax`; reduced-motion forces
inset `0%` ([style.css](../../wp-content/plugins/skvn-marine-blocks/src/slider/style.css)).

Contract: `docs/decisions/swiper-architecture.md` Rev 1.3.

---

## 1. Hypothesis tree — làm theo thứ tự

### H1 — B1 "ảnh scale khi load" = layout jump của inset var (khả năng cao nhất)

**Cơ chế:** `--skvn-parallax-inset` được JS set **sau first paint**. Frame đầu bg box
có `inset: 0%`; sau init nó nhảy sang `inset: -35%` (medium) → box phình 35% mỗi cạnh
→ ảnh `object-fit: cover` trong box to hơn = nhìn như ảnh zoom đột ngột. Không phải
Swiper scale — là CSS layout jump.

**Test cô lập (5 phút, không cần build):**
DevTools trên trang có slider parallax → Elements → thêm vào `<style>`:
```css
.skvn-slider--parallax-test { --skvn-parallax-inset: -35% !important; }
```
gắn class đó vào slider root TRƯỚC khi reload có cache disabled — hoặc đơn giản hơn:
sửa tạm `style.css` build: `.skvn-slide__bg { inset: -35% }` (hardcode) → reload.
- Symptom B1 biến mất → **H1 confirmed**.

**Fix F1 (khuyến nghị):** chuyển inset ra khỏi JS — PHP render in inline style trên
slider root: `style="--skvn-parallax-inset:-35%"` (giá trị từ attribute, qua `esc_attr`,
map preset trong PHP giống presetInset của view.ts). JS xoá phần set inset (giữ cleanup).
Inset là config-static (không phụ thuộc Swiper state) → không có lý do gì đợi JS.
- Lưu ý reduced-motion: PHP không biết client preference → thêm media query
  `@media (prefers-reduced-motion: reduce) { .skvn-slider { --skvn-parallax-inset: 0% !important } }`
  hoặc JS remove var khi reducedMotion (đường nào cũng phải giữ hành vi hiện tại: reduced = không parallax).

### H2 — B2 "stutter slide 2/3" = transition desync, không phải clones

Sau khi bỏ premise clones, các ứng viên theo thứ tự:

**H2a — Thiếu `data-swiper-parallax-duration`.** Parallax module áp transition duration
riêng cho parallax elements mỗi lần `setTransition`. Nếu duration không khớp `speed`
(config.transitionDuration) trong một số nhánh (đặc biệt loopFix nhảy không-animation),
bg sẽ về đích lệch pha với slide → nhìn như giật.
- Test: set `data-swiper-parallax-duration` = `config.transitionDuration` trên mỗi bg → còn giật?

**H2b — `loopFix` jump.** Với `loop: true`, khi Swiper rearrange (nhảy index không animation),
transform của parallax element phải được set lại tức thời (duration 0). Nếu bg đang có
transition dở → snap giữa chừng = stutter. Đặc trưng: chỉ giật ở biên vòng lặp hoặc slide
ngay sau rearrange (khớp mô tả "slide 2/3").
- Test cô lập: **tắt `loop`** (editor toggle) → còn giật không? Hết giật = confirmed H2b.

**H2c — Ảnh nặng + repaint.** Bg box 135–150% kích thước slide (do inset) + `will-change`
+ cover = repaint diện tích lớn mỗi transition; GPU yếu sẽ drop frame.
- Test: thay ảnh demo bằng ảnh 200KB → còn giật không? So sánh Performance tab (FPS).

**H2d — Double transform: zoom-out effect + parallax scale.** Nếu transition style
zoom-out (media scale bằng CSS) chồng parallax scale trên cùng cây DOM → 2 transition
khác duration chạy song song.
- Test: chuyển transition = wipe, depth = translate-only → còn giật không?

### H3 — Editor re-render / MutationObserver re-init (chỉ check nếu H1+H2 không đủ)

MutationObserver re-init slider trong editor context; nếu frontend cũng có observer
chạy lại `injectParallaxAttrs` giữa chừng → attrs bị remove/re-add trong lúc transition.
- Test: log mỗi lần `injectParallaxAttrs` chạy (xem §2) — chạy >1 lần trên frontend là red flag.

---

## 2. Instrumentation (paste vào Console khi debug)

```js
const s = document.querySelector('.swiper')?.swiper;
if (!s) console.warn('No swiper instance');
['slideChange','slideChangeTransitionStart','slideChangeTransitionEnd','beforeLoopFix','loopFix']
  .forEach(ev => s.on(ev, () => console.log(ev, 'real:', s.realIndex, 'progress:', s.progress.toFixed(3))));
// Dump transform của mọi bg mỗi 300ms trong 5s:
const dump = () => document.querySelectorAll('.skvn-slide__bg').forEach((bg,i) =>
  console.log(i, getComputedStyle(bg).transform, getComputedStyle(bg).transitionDuration));
const t = setInterval(dump, 300); setTimeout(() => clearInterval(t), 5000);
```

Nhìn gì:
- `transitionDuration` của bg lúc slide change: có khớp `speed` không? Có bg nào 0s giữa animation?
- `transform` sau `loopFix`: có snap giá trị lớn tức thời không?
- B1: transform/kích thước bg ở frame đầu vs sau init (dùng Performance recording từ reload).

## 3. Test matrix (chạy sau khi có fix, trước khi Dev verify)

| # | loop | effect | intensity | depth | Kỳ vọng |
|---|---|---|---|---|---|
| 1 | on | wipe | medium | both | không giật, không edge-reveal |
| 2 | off | wipe | medium | both | như trên (so sánh với #1 → cô lập loop) |
| 3 | on | fade | subtle | both | fade mượt |
| 4 | on | zoom-out | medium | translate | không double-scale |
| 5 | on | wipe | strong | scale | scale không pre-apply lúc load |
| 6 | on | wipe | medium (advanced off) | — | preset path |
| 7 | reduced-motion ON | — | — | — | không transform, không inset |

## 4. Fix directions xếp hạng

1. **F1 — inset → PHP inline style** (fix B1, đơn giản, đúng chỗ sở hữu data).
2. **F2 — inject data-attrs TRƯỚC `new Swiper()`** trở lại (v11 không clones → an toàn),
   bỏ `swiper.update()` sau init nếu không còn cần. Đơn giản hoá timing = ít race hơn.
3. **F3 — set `data-swiper-parallax-duration`** khớp `transitionDuration` (fix H2a nếu confirmed).
4. **F4 — mitigation demo:** nếu hết tuần 1 chưa sạch → demo dùng preset **subtle** +
   loop off, hoặc tắt parallax slide đầu. Ghi rõ là mitigation, không phải fix.

Mọi fix phải cập nhật lại **invariant trong `docs/decisions/swiper-architecture.md`**
(Rev 1.3) — premise clones phải được sửa dù fix nào thắng.

## 5. Exit criteria (Dev verify trên demo site)

- [ ] Load trang: ảnh bg đứng yên, không phình/zoom trong 2s đầu (quay video 60fps nếu cần)
- [ ] Click arrow qua toàn bộ slides ×3 vòng: không giật ở bất kỳ slide nào
- [ ] Autoplay chạy 2 vòng loop: mượt qua biên vòng lặp
- [ ] Test matrix §3 pass đủ 7 dòng
- [ ] Console sạch (không error/warning từ slider)
- [ ] Reduced-motion ON: hoàn toàn không parallax
- [ ] Evidence gửi lại: video trước/sau + screenshot computed transform + kết quả matrix

## 6. Lệnh Dev

```bash
# Build + lint sau khi AI sửa
source /home/shinkuro/.nvm/nvm.sh && nvm use 20 && cd /mnt/d/Github/skvn-marine/wp-content/plugins/skvn-marine-blocks && npm run build && find modules/ -name "*.php" -exec php -l {} \;
```
