<?php
/**
 * Site navigation: markup.
 *
 * Takes the resolved sections from cb_peoplesafe2027_nav_data() and returns
 * the HTML, escaped. Styles are in src/css/site-nav.css, behaviour in
 * src/js/site-nav.js.
 *
 * @package cb-peoplesafe2027
 */

defined( 'ABSPATH' ) || exit;

/**
 * An arrow icon, inline.
 *
 * @return string
 */
function cb_peoplesafe2027_nav_arrow() {
	return '<svg class="site-nav__arrow" aria-hidden="true" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>';
}

/**
 * Attributes for a link: href, and target with rel when it opens a new tab.
 *
 * @param string $url    URL.
 * @param bool   $target Open in a new tab.
 * @return string
 */
function cb_peoplesafe2027_nav_href( $url, $target ) {
	return ' href="' . esc_url( $url ) . '"' . ( $target ? ' target="_blank" rel="noopener"' : '' );
}

/**
 * One tile, card or link of a panel.
 *
 * @param array  $item    Resolved item.
 * @param string $display 'products', 'cards', 'tiles' or 'plain'.
 * @return string
 */
function cb_peoplesafe2027_nav_item( $item, $display ) {
	$image = '';

	if ( $item['image'] ) {
		$image = wp_get_attachment_image(
			$item['image'],
			'medium',
			false,
			array(
				'class'   => 'cards' === $display ? '' : 'item__image',
				'alt'     => '',
				'loading' => 'lazy',
			)
		);

		if ( $image && 'cards' === $display ) {
			$image = '<div class="item__image">' . $image . '</div>';
		}
	}

	$desc = $item['desc'] ? '<div class="item__desc">' . esc_html( $item['desc'] ) . '</div>' : '';

	if ( 'cards' === $display ) {
		return '<a class="item"' . cb_peoplesafe2027_nav_href( $item['url'], $item['target'] ) . '>' . $image
			. '<div class="item__title"><span>' . esc_html( $item['label'] ) . '</span>' . cb_peoplesafe2027_nav_arrow() . '</div>' . $desc . '</a>';
	}

	return '<a class="item"' . cb_peoplesafe2027_nav_href( $item['url'], $item['target'] ) . '>' . $image
		. '<div class="item__inner"><div class="item__title">' . esc_html( $item['label'] ) . '</div>' . $desc . '</div></a>';
}

/**
 * The "View all ..." link under a panel.
 *
 * @param array|null $more Resolved link.
 * @return string
 */
function cb_peoplesafe2027_nav_more( $more ) {
	if ( ! $more ) {
		return '';
	}

	return '<div class="more"><a class="more__link"' . cb_peoplesafe2027_nav_href( $more['url'], $more['target'] ) . '><span>'
		. esc_html( $more['text'] ) . '</span>' . cb_peoplesafe2027_nav_arrow() . '</a></div>';
}

/**
 * The contents of one panel of a "panels" section.
 *
 * @param array $panel Resolved panel.
 * @return string
 */
function cb_peoplesafe2027_nav_panel_body( $panel ) {
	$html = '';

	if ( $panel['heading'] ) {
		$html .= '<div class="h3">' . esc_html( $panel['heading'] ) . '</div>';
	}

	if ( $panel['intro'] ) {
		$html .= '<div>' . esc_html( $panel['intro'] ) . '</div>';
	}

	if ( 'cols' === $panel['display'] ) {
		$html .= '<ul>';

		foreach ( $panel['items'] as $item ) {
			$html .= '<li><a' . cb_peoplesafe2027_nav_href( $item['url'], $item['target'] ) . '><span>' . esc_html( $item['label'] ) . '</span>' . cb_peoplesafe2027_nav_arrow() . '</a></li>';
		}

		return $html . '</ul>' . cb_peoplesafe2027_nav_more( $panel['more'] );
	}

	$items = '';

	foreach ( $panel['items'] as $item ) {
		$items .= cb_peoplesafe2027_nav_item( $item, $panel['display'] );
	}

	if ( 'cards' === $panel['display'] ) {
		return $html . $items;
	}

	return $html . '<div class="items">' . $items . '</div>' . cb_peoplesafe2027_nav_more( $panel['more'] );
}

/**
 * The panel a section opens, or an empty string for a plain link or button.
 *
 * @param array  $section Resolved section.
 * @param string $dom_id  Id for the panel.
 * @return string
 */
