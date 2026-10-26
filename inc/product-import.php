<?php
/**
 * One-off: bring the products of the 2024 site across.
 *
 * Tools, Import products creates each product from inc/import/products/*.json
 * (the live page converted to this theme's blocks): its blocks, product types,
 * order, featured image and meta description. Images are downloaded from the
 * live site into the media library, once each. A product that already exists
 * (same slug) is left alone. It works a few products per click, so a slow
 * server does not time out. Delete inc/import/ and this file once the products
 * are in.
 *
 * @package cb-peoplesafe2027
 */

defined( 'ABSPATH' ) || exit;

/**
 * Add an image to the media library from its live URL, once.
 *
 * @param string $url Live image URL.
 * @return int Attachment ID, or 0.
 */
function cb_peoplesafe2027_import_image( $url ) {
	$existing = get_posts(
		array(
			'post_type'   => 'attachment',
			'post_status' => 'inherit',
			'meta_key'    => '_cb_import_src',
			'meta_value'  => $url,
			'fields'      => 'ids',
			'numberposts' => 1,
		)
	);

	if ( $existing ) {
		return (int) $existing[0];
	}

	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$tmp = download_url( $url, 60 );

	if ( is_wp_error( $tmp ) ) {
		cb_peoplesafe2027_seed_log( 'Image not found: ' . $url );
		return 0;
	}

	$id = media_handle_sideload(
		array(
			'name'     => wp_basename( wp_parse_url( $url, PHP_URL_PATH ) ),
			'tmp_name' => $tmp,
		),
		0
	);

	if ( is_wp_error( $id ) ) {
		wp_delete_file( $tmp );
		cb_peoplesafe2027_seed_log( 'Image failed: ' . $url );
		return 0;
	}

	// Keep the image's own address from taking over a page's address (an attachment with the same slug redirects the page to the file).
	wp_update_post(
		array(
			'ID'        => $id,
			'post_name' => sanitize_title( pathinfo( $url, PATHINFO_FILENAME ) ) . '-image',
		)
	);

	update_post_meta( $id, '_cb_import_src', $url );

	return (int) $id;
}

/**
 * Swap the image placeholders in a product's blocks for media library IDs.
 *
 * @param string $content Block markup with @@img:URL@@ placeholders.
 * @return string
 */
function cb_peoplesafe2027_import_images_in( $content ) {
	return preg_replace_callback(
		'/"@@img:(.*?)@@"/',
		function ( $m ) {
			$url = json_decode( '"' . $m[1] . '"' );

			return (string) ( $url ? cb_peoplesafe2027_import_image( $url ) : 0 );
		},
		$content
	);
}

/**
 * Swap the product placeholders (related products) for post IDs, where the
 * product exists yet. Returns the content and whether any placeholder is left.
 *
 * @param string $content Block markup.
 * @return array{0: string, 1: bool}
 */
function cb_peoplesafe2027_import_products_in( $content ) {
	$left    = false;
	$content = preg_replace_callback(
		'/"@@prod:([a-z0-9-]+)@@"/',
		function ( $m ) use ( &$left ) {
			$found = get_posts(
				array(
					'post_type'   => 'product',
					'name'        => $m[1],
					'post_status' => 'any',
					'numberposts' => 1,
					'fields'      => 'ids',
					'lang'        => function_exists( 'pll_default_language' ) ? pll_default_language() : '',
				)
			);

			if ( $found ) {
				return (string) $found[0];
			}

			$left = true;

			return $m[0];
		},
		$content
	);

	return array( $content, $left );
}

/**
 * Create up to $limit products that do not exist yet.
 *
 * @param int $limit How many to create.
 * @return int How many products are still to do.
 */
