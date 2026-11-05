# Carousel grouped attributes — V1 / 1.3.17 (gói B)

Ngày chuẩn bị: 2026-10-16. Plan: [043](../../../.context/planning/043_VER_1_3_17_CAROUSEL_ONSITE_REPAIR_PLAN.md) §5 + §8 (B-UI/B-UX/B-TECH). Spec: [042 §11.1](../../../.context/planning/042_VER_1_3_17_CAROUSEL_VISUAL_SPEC.md#111-product-tóm-tắt-giá-trị-theo-từng-thuộc-tính).
Status: **Đã có source; chưa build/lint, chưa onsite. Không phải PASS.**

Phạm vi: **Product Carousel** của `product-collection`. Product Grid giữ flat chips cũ; Post Carousel không đổi. Query / Items to Show / alignment / quote URL / engine không đổi.

## Behavior kỳ vọng

- Mỗi nhóm có label đọc được (không bao giờ slug `pa_*`), giữ thứ tự nhóm + giá trị theo nguồn.
- `specValuesLimit` ∈ {2, 3, 4}; chỉ hiện `+N` khi nhóm còn N giá trị bị rút gọn. `+N` là text, không expand/tooltip/modal/link.
- Không dedupe trong/giữa nhóm. Giá trị rỗng bị bỏ, giá trị `"0"` được giữ.
- Chỉ đọc attribute có `visible`. Hỗ trợ taxonomy + custom attributes.
- `showSpecChips=false` → ẩn label, values và `+N`.
- Tên sản phẩm đầy đủ, CTA không bị che, equal-height giữ nguyên.

### Default / legacy — **ĐÃ CHỐT 2026-10-19: default = 3**

| Trường hợp | Hành vi | Nguồn |
|---|---|---|
| Block có `specValuesLimit` = 2/3/4 | Dùng đúng giá trị đó | allowlist PHP + TS |
| Block **thiếu setting** (legacy, hoặc carousel mới chưa lưu) | Fallback **3** | human chốt 2026-10-19 |
| Giá trị lưu sai (0, 5, "abc", null) | Fallback **3** | allowlist |
| Variation inserter Carousel | **Không** ghi sẵn `specValuesLimit` (không force-save vào content) | human chốt 2026-10-19 |

Đổi fallback = sửa 1 dòng: `SKVN_MARINE_BLOCKS_SPEC_VALUES_LIMIT_DEFAULT` (`modules/collection-render/cards.php`) và `SPEC_VALUES_LIMIT_DEFAULT` (`src/collection/constants.ts`) — phải giữ hai chỗ đồng bộ.

## Target / setup (human điền)

- Product Carousel URL (đã có từ Gate A): **human điền**.
- Product Grid URL (regression): **human điền**.
- Post Carousel URL (regression): **human điền**.
- Build/commit/dirty diff deployed: **human điền**.
- Fixture chuẩn: **Parrotfish Fillet Skinless** — Size 5, Pack 3, Freezing 1, Scientific name 1 (IQF thuộc cả Pack và Freezing).
- Fixture bổ sung: nhóm 0/1/2/3/4/5+ giá trị; giá trị dài; tên nhóm dài; nhãn trùng giữa 2 nhóm; 1 custom attribute; 1 attribute để `visible=false`; 1 giá trị `"0"`.

## Cases

| ID | Steps | Expected / PASS | Evidence gửi lại |
|---|---|---|---|
| B-L2 | Product Carousel, đặt limit **2**, xem card Parrotfish | Size: 2 chip + `+3`; Pack: 2 chip + `+1`; Freezing: 1 chip; Scientific name: 1 chip. Tổng 6 giá trị + 2 chỉ báo | Screenshot + DOM `.skvn-collection-card__spec-group` |
| B-L3 | Đặt limit **3** | Size: 3 chip + `+2`; Pack: 3 chip (đủ); Freezing/Scientific name đủ. Tổng 8 + 1 chỉ báo | Screenshot + DOM |
| B-L4 | Đặt limit **4** | Size: 4 chip + `+1`; các nhóm khác đủ. Tổng 9 + 1 chỉ báo | Screenshot + DOM |
| B-IQF | Nhìn Pack và Freezing ở cả 3 limit | `IQF` vẫn hiện ở **cả hai** nhóm (không dedupe giữa nhóm) | Screenshot |
| B-COUNTS | Card có nhóm 0/1/2/3/4/5+ giá trị | Nhóm 0 giá trị không render; 1–4 hiện đủ không `+N`; 5+ hiện `+N` đúng số | DOM + screenshot |
| B-LONG | Giá trị dài, tên nhóm dài, nhãn trùng | Giá trị wrap trong card; không tràn ngang; không bị clip; không thu nhỏ chữ; tên sản phẩm đầy đủ; CTA vẫn thấy | Screenshot + `getBoundingClientRect()` card vs page |
| B-ZERO | Nhóm chứa giá trị `0` | `0` vẫn hiện (không bị coi là rỗng) | DOM |
| B-INVISIBLE | Attribute để `visible=false` | Nhóm đó không xuất hiện | DOM |
| B-OFF | Tắt `showSpecChips` | Ẩn label + values + `+N`; title/CTA/MOQ/certs/lead time không đổi | Screenshot đối chiếu |
| B-CUSTOM | Product có custom attribute (không taxonomy) | Nhóm dùng tên attribute + giá trị options; không vỡ card nếu options rỗng | DOM + screenshot |
| B-PERSIST | 2 → 3 → 4, Save, reload editor, mở frontend | Giá trị giữ qua save/reopen; frontend khớp control; không invalid-block | Attrs + screenshot |
| B-LEGACY | Mở carousel cũ không có `specValuesLimit` | Render theo fallback 3, **không** có lỗi; control hiện 3 nhưng chưa lưu thì vẫn an toàn | DOM + attrs |
| B-VP | Desktop 1440, tablet 768, mobile 390 (cả 3 limit) | Card không tràn ngang; CTA không bị che; group rows wrap hợp lý | Screenshot + rects |
| B-REG | Product Grid + Post Carousel + carousel alignment đã nghiệm thu | Grid vẫn flat chips như cũ; Post không đổi; alignment Wide/Full/Normal giữ nguyên; Items to Show/quote URL không đổi | Paired screenshots + attrs |
| B-CONSOLE | Mở trang, xem console | Không lỗi console mới liên quan thay đổi | Console log |

Geometry snippet (human DevTools):

```js
({
  clientWidth: document.documentElement.clientWidth,
  scrollWidth: document.documentElement.scrollWidth,
  groups: [...document.querySelectorAll(
    '.skvn-collection--product.skvn-collection--carousel .skvn-collection-card'
  )].slice(0, 3).map((card) => ({
    title: card.querySelector('.skvn-collection-card__title')?.textContent?.trim(),
    ctaVisible: !!card.querySelector('.skvn-collection-card__cta')?.offsetParent,
    card: card.getBoundingClientRect().toJSON(),
    groups: [...card.querySelectorAll('.skvn-collection-card__spec-group')].map((g) => ({
      label: g.querySelector('.skvn-collection-card__spec-group-label')?.textContent?.trim(),
      values: [...g.querySelectorAll('.skvn-collection-card__spec-tag')].map((v) => v.textContent.trim()),
      more: g.querySelector('.skvn-collection-card__spec-more')?.firstChild?.textContent?.trim() || null,
    })),
  })),
})
```

## Lệnh human chạy (build + lint)

```bash
source /home/shinkuro/.nvm/nvm.sh && nvm use 20 && cd /mnt/d/Github/skvn-marine/wp-content/plugins/skvn-marine-blocks && npm run build && find modules/ -name "*.php" -exec php -l {} \;
```

Sau đó (nếu cần deploy test):

```bash
source /home/shinkuro/.nvm/nvm.sh && nvm use 20 && cd /mnt/d/Github/skvn-marine && node tools/build-deploy-artifact.mjs && bash tools/package-plugin-zip.sh
```

## Evidence record

Mỗi case: ngày/người kiểm, URL, fixture/settings, browser/viewport, source/build version, expected/actual, PASS/FAIL, đường dẫn ảnh/video/log. Defect cũ ghi riêng, không tự PASS. C/D chưa có source — không kiểm ở đây.