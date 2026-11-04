<?php
/**
 * Landing pages: built with the site's blocks like any page, or supplied as HTML.
 *
 * The way a landing page is built is its Template (the native Template choice in the editor sidebar and the Page
 * Attributes box):
 *
 * - Default template: blocks, with the site header and footer.
 * - Landing page: blocks, minimal header and footer (logo only; the intro and small print in the footer).
 * - Landing page: custom HTML. The page's HTML is its content, in a plain code box, output exactly as saved between
 *   the site header and footer.
 * - Landing page: custom HTML, minimal header and footer.
 * - Landing page: custom HTML, full document. The HTML is the whole response: no site head, header, footer or
 *   scripts, so a complete .html file can be supplied. Anything missing (tracking, for example) is the supplier's.
 *
 * A custom HTML page is never parsed, cleaned or escaped here. WordPress's own rule applies on save: only users with
 * the unfiltered_html capability keep scripts and styles; for anyone else they are stripped. The HTML is the page's
 * normal content, so it has the normal revisions. Landing pages are hidden from search engines unless Yoast's
 * "Allow search engines to show this page" is set to Yes on the page.
 *
 * @package cb-peoplesafe2027
 */

defined( 'ABSPATH' ) || exit;

/**
 * What each landing page template does.
 *
 * @return array template file => html (bool) and chrome (site, minimal or none).
 */
function cb_peoplesafe2027_landing_templates() {
	return array(
		'page-templates/landing-minimal.php'      => array( 'html' => false, 'chrome' => 'minimal' ),
		'page-templates/landing-html.php'         => array( 'html' => true, 'chrome' => 'site' ),
		'page-templates/landing-html-minimal.php' => array( 'html' => true, 'chrome' => 'minimal' ),
		'page-templates/landing-html-none.php'    => array( 'html' => true, 'chrome' => 'none' ),
	);
}

/**
 * How a landing page is built and what surrounds it, from its template.
 *
 * @param int $post_id Post ID (the current post by default).
 * @return array html (bool) and chrome (site, minimal or none).
 */
function cb_peoplesafe2027_landing_setup( $post_id = 0 ) {
	$post_id  = $post_id ? $post_id : get_the_ID();
	$template = (string) get_post_meta( $post_id, '_wp_page_template', true );
	$all      = cb_peoplesafe2027_landing_templates();

	return isset( $all[ $template ] ) ? $all[ $template ] : array( 'html' => false, 'chrome' => 'site' );
}

/**
 * Whether the header and footer show the logo and intro only (the root hub, and a minimal landing page).
 *
 * @return bool
 */
function cb_peoplesafe2027_logo_only() {
	return is_singular( 'root_hub' ) || ( is_singular( 'landing_page' ) && 'minimal' === cb_peoplesafe2027_landing_setup( get_queried_object_id() )['chrome'] );
}

/**
 * Print a landing page. Called by the landing page templates.
 *
 * @return void
 */
