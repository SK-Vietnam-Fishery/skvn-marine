<?php
/**
 * Governed B2B search results template.
 */

get_header();

$skvn_marine_term     = get_search_query();
$skvn_marine_target   = function_exists( 'skvn_marine_get_search_target' ) ? skvn_marine_get_search_target() : 'all';
$skvn_marine_settings = function_exists( 'skvn_marine_get_header_actions' ) ? skvn_marine_get_header_actions() : array();
$skvn_marine_products = array();
$skvn_marine_articles = array();

if ( '' !== $skvn_marine_term && function_exists( 'skvn_marine_get_prioritized_search_posts' ) ) {
	if ( in_array( $skvn_marine_target, array( 'products', 'all' ), true ) && ! empty( $skvn_marine_settings['product_search_enabled'] ) ) {
		$skvn_marine_products = skvn_marine_get_prioritized_search_posts(
			'product',
			$skvn_marine_term,
			array( 'product_cat', 'product_tag' )
		);
	}

	if ( in_array( $skvn_marine_target, array( 'articles', 'all' ), true ) && ! empty( $skvn_marine_settings['post_search_enabled'] ) ) {
		$skvn_marine_articles = skvn_marine_get_prioritized_search_posts(
			'post',
			$skvn_marine_term,
			array( 'category', 'post_tag' )
		);
	}
}
?>

<main id="primary" class="site-main skvn-search-page">
	<section class="skvn-search-hero">
		<div class="skvn-container">
			<p class="skvn-section__eyebrow"><?php echo esc_html__( 'Search', 'skvn-marine' ); ?></p>
			<h1 class="skvn-section__heading">
				<?php
				printf(
					/* translators: %s: search term. */
					esc_html__( 'Results for "%s"', 'skvn-marine' ),
					esc_html( $skvn_marine_term )
				);
				?>
			</h1>
			<form class="skvn-search-page__form" action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get" role="search">
				<label class="screen-reader-text" for="skvn-search-page-keyword"><?php echo esc_html__( 'Search keyword', 'skvn-marine' ); ?></label>
				<select name="skvn_search_target" aria-label="<?php echo esc_attr__( 'Search target', 'skvn-marine' ); ?>">
					<option value="products" <?php selected( $skvn_marine_target, 'products' ); ?>><?php echo esc_html__( 'Products', 'skvn-marine' ); ?></option>
					<option value="articles" <?php selected( $skvn_marine_target, 'articles' ); ?>><?php echo esc_html__( 'Articles', 'skvn-marine' ); ?></option>
					<option value="all" <?php selected( $skvn_marine_target, 'all' ); ?>><?php echo esc_html__( 'All site', 'skvn-marine' ); ?></option>
				</select>
				<input id="skvn-search-page-keyword" type="search" name="s" value="<?php echo esc_attr( $skvn_marine_term ); ?>">
				<button class="skvn-button skvn-button--primary" type="submit"><?php echo esc_html__( 'Search', 'skvn-marine' ); ?></button>
			</form>
		</div>
	</section>

	<div class="skvn-container skvn-search-results">
		<?php if ( in_array( $skvn_marine_target, array( 'products', 'all' ), true ) && ! empty( $skvn_marine_settings['product_search_enabled'] ) ) : ?>
			<section class="skvn-search-section" aria-labelledby="skvn-search-products">
				<h2 id="skvn-search-products"><?php echo esc_html__( 'Products', 'skvn-marine' ); ?></h2>
				<?php if ( $skvn_marine_products ) : ?>
					<div class="skvn-search-grid skvn-search-grid--products">
						<?php foreach ( $skvn_marine_products as $post ) : ?>
							<?php setup_postdata( $post ); ?>
							<article class="skvn-search-card skvn-search-card--product">
								<a class="skvn-search-card__media" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
									<?php
									if ( has_post_thumbnail() ) {
										the_post_thumbnail( 'medium' );
									}
									?>
								</a>
								<div class="skvn-search-card__body">
									<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
									<p class="skvn-search-card__meta"><?php echo wp_kses_post( get_the_term_list( get_the_ID(), 'product_cat', '', ', ' ) ); ?></p>
									<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 18 ) ); ?></p>
									<div class="skvn-search-card__actions">
										<a class="skvn-button skvn-button--secondary-light" href="<?php the_permalink(); ?>"><?php echo esc_html__( 'View details', 'skvn-marine' ); ?></a>
										<?php if ( function_exists( 'skvn_marine_get_product_quote_url' ) ) : ?>
											<a class="skvn-button skvn-button--primary" href="<?php echo esc_url( skvn_marine_get_product_quote_url( get_the_ID() ) ); ?>"><?php echo esc_html__( 'Request Quote', 'skvn-marine' ); ?></a>
										<?php endif; ?>
									</div>
								</div>
							</article>
						<?php endforeach; ?>
						<?php wp_reset_postdata(); ?>
					</div>
				<?php else : ?>
					<p><?php echo esc_html__( 'No matching products found.', 'skvn-marine' ); ?></p>
				<?php endif; ?>
			</section>
		<?php endif; ?>

		<?php if ( in_array( $skvn_marine_target, array( 'articles', 'all' ), true ) && ! empty( $skvn_marine_settings['post_search_enabled'] ) ) : ?>
			<section class="skvn-search-section" aria-labelledby="skvn-search-articles">
				<h2 id="skvn-search-articles"><?php echo esc_html__( 'Related articles', 'skvn-marine' ); ?></h2>
				<?php if ( $skvn_marine_articles ) : ?>
					<div class="skvn-search-grid skvn-search-grid--articles">
						<?php foreach ( $skvn_marine_articles as $post ) : ?>
							<?php setup_postdata( $post ); ?>
							<article class="skvn-search-card">
								<div class="skvn-search-card__body">
									<p class="skvn-search-card__meta"><?php echo wp_kses_post( get_the_category_list( ', ' ) ); ?></p>
									<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
									<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 22 ) ); ?></p>
									<a class="skvn-button skvn-button--secondary-light" href="<?php the_permalink(); ?>"><?php echo esc_html__( 'Read more', 'skvn-marine' ); ?></a>
								</div>
							</article>
						<?php endforeach; ?>
						<?php wp_reset_postdata(); ?>
					</div>
				<?php else : ?>
					<p><?php echo esc_html__( 'No matching articles found.', 'skvn-marine' ); ?></p>
				<?php endif; ?>
			</section>
		<?php endif; ?>
	</div>
</main>

<?php
get_footer();
