<?php
/**
 * Listing intros: the words and the SEO fields of a listing page (/uk/accreditations/, /uk/blogs/ and so on).
 *
 * A listing has no page to edit, so each has a Listing intro post (its own post type, with no address): the intro is
 * written in the block editor, and Yoast's box on it holds the SEO title and description. The listing draws the
 * intro's blocks under its Hero, and takes its search result title and description from the same entry. Polylang gives
 * each region its own entry; the Listing box on it says which listing it is for.
 *
 * @package cb-peoplesafe2027
 */

defined( 'ABSPATH' ) || exit;

/**
 * The listings that have an intro: key => title.
 *
 * @return string[]
 */
function cb_peoplesafe2027_listings() {
	return array(
		'knowledge-hub' => __( 'Knowledge Hub', 'cb-peoplesafe2027' ),
		'blogs'         => __( 'Blogs', 'cb-peoplesafe2027' ),
		'news'          => __( 'News', 'cb-peoplesafe2027' ),
		'guides'        => __( 'Guides', 'cb-peoplesafe2027' ),
		'whitepapers'   => __( 'Whitepapers', 'cb-peoplesafe2027' ),
		'brochures'     => __( 'Brochures', 'cb-peoplesafe2027' ),
		'infographics'  => __( 'Infographics', 'cb-peoplesafe2027' ),
		'video'         => __( 'Videos', 'cb-peoplesafe2027' ),
		'case-studies'  => __( 'Case studies', 'cb-peoplesafe2027' ),
		'stories'       => __( 'Real life stories', 'cb-peoplesafe2027' ),
		'partnerships'  => __( 'Partnerships', 'cb-peoplesafe2027' ),
		'accreditations' => __( 'Accreditations', 'cb-peoplesafe2027' ),
	);
}

/**
 * The listing being shown, by key, or an empty string when the request is not one of them.
 *
 * @return string
 */
function cb_peoplesafe2027_current_listing() {
	if ( is_singular() ) {
		return '';
	}

	$stream = (string) get_query_var( 'cb_post_stream' );

	if ( $stream ) {
		return $stream;
	}

	$base = (string) get_query_var( 'cb_term_base' );

	if ( ! $base ) {
		return '';
	}

	if ( is_post_type_archive( 'resource' ) ) {
		return 'resources' === $base ? 'knowledge-hub' : $base;
	}

	if ( is_post_type_archive( array( 'case_study', 'partner' ) ) ) {
		return $base;
	}

	return '';
}

/**
 * The Listing intro entry for a listing in a region: its ID, or 0.
 *
 * @param string $key    A key of cb_peoplesafe2027_listings().
 * @param string $region Region slug. Defaults to the one requested.
 * @return int
 */
function cb_peoplesafe2027_listing_intro_id( $key, $region = '' ) {
	$region = $region ? $region : cb_peoplesafe2027_request_region();
	$found  = get_posts(
		array(
			'post_type'        => 'listing_intro',
			'post_status'      => 'publish',
			'numberposts'      => 1,
			'fields'           => 'ids',
			'lang'             => $region,
			'meta_key'         => 'cb_listing', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- a few entries, one query.
			'meta_value'       => $key, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value -- as above.
			'suppress_filters' => false,
		)
	);

	return $found ? (int) $found[0] : 0;
}

/**
 * The intro's blocks for a listing, drawn: empty when there is no entry or it has no content.
 *
 * @param string $key A key of cb_peoplesafe2027_listings().
 * @return string HTML.
 */
function cb_peoplesafe2027_listing_intro_html( $key ) {
	$id = cb_peoplesafe2027_listing_intro_id( $key );

	if ( ! $id ) {
		return '';
	}

	$content = trim( (string) get_post_field( 'post_content', $id ) );

	return '' === $content ? '' : '<div class="listing-intro">' . do_blocks( $content ) . '</div>';
}

/**
 * The Listing box: which listing an entry is for.
 *
 * @return void
 */
function cb_peoplesafe2027_listing_box() {
	add_meta_box(
		'cb-listing',
		'Listing',
		function ( $post ) {
			wp_nonce_field( 'cb_listing', 'cb_listing_nonce' );
			$current = (string) get_post_meta( $post->ID, 'cb_listing', true );
			?>
			<p><label for="cb_listing" class="screen-reader-text">Listing</label>
			<select id="cb_listing" name="cb_listing" style="width:100%">
				<option value="">Choose a listing</option>
				<?php foreach ( cb_peoplesafe2027_listings() as $key => $title ) { ?>
					<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $current, $key ); ?>><?php echo esc_html( $title ); ?></option>
				<?php } ?>
			</select></p>
			<p class="description">The listing page this introduces. The blocks appear under its title, and the SEO title and description below are the listing's.</p>
			<?php
		},
		'listing_intro',
		'side',
		'high'
	);
}
add_action( 'add_meta_boxes', 'cb_peoplesafe2027_listing_box' );

/**
 * Save the Listing choice.
 *
 * @param int $post_id Post ID.
 * @return void
 */
