# Onsite collection carousel — V1 / 1.3.17

## Product manual-only — human decision 2026-10-19

Status: **Đã có source / Chờ kiểm chứng**. Product Carousel không autoplay, không Play/Pause. Post Carousel/Hero Slider không đổi. Attributes autoplay/autoplayDelay vẫn registered để block cũ mở lại được, nhưng không có hiệu lực cho Product. Đây là thay đổi source; build/ZIP trước ngày này không bao phủ.

Target: Product/Post URLs trong Target/setup bên dưới; thêm Hero test URL do human điền. Sau build/deploy thử trên trang kiểm, có ≥6 Product/6 Post, arrows/dots on, desktop4-2-1 và mobile. Dùng một Product cũ lưu autoplay=true, delay3000 và một Product mới; tắt reduced-motion cho case so Post.

| ID | Steps | Expected / PASS | Evidence |
|---|---|---|---|
| AUTO-P | Mở Product mới và legacy true; inspect config/DOM; chờ15s; tab away/back, hover/focus rồi rời | Config autoplay=false; không Play/Pause; không tự chuyển card hoặc tự resume | URL/attrs, DOM config, video/index trước-sau, console |
| AUTO-M | Bấm arrows/dots và swipe mobile ở cả hai Product | Manual navigation còn hoạt động, sau thao tác vẫn không tự chạy | Video desktop/mobile |
| AUTO-E | Mở Product legacy trong editor, save/reload, đổi Grid rồi Carousel | Không có Autoplay/delay control ở Product; không invalid block; frontend vẫn manual-only | Editor ảnh/attrs trước-sau |
| AUTO-C | Fixture HTML cache cũ có Product class + config autoplay=true + Pause, dùng JS build mới | Runtime không bật Autoplay/coordinator và gỡ nút cũ khi init; cần purge cache để SSR cũng cập nhật | DOM sau init, video/index15s |
| AUTO-REG | Post bật/tắt autoplay và Hero hiện có trên cùng trang với Product | Post controls/delay/Pause và autoplay giữ hành vi cũ; Hero không đổi; Product vẫn đứng yên | Settings, video, console |

Human gửi build output và evidence từng case. Chưa có runtime PASS; agent không chạy build/lint lượt này. Cache HTML/JS cần được refresh sau deploy; không coi source mới đã xuất hiện onsite trước khi kiểm build.

Ngày chuẩn bị: 2026-10-15. Status: **Chờ kiểm chứng**. Scope đợt source đầu: Product full-title wrapping và reduced-motion CSS; G5 native query → cards → carousel basic smoke. Không phải full technical QA1.3.18.

## Target/setup

- Product page URL: **human điền**.
- Post page URL: **human điền**.
- Build/commit/dirty diff deployed: **human điền**, không suy ra version từ current milestone.
- Human chạy build/lint bên dưới, deploy bằng workflow đang dùng. Không thử trên production nếu chưa được human cho phép.
- Woo active; ≥6 simple published products với tên ngắn, tên nhiều dòng, một mã liền ít nhất60 ký tự và ảnh bình thường; ≥6 published posts. Không cần cert/MOQ/lead time/custom fields.
- Hai collection dùng query newest, không taxonomy filters, itemsToShow6, carousel4-2-1, autoplay off, arrows/dots on, equalHeight on. Tạo duplicate Grid để so shared style. Giữ một legacy Product ratio4:3 và Post16:9.
- Không cần thiết kế/chọn ba media modes mới. G-MEDIA (persistence/legacy) vẫn riêng, chưa quyết.

## Exact cases

| ID | Steps | Expected / PASS criteria | Evidence gửi lại |
|---|---|---|---|
| G5-P | Mở Product URL; đối chiếu6 sản phẩm query newest với cards; bấm next/prev và swipe mobile | Query ra đúng dữ liệu đơn giản; cards hiện; carousel di chuyển, không fatal/console error mới. Không cần dữ liệu phức tạp | URL,6IDs/titles, ảnh/video chuyển slide, console |
| G5-S | Lặp với6 Post trên Post URL | Native Post results thành cards và carousel hoạt động | IDs/URL/video |
| UI-T1 | Product carousel desktop1440 và mobile375/320; so tên ngắn/dài/mã60 ký tự | Toàn bộ tên đọc được, wrap không ellipsis/clamp; không tràn card/page; không đổi font/màu | Screenshot + computed white-space/overflow-wrap/display, viewport |
| UI-T2 | Cùng hàng có title khác độ dài; equalHeight on; inspect CTA; lặp resize640/1024 | CTA giữ alignment nhờ flex/margin-auto hiện có; title đầy đủ. Nếu lệch ghi FAIL, chưa claim đã sửa alignment runtime | Ảnh hàng card + CTA rect top/bottom, content optional có/không |
| UI-R1 | OS/DevTools reduced-motion=no-preference; hover card rồi bật reduce; hover lại, mousedown CTA | Reduce: card/image/CTA transform none; card/image/arrows/dots transition none. Normal giữ motion source cũ. Không kết luận autoplay state từ test CSS này | Video hai states + computed transform/transition |
| UI-R2 | Khi reduce bật bấm arrows/dots/swipe | Vẫn chuyển được slide; không lộ/clip hình mới do CSS diff. Runtime autoplay/keyboard full test1.3.18 | Video + console |
| REG-1 | So Post title và Product Grid trước/sau, legacy ratio4:3, Post16:9 | Rule full title mới chỉ Product Carousel; saved ratio không đổi; shared content không regression | Paired images, attrs không đổi |
| REG-2 | Mở editor existing content và reload; tab qua frontend links; desktop có scrollbar dọc | Không invalid-block/fatal mới; links/focus hiện có không bị che; scrollWidth≤clientWidth | Editor/frontend screenshots, console, geometry |

