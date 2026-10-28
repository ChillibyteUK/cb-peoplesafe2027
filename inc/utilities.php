<?php
/**
 * Reusable, project-agnostic utility functions — safe to lift verbatim into
 * other projects built on this skeleton. Project-specific helpers (coupled
 * to a project's own field schema or content structure) belong in
 * inc/helpers.php instead — don't create that file until something actually
 * needs it.
 *
 * @package cb-peoplesafe2027
 */

defined( 'ABSPATH' ) || exit;

/**
 * Strip formatting from a UK phone number for use in tel: links.
 *
 * @param string $phone Phone number as entered (spaces, brackets, dashes allowed).
 * @return string
 */
function parse_phone( $phone ) {
	$phone = preg_replace( '/\s+/', '', $phone );
	$phone = preg_replace( '/\(0\)/', '', $phone );
	$phone = preg_replace( '/[\(\)\.]/', '', $phone );
	$phone = preg_replace( '/-/', '', $phone );
	$phone = preg_replace( '/^0/', '+44', $phone );
	return $phone;
}

/**
 * Pluralise a word based on quantity.
 *
 * @param int         $quantity Quantity to check.
 * @param string      $singular Singular form.
 * @param string|null $plural   Explicit plural form, if the default suffix rules don't apply.
 * @return string
 */
function pluralise( $quantity, $singular, $plural = null ) {
	if ( 1 === $quantity || ! strlen( $singular ) ) {
		return $singular;
	}
	if ( null !== $plural ) {
		return $plural;
	}

	$last_letter = strtolower( $singular[ strlen( $singular ) - 1 ] );
	switch ( $last_letter ) {
		case 'y':
			return substr( $singular, 0, -1 ) . 'ies';
		case 's':
			return $singular . 'es';
		default:
			return $singular . 's';
	}
}

/**
 * List available icons from img/icons/ as slug => label pairs.
 *
 * Drop an .svg file into img/icons/ and it appears automatically — no
 * registration step. Useful as the options list for a generated block's
 * "select" field (see add_block.sh) when a block needs an icon picker.
 *
 * @return array Slug => human-readable label pairs.
 */
function get_icon_choices() {
	$choices = array();
	$files   = glob( get_template_directory() . '/img/icons/*.svg' );

	if ( ! $files ) {
		return $choices;
	}

	foreach ( $files as $file ) {
		$slug             = basename( $file, '.svg' );
		$choices[ $slug ] = ucwords( str_replace( array( '-', '_' ), ' ', $slug ) );
	}

	return $choices;
}

/**
 * Inline an SVG icon from img/icons/ by slug.
 *
 * @param string $name Icon slug — matches a filename in img/icons/ without the extension.
 * @return string SVG markup, or an empty string if the icon doesn't exist.
 */
