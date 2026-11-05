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
 *    Khi bật "Smooth initial loading" (tùy chọn, mới trong 1.3.18), dùng luồng
 *    mới: không có minimum hold, hiển thị sau ngưỡng "Show loading after",
 *    tắt theo "Loading fade duration", always fail-open.
 *  - splash:    overlay brand, chỉ 1 lần/phiên (sessionStorage), tự ẩn.
 *  - brandbar:  thanh mỏng ở đỉnh, không che, CSS animation tự chạy.
 *  - off:       không render.
 *
 * Chuyển trang: tùy chọn "Page transition: Fade" dùng native cross-document
 * View Transitions (progressive enhancement), độc lập với loading.
 *
 * @package skvn-marine
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Resolve config + eligibility for loading overlay. Returns config array, or false if not running.
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
			'type'               => 'preloader',
			'scope'              => 'front',
			'dismiss'            => 'hero',
			'min_display'        => 0,
			'mark_text'          => '',
			'tagline'            => '',
			'use_logo'           => false,
			'logo_id'            => 0,
			'bg_type'            => 'gradient',
			'bg_solid'           => 'skvn-blue-950',
			'bg_gradient'        => 'skvn-navy-deep',
			'text_color'         => 'skvn-white',
			'accent_color'       => 'skvn-gold-300',
			'smooth_initial_loading' => false,
			'page_transition'    => 'none',
			'frequency'          => 'once',
			'loading_show_after' => 150,
			'loading_fade_duration' => 200,
			'page_transition_duration' => 180,
		);

	$run = 'off' !== $cfg['type'] && ! is_admin();
	if ( $run && 'front' === $cfg['scope'] ) {
		$run = is_front_page() || (
			'preloader' === $cfg['type'] && ! empty( $cfg['smooth_initial_loading'] ) && is_page( 'home' )
		);
	}

	$resolved = $run ? $cfg : false;
	return $resolved;
}

/**
 * Whether a sensitive route should be excluded from page transitions.
 * Detection by real WordPress/WooCommerce conditionals and page display config,
 * never by guessing slugs.
 *
 * @return bool
 */
function skvn_marine_preloader_is_sensitive_route() {
	if ( is_admin() || is_preview() || is_feed() || is_trackback() || is_comments_popup() ) {
		return true;
	}

	if ( function_exists( 'is_cart' ) && is_cart() ) {
		return true;
	}
	if ( function_exists( 'is_checkout' ) && is_checkout() ) {
		return true;
	}
	if ( function_exists( 'is_account_page' ) && is_account_page() ) {
		return true;
	}

	if ( ! empty( $_GET['download'] ) ) {
		return true;
	}

	// Quote / Thank-you pages via real page display preset (not slug guessing).
	if ( is_page() && defined( 'SKVN_MARINE_PAGE_DISPLAY_PRESET_META' ) && function_exists( 'skvn_marine_page_uses_display_feature' ) ) {
		$post_id = get_queried_object_id();
		if ( $post_id ) {
			$preset = get_post_meta( $post_id, SKVN_MARINE_PAGE_DISPLAY_PRESET_META, true );
			if ( 'skvn_request_quote_page' === $preset ) {
				return true;
			}
		}
	}

	return false;
}

/**
 * Whether page transition (fade) is enabled and eligible for the current request.
 *
 * @return bool
 */
