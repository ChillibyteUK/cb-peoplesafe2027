<?php
/**
 * Theme setup — supports, nav menus.
 *
 * @package cb-peoplesafe2027
 */

defined( 'ABSPATH' ) || exit;

/**
 * Core theme supports and nav menu locations.
 *
 * @return void
 */
function cb_peoplesafe2027_setup() {
	load_theme_textdomain( 'cb-peoplesafe2027', get_template_directory() . '/languages' );

	add_theme_support( 'title-tag' ); // Site title in <head> — no separate "site title" support needed beyond this.
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'style', 'script' ) ); // Clean markup for enqueued tags. Not search-form/comment-form/comment-list/gallery/caption — none of those are in use.
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'disable-custom-colors' );

	// Rename/extend per project.
	register_nav_menus(
		array(
			'primary' => __( 'Primary Menu', 'cb-peoplesafe2027' ),
			'footer'  => __( 'Footer Menu', 'cb-peoplesafe2027' ),
		)
	);
}
add_action( 'after_setup_theme', 'cb_peoplesafe2027_setup' );

/**
 * Turn off the block editor's Notes feature (WP core's "collaboration"
 * commenting UI — the "Add note" block menu item, Ctrl+Alt+M, and its
 * sidebar). It's on by default for post/page via 'editor' => array( 'notes'
 * => true ) in wp-includes/post.php's core post type registration, run on
 * init at priority 0 — this overrides it on the normal init priority for
 * every registered post type, not just post/page, so it also covers any
 * custom post type that inherits the same 'editor' support default. No
 * project use for it identified so far; re-enable per post type with
 * add_post_type_support( $post_type, 'editor', array( 'notes' => true ) )
 * if one comes up.
 *
 * @return void
 */
function cb_peoplesafe2027_disable_editor_notes() {
	foreach ( get_post_types( array(), 'names' ) as $post_type ) {
		if ( post_type_supports( $post_type, 'editor' ) ) {
			add_post_type_support( $post_type, 'editor', array( 'notes' => false ) );
		}
	}
}
add_action( 'init', 'cb_peoplesafe2027_disable_editor_notes', 20 );