function cb_peoplesafe2027_import_products( $limit ) {
	$dir     = CB_PEOPLESAFE2027_DIR . '/inc/import';
	$default = function_exists( 'pll_default_language' ) ? pll_default_language() : '';
	$types   = json_decode( (string) file_get_contents( $dir . '/product-types.json' ), true ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	$term_id = array();

	foreach ( (array) $types as $slug => $name ) {
		$term = get_term_by( 'slug', $slug, 'product_type' );

		if ( ! $term ) {
			$made = wp_insert_term( $name, 'product_type', array( 'slug' => $slug ) );

			if ( is_wp_error( $made ) ) {
				continue;
			}

			if ( $default && function_exists( 'pll_set_term_language' ) ) {
				pll_set_term_language( $made['term_id'], $default );
			}

			$term_id[ $slug ] = (int) $made['term_id'];
			continue;
		}

		$term_id[ $slug ] = (int) $term->term_id;
	}

	$items = array();

	foreach ( glob( $dir . '/products/*.json' ) as $file ) {
		$data = json_decode( (string) file_get_contents( $file ), true ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

		if ( $data ) {
			$items[ (int) $data['menu_order'] ] = $data;
		}
	}

	ksort( $items );

	$todo = 0;

	foreach ( $items as $data ) {
		$exists = get_posts(
			array(
				'post_type'   => 'product',
				'name'        => $data['slug'],
				'post_status' => 'any',
				'numberposts' => 1,
				'fields'      => 'ids',
				'lang'        => $default,
			)
		);

		if ( $exists ) {
			continue;
		}

		if ( $limit <= 0 ) {
			++$todo;
			continue;
		}

		--$limit;

		$content = cb_peoplesafe2027_import_images_in( $data['content'] );
		$id      = wp_insert_post(
			array(
				'post_type'    => 'product',
				'post_status'  => 'publish',
				'post_name'    => $data['slug'],
				'post_title'   => $data['title'],
				'menu_order'   => (int) $data['menu_order'],
				'post_content' => wp_slash( $content ),
			),
			true
		);

		if ( is_wp_error( $id ) ) {
			cb_peoplesafe2027_seed_log( $data['slug'] . ': ' . $id->get_error_message() );
			continue;
		}

		if ( $default ) {
			pll_set_post_language( $id, $default );
		}

		$ids = array();

		foreach ( (array) $data['types'] as $slug ) {
			if ( isset( $term_id[ $slug ] ) ) {
				$ids[] = $term_id[ $slug ];
			}
		}

		wp_set_object_terms( $id, $ids, 'product_type' );

		if ( ! empty( $data['og'] ) ) {
			$thumb = cb_peoplesafe2027_import_image( $data['og'] );

			if ( $thumb ) {
				set_post_thumbnail( $id, $thumb );
			}
		}

		if ( ! empty( $data['desc'] ) ) {
			update_post_meta( $id, '_yoast_wpseo_metadesc', $data['desc'] );
		}

		cb_peoplesafe2027_seed_log( 'Created ' . $data['slug'] );
	}

	// Related products can only be linked once the products they point at exist.
	foreach ( get_posts(
		array(
			'post_type'   => 'product',
			'post_status' => 'any',
			'numberposts' => -1,
			's'           => '@@prod:',
			'lang'        => $default,
		)
	) as $product ) {
		list( $updated, $left ) = cb_peoplesafe2027_import_products_in( $product->post_content );

		if ( $updated !== $product->post_content ) {
			wp_update_post(
				array(
					'ID'           => $product->ID,
					'post_content' => wp_slash( $updated ),
				)
			);
		}

		if ( $left ) {
			cb_peoplesafe2027_seed_log( $product->post_name . ': some related products are not imported yet' );
		}
	}

	return $todo;
}

/**
 * Tools, Import products.
 *
 * @return void
 */
function cb_peoplesafe2027_import_admin_menu() {
	add_management_page( 'Import products', 'Import products', 'manage_options', 'cb-import-products', 'cb_peoplesafe2027_import_admin_page' );
}
add_action( 'admin_menu', 'cb_peoplesafe2027_import_admin_menu' );

/**
 * The Import products page.
 *
 * @return void
 */
function cb_peoplesafe2027_import_admin_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$GLOBALS['cb_peoplesafe2027_seed_log'] = array();
	$left                                  = null;

	if ( isset( $_POST['cb_import_products'] ) && check_admin_referer( 'cb_import_products' ) ) {
		set_time_limit( 300 );
		$left = cb_peoplesafe2027_import_products( 3 );
	}
	?>
	<div class="wrap">
		<h1>Import products</h1>
		<p>Creates the products of the 2024 site, three at a time: their blocks, types, order, featured image and meta description. Images are downloaded from the live site. Products that already exist are left alone. Press the button until nothing is left.</p>
		<?php if ( ! empty( $GLOBALS['cb_peoplesafe2027_seed_log'] ) ) { ?>
			<div class="notice notice-success"><p><?php echo implode( '<br>', array_map( 'esc_html', $GLOBALS['cb_peoplesafe2027_seed_log'] ) ); ?></p></div>
		<?php } ?>
		<?php if ( null !== $left ) { ?>
			<p><strong><?php echo esc_html( $left ? $left . ' products still to import.' : 'All products are imported.' ); ?></strong></p>
		<?php } ?>
		<form method="post">
			<?php wp_nonce_field( 'cb_import_products' ); ?>
			<p><button type="submit" name="cb_import_products" value="1" class="button button-primary">Import the next three products</button></p>
		</form>
	</div>
	<?php
}
