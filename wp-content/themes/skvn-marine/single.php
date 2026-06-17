<?php
/**
 * Single post template — Style C.
 *
 * Layout: hero image (navy gradient overlay + title) →
 * 2-col grid (post body + sidebar: Quote CTA island + related posts + categories).
 *
 * Comment section is intentionally omitted — B2B context, hidden via
 * comments_open filter in functions.php + no comments_template() call here.
 */

get_header();

if ( ! have_posts() ) {
	get_footer();
	return;
}

the_post();

$skvn_cats      = get_the_category();
$skvn_first_cat = ! empty( $skvn_cats ) ? $skvn_cats[0] : null;

// Estimate reading time.
$skvn_content   = get_the_content();
$skvn_word_count = str_word_count( wp_strip_all_tags( $skvn_content ) );
$skvn_read_time  = max( 1, (int) ceil( $skvn_word_count / 200 ) );

// Related posts: same category, exclude current.
$skvn_related_args = array(
	'posts_per_page'      => 3,
	'post__not_in'        => array( get_the_ID() ),
	'orderby'             => 'date',
	'order'               => 'DESC',
	'ignore_sticky_posts' => true,
);

if ( $skvn_first_cat ) {
	$skvn_related_args['category__in'] = array( $skvn_first_cat->term_id );
}

$skvn_related_query = new WP_Query( $skvn_related_args );
?>

<!-- POST HERO -->
<div class="skvn-post-hero">
	<?php if ( has_post_thumbnail() ) : ?>
		<?php the_post_thumbnail( 'full', array( 'class' => 'skvn-post-hero__img', 'alt' => '' ) ); ?>
		<div class="skvn-post-hero__overlay" aria-hidden="true"></div>
	<?php else : ?>
		<div class="skvn-post-hero__placeholder" aria-hidden="true"></div>
	<?php endif; ?>

	<div class="skvn-post-hero__content">
		<div class="skvn-post-hero__meta">
			<?php if ( $skvn_first_cat ) : ?>
				<a class="skvn-post-hero__cat" href="<?php echo esc_url( get_category_link( $skvn_first_cat->term_id ) ); ?>">
					<?php echo esc_html( $skvn_first_cat->name ); ?>
				</a>
			<?php endif; ?>
			<time class="skvn-post-hero__date" datetime="<?php echo esc_attr( get_the_date( 'Y-m-d' ) ); ?>">
				<?php echo esc_html( get_the_date() ); ?>
			</time>
		</div>
		<h1 class="skvn-post-hero__title"><?php the_title(); ?></h1>
	</div>
</div>