function get_icon( $name ) {
	if ( ! $name ) {
		return '';
	}

	$path = get_template_directory() . '/img/icons/' . basename( $name ) . '.svg';

	if ( ! file_exists( $path ) ) {
		return '';
	}

	return file_get_contents( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
}

/**
 * Estimate reading time for a piece of content.
 *
 * @param string $content          Content to estimate.
 * @param int    $words_per_minute Reading speed assumption.
 * @param bool   $with_gutenberg   Parse content as Gutenberg blocks before stripping tags.
 * @param bool   $formatted        Return a formatted sentence instead of a bare number.
 * @return int|string
 */
function estimate_reading_time_in_minutes( $content = '', $words_per_minute = 300, $with_gutenberg = false, $formatted = false ) {
	if ( $with_gutenberg ) {
		$blocks       = parse_blocks( $content );
		$content_html = '';

		foreach ( $blocks as $block ) {
			$content_html .= render_block( $block );
		}

		$content = $content_html;
	}

	$content = wp_strip_all_tags( $content );

	if ( ! $content ) {
		return 0;
	}

	$words_count = str_word_count( $content );
	$minutes     = ceil( $words_count / $words_per_minute );

	if ( $formatted ) {
		$minutes = '<p class="reading">Estimated reading time ' . $minutes . ' ' . pluralise( $minutes, 'minute' ) . '</p>';
	}

	return $minutes;
}

/**
 * Build breadcrumb items for the current singular view.
 *
 * @param int $post_id Current post ID.
 * @return array<int, array{label: string, url: string}>
 */
function cb_peoplesafe2027_get_breadcrumbs( $post_id = 0 ) {
	$post_id     = $post_id ? (int) $post_id : get_the_ID();
	$breadcrumbs = array(
		array(
			'label' => __( 'Home', 'cb-peoplesafe2027' ),
			// The region's home (/uk/), not the front page's own permalink that Polylang puts in home_url().
			'url'   => ( function_exists( 'cb_peoplesafe2027_region_url' ) && pll_current_language() ) ? cb_peoplesafe2027_region_url( pll_current_language() ) : home_url( '/' ),
		),
	);

	if ( ! $post_id ) {
		// A listing (the case studies archive) has no post: its own title is the last crumb.
		if ( is_post_type_archive() || get_query_var( 'cb_post_stream' ) ) {
			$label = (string) apply_filters( 'cb_peoplesafe2027_archive_breadcrumb', post_type_archive_title( '', false ) );

			// A filtered hub (?sector= or ?incident=) keeps the hub as a link and ends on the term.
			$filter = '';
			$term   = null;

			if ( is_post_type_archive( 'case_study' ) ) {
				// phpcs:disable WordPress.Security.NonceVerification.Recommended -- a public filter, no state changes.
				foreach ( array( 'sector', 'incident' ) as $taxonomy ) {
					if ( isset( $_GET[ $taxonomy ] ) ) {
						$term = get_term_by( 'slug', sanitize_title( wp_unslash( $_GET[ $taxonomy ] ) ), $taxonomy );
					}
				}
				// phpcs:enable WordPress.Security.NonceVerification.Recommended
				$filter = $term && ! is_wp_error( $term ) ? html_entity_decode( $term->name ) : '';
			}

			if ( '' !== $label && ( is_post_type_archive( array( 'case_study', 'resource' ) ) || get_query_var( 'cb_post_stream' ) ) ) {
				$breadcrumbs[] = array(
					'label' => __( 'Knowledge Hub', 'cb-peoplesafe2027' ),
					'url'   => trailingslashit( $breadcrumbs[0]['url'] ) . 'resources/',
				);
			}

			if ( '' !== $label ) {
				$request = isset( $_SERVER['REQUEST_URI'] ) ? preg_replace( '#/page/\d+/?$#', '/', strtok( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ), '?' ) ) : '';

				$breadcrumbs[] = array(
					'label' => $label,
					'url'   => '' !== $filter ? home_url( $request ) : '',
				);

				if ( '' !== $filter ) {
					$breadcrumbs[] = array(
						'label' => $filter,
						'url'   => '',
					);
				}
			}
		}

		return $breadcrumbs;
	}

	if ( 'post' === get_post_type( $post_id ) ) {
		// Home, Knowledge Hub, then Blogs or News.
		$is_news       = function_exists( 'cb_peoplesafe2027_is_news_post' ) && cb_peoplesafe2027_is_news_post( get_post( $post_id ) );
		$breadcrumbs[] = array(
			'label' => __( 'Knowledge Hub', 'cb-peoplesafe2027' ),
			'url'   => trailingslashit( $breadcrumbs[0]['url'] ) . 'resources/',
		);
		$breadcrumbs[] = array(
			'label' => $is_news ? __( 'News', 'cb-peoplesafe2027' ) : __( 'Blogs', 'cb-peoplesafe2027' ),
			'url'   => trailingslashit( $breadcrumbs[0]['url'] ) . ( $is_news ? 'news/' : 'blogs/' ),
		);
	} elseif ( is_page( $post_id ) || 'page' === get_post_type( $post_id ) ) {
		$ancestor_ids = array_reverse( get_post_ancestors( $post_id ) );

		foreach ( $ancestor_ids as $ancestor_id ) {
			$breadcrumbs[] = array(
				'label' => get_the_title( $ancestor_id ),
				'url'   => get_permalink( $ancestor_id ),
			);
		}
	}

	// A custom post type sits under its base (/uk/products/): a crumb for the listing between Home and the item.
	$type = get_post_type( $post_id );

	if ( ! in_array( $type, array( 'page', 'post' ), true ) ) {
		$parent = dirname( untrailingslashit( get_permalink( $post_id ) ) );
		$home   = untrailingslashit( $breadcrumbs[0]['url'] );

		if ( $parent && $parent !== $home && 0 === strpos( $parent, $home ) ) {
			$object = get_post_type_object( $type );

			if ( in_array( $type, array( 'case_study', 'resource' ), true ) ) {
				$breadcrumbs[] = array(
					'label' => __( 'Knowledge Hub', 'cb-peoplesafe2027' ),
					'url'   => trailingslashit( $breadcrumbs[0]['url'] ) . 'resources/',
				);
			}

			$breadcrumbs[] = array(
				'label' => 'stories' === basename( $parent ) ? __( 'Real life stories', 'cb-peoplesafe2027' ) : ( ( $object && $object->has_archive ) ? $object->labels->name : ucfirst( str_replace( '-', ' ', basename( $parent ) ) ) ),
				'url'   => trailingslashit( $parent ),
			);
		}
	}

	$breadcrumbs[] = array(
		'label' => get_the_title( $post_id ),
		'url'   => '',
	);

	return $breadcrumbs;
}

