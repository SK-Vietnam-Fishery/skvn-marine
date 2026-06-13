<?php
/**
 * Header actions and governed search helpers for SKVN Marine.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const SKVN_MARINE_HEADER_ACTIONS_OPTION = 'skvn_header_actions';

add_action( 'generate_after_header_content', 'skvn_marine_render_header_actions', 20 );

// Bridge GP → SKVN hook, chỉ tồn tại khi GP còn
if (defined('GENERATE_VERSION')){
	add_action('generate_after_header_content', function(){
		do_action('skvn_marine_after_header');
	}, 20);
}
/**
 * Get default header action settings.
 *
 * @return array<string,mixed>
 */
function skvn_marine_get_default_header_actions() {
	return array(
		'enabled'                => false,
		'product_search_enabled' => false,
		'post_search_enabled'    => false,
		'default_search_target'  => 'products',
		'contact_enabled'        => false,
		'contact_label'          => __( 'Contact', 'skvn-marine' ),
		'contact_url'            => home_url( '/contact/' ),
		'quote_enabled'          => false,
		'quote_label'            => __( 'Request Quote', 'skvn-marine' ),
		'quote_url'              => home_url( '/request-a-quote/' ),
		'layout'                 => 'compact',
	);
}

/**
 * Sanitize header action settings.
 *
 * @param mixed $value Raw option value.
 * @return array<string,mixed>
 */
function skvn_marine_sanitize_header_actions( $value ) {
	$defaults = skvn_marine_get_default_header_actions();
	$value    = is_array( $value ) ? wp_unslash( $value ) : array();

	$settings = array(
		'enabled'                => ! empty( $value['enabled'] ),
		'product_search_enabled' => ! empty( $value['product_search_enabled'] ),
		'post_search_enabled'    => ! empty( $value['post_search_enabled'] ),
		'contact_enabled'        => ! empty( $value['contact_enabled'] ),
		'quote_enabled'          => ! empty( $value['quote_enabled'] ),
	);

	$target = isset( $value['default_search_target'] ) ? sanitize_key( $value['default_search_target'] ) : $defaults['default_search_target'];
	if ( ! in_array( $target, array( 'products', 'articles', 'all' ), true ) ) {
		$target = $defaults['default_search_target'];
	}

	$layout = isset( $value['layout'] ) ? sanitize_key( $value['layout'] ) : $defaults['layout'];
	if ( ! in_array( $layout, array( 'compact', 'full' ), true ) ) {
		$layout = $defaults['layout'];
	}

	$settings['default_search_target'] = $target;
	$settings['contact_label']         = isset( $value['contact_label'] ) ? sanitize_text_field( $value['contact_label'] ) : $defaults['contact_label'];
	$settings['contact_url']           = isset( $value['contact_url'] ) ? esc_url_raw( $value['contact_url'] ) : $defaults['contact_url'];
	$settings['quote_label']           = isset( $value['quote_label'] ) ? sanitize_text_field( $value['quote_label'] ) : $defaults['quote_label'];
	$settings['quote_url']             = isset( $value['quote_url'] ) ? esc_url_raw( $value['quote_url'] ) : $defaults['quote_url'];
	$settings['layout']                = $layout;

	foreach ( array( 'contact_label', 'contact_url', 'quote_label', 'quote_url' ) as $key ) {
		if ( '' === $settings[ $key ] ) {
			$settings[ $key ] = $defaults[ $key ];
		}
	}

	return $settings;
}

/**
 * Get sanitized header action settings.
 *
 * @return array<string,mixed>
 */
function skvn_marine_get_header_actions() {
	return skvn_marine_sanitize_header_actions(
		get_option( SKVN_MARINE_HEADER_ACTIONS_OPTION, skvn_marine_get_default_header_actions() )
	);
}

/**
 * Get the active search target from the request or settings.
 *
 * @return string
 */
function skvn_marine_get_search_target() {
	$settings = skvn_marine_get_header_actions();
	$target   = isset( $_GET['skvn_search_target'] ) ? sanitize_key( wp_unslash( $_GET['skvn_search_target'] ) ) : $settings['default_search_target'];

	if ( ! in_array( $target, array( 'products', 'articles', 'all' ), true ) ) {
		$target = $settings['default_search_target'];
	}

	return $target;
}

/**
 * Render governed header actions inside the GeneratePress header shell.
 *
 * @return void
 */