function skvn_marine_preloader_transition_eligible() {
	$raw = function_exists( 'skvn_marine_blocks_get_preloader' )
		? skvn_marine_blocks_get_preloader()
		: array( 'page_transition' => 'none' );

	if ( 'fade' !== ( $raw['page_transition'] ?? 'none' ) ) {
		return false;
	}

	return ! skvn_marine_preloader_is_sensitive_route();
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

/**
 * Page transition head: opt-in meta tag + root fade CSS.
 *
 * @return void
 */
function skvn_marine_preloader_transition_head() {
	if ( ! skvn_marine_preloader_transition_eligible() ) {
		return;
	}

	$raw = function_exists( 'skvn_marine_blocks_get_preloader' )
		? skvn_marine_blocks_get_preloader()
		: array();
	$duration = (int) ( $raw['page_transition_duration'] ?? 180 );
	$duration = max( 100, min( 400, $duration ) );
	?>
	<meta name="view-transition" content="same-origin">
	<style id="skvn-preloader-transition">
	::view-transition-old(root) {
		animation: skvn-vt-fade-out ease-out both;
		animation-duration: <?php echo $duration; ?>ms;
		z-index: 1;
	}
	::view-transition-new(root) {
		z-index: auto;
		animation: none;
	}
	@keyframes skvn-vt-fade-out {
		from { opacity: 1; }
		to   { opacity: 0; }
	}
	@media (prefers-reduced-motion: reduce) {
		::view-transition-group(root), ::view-transition-old(root), ::view-transition-new(root) { animation: none !important; }
	}
	</style>
	<?php
}

add_action( 'wp_head', 'skvn_marine_preloader_head', 1 );
add_action( 'wp_body_open', 'skvn_marine_preloader_markup' );

/** Homepage curtain is opt-in; /home/ is the explicitly supplied landing page. */
function skvn_marine_preloader_uses_curtain( $cfg ) {
	return false !== $cfg && 'preloader' === $cfg['type']
		&& ! empty( $cfg['smooth_initial_loading'] )
		&& ( is_front_page() || is_page( 'home' ) );
}

/** Independent early lifecycle: never waits for the plugin bundle to recover. */
function skvn_marine_preloader_curtain_head( $cfg ) {
	?>
	<style id="skvn-curtain-css">
		/* No JS = no cover. Fixed inset owns the viewport without 100vw/scroll lock. */
		#skvn-preloader { display: none; animation: none; transition: none; }
		html[data-skvn-loading] #skvn-preloader {
			display: flex; opacity: 1; visibility: visible;
			transform: translateY(0); pointer-events: auto;
			transition: transform 750ms cubic-bezier(.65, 0, .35, 1);
			animation: skvn-curtain-recovery 2500ms step-end forwards;
		}
		html[data-skvn-loading="revealing"] #skvn-preloader { transform: translateY(-100%); }
		@keyframes skvn-curtain-recovery { to { visibility: hidden; pointer-events: none; } }
		@media (prefers-reduced-motion: reduce) {
			html[data-skvn-loading] #skvn-preloader { display: none; transition: none; }
		}
	</style>
	<script id="skvn-curtain-bootstrap">
	( function () {
		var root = document.documentElement;
		var motion = window.matchMedia( '(prefers-reduced-motion: reduce)' );
		var once = <?php echo wp_json_encode( 'once' === ( $cfg['frequency'] ?? 'once' ) ); ?>;
		var state = 'pending';
		var deadline, watchdog, cleanupTimer;
		function panel() { return document.getElementById( 'skvn-preloader' ); }
		function blockTab( event ) {
			if ( root.hasAttribute( 'data-skvn-loading' ) && event.key === 'Tab' ) { event.preventDefault(); }
		}
		function release() {
			if ( state === 'released' ) { return; }
			state = 'released';
			clearTimeout( deadline ); clearTimeout( watchdog ); clearTimeout( cleanupTimer );
			root.removeAttribute( 'data-skvn-loading' );
			root.removeAttribute( 'data-skvn-loading-until' );
			var el = panel();
			if ( el ) { el.remove(); }
			document.removeEventListener( 'keydown', blockTab, true );
			document.removeEventListener( 'skvn:slider-ready', ready );
			document.removeEventListener( 'DOMContentLoaded', ready );
			document.removeEventListener( 'transitionend', transitionEnd );
			document.removeEventListener( 'transitioncancel', transitionEnd );
			motion.removeEventListener( 'change', motionChanged );
			window.dispatchEvent( new Event( 'skvn:loading-released' ) );
		}
		function reveal( timedOut ) {
			if ( state !== 'covering' ) { return; }
			state = 'revealing';
			clearTimeout( deadline );
			if ( timedOut ) { console.warn( '[SKVN loader] Main Hero Slider not ready at 1500ms; revealing static fallback.' ); }
			if ( motion.matches ) { release(); return; }
			// Commit the initial transform even when a cached bundle initializes before paint.
			var el = panel();
			if ( el ) { el.getBoundingClientRect(); }
			root.setAttribute( 'data-skvn-loading', 'revealing' );
			cleanupTimer = setTimeout( release, 850 );
		}
		function ready() {
			var heroes = document.querySelectorAll( '.skvn-slider--hero[data-skvn-slider]' );
			if ( heroes.length !== 1 ) { return; } // Never guess among multiple heroes.
			var hero = heroes[0];
			var box = hero.getBoundingClientRect();
			if ( hero.dataset.skvnSliderReady === 'true' && box.width > 0 && box.height > 0 ) { reveal( false ); }
		}
		function transitionEnd( event ) {
			if ( state === 'revealing' && event.target === panel() && event.propertyName === 'transform' ) { release(); }
		}
		function motionChanged() { if ( motion.matches ) { release(); } }
		window.addEventListener( 'pageshow', function ( event ) { if ( event.persisted ) { release(); } } );
		window.addEventListener( 'pagehide', release );
		window.addEventListener( 'pageswap', function ( event ) {
			if ( state !== 'released' && event.viewTransition ) { event.viewTransition.skipTransition(); }
			release();
		} );
		window.addEventListener( 'pagereveal', function ( event ) {
			if ( state !== 'released' && event.viewTransition ) { event.viewTransition.skipTransition(); }
		} );
		if ( motion.matches ) { release(); return; }
		if ( once ) {
			try {
				if ( sessionStorage.getItem( 'skvnLoadingShown' ) ) { release(); return; }
				sessionStorage.setItem( 'skvnLoadingShown', '1' );
			} catch ( error ) { release(); return; }
		}
		state = 'covering';
		// Arm recovery before activating the cover or installing any other listeners.
		watchdog = setTimeout( release, 2500 );
		deadline = setTimeout( function () { reveal( true ); }, 1500 );
		root.setAttribute( 'data-skvn-loading-until', String( Date.now() + 2500 ) );
		root.setAttribute( 'data-skvn-loading', 'covering' );
		document.addEventListener( 'keydown', blockTab, true );
		document.addEventListener( 'skvn:slider-ready', ready );
		document.addEventListener( 'DOMContentLoaded', ready );
		document.addEventListener( 'transitionend', transitionEnd );
		document.addEventListener( 'transitioncancel', transitionEnd );
		motion.addEventListener( 'change', motionChanged );
		ready();
	} )();
	</script>
	<?php
}

