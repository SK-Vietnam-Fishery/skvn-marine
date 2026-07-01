<?php
/**
 * Loading screen renderer (preloader / splash / brand bar).
 *
 * Đọc cấu hình từ plugin getter skvn_marine_blocks_get_preloader() (bọc
 * function_exists → plugin off thì dùng default theme). Màu lấy từ theme.json
 * palette/gradient, resolve sang giá trị literal để inline CSS không phụ thuộc
 * thứ tự load của global styles.
 *
 * Loại:
 *  - preloader: overlay + spinner, ẩn theo hero/window/time + timeout an toàn.
 *  - splash:    overlay brand, chỉ 1 lần/phiên (sessionStorage), tự ẩn.
 *  - brandbar:  thanh mỏng ở đỉnh, không che, CSS animation tự chạy.
 *  - off:       không render.
 *
 * @package skvn-marine
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Resolve config + eligibility. Returns config array, or false if not running.
 *
 * @return array<string,mixed>|false
 */
function skvn_marine_preloader_config() {
	static $resolved = null;
	if ( null !== $resolved ) {
		return $resolved;
	}

	$cfg = function_exists( 'skvn_marine_blocks_get_preloader' )
		? skvn_marine_blocks_get_preloader()
		: array(
			'type'         => 'preloader',
			'scope'        => 'front',
			'dismiss'      => 'hero',
			'min_display'  => 0,
			'mark_text'    => '',
			'tagline'      => '',
			'use_logo'     => false,
			'logo_id'      => 0,
			'bg_type'      => 'gradient',
			'bg_solid'     => 'skvn-blue-950',
			'bg_gradient'  => 'skvn-navy-deep',
			'text_color'   => 'skvn-white',
			'accent_color' => 'skvn-gold-300',
		);

	$run = 'off' !== $cfg['type'] && ! is_admin();
	if ( $run && 'front' === $cfg['scope'] ) {
		$run = is_front_page();
	}

	$resolved = $run ? $cfg : false;
	return $resolved;
}

/**
 * Resolve a palette slug to its literal color.
 *
 * @param string $slug Palette slug.
 * @return string
 */
function skvn_marine_preloader_color( $slug ) {
	static $map = null;
	if ( null === $map ) {
		$map     = array();
		$palette = wp_get_global_settings( array( 'color', 'palette' ) );
		$palette = isset( $palette['theme'] ) && is_array( $palette['theme'] ) ? $palette['theme'] : array();
		foreach ( $palette as $color ) {
			if ( isset( $color['slug'], $color['color'] ) ) {
				$map[ $color['slug'] ] = $color['color'];
			}
		}
	}
	return $map[ $slug ] ?? '#082f49';
}

/**
 * Resolve a gradient slug to its literal CSS gradient.
 *
 * @param string $slug Gradient slug.
 * @return string
 */
function skvn_marine_preloader_gradient( $slug ) {
	static $map = null;
	if ( null === $map ) {
		$map       = array();
		$gradients = wp_get_global_settings( array( 'color', 'gradients' ) );
		$gradients = isset( $gradients['theme'] ) && is_array( $gradients['theme'] ) ? $gradients['theme'] : array();
		foreach ( $gradients as $gradient ) {
			if ( isset( $gradient['slug'], $gradient['gradient'] ) ) {
				$map[ $gradient['slug'] ] = $gradient['gradient'];
			}
		}
	}
	return $map[ $slug ] ?? 'linear-gradient(135deg, #082f49 0%, #0c4a6e 100%)';
}

/**
 * Convert a hex color to an rgba() string (for skeleton bar tints).
 *
 * @param string $hex   Hex color (#rgb or #rrggbb).
 * @param string $alpha Alpha component, e.g. '0.14'.
 * @return string
 */
function skvn_marine_preloader_rgba( $hex, $alpha ) {
	$hex = ltrim( (string) $hex, '#' );
	if ( 3 === strlen( $hex ) ) {
		$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
	}
	if ( 6 !== strlen( $hex ) || ! ctype_xdigit( $hex ) ) {
		return 'rgba(255, 255, 255, ' . $alpha . ')';
	}
	return sprintf(
		'rgba(%d, %d, %d, %s)',
		hexdec( substr( $hex, 0, 2 ) ),
		hexdec( substr( $hex, 2, 2 ) ),
		hexdec( substr( $hex, 4, 2 ) ),
		$alpha
	);
}

add_action( 'wp_head', 'skvn_marine_preloader_head', 1 );
add_action( 'wp_body_open', 'skvn_marine_preloader_markup' );

/**
 * Print critical CSS + early scroll-lock / session-skip class.
 *
 * @return void
 */