function skvn_marine_render_header_actions() {
	$settings = skvn_marine_get_header_actions();

	if ( empty( $settings['enabled'] ) ) {
		return;
	}

	$layout        = 'full' === $settings['layout'] ? 'full' : 'compact';
	$search_target = skvn_marine_get_search_target();
	?>
	<div class="skvn-header-actions skvn-header-actions--<?php echo esc_attr( $layout ); ?>" aria-label="<?php echo esc_attr__( 'Header actions', 'skvn-marine' ); ?>">
		<?php if ( ! empty( $settings['product_search_enabled'] ) || ! empty( $settings['post_search_enabled'] ) ) : ?>
			<form class="skvn-header-search" action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get" role="search">
				<label class="screen-reader-text" for="skvn-header-search-keyword"><?php echo esc_html__( 'Search keyword', 'skvn-marine' ); ?></label>
				<?php skvn_marine_render_search_target_control( $settings, $search_target ); ?>
				<input id="skvn-header-search-keyword" type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php echo esc_attr__( 'Search', 'skvn-marine' ); ?>">
				<button type="submit" class="skvn-header-search__submit" aria-label="<?php echo esc_attr__( 'Search', 'skvn-marine' ); ?>">⌕</button>
			</form>
		<?php endif; ?>

		<?php if ( ! empty( $settings['contact_enabled'] ) ) : ?>
			<a class="skvn-header-action skvn-header-action--contact" href="<?php echo esc_url( $settings['contact_url'] ); ?>"><?php echo esc_html( $settings['contact_label'] ); ?></a>
		<?php endif; ?>

		<?php if ( ! empty( $settings['quote_enabled'] ) ) : ?>
			<a class="skvn-header-action skvn-header-action--quote" href="<?php echo esc_url( $settings['quote_url'] ); ?>"><?php echo esc_html( $settings['quote_label'] ); ?></a>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Render search target select or hidden field based on enabled intents.
 *
 * @param array<string,mixed> $settings      Header settings.
 * @param string              $search_target Active search target.
 * @return void
 */
function skvn_marine_render_search_target_control( $settings, $search_target ) {
	$options = array();

	if ( ! empty( $settings['product_search_enabled'] ) ) {
		$options['products'] = __( 'Products', 'skvn-marine' );
	}

	if ( ! empty( $settings['post_search_enabled'] ) ) {
		$options['articles'] = __( 'Articles', 'skvn-marine' );
	}

	if ( count( $options ) > 1 ) {
		$options['all'] = __( 'All site', 'skvn-marine' );
		?>
		<label class="screen-reader-text" for="skvn-header-search-target"><?php echo esc_html__( 'Search target', 'skvn-marine' ); ?></label>
		<select id="skvn-header-search-target" name="skvn_search_target">
			<?php foreach ( $options as $value => $label ) : ?>
				<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $search_target, $value ); ?>>
					<?php echo esc_html( $label ); ?>
				</option>
			<?php endforeach; ?>
		</select>
		<?php
		return;
	}

	$target = key( $options );
	printf(
		'<input type="hidden" name="skvn_search_target" value="%s">',
		esc_attr( $target ? $target : 'all' )
	);
}

/**
 * Find term IDs matching a search term.
 *
 * @param string[] $taxonomies Taxonomy names.
 * @param string   $term       Search term.
 * @return int[]
 */
function skvn_marine_find_search_term_ids( $taxonomies, $term ) {
	$ids = array();

	foreach ( $taxonomies as $taxonomy ) {
		if ( ! taxonomy_exists( $taxonomy ) ) {
			continue;
		}

		$terms = get_terms(
			array(
				'taxonomy'   => $taxonomy,
				'fields'     => 'ids',
				'hide_empty' => false,
				'search'     => $term,
			)
		);

		if ( is_wp_error( $terms ) ) {
			continue;
		}

		$ids = array_merge( $ids, array_map( 'absint', $terms ) );
	}

	return array_values( array_unique( array_filter( $ids ) ) );
}

/**
 * Build a taxonomy-first and title-first result query.
 *
 * @param string   $post_type  Post type.
 * @param string   $term       Search term.
 * @param string[] $taxonomies Taxonomy names.
 * @param int      $limit      Maximum result count.
 * @return WP_Post[]
 */
function skvn_marine_get_prioritized_search_posts( $post_type, $term, $taxonomies, $limit = 6 ) {
	$results  = array();
	$exclude  = array();
	$term_ids = skvn_marine_find_search_term_ids( $taxonomies, $term );

	if ( $term_ids ) {
		$tax_query = array( 'relation' => 'OR' );

		foreach ( $taxonomies as $taxonomy ) {
			if ( ! taxonomy_exists( $taxonomy ) ) {
				continue;
			}

			$tax_query[] = array(
				'taxonomy' => $taxonomy,
				'field'    => 'term_id',
				'terms'    => $term_ids,
			);
		}

		$taxonomy_query = new WP_Query(
			array(
				'post_type'           => $post_type,
				'post_status'         => 'publish',
				'posts_per_page'      => $limit,
				'ignore_sticky_posts' => true,
				'tax_query'           => $tax_query,
			)
		);

		foreach ( $taxonomy_query->posts as $post ) {
			$results[] = $post;
			$exclude[] = $post->ID;
		}
	}

	if ( count( $results ) < $limit ) {
		$title_query = new WP_Query(
			array(
				'post_type'           => $post_type,
				'post_status'         => 'publish',
				'posts_per_page'      => $limit - count( $results ),
				'post__not_in'        => $exclude,
				'ignore_sticky_posts' => true,
				's'                   => $term,
				'search_columns'      => array( 'post_title' ),
			)
		);

		foreach ( $title_query->posts as $post ) {
			$results[] = $post;
			$exclude[] = $post->ID;
		}
	}

	if ( count( $results ) < $limit ) {
		$fallback_query = new WP_Query(
			array(
				'post_type'           => $post_type,
				'post_status'         => 'publish',
				'posts_per_page'      => $limit - count( $results ),
				'post__not_in'        => $exclude,
				'ignore_sticky_posts' => true,
				's'                   => $term,
			)
		);

		foreach ( $fallback_query->posts as $post ) {
			$results[] = $post;
		}
	}

	return $results;
}