/**
 * Print critical CSS + early scroll-lock / session-skip class.
 *
 * @return void
 */
function skvn_marine_preloader_head() {
	$cfg = skvn_marine_preloader_config();

	// Page transition opt-in (independent of loading) is emitted whenever eligible.
	skvn_marine_preloader_transition_head();

	if ( false === $cfg ) {
		return;
	}

	$bg     = 'gradient' === $cfg['bg_type']
		? skvn_marine_preloader_gradient( $cfg['bg_gradient'] )
		: skvn_marine_preloader_color( $cfg['bg_solid'] );
	$fg     = skvn_marine_preloader_color( $cfg['text_color'] );
	$accent = skvn_marine_preloader_color( $cfg['accent_color'] );
	$fadeDuration = (int) ( $cfg['loading_fade_duration'] ?? 200 );
	$fadeDuration = max( 100, min( 500, $fadeDuration ) );

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
			opacity: 1; transition: opacity <?php echo $fadeDuration; ?>ms ease-out;
		}
		#skvn-preloader.is-hidden { opacity: 0; visibility: hidden; pointer-events: none; }
		/* No-JS fail-open: overlay releases itself after the safety cap so the
		   page is never locked forever when JavaScript cannot run. */
		#skvn-preloader:not([data-skvn-js]) {
			animation: skvn-preloader-failopen 3500ms ease-out forwards;
		}
		@keyframes skvn-preloader-failopen {
			to { opacity: 0; visibility: hidden; pointer-events: none; }
		}
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
			#skvn-preloader.is-hidden { display: none; }
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
	<?php
	if ( skvn_marine_preloader_uses_curtain( $cfg ) ) {
		skvn_marine_preloader_curtain_head( $cfg );
		return; // No legacy root scroll lock or body bootstrap in this branch.
	}
	?>
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
 * Bootstrap inline JavaScript for the new smooth initial loading flow and/or
 * page transitions. Emitted when a loading overlay exists or page transition
 * is eligible.
 *
 * @param array<string,mixed> $cfg          Preloader config (raw getter value or defaults).
 * @param bool                $hasOverlay   Whether #skvn-preloader markup exists on this page.
 * @param bool                $transitionEligible Whether page transition applies.
 * @return void
 */
