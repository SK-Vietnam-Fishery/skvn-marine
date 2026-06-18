<?php
/**
 * Archive template — Style C Hybrid.
 *
 * Layout: navy archive banner → featured post (first post, 2-col) →
 * main card grid + right sidebar (Quote CTA island + categories + certifications).
 *
 * No JS sidebar toggle (Option A): sidebar always present.
 * Grid sizing uses fr/% only — no hardcoded px/em layout values.
 */

get_header();

$skvn_queried = get_queried_object();

$skvn_archive_title = '';
$skvn_archive_desc  = '';

if ( is_category() ) {
	$skvn_archive_title = single_cat_title( '', false );
	$skvn_archive_desc  = category_description();
} elseif ( is_tag() ) {
	$skvn_archive_title = single_tag_title( '', false );
	$skvn_archive_desc  = tag_description();
} elseif ( is_author() ) {
	$skvn_archive_title = get_the_author();
} elseif ( is_date() ) {
	$skvn_archive_title = get_the_date( 'm/Y' );
} else {
	$skvn_archive_title = esc_html__( 'Tin tức & Kiến thức ngành', 'skvn-marine' );
	$skvn_archive_desc  = esc_html__( 'Cập nhật thông tin thủy sản, kỹ thuật bảo quản và tin tức SKVN Marine.', 'skvn-marine' );
}

// Collect all posts from the global query so we can split featured + grid.
$skvn_featured    = null;
$skvn_grid_posts  = array();
$skvn_post_count  = 0;

if ( have_posts() ) {
	while ( have_posts() ) {
		the_post();
		if ( $skvn_post_count === 0 ) {
			$skvn_featured = get_post();
		} else {
			$skvn_grid_posts[] = get_post();
		}
		$skvn_post_count++;
	}
	rewind_posts();
}
?>

<div class="skvn-archive-banner">
	<div class="skvn-archive-banner__inner">
		<p class="skvn-archive-banner__eyebrow">knowledge hub</p>
		<h1 class="skvn-archive-banner__title"><?php echo esc_html( $skvn_archive_title ); ?></h1>
		<?php if ( $skvn_archive_desc ) : ?>
			<p class="skvn-archive-banner__desc"><?php echo wp_kses_post( $skvn_archive_desc ); ?></p>
		<?php endif; ?>
	</div>
</div>

