<?php
/**
 * The Blogs and News listings: the cards, and the endpoint that the listings' search, category filter and Load more
 * call (cb/v1/posts), so the first page and everything after it are drawn by the same PHP.
 *
 * Layout of a run of cards: the first is a lead card across the whole row, the next two take half a row each, the rest
 * a third. Only a first page without a search has the lead card; a batch loaded later is thirds so rows stay whole.
 *
 * @package cb-peoplesafe2027
 */

defined( 'ABSPATH' ) || exit;

/**
 * Cards on a first page, and in each batch Load more adds: a lead, two halves and six thirds, then rows of three.
 */
const CB_PEOPLESAFE2027_POSTS_PER_BATCH = 9;

/**
 * The first words of a post's own text, from its paragraphs (blocks that draw their own content add nothing).
 *
 * @param int $post_id Post ID.
 * @param int $words   How many words.
 * @return string Plain text.
 */
function cb_peoplesafe2027_post_excerpt( $post_id, $words = 25 ) {
	$text = cb_peoplesafe2027_blocks_text( parse_blocks( (string) get_post_field( 'post_content', $post_id ) ), $words );

	return wp_trim_words( trim( html_entity_decode( preg_replace( '/\s+/', ' ', $text ) ) ), $words, '…' );
}

/**
 * The paragraphs' text in a list of blocks, going into columns and groups, until it has about this many words. A Text
 * and media block's own text counts too.
 *
 * @param array[] $blocks Parsed blocks.
 * @param int     $words  Enough words.
 * @return string Plain text.
 */
function cb_peoplesafe2027_blocks_text( $blocks, $words ) {
	$text = '';

	foreach ( $blocks as $block ) {
		if ( 'core/paragraph' === $block['blockName'] ) {
			$text .= ' ' . wp_strip_all_tags( $block['innerHTML'] );
		} elseif ( 'cb-peoplesafe2027/text-and-media' === $block['blockName'] && ! empty( $block['attrs']['belowMedia'] ) ) {
			$text .= ' ' . wp_strip_all_tags( $block['attrs']['belowMedia'] );
		} elseif ( ! empty( $block['innerBlocks'] ) ) {
			$text .= ' ' . cb_peoplesafe2027_blocks_text( $block['innerBlocks'], $words );
		}

		if ( str_word_count( $text ) >= $words ) {
			break;
		}
	}

	return $text;
}

/**
 * The search words in a query: those of two letters or more, at most six.
 *
 * @param string $search Search text.
 * @return string[]
 */
function cb_peoplesafe2027_search_terms( $search ) {
	$words = preg_split( '/\s+/u', trim( $search ) );

	return array_slice( array_values( array_filter( $words, static function ( $word ) {
		return mb_strlen( $word ) >= 2;
	} ) ), 0, 6 );
}

/**
 * Text escaped for HTML with the search words wrapped in <mark>. The text is split on the words before it is
 * escaped, so an entity such as &amp; is never cut by a match.
 *
 * @param string   $text  Plain text.
 * @param string[] $terms Search words.
 * @return string HTML.
 */
function cb_peoplesafe2027_highlight( $text, $terms ) {
	if ( ! $terms ) {
		return esc_html( $text );
	}

	$parts = preg_split( '/(' . implode( '|', array_map( static function ( $term ) {
		return preg_quote( $term, '/' );
	}, $terms ) ) . ')/iu', $text, -1, PREG_SPLIT_DELIM_CAPTURE );
	$html  = '';

	foreach ( $parts as $index => $part ) {
		$html .= ( $index % 2 ) ? '<mark>' . esc_html( $part ) . '</mark>' : esc_html( $part );
	}

	return $html;
}

/**
 * All the readable text in a list of blocks, going into columns and groups: paragraphs, headings, lists, quotes, and
 * the text kept in a block's own settings (a video's description and transcript, FAQ answers, a pushthrough's text).
 * This is what a search can match, so a snippet can show it.
 *
 * @param array[] $blocks Parsed blocks.
 * @return string Plain text.
 */
