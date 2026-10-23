<?php
/**
 * Enqueue theme CSS/JS. filemtime versioning, no dependencies (no jQuery,
 * no Bootstrap JS) — plain vanilla output, loads immediately.
 *
 * @package cb-peoplesafe2027
 */

defined( 'ABSPATH' ) || exit;

/**
 * Enqueue theme.min.css.
 *
 * @return void
 */
function cb_peoplesafe2027_enqueue_styles() {
	$rel = '/css/theme.min.css';
	$abs = get_stylesheet_directory() . $rel;
	if ( file_exists( $abs ) ) {
		wp_enqueue_style( 'cb-peoplesafe2027-theme', get_stylesheet_directory_uri() . $rel, array(), filemtime( $abs ) );
	}
}
add_action( 'wp_enqueue_scripts', 'cb_peoplesafe2027_enqueue_styles' );

/**
 * Enqueue a file from js/vendor/, filemtime-versioned like everything else.
 *
 * Third-party libraries a project needs should be vendored into js/vendor/
 * and committed, the same convention the compiled css/ and js/ output
 * already follows, rather than loaded from a CDN — that avoids extra DNS/TLS
 * handshakes and keeps a vendored stylesheet off a third-party origin. Note
 * the file the upstream version came from in a comment near the call site
 * (e.g. gsap.min.js 3.12.7 cdn.jsdelivr.net/npm/gsap). The libraries this
 * theme uses (Lenis, GSAP, ScrollTrigger) are tracked in package.json and copied
 * in by `npm run vendor`, which `npm run dist` runs first.
 *
 * @param string $handle Handle to register under.
 * @param string $file   Filename within js/vendor/.
 * @param bool   $is_css True to enqueue as a stylesheet rather than a script.
 * @return void
 */
function cb_peoplesafe2027_enqueue_vendor( $handle, $file, $is_css = false ) {
	$rel = '/js/vendor/' . $file;
	$abs = get_stylesheet_directory() . $rel;
	if ( ! file_exists( $abs ) ) {
		return;
	}
	$url = get_stylesheet_directory_uri() . $rel;
	if ( $is_css ) {
		wp_enqueue_style( $handle, $url, array(), filemtime( $abs ) );
	} else {
		wp_enqueue_script( $handle, $url, array(), filemtime( $abs ), true );
	}
}

/**
 * Enqueue theme.min.js.
 *
 * @return void
 */
function cb_peoplesafe2027_enqueue_scripts() {

	// Lenis (smooth scrolling) is served from js/vendor/, like every library here.
	cb_peoplesafe2027_enqueue_vendor( 'lenis', 'lenis.min.js' );

	$rel = '/js/theme.min.js';
	$abs = get_stylesheet_directory() . $rel;
	if ( file_exists( $abs ) ) {
		wp_enqueue_script( 'cb-peoplesafe2027-theme', get_stylesheet_directory_uri() . $rel, array( 'lenis' ), filemtime( $abs ), true );
	}
}
add_action( 'wp_enqueue_scripts', 'cb_peoplesafe2027_enqueue_scripts' );

/**
 * Load GSAP, ScrollTrigger and the glue that connects them to Lenis. Call it
 * from a block's render.php (or a template) that animates with GSAP, so the
 * libraries load only on pages that need them; wp_enqueue_script in a block
 * render still prints in the footer. Use window.cbMotion (src/js/motion.js).
 *
 * @return void
 */
function cb_peoplesafe2027_enqueue_motion() {
	cb_peoplesafe2027_enqueue_vendor( 'gsap', 'gsap.min.js' );
	cb_peoplesafe2027_enqueue_vendor( 'gsap-scrolltrigger', 'ScrollTrigger.min.js' );

	$rel = '/js/motion.min.js';
	$abs = get_stylesheet_directory() . $rel;

	if ( file_exists( $abs ) ) {
		wp_enqueue_script( 'cb-peoplesafe2027-motion', get_stylesheet_directory_uri() . $rel, array( 'lenis', 'gsap', 'gsap-scrolltrigger', 'cb-peoplesafe2027-theme' ), filemtime( $abs ), true );
	}
}

/**
 * Load the shared slider: Embla (vendored) and src/js/slider.js, which turns
 * every [data-slider] element into an accessible carousel. Call it from the
 * render.php of any block that prints a slider, so the code loads only on
 * pages that have one. The markup contract is in src/js/slider.js.
 *
 * @return void
 */
function cb_peoplesafe2027_enqueue_slider() {
	cb_peoplesafe2027_enqueue_vendor( 'embla-carousel', 'embla-carousel.umd.js' );

	$rel = '/js/slider.min.js';
	$abs = get_stylesheet_directory() . $rel;

	if ( file_exists( $abs ) ) {
		wp_enqueue_script( 'cb-peoplesafe2027-slider', get_stylesheet_directory_uri() . $rel, array( 'embla-carousel' ), filemtime( $abs ), true );
	}
}