/**
 * Render breadcrumb markup with schema metadata.
 *
 * @param array  $breadcrumbs Breadcrumb items.
 * @param string $class_name  Wrapper class name.
 * @return void
 */
function cb_peoplesafe2027_render_breadcrumbs( $breadcrumbs, $class_name = 'cb-breadcrumbs' ) {
	if ( empty( $breadcrumbs ) || ! is_array( $breadcrumbs ) || is_front_page() ) {
		return;
	}
	?>
	<nav class="<?php echo esc_attr( $class_name ); ?>" aria-label="Breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList">
		<div class="container">
			<ol class="cb-breadcrumbs__list">
				<?php foreach ( $breadcrumbs as $index => $breadcrumb ) { ?>
					<li class="cb-breadcrumbs__item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
						<?php if ( ! empty( $breadcrumb['url'] ) ) { ?>
							<a href="<?php echo esc_url( $breadcrumb['url'] ); ?>" itemprop="item"><span itemprop="name"><?php echo esc_html( $breadcrumb['label'] ); ?></span></a>
						<?php } else { ?>
							<span itemprop="name" aria-current="page"><?php echo esc_html( $breadcrumb['label'] ); ?></span>
						<?php } ?>
						<meta itemprop="position" content="<?php echo esc_attr( $index + 1 ); ?>">
					</li>
				<?php } ?>
			</ol>
		</div>
	</nav>
	<?php
}

/**
 * Build a Vimeo player embed URL from any Vimeo link.
 *
 * Accepts plain (vimeo.com/123456789), channel/group
 * (vimeo.com/channels/staffpicks/123456789), player
 * (player.vimeo.com/video/123456789), and unlisted links carrying the
 * private hash either in the path (vimeo.com/123456789/abcdef1234) or as
 * ?h=abcdef1234. Anything that isn't a Vimeo URL, or has no numeric video
 * ID, returns an empty string — callers use that to fall back to a plain
 * link instead of a modal player.
 *
 * @param string $url URL as entered.
 * @return string Embed URL (autoplay + dnt), or '' if not a Vimeo video link.
 */
function cb_peoplesafe2027_get_vimeo_embed_url( $url ) {
	if ( ! is_string( $url ) || '' === trim( $url ) ) {
		return '';
	}

	$parts = wp_parse_url( $url );

	if ( ! is_array( $parts ) || empty( $parts['host'] ) ) {
		return '';
	}

	$host = strtolower( preg_replace( '/^www\./', '', $parts['host'] ) );

	if ( ! in_array( $host, array( 'vimeo.com', 'player.vimeo.com' ), true ) ) {
		return '';
	}

	if ( empty( $parts['path'] ) || ! preg_match_all( '#/(\d+)(?=/|$)#', $parts['path'], $matches ) ) {
		return '';
	}

	$video_id = end( $matches[1] );
	$hash     = '';

	if ( preg_match( '#/' . $video_id . '/([a-f0-9]+)#i', $parts['path'], $hash_match ) ) {
		$hash = $hash_match[1];
	}

	if ( ! $hash && ! empty( $parts['query'] ) ) {
		parse_str( $parts['query'], $query );

		if ( ! empty( $query['h'] ) && is_string( $query['h'] ) && preg_match( '/^[a-f0-9]+$/i', $query['h'] ) ) {
			$hash = $query['h'];
		}
	}

	// No title, byline, portrait, badge, Vimeo logo or picture-in-picture; the controls take the brand yellow. Logo and colour need a paid Vimeo plan on the video's owner; the player ignores them otherwise.
	$embed_url = 'https://player.vimeo.com/video/' . $video_id . '?autoplay=1&dnt=1&title=0&byline=0&portrait=0&badge=0&vimeo_logo=0&pip=0&color=ffb422';

	if ( $hash ) {
		$embed_url .= '&h=' . $hash;
	}

	return $embed_url;
}