<div class="skvn-archive-wrap">

	<?php if ( $skvn_featured ) :
		$skvn_feat_cats = get_the_category( $skvn_featured->ID );
		$skvn_feat_cat  = ! empty( $skvn_feat_cats ) ? esc_html( $skvn_feat_cats[0]->name ) : '';
		$skvn_feat_date = get_the_date( 'd.m.Y', $skvn_featured->ID );
	?>
	<article class="skvn-archive-featured" aria-label="<?php esc_attr_e( 'Bài viết nổi bật', 'skvn-marine' ); ?>">
		<div class="skvn-archive-featured__thumb">
			<?php if ( has_post_thumbnail( $skvn_featured->ID ) ) : ?>
				<a href="<?php echo esc_url( get_permalink( $skvn_featured->ID ) ); ?>" tabindex="-1" aria-hidden="true">
					<?php echo get_the_post_thumbnail( $skvn_featured->ID, 'large', array( 'alt' => '' ) ); ?>
				</a>
			<?php else : ?>
				<div class="skvn-archive-featured__placeholder" aria-hidden="true"></div>
			<?php endif; ?>
		</div>
		<div class="skvn-archive-featured__body">
			<span class="skvn-archive-featured__label"><?php esc_html_e( 'Nổi bật', 'skvn-marine' ); ?></span>
			<?php if ( $skvn_feat_cat ) : ?>
				<p class="skvn-archive-featured__cat">
					<?php echo esc_html( $skvn_feat_cat ); ?>
					<?php if ( $skvn_feat_date ) : ?>
						· <?php echo esc_html( $skvn_feat_date ); ?>
					<?php endif; ?>
				</p>
			<?php endif; ?>
			<h2 class="skvn-archive-featured__title">
				<a href="<?php echo esc_url( get_permalink( $skvn_featured->ID ) ); ?>">
					<?php echo esc_html( get_the_title( $skvn_featured->ID ) ); ?>
				</a>
			</h2>
			<div class="skvn-archive-featured__excerpt">
				<?php echo wp_kses_post( wp_trim_words( get_the_excerpt( $skvn_featured->ID ), 30, '…' ) ); ?>
			</div>
			<a class="skvn-archive-featured__btn" href="<?php echo esc_url( get_permalink( $skvn_featured->ID ) ); ?>">
				<?php esc_html_e( 'Đọc bài viết', 'skvn-marine' ); ?>
				<svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
			</a>
		</div>
	</article>
	<?php endif; ?>

	<div class="skvn-archive-layout">
		<main class="skvn-archive-main" id="main">

			<?php if ( ! empty( $skvn_grid_posts ) ) : ?>
			<div class="skvn-archive-grid">
				<?php foreach ( $skvn_grid_posts as $skvn_post ) :
					$skvn_cats = get_the_category( $skvn_post->ID );
					$skvn_cat  = ! empty( $skvn_cats ) ? $skvn_cats[0] : null;
					$skvn_date = get_the_date( 'd.m.Y', $skvn_post->ID );
				?>
				<article class="skvn-post-card">
					<div class="skvn-post-card__thumb">
						<?php if ( has_post_thumbnail( $skvn_post->ID ) ) : ?>
							<a href="<?php echo esc_url( get_permalink( $skvn_post->ID ) ); ?>" tabindex="-1" aria-hidden="true">
								<?php echo get_the_post_thumbnail( $skvn_post->ID, 'medium', array( 'alt' => '' ) ); ?>
							</a>
						<?php else : ?>
							<div class="skvn-post-card__placeholder" aria-hidden="true"></div>
						<?php endif; ?>
					</div>
					<div class="skvn-post-card__body">
						<div class="skvn-post-card__meta">
							<?php if ( $skvn_cat ) : ?>
								<span class="skvn-post-card__cat">
									<a href="<?php echo esc_url( get_category_link( $skvn_cat->term_id ) ); ?>">
										<?php echo esc_html( $skvn_cat->name ); ?>
									</a>
								</span>
								<span class="skvn-post-card__dot" aria-hidden="true"></span>
							<?php endif; ?>
							<time class="skvn-post-card__date" datetime="<?php echo esc_attr( get_the_date( 'Y-m-d', $skvn_post->ID ) ); ?>">
								<?php echo esc_html( $skvn_date ); ?>
							</time>
						</div>
						<h3 class="skvn-post-card__title">
							<a href="<?php echo esc_url( get_permalink( $skvn_post->ID ) ); ?>">
								<?php echo esc_html( get_the_title( $skvn_post->ID ) ); ?>
							</a>
						</h3>
						<p class="skvn-post-card__excerpt">
							<?php echo esc_html( wp_trim_words( get_the_excerpt( $skvn_post->ID ), 18, '…' ) ); ?>
						</p>
						<div class="skvn-post-card__foot">
							<a class="skvn-post-card__link" href="<?php echo esc_url( get_permalink( $skvn_post->ID ) ); ?>">
								<?php esc_html_e( 'Đọc thêm', 'skvn-marine' ); ?> →
							</a>
						</div>
					</div>
				</article>
				<?php endforeach; ?>
			</div>
			<?php elseif ( $skvn_post_count === 0 ) : ?>
				<p class="skvn-archive-empty"><?php esc_html_e( 'Chưa có bài viết nào.', 'skvn-marine' ); ?></p>
			<?php endif; ?>

			<?php
			the_posts_pagination( array(
				'mid_size'           => 2,
				'prev_text'          => '‹',
				'next_text'          => '›',
				'screen_reader_text' => __( 'Điều hướng bài viết', 'skvn-marine' ),
				'class'              => 'skvn-archive-pagination',
			) );
			?>

		</main><!-- #main -->

		<aside class="skvn-archive-sidebar" aria-label="<?php esc_attr_e( 'Sidebar', 'skvn-marine' ); ?>">

			<!-- Quote CTA island -->
			<div class="skvn-island skvn-island--navy">
				<p class="skvn-island__eyebrow"><?php esc_html_e( 'Tư vấn miễn phí', 'skvn-marine' ); ?></p>
				<h4 class="skvn-island__heading"><?php esc_html_e( 'Nhận báo giá thiết bị theo yêu cầu', 'skvn-marine' ); ?></h4>
				<a class="skvn-island__cta skvn-button skvn-button--accent" href="<?php echo esc_url( home_url( '/request-a-quote/' ) ); ?>">
					<?php esc_html_e( 'Yêu cầu báo giá', 'skvn-marine' ); ?>
				</a>
				<small class="skvn-island__note"><?php esc_html_e( 'Phản hồi trong 24h làm việc', 'skvn-marine' ); ?></small>
			</div>

			<!-- Categories island -->
			<div class="skvn-island">
				<p class="skvn-island__label"><?php esc_html_e( 'Danh mục', 'skvn-marine' ); ?></p>
				<?php
				$skvn_sidebar_cats = get_categories( array(
					'hide_empty' => true,
					'number'     => 8,
					'orderby'    => 'count',
					'order'      => 'DESC',
				) );

				if ( ! empty( $skvn_sidebar_cats ) ) :
				?>
				<ul class="skvn-island__cat-list">
					<?php foreach ( $skvn_sidebar_cats as $skvn_sb_cat ) : ?>
					<li class="skvn-island__cat-row">
						<a href="<?php echo esc_url( get_category_link( $skvn_sb_cat->term_id ) ); ?>">
							<?php echo esc_html( $skvn_sb_cat->name ); ?>
						</a>
						<span class="skvn-island__cat-count"><?php echo absint( $skvn_sb_cat->count ); ?></span>
					</li>
					<?php endforeach; ?>
				</ul>
				<?php endif; ?>
			</div>

			<!-- Certifications island -->
			<div class="skvn-island">
				<p class="skvn-island__label"><?php esc_html_e( 'Chứng nhận', 'skvn-marine' ); ?></p>
				<ul class="skvn-island__cert-list">
					<li class="skvn-island__cert-row">
						<span class="skvn-island__cert-icon" aria-hidden="true">✓</span>
						<span><?php esc_html_e( 'VSATTP — Bộ Y Tế', 'skvn-marine' ); ?></span>
					</li>
					<li class="skvn-island__cert-row">
						<span class="skvn-island__cert-icon" aria-hidden="true">✓</span>
						<span><?php esc_html_e( 'HACCP ISO 22000', 'skvn-marine' ); ?></span>
					</li>
					<li class="skvn-island__cert-row">
						<span class="skvn-island__cert-icon" aria-hidden="true">✓</span>
						<span><?php esc_html_e( 'Cold Chain Certified', 'skvn-marine' ); ?></span>
					</li>
					<li class="skvn-island__cert-row">
						<span class="skvn-island__cert-icon" aria-hidden="true">✓</span>
						<span><?php esc_html_e( 'Bảo hành 24 tháng', 'skvn-marine' ); ?></span>
					</li>
				</ul>
			</div>

		</aside>
	</div><!-- .skvn-archive-layout -->

</div><!-- .skvn-archive-wrap -->

<?php get_footer(); ?>