function cb_peoplesafe2027_save_listing( $post_id ) {
	if ( ! isset( $_POST['cb_listing_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['cb_listing_nonce'] ) ), 'cb_listing' ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$key = isset( $_POST['cb_listing'] ) ? sanitize_key( wp_unslash( $_POST['cb_listing'] ) ) : '';

	if ( $key && isset( cb_peoplesafe2027_listings()[ $key ] ) ) {
		update_post_meta( $post_id, 'cb_listing', $key );
	} else {
		delete_post_meta( $post_id, 'cb_listing' );
	}
}
add_action( 'save_post_listing_intro', 'cb_peoplesafe2027_save_listing' );

/**
 * A new translation of an entry is for the same listing.
 *
 * @param string[] $keys Meta keys Polylang copies.
 * @return string[]
 */
function cb_peoplesafe2027_listing_copy_meta( $keys ) {
	$keys[] = 'cb_listing';

	return $keys;
}
add_filter( 'pll_copy_post_metas', 'cb_peoplesafe2027_listing_copy_meta' );

/**
 * A "Shown on" column in the Listing intros list, with a link to the listing.
 *
 * @param string[] $columns Columns.
 * @return string[]
 */
function cb_peoplesafe2027_listing_columns( $columns ) {
	$columns['cb_listing'] = 'Listing';

	return $columns;
}
add_filter( 'manage_listing_intro_posts_columns', 'cb_peoplesafe2027_listing_columns' );

/**
 * Fill the Listing column.
 *
 * @param string $column  Column.
 * @param int    $post_id Post ID.
 * @return void
 */
function cb_peoplesafe2027_listing_column( $column, $post_id ) {
	if ( 'cb_listing' !== $column ) {
		return;
	}

	$key      = (string) get_post_meta( $post_id, 'cb_listing', true );
	$listings = cb_peoplesafe2027_listings();

	if ( ! isset( $listings[ $key ] ) ) {
		echo 'Not set';
		return;
	}

	$lang = function_exists( 'pll_get_post_language' ) ? pll_get_post_language( $post_id ) : '';
	$base = array(
		'knowledge-hub' => 'resources/',
		'case-studies'  => 'case-studies/',
	);
	$path = isset( $base[ $key ] ) ? $base[ $key ] : $key . '/';
	$url  = $lang ? trailingslashit( cb_peoplesafe2027_region_url( $lang ) ) . $path : '';

	echo $url ? '<a href="' . esc_url( $url ) . '" target="_blank" rel="noopener">' . esc_html( $listings[ $key ] ) . '</a>' : esc_html( $listings[ $key ] );
}
add_action( 'manage_listing_intro_posts_custom_column', 'cb_peoplesafe2027_listing_column', 10, 2 );

/**
 * The SEO title for a listing: the entry's Yoast title (with Yoast's variables filled in), or the listing's name and
 * the site name.
 *
 * @param string $value Yoast's title.
 * @return string
 */
function cb_peoplesafe2027_listing_seo_title( $value ) {
	$key = cb_peoplesafe2027_current_listing();

	if ( ! $key ) {
		return $value;
	}

	$id    = cb_peoplesafe2027_listing_intro_id( $key );
	$title = $id ? (string) get_post_meta( $id, '_yoast_wpseo_title', true ) : '';

	if ( '' !== $title ) {
		return function_exists( 'wpseo_replace_vars' ) ? wpseo_replace_vars( $title, get_post( $id ) ) : $title;
	}

	return cb_peoplesafe2027_listings()[ $key ] . ' - ' . get_bloginfo( 'name' );
}
add_filter( 'wpseo_title', 'cb_peoplesafe2027_listing_seo_title' );
add_filter( 'wpseo_opengraph_title', 'cb_peoplesafe2027_listing_seo_title' );

/**
 * The SEO description for a listing: the entry's Yoast description.
 *
 * @param string $value Yoast's description.
 * @return string
 */
function cb_peoplesafe2027_listing_seo_description( $value ) {
	$key = cb_peoplesafe2027_current_listing();

	if ( ! $key ) {
		return $value;
	}

	$id   = cb_peoplesafe2027_listing_intro_id( $key );
	$desc = $id ? (string) get_post_meta( $id, '_yoast_wpseo_metadesc', true ) : '';

	if ( '' === $desc ) {
		return '';
	}

	return function_exists( 'wpseo_replace_vars' ) ? wpseo_replace_vars( $desc, get_post( $id ) ) : $desc;
}
add_filter( 'wpseo_metadesc', 'cb_peoplesafe2027_listing_seo_description' );
add_filter( 'wpseo_opengraph_desc', 'cb_peoplesafe2027_listing_seo_description' );

/**
 * Listing intros are translatable: each region has its own, with its own SEO fields.
 *
 * @param string[] $types Translated post types.
 * @return string[]
 */
function cb_peoplesafe2027_listing_translatable( $types ) {
	$types['listing_intro'] = 'listing_intro';

	return $types;
}
add_filter( 'pll_get_post_types', 'cb_peoplesafe2027_listing_translatable' );
