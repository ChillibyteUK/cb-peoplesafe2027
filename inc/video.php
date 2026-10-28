<?php
/**
 * Video links: work out the provider, the embed URL and a poster from any
 * YouTube or Vimeo link, so a block takes one pasted URL instead of a provider
 * and an ID. Used by blocks that show a video behind a play button
 * (src/js/video-facade.js loads the player only when it is clicked). Posters
 * are saved on this site, so nothing is requested from the provider until then.
 *
 * @package cb-peoplesafe2027
 */

defined( 'ABSPATH' ) || exit;

/**
 * Describe a YouTube or Vimeo URL.
 *
 * @param string $url Link as pasted.
 * @return array{provider: string, id: string, embed: string, url: string}|null Null when it is not a video link.
 */
function cb_peoplesafe2027_parse_video_url( $url ) {
	$url   = trim( (string) $url );
	$parts = wp_parse_url( $url );

	if ( '' === $url || empty( $parts['host'] ) ) {
		return null;
	}

	$host = strtolower( preg_replace( '/^www\./', '', $parts['host'] ) );
	$path = isset( $parts['path'] ) ? $parts['path'] : '';

	if ( in_array( $host, array( 'youtube.com', 'm.youtube.com', 'youtube-nocookie.com', 'youtu.be' ), true ) ) {
		$id = '';

		if ( 'youtu.be' === $host ) {
			$id = trim( $path, '/' );
		} elseif ( preg_match( '#^/(?:embed|shorts|live|v)/([\w-]{6,})#', $path, $m ) ) {
			$id = $m[1];
		} elseif ( ! empty( $parts['query'] ) ) {
			parse_str( $parts['query'], $query );
			$id = isset( $query['v'] ) ? (string) $query['v'] : '';
		}

		if ( ! preg_match( '/^[\w-]{6,}$/', $id ) ) {
			return null;
		}

		return array(
			'provider' => 'youtube',
			'id'       => $id,
			'embed'    => 'https://www.youtube-nocookie.com/embed/' . $id . '?autoplay=1&rel=0&iv_load_policy=3&playsinline=1&color=white',
			'url'      => $url,
		);
	}

	$vimeo = cb_peoplesafe2027_get_vimeo_embed_url( $url );

	if ( '' !== $vimeo ) {
		preg_match( '#/video/(\d+)#', $vimeo, $m );

		return array(
			'provider' => 'vimeo',
			'id'       => isset( $m[1] ) ? $m[1] : '',
			'embed'    => $vimeo,
			'url'      => $url,
		);
	}

	return null;
}

/**
 * A poster image for a video, kept on this site.
 *
 * The provider's thumbnail is fetched once, saved under uploads/cb-video-posters
 * and served from there, so a page with a video makes no request to YouTube or
 * Vimeo until the visitor presses play. If the thumbnail cannot be fetched this
 * returns an empty string (the block shows a plain panel) and tries again in an
 * hour, so a slow provider never slows page views.
 *
 * @param array $video Result of cb_peoplesafe2027_parse_video_url().
 * @return string Local poster URL, or an empty string.
 */
function cb_peoplesafe2027_video_poster_url( $video ) {
	$upload = wp_upload_dir();
	$name   = sanitize_file_name( $video['provider'] . '-' . $video['id'] . '.jpg' );
	$path   = $upload['basedir'] . '/cb-video-posters/' . $name;

	if ( file_exists( $path ) ) {
		return $upload['baseurl'] . '/cb-video-posters/' . $name;
	}

	$retry = 'cb_ps_poster_retry_' . md5( $name );

	if ( get_transient( $retry ) ) {
		return '';
	}

	$sources = ( 'youtube' === $video['provider'] )
		? array(
			'https://img.youtube.com/vi/' . $video['id'] . '/maxresdefault.jpg',
			'https://img.youtube.com/vi/' . $video['id'] . '/hqdefault.jpg',
		)
		: array( cb_peoplesafe2027_vimeo_thumbnail_url( $video['url'] ) );

	foreach ( array_filter( $sources ) as $source ) {
		$response = wp_remote_get( $source, array( 'timeout' => 8 ) );

		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
			continue;
		}

		$body = wp_remote_retrieve_body( $response );
		$type = wp_remote_retrieve_header( $response, 'content-type' );

		// Only an ordinary image of a sensible size is kept.
		if ( 0 !== strpos( (string) $type, 'image/jpeg' ) || strlen( $body ) < 1024 || strlen( $body ) > 2 * MB_IN_BYTES ) {
			continue;
		}

		if ( wp_mkdir_p( dirname( $path ) ) && false !== file_put_contents( $path, $body ) ) { // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents -- a cached copy in uploads.
			return $upload['baseurl'] . '/cb-video-posters/' . $name;
		}
	}

	set_transient( $retry, 1, HOUR_IN_SECONDS );

	return '';
}

