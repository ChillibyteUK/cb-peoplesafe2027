<?php
/**
 * Block template for Team.
 *
 * People from the People type, in the order chosen. "Profiles": photo beside
 * name, role and bio. "Photo cards": photo with the name and role over it.
 *
 * @package cb-peoplesafe2027
 */

defined( 'ABSPATH' ) || exit;

$cb_people = array();

foreach ( isset( $attributes['people'] ) ? (array) $attributes['people'] : array() as $cb_row ) {
	$cb_person = ! empty( $cb_row['id'] ) ? get_post( (int) $cb_row['id'] ) : null;

	if ( $cb_person && 'person' === $cb_person->post_type && 'publish' === $cb_person->post_status ) {
		$cb_people[] = $cb_person;
	}
}

if ( ! $cb_people ) {
	return;
}

$cb_cards = isset( $attributes['layout'] ) && 'cards' === $attributes['layout'];
$cb_title = isset( $attributes['title'] ) ? $attributes['title'] : '';

$cb_wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'team team--' . ( $cb_cards ? 'cards' : 'profiles' ) ) );
?>
<section <?php echo $cb_wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() already escapes. ?>>
	<div class="container">
		<?php if ( $cb_title ) { ?>
			<h2 class="team__title"><?php echo esc_html( $cb_title ); ?></h2>
		<?php } ?>
		<div class="team__list" data-animate="fade-up">
			<?php
			foreach ( $cb_people as $cb_person ) {
				$cb_role  = (string) get_post_meta( $cb_person->ID, 'cb_role', true );
				$cb_photo = get_the_post_thumbnail(
					$cb_person,
					$cb_cards ? 'large' : 'medium_large',
					array(
						'class'   => 'team__photo',
						'alt'     => '',
						'loading' => 'lazy',
					)
				);
				?>
				<article class="team__person">
					<?php if ( $cb_photo ) { ?>
						<div class="team__image"><?php echo $cb_photo; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
					<?php } ?>
					<?php if ( $cb_cards ) { ?>
						<div class="team__details">
							<div class="team__name"><?php echo esc_html( get_the_title( $cb_person ) ); ?></div>
							<?php if ( $cb_role ) { ?>
								<div class="team__role"><?php echo esc_html( $cb_role ); ?></div>
							<?php } ?>
						</div>
					<?php } else { ?>
						<div class="team__body">
							<h2 class="team__name"><?php echo esc_html( get_the_title( $cb_person ) ); ?></h2>
							<?php if ( $cb_role ) { ?>
								<h3 class="team__role"><?php echo esc_html( $cb_role ); ?></h3>
							<?php } ?>
							<div class="team__bio"><?php echo wp_kses_post( apply_filters( 'the_content', $cb_person->post_content ) ); ?></div>
						</div>
					<?php } ?>
				</article>
			<?php } ?>
		</div>
	</div>
</section>
