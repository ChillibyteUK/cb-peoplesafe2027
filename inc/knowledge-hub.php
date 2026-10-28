<?php
/**
 * The Knowledge Hub page (/uk/resources/): a search over everything, a tile for each kind of content with how many
 * there are, the latest across blogs, news, guides and whitepapers, then a row of the newest of the main kinds.
 *
 * It is drawn by archive-resource.php when the path is the resources base itself (not /guides/ and so on). The
 * introduction and the search title and description are in Site-Wide Settings.
 *
 * @package cb-peoplesafe2027
 */

defined( 'ABSPATH' ) || exit;

/**
 * The kinds of content the hub links to, in tile order: key => label, path under the region, post type, and the term
 * that picks it out.
 *
 * @return array[]
 */
function cb_peoplesafe2027_hub_types() {
	return array(
		'blogs'        => array( __( 'Blogs', 'cb-peoplesafe2027' ), 'blogs/', 'post', '' ),
		'news'         => array( __( 'News', 'cb-peoplesafe2027' ), 'news/', 'post', '' ),
		'guides'       => array( __( 'Guides', 'cb-peoplesafe2027' ), 'guides/', 'resource', 'guide' ),
		'whitepapers'  => array( __( 'Whitepapers', 'cb-peoplesafe2027' ), 'whitepapers/', 'resource', 'whitepaper' ),
		'brochures'    => array( __( 'Brochures', 'cb-peoplesafe2027' ), 'brochures/', 'resource', 'brochure' ),
		'infographics' => array( __( 'Infographics', 'cb-peoplesafe2027' ), 'infographics/', 'resource', 'infographic' ),
		'video'        => array( __( 'Videos', 'cb-peoplesafe2027' ), 'video/', 'resource', 'video' ),
		'case-studies' => array( __( 'Case studies', 'cb-peoplesafe2027' ), 'case-studies/', 'case_study', '' ),
		'stories'      => array( __( 'Real life stories', 'cb-peoplesafe2027' ), 'stories/', 'case_study', 'user-story' ),
	);
}

/**
 * Query arguments for the newest of one kind of content in a region.
 *
 * @param string $key    A key of cb_peoplesafe2027_hub_types().
 * @param string $region Region slug.
 * @param int    $count  How many.
 * @return array
 */
function cb_peoplesafe2027_hub_query_args( $key, $region, $count ) {
	$types = cb_peoplesafe2027_hub_types();
	$type  = $types[ $key ];
	$args  = array_merge(
		cb_peoplesafe2027_posts_index_args( 'news' === $key ? 'news' : 'blogs', $region ),
		array(
			'post_type'      => $type[2],
			'posts_per_page' => $count,
			'orderby'        => 'date',
			'order'          => 'DESC',
		)
	);

	if ( 'post' !== $type[2] ) {
		unset( $args['category__in'], $args['category__not_in'] );
	}

	if ( 'resource' === $type[2] ) {
		$args['tax_query']  = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- a few small, fixed queries.
			array(
				'taxonomy' => 'resource_type',
				'field'    => 'term_id',
				'terms'    => array( cb_peoplesafe2027_region_term_id( $type[3], 'resource_type', $region ) ),
			),
		);
		$args['meta_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- as above.
			'relation' => 'OR',
			array(
				'key'     => 'cb_hide_from_listing',
				'compare' => 'NOT EXISTS',
			),
			array(
				'key'     => 'cb_hide_from_listing',
				'value'   => '1',
				'compare' => '!=',
			),
		);
	}

	if ( 'case_study' === $type[2] ) {
		$story              = cb_peoplesafe2027_region_term_id( 'user-story', 'case_study_type', $region );
		$args['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- as above.
			array(
				'taxonomy' => 'case_study_type',
				'field'    => 'term_id',
				'terms'    => array( $story ),
				'operator' => 'stories' === $key ? 'IN' : 'NOT IN',
			),
		);
	}

	return $args;
}

/**
 * A card for a row: the image with a curved bottom edge (as on the product cards), then the title.
 *
 * @param int    $post_id Post ID.
 * @param string $key     The kind's key (video adds a play button).
 * @return string HTML.
 */
