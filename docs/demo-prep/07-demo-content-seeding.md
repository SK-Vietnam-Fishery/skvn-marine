# 07 — Demo content seeding: 30 sản phẩm B2B

> Yêu cầu user 2026-07-12: demo cần 30 sản phẩm kiểu "một loài cá, nhiều cách bán"
> (grouper fillet skinless, skin-on, head...). File này là cách làm nhanh + khoa học.
> CSV sẵn sàng import: `assets/demo-products.csv`.

---

## 1. Nguyên tắc: sản phẩm = ma trận, không phải danh sách tên

30 sản phẩm sinh từ ma trận **Species × Cut × Form × Freezing** — đúng cách trader
seafood B2B thật phân loại hàng:

| Trục | Giá trị dùng trong demo | Lưu ở đâu (WooCommerce native) |
|---|---|---|
| Species | Grouper (12), Mahi-Mahi (6), Barramundi (6), Yellowfin Tuna (6) | Category + attribute `Species` |
| Cut | Fillet, Portion, Loin, Steak, Head, Cheek, Whole Round, WGS, Trim, Cube, Saku, Ground | Attribute `Cut` + Tag |
| Form | Skinless, Skin-on, Bone-in, Boneless | Attribute `Form` |
| Freezing | IQF, Block Frozen | Attribute `Freezing` |
| Size grade | vd 200-300g, 2-4kg | Attribute `Size Grade` |

Grouper chiếm 12/30 — đúng ý "một loài cá, nhiều cách bán": cùng con cá nhưng fillet
skinless/skin-on, portion, steak, head, cheek, loin, trim, whole... là các SKU thương mại
khác nhau.

**SKU convention** (sinh từ ma trận, unique, tra ngược được):

```
{SPECIES}-{CUT}[-{FORM}]-{FREEZING}
GRP-FLT-SKL-IQF  = Grouper Fillet Skinless IQF
GRP-HED-IQF      = Grouper Head IQF (cut không có form thì bỏ segment)
TUN-SAK-BLK      = Tuna Saku Block Frozen
```

## 2. Vì sao Simple products (không phải Variable)

- Mỗi cut có **URL + product_id riêng** → quote flow `/request-a-quote/?product_id=X`
  demo được per-product (điểm mạnh B2B của site).
- Product Collection block có 30 items thật để show grid/carousel + responsive presets.
- Variable product gộp các cut vào 1 trang → mất câu chuyện "mỗi cách bán một SKU",
  và Collection block chỉ thấy 4 sản phẩm.
- Giá: để trống (quote-based B2B) — đúng mô hình hiện tại, không cần price demo.
- Đúng invariant A3: WooCommerce native, không custom CPT/meta. MOQ/certifications chờ
  woo-catalog 1.5.0 — KHÔNG nhét vào description bằng free-text có cấu trúc giả.

## 3. Quy trình import (Dev, ~15 phút + ảnh)

1. **Local trước:** WP Admin (localhost:8080) → Products → Import →
   chọn `docs/demo-prep/assets/demo-products.csv` → importer tự map cột (format chuẩn
   WooCommerce) → Run.
2. Check: Products list đủ 30, categories Grouper/Mahi-Mahi/Barramundi/Yellowfin Tuna
   tự tạo, attributes hiển thị trong product page.
3. Mở trang có **Product Grid/Carousel block** → xác nhận render với data thật
   (image fallback sẽ hiện SKVN placeholder khi chưa có ảnh — đúng thiết kế 1.3.3).
4. Gắn ảnh (bước 4 bên dưới) → re-import nếu sửa CSV: importer **update theo SKU**,
   không tạo trùng (idempotent — tick "Update existing products" khi re-run).
5. Demo site: lặp lại import với cùng CSV.

## 4. Ảnh — 8 ảnh dùng chung, không cần 30

Ảnh theo **cut type**, share giữa species (người xem demo không soi vân cá):

| Ảnh | Dùng cho |
|---|---|
| fillet-skinless.jpg | mọi Fillet Skinless + Loin |
| fillet-skinon.jpg | mọi Fillet/Portion Skin-on |
| portion.jpg | Portion Skinless |
| steak.jpg | Steak + Saku + Cube |
| head.jpg | Head + Cheek |
| whole-fish.jpg | Whole Round + WGS |
| trim.jpg | Trim + Ground |
| block-frozen.jpg | các item Block chưa có ảnh riêng |

- Cột `Images` trong CSV để trống — gắn ảnh nhanh nhất là sửa CSV thêm URL sau khi
  upload media (importer nhận URL), hoặc gắn tay 30 lần (~20 phút).
- ALT tự fill từ attachment title (`inc/media.php`) — đặt title file ảnh tử tế khi upload.
- Nguồn ảnh: ảnh công ty có sẵn > stock (Pexels/Unsplash "fish fillet frozen") — tránh
  ảnh watermark trong demo.

## 5. Short description template (đã áp trong CSV)

```
Origin: Vietnam. {IQF|Block frozen}, 10% glazing. Packing: {pack}. Size grade: {size}.
```

B2B đủ tin, không bịa cert/MOQ (chờ 1.5.0). Description dài để trống — demo không mở tab đó.

## 6. Checklist hoàn thành

- [ ] Import local pass, 30 products hiển thị
- [ ] Product Grid + Carousel render với data thật, responsive presets OK
- [ ] Quote CTA từ product card mang đúng `product_id`
- [ ] 8 ảnh upload + gắn (hoặc chấp nhận placeholder có chủ ý)
- [ ] Import demo site pass
- [ ] Category menu/filter demo kể được chuyện "một loài cá, nhiều cách bán"
