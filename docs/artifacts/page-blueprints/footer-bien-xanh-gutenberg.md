# Bien Xanh Footer — Gutenberg Translation

Status: paste-ready artifact for the selected footer page.

## Contract

- Use core Gutenberg blocks only.
- Do not paste raw Tailwind classes, raw SVG, inline `<style>`, or external payment images into footer content.
- Theme CSS owns the visual classes:
  - `skvn-site-footer`
  - `skvn-site-footer__grid`
  - `skvn-footer-brand`
  - `skvn-footer-brand__identity`
  - `skvn-footer-brand__mark`
  - `skvn-footer-brand__name`
  - `skvn-footer-brand__tagline`
  - `skvn-icon-list`
  - `skvn-icon-list__item`
  - `skvn-footer-social`
  - `skvn-footer-social__link`
  - `skvn-footer-list`
  - `skvn-footer-contact`
  - `skvn-footer-contact__label`
  - `skvn-footer-payments`
  - `skvn-footer-payment`
  - `skvn-site-footer__bottom`
  - `skvn-site-footer__bottom-grid`

## Gutenberg Markup

```html
<!-- wp:group {"align":"full","className":"skvn-site-footer","layout":{"type":"default"}} -->
<div class="wp-block-group alignfull skvn-site-footer">
	<!-- wp:group {"className":"skvn-site-footer__grid","layout":{"type":"default"}} -->
	<div class="wp-block-group skvn-site-footer__grid">
		<!-- wp:group {"className":"skvn-footer-brand","layout":{"type":"default"}} -->
		<div class="wp-block-group skvn-footer-brand">
			<!-- wp:group {"className":"skvn-footer-brand__identity","layout":{"type":"default"}} -->
			<div class="wp-block-group skvn-footer-brand__identity">
				<!-- wp:paragraph {"className":"skvn-footer-brand__mark"} -->
				<p class="skvn-footer-brand__mark">BX</p>
				<!-- /wp:paragraph -->

				<!-- wp:group {"layout":{"type":"default"}} -->
				<div class="wp-block-group">
					<!-- wp:paragraph {"className":"skvn-footer-brand__name"} -->
					<p class="skvn-footer-brand__name">BIỂN XANH</p>
					<!-- /wp:paragraph -->

					<!-- wp:paragraph {"className":"skvn-footer-brand__tagline"} -->
					<p class="skvn-footer-brand__tagline">HẢI SẢN NINH THUẬN</p>
					<!-- /wp:paragraph -->
				</div>
				<!-- /wp:group -->
			</div>
			<!-- /wp:group -->

			<!-- wp:paragraph -->
			<p>Chuyên cung cấp hải sản tươi sống đánh bắt trong ngày từ vùng biển Ninh Thuận. Đóng thùng giữ lạnh, giao nhanh toàn quốc.</p>
			<!-- /wp:paragraph -->

			<!-- wp:group {"className":"skvn-icon-list skvn-footer-social","layout":{"type":"default"}} -->
			<div class="wp-block-group skvn-icon-list skvn-footer-social">
				<!-- wp:paragraph -->
				<p><a class="skvn-icon-list__item skvn-footer-social__link" href="#">FB</a></p>
				<!-- /wp:paragraph -->
				<!-- wp:paragraph -->
				<p><a class="skvn-icon-list__item skvn-footer-social__link" href="#">IG</a></p>
				<!-- /wp:paragraph -->
				<!-- wp:paragraph -->
				<p><a class="skvn-icon-list__item skvn-footer-social__link" href="#">YT</a></p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:group -->

		<!-- wp:group {"layout":{"type":"default"}} -->
		<div class="wp-block-group">
			<!-- wp:heading {"level":3} -->
			<h3 class="wp-block-heading">Về chúng tôi</h3>
			<!-- /wp:heading -->

			<!-- wp:list {"className":"skvn-footer-list"} -->
			<ul class="skvn-footer-list">
				<li><a href="#">Giới thiệu Biển Xanh</a></li>
				<li><a href="#">Vùng biển Ninh Thuận</a></li>
				<li><a href="#">Quy trình đánh bắt</a></li>
				<li><a href="#">Chứng nhận VSATTP</a></li>
				<li><a href="#">Tuyển dụng</a></li>
			</ul>
			<!-- /wp:list -->
		</div>
		<!-- /wp:group -->

		<!-- wp:group {"layout":{"type":"default"}} -->
		<div class="wp-block-group">
			<!-- wp:heading {"level":3} -->
			<h3 class="wp-block-heading">Hỗ trợ khách hàng</h3>
			<!-- /wp:heading -->

			<!-- wp:list {"className":"skvn-footer-list"} -->
			<ul class="skvn-footer-list">
				<li><a href="#">Chính sách giao hàng</a></li>
				<li><a href="#">Chính sách đổi trả</a></li>
				<li><a href="#">Hướng dẫn đặt hàng</a></li>
				<li><a href="#">Hướng dẫn bảo quản</a></li>
				<li><a href="#">Câu hỏi thường gặp</a></li>
			</ul>
			<!-- /wp:list -->
		</div>
		<!-- /wp:group -->

		<!-- wp:group {"layout":{"type":"default"}} -->
		<div class="wp-block-group">
			<!-- wp:heading {"level":3} -->
			<h3 class="wp-block-heading">Liên hệ</h3>
			<!-- /wp:heading -->

			<!-- wp:list {"className":"skvn-footer-contact"} -->
			<ul class="skvn-footer-contact">
				<li><span class="skvn-footer-contact__label">Địa chỉ</span>321 Đường Biển Xanh, Phan Rang - Tháp Chàm, Ninh Thuận</li>
				<li><span class="skvn-footer-contact__label">Hotline</span><a href="tel:0987666321">0987 666 321</a></li>
				<li><span class="skvn-footer-contact__label">Email</span><a href="mailto:hello@bienxanh.vn">hello@bienxanh.vn</a></li>
			</ul>
			<!-- /wp:list -->

			<!-- wp:paragraph {"className":"skvn-footer-payments"} -->
			<p class="skvn-footer-payments"><span class="skvn-footer-payment">Visa</span> <span class="skvn-footer-payment">MC</span> <span class="skvn-footer-payment">COD</span></p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->

	<!-- wp:group {"className":"skvn-site-footer__bottom","layout":{"type":"default"}} -->
	<div class="wp-block-group skvn-site-footer__bottom">
		<!-- wp:group {"className":"skvn-site-footer__bottom-grid","layout":{"type":"default"}} -->
		<div class="wp-block-group skvn-site-footer__bottom-grid">
			<!-- wp:paragraph -->
			<p>© 2025 Biển Xanh - Hải Sản Ninh Thuận. Tất cả quyền được bảo lưu.</p>
			<!-- /wp:paragraph -->
			<!-- wp:paragraph -->
			<p>Thiết kế bởi Biển Xanh Team.</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
```

## Not Translated

- Raw Tailwind utility classes.
- Inline SVG icons.
- External payment image URLs.
- Raw footer wrapper; the theme renderer owns the actual `<footer>`.