function cb_peoplesafe2027_landing_render() {
	while ( have_posts() ) {
		the_post();

		$setup = cb_peoplesafe2027_landing_setup( get_the_ID() );

		if ( 'none' === $setup['chrome'] ) {
			// The saved HTML is the whole page.
			echo get_post_field( 'post_content', get_the_ID() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- the supplied page, as saved by someone allowed to post unfiltered HTML.
			continue;
		}

		get_header();

		if ( $setup['html'] ) {
			echo get_post_field( 'post_content', get_the_ID() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- as above.
		} else {
			the_content();
		}

		get_footer();
	}
}

/**
 * Whether a post is a landing page built from custom HTML.
 *
 * @param WP_Post|int|null $post Post.
 * @return bool
 */
function cb_peoplesafe2027_landing_is_html( $post ) {
	$post = get_post( $post );

	return $post && 'landing_page' === $post->post_type && cb_peoplesafe2027_landing_setup( $post->ID )['html'];
}

/**
 * A custom HTML landing page is edited in a plain code box, not the block editor.
 *
 * @param bool    $use  Whether to use the block editor.
 * @param WP_Post $post The post.
 * @return bool
 */
function cb_peoplesafe2027_landing_block_editor( $use, $post ) {
	return cb_peoplesafe2027_landing_is_html( $post ) ? false : $use;
}
add_filter( 'use_block_editor_for_post', 'cb_peoplesafe2027_landing_block_editor', 10, 2 );

/**
 * No visual editor for custom HTML: it would rewrite the markup.
 *
 * @param bool $can Whether the user can use the visual editor.
 * @return bool
 */
function cb_peoplesafe2027_landing_no_visual( $can ) {
	global $post;

	return ( $post && cb_peoplesafe2027_landing_is_html( $post ) ) ? false : $can;
}
add_filter( 'user_can_richedit', 'cb_peoplesafe2027_landing_no_visual' );

/**
 * A bigger box, with no media button or quick tags, for custom HTML.
 *
 * @param array  $settings  Editor settings.
 * @param string $editor_id Editor ID.
 * @return array
 */
function cb_peoplesafe2027_landing_editor_settings( $settings, $editor_id ) {
	global $post;

	if ( 'content' === $editor_id && $post && cb_peoplesafe2027_landing_is_html( $post ) ) {
		$settings['media_buttons'] = false;
		$settings['quicktags']     = false;
		$settings['tinymce']       = false;
		$settings['textarea_rows'] = 32;
		$settings['editor_class']  = 'cb-landing-html';
	}

	return $settings;
}
add_filter( 'wp_editor_settings', 'cb_peoplesafe2027_landing_editor_settings', 10, 2 );

/**
 * Above the code box: what to put in it, and a way to load a file. The file is read in the browser into the box
 * (nothing is uploaded).
 *
 * @param WP_Post $post The post.
 * @return void
 */
function cb_peoplesafe2027_landing_html_help( $post ) {
	if ( ! cb_peoplesafe2027_landing_is_html( $post ) ) {
		return;
	}

	$chrome = cb_peoplesafe2027_landing_setup( $post->ID )['chrome'];
	?>
	<style>.cb-landing-html{width:100%;font-family:monospace;font-size:13px}</style>
	<div class="notice notice-info inline">
		<p>
			<?php
			if ( 'none' === $chrome ) {
				esc_html_e( 'Custom HTML, full document: what is saved below is the whole page, output exactly as written. Supply a complete HTML file. The site\'s header, footer, scripts and tracking are not added.', 'cb-peoplesafe2027' );
			} else {
				esc_html_e( 'Custom HTML: what is saved below is output exactly as written, between the site\'s header and footer. Use the page\'s body content only: remove the html, head and body tags and any header or footer first.', 'cb-peoplesafe2027' );
			}
			?>
		</p>
		<p>
			<label for="cb-landing-file"><strong><?php esc_html_e( 'Load from an .html file', 'cb-peoplesafe2027' ); ?></strong></label>
			<input type="file" id="cb-landing-file" accept=".html,.htm,text/html">
			<span id="cb-landing-file-note" class="description"></span>
		</p>
	</div>
	<script>
	( function () {
		var file = document.getElementById( 'cb-landing-file' ), area = document.getElementById( 'content' ), note = document.getElementById( 'cb-landing-file-note' );
		if ( ! file || ! area ) {
			return;
		}
		file.addEventListener( 'change', function () {
			var chosen = file.files[ 0 ], reader;
			if ( ! chosen ) {
				return;
			}
			reader = new FileReader();
			reader.onload = function () {
				area.value = reader.result;
				note.textContent = ' Loaded ' + chosen.name + ' (' + Math.round( chosen.size / 1024 ) + ' KB). Update the page to save it.';
			};
			reader.readAsText( chosen );
		} );
	}() );
	</script>
	<?php
}
add_action( 'edit_form_after_title', 'cb_peoplesafe2027_landing_html_help' );

/**
 * Hidden from search engines unless the page's Yoast setting says Yes (index).
 *
 * @param array $robots Yoast's robots directives.
 * @return array
 */
function cb_peoplesafe2027_landing_robots( $robots ) {
	if ( is_singular( 'landing_page' ) && '2' !== (string) get_post_meta( get_queried_object_id(), '_yoast_wpseo_meta-robots-noindex', true ) ) {
		$robots['index'] = 'noindex';
	}

	return $robots;
}
add_filter( 'wpseo_robots_array', 'cb_peoplesafe2027_landing_robots' );

/**
 * A "Built with" column in the Landing pages list.
 *
 * @param array $columns Columns.
 * @return array
 */
function cb_peoplesafe2027_landing_columns( $columns ) {
	$columns['cb_built'] = __( 'Built with', 'cb-peoplesafe2027' );

	return $columns;
}
add_filter( 'manage_landing_page_posts_columns', 'cb_peoplesafe2027_landing_columns' );

/**
 * Fill the column.
 *
 * @param string $column  Column.
 * @param int    $post_id Post ID.
 * @return void
 */
function cb_peoplesafe2027_landing_column( $column, $post_id ) {
	if ( 'cb_built' === $column ) {
		echo cb_peoplesafe2027_landing_setup( $post_id )['html'] ? esc_html__( 'Custom HTML', 'cb-peoplesafe2027' ) : esc_html__( 'Blocks', 'cb-peoplesafe2027' );
	}
}
add_action( 'manage_landing_page_posts_custom_column', 'cb_peoplesafe2027_landing_column', 10, 2 );
