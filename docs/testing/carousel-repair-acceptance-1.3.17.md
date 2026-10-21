# Carousel repair acceptance — V1 / 1.3.17 (gói A: Width)

Ngày chuẩn bị: 2026-10-16. Plan: [043](../../../.context/planning/043_VER_1_3_17_CAROUSEL_ONSITE_REPAIR_PLAN.md) §4 + §8 (A-UI/A-UX). Spec: [042 §11](../../../.context/planning/042_VER_1_3_17_CAROUSEL_VISUAL_SPEC.md#11-human-lock-2026-10-16--attribute-summary-local-skeleton-alignment).
Status: **Đã có source gói A; build/lint PASS 2026-10-17; chờ human onsite Gate A. Không phải PASS.**

Scope lượt này: `contentWidth` (Normal/Wide/Full, default Wide chỉ cho carousel mới chèn; legacy giữ layout cũ) xuyên schema → variation → editor control → PHP renderer cho Product/Post Carousel. Không query/engine/migration/bulk change. Gói B/C/D chưa làm.

## Target / setup (human điền)

- Product carousel page URL: **human điền**.
- Post carousel page URL: **human điền**.
- Grid regression page URL (Product Grid + Post Grid hiện có): **human điền**.
- Build/commit/dirty diff deployed: **human điền** (chạy lệnh build/lint bên dưới rồi deploy bằng workflow đang dùng; không thử production khi chưa cho phép).
- Fixture: ≥6 Product + ≥6 Post published, query newest, không filter; carousel 4-2-1 (Product) / 3-2-1 (Post), autoplay off. Một legacy carousel cũ (không có `contentWidth`), một Grid cũ.
- Token baseline: theme `--skvn-content-width: 760px`, `--skvn-wide-width: 1200px` (theme.json contentSize/wideSize). Không hard-code số này vào plugin; chỉ dùng để đối chiếu rect.

## Gate A cases

| ID | Steps | Expected / PASS criteria | Evidence gửi lại |
|---|---|---|---|
| T01 attrs | 1. Chèn mới Product Carousel + Post Carousel qua inserter. 2. Mở legacy carousel cũ. 3. Đặt explicit Normal cho một carousel mới. Save + reload editor mỗi bước | Mới chèn: attrs có `"contentWidth":"wide"`. Legacy: không có key `contentWidth`, frontend section không có `alignwide/alignfull`. Explicit Normal: `"contentWidth":"normal"` tồn tại qua save/reopen, frontend không thêm class width | Saved markup attrs (copy block comment JSON), editor screenshots, frontend section class list |
| T02 geometry | Top-level Normal / Wide / Full trên cùng page; nested trong Group constrained; desktop 1440 (có scrollbar dọc) + mobile 375; chạy snippet geometry | Wide ≈ wide width theme; Normal ≈ content width; Full ≈ page canvas; header/carousel/footer cùng width. Nested giữ constraint cha (ghi rõ nếu parent chặn, không sửa page). `scrollWidth ≤ clientWidth`, không overflow/clip mới | Section rects + clientWidth/scrollWidth log, screenshots từng width, viewport |
| T03 editor-persist | Duplicate/copy carousel đã đặt Wide; đổi Grid→Carousel trên block legacy; đổi Carousel→Grid trên block Wide; reload editor | Mọi thao tác giữ `contentWidth` gốc; Grid→Carousel legacy không tự thành Wide (user chọn rõ mới đổi); không invalid-block/fatal mới | Before/after attrs, editor screenshots, console |
| T04 grid-reg | So Product Grid + Post Grid trước/sau deploy (attrs, render, count) | Grid render/count/attrs không đổi; renderer giữ behavior Grid trước sửa | Paired screenshots + attrs diff |

Geometry snippet (human DevTools, console):

```js
({
  clientWidth: document.documentElement.clientWidth,
  scrollWidth: document.documentElement.scrollWidth,
  sections: [...document.querySelectorAll('.skvn-collection--carousel')]
    .map(s => ({ cls: s.className,
      rect: s.getBoundingClientRect().toJSON() }))
})
```

Không dùng hidden/clip để che overflow nếu phát hiện; gửi boxes + screenshot cho agent. Defect cũ ghi riêng, không tự PASS. Gói B/C/D chưa có source — không kiểm phần đó ở đây.

## Lệnh human chạy (build + lint sau mỗi lượt sửa plugin)

```bash
source /home/shinkuro/.nvm/nvm.sh && nvm use 20 && cd /mnt/d/Github/skvn-marine/wp-content/plugins/skvn-marine-blocks && npm run build && find modules/ -name "*.php" -exec php -l {} \;
```

Agent không tự chạy build/lint trong chat (AGENTS.md + handoff 043 §10).

Build evidence 2026-10-17 (human cho phép agent chạy ngoại lệ):
`npm run build` PASS (webpack 5.106.2, compiled successfully), PHP lint 18/18 module files "No syntax errors detected". `node tools/build-deploy-artifact.mjs` PASS; `bash tools/package-plugin-zip.sh` → `build/skvn-marine-blocks.zip` (913,995 bytes). Verify trong zip: `modules/collection-render/` đủ 6 file; `contentWidth` có trong cả hai `build/*-collection/block.json` và không có default toàn block; `contentWidth:"wide"` xuất hiện 2 lần trong `build/index.ts.js` (đúng 2 variation Carousel); helper `skvn_marine_blocks_collection_width_class` có trong artifact `cards.php` và được gọi 1 lần trong mỗi renderer. Đây là **milestone development build**, chưa phải release.

## Evidence record

Mỗi case: ngày/người kiểm, URL, fixture/settings, browser/viewport, source/build version, expected/actual, PASS/FAIL, đường dẫn ảnh/video/log. Sau human gửi output, agent đối chiếu và sửa scoped regression; chưa đóng milestone.