function skvn_marine_preloader_bootstrap_js( $cfg, $hasOverlay, $transitionEligible ) {
	$fallbackText = '' !== ( $cfg['mark_text'] ?? '' )
		? $cfg['mark_text']
		: get_bloginfo( 'name' );
	$jsCfg = array(
		'type'                     => $cfg['type'] ?? 'preloader',
		'smooth_initial_loading'   => ! empty( $cfg['smooth_initial_loading'] ),
		'page_transition'          => $cfg['page_transition'] ?? 'none',
		'frequency'                => $cfg['frequency'] ?? 'once',
		'loading_show_after'       => max( 0, min( 500, (int) ( $cfg['loading_show_after'] ?? 150 ) ) ),
		'loading_fade_duration'    => max( 100, min( 500, (int) ( $cfg['loading_fade_duration'] ?? 200 ) ) ),
		'page_transition_duration' => max( 100, min( 400, (int) ( $cfg['page_transition_duration'] ?? 180 ) ) ),
		'logo_fallback_text'       => $fallbackText,
	);
	?>
	<script id="skvn-preloader-bootstrap">
	( function () {
		var root = document.documentElement;
		var el = document.getElementById( 'skvn-preloader' );
		var cfg = <?php echo wp_json_encode( $jsCfg ); ?>;
		var done = false;
        var showTimer = null;
        var safetyTimeout = null;
        var reduced = window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

		if ( el ) {
			el.setAttribute( 'data-skvn-js', '1' );

			// Broken logo image falls back to text; never hold the page for the logo.
			var logo = el.querySelector( '.skvn-preloader__logo[data-skvn-logo]' );
			if ( logo ) {
				logo.addEventListener( 'error', function () {
					var span = document.createElement( 'span' );
					span.className = 'skvn-preloader__mark';
					span.textContent = cfg.logo_fallback_text || '';
					logo.parentNode.replaceChild( span, logo );
				}, { once: true } );
			}
		}

		function clearTimers() {
			if ( showTimer )     { clearTimeout( showTimer );     showTimer = null; }
			if ( safetyTimeout ) { clearTimeout( safetyTimeout ); safetyTimeout = null; }
		}
		function removeEl() {
			if ( el && el.parentNode ) {
				el.parentNode.removeChild( el );
			}
		}
		function release() {
			if ( done ) { return; }
			done = true;
			clearTimers();
			root.classList.remove( 'skvn-preloading' );
			if ( el ) {
				el.classList.add( 'is-hidden' );
				el.addEventListener( 'transitionend', removeEl, { once: true } );
				setTimeout( removeEl, ( cfg.loading_fade_duration || 200 ) + 400 );
			}
			if ( 'once' === cfg.frequency ) {
				try { sessionStorage.setItem( 'skvnLoadingShown', '1' ); } catch ( e ) {}
			}
		}
		function isDocumentReady() {
			return 'loading' !== document.readyState;
		}
		function heroReady() {
			var hero = document.querySelector( '.skvn-slide__background-image' );
			if ( ! hero ) { return true; }
			return hero.complete && hero.naturalWidth > 0;
		}
		function scheduleReadinessRelease() {
			var checkReady = function () {
				if ( done ) { return; }
				if ( isDocumentReady() && heroReady() ) {
					release();
					return;
				}
				window.addEventListener( 'load', checkReady, { once: true } );
				var hero = document.querySelector( '.skvn-slide__background-image' );
				if ( hero ) {
					hero.addEventListener( 'load',  checkReady, { once: true } );
					hero.addEventListener( 'error', checkReady, { once: true } );
				}
			};
			if ( 'loading' === document.readyState ) {
				document.addEventListener( 'DOMContentLoaded', checkReady );
			} else {
				checkReady();
			}
		}
		function beginLoadingFlow() {
			if ( done ) { return; }
			if ( reduced ) { release(); return; }
			if ( ! cfg.smooth_initial_loading ) { release(); return; }
			if ( 'once' === cfg.frequency ) {
				try { if ( sessionStorage.getItem( 'skvnLoadingShown' ) ) { release(); return; } } catch ( e ) {}
			}
			// Delayed decision: show overlay only if content not ready after threshold.
			showTimer = setTimeout( function () {
				if ( done ) { return; }
				if ( isDocumentReady() && heroReady() ) {
					release(); // fast page: skip overlay entirely
					return;
				}
				// Overlay stays visible (already painted); wait for readiness then fade.
				scheduleReadinessRelease();
			}, cfg.loading_show_after ); // PHP normalizes the range; zero is a valid delay.
		}

		// Safety timeout: fixed 3.5 s cap for the new flow, never extended by min_display.
		if ( el && cfg.smooth_initial_loading ) {
			beginLoadingFlow();
			safetyTimeout = setTimeout( release, 3500 );
		}

		// Page transitions (cross-document View Transitions, progressive enhancement).
		if ( <?php echo $transitionEligible ? 'true' : 'false'; ?> ) {
			// Dismiss overlay on source before navigating away; never preventDefault.
			window.addEventListener( 'pageswap', function () {
				if ( el && ! el.classList.contains( 'is-hidden' ) ) {
					el.classList.add( 'is-hidden' );
				}
			} );
			// Skip transition on destination when the loading overlay should take over.
			window.addEventListener( 'page-reveal', function ( event ) {
				if ( ! event || ! event.viewTransition ) { return; }
				var shouldShow = cfg.smooth_initial_loading && ! reduced;
				if ( 'once' === cfg.frequency ) {
					try { if ( sessionStorage.getItem( 'skvnLoadingShown' ) ) { shouldShow = false; } } catch ( e ) {}
				}
				if ( shouldShow ) {
					event.viewTransition.skipTransition();
				}
			} );
		}

		// BFCache / history restore: release any stuck overlay, never replay.
		window.addEventListener( 'pageshow', function ( event ) {
			if ( event && event.persisted ) {
				release();
			}
		} );

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
	$raw = function_exists( 'skvn_marine_blocks_get_preloader' )
		? skvn_marine_blocks_get_preloader()
		: array( 'page_transition' => 'none', 'smooth_initial_loading' => false );
	$cfg = skvn_marine_preloader_config(); // false or resolved config
	$transitionEligible = skvn_marine_preloader_transition_eligible();
	$is_splash   = false !== $cfg && 'splash' === $cfg['type'];
	$is_skeleton = false !== $cfg && 'skeleton' === $cfg['type'];
	$useNewFlow  = false !== $cfg && 'preloader' === $cfg['type'] && ! empty( $cfg['smooth_initial_loading'] );

	if ( false !== $cfg && 'brandbar' === $cfg['type'] ) {
		echo '<div id="skvn-brandbar" aria-hidden="true"></div>';
		if ( $transitionEligible ) {
			skvn_marine_preloader_bootstrap_js( $raw, false, true );
		}
		return;
	}

	if ( false === $cfg ) {
		if ( $transitionEligible ) {
			skvn_marine_preloader_bootstrap_js( $raw, false, true );
		}
		return;
	}

	$mark        = '' !== $cfg['mark_text'] ? $cfg['mark_text'] : get_bloginfo( 'name' );
	$logo        = '';
	if ( ! empty( $cfg['use_logo'] ) ) {
		$logoId = ! empty( $cfg['logo_id'] ) ? (int) $cfg['logo_id'] : (int) get_theme_mod( 'custom_logo' );
		if ( $logoId ) {
			$logo = wp_get_attachment_image( $logoId, 'medium', false, array(
				'class'         => 'skvn-preloader__logo',
				'alt'           => $mark,
				'data-skvn-logo' => '1',
			) );
		}
	}
	?>
	<div id="skvn-preloader" class="skvn-preloader" role="status" aria-label="<?php esc_attr_e( 'Loading', 'skvn-marine' ); ?>">
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
			<?php if ( ! $is_splash && ! $useNewFlow ) : ?>
				<span class="skvn-preloader__spinner" aria-hidden="true"></span>
			<?php endif; ?>
		</div>
		<?php endif; ?>
	</div>
	<?php

	if ( skvn_marine_preloader_uses_curtain( $cfg ) ) {
		// Head lifecycle owns this overlay. Keep logo fallback independent of readiness.
		?>
		<script id="skvn-curtain-logo">
		( function () {
			var el = document.getElementById( 'skvn-preloader' );
			if ( ! document.documentElement.hasAttribute( 'data-skvn-loading' ) ) { if ( el ) { el.remove(); } return; }
			var logo = el && el.querySelector( '.skvn-preloader__logo' );
			function fallback() {
				if ( ! logo || ! logo.parentNode ) { return; }
				var text = document.createElement( 'span' );
				text.className = 'skvn-preloader__mark';
				text.textContent = <?php echo wp_json_encode( $mark ); ?>;
				logo.replaceWith( text );
			}
			if ( logo ) { logo.addEventListener( 'error', fallback, { once: true } ); if ( logo.complete && ! logo.naturalWidth ) { fallback(); } }
		} )();
		</script>
		<?php
	} elseif ( $useNewFlow ) {
		skvn_marine_preloader_bootstrap_js( $cfg, true, $transitionEligible );
	} else {
		// Legacy inline dismiss logic (unchanged behavior for non-smooth modes).
		?>
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
		if ( $transitionEligible ) {
			skvn_marine_preloader_bootstrap_js( $raw, false, true );
		}
	}
}
