<?php
/**
 * Block template for Text and media.
 *
 * Text beside an image or a video. A video is a poster behind a play button;
 * src/js/video-facade.js loads the player only when it is clicked.
 *
 * @package cb-peoplesafe2027
 */

defined( 'ABSPATH' ) || exit;

$cb_pre_title  = isset( $attributes['preTitle'] ) ? $attributes['preTitle'] : '';
$cb_heading    = isset( $attributes['title'] ) ? $attributes['title'] : '';
$cb_buttons    = isset( $attributes['buttons'] ) ? $attributes['buttons'] : array();
$cb_media_type = isset( $attributes['mediaType'] ) ? $attributes['mediaType'] : 'image';
$cb_position   = isset( $attributes['mediaPosition'] ) ? $attributes['mediaPosition'] : 'right';
$cb_split      = isset( $attributes['split'] ) ? $attributes['split'] : 'half';
$cb_below      = isset( $attributes['belowMedia'] ) ? $attributes['belowMedia'] : '';
$cb_transcript = isset( $attributes['transcript'] ) ? $attributes['transcript'] : '';

$cb_image_id = isset( $attributes['imageId'] ) ? (int) $attributes['imageId'] : 0;
$cb_video    = ( 'video' === $cb_media_type && ! empty( $attributes['videoUrl'] ) ) ? cb_peoplesafe2027_parse_video_url( $attributes['videoUrl'] ) : null;
$cb_list     = array();

if ( 'list' === $cb_media_type && ! empty( $attributes['listItems'] ) ) {
	foreach ( (array) $attributes['listItems'] as $cb_item ) {
		if ( ! empty( $cb_item['text'] ) ) {
			$cb_list[] = $cb_item['text'];
		}
	}
}

$cb_has_media = ( 'image' === $cb_media_type && $cb_image_id ) || $cb_video || $cb_list;

// WordPress passes the rendered inner blocks in as $content.
$cb_text = isset( $content ) ? (string) $content : '';

$cb_play_in = ( isset( $attributes['playIn'] ) && 'inline' === $attributes['playIn'] ) ? 'inline' : 'modal';
$cb_poster  = '';

if ( $cb_video ) {
	$cb_poster_id = isset( $attributes['posterId'] ) ? (int) $attributes['posterId'] : 0;
	$cb_poster    = $cb_poster_id ? (string) wp_get_attachment_image_url( $cb_poster_id, 'large' ) : cb_peoplesafe2027_video_poster_url( $cb_video );
}

// The text and the media slide in from their own sides; text only rises.
// Below the text, the title, text and media are stacked and centred, and everything rises.
$cb_stacked     = 'below' === $cb_position;
$cb_media_left  = 'left' === $cb_position;
$cb_text_anim   = ( $cb_has_media && ! $cb_stacked ) ? ( $cb_media_left ? 'fade-left' : 'fade-right' ) : 'fade-up';
$cb_media_anim  = $cb_stacked ? 'fade-up' : ( $cb_media_left ? 'fade-right' : 'fade-left' );
$cb_video_label = $cb_heading ? $cb_heading : __( 'video', 'cb-peoplesafe2027' );

$cb_classes  = 'text-and-media';
$cb_classes .= $cb_stacked ? ' text-and-media--stacked' : ' text-and-media--media-' . ( $cb_media_left ? 'left' : 'right' );
$cb_classes .= ' text-and-media--' . ( 'wide-text' === $cb_split ? 'wide-text' : 'half' );
$cb_classes .= $cb_has_media ? '' : ' text-and-media--text-only';

$cb_wrapper_attributes = get_block_wrapper_attributes( array( 'class' => $cb_classes ) );
?>
<section <?php echo $cb_wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() already escapes. ?>>
	<div class="container">
		<div class="text-and-media__grid">
			<div class="text-and-media__content" data-animate="<?php echo esc_attr( $cb_text_anim ); ?>">
				<?php if ( $cb_pre_title ) { ?>
					<div class="text-and-media__pretitle"><?php echo esc_html( $cb_pre_title ); ?></div>
				<?php } ?>
				<?php if ( $cb_heading ) { ?>
					<h2 class="text-and-media__title"><?php echo esc_html( $cb_heading ); ?></h2>
				<?php } ?>
				<?php if ( trim( $cb_text ) ) { ?>
					<div class="text-and-media__text"><?php echo $cb_text; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- inner blocks, already rendered and escaped. ?></div>
				<?php } ?>
				<?php cb_peoplesafe2027_cta_buttons( $cb_buttons ); ?>
			</div>
			<?php if ( $cb_has_media ) { ?>
				<div class="text-and-media__media" data-animate="<?php echo esc_attr( $cb_media_anim ); ?>">
					<?php if ( $cb_video ) { ?>
						<button type="button" class="video-facade" data-play="<?php echo esc_attr( $cb_play_in ); ?>" data-embed="<?php echo esc_url( $cb_video['embed'] ); ?>" data-title="<?php echo esc_attr( $cb_heading ? $cb_heading : 'Video' ); ?>">
							<?php if ( $cb_poster ) { ?>
								<img class="video-facade__poster" src="<?php echo esc_url( $cb_poster ); ?>" alt="" loading="lazy">
							<?php } ?>
							<span class="video-facade__play" aria-hidden="true"></span>
							<span class="visually-hidden">
								<?php
								/* translators: %s: video title */
								echo esc_html( sprintf( __( 'Play video: %s', 'cb-peoplesafe2027' ), $cb_video_label ) );
								?>
							</span>
						</button>
						<?php if ( $cb_below ) { ?>
							<div class="text-and-media__below"><?php echo wp_kses_post( cb_peoplesafe2027_rich_paragraphs( $cb_below ) ); ?></div>
						<?php } ?>
					<?php } elseif ( $cb_list ) { ?>
						<ul class="wp-block-list <?php echo ( isset( $attributes['tickColour'] ) && 'orange' === $attributes['tickColour'] ) ? 'is-style-check-orange' : 'is-style-check'; ?> text-and-media__list">
							<?php foreach ( $cb_list as $cb_line ) { ?>
								<li>
								<?php
								echo wp_kses(
									$cb_line,
									array(
										'a'      => array(
											'href'   => true,
											'target' => true,
											'rel'    => true,
										),
										'strong' => array(),
										'b'      => array(),
										'em'     => array(),
										'i'      => array(),
										'br'     => array(),
									)
								);
								?>
									</li>
							<?php } ?>
						</ul>
					<?php } else { ?>
						<?php
						echo wp_get_attachment_image( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							$cb_image_id,
							'large',
							false,
							array(
								'class' => 'text-and-media__image' . ( ( isset( $attributes['framed'] ) && ! $attributes['framed'] ) ? ' text-and-media__image--plain' : '' ),
							)
						);
						?>
					<?php } ?>
				</div>
			<?php } ?>
		</div>
		<?php if ( $cb_video && $cb_transcript ) { ?>
			<details class="text-and-media__transcript">
				<summary><?php esc_html_e( 'Video transcript', 'cb-peoplesafe2027' ); ?></summary>
				<div class="text-and-media__transcript-body"><?php echo wp_kses_post( cb_peoplesafe2027_rich_paragraphs( $cb_transcript ) ); ?></div>
			</details>
		<?php } ?>
	</div>
</section>
