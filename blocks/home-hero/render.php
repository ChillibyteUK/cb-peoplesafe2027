<?php
/**
 * Block template for Home hero.
 *
 * A navy hero: the headline, intro and buttons on the left, and a slideshow of
 * photos on the right that fades from one to the next (src/js/home-hero.js),
 * with a pause button. A navy gradient fades the photo into the page on its
 * left edge. Below lg the photos sit above the text and fade into it from the
 * bottom. A slide with a Vimeo link plays that video (muted, looping, dnt) over its photo, which stays as the poster while the player loads. A block saved before slideshows existed shows its old single image.
 *
 * @package cb-peoplesafe2027
 */

defined( 'ABSPATH' ) || exit;

$cb_title      = isset( $attributes['title'] ) ? $attributes['title'] : '';
$cb_word_one   = isset( $attributes['wordOne'] ) ? $attributes['wordOne'] : '';
$cb_word_two   = isset( $attributes['wordTwo'] ) ? $attributes['wordTwo'] : '';
$cb_intro      = isset( $attributes['content'] ) ? $attributes['content'] : '';
$cb_buttons    = isset( $attributes['buttons'] ) ? $attributes['buttons'] : array();
$cb_mobile_id  = isset( $attributes['mobileImageId'] ) ? (int) $attributes['mobileImageId'] : 0;
$cb_mobile_url = isset( $attributes['mobileImageUrl'] ) ? $attributes['mobileImageUrl'] : '';
$cb_mobile_alt = isset( $attributes['mobileImageAlt'] ) ? $attributes['mobileImageAlt'] : '';

// The photos, at most five. Each has up to three event cards that pop up on it, one after another.
$cb_icons = array(
	'tick'   => '<path d="M5 12.5l4.5 4.5L19 7.5"/>',
	'bell'   => '<path d="M6 16.5V11a6 6 0 0 1 12 0v5.5l1.5 1.5h-15L6 16.5z"/><path d="M10 20.5a2 2 0 0 0 4 0"/>',
	'shield' => '<path d="M12 3.5l7 2.5v5.5c0 4.2-2.9 7.8-7 9-4.1-1.2-7-4.8-7-9V6l7-2.5z"/><path d="M9 12.2l2.2 2.2L15.2 10"/>',
);
$cb_tones = array( 'blue', 'navy', 'teal' );
$cb_slides = array();

foreach ( isset( $attributes['slides'] ) ? (array) $attributes['slides'] : array() as $cb_row ) {
	$cb_video = '';

	if ( ! empty( $cb_row['video'] ) ) {
		// A Vimeo link becomes a background player: muted, looping, autoplaying, and dnt=1 so it sets no cookies.
		$cb_link = trim( (string) $cb_row['video'] );

		// A bare ID (123456789, or 123456789/abcdef1234 for an unlisted video) is accepted as well as a full link.
		if ( preg_match( '#^\d+(/[a-f0-9]+)?$#i', $cb_link ) ) {
			$cb_link = 'https://vimeo.com/' . $cb_link;
		}

		$cb_embed = cb_peoplesafe2027_get_vimeo_embed_url( $cb_link );

		if ( '' !== $cb_embed ) {
			$cb_video = preg_replace( '/\?.*?(&h=[a-f0-9]+)?$/i', '?autoplay=1&muted=1&loop=1&background=1&dnt=1$1', $cb_embed );
		}
	}

	if ( empty( $cb_row['image'] ) && '' === $cb_video ) {
		continue;
	}

	$cb_cards = array();

	foreach ( isset( $cb_row['cards'] ) ? (array) $cb_row['cards'] : array() as $cb_card ) {
		$cb_card_title = isset( $cb_card['title'] ) ? trim( (string) $cb_card['title'] ) : '';

		if ( '' === $cb_card_title ) {
			continue;
		}

		$cb_cards[] = array(
			'title' => $cb_card_title,
			'text'  => isset( $cb_card['text'] ) ? trim( (string) $cb_card['text'] ) : '',
			'icon'  => isset( $cb_card['icon'], $cb_icons[ $cb_card['icon'] ] ) ? $cb_card['icon'] : 'tick',
			'tone'  => isset( $cb_card['tone'] ) && in_array( $cb_card['tone'], $cb_tones, true ) ? $cb_card['tone'] : 'blue',
		);
	}

	$cb_slides[] = array(
		'id'    => empty( $cb_row['image'] ) ? 0 : (int) $cb_row['image'],
		'video' => $cb_video,
		'cards' => array_slice( $cb_cards, 0, 3 ),
	);
}

$cb_slides = array_slice( $cb_slides, 0, 5 );