function cb_peoplesafe2027_hub_card( $post_id, $key ) {
	ob_start();
	?>
	<article class="hub-card hub-card--<?php echo esc_attr( $key ); ?>">
		<div class="hub-card__media">
			<?php echo get_the_post_thumbnail( $post_id, 'large', array( 'alt' => '', 'loading' => 'lazy' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core image markup. ?>
			<?php if ( 'video' === $key ) { ?>
				<span class="case-hub__play" aria-hidden="true"></span>
			<?php } ?>
		</div>
		<div class="hub-card__body">
			<h3 class="hub-card__title"><a class="hub-card__link" href="<?php echo esc_url( get_permalink( $post_id ) ); ?>"><?php echo esc_html( wp_strip_all_tags( get_the_title( $post_id ) ) ); ?></a></h3>
		</div>
	</article>
	<?php
	return ob_get_clean();
}

/**
 * The newest items across blogs, news, guides and whitepapers, newest first.
 *
 * @param string $region Region slug.
 * @param int    $count  How many.
 * @return int[] Post IDs.
 */
function cb_peoplesafe2027_hub_latest_ids( $region, $count ) {
	$found = array();

	foreach ( array( 'blogs', 'news', 'guides', 'whitepapers' ) as $key ) {
		$args           = cb_peoplesafe2027_hub_query_args( $key, $region, $count );
		$args['fields'] = 'ids';

		foreach ( ( new WP_Query( $args ) )->posts as $id ) {
			$found[ $id ] = strtotime( get_post_field( 'post_date', $id ) );
		}
	}

	arsort( $found );

	return array_slice( array_keys( $found ), 0, $count );
}

/**
 * Draw the hub's body: the search, the tiles, the latest and the rows. The Hero is drawn by the caller.
 *
 * @param string $region Region slug.
 * @return void
 */
function cb_peoplesafe2027_render_knowledge_hub( $region ) {
	$root  = trailingslashit( cb_peoplesafe2027_region_url( $region ) );
	$types = cb_peoplesafe2027_hub_types();
	?>
	<section
		class="case-hub posts-index knowledge-hub__search"
		data-posts-index
		data-posts-search-only
		data-posts-hide=".knowledge-hub__sections"
		data-endpoint="<?php echo esc_url( rest_url( 'cb/v1/posts' ) ); ?>"
		data-stream="all"
		data-region="<?php echo esc_attr( $region ); ?>"
		data-category=""
		data-loaded="0"
		data-total="0"
	>
		<div class="container">
			<div class="posts-index__search">
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true" focusable="false"><circle cx="11" cy="11" r="7.5"/><path d="m16.5 16.5 4.5 4.5"/></svg>
				<label class="visually-hidden" for="posts-index-search"><?php esc_html_e( 'Search the Knowledge Hub', 'cb-peoplesafe2027' ); ?></label>
				<input id="posts-index-search" type="search" placeholder="<?php esc_attr_e( 'Search everything: blogs, guides, videos, case studies and more', 'cb-peoplesafe2027' ); ?>" autocomplete="off" data-posts-search>
			</div>
			<p class="visually-hidden" role="status" data-posts-status></p>
			<div class="posts-index__grid" data-posts-grid hidden></div>
			<p class="posts-index__empty" data-posts-empty hidden><?php esc_html_e( 'Nothing found. Try different keywords.', 'cb-peoplesafe2027' ); ?></p>
			<button class="button button--outline posts-index__more" type="button" data-posts-more hidden><?php esc_html_e( 'Load more', 'cb-peoplesafe2027' ); ?></button>
		</div>
	</section>

	<div class="knowledge-hub__sections">
		<section class="knowledge-hub__types">
			<div class="container">
				<nav class="knowledge-hub__tiles" aria-label="<?php esc_attr_e( 'Browse by type', 'cb-peoplesafe2027' ); ?>">
					<span class="knowledge-hub__tiles-label"><?php esc_html_e( 'Browse by type', 'cb-peoplesafe2027' ); ?></span>
					<?php
					foreach ( $types as $key => $type ) {
						$count = ( new WP_Query( array_merge( cb_peoplesafe2027_hub_query_args( $key, $region, 1 ), array( 'fields' => 'ids' ) ) ) )->found_posts;
						?>
						<a class="knowledge-hub__tile" href="<?php echo esc_url( $root . $type[1] ); ?>"><?php echo esc_html( $type[0] ); ?> <span class="knowledge-hub__tile-count"><?php echo esc_html( number_format_i18n( $count ) ); ?></span></a>
					<?php } ?>
				</nav>
			</div>
		</section>

		<section class="knowledge-hub__latest">
			<div class="container">
				<h2 class="knowledge-hub__heading"><?php esc_html_e( 'Latest', 'cb-peoplesafe2027' ); ?></h2>
				<div class="posts-index__grid">
					<?php echo cb_peoplesafe2027_post_cards( cb_peoplesafe2027_hub_latest_ids( $region, 3 ), true, 'all' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built in cb_peoplesafe2027_post_card(). ?>
				</div>
			</div>
		</section>

		<?php
		foreach ( array( 'guides', 'video', 'infographics', 'whitepapers', 'case-studies' ) as $key ) {
			$args           = cb_peoplesafe2027_hub_query_args( $key, $region, 4 );
			$args['fields'] = 'ids';
			$ids            = ( new WP_Query( $args ) )->posts;

			if ( ! $ids ) {
				continue;
			}
			?>
			<section class="knowledge-hub__row">
				<div class="container">
					<div class="knowledge-hub__row-header">
						<h2 class="knowledge-hub__heading"><?php echo esc_html( $types[ $key ][0] ); ?></h2>
						<a class="knowledge-hub__all" href="<?php echo esc_url( $root . $types[ $key ][1] ); ?>"><?php esc_html_e( 'View all', 'cb-peoplesafe2027' ); ?> <span class="visually-hidden"><?php echo esc_html( mb_strtolower( $types[ $key ][0] ) ); ?></span> &rarr;</a>
					</div>
					<div class="knowledge-hub__cards">
						<?php
						foreach ( $ids as $id ) {
							echo cb_peoplesafe2027_hub_card( $id, $key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built in cb_peoplesafe2027_hub_card().
						}
						?>
					</div>
				</div>
			</section>
		<?php } ?>

		<?php
		echo do_blocks( '<!-- wp:cb-peoplesafe2027/newsletter-signup ' . wp_json_encode( array( 'imageUrl' => get_theme_file_uri( 'img/newsletter-signup.png' ) ) ) . ' /-->' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- block output.
		?>
	</div>
	<?php
}

/**
 * Whether the request is the Knowledge Hub itself (the resources base, not one kind of resource).
 *
 * @return bool
 */
function cb_peoplesafe2027_is_knowledge_hub() {
	return is_post_type_archive( 'resource' ) && 'resources' === get_query_var( 'cb_term_base' );
}

/**
 * Title and description for the hub: from Site-Wide Settings, or a plain title.
 */
add_filter(
	'wpseo_title',
	function ( $title ) {
		if ( ! cb_peoplesafe2027_is_knowledge_hub() ) {
			return $title;
		}

		return cb_peoplesafe2027_get_setting( 'knowledge_hub_seo_title', __( 'Knowledge Hub', 'cb-peoplesafe2027' ) . ' - ' . get_bloginfo( 'name' ) );
	}
);
add_filter(
	'wpseo_metadesc',
	function ( $desc ) {
		return cb_peoplesafe2027_is_knowledge_hub() ? cb_peoplesafe2027_get_setting( 'knowledge_hub_seo_description', $desc ) : $desc;
	}
);

/**
 * Schema for the hub: a CollectionPage whose list is the kinds of content, each with its address.
 *
 * @param array[] $graph   Yoast's schema pieces.
 * @param object  $context Yoast's context.
 * @return array[]
 */
add_filter(
	'wpseo_schema_graph',
	function ( $graph, $context ) {
		if ( ! cb_peoplesafe2027_is_knowledge_hub() ) {
			return $graph;
		}

		$root     = trailingslashit( cb_peoplesafe2027_region_url( cb_peoplesafe2027_request_region() ) );
		$items    = array();
		$position = 0;

		foreach ( cb_peoplesafe2027_hub_types() as $type ) {
			$items[] = array(
				'@type'    => 'ListItem',
				'position' => ++$position,
				'name'     => $type[0],
				'url'      => $root . $type[1],
			);
		}

		$page_id = trailingslashit( $context->canonical ) . '#webpage';
		$found   = false;

		foreach ( $graph as $key => $piece ) {
			if ( isset( $piece['@type'] ) && ( in_array( 'WebPage', (array) $piece['@type'], true ) || in_array( 'CollectionPage', (array) $piece['@type'], true ) ) ) {
				$graph[ $key ]['@type']      = 'CollectionPage';
				$graph[ $key ]['mainEntity'] = array( '@id' => $piece['@id'] . '#types' );
				$page_id                     = $piece['@id'];
				$found                       = true;
			}
		}

		if ( ! $found ) {
			$graph[] = array(
				'@type'      => 'CollectionPage',
				'@id'        => $page_id,
				'url'        => $context->canonical,
				'name'       => wp_get_document_title(),
				'isPartOf'   => array( '@id' => trailingslashit( $context->site_url ) . '#website' ),
				'inLanguage' => get_bloginfo( 'language' ),
				'mainEntity' => array( '@id' => $page_id . '#types' ),
			);
		}

		$graph[] = array(
			'@type'           => 'ItemList',
			'@id'             => $page_id . '#types',
			'name'            => __( 'Knowledge Hub', 'cb-peoplesafe2027' ),
			'itemListElement' => $items,
		);

		return $graph;
	},
	13,
	2
);

/**
 * A body class on the hub, so its Hero can be given less space under it.
 */
add_filter(
	'body_class',
	function ( $classes ) {
		if ( cb_peoplesafe2027_is_knowledge_hub() ) {
			$classes[] = 'knowledge-hub-page';
		}

		return $classes;
	}
);
