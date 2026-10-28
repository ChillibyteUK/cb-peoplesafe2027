<?php
/**
 * Custom Taxonomies Registration
 *
 * Duplicate the register_taxonomy() call below (commented out) as a
 * starting point for a new taxonomy — nothing is registered by default.
 *
 * @package cb-peoplesafe2027
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register custom taxonomies for the theme.
 *
 * @return void
 */
function cb_peoplesafe2027_register_theme_taxonomies() {

	/*
	register_taxonomy(
		'service',
		array( 'case_study', 'post' ),
		array(
			'labels'             => array(
				'name'          => 'Services',
				'singular_name' => 'Service',
			),
			'public'             => true,
			'publicly_queryable' => true,
			'hierarchical'       => true,
			'show_ui'            => true,
			'show_in_nav_menus'  => true,
			'show_tagcloud'      => false,
			'show_in_quick_edit' => true,
			'show_admin_column'  => true,
			'show_in_rest'       => true,
			'rewrite'            => false,
		)
	);
	*/

	/*
	 * Project taxonomies. Only resource_type and jurisdiction have public
	 * archives: resource_type is routed by routing.php (rewrite false here),
	 * and jurisdiction has its own /jurisdiction/ base, not nested under
	 * /legislation/ (that nesting 404s). The others are admin and query
	 * filters with no URLs of their own, which also keeps them from colliding
	 * with pages (for example /sectors/ is a page).
	 */
	$taxonomies = array(
		'product_type'    => array( 'Product types', 'Product type', 'product', false ),
		'sector'          => array( 'Sectors', 'Sector', 'case_study', false ),
		'case_study_type' => array( 'Case study types', 'Case study type', 'case_study', false ), // Includes User story.
		'incident'        => array( 'Incident types', 'Incident type', 'case_study', false ), // What a user story was about: verbal abuse, medical, entrapment.
		'resource_type'   => array( 'Resource types', 'Resource type', 'resource', true ),
		'jurisdiction'    => array( 'Jurisdictions', 'Jurisdiction', 'legislation', true ), // Country, then state or province.
		'partner_type'    => array( 'Partner types', 'Partner type', 'partner', false ),
		'career_type'     => array( 'Career types', 'Career type', 'career', false ),
		'landing_type'    => array( 'Landing types', 'Landing type', 'landing_page', false ), // PPC, thank-you, campaign, test.
		'team'            => array( 'Teams', 'Team', 'person', false ),
	);

	foreach ( $taxonomies as $name => $tax ) {
		$rewrite = false;

		if ( 'jurisdiction' === $name ) {
			$rewrite = array(
				'slug'         => 'jurisdiction',
				'with_front'   => false,
				'hierarchical' => true,
			);
		}

		register_taxonomy(
			$name,
			$tax[2],
			array(
				'labels'             => array(
					'name'          => $tax[0],
					'singular_name' => $tax[1],
				),
				'public'             => $tax[3],
				'publicly_queryable' => $tax[3],
				'hierarchical'       => true,
				'show_ui'            => true,
				'show_in_nav_menus'  => $tax[3],
				'show_admin_column'  => true,
				'show_in_rest'       => true,
				'rewrite'            => $rewrite,
				'query_var'          => $tax[3],
			)
		);
	}
}
add_action( 'init', 'cb_peoplesafe2027_register_theme_taxonomies' );

/**
 * Make the Incident types translatable (one set of terms per region).
 *
 * The other taxonomies are ticked in Polylang's settings; this one is added in
 * code so it works on every site without a settings step.
 *
 * @param string[] $taxonomies Translatable taxonomies.
 * @return string[]
 */
function cb_peoplesafe2027_translatable_incident( $taxonomies ) {
	$taxonomies['incident'] = 'incident';

	return $taxonomies;
}
add_filter( 'pll_get_taxonomies', 'cb_peoplesafe2027_translatable_incident' );