// A block saved before slideshows existed: its small-screen image becomes the one slide.
if ( ! $cb_slides && $cb_mobile_id ) {
	$cb_slides[] = array(
		'id'    => $cb_mobile_id,
		'cards' => array(),
	);
}

$cb_wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class'          => 'home-hero',
		'data-home-hero' => '',
	)
);
?>
<section <?php echo $cb_wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() already escapes. ?>>
	<?php if ( $cb_slides || $cb_mobile_url ) { ?>
		<div class="home-hero__slides">
			<?php
			foreach ( $cb_slides as $cb_index => $cb_slide ) {
				$cb_first = 0 === $cb_index;
				?>
				<div class="home-hero__slide<?php echo $cb_first ? ' is-active' : ''; ?>"<?php echo $cb_first ? '' : ' aria-hidden="true"'; ?>>
					<?php
					// The photo, if there is one, shows at once and as the video's poster: the video fades in over it once it is playing.
					if ( $cb_slide['id'] ) {
						echo wp_get_attachment_image( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							$cb_slide['id'],
							'full',
							false,
							array(
								'class'         => 'home-hero__image',
								'sizes'         => '(min-width: 992px) 64vw, 100vw',
								'loading'       => $cb_first ? 'eager' : 'lazy',
								'fetchpriority' => $cb_first ? 'high' : 'low',
							)
						);
					}
					?>
					<?php if ( $cb_slide['video'] ) { ?>
						<?php // Only the first video loads at once; the slideshow script loads each of the others just before its turn and pauses it when it is not showing. ?>
						<iframe class="home-hero__video" <?php echo $cb_first || 1 === count( $cb_slides ) ? 'src' : 'data-src'; ?>="<?php echo esc_url( $cb_slide['video'] ); ?>" title="" allow="autoplay; fullscreen" tabindex="-1" aria-hidden="true"></iframe>
					<?php } ?>
					<?php if ( $cb_slide['cards'] ) { ?>
						<ul class="home-hero__cards">
							<?php foreach ( $cb_slide['cards'] as $cb_position => $cb_card ) { ?>
								<li class="home-hero__card home-hero__card--<?php echo esc_attr( $cb_card['tone'] ); ?>" style="--i: <?php echo (int) $cb_position; ?>">
									<span class="home-hero__card-icon" aria-hidden="true">
										<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><?php echo $cb_icons[ $cb_card['icon'] ]; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed markup defined above. ?></svg>
									</span>
									<span class="home-hero__card-text">
										<strong class="home-hero__card-title"><?php echo esc_html( $cb_card['title'] ); ?></strong>
										<?php if ( $cb_card['text'] ) { ?>
											<span class="home-hero__card-meta"><?php echo esc_html( $cb_card['text'] ); ?></span>
										<?php } ?>
									</span>
								</li>
							<?php } ?>
						</ul>
					<?php } ?>
				</div>
			<?php } ?>
			<?php if ( ! $cb_slides && $cb_mobile_url ) { ?>
				<div class="home-hero__slide is-active">
					<img class="home-hero__image" src="<?php echo esc_url( $cb_mobile_url ); ?>" alt="<?php echo esc_attr( $cb_mobile_alt ); ?>" fetchpriority="high">
				</div>
			<?php } ?>
			<?php if ( count( $cb_slides ) > 1 ) { ?>
				<button class="home-hero__pause" type="button" aria-pressed="false" data-home-hero-pause data-label-pause="<?php esc_attr_e( 'Pause slideshow', 'cb-peoplesafe2027' ); ?>" data-label-play="<?php esc_attr_e( 'Play slideshow', 'cb-peoplesafe2027' ); ?>" hidden>
					<span class="home-hero__pause-icon" aria-hidden="true"></span>
					<span class="visually-hidden"><?php esc_html_e( 'Pause slideshow', 'cb-peoplesafe2027' ); ?></span>
				</button>
			<?php } ?>
		</div>
	<?php } ?>
	<div class="container home-hero__inner">
		<div class="home-hero__content">
			<h1 class="home-hero__title">
				<?php if ( $cb_title ) { ?>
					<span class="home-hero__title-text"><?php echo esc_html( $cb_title ); ?></span>
				<?php } ?>
				<?php if ( $cb_word_one || $cb_word_two ) { ?>
					<span class="home-hero__title-text"><?php echo esc_html( trim( $cb_word_one . ' ' . $cb_word_two ) ); ?></span>
				<?php } ?>
			</h1>
			<?php if ( $cb_intro ) { ?>
				<div class="home-hero__text"><?php echo wp_kses_post( $cb_intro ); ?></div>
			<?php } ?>
			<?php cb_peoplesafe2027_cta_buttons( $cb_buttons ); ?>
		</div>
	</div>
</section>
