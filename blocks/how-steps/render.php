<?php
/**
 * Block template for How steps.
 *
 * A title and intro, with an optional Watch video button that opens the
 * shared pop-out player (src/js/video-facade.js), over up to four step cards,
 * each an image, a title and a line of text. From lg a numbered timeline
 * draws across the top and the cards appear one after another in step with it
 * (src/js/how-steps.js); below lg the number moves into each card's title.
 *
 * @package cb-peoplesafe2027
 */

defined( 'ABSPATH' ) || exit;

$cb_steps = array();

foreach ( isset( $attributes['steps'] ) ? (array) $attributes['steps'] : array() as $cb_row ) {
	$cb_step_title = isset( $cb_row['title'] ) ? trim( wp_strip_all_tags( $cb_row['title'] ) ) : '';
	$cb_step_text  = isset( $cb_row['text'] ) ? trim( wp_strip_all_tags( $cb_row['text'] ) ) : '';
	$cb_step_image = isset( $cb_row['image'] ) ? (int) $cb_row['image'] : 0;

	if ( '' !== $cb_step_title || '' !== $cb_step_text ) {
		$cb_steps[] = array(
			'title' => $cb_step_title,
			'text'  => $cb_step_text,
			'image' => $cb_step_image,
		);
	}
}

$cb_steps = array_slice( $cb_steps, 0, 4 );

if ( ! $cb_steps ) {
	return;
}

$cb_heading = isset( $attributes['title'] ) ? $attributes['title'] : '';
$cb_intro   = isset( $attributes['intro'] ) ? $attributes['intro'] : '';
$cb_video   = ! empty( $attributes['videoUrl'] ) ? cb_peoplesafe2027_parse_video_url( $attributes['videoUrl'] ) : null;
$cb_label   = ! empty( $attributes['videoLabel'] ) ? $attributes['videoLabel'] : __( 'Watch video', 'cb-peoplesafe2027' );

$cb_wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class'          => 'how-steps',
		'data-how-steps' => '',
		'style'          => '--steps: ' . count( $cb_steps ),
	)
);
?>
<section <?php echo $cb_wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() already escapes. ?>>
	<div class="container">
		<div class="how-steps__header">
			<div class="how-steps__intro">
				<?php if ( $cb_heading ) { ?>
					<h2 class="how-steps__title"><?php echo esc_html( $cb_heading ); ?></h2>
				<?php } ?>
				<?php if ( '' !== wp_strip_all_tags( $cb_intro ) ) { ?>
					<div class="how-steps__lead"><?php echo wp_kses_post( cb_peoplesafe2027_rich_paragraphs( $cb_intro ) ); ?></div>
				<?php } ?>
			</div>
			<?php if ( $cb_video ) { ?>
				<button type="button" class="button button--outline how-steps__video" data-video-modal data-embed="<?php echo esc_url( $cb_video['embed'] ); ?>" data-title="<?php echo esc_attr( $cb_heading ? $cb_heading : $cb_label ); ?>">
					<svg viewBox="0 0 16 16" width="16" height="16" aria-hidden="true" focusable="false"><path fill="currentColor" d="M4 2.5v11a.5.5 0 0 0 .76.43l9-5.5a.5.5 0 0 0 0-.86l-9-5.5A.5.5 0 0 0 4 2.5z"/></svg>
					<span><?php echo esc_html( $cb_label ); ?></span>
				</button>
			<?php } ?>
		</div>
		<ol class="how-steps__steps" role="list">
			<?php foreach ( $cb_steps as $cb_position => $cb_step ) { ?>
				<li class="how-steps__step" style="--i: <?php echo (int) $cb_position; ?>">
					<span class="how-steps__dot" aria-hidden="true"><?php echo (int) ( $cb_position + 1 ); ?></span>
					<div class="how-steps__card">
					<?php if ( $cb_step['image'] ) { ?>
						<div class="how-steps__visual">
							<?php
							echo wp_get_attachment_image( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								$cb_step['image'],
								'large',
								false,
								array(
									'class' => 'how-steps__image',
									'sizes' => '(min-width: 992px) 25vw, (min-width: 768px) 45vw, 100vw',
								)
							);
							?>
						</div>
					<?php } ?>
					<?php if ( '' !== $cb_step['title'] ) { ?>
						<h3 class="how-steps__step-title"><span class="how-steps__num" aria-hidden="true"><?php echo (int) ( $cb_position + 1 ); ?>. </span><?php echo esc_html( $cb_step['title'] ); ?></h3>
					<?php } ?>
					<?php if ( '' !== $cb_step['text'] ) { ?>
						<p class="how-steps__text"><?php echo esc_html( $cb_step['text'] ); ?></p>
					<?php } ?>
					</div>
				</li>
			<?php } ?>
		</ol>
	</div>
</section>
