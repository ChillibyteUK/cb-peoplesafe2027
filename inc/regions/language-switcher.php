<?php
/**
 * Language switcher: the region dropdown in the header.
 *
 * A button showing the current region's flag and code opens a list of every
 * region, using the theme's click-to-open dropdown (src/js/nav-dropdown.js).
 * Each link goes to the equivalent page in that region. A region with no
 * equivalent is linked to its home page and marked, so the visitor is not
 * sent to a page that does not exist. Region names come from Polylang; flags
 * are in img/flags (flag-icons, MIT licence), named by country code taken
 * from the locale (en_GB is gb).
 *
 * Use it in a template with cb_peoplesafe2027_language_switcher().
 *
 * @package cb-peoplesafe2027
 */

defined( 'ABSPATH' ) || exit;

/**
 * URL of a region's flag, from its locale, or an empty string when the theme
 * has no flag for it.
 *
 * @param string $locale Locale, for example en_GB.
 * @return string
 */
function cb_peoplesafe2027_flag_url( $locale ) {
	$country = strtolower( substr( (string) $locale, 3, 2 ) );
	$file    = '/img/flags/' . $country . '.svg';

	return ( '' !== $country && file_exists( get_template_directory() . $file ) ) ? get_template_directory_uri() . $file : '';
}

/**
 * Output a flag image, or nothing when there is none.
 *
 * @param string $url Flag URL.
 * @return void
 */
function cb_peoplesafe2027_flag_img( $url ) {
	if ( '' !== $url ) {
		printf( '<img class="flag" src="%s" width="20" height="15" alt="" aria-hidden="true">', esc_url( $url ) );
	}
}

/**
 * Regions for the switcher, one entry per region.
 *
 * @return array[] Each entry has slug, name, locale, flag, url, current and fallback.
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
			'flag'     => cb_peoplesafe2027_flag_url( $lang['locale'] ),
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
	// The Root hub is itself the region chooser, so the header dropdown is left out there.
	if ( cb_peoplesafe2027_is_root_request() ) {
		return;
	}

	$items = cb_peoplesafe2027_language_switcher_items();

	if ( count( $items ) < 2 ) {
		return;
	}

	$current = null;

	foreach ( $items as $item ) {
		if ( $item['current'] ) {
			$current = $item;
			break;
		}
	}

	if ( ! $current ) {
		$current = $items[0];
	}
	?>
	<nav class="language-switcher" aria-label="<?php esc_attr_e( 'Region', 'cb-peoplesafe2027' ); ?>">
		<button type="button" class="dropdown-toggle language-switcher__toggle" aria-controls="language-switcher-menu" aria-expanded="false">
			<?php cb_peoplesafe2027_flag_img( $current['flag'] ); ?>
			<span class="language-switcher__code"><?php echo esc_html( strtoupper( $current['slug'] ) ); ?></span>
			<span class="visually-hidden">
				<?php
				/* translators: %s: current region name */
				echo esc_html( sprintf( __( 'Region: %s. Change region', 'cb-peoplesafe2027' ), $current['name'] ) );
				?>
			</span>
			<svg class="language-switcher__chevron" width="10" height="6" viewBox="0 0 10 6" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M1 1l4 4 4-4" /></svg>
		</button>
		<ul class="dropdown-menu language-switcher__menu" id="language-switcher-menu">
			<?php foreach ( $items as $item ) { ?>
				<li class="language-switcher__item<?php echo $item['current'] ? ' is-current' : ''; ?><?php echo $item['fallback'] ? ' is-fallback' : ''; ?>">
					<a class="language-switcher__link" href="<?php echo esc_url( $item['url'] ); ?>" hreflang="<?php echo esc_attr( $item['locale'] ); ?>" lang="<?php echo esc_attr( $item['locale'] ); ?>"<?php echo $item['current'] ? ' aria-current="true"' : ''; ?>>
						<?php cb_peoplesafe2027_flag_img( $item['flag'] ); ?>
						<span><?php echo esc_html( $item['name'] ); ?></span>
					</a>
				</li>
			<?php } ?>
		</ul>
	</nav>
	<?php
}
