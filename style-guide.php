<?php
/**
 * Template Name: Style Guide
 *
 * A living style guide: every design token in src/css/tokens.css, shown as it
 * renders, plus the elements and components built from them. The token
 * sections are generated from tokens.css, so they cannot drift. Add a section
 * for each new component or block as it is built.
 *
 * Editors only: anyone else gets the 404 page. Always noindex.
 *
 * @package cb-peoplesafe2027
 */

defined( 'ABSPATH' ) || exit;

if ( ! current_user_can( 'edit_pages' ) ) {
	global $wp_query;
	$wp_query->set_404();
	status_header( 404 );
	nocache_headers();
	get_template_part( '404' );
	return;
}

add_filter( 'wp_robots', 'wp_robots_no_robots' );

/**
 * Read the :root custom properties from tokens.css, in file order.
 *
 * @return string[] Token name (without the leading dashes) => value.
 */
function cb_peoplesafe2027_style_guide_tokens() {
	$file = get_template_directory() . '/src/css/tokens.css';

	if ( ! is_readable( $file ) ) {
		return array();
	}

	// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	$css = (string) file_get_contents( $file );
	$css = preg_replace( '#/\*.*?\*/#s', '', $css );
	preg_match_all( '/--([\w-]+)\s*:\s*([^;]+);/', $css, $matches, PREG_SET_ORDER );

	$tokens = array();

	foreach ( $matches as $match ) {
		$tokens[ $match[1] ] = trim( preg_replace( '/\s+/', ' ', $match[2] ) );
	}

	return $tokens;
}

/**
 * Tokens whose name starts with a prefix.
 *
 * @param string[] $tokens Tokens.
 * @param string   $prefix Name prefix.
 * @return string[]
 */
function cb_peoplesafe2027_style_guide_group( $tokens, $prefix ) {
	return array_filter(
		$tokens,
		function ( $name ) use ( $prefix ) {
			return 0 === strpos( $name, $prefix );
		},
		ARRAY_FILTER_USE_KEY
	);
}

$tokens = cb_peoplesafe2027_style_guide_tokens();

get_header();
?>

<style>
	.sg { padding-block: var(--space-5); }
	.sg h2 { margin-top: var(--space-6); padding-bottom: var(--space-2); border-bottom: 1px solid var(--col-border); }
	.sg h3 { margin-top: var(--space-5); font-size: var(--fs-base); text-transform: uppercase; }
	.sg__row { display: grid; grid-template-columns: minmax(10rem, 14rem) 1fr; gap: var(--space-3); align-items: center; padding-block: var(--space-2); border-bottom: 1px solid var(--col-neutral); }
	.sg__name { font-size: var(--fs-small); }
	.sg__name small { display: block; color: var(--col-body-muted); word-break: break-all; }
	.sg__chip { min-height: 2.5rem; border: 1px solid var(--col-border); border-radius: var(--radius-sm); }
	.sg__bar { height: 1rem; background: var(--col-blue-400); }
	.sg__box { width: 6rem; height: 4rem; background: var(--col-white); border: 1px solid var(--col-neutral); }
	.sg__dark { background: var(--col-navy-400); padding: var(--space-4); border-radius: var(--radius-sm); }
	.sg__grad { background: var(--gradient-orange); padding: var(--space-4); border-radius: var(--radius-sm); }
	.sg__toc { display: flex; flex-wrap: wrap; gap: var(--space-3); padding: 0; list-style: none; }
</style>

