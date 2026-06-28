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

if ( ! function_exists( 'skvn_marine_sidebar_related_ids' ) ) {
	/**
	 * Sidebar related post IDs — 2 newest + 4 pseudo-random (Option C, cached).
	 *
	 * Pool = 30 bài mới nhất (trừ bài hiện tại) → giữ 2 mới nhất → shuffle phần
	 * còn lại lấy 4 → dedupe + backfill cho đủ 6 → cache transient 8h để ổn định
	 * khi F5, không query mỗi load.
	 *
	 * @param int $post_id Current post ID.
	 * @return int[]
	 */
	function skvn_marine_sidebar_related_ids( $post_id ) {
		$key    = 'skvn_related_' . (int) $post_id;
		$cached = get_transient( $key );
		if ( is_array( $cached ) ) {
			return $cached;
		}

		$pool = get_posts( array(
			'posts_per_page'      => 30,
			'post__not_in'        => array( $post_id ),
			'orderby'             => 'date',
			'order'               => 'DESC',
			'fields'              => 'ids',
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		) );

		if ( empty( $pool ) ) {
			return array();
		}

		$newest = array_slice( $pool, 0, 2 );
		$rest   = array_slice( $pool, 2 );
		shuffle( $rest );
		$ids = array_values( array_unique( array_merge( $newest, array_slice( $rest, 0, 4 ) ) ) );

		// Backfill to 6 if dedupe shrank the set.
		foreach ( $pool as $pid ) {
			if ( count( $ids ) >= 6 ) {
				break;
			}
			if ( ! in_array( $pid, $ids, true ) ) {
				$ids[] = $pid;
			}
		}

		set_transient( $key, $ids, 8 * HOUR_IN_SECONDS );
		return $ids;
	}
}
?>