/**
 * Format a byte count for display next to a download link — whole kb,
 * switching to 1dp Mb at 1MB+ ("235kb", "1.2Mb").
 *
 * @param int $bytes Byte count.
 * @return string
 */
function cb_peoplesafe2027_format_file_size( $bytes ) {
	$bytes = (int) $bytes;

	if ( $bytes >= 1048576 ) {
		return rtrim( rtrim( number_format( $bytes / 1048576, 1 ), '0' ), '.' ) . 'Mb';
	}

	return round( $bytes / 1024 ) . 'kb';
}

/**
 * Uppercase file extension for an attachment, read straight off the real
 * file path — deliberately not wp_check_filetype(), which only recognises
 * WordPress's allowed-uploads list and returns blank for anything else.
 *
 * @param int $attachment_id Attachment ID.
 * @return string e.g. "PDF", "XHTML", or "" when unresolvable.
 */
function cb_peoplesafe2027_get_attachment_ext( $attachment_id ) {
	$path = get_attached_file( (int) $attachment_id );

	if ( ! $path ) {
		return '';
	}

	return strtoupper( pathinfo( $path, PATHINFO_EXTENSION ) );
}


/**
 * Rich-text lines as clean paragraphs. A rich-text field in a block's edit form
 * keeps its lines as HTML separated by <br>; each line becomes a <p>. Text that
 * is already paragraphs or lists (older pages) is returned as it is.
 *
 * @param string $html Field value.
 * @return string
 */
function cb_peoplesafe2027_rich_paragraphs( $html ) {
	$html = trim( wp_kses_post( (string) $html ) );

	if ( '' === $html || preg_match( '#<(p|ul|ol)[\s>]#i', $html ) ) {
		return $html;
	}

	// Plain text from older pages: a blank line starts a paragraph, a single line break stays a line break.
	if ( ! preg_match( '#<br\s*/?>#i', $html ) && preg_match( '#\n#', $html ) ) {
		$paragraphs = array_filter( array_map( 'trim', preg_split( '#\R\s*\R#', $html ) ) );
		$paragraphs = array_map(
			function ( $cb_paragraph ) {
				return preg_replace( '#\s*\R\s*#', '<br>', $cb_paragraph );
			},
			$paragraphs
		);

		return $paragraphs ? '<p>' . implode( '</p><p>', $paragraphs ) . '</p>' : '';
	}

	$lines = array_filter( array_map( 'trim', preg_split( '#(?:\s*<br\s*/?>\s*)+#i', $html ) ) );

	return $lines ? '<p>' . implode( '</p><p>', $lines ) . '</p>' : '';
}

/**
 * A list typed one item per line as <li> elements. Older pages store <li>
 * elements already; those are returned as they are.
 *
 * @param string $text Field value.
 * @return string
 */
function cb_peoplesafe2027_lines_to_items( $text ) {
	$text = trim( (string) $text );

	if ( '' === $text ) {
		return '';
	}

	if ( false !== stripos( $text, '<li' ) ) {
		return wp_kses_post( $text );
	}

	$items = '';

	foreach ( preg_split( '#\R#', $text ) as $line ) {
		$line = trim( $line );

		if ( '' !== $line ) {
			$items .= '<li>' . esc_html( $line ) . '</li>';
		}
	}

	return $items;
}


/**
 * Rich-text lines on one line: the same field as cb_peoplesafe2027_rich_paragraphs()
 * with the lines joined by a space and no <p>, for text that sits inside an
 * element of its own (a list item's definition, for example).
 *
 * @param string $html Field value.
 * @return string
 */
function cb_peoplesafe2027_rich_paragraphs_inline( $html ) {
	$html = trim( (string) $html );
	$html = preg_replace( '#</p>\s*<p[^>]*>#i', ' ', $html );
	$html = preg_replace( '#</?p[^>]*>#i', '', $html );

	return trim( preg_replace( '#(?:\s*<br\s*/?>\s*)+#i', ' ', $html ) );
}

/**
 * Print one of the footer's menus: a plain list, nothing when the location has no menu.
 *
 * @param string $location Menu location.
 * @return void
 */
function cb_peoplesafe2027_footer_menu( $location ) {
	wp_nav_menu(
		array(
			'theme_location' => $location,
			'container'      => false,
			'menu_class'     => 'menu',
			'depth'          => 1,
			'fallback_cb'    => false,
		)
	);
}

/**
 * The pages under the Sectors page, in the current region, in page order.
 *
 * @return WP_Post[]
 */