Geometry snippet (human DevTools):
```js
({
  clientWidth: document.documentElement.clientWidth,
  scrollWidth: document.documentElement.scrollWidth,
  cards: [...document.querySelectorAll('.skvn-collection--product.skvn-collection--carousel .skvn-collection-card')]
    .map(card => ({ card: card.getBoundingClientRect().toJSON(),
      title: card.querySelector('.skvn-collection-card__title')?.getBoundingClientRect().toJSON(),
      cta: card.querySelector('.skvn-collection-card__cta')?.getBoundingClientRect().toJSON() }))
})
```

Không dùng hidden/clip để che overflow nếu phát hiện; gửi bounding boxes + screenshot cho agent. Thiếu local page không phải source failure. Defect cũ ghi riêng, không tự PASS hoặc quy tất cả cho diff mới.

## Lệnh human chạy

```bash
source /home/shinkuro/.nvm/nvm.sh && nvm use 20 && cd /mnt/d/Github/skvn-marine/wp-content/plugins/skvn-marine-blocks && npm run build && find modules/ -name "*.php" -exec php -l {} \;
```

Agent đã chạy build/lint trong lượt chuẩn bị 2026-10-15 (human cho phép riêng): `npm run build` PASS, PHP lint 18 module files + functions.php + plugin bootstrap PASS, 0 errors, metadata bump 1.3.17, ZIP tại `build/skvn-marine-blocks.zip` (+ `build/skvn-marine.zip`). Không block registration hoặc require/include thay đổi ở lượt source này; ZIP đã gồm `build/` + `modules/` đầy đủ.

## Evidence record

Mỗi case: ngày/người kiểm, URL, fixtureIDs/settings, browser/viewport/OS motion setting, source/build version, expected/actual, PASS/FAIL và đường dẫn ảnh/video/log. Sau human gửi output, agent đối chiếu và sửa scoped regression; chưa đóng milestone.

Full UI spec/G1–G4 và G-MEDIA còn pending xem [042](../../.context/planning/042_VER_1_3_17_CAROUSEL_VISUAL_SPEC.md). File này chưa là evidence PASS cho toàn bộ milestone.

## Product Items to show tối đa 20 — 2026-10-16

Human yêu cầu điều chỉnh hẹp trong current 1.3.17: tăng giới hạn Product lên20, không đổi default3 hoặc số cột/preset. Áp dụng Product Grid/Carousel cùng query owner. Post giữ editor max10 Carousel/9 Grid, backend10 và preview5. Product editor/query backend/approximate preview cùng cap20. Preview vẫn approximate về semantics lọc/order như trước; lượt này chỉ đổi count limit.

Status: **Đã có source; chờ build/lint và onsite**. Kết quả build/ZIP ngày2026-10-15 ở trên không chứng minh diff mới này; cần human build lại bằng lệnh trong file trước deploy/test. Agent không chạy build/lint trong lượt2026-10-16.

Fixture: ít nhất21 Product published phù hợp query newest/no filters; thêm một nhóm chỉ có7 Product để kiểm ít kết quả. Không sửa dữ liệu production khi chưa được human cho phép.

| ID | Steps | Expected / PASS | Evidence |
|---|---|---|---|
| COUNT-20 | Chọn Product Carousel; đặt Items to show20; Save; reload editor; mở frontend | Editor giữ20; preview request per_page20; frontend tối đa20 Product đúng query; preset4-2-1 vẫn4/2/1 card mỗi viewport | Screenshot control sau reload, Network request/count, IDs/card count |
| COUNT-GRID | Đổi Product sang Grid, giữ20; Save/reload |20 items lưu được và render; không ép20 cột | Attributes/screenshot/Grid count |
| COUNT-BOUND | Trên bản thử, nhập giá trị vượt20 bằng control nếu cho phép; kiểm backend với stored attr21 | Control tối đa20; backend clamp20 ngay cả saved attr vượt giới hạn | Saved attr/request + rendered count |
| COUNT-DEFAULT | Chèn mới Product không chỉnh count; mở content cũ đang3/6/10 | Default vẫn3; các số đã lưu không tự đổi20 | Attributes/control/card count |
| COUNT-FEWER | Query nhóm có7 Product, Items to show20 | Chỉ7 kết quả, không chèn dummy/duplicate để đủ20 | IDs/card count |
| COUNT-POST | Mở Post Carousel/Grid, thử tăng count | Max vẫn10/9 như trước; không ảnh hưởng Post query/preview | Screenshot control và request |

Build output + các case gửi lại để agent đối chiếu. Không claim PASS chỉ từ source diff; không thay CSS hoặc sửa overlap/attributes UI trong lượt này.