<!-- POST LAYOUT -->
<div class="skvn-single-wrap">
	<div class="skvn-single-layout">

		<!-- POST HERO — grid column 1, row 1 -->
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
									<?php the_post_thumbnail( 'large', array( 'alt' => '' ) ); ?>
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
			<?php
			$skvn_sidebar = function_exists( 'skvn_marine_blocks_get_sidebar_content' )
				? skvn_marine_blocks_get_sidebar_content()
				: null;

			if ( $skvn_sidebar ) :
				// ---- CTA island (fixed top — không vào hệ thống order) ----
				$skvn_cta = $skvn_sidebar['cta'];
				$skvn_cta_bg = 'gradient' === $skvn_cta['bg_type']
					? 'var(--wp--preset--gradient--' . $skvn_cta['bg_gradient'] . ')'
					: 'var(--wp--preset--color--' . $skvn_cta['bg_solid'] . ')';
				$skvn_cta_style = sprintf(
					'--skvn-cta-bg:%1$s;--skvn-cta-fg:var(--wp--preset--color--%2$s);--skvn-cta-btn-base:var(--wp--preset--color--%3$s);--skvn-cta-btn-text:var(--wp--preset--color--%4$s);',
					$skvn_cta_bg,
					$skvn_cta['text_color'],
					$skvn_cta['btn_base'],
					$skvn_cta['btn_text']
				);
				?>
				<div class="skvn-island skvn-island--cta" style="<?php echo esc_attr( $skvn_cta_style ); ?>">
					<p class="skvn-island__eyebrow"><?php echo esc_html( $skvn_cta['eyebrow'] ); ?></p>
					<h4 class="skvn-island__heading"><?php echo esc_html( $skvn_cta['heading'] ); ?></h4>
					<a class="skvn-island__cta" href="<?php echo esc_url( $skvn_cta['url'] ); ?>">
						<?php echo esc_html( $skvn_cta['label'] ); ?>
					</a>
					<small class="skvn-island__note"><?php echo esc_html( $skvn_cta['note'] ); ?></small>
				</div>

				<?php
				// ---- Ordered islands: TOC / Category / Related ----
				$skvn_islands = array();

				// TOC (per-post content).
				$skvn_toc_raw = function_exists( 'skvn_marine_blocks_get_post_toc' )
					? skvn_marine_blocks_get_post_toc( get_the_ID() )
					: '';
				if ( '' !== trim( $skvn_toc_raw ) ) {
					ob_start(); ?>
					<div class="skvn-island">
						<p class="skvn-island__label"><?php echo esc_html( $skvn_sidebar['toc']['label'] ); ?></p>
						<div class="skvn-island__toc">
							<?php
							// Sanitization deferred (bản tạm) — content is admin-entered raw.
							echo do_shortcode( do_blocks( $skvn_toc_raw ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							?>
						</div>
					</div>
					<?php
					$skvn_islands[] = array( 'order' => (int) $skvn_sidebar['toc']['order'], 'idx' => 0, 'html' => ob_get_clean() );
				}

				// Category list (no count, fallback primary category).
				$skvn_cat_cfg = $skvn_sidebar['category'];
				if ( $skvn_cat_cfg['enabled'] ) {
					$skvn_terms = array();
					if ( ! empty( $skvn_cat_cfg['cat_ids'] ) ) {
						$skvn_terms = get_terms( array(
							'taxonomy'   => 'category',
							'include'    => $skvn_cat_cfg['cat_ids'],
							'hide_empty' => false,
							'orderby'    => 'include',
						) );
					}
					if ( ( empty( $skvn_terms ) || is_wp_error( $skvn_terms ) ) && $skvn_first_cat ) {
						$skvn_terms = array( $skvn_first_cat );
					}
					if ( ! empty( $skvn_terms ) && ! is_wp_error( $skvn_terms ) ) {
						ob_start(); ?>
						<div class="skvn-island">
							<p class="skvn-island__label"><?php echo esc_html( $skvn_cat_cfg['label'] ); ?></p>
							<ul class="skvn-island__cat-list">
								<?php foreach ( $skvn_terms as $skvn_term ) : ?>
								<li class="skvn-island__cat-row">
									<a href="<?php echo esc_url( get_category_link( $skvn_term->term_id ) ); ?>">
										<?php echo esc_html( $skvn_term->name ); ?>
									</a>
								</li>
								<?php endforeach; ?>
							</ul>
						</div>
						<?php
						$skvn_islands[] = array( 'order' => (int) $skvn_cat_cfg['order'], 'idx' => 1, 'html' => ob_get_clean() );
					}
				}

				// Related posts (Option C, link list — copy markup category).
				$skvn_rel_cfg = $skvn_sidebar['related'];
				if ( $skvn_rel_cfg['enabled'] ) {
					$skvn_rel_ids = skvn_marine_sidebar_related_ids( get_the_ID() );
					if ( ! empty( $skvn_rel_ids ) ) {
						ob_start(); ?>
						<div class="skvn-island">
							<p class="skvn-island__label"><?php echo esc_html( $skvn_rel_cfg['label'] ); ?></p>
							<ul class="skvn-island__cat-list">
								<?php foreach ( $skvn_rel_ids as $skvn_rel_id ) : ?>
								<li class="skvn-island__cat-row">
									<a href="<?php echo esc_url( get_permalink( $skvn_rel_id ) ); ?>">
										<?php echo esc_html( get_the_title( $skvn_rel_id ) ); ?>
									</a>
								</li>
								<?php endforeach; ?>
							</ul>
						</div>
						<?php
						$skvn_islands[] = array( 'order' => (int) $skvn_rel_cfg['order'], 'idx' => 2, 'html' => ob_get_clean() );
					}
				}

				// Order resolve + tie-break (runtime, deterministic): order asc,
				// trùng số → giữ theo idx cố định (đẩy về số trống kế tiếp).
				usort( $skvn_islands, function ( $a, $b ) {
					$cmp = $a['order'] <=> $b['order'];
					return 0 !== $cmp ? $cmp : ( $a['idx'] <=> $b['idx'] );
				} );
				foreach ( $skvn_islands as $skvn_island ) {
					echo $skvn_island['html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}

			else :
				// ---- Fallback khi plugin off: markup default ----
				?>
				<div class="skvn-island skvn-island--navy">
					<p class="skvn-island__eyebrow"><?php esc_html_e( 'Tư vấn miễn phí', 'skvn-marine' ); ?></p>
					<h4 class="skvn-island__heading"><?php esc_html_e( 'Thiết bị cold chain đạt chuẩn xuất khẩu EU', 'skvn-marine' ); ?></h4>
					<a class="skvn-island__cta" href="<?php echo esc_url( home_url( '/request-a-quote/' ) ); ?>">
						<?php esc_html_e( 'Yêu cầu báo giá', 'skvn-marine' ); ?>
					</a>
					<small class="skvn-island__note"><?php esc_html_e( 'Phản hồi trong 24h làm việc', 'skvn-marine' ); ?></small>
				</div>

				<?php
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
										<?php the_post_thumbnail( 'large', array( 'alt' => '' ) ); ?>
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
			<?php endif; ?>

		</aside>

	</div><!-- .skvn-single-layout -->
</div><!-- .skvn-single-wrap -->

<?php
// comments_template() intentionally omitted — B2B context, comments hidden.
get_footer();
?>
