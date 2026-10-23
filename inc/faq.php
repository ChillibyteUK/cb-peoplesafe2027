<?php
/**
 * FAQ helpers: answer clean-up and the single FAQPage JSON-LD.
 *
 * Every FAQ block on a page queues its questions here as it renders, and one
 * FAQPage script is printed in the footer holding all of them. That keeps to
 * one FAQPage per page however many FAQ blocks it has, and it is JSON-LD, not
 * microdata. A question that appears twice on a page is listed once.
 *
 * @package cb-peoplesafe2027
 */

defined( 'ABSPATH' ) || exit;

/**
 * The HTML an FAQ answer may carry: the tags Google accepts in an answer's text.
 *
 * @param string $html Answer as entered.
 * @return string
 */
function cb_peoplesafe2027_faq_answer_html( $html ) {
	return wp_kses(
		cb_peoplesafe2027_rich_paragraphs( $html ),
		array(
			'p'      => array(),
			'br'     => array(),
			'a'      => array(
				'href'   => true,
				'target' => true,
				'rel'    => true,
			),
			'strong' => array(),
			'b'      => array(),
			'em'     => array(),
			'i'      => array(),
			'ul'     => array(),
			'ol'     => array(),
			'li'     => array(),
		)
	);
}

/**
 * Queue questions for the page's FAQPage schema, or read back what is queued.
 *
 * @param array|null $items List of array( 'question' => string, 'answer' => string ), or null to read.
 * @return array[] Every Question entity queued so far.
 */
function cb_peoplesafe2027_faq_schema_items( $items = null ) {
	static $entities = array();
	static $seen     = array();

	foreach ( (array) $items as $item ) {
		$key = md5( $item['question'] );

		if ( isset( $seen[ $key ] ) ) {
			continue;
		}

		$seen[ $key ] = true;
		$entities[]   = array(
			'@type'          => 'Question',
			'name'           => $item['question'],
			'acceptedAnswer' => array(
				'@type' => 'Answer',
				'text'  => $item['answer'],
			),
		);
	}

	return $entities;
}

/**
 * Queue questions from a block. Safe to call from any number of blocks.
 *
 * @param array $items List of array( 'question' => string, 'answer' => string ).
 * @return void
 */
function cb_peoplesafe2027_queue_faq_schema( array $items ) {
	cb_peoplesafe2027_faq_schema_items( $items );
}

/**
 * Print the page's one FAQPage JSON-LD script, if any question was queued.
 *
 * @return void
 */
function cb_peoplesafe2027_output_faq_schema() {
	$entities = cb_peoplesafe2027_faq_schema_items();

	if ( ! $entities ) {
		return;
	}

	$schema = array(
		'@context'   => 'https://schema.org',
		'@type'      => 'FAQPage',
		'mainEntity' => $entities,
	);

	echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP ) . '</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON-encoded with the tag characters escaped.
}
add_action( 'wp_footer', 'cb_peoplesafe2027_output_faq_schema' );