function cb_peoplesafe2027_blocks_all_text( $blocks ) {
	$text = '';

	foreach ( $blocks as $block ) {
		$name  = (string) $block['blockName'];
		$attrs = $block['attrs'];

		if ( in_array( $name, array( 'core/paragraph', 'core/heading', 'core/list', 'core/quote', 'core/table' ), true ) ) {
			$text .= ' ' . wp_strip_all_tags( $block['innerHTML'] );
		}

		if ( 'cb-peoplesafe2027/text-and-media' === $name ) {
			$text .= ' ' . wp_strip_all_tags( ( isset( $attrs['belowMedia'] ) ? $attrs['belowMedia'] : '' ) . ' ' . ( isset( $attrs['transcript'] ) ? $attrs['transcript'] : '' ) );
		}

		if ( 'cb-peoplesafe2027/pushthrough' === $name ) {
			$text .= ' ' . wp_strip_all_tags( ( isset( $attrs['title'] ) ? $attrs['title'] : '' ) . ' ' . ( isset( $attrs['content'] ) ? $attrs['content'] : '' ) );
		}

		if ( 'cb-peoplesafe2027/faq' === $name && ! empty( $attrs['faqs'] ) ) {
			foreach ( $attrs['faqs'] as $faq ) {
				$text .= ' ' . wp_strip_all_tags( ( isset( $faq['question'] ) ? $faq['question'] : '' ) . ' ' . ( isset( $faq['answer'] ) ? $faq['answer'] : '' ) );
			}
		}

		if ( ! empty( $block['innerBlocks'] ) ) {
			$text .= ' ' . cb_peoplesafe2027_blocks_all_text( $block['innerBlocks'] );
		}
	}

	return $text;
}

/**
 * A snippet of a post's text around the first search word in its body, or its opening words when the words are only
 * in the title.
 *
 * @param int      $post_id Post ID.
 * @param string[] $terms   Search words.
 * @param int      $words   Opening words to fall back to.
 * @return string Plain text.
 */
function cb_peoplesafe2027_post_snippet( $post_id, $terms, $words = 25 ) {
	$text = cb_peoplesafe2027_blocks_all_text( parse_blocks( (string) get_post_field( 'post_content', $post_id ) ) );

	$text = trim( html_entity_decode( preg_replace( '/\s+/', ' ', $text ) ) );
	$pos  = false;

	foreach ( $terms as $term ) {
		$found = mb_stripos( $text, $term );

		if ( false !== $found && ( false === $pos || $found < $pos ) ) {
			$pos = $found;
		}
	}

	if ( false === $pos ) {
		return wp_trim_words( $text, $words, '…' );
	}

	$start = max( 0, $pos - 70 );

	return ( $start > 0 ? '…' : '' ) . trim( mb_substr( $text, $start, 220 ) ) . ( $start + 220 < mb_strlen( $text ) ? '…' : '' );
}

/**
 * What kind of content a post is, for a badge: Blog, News, a resource's type (Guide, Whitepaper, Video and so on), or
 * Case study or User story.
 *
 * @param int $post_id Post ID.
 * @return string
 */
function cb_peoplesafe2027_content_kind( $post_id ) {
	$type = get_post_type( $post_id );

	if ( 'post' === $type ) {
		return cb_peoplesafe2027_is_news_post( get_post( $post_id ) ) ? __( 'News', 'cb-peoplesafe2027' ) : __( 'Blog', 'cb-peoplesafe2027' );
	}

	$taxonomy = array(
		'resource'   => 'resource_type',
		'case_study' => 'case_study_type',
	);

	if ( isset( $taxonomy[ $type ] ) ) {
		$terms = get_the_terms( $post_id, $taxonomy[ $type ] );

		if ( $terms && ! is_wp_error( $terms ) ) {
			return html_entity_decode( $terms[0]->name );
		}
	}

	return 'case_study' === $type ? __( 'Case study', 'cb-peoplesafe2027' ) : '';
}

