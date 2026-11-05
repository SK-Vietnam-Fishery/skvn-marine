# Carousel local skeleton — V1 / 1.3.17 (gói D)

Ngày chuẩn bị: 2026-10-19. Plan: [043](../../../.context/planning/043_VER_1_3_17_CAROUSEL_ONSITE_REPAIR_PLAN.md) §7 + §8 (D-UI/D-TECH); spec [042 §11.2](../../../.context/planning/042_VER_1_3_17_CAROUSEL_VISUAL_SPEC.md#112-skeleton-riêng-vùng-carousel).
Status: **Đã có source; chưa build/lint, chưa onsite. Không phải PASS.**

Phạm vi: carousel của `product-collection` + `post-collection` (cùng renderer). Không đụng width Normal/Wide/Full, `+N`, query, preset. Grid không có carousel wrapper nên không có skeleton.

## Cơ chế (đọc trước khi test)

**State owner = phần tử `<section class="skvn-collection … skvn-collection--carousel">`**, KHÔNG phải `.skvn-collection__carousel-outer`.

DOM thực tế (`modules/collection-render/product-collection.php:49` + `cards.php` render carousel):

```text
<section class="skvn-collection skvn-collection--product skvn-collection--carousel skvn-collection--preset-4-2-1 skvn-collection--ratio-1-1">   ← state owner
  <header>… <button class="skvn-collection__arrow--prev"> … </header>
  <div class="skvn-collection__carousel-outer" data-skvn-collection-carousel>   ← JS đọc dataset ở đây
    <div class="skvn-collection__carousel swiper">                             ← vùng skeleton nằm trong đây
      <div class="swiper-wrapper">…</div>
      <div class="skvn-collection__skeleton" aria-hidden="true">…</div>
```

| State | Điều kiện (trên section) | Nội dung thật | Skeleton | Cuộn tay |
|---|---|---|---|---|
| SSR / JS tắt / chưa init | không có class | **hiện** | ẩn | có (fallback có chủ đích) |
| Initializing | `.skvn-is-initializing` | ẩn (chỉ `.swiper-wrapper` ẩn) | **hiện** | có |
| Ready | `.skvn-is-ready` | hiện, Swiper sở hữu | ẩn | **không** |
| Fallback / lỗi / destroy | `.skvn-is-fallback` | hiện | ẩn | có |

- Skeleton **không** phủ toàn trang: nằm **bên trong** `.skvn-collection__carousel`, `inset: 0`, không đụng footer/catalog CTA.
- **Không** phụ thuộc JS để mở nội dung thật: mặc định (không có class) là hiện nội dung thật.
- Skeleton **tĩnh**: không shimmer, không animation, `aria-hidden="true"`, không có link/CTA/focusable.
- Geometry dùng **cùng nguồn**: `--skvn-collection-columns` (preset) + `--skvn-carousel-gap`; media dùng **cùng bộ khai báo `aspect-ratio`** với ảnh thật; **1 skeleton card = 1 item thật**.
- Watchdog **5000ms** chỉ để đảm bảo fallback chạy nếu init kẹt — **không thay thế tín hiệu ready**.
- Ảnh sản phẩm tải chậm **không** giữ skeleton: readiness chỉ đo geometry, không đợi ảnh.
- **Cửa sổ initializing rất ngắn** (ready sau ~1 frame). Cold load bình thường thường **không thấy** skeleton — đó là hành vi đúng, không phải lỗi.

## Target / setup (human điền)

- Carousel URL có ≥6 item (Product), preset 4-2-1: **human điền**.
- Carousel Post (kiểm chung renderer): **human điền**.
- Trang có **2 carousel** (kiểm isolation + nhiều carousel): **human điền**.
- Product Grid trên cùng trang hoặc trang riêng: **human điền**.
- Build/commit deployed: **human điền**.

## Cases

| ID | Steps | Expected / PASS | Evidence |
|---|---|---|---|
| D-01 | DevTools → Network → **Block request URL** `collection-view.ts`, reload | **SSR/fallback**: nội dung thật hiện, cuộn ngang tay tới từng card, mọi link/CTA focus được; **skeleton KHÔNG hiện** (runtime chưa chạy nên không có state class); arrows/pause ẩn | Screenshot + DOM `.skvn-collection__skeleton` computed `display:none` |
| D-02 | Bỏ block, reload; đo ở 390 / 768 / 1440 | **Ready**: section có `skvn-is-ready`; carousel-outer **KHÔNG** có class; `overflow-x` = `hidden` (**không scrollbar ngang**); `gap` wrapper = `0`; số cột khớp preset 4/2/1; media đúng tỉ lệ | Class list **của section** + computed `overflow-x`, `gap`, rect |
| D-03 | Cold load bình thường, quay video từ lúc mở trang | Không flash card khổng lồ, không còn vùng trắng, layout không dịch. Skeleton **có thể không xuất hiện** (ready ~1 frame) — đó là hành vi đúng | Video + rect carousel trước/sau |
| D-04 | **Mô phỏng có kiểm soát**: DevTools console chạy đoạn dưới để ép state initializing | Skeleton hiện đúng vùng carousel; hàng thật ẩn nhưng **giữ nguyên chiều cao**; skeleton **không** bị ẩn theo cha; footer/catalog CTA không bị phủ; skeleton **không focusable** | Screenshot + computed `visibility` của wrapper/skeleton + tab order |
| D-05 | Chặn JS → nội dung thật; bỏ chặn nhưn hỏng config (sửa tạm `data-skvn-collection-carousel` thành JSON sai) | Init thất bại: section có `skvn-is-fallback`, **thoát loading**, skeleton ẩn, nội dung thật dùng được; không retry loop | Screenshot + class list + console |
| D-06 | Bật screen reader hoặc kiểm tab order | Skeleton **không** nhận focus, **không** đọc nội dung giả; nội dung thật đọc đúng 1 lần | Transcript / accessibility tree |
| D-07 | Trang có 2 carousel; reload + resize 390 ↔ 768 ↔ 1440 | Mỗi section có state **riêng**; không carousel nào kẹt initializing; không ảnh hưởng nhau | Class list từng section |
| D-08 | So với bản trước: width Normal/Wide/Full, thuộc tính `+N`, CTA, arrows/dots, Product Grid | Không regression; **arrows phải hiện lại ở Ready**; Grid không có skeleton | Paired screenshots + attrs |

**Mô phỏng D-04 (chạy trong console trang):**
```js
document.querySelector('.skvn-collection--carousel')
  .classList.add('skvn-is-initializing');
// quan sát, rồi:
document.querySelector('.skvn-collection--carousel')
  .classList.remove('skvn-is-initializing');
```
Đây là **mô phỏng có kiểm soát**, không phải cold-load thật. Ghi rõ trong evidence.

Geometry snippet (human DevTools):

```js
[...document.querySelectorAll('.skvn-collection--carousel')].map((section) => {
  const el = section.querySelector('.skvn-collection__carousel-outer');
  const carousel = section.querySelector('.skvn-collection__carousel');
  const wrapper = section.querySelector('.swiper-wrapper');
  const sk = section.querySelector('.skvn-collection__skeleton');
  const track = section.querySelector('.skvn-collection__skeleton-track');
  return {
    sectionClasses: section.className,           // ← state owner
    outerClasses: el?.className,                  // ← phải KHÔNG có state class
    skeletonVisible: sk ? getComputedStyle(sk).display !== 'none' : false,
    wrapperVisibility: wrapper ? getComputedStyle(wrapper).visibility : null,
    realSlides: section.querySelectorAll('.swiper-slide').length,
    skeletonCards: sk?.querySelectorAll('.skvn-collection__skeleton-card').length ?? 0,
    carouselOverflowX: carousel ? getComputedStyle(carousel).overflowX : null,
    wrapperGap: wrapper ? getComputedStyle(wrapper).gap : null,
    skeletonGap: track ? getComputedStyle(track).gap : null,
    carouselScrollable: carousel ? carousel.scrollWidth > carousel.clientWidth : null,
    pageOverflow:
      document.documentElement.scrollWidth - document.documentElement.clientWidth,
  };
});
```

**Phân biệt overflow:** `carouselScrollable = true` ở state Ready là **lỗi** (đã lỡ gap/overflow fallback). `pageOverflow > 0` là lỗi tràn ngang toàn trang. Track dài bên trong carousel ở state SSR/fallback là **bình thường**, không phải lỗi.

**Đo layout shift:** bật `Performance` → `Layout Shifts` trước khi reload. Không kết luận "không CLS" từ screenshot.

## Lệnh human chạy

```bash
source /home/shinkuro/.nvm/nvm.sh && nvm use 20 && cd /mnt/d/Github/skvn-marine/wp-content/plugins/skvn-marine-blocks && npm run build && find modules/ -name "*.php" -exec php -l {} \;
```
```bash
source /home/shinkuro/.nvm/nvm.sh && nvm use 20 && cd /mnt/d/Github/skvn-marine && node tools/build-deploy-artifact.mjs && bash tools/package-plugin-zip.sh
```

## Evidence record

Mỗi case: ngày/người kiểm, URL, preset/viewport, browser, build version, expected/actual, PASS/FAIL, artifact path. Không coi source/build là PASS. Lỗi phân biệt rõ gói B/C/D; nếu do tương tác chéo thì ghi rõ.