function cb_peoplesafe2027_footer_sectors() {
	$args = array(
		'post_type'      => 'page',
		'name'           => 'sectors',
		'post_status'    => 'publish',
		'posts_per_page' => 1,
	);

	if ( function_exists( 'pll_current_language' ) && pll_current_language() ) {
		$args['lang'] = pll_current_language();
	}

	$parent = get_posts( $args );

	if ( ! $parent ) {
		return array();
	}

	return get_pages(
		array(
			'parent'      => $parent[0]->ID,
			'sort_column' => 'menu_order,post_title',
			'post_status' => 'publish',
		)
	);
}

/**
 * A sector's name for the footer: its title, with the one long title shortened.
 *
 * @param WP_Post $page Sector page.
 * @return string
 */
function cb_peoplesafe2027_footer_sector_title( $page ) {
	$title = html_entity_decode( get_the_title( $page ) );

	return 'Professional Services & Government' === $title ? 'Prof. Services & Government' : $title;
}

/**
 * A product's intro for a card: the intro of its Hero block, with any list
 * left out, as plain text. Falls back to the product's excerpt, then its text.
 *
 * @param WP_Post $product Product.
 * @return string
 */
function cb_peoplesafe2027_product_intro( $product ) {
	foreach ( parse_blocks( $product->post_content ) as $block ) {
		if ( 'cb-peoplesafe2027/hero' === $block['blockName'] && ! empty( $block['attrs']['intro'] ) ) {
			return trim( wp_strip_all_tags( preg_replace( '#<(ul|ol)\b.*?</\1>#is', '', $block['attrs']['intro'] ) ) );
		}
	}

	return wp_trim_words( has_excerpt( $product ) ? $product->post_excerpt : wp_strip_all_tags( strip_shortcodes( excerpt_remove_blocks( $product->post_content ) ) ), 20 );
}

/**
 * A product's intro for a related-products card: the intro of its Hero block as
 * the live card shows it, the sentence and its short list (paragraphs, lists and
 * line breaks only).
 *
 * @param WP_Post $product Product.
 * @return string HTML, or an empty string when the product has no Hero intro.
 */
function cb_peoplesafe2027_product_intro_html( $product ) {
	foreach ( parse_blocks( $product->post_content ) as $block ) {
		if ( 'cb-peoplesafe2027/hero' === $block['blockName'] && ! empty( $block['attrs']['intro'] ) ) {
			return trim(
				wp_kses(
					(string) $block['attrs']['intro'],
					array(
						'p'      => array(),
						'br'     => array(),
						'ul'     => array(),
						'ol'     => array(),
						'li'     => array(),
						'strong' => array(),
						'em'     => array(),
					)
				)
			);
		}
	}

	return '';
}

/**
 * The Book a Demo form address, from Site-Wide Settings. Falls back to the
 * live form so the pop-up works before the setting has been saved.
 *
 * @return string
 */
function cb_peoplesafe2027_demo_form_url() {
	return (string) cb_peoplesafe2027_get_setting( 'demo_form_url', 'https://safe.peoplesafe.co.uk/l/830213/2022-06-22/rgxm4' ) ?: 'https://safe.peoplesafe.co.uk/l/830213/2022-06-22/rgxm4';
}

/**
 * Load Turtl's embed script, once, in the footer. A Turtl link on the page
 * (blocks/embed) becomes the document viewer when it runs.
 *
 * @return void
 */
function cb_peoplesafe2027_enqueue_turtl() {
	if ( wp_script_is( 'turtl-embed', 'enqueued' ) ) {
		return;
	}

	wp_enqueue_script( 'turtl-embed', 'https://app-static.turtl.co/embed/turtl.embed.v1.js', array(), null, array( 'in_footer' => true, 'strategy' => 'async' ) ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion -- third-party script, version set by Turtl.
}

add_filter(
	'script_loader_tag',
	function ( $tag, $handle ) {
		return 'turtl-embed' === $handle ? str_replace( '<script ', '<script data-turtl-script="embed" data-turtl-assets-hostname="https://assets.turtl.co" ', $tag ) : $tag;
	},
	10,
	2
);

/**
 * A user story's text as plain paragraphs: the post's blocks without their
 * comments and wrappers, so it can sit inside another block's panel.
 *
 * @param WP_Post $post The story.
 * @return string HTML of <p> elements.
 */
function cb_peoplesafe2027_story_text( $post ) {
	$html = preg_replace( '/<!--.*?-->/s', '', (string) $post->post_content );

	return trim( (string) $html );
}
