# Demo data — Product Carousel visual lock (038)

Status: **human prep** (chưa implement CSS/Swiper).
Artifact: `.local/Seafood Export Carousels Tailwind.html` (mảng `products` ~dòng 841).
Sites: nhập **mỗi** môi trường sẽ chấm (local `http://localhost:8080` **và** onsite).

Code đọc field lúc render (`modules/collection-render/cards.php`). Không có giá trị → hàng đó **ẩn**. PDF không bắt buộc (D3).

`_skvn_certifications` nhận **cả** PHP array (WP-CLI `--format=json`) lẫn chuỗi `HACCP, EU Approved, Traceable` (Custom Fields wp-admin) — source đã parse cả hai từ 2026-10-07. Tương tự, `_skvn_moq` / `_skvn_lead_time` nhận text nhiều giá trị cách nhau bằng dấu phẩy (vd `5 MT, 10 MT`), mỗi giá trị một dòng, cap 3.

---

## 1. Làm một lần trên site (taxonomy + attribute)

### Product tags (badge overlay)

`Products → Tags` (WooCommerce `product_tag`):

```text
Wild Caught
Farmed
```

Gán **đúng một** tag / product (cột Badge dưới).

### Custom product attribute (spec chips)

Không cần global attribute. Trên từng product:

`Product data → Attributes → Add` (Custom product attribute):

```text
Name:     Specs
Values:   (4 giá trị, mỗi cái một dòng hoặc ngăn bằng |)
Visible on the product page:  YES   ← bắt buộc, không thì chip không render
Used for variations:          NO
```

Rồi **Save attributes**.

---

## 2. Custom fields (MOQ / lead / certs / PDF)

Trên màn hình Edit product: **Screen Options** → bật **Custom Fields**.

| Name | Ví dụ | Ghi chú |
|---|---|---|
| `_skvn_moq` | `5 MT` | string |
| `_skvn_lead_time` | `15–20 ngày` | string, giữ dấu `–` |
| `_skvn_certifications` | `HACCP, EU Approved, Traceable` | sau khi PHP nhận CSV; trước đó dùng WP-CLI |
| `_skvn_spec_sheet_url` | *(để trống)* | chỉ điền nếu đã upload PDF; không thì card ẩn link |

Dấu gạch dưới đầu tên là đúng.

### WP-CLI certs (local, trước khi PHP nhận CSV)

Runtime local: `/mnt/d/Github/minhhaifish`. Thay `PRODUCT_ID`.

```bash
wp post meta update PRODUCT_ID _skvn_certifications '["HACCP","EU Approved","Traceable"]' --format=json --path=/mnt/d/Github/minhhaifish --allow-root
```

---

## 3. Sáu product — copy đúng artifact

Dùng product **có sẵn** (đổi tên + field) hoặc tạo mới. Ảnh: featured image; thiếu thì Woo placeholder.

| # | Product name | Tag | Specs (4 chips) | `_skvn_certifications` | `_skvn_moq` | `_skvn_lead_time` |
|---|---|---|---|---|---|---|
| 1 | Grouper Fillet – Wild Caught | Wild Caught | `Size 200–400g` \| `Skin-on Fillet` \| `IQF` \| `Vietnam` | `HACCP, EU Approved, Traceable` | `5 MT` | `15–20 ngày` |
| 2 | Black Tiger Shrimp – Farmed | Farmed | `16/20` \| `HLSO` \| `Block Frozen` \| `Vietnam` | `ASC, BAP, HACCP` | `3 MT` | `12–18 ngày` |
| 3 | Yellowfin Tuna Loin – Wild Caught | Wild Caught | `CO-treated` \| `Loin` \| `Vacuum Pack` \| `Pacific` | `MSC, EU Approved, HACCP` | `2 MT` | `18–25 ngày` |
| 4 | Pangasius Fillet – Farmed | Farmed | `170–220g` \| `Well-trimmed` \| `IQF` \| `Mekong Delta` | `ASC, BAP, EU Approved` | `10 MT` | `10–15 ngày` |
| 5 | Vannamei Shrimp – Farmed | Farmed | `26/30` \| `PD` \| `IQF` \| `Vietnam` | `ASC, BAP, HACCP` | `5 MT` | `14–20 ngày` |
| 6 | Octopus Whole – Wild Caught | Wild Caught | `T3 (1–2kg)` \| `Whole Cleaned` \| `Block` \| `Indo-Pacific` | `HACCP, EU Approved, Traceable` | `4 MT` | `16–22 ngày` |

CTA trên card: code sẽ ra **Yêu cầu báo giá** (khớp HTML). Cần trang quote `/request-a-quote/` như contract sẵn.

---

## 4. Block trên trang test

Insert **SKVN Product Carousel**. Query: đủ 6 product trên (category chung hoặc “latest” 6 cái).

| Control | Giá trị |
|---|---|
| Eyebrow | `Export Catalog` |
| Heading | `Featured Export Products` |
| Intro | `Hải sản đông lạnh đạt chuẩn xuất khẩu, đầy đủ chứng nhận — sẵn sàng cho đơn hàng số lượng lớn.` |
| Image ratio | `1:1` |
| Spec chips | on |
| Action | quote |
| Arrows | on |
| Pagination | on |
| Autoplay | **off** |
| Catalog CTA | on nếu có URL PDF catalog; label `Tải catalog (PDF)` |
| Archive | URL shop/catalog; label `Xem toàn bộ danh mục` |

Khi implement xong: preset track `4-3-2` (peek mobile). Trước đó preset editor có thể còn `3-2-1` — không phải lỗi data.

---

## 5. Kiểm tra data trước khi bảo agent “làm B xong data”

Mở một product (frontend hoặc admin preview). Tự hỏi:

- [ ] Tên khớp bảng §3
- [ ] Tag Wild Caught hoặc Farmed
- [ ] Attributes → Specs visible, 4 giá trị
- [ ] Custom field `_skvn_moq` và `_skvn_lead_time` có chữ
- [ ] Certs: CSV hoặc JSON array (không để trống nếu muốn giống HTML)
- [ ] Lặp trên local **và** onsite nếu hai DB khác nhau

Gửi lại: URL trang test local + URL onsite. Agent bắt đầu CSS/Swiper khi bạn gõ `làm B`.