/**
 * One card.
 *
 * @param int    $post_id  Post ID.
 * @param string $variant  lead, half or standard.
 * @param string $context  blogs (the category badge), news (no badge) or all (the kind of content as the badge).
 * @param array  $terms    Search words to highlight, and to take the excerpt around.
 * @return string HTML.
 */
function cb_peoplesafe2027_post_card( $post_id, $variant = 'standard', $context = 'blogs', $terms = array() ) {
	$badge = '';
	$type  = get_post_type( $post_id );

	if ( 'all' === $context ) {
		$badge = cb_peoplesafe2027_content_kind( $post_id );
	} elseif ( 'blogs' === $context ) {
		foreach ( get_the_category( $post_id ) as $category ) {
			if ( 'uncategorized' !== cb_peoplesafe2027_default_term_slug( $category->term_id ) ) {
				$badge = html_entity_decode( $category->name );
				break;
			}
		}
	}

	$image   = get_the_post_thumbnail( $post_id, 'large', array( 'alt' => '', 'loading' => 'lazy' ) );
	$excerpt = $terms ? cb_peoplesafe2027_post_snippet( $post_id, $terms ) : cb_peoplesafe2027_post_excerpt( $post_id, 'lead' === $variant ? 40 : 25 );
	$minutes = cb_peoplesafe2027_post_reading_minutes( $post_id );

	ob_start();
	?>
	<article class="post-card post-card--<?php echo esc_attr( $variant . ( $terms ? ' post-card--match' : '' ) ); ?>">
		<a class="post-card__link" href="<?php echo esc_url( get_permalink( $post_id ) ); ?>">
			<div class="post-card__media<?php echo $image ? '' : ' post-card__media--empty'; ?><?php echo 'case_study' === $type ? ' post-card__media--logo' : ''; ?>">
				<?php echo $image; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core image markup. ?>
				<?php if ( $badge ) { ?>
					<span class="post-card__badge"><?php echo esc_html( $badge ); ?></span>
				<?php } ?>
			</div>
			<div class="post-card__body">
				<p class="post-card__meta"><?php echo esc_html( get_the_date( 'j M Y', $post_id ) ); ?><?php echo 'post' === $type ? ' &middot; ' . esc_html( sprintf( /* translators: %d: minutes */ __( '%d min read', 'cb-peoplesafe2027' ), $minutes ) ) : ''; ?></p>
				<h2 class="post-card__title"><?php echo cb_peoplesafe2027_highlight( wp_strip_all_tags( get_the_title( $post_id ) ), $terms ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped piece by piece in the function. ?></h2>
				<?php if ( $excerpt ) { ?>
					<p class="post-card__excerpt"><?php echo cb_peoplesafe2027_highlight( $excerpt, $terms ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped piece by piece in the function. ?></p>
				<?php } ?>
			</div>
		</a>
	</article>
	<?php
	return ob_get_clean();
}

/**
 * A run of cards: with $lead, the first is the lead and the next two halves; every other card is a standard one.
 *
 * @param int[]|WP_Post[] $posts   Posts or IDs.
 * @param bool            $lead    Start with the lead layout.
 * @param string          $context blogs, news or all.
 * @param string[]        $terms   Search words to highlight.
 * @return string HTML.
 */
function cb_peoplesafe2027_post_cards( $posts, $lead, $context, $terms = array() ) {
	$html = '';

	foreach ( array_values( $posts ) as $index => $item ) {
		$post_id = $item instanceof WP_Post ? $item->ID : (int) $item;
		$variant = 'standard';

		if ( $lead ) {
			$variant = 0 === $index ? 'lead' : ( $index <= 2 ? 'half' : 'standard' );
		}

		$html .= cb_peoplesafe2027_post_card( $post_id, $variant, $context, $terms );
	}

	return $html;
}

/**
 * Query arguments for a listing: the kind of post (blogs or news) in a region, narrowed by category and search.
 *
 * @param string $stream   blogs or news.
 * @param string $region   Region slug.
 * @param string $category Category slug, blogs only.
 * @param string $search   Search words.
 * @return array
 */
function cb_peoplesafe2027_posts_index_args( $stream, $region, $category = '', $search = '' ) {
	$args = array(
		'post_type'           => 'all' === $stream ? array( 'post', 'resource', 'case_study' ) : 'post',
		'post_status'         => 'publish',
		'ignore_sticky_posts' => true,
		'lang'                => $region,
	);

	$news_term = cb_peoplesafe2027_region_term_id( cb_peoplesafe2027_news_category_slug(), 'category', $region );

	if ( $news_term && 'all' !== $stream ) {
		$args[ 'news' === $stream ? 'category__in' : 'category__not_in' ] = array( $news_term );
	}

	if ( 'blogs' === $stream && $category ) {
		$term = get_term_by( 'slug', $category, 'category' );

		if ( $term ) {
			$args['category__in'] = array( $term->term_id );
		}
	}

	if ( '' !== $search ) {
		$args['s'] = $search;
	}

	return $args;
}

add_action(
	'rest_api_init',
	static function () {
		register_rest_route(
			'cb/v1',
			'/posts',
			array(
				'methods'             => 'GET',
				'permission_callback' => '__return_true',
				'callback'            => 'cb_peoplesafe2027_rest_posts',
				'args'                => array(
					'stream'   => array(
						'type'    => 'string',
						'enum'    => array( 'blogs', 'news', 'all' ),
						'default' => 'blogs',
					),
					'region'   => array(
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_key',
					),
					'category' => array(
						'type'              => 'string',
						'default'           => '',
						'sanitize_callback' => static function ( $value ) {
							return sanitize_title( $value ); // A closure: WordPress passes more arguments than sanitize_title() takes as plain text.
						},
					),
					'q'        => array(
						'type'              => 'string',
						'default'           => '',
						'sanitize_callback' => 'sanitize_text_field',
					),
					'offset'   => array(
						'type'              => 'integer',
						'default'           => 0,
						'sanitize_callback' => 'absint',
					),
					'lead'     => array(
						'type'    => 'boolean',
						'default' => false,
					),
				),
			)
		);
	}
);

/**
 * The cards for a listing, from an offset: the HTML, how many came back and how many there are in all.
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response
 */
function cb_peoplesafe2027_rest_posts( WP_REST_Request $request ) {
	$region = $request->get_param( 'region' );

	if ( ! in_array( $region, cb_peoplesafe2027_region_slugs(), true ) ) {
		$region = pll_default_language();
	}

	$stream = in_array( $request->get_param( 'stream' ), array( 'news', 'all' ), true ) ? $request->get_param( 'stream' ) : 'blogs';
	$search = mb_substr( trim( (string) $request->get_param( 'q' ) ), 0, 100 );
	$offset = (int) $request->get_param( 'offset' );
	$query  = new WP_Query(
		array_merge(
			cb_peoplesafe2027_posts_index_args( $stream, $region, (string) $request->get_param( 'category' ), $search ),
			array(
				'posts_per_page' => CB_PEOPLESAFE2027_POSTS_PER_BATCH,
				'offset'         => $offset,
				'fields'         => 'ids',
			)
		)
	);

	$lead = (bool) $request->get_param( 'lead' ) && 0 === $offset && '' === $search;

	return new WP_REST_Response(
		array(
			'html'  => cb_peoplesafe2027_post_cards( $query->posts, $lead, $stream, cb_peoplesafe2027_search_terms( $search ) ),
			'count' => count( $query->posts ),
			'total' => (int) $query->found_posts,
		)
	);
}