function cb_peoplesafe2027_nav_panel( $section, $dom_id ) {
	$style = $section['width'] ? ' style="--panel-width:' . (int) $section['width'] . 'px"' : '';

	if ( 'list' === $section['layout'] ) {
		$html = '<ul>';

		foreach ( $section['items'] as $item ) {
			$html .= '<li><a' . cb_peoplesafe2027_nav_href( $item['url'], $item['target'] ) . '>' . esc_html( $item['label'] ) . '</a></li>';
		}

		return '<div id="' . esc_attr( $dom_id ) . '" class="site-nav__panel site-nav__panel--list"' . $style . '>' . $html . '</ul></div>';
	}

	if ( 'tiles' === $section['layout'] ) {
		$items = '';

		foreach ( $section['items'] as $item ) {
			$items .= cb_peoplesafe2027_nav_item( $item, 'tiles' );
		}

		return '<div id="' . esc_attr( $dom_id ) . '" class="site-nav__panel site-nav__panel--tiles"' . $style . '><div class="items">' . $items . '</div>' . cb_peoplesafe2027_nav_more( $section['more'] ) . '</div>';
	}

	if ( 'panels' !== $section['layout'] ) {
		return '';
	}

	$left  = '';
	$right = '';

	foreach ( $section['panels'] as $index => $panel ) {
		$body_id = $dom_id . '-' . ( $index + 1 );
		$active  = 0 === $index;

		$left  .= '<li' . ( $active ? ' class="active"' : '' ) . '><button type="button" aria-controls="' . esc_attr( $body_id ) . '" aria-expanded="' . ( $active ? 'true' : 'false' ) . '">' . esc_html( $panel['title'] ) . '</button></li>';
		$right .= '<div id="' . esc_attr( $body_id ) . '" class="right right--' . esc_attr( $panel['display'] ) . ( $active ? ' active' : '' ) . '">' . cb_peoplesafe2027_nav_panel_body( $panel ) . '</div>';
	}

	return '<div id="' . esc_attr( $dom_id ) . '" class="site-nav__panel site-nav__panel--panels"' . $style . '><ul class="left">' . $left . '</ul>' . $right . '</div>';
}

/**
 * The link, button or panel toggle of one section, for the bar.
 *
 * @param array  $section Resolved section.
 * @param string $dom_id  Id of its panel, when it has one.
 * @param bool   $has_panel Whether it opens a panel.
 * @return string
 */
function cb_peoplesafe2027_nav_toggle( $section, $dom_id, $has_panel ) {
	$label = esc_html( $section['title'] );

	if ( 'button' === $section['layout'] ) {
		if ( $section['dialog'] ) {
			return '<button type="button" class="button button--primary" data-dialog-target="' . esc_attr( $section['dialog'] ) . '"><span>' . $label . '</span></button>';
		}

		return '<a class="button button--primary"' . cb_peoplesafe2027_nav_href( $section['url'], $section['target'] ) . '><span>' . $label . '</span></a>';
	}

	if ( $has_panel ) {
		$href = $section['url'] ? cb_peoplesafe2027_nav_href( $section['url'], false ) : '';

		return '<a class="site-nav__toggle" data-panel="' . esc_attr( $dom_id ) . '" aria-controls="' . esc_attr( $dom_id ) . '" aria-expanded="false"' . $href . ( $section['url'] ? '' : ' role="button" tabindex="0"' ) . '>' . $label . '</a>';
	}

	$icon = 'lock' === $section['icon'] ? '<svg class="site-nav__icon" aria-hidden="true" viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg>' : '';

	return '<a class="site-nav__toggle"' . cb_peoplesafe2027_nav_href( $section['url'], $section['target'] ) . '>' . $icon . $label . '</a>';
}

/**
 * Build the navigation HTML.
 *
 * @param array[] $sections From cb_peoplesafe2027_nav_data().
 * @return string[] Keys main, extras and menus.
 */
function cb_peoplesafe2027_nav_html( $sections ) {
	$parts = array(
		'main'   => '',
		'extras' => '',
		'menus'  => '',
	);

	foreach ( $sections as $section ) {
		$dom_id    = 'site-nav-panel-' . $section['id'];
		$panel     = cb_peoplesafe2027_nav_panel( $section, $dom_id );
		$toggle    = cb_peoplesafe2027_nav_toggle( $section, $dom_id, '' !== $panel );
		$parts[ 'extras' === $section['position'] ? 'extras' : 'main' ] .= $toggle;
		$parts['menus']                                                .= $panel;
	}

	return $parts;
}
