<?php
/**
 * Title: SKVN Site Footer
 * Slug: skvn-marine/site-footer
 * Categories: skvn-marine
 */
?>
<!-- wp:group {"align":"full","className":"skvn-site-footer","layout":{"type":"default"}} -->
<div class="wp-block-group alignfull skvn-site-footer">
	<!-- wp:group {"className":"skvn-site-footer__grid","layout":{"type":"default"}} -->
	<div class="wp-block-group skvn-site-footer__grid">
		<!-- wp:group {"className":"skvn-footer-brand","layout":{"type":"default"}} -->
		<div class="wp-block-group skvn-footer-brand">
			<!-- wp:group {"className":"skvn-footer-brand__identity","layout":{"type":"default"}} -->
			<div class="wp-block-group skvn-footer-brand__identity">
				<!-- wp:paragraph {"className":"skvn-footer-brand__mark"} -->
				<p class="skvn-footer-brand__mark"><?php echo esc_html__( 'SK', 'skvn-marine' ); ?></p>
				<!-- /wp:paragraph -->

				<!-- wp:group {"layout":{"type":"default"}} -->
				<div class="wp-block-group">
					<!-- wp:paragraph {"className":"skvn-footer-brand__name"} -->
					<p class="skvn-footer-brand__name"><?php echo esc_html__( 'SKVN Marine', 'skvn-marine' ); ?></p>
					<!-- /wp:paragraph -->

					<!-- wp:paragraph {"className":"skvn-footer-brand__tagline"} -->
					<p class="skvn-footer-brand__tagline"><?php echo esc_html__( 'Seafood Export', 'skvn-marine' ); ?></p>
					<!-- /wp:paragraph -->
				</div>
				<!-- /wp:group -->
			</div>
			<!-- /wp:group -->

			<!-- wp:paragraph -->
			<p><?php echo esc_html__( 'B2B seafood supply, cold-chain handling, and export-ready product support from Viet Nam.', 'skvn-marine' ); ?></p>
			<!-- /wp:paragraph -->

			<!-- wp:group {"className":"skvn-icon-list skvn-footer-social","layout":{"type":"default"}} -->
			<div class="wp-block-group skvn-icon-list skvn-footer-social">
				<!-- wp:paragraph -->
				<p><a class="skvn-icon-list__item skvn-footer-social__link" href="#"><?php echo esc_html__( 'FB', 'skvn-marine' ); ?></a></p>
				<!-- /wp:paragraph -->
				<!-- wp:paragraph -->
				<p><a class="skvn-icon-list__item skvn-footer-social__link" href="#"><?php echo esc_html__( 'IN', 'skvn-marine' ); ?></a></p>
				<!-- /wp:paragraph -->
				<!-- wp:paragraph -->
				<p><a class="skvn-icon-list__item skvn-footer-social__link" href="#"><?php echo esc_html__( 'YT', 'skvn-marine' ); ?></a></p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:group -->

		<!-- wp:group {"layout":{"type":"default"}} -->
		<div class="wp-block-group">
			<!-- wp:heading {"level":3} -->
			<h3><?php echo esc_html__( 'Company', 'skvn-marine' ); ?></h3>
			<!-- /wp:heading -->

			<!-- wp:list {"className":"skvn-footer-list"} -->
			<ul class="skvn-footer-list">
				<li><a href="#"><?php echo esc_html__( 'About SKVN Marine', 'skvn-marine' ); ?></a></li>
				<li><a href="#"><?php echo esc_html__( 'Source regions', 'skvn-marine' ); ?></a></li>
				<li><a href="#"><?php echo esc_html__( 'Cold-chain process', 'skvn-marine' ); ?></a></li>
				<li><a href="#"><?php echo esc_html__( 'Certifications', 'skvn-marine' ); ?></a></li>
				<li><a href="#"><?php echo esc_html__( 'Careers', 'skvn-marine' ); ?></a></li>
			</ul>
			<!-- /wp:list -->
		</div>
		<!-- /wp:group -->

		<!-- wp:group {"layout":{"type":"default"}} -->
		<div class="wp-block-group">
			<!-- wp:heading {"level":3} -->
			<h3><?php echo esc_html__( 'Buyer Support', 'skvn-marine' ); ?></h3>
			<!-- /wp:heading -->

			<!-- wp:list {"className":"skvn-footer-list"} -->
			<ul class="skvn-footer-list">
				<li><a href="#"><?php echo esc_html__( 'Shipping policy', 'skvn-marine' ); ?></a></li>
				<li><a href="#"><?php echo esc_html__( 'Return policy', 'skvn-marine' ); ?></a></li>
				<li><a href="#"><?php echo esc_html__( 'Ordering guide', 'skvn-marine' ); ?></a></li>
				<li><a href="#"><?php echo esc_html__( 'Storage guide', 'skvn-marine' ); ?></a></li>
				<li><a href="#"><?php echo esc_html__( 'Frequently asked questions', 'skvn-marine' ); ?></a></li>
			</ul>
			<!-- /wp:list -->
		</div>
		<!-- /wp:group -->

		<!-- wp:group {"layout":{"type":"default"}} -->
		<div class="wp-block-group">
			<!-- wp:heading {"level":3} -->
			<h3><?php echo esc_html__( 'Contact', 'skvn-marine' ); ?></h3>
			<!-- /wp:heading -->

			<!-- wp:list {"className":"skvn-footer-contact"} -->
			<ul class="skvn-footer-contact">
				<li><span class="skvn-footer-contact__label"><?php echo esc_html__( 'Address', 'skvn-marine' ); ?></span><?php echo esc_html__( 'Viet Nam seafood sourcing office', 'skvn-marine' ); ?></li>
				<li><span class="skvn-footer-contact__label"><?php echo esc_html__( 'Hotline', 'skvn-marine' ); ?></span><a href="tel:+84000000000"><?php echo esc_html__( '+84 000 000 000', 'skvn-marine' ); ?></a></li>
				<li><span class="skvn-footer-contact__label"><?php echo esc_html__( 'Email', 'skvn-marine' ); ?></span><a href="mailto:sales@example.com"><?php echo esc_html__( 'sales@example.com', 'skvn-marine' ); ?></a></li>
			</ul>
			<!-- /wp:list -->

			<!-- wp:paragraph {"className":"skvn-footer-payments"} -->
			<p class="skvn-footer-payments"><span class="skvn-footer-payment"><?php echo esc_html__( 'Visa', 'skvn-marine' ); ?></span> <span class="skvn-footer-payment"><?php echo esc_html__( 'MC', 'skvn-marine' ); ?></span> <span class="skvn-footer-payment"><?php echo esc_html__( 'COD', 'skvn-marine' ); ?></span></p>
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
			<p><?php echo esc_html__( '© 2026 SKVN Marine. All rights reserved.', 'skvn-marine' ); ?></p>
			<!-- /wp:paragraph -->
			<!-- wp:paragraph -->
			<p><?php echo esc_html__( 'Designed by SKVN Team.', 'skvn-marine' ); ?></p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
