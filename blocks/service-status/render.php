<?php
/**
 * Block template for Service status.
 *
 * An overall status banner over a list of services, each operational, degraded
 * or down. The overall state is worked out from the list: every service
 * operational is "Fully operational", every service down is "Down", anything
 * else is "Partly degraded". The "Last updated" date is the page's own
 * modified date, so saving the page is all an editor has to do. State is
 * always written out as text as well as shown in colour. An optional message
 * (an incident notice, say) goes above the banner.
 *
 * @package cb-peoplesafe2027
 */

defined( 'ABSPATH' ) || exit;

$cb_states = array(
	'operational' => __( 'Operational', 'cb-peoplesafe2027' ),
	'degraded'    => __( 'Degraded', 'cb-peoplesafe2027' ),
	'down'        => __( 'Down', 'cb-peoplesafe2027' ),
);

$cb_services = array();

foreach ( isset( $attributes['services'] ) ? (array) $attributes['services'] : array() as $cb_row ) {
	$cb_name   = isset( $cb_row['name'] ) ? trim( wp_strip_all_tags( $cb_row['name'] ) ) : '';
	$cb_status = isset( $cb_row['status'], $cb_states[ $cb_row['status'] ] ) ? $cb_row['status'] : 'operational';

	if ( '' !== $cb_name ) {
		$cb_services[] = array(
			'name'   => $cb_name,
			'status' => $cb_status,
		);
	}
}

$cb_message = isset( $attributes['message'] ) ? trim( $attributes['message'] ) : '';

if ( ! $cb_services ) {
	return;
}

$cb_statuses = wp_list_pluck( $cb_services, 'status' );
$cb_overall  = 'degraded';
$cb_headline = __( 'Partly degraded', 'cb-peoplesafe2027' );

if ( array( 'operational' ) === array_values( array_unique( $cb_statuses ) ) ) {
	$cb_overall  = 'operational';
	$cb_headline = __( 'Fully operational', 'cb-peoplesafe2027' );
} elseif ( array( 'down' ) === array_values( array_unique( $cb_statuses ) ) ) {
	$cb_overall  = 'down';
	$cb_headline = __( 'Down', 'cb-peoplesafe2027' );
}

$cb_heading = isset( $attributes['title'] ) ? $attributes['title'] : '';
$cb_updated = ( ! isset( $attributes['showUpdated'] ) || $attributes['showUpdated'] ) && get_the_ID() ? get_post_modified_time( 'j F Y, H:i', false, get_the_ID(), true ) : '';

$cb_wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'service-status' ) );
?>
<section <?php echo $cb_wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() already escapes. ?>>
	<div class="container">
		<?php if ( '' !== wp_strip_all_tags( $cb_message ) ) { ?>
			<div class="service-status__message"><?php echo wp_kses_post( cb_peoplesafe2027_rich_paragraphs( $cb_message ) ); ?></div>
		<?php } ?>
		<div class="service-status__overall service-status__overall--<?php echo esc_attr( $cb_overall ); ?>" data-animate="fade-up">
			<span class="service-status__dot" aria-hidden="true"></span>
			<div>
				<p class="service-status__headline"><?php echo esc_html( $cb_headline ); ?></p>
				<?php if ( $cb_updated ) { ?>
					<p class="service-status__updated">
						<?php
						/* translators: %s: date and time */
						echo esc_html( sprintf( __( 'Last updated %s', 'cb-peoplesafe2027' ), $cb_updated ) );
						?>
					</p>
				<?php } ?>
			</div>
		</div>
		<?php if ( $cb_heading ) { ?>
			<h2 class="service-status__title"><?php echo esc_html( $cb_heading ); ?></h2>
		<?php } ?>
		<ul class="service-status__list" data-animate="fade-up">
			<?php foreach ( $cb_services as $cb_service ) { ?>
				<li class="service-status__item service-status__item--<?php echo esc_attr( $cb_service['status'] ); ?>">
					<span class="service-status__dot" aria-hidden="true"></span>
					<span class="service-status__name"><?php echo esc_html( $cb_service['name'] ); ?></span>
					<span class="service-status__state"><?php echo esc_html( $cb_states[ $cb_service['status'] ] ); ?></span>
				</li>
			<?php } ?>
		</ul>
	</div>
</section>
