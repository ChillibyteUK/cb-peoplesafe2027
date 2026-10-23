<?php
/**
 * Sectors: the "sector group" setting on each page under the Sectors page.
 *
 * Each sector page (the pages directly under the Sectors page) is public,
 * private or both; the Sector cards block's filter buttons use it. It is a
 * page setting in the editor's sidebar (blocks/_editor-sector-group), saved as
 * the cb_sector_group meta.
 *
 * @package cb-peoplesafe2027
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the sector group meta, readable and writable in the block editor.
 *
 * @return void
 */
function cb_peoplesafe2027_register_sector_meta() {
	register_post_meta(
		'page',
		'cb_sector_group',
		array(
			'type'              => 'string',
			'single'            => true,
			'default'           => 'public',
			'show_in_rest'      => true,
			'sanitize_callback' => 'cb_peoplesafe2027_sanitize_sector_group',
			'auth_callback'     => function () {
				return current_user_can( 'edit_pages' );
			},
		)
	);
}
add_action( 'init', 'cb_peoplesafe2027_register_sector_meta' );

/**
 * Only the three known values are kept.
 *
 * @param mixed $value Submitted value.
 * @return string
 */
function cb_peoplesafe2027_sanitize_sector_group( $value ) {
	return in_array( $value, array( 'public', 'private', 'both' ), true ) ? $value : 'public';
}

/**
 * Load the sidebar panel's script in the block editor.
 *
 * @return void
 */
function cb_peoplesafe2027_enqueue_sector_group_panel() {
	$asset_file = CB_PEOPLESAFE2027_DIR . '/blocks/_editor-sector-group/build/index.asset.php';

	if ( ! file_exists( $asset_file ) ) {
		return;
	}

	$asset = require $asset_file;

	wp_enqueue_script(
		'cb-peoplesafe2027-sector-group',
		get_template_directory_uri() . '/blocks/_editor-sector-group/build/index.js',
		$asset['dependencies'],
		$asset['version'],
		true
	);
}
add_action( 'enqueue_block_editor_assets', 'cb_peoplesafe2027_enqueue_sector_group_panel' );