function skvn_marine_preloader_head() {
	$cfg = skvn_marine_preloader_config();
	if ( false === $cfg ) {
		return;
	}

	$bg     = 'gradient' === $cfg['bg_type']
		? skvn_marine_preloader_gradient( $cfg['bg_gradient'] )
		: skvn_marine_preloader_color( $cfg['bg_solid'] );
	$fg     = skvn_marine_preloader_color( $cfg['text_color'] );
	$accent = skvn_marine_preloader_color( $cfg['accent_color'] );

	if ( 'brandbar' === $cfg['type'] ) {
		?>
		<style id="skvn-preloader-css">
			#skvn-brandbar {
				position: fixed; top: 0; left: 0; height: 3px; width: 0; z-index: 999999;
				background: <?php echo esc_html( $accent ); ?>;
				animation: skvn-bar 1s ease-out forwards;
			}
			@keyframes skvn-bar { 0% { width: 0; opacity: 1; } 80% { width: 100%; opacity: 1; } 100% { width: 100%; opacity: 0; } }
			@media (prefers-reduced-motion: reduce) { #skvn-brandbar { animation: none; display: none; } }
		</style>
		<?php
		return;
	}

	$is_splash = 'splash' === $cfg['type'];
	?>
	<style id="skvn-preloader-css">
		html.skvn-preloading { overflow: hidden; }
		<?php if ( $is_splash ) : ?>
		html.skvn-splash-seen #skvn-preloader { display: none; }
		<?php endif; ?>
		#skvn-preloader {
			position: fixed; inset: 0; z-index: 999999;
			display: flex; align-items: center; justify-content: center;
			background: <?php echo esc_html( $bg ); ?>;
			opacity: 1; transition: opacity .45s ease;
		}
		#skvn-preloader.is-hidden { opacity: 0; visibility: hidden; pointer-events: none; }
		.skvn-preloader__inner { display: flex; flex-direction: column; align-items: center; gap: 16px; text-align: center; }
		.skvn-preloader__logo { max-height: 72px; width: auto; }
		.skvn-preloader__mark {
			color: <?php echo esc_html( $fg ); ?>;
			font-family: system-ui, -apple-system, "Segoe UI", sans-serif;
			font-weight: 700; font-size: 24px; letter-spacing: .06em;
		}
		.skvn-preloader__tagline {
			color: <?php echo esc_html( $fg ); ?>; opacity: .65;
			font-family: system-ui, sans-serif; font-size: 12px; letter-spacing: .18em; text-transform: uppercase;
		}
		.skvn-preloader__spinner {
			width: 38px; height: 38px; border: 3px solid rgba(255,255,255,.18);
			border-top-color: <?php echo esc_html( $accent ); ?>; border-radius: 50%;
			animation: skvn-pl-spin .8s linear infinite; margin-top: 4px;
		}
		@keyframes skvn-pl-spin { to { transform: rotate(360deg); } }
		@media (prefers-reduced-motion: reduce) {
			.skvn-preloader__spinner { animation: none; opacity: .5; }
			#skvn-preloader { transition: none; }
		}
		.skvn-skeleton { width: min(760px, 90vw); display: flex; flex-direction: column; gap: 18px; }
		.skvn-skeleton__bar {
			position: relative; overflow: hidden; height: 16px; border-radius: 6px;
			background: <?php echo esc_html( skvn_marine_preloader_rgba( $fg, '0.14' ) ); ?>;
		}
		.skvn-skeleton__bar--eyebrow { width: 120px; height: 12px; }
		.skvn-skeleton__bar--title { width: 65%; height: 30px; }
		.skvn-skeleton__bar--title2 { width: 48%; height: 30px; }
		.skvn-skeleton__bar--text { width: 54%; }
		.skvn-skeleton__bar--btn { width: 150px; height: 44px; margin-top: 8px; }
		.skvn-skeleton__bar::after {
			content: ""; position: absolute; inset: 0; transform: translateX(-100%);
			background: linear-gradient(90deg, transparent, <?php echo esc_html( skvn_marine_preloader_rgba( $fg, '0.30' ) ); ?>, transparent);
			animation: skvn-shimmer 1.3s infinite;
		}
		@keyframes skvn-shimmer { to { transform: translateX(100%); } }
		@media (prefers-reduced-motion: reduce) { .skvn-skeleton__bar::after { animation: none; } }
	</style>
	<script id="skvn-preloader-lock">
		( function () {
			var r = document.documentElement;
			<?php if ( $is_splash ) : ?>
			try { if ( sessionStorage.getItem( 'skvnSplashSeen' ) ) { r.classList.add( 'skvn-splash-seen' ); return; } } catch ( e ) {}
			<?php endif; ?>
			r.classList.add( 'skvn-preloading' );
		} )();
	</script>
	<?php
}

/**
 * Print overlay/bar markup + dismiss script.
 *
 * @return void
 */
