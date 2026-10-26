<?php
/**
 * CB Peoplesafe 2027 — theme functions.
 *
 * Standalone theme, no parent theme. See style.css header for the
 * "BS-flavored naming, not Bootstrap" note and browser support baseline.
 *
 * @package cb-peoplesafe2027
 */

defined( 'ABSPATH' ) || exit;

// Disable the Theme and Plugin file editors in wp-admin. Core omits both
// menu items (and blocks direct access to theme-editor.php/plugin-editor.php)
// when this is set; defining it here rather than wp-config.php keeps the
// hardening with the theme, and the guard respects a wp-config.php value.
if ( ! defined( 'DISALLOW_FILE_EDIT' ) ) {
	define( 'DISALLOW_FILE_EDIT', true );
}

define( 'CB_PEOPLESAFE2027_DIR', get_template_directory() );

require_once CB_PEOPLESAFE2027_DIR . '/inc/setup.php';
require_once CB_PEOPLESAFE2027_DIR . '/inc/enqueue.php';
require_once CB_PEOPLESAFE2027_DIR . '/inc/class-cb-peoplesafe-2027-nav-walker.php';
require_once CB_PEOPLESAFE2027_DIR . '/inc/blocks.php';
require_once CB_PEOPLESAFE2027_DIR . '/inc/editor.php';
require_once CB_PEOPLESAFE2027_DIR . '/inc/options.php';
require_once CB_PEOPLESAFE2027_DIR . '/inc/social-icons.php';
require_once CB_PEOPLESAFE2027_DIR . '/inc/head-tags.php';
require_once CB_PEOPLESAFE2027_DIR . '/inc/block-usage.php';
require_once CB_PEOPLESAFE2027_DIR . '/inc/utilities.php';
require_once CB_PEOPLESAFE2027_DIR . '/inc/posttypes.php';
require_once CB_PEOPLESAFE2027_DIR . '/inc/taxonomies.php';
require_once CB_PEOPLESAFE2027_DIR . '/inc/security.php';
require_once CB_PEOPLESAFE2027_DIR . '/inc/toc.php';
require_once CB_PEOPLESAFE2027_DIR . '/inc/footnotes.php';
require_once CB_PEOPLESAFE2027_DIR . '/inc/cta-buttons.php';
require_once CB_PEOPLESAFE2027_DIR . '/inc/trustpilot.php';
require_once CB_PEOPLESAFE2027_DIR . '/inc/video.php';
require_once CB_PEOPLESAFE2027_DIR . '/inc/faq.php';
require_once CB_PEOPLESAFE2027_DIR . '/inc/regions.php';
require_once CB_PEOPLESAFE2027_DIR . '/inc/sectors.php';
require_once CB_PEOPLESAFE2027_DIR . '/inc/nav.php';
require_once CB_PEOPLESAFE2027_DIR . '/inc/nav-render.php';
require_once CB_PEOPLESAFE2027_DIR . '/inc/nav-seed.php';
require_once CB_PEOPLESAFE2027_DIR . '/inc/product-import.php';
require_once CB_PEOPLESAFE2027_DIR . '/inc/people.php';
