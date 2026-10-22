<?php
/**
 * Language switcher: a rudimentary region switcher for the header.
 *
 * Plain markup, no styling, so it works as a test fixture and as a starting
 * point. Each link goes to the equivalent page in that region. A region with
 * no equivalent is linked to its home page and marked, so the visitor is not
 * sent to a page that does not exist. Region names come from Polylang.
 *
 * Use it in a template with cb_peoplesafe2027_language_switcher().
 *
 * @package cb-peoplesafe2027
 */

defined( 'ABSPATH' ) || exit;

/**
 * Regions for the switcher, one entry per region.
 *
 * @return array[] Each entry has slug, name, locale, url, current and fallback.
 */
function cb_peoplesafe2027_language_switcher_items() {
	if ( ! function_exists( 'pll_the_languages' ) ) {
		return array();
	}

	$raw   = pll_the_languages(
		array(
			'raw'                    => 1,
			'hide_if_no_translation' => 0,
			'force_home'             => 0,
		)
	);
	$items = array();

	foreach ( (array) $raw as $slug => $lang ) {
		$items[] = array(
			'slug'     => $slug,
			'name'     => $lang['name'],
			'locale'   => str_replace( '_', '-', $lang['locale'] ),
			'url'      => $lang['url'],
			'current'  => ! empty( $lang['current_lang'] ),
			'fallback' => ! empty( $lang['no_translation'] ),
		);
	}

	/**
	 * Filter the switcher entries.
	 *
	 * @param array[] $items Entries.
	 */
	return (array) apply_filters( 'cb_peoplesafe2027_language_switcher_items', $items );
}

/**
 * Regions for the Root hub's region selector block: each links to that
 * region's home page, whatever the current page is.
 *
 * @return array[] Each entry has slug, name, locale and url.
 */
function cb_peoplesafe2027_region_selector_items() {
	$items = array();

	foreach ( cb_peoplesafe2027_region_slugs() as $slug ) {
		$language = PLL()->model->get_language( $slug );

		if ( ! $language ) {
			continue;
		}

		$items[] = array(
			'slug'   => $slug,
			'name'   => $language->name,
			'locale' => str_replace( '_', '-', $language->locale ),
			'url'    => cb_peoplesafe2027_region_url( $slug ),
		);
	}

	/**
	 * Filter the region selector entries.
	 *
	 * @param array[] $items Entries.
	 */
	return (array) apply_filters( 'cb_peoplesafe2027_region_selector_items', $items );
}

/**
 * Print the region switcher.
 *
 * @return void
 */
function cb_peoplesafe2027_language_switcher() {
	$items = cb_peoplesafe2027_language_switcher_items();

	if ( count( $items ) < 2 ) {
		return;
	}
	?>
	<nav class="language-switcher" aria-label="<?php esc_attr_e( 'Region', 'cb-peoplesafe2027' ); ?>">
		<ul class="language-switcher__list">
			<?php foreach ( $items as $item ) : ?>
				<li class="language-switcher__item<?php echo $item['current'] ? ' is-current' : ''; ?><?php echo $item['fallback'] ? ' is-fallback' : ''; ?>">
					<a href="<?php echo esc_url( $item['url'] ); ?>" hreflang="<?php echo esc_attr( $item['locale'] ); ?>" lang="<?php echo esc_attr( $item['locale'] ); ?>"<?php echo $item['current'] ? ' aria-current="true"' : ''; ?> title="<?php echo esc_attr( $item['name'] ); ?>">
						<?php echo esc_html( strtoupper( $item['slug'] ) ); ?>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</nav>
	<?php
}