function skvn_marine_preloader_markup() {
	$cfg = skvn_marine_preloader_config();
	if ( false === $cfg ) {
		return;
	}

	if ( 'brandbar' === $cfg['type'] ) {
		echo '<div id="skvn-brandbar" aria-hidden="true"></div>';
		return;
	}

	$is_splash   = 'splash' === $cfg['type'];
	$is_skeleton = 'skeleton' === $cfg['type'];
	$mark        = '' !== $cfg['mark_text'] ? $cfg['mark_text'] : get_bloginfo( 'name' );
	$logo        = $cfg['use_logo'] && $cfg['logo_id']
		? wp_get_attachment_image( $cfg['logo_id'], 'medium', false, array( 'class' => 'skvn-preloader__logo', 'alt' => $mark ) )
		: '';
	?>
	<div id="skvn-preloader" class="skvn-preloader" role="status" aria-label="<?php esc_attr_e( 'Đang tải', 'skvn-marine' ); ?>">
		<?php if ( $is_skeleton ) : ?>
		<div class="skvn-skeleton" aria-hidden="true">
			<span class="skvn-skeleton__bar skvn-skeleton__bar--eyebrow"></span>
			<span class="skvn-skeleton__bar skvn-skeleton__bar--title"></span>
			<span class="skvn-skeleton__bar skvn-skeleton__bar--title2"></span>
			<span class="skvn-skeleton__bar skvn-skeleton__bar--text"></span>
			<span class="skvn-skeleton__bar skvn-skeleton__bar--btn"></span>
		</div>
		<?php else : ?>
		<div class="skvn-preloader__inner">
			<?php if ( $logo ) : ?>
				<?php echo $logo; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php else : ?>
				<span class="skvn-preloader__mark"><?php echo esc_html( $mark ); ?></span>
			<?php endif; ?>
			<?php if ( '' !== $cfg['tagline'] ) : ?>
				<span class="skvn-preloader__tagline"><?php echo esc_html( $cfg['tagline'] ); ?></span>
			<?php endif; ?>
			<?php if ( ! $is_splash ) : ?>
				<span class="skvn-preloader__spinner" aria-hidden="true"></span>
			<?php endif; ?>
		</div>
		<?php endif; ?>
	</div>
	<script id="skvn-preloader-js">
	( function () {
		var root = document.documentElement;
		var el = document.getElementById( 'skvn-preloader' );
		if ( ! el ) { root.classList.remove( 'skvn-preloading' ); return; }

		var isSplash = <?php echo $is_splash ? 'true' : 'false'; ?>;
		var dismiss = <?php echo wp_json_encode( $cfg['dismiss'] ); ?>;
		var minDisplay = <?php echo (int) $cfg['min_display']; ?>;
		var start = ( window.performance && performance.now ) ? performance.now() : Date.now();
		var done = false;

		function remove() { if ( el && el.parentNode ) { el.parentNode.removeChild( el ); } }
		function doHide() {
			done = true;
			el.classList.add( 'is-hidden' );
			root.classList.remove( 'skvn-preloading' );
			el.addEventListener( 'transitionend', remove, { once: true } );
			setTimeout( remove, 800 );
		}
		function hide() {
			if ( done ) { return; }
			var now = ( window.performance && performance.now ) ? performance.now() : Date.now();
			var waited = now - start;
			if ( waited < minDisplay ) { setTimeout( doHide, minDisplay - waited ); done = true; return; }
			doHide();
		}

		if ( isSplash ) {
			try { sessionStorage.setItem( 'skvnSplashSeen', '1' ); } catch ( e ) {}
			setTimeout( hide, Math.max( minDisplay, 1000 ) );
			window.addEventListener( 'load', function () { setTimeout( hide, Math.max( 0, minDisplay ) ); } );
			setTimeout( hide, 4000 ); // safety.
			return;
		}

		function bindHero() {
			var hero = document.querySelector( '.skvn-slide__background-image' );
			if ( hero ) {
				if ( hero.complete && hero.naturalWidth > 0 ) { hide(); }
				else { hero.addEventListener( 'load', hide ); hero.addEventListener( 'error', hide ); }
			} else { hide(); }
		}

		if ( 'time' === dismiss ) {
			setTimeout( hide, Math.max( minDisplay, 1 ) );
		} else if ( 'window' === dismiss ) {
			if ( document.readyState === 'complete' ) { hide(); }
			else { window.addEventListener( 'load', hide ); }
		} else { // hero
			if ( document.readyState === 'loading' ) { document.addEventListener( 'DOMContentLoaded', bindHero ); }
			else { bindHero(); }
		}

		setTimeout( hide, 3500 );            // safety: never hang.
		window.addEventListener( 'load', hide );
	} )();
	</script>
	<?php
}