<div class="container sg">
	<h1>Style guide</h1>
	<p>
		Generated from <code>src/css/tokens.css</code>. Root font size 18px, so 1rem = 18px.
		Breakpoints (in <code>tokens.config.js</code>): sm 576, md 768, lg 992, xl 1240, xxl 1400.
	</p>
	<ul class="sg__toc">
		<?php foreach ( array( 'colour', 'gradients', 'type', 'headings', 'spacing', 'radius', 'shadows', 'motion', 'stacking', 'buttons', 'layout' ) as $section ) { ?>
			<li><a href="#<?php echo esc_attr( $section ); ?>"><?php echo esc_html( ucfirst( $section ) ); ?></a></li>
		<?php } ?>
	</ul>

	<h2 id="colour">Colour</h2>
	<?php foreach ( array_merge( cb_peoplesafe2027_style_guide_group( $tokens, 'col-' ), cb_peoplesafe2027_style_guide_group( $tokens, 'overlay' ) ) as $name => $value ) { ?>
		<div class="sg__row">
			<div class="sg__name">--<?php echo esc_html( $name ); ?><small><?php echo esc_html( $value ); ?></small></div>
			<div class="sg__chip" style="background: var(--<?php echo esc_attr( $name ); ?>)"></div>
		</div>
	<?php } ?>

	<h2 id="gradients">Gradients</h2>
	<?php foreach ( cb_peoplesafe2027_style_guide_group( $tokens, 'gradient-' ) as $name => $value ) { ?>
		<?php if ( 0 === strpos( $value, 'linear-gradient' ) ) { ?>
			<div class="sg__row">
				<div class="sg__name">--<?php echo esc_html( $name ); ?></div>
				<div class="sg__chip" style="background: var(--<?php echo esc_attr( $name ); ?>)"></div>
			</div>
		<?php } else { ?>
			<div class="sg__row"><div class="sg__name">--<?php echo esc_html( $name ); ?></div><div><?php echo esc_html( $value ); ?></div></div>
		<?php } ?>
	<?php } ?>

	<h2 id="type">Type</h2>
	<div class="sg__row"><div class="sg__name">--font-family-base</div><div style="font-family: var(--font-family-base)"><?php echo esc_html( isset( $tokens['font-family-base'] ) ? $tokens['font-family-base'] : '' ); ?></div></div>
	<h3>Sizes</h3>
	<?php foreach ( cb_peoplesafe2027_style_guide_group( $tokens, 'fs-' ) as $name => $value ) { ?>
		<div class="sg__row">
			<div class="sg__name">--<?php echo esc_html( $name ); ?><small><?php echo esc_html( $value ); ?></small></div>
			<div style="font-size: var(--<?php echo esc_attr( $name ); ?>); line-height: var(--line-height-heading)">Protect your people. Wherever. Whenever.</div>
		</div>
	<?php } ?>
	<h3>Weights</h3>
	<?php foreach ( cb_peoplesafe2027_style_guide_group( $tokens, 'fw-' ) as $name => $value ) { ?>
		<div class="sg__row">
			<div class="sg__name">--<?php echo esc_html( $name ); ?><small><?php echo esc_html( $value ); ?></small></div>
			<div style="font-weight: var(--<?php echo esc_attr( $name ); ?>)">Protect your people. Wherever. Whenever.</div>
		</div>
	<?php } ?>

	<h2 id="headings">Headings and text</h2>
	<h1>Heading 1</h1>
	<h2 style="margin-top: 0; border: 0">Heading 2</h2>
	<h3 style="margin-top: 0; font-size: var(--fs-h3); text-transform: none">Heading 3</h3>
	<h4>Heading 4</h4>
	<h5>Heading 5</h5>
	<h6>Heading 6</h6>
	<p>Body copy at 18px, light weight. <a href="#headings">A link</a>, <strong>strong text</strong> and <em>emphasis</em>.</p>
	<ul><li>List item one</li><li>List item two</li></ul>
	<p><small>Small text.</small></p>

	<h2 id="spacing">Spacing</h2>
	<?php foreach ( cb_peoplesafe2027_style_guide_group( $tokens, 'space-' ) as $name => $value ) { ?>
		<div class="sg__row">
			<div class="sg__name">--<?php echo esc_html( $name ); ?><small><?php echo esc_html( $value ); ?></small></div>
			<div><div class="sg__bar" style="width: var(--<?php echo esc_attr( $name ); ?>)"></div></div>
		</div>
	<?php } ?>

	<h2 id="radius">Radius</h2>
	<?php foreach ( cb_peoplesafe2027_style_guide_group( $tokens, 'radius-' ) as $name => $value ) { ?>
		<div class="sg__row">
			<div class="sg__name">--<?php echo esc_html( $name ); ?><small><?php echo esc_html( $value ); ?></small></div>
			<div><div class="sg__box" style="background: var(--col-neutral); border-radius: var(--<?php echo esc_attr( $name ); ?>)"></div></div>
		</div>
	<?php } ?>

	<h2 id="shadows">Shadows</h2>
	<?php foreach ( cb_peoplesafe2027_style_guide_group( $tokens, 'shadow-' ) as $name => $value ) { ?>
		<div class="sg__row">
			<div class="sg__name">--<?php echo esc_html( $name ); ?></div>
			<div style="padding: var(--space-3)"><div class="sg__box" style="box-shadow: var(--<?php echo esc_attr( $name ); ?>)"></div></div>
		</div>
	<?php } ?>

	<h2 id="motion">Motion and borders</h2>
	<?php foreach ( array_merge( cb_peoplesafe2027_style_guide_group( $tokens, 'transition' ), cb_peoplesafe2027_style_guide_group( $tokens, 'border-width' ) ) as $name => $value ) { ?>
		<div class="sg__row"><div class="sg__name">--<?php echo esc_html( $name ); ?></div><div><?php echo esc_html( $value ); ?></div></div>
	<?php } ?>

	<h2 id="stacking">Stacking</h2>
	<?php foreach ( cb_peoplesafe2027_style_guide_group( $tokens, 'z-' ) as $name => $value ) { ?>
		<div class="sg__row"><div class="sg__name">--<?php echo esc_html( $name ); ?></div><div><?php echo esc_html( $value ); ?></div></div>
	<?php } ?>

	<h2 id="buttons">Buttons</h2>
	<h3>On white</h3>
	<div class="button-group">
		<a class="button button--primary" href="#buttons">Primary</a>
		<a class="button button--outline" href="#buttons">Outline</a>
		<a class="button button--primary button--small" href="#buttons">Small primary</a>
		<a class="button button--outline button--small" href="#buttons">Small outline</a>
		<button class="button button--primary" type="button" disabled>Disabled</button>
	</div>
	<h3>On dark</h3>
	<div class="sg__dark"><div class="button-group"><a class="button button--outline-white" href="#buttons">Outline white</a></div></div>
	<h3>On a gradient banner</h3>
	<div class="sg__grad"><div class="button-group"><a class="button button--white" href="#buttons">White</a></div></div>
	<h3>Core Buttons block</h3>
	<div class="wp-block-buttons" style="margin-bottom: var(--space-4)">
		<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="#buttons">Core button</a></div>
		<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="#buttons">Core outline</a></div>
	</div>
	<p>Button tokens:</p>
	<?php foreach ( cb_peoplesafe2027_style_guide_group( $tokens, 'button-' ) as $name => $value ) { ?>
		<div class="sg__row"><div class="sg__name">--<?php echo esc_html( $name ); ?></div><div><?php echo esc_html( $value ); ?></div></div>
	<?php } ?>

	<h2 id="layout">Layout</h2>
	<?php foreach ( cb_peoplesafe2027_style_guide_group( $tokens, 'container-' ) as $name => $value ) { ?>
		<div class="sg__row"><div class="sg__name">--<?php echo esc_html( $name ); ?></div><div><?php echo esc_html( $value ); ?></div></div>
	<?php } ?>
	<?php foreach ( array( 'grid-columns', 'grid-gutter', 'grid-min', 'nav-height', 'line-height-base', 'line-height-heading' ) as $name ) { ?>
		<?php if ( isset( $tokens[ $name ] ) ) { ?>
			<div class="sg__row"><div class="sg__name">--<?php echo esc_html( $name ); ?></div><div><?php echo esc_html( $tokens[ $name ] ); ?></div></div>
		<?php } ?>
	<?php } ?>
</div>

<?php
get_footer();