<!-- POST LAYOUT -->
<div class="skvn-single-wrap">
	<div class="skvn-single-layout">

		<main class="skvn-single-main" id="main">
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'skvn-post-body' ); ?>>

				<!-- Author row -->
				<div class="skvn-post-author">
					<div class="skvn-post-author__avatar" aria-hidden="true">
						<?php echo esc_html( mb_strtoupper( mb_substr( get_the_author(), 0, 2 ) ) ); ?>
					</div>
					<div class="skvn-post-author__info">
						<span class="skvn-post-author__name"><?php echo esc_html( get_the_author() ); ?></span>
						<span class="skvn-post-author__role"><?php echo esc_html( get_the_author_meta( 'description' ) ?: 'SKVN Marine' ); ?></span>
					</div>
					<span class="skvn-post-author__read-time">
						<?php
						printf(
							/* translators: %d: estimated reading time in minutes. */
							esc_html( _n( '%d min read', '%d min read', $skvn_read_time, 'skvn-marine' ) ),
							$skvn_read_time
						);
						?>
					</span>
				</div>

				<!-- Post content -->
				<div class="skvn-post-body__content entry-content">
					<?php the_content(); ?>
				</div>

				<!-- Post tags -->
				<?php $skvn_tags = get_the_tags(); if ( $skvn_tags ) : ?>
				<div class="skvn-post-tags" aria-label="<?php esc_attr_e( 'Tags', 'skvn-marine' ); ?>">
					<?php foreach ( $skvn_tags as $skvn_tag ) : ?>
						<a class="skvn-post-tag" href="<?php echo esc_url( get_tag_link( $skvn_tag->term_id ) ); ?>">
							<?php echo esc_html( $skvn_tag->name ); ?>
						</a>
					<?php endforeach; ?>
				</div>
				<?php endif; ?>

			</article>

			<!-- Related posts grid (below content) -->
			<?php if ( $skvn_related_query->have_posts() ) : ?>
			<div class="skvn-post-related">
				<p class="skvn-post-related__eyebrow"><?php esc_html_e( 'Bài viết liên quan', 'skvn-marine' ); ?></p>
				<div class="skvn-post-related__grid">
					<?php while ( $skvn_related_query->have_posts() ) :
						$skvn_related_query->the_post();
						$skvn_rel_cats = get_the_category();
						$skvn_rel_cat  = ! empty( $skvn_rel_cats ) ? $skvn_rel_cats[0] : null;
					?>
					<article class="skvn-post-related__card">
						<div class="skvn-post-related__thumb">
							<?php if ( has_post_thumbnail() ) : ?>
								<a href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
									<?php the_post_thumbnail( 'medium', array( 'alt' => '' ) ); ?>
								</a>
							<?php else : ?>
								<div class="skvn-post-related__placeholder" aria-hidden="true"></div>
							<?php endif; ?>
						</div>
						<div class="skvn-post-related__body">
							<?php if ( $skvn_rel_cat ) : ?>
								<p class="skvn-post-related__cat"><?php echo esc_html( $skvn_rel_cat->name ); ?></p>
							<?php endif; ?>
							<h4 class="skvn-post-related__title">
								<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							</h4>
						</div>
					</article>
					<?php endwhile;
					wp_reset_postdata(); ?>
				</div>
			</div>
			<?php endif; ?>

		</main>

		<!-- SIDEBAR -->
		<aside class="skvn-single-sidebar" aria-label="<?php esc_attr_e( 'Sidebar', 'skvn-marine' ); ?>">

			<!-- Quote CTA island -->
			<div class="skvn-island skvn-island--navy">
				<p class="skvn-island__eyebrow"><?php esc_html_e( 'Tư vấn miễn phí', 'skvn-marine' ); ?></p>
				<h4 class="skvn-island__heading"><?php esc_html_e( 'Thiết bị cold chain đạt chuẩn xuất khẩu EU', 'skvn-marine' ); ?></h4>
				<a class="skvn-island__cta" href="<?php echo esc_url( home_url( '/request-a-quote/' ) ); ?>">
					<?php esc_html_e( 'Yêu cầu báo giá', 'skvn-marine' ); ?>
				</a>
				<small class="skvn-island__note"><?php esc_html_e( 'Phản hồi trong 24h làm việc', 'skvn-marine' ); ?></small>
			</div>

			<!-- Related posts sidebar list -->
			<?php
			// Rerun query for sidebar (need up to 3 from same cat).
			$skvn_sb_related = new WP_Query( array_merge( $skvn_related_args, array( 'posts_per_page' => 3 ) ) );
			if ( $skvn_sb_related->have_posts() ) :
			?>
			<div class="skvn-island">
				<p class="skvn-island__label"><?php esc_html_e( 'Bài viết cùng chủ đề', 'skvn-marine' ); ?></p>
				<ul class="skvn-island__post-list">
					<?php while ( $skvn_sb_related->have_posts() ) :
						$skvn_sb_related->the_post();
						$skvn_sbr_cats = get_the_category();
						$skvn_sbr_cat  = ! empty( $skvn_sbr_cats ) ? $skvn_sbr_cats[0] : null;
					?>
					<li class="skvn-island__post-item">
						<?php if ( has_post_thumbnail() ) : ?>
							<div class="skvn-island__post-thumb" aria-hidden="true">
								<a href="<?php the_permalink(); ?>" tabindex="-1">
									<?php the_post_thumbnail( array( 104, 80 ), array( 'alt' => '' ) ); ?>
								</a>
							</div>
						<?php endif; ?>
						<div class="skvn-island__post-info">
							<?php if ( $skvn_sbr_cat ) : ?>
								<span class="skvn-island__post-cat"><?php echo esc_html( $skvn_sbr_cat->name ); ?></span>
							<?php endif; ?>
							<h5 class="skvn-island__post-title">
								<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							</h5>
						</div>
					</li>
					<?php endwhile;
					wp_reset_postdata(); ?>
				</ul>
			</div>
			<?php endif; ?>

			<!-- Categories island -->
			<div class="skvn-island">
				<p class="skvn-island__label"><?php esc_html_e( 'Danh mục', 'skvn-marine' ); ?></p>
				<?php
				$skvn_sb_cats = get_categories( array(
					'hide_empty' => true,
					'number'     => 6,
					'orderby'    => 'count',
					'order'      => 'DESC',
				) );
				if ( ! empty( $skvn_sb_cats ) ) :
				?>
				<ul class="skvn-island__cat-list">
					<?php foreach ( $skvn_sb_cats as $skvn_sb_cat ) : ?>
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

		</aside>

	</div><!-- .skvn-single-layout -->
</div><!-- .skvn-single-wrap -->

<?php
// comments_template() intentionally omitted — B2B context, comments hidden.
get_footer();
?>
