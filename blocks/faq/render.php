<?php
/**
 * Block template for FAQ.
 *
 * An accordion of native <details> elements (no JavaScript): the questions are
 * h3 headings inside the summaries, closed to begin with, one open at a time
 * within a block. Every question also goes to the page's single FAQPage
 * JSON-LD (inc/faq.php).
 *
 * @package cb-peoplesafe2027
 */

defined( 'ABSPATH' ) || exit;

$cb_faqs  = array();
$cb_items = array();

foreach ( isset( $attributes['faqs'] ) ? (array) $attributes['faqs'] : array() as $cb_row ) {
	$cb_question = isset( $cb_row['question'] ) ? trim( wp_strip_all_tags( $cb_row['question'] ) ) : '';
	$cb_answer   = isset( $cb_row['answer'] ) ? cb_peoplesafe2027_faq_answer_html( $cb_row['answer'] ) : '';

	if ( '' === $cb_question || '' === wp_strip_all_tags( $cb_answer ) ) {
		continue;
	}

	$cb_faqs[]  = array(
		'question' => $cb_question,
		'answer'   => $cb_answer,
	);
	$cb_items[] = array(
		'question' => $cb_question,
		'answer'   => $cb_answer,
	);
}

if ( ! $cb_faqs ) {
	return;
}

cb_peoplesafe2027_queue_faq_schema( $cb_items );

$cb_heading = isset( $attributes['title'] ) ? $attributes['title'] : '';
$cb_group   = wp_unique_id( 'faq-' );

$cb_wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'faq' ) );
?>
<section <?php echo $cb_wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() already escapes. ?>>
	<div class="container">
		<?php if ( $cb_heading ) { ?>
			<h2 class="faq__title"><?php echo esc_html( $cb_heading ); ?></h2>
		<?php } ?>
		<div class="accordion" data-animate="fade-up">
			<?php foreach ( $cb_faqs as $cb_faq ) { ?>
				<details class="accordion-item" name="<?php echo esc_attr( $cb_group ); ?>">
					<summary class="accordion-header">
						<h3 class="faq__question"><?php echo esc_html( $cb_faq['question'] ); ?></h3>
						<svg class="accordion-icon" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
					</summary>
					<div class="accordion-body"><?php echo $cb_faq['answer']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- filtered by cb_peoplesafe2027_faq_answer_html(). ?></div>
				</details>
			<?php } ?>
		</div>
	</div>
</section>