/**
 * The thumbnail URL Vimeo's oEmbed endpoint gives for a video, at a larger
 * size, or an empty string. Only a Vimeo CDN address is accepted.
 *
 * @param string $url Vimeo link.
 * @return string
 */
function cb_peoplesafe2027_vimeo_thumbnail_url( $url ) {
	$response = wp_remote_get( 'https://vimeo.com/api/oembed.json?url=' . rawurlencode( $url ), array( 'timeout' => 5 ) );

	if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
		return '';
	}

	$data  = json_decode( wp_remote_retrieve_body( $response ), true );
	$thumb = ( is_array( $data ) && ! empty( $data['thumbnail_url'] ) ) ? esc_url_raw( $data['thumbnail_url'] ) : '';
	$host  = (string) wp_parse_url( $thumb, PHP_URL_HOST );

	if ( '' === $thumb || ! preg_match( '/(^|\.)vimeocdn\.com$/', $host ) ) {
		return '';
	}

	// oEmbed returns a 295px thumbnail: ask Vimeo's CDN for a larger one.
	return preg_replace( '/-d_\d+(x\d+)?/', '-d_1280', $thumb );
}

/**
 * VideoObject schema for a video resource, added to Yoast's schema graph and linked from the page.
 *
 * Built from what the page already holds: the title, the description under the player, the poster, the date, the
 * player's embed address, the transcript and the length (saved at import in cb_video_duration).
 *
 * @param array[] $graph   Yoast's schema pieces.
 * @param object  $context Yoast's context for the page.
 * @return array[]
 */
function cb_peoplesafe2027_video_schema( $graph, $context ) {
	if ( ! is_singular( 'resource' ) || ! has_term( 'video', 'resource_type' ) ) {
		return $graph;
	}

	$attrs = array();

	foreach ( parse_blocks( get_post_field( 'post_content', get_queried_object_id() ) ) as $block ) {
		if ( 'cb-peoplesafe2027/text-and-media' === $block['blockName'] && ! empty( $block['attrs']['videoUrl'] ) ) {
			$attrs = $block['attrs'];
			break;
		}
	}

	$video = $attrs ? cb_peoplesafe2027_parse_video_url( $attrs['videoUrl'] ) : null;

	if ( ! $video ) {
		return $graph;
	}

	$id    = trailingslashit( $context->canonical ) . '#video';
	$piece = array(
		'@type'        => 'VideoObject',
		'@id'          => $id,
		'name'         => wp_strip_all_tags( get_the_title() ),
		'description'  => wp_strip_all_tags( isset( $attrs['belowMedia'] ) ? $attrs['belowMedia'] : get_the_title() ),
		'uploadDate'   => get_the_date( 'c' ),
		'embedUrl'     => 'youtube' === $video['provider'] ? 'https://www.youtube.com/embed/' . $video['id'] : 'https://player.vimeo.com/video/' . $video['id'],
		'inLanguage'   => get_bloginfo( 'language' ),
	);

	$thumb = get_the_post_thumbnail_url( get_queried_object_id(), 'large' );

	if ( $thumb ) {
		$piece['thumbnailUrl'] = $thumb;
	}

	$seconds = (int) get_post_meta( get_queried_object_id(), 'cb_video_duration', true );

	if ( $seconds ) {
		$piece['duration'] = sprintf( 'PT%dH%dM%dS', intdiv( $seconds, 3600 ), intdiv( $seconds % 3600, 60 ), $seconds % 60 );
	}

	if ( ! empty( $attrs['transcript'] ) ) {
		$piece['transcript'] = wp_strip_all_tags( $attrs['transcript'] );
	}

	foreach ( $graph as $key => $existing ) {
		if ( isset( $existing['@type'] ) && in_array( 'WebPage', (array) $existing['@type'], true ) ) {
			$graph[ $key ]['video']    = array( array( '@id' => $id ) );
			$piece['isPartOf']         = array( '@id' => $existing['@id'] );
		}
	}

	$graph[] = $piece;

	return $graph;
}
add_filter( 'wpseo_schema_graph', 'cb_peoplesafe2027_video_schema', 10, 2 );
