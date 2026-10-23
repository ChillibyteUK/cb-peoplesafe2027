<?php
/**
 * Trustpilot: the widget definitions live in Site-Wide Settings (Trustpilot
 * tab) so they are controlled centrally. The Trustpilot widget block only
 * picks one by name.
 *
 * @package cb-peoplesafe2027
 */

defined( 'ABSPATH' ) || exit;

/**
 * The configured widgets, keyed by a slug made from each one's name.
 *
 * @return array[] Each has name, template_id, token, height, stars, review_languages, card.
 */
function cb_peoplesafe2027_trustpilot_widgets() {
	$widgets = array();

	foreach ( cb_peoplesafe2027_get_repeater_setting( 'trustpilot_widgets' ) as $row ) {
		$name = isset( $row['name'] ) ? trim( (string) $row['name'] ) : '';

		if ( '' === $name || empty( $row['template_id'] ) || empty( $row['token'] ) ) {
			continue;
		}

		$widgets[ sanitize_title( $name ) ] = array(
			'name'             => $name,
			'template_id'      => trim( (string) $row['template_id'] ),
			'token'            => trim( (string) $row['token'] ),
			'height'           => ! empty( $row['height'] ) ? trim( (string) $row['height'] ) : '',
			'stars'            => ! empty( $row['stars'] ) ? trim( (string) $row['stars'] ) : '',
			'review_languages' => ! empty( $row['review_languages'] ) ? trim( (string) $row['review_languages'] ) : '',
			'card'             => ! empty( $row['card'] ) && in_array( strtolower( trim( (string) $row['card'] ) ), array( 'yes', 'y', '1', 'true', 'on' ), true ),
		);
	}

	return $widgets;
}

/**
 * Hand the block editor the list of widget names for the block's dropdown.
 *
 * @return void
 */
function cb_peoplesafe2027_trustpilot_editor_data() {
	$options = array();

	foreach ( cb_peoplesafe2027_trustpilot_widgets() as $slug => $widget ) {
		$options[] = array(
			'value' => $slug,
			'label' => $widget['name'],
		);
	}

	wp_register_script( 'cb-peoplesafe2027-trustpilot-data', false, array(), null, false ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion -- inline data only.
	wp_enqueue_script( 'cb-peoplesafe2027-trustpilot-data' );
	wp_add_inline_script( 'cb-peoplesafe2027-trustpilot-data', 'window.cbPeoplesafe2027Trustpilot = ' . wp_json_encode( $options ) . ';' );
}
add_action( 'enqueue_block_editor_assets', 'cb_peoplesafe2027_trustpilot_editor_data' );
