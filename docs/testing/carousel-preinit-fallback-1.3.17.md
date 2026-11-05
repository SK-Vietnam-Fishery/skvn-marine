# Carousel pre-init & fallback — V1 / 1.3.17 (gói C)

Ngày chuẩn bị: 2026-10-19. Plan: [043](../../../.context/planning/043_VER_1_3_17_CAROUSEL_ONSITE_REPAIR_PLAN.md) §6 + §8 (C-UI/C-TECH). Quyết định: human chọn làm C ngay, **chồng lên B chưa kiểm chứng** (rủi ro đã được human chấp nhận).
Status: **Đã có source; chưa build/lint, chưa onsite. Không phải PASS.**

Phạm vi: `product-collection` + `post-collection` carousel. Không đổi loop policy, autoplay coordinator, reduced-motion. Không phải skeleton (đó là gói D).

## Behavior kỳ vọng

- **Static/SSR (mặc định, JS chưa chạy):** slide đã có width đúng preset (cột C theo viewport 640/1024), nội dung thật nhìn thấy, mọi item tiếp cận được bằng cuộn ngang/keyboard (focus link sẽ cuộn tới).
- **Ready:** class `skvn-is-ready`; Swiper sở hữu geometry; gap CSS bị bỏ (`gap: 0`) để không nhân đôi với `spaceBetween`; không còn cuộn ngang thủ công.
- **Fallback:** config hỏng / markup thiếu / init throw / geometry không dùng được → `skvn-is-fallback`, quay về static, **không** skeleton/busy vô hạn, không retry vòng lặp.
- **Isolation:** 1 carousel lỗi không chặn carousel khác trên cùng trang.
- Controls (arrows/pause) `visibility: hidden` trước ready (giữ nguyên geometry, không dịch layout); dots row được reserve `min-height: 0.6rem`.
- Gap = 1 nguồn: PHP `skvn_marine_blocks_collection_carousel_gap()` → `config.spaceBetween` + `--skvn-carousel-gap` → cùng giá trị cho JS và CSS.

## Target / setup (human điền)

- Carousel URL có ≥6 item: **human điền**.
- Trang có **2 carousel** (Product + Post) để kiểm isolation: **human điền**.
- Preset kiểm: `4-2-1` (desktop 4 / tablet 2 / mobile 1) và thêm 1 carousel `3-2-1`.
- Build/commit deployed: **human điền**.

## Cases

| ID | Steps | Expected / PASS | Evidence |
|---|---|---|---|
| C-PRE | Mở trang khi JS bị chặn (DevTools block script hoặc tắt JS) | Slide có width theo preset; không chỉ 1 slide 100% bị giấu; cuộn ngang tới được item cuối; mọi link/CTA focus được | Screenshot + rects slide 1/2/last + scrollWidth carousel |
| C-GAP | Cùng trang, đo `gap` wrapper trước khi init và sau ready | Pre-init gap = 16px từ var; ready gap = 0 (Swiper spaceBetween lo spacing) — **không nhân đôi** | Computed `gap` + `spaceBetween` từ data attr |
| C-READY | Sau init, đo container/slide/columns | **Section** có `skvn-is-ready` (carousel-outer không có class nào); số cột đúng preset tại 1440/768/390; slide width > 0; **không còn scrollbar ngang** (`overflow-x: hidden`) | Class list section + outer + rects 3 viewport |
| C-CTRL | So header/footer position trước vs sau init | Arrows/pause ẩn trước ready rồi hiện; dots row không làm dịch layout | Screenshot trước/sau + `visibility` + `min-height` |
| C-FB-JSOFF | JS không chạy | Không skeleton, không busy state vô hạn; nội dung thật đọc được; mọi item reachable | Screenshot |
| C-FB-INIT | DevTools chặn `collection-view.ts` (giữ CSS) hoặc sửa tạm data-config để hỏng | Carousel vẫn hiện nội dung thật, cuộn tay được; class `skvn-is-fallback`; không loop retry | Console + class list + screenshot |
| C-ISO | Trang có 2 carousel, chặn JS của 1 cái | Carousel còn lại vẫn Ready + swipe/arrows bình thường | Screenshot + class list từng cái |
| C-RSZ | Resize 390 ↔ 768 ↔ 1440, xoay màn hình | Cột đổi đúng preset; không overflow ngang trang (`scrollWidth ≤ clientWidth`); không kẹt state | Rect + clientWidth/scrollWidth log |
| C-WARM | Reload nhiều lần (cold/warm) | Không nhân class, không nhân listener; carousel vẫn Ready; console sạch | Console + `skvnCollectionCleanup` check |

Geometry snippet (human DevTools):

```js
[...document.querySelectorAll('.skvn-collection--carousel')].map((section) => {
  const el = section.querySelector('.skvn-collection__carousel-outer');
  const carousel = section.querySelector('.skvn-collection__carousel');
  const wrapper = section.querySelector('.swiper-wrapper');
  const slides = [...section.querySelectorAll('.swiper-slide')];
  return {
    sectionClasses: section.className,          // state owner
    outerClasses: el?.className,
    spaceBetween: JSON.parse(el?.dataset.skvnCollectionCarousel || '{}').spaceBetween,
    gap: wrapper ? getComputedStyle(wrapper).gap : null,
    overflowX: carousel ? getComputedStyle(carousel).overflowX : null,
    scrollable: carousel ? carousel.scrollWidth > carousel.clientWidth : null,
    pageOverflow: document.documentElement.scrollWidth - document.documentElement.clientWidth,
    carousel: carousel?.getBoundingClientRect().toJSON(),
    slideWidth: slides[0]?.getBoundingClientRect().width,
    lastSlideLeft: slides[slides.length - 1]?.getBoundingClientRect().left,
    columns: new Set(slides.slice(0, 2).map(s => Math.round(s.getBoundingClientRect().left))).size,
  };
});
```

`scrollable` phải `false` ở Ready (không scrollbar ngang). Ở SSR/fallback `true` là hành vi có chủ đích. `pageOverflow > 0` mới là lỗi tràn trang.

## Lệnh human chạy

```bash
source /home/shinkuro/.nvm/nvm.sh && nvm use 20 && cd /mnt/d/Github/skvn-marine/wp-content/plugins/skvn-marine-blocks && npm run build && find modules/ -name "*.php" -exec php -l {} \;
```
```bash
source /home/shinkuro/.nvm/nvm.sh && nvm use 20 && cd /mnt/d/Github/skvn-marine && node tools/build-deploy-artifact.mjs && bash tools/package-plugin-zip.sh
```

## Evidence record

Mỗi case: ngày/người kiểm, URL, preset/viewport, browser, build version, expected/actual, PASS/FAIL, artifact path. Không coi source/build là PASS. Nếu lỗi phát sinh do B hoặc C ghi rõ gói, không gộp.