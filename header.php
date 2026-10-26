<?php
/**
 * Header template.
 *
 * @package cb-peoplesafe2027
 */

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<a class="visually-hidden" href="#main">Skip to content</a>
<?php wp_body_open(); ?>

<!-- HEADER-NAV:START -->
<?php
// The root hub (region chooser) shows the logo only.
$cb_logo_only = is_singular( 'root_hub' );
$cb_nav       = $cb_logo_only ? array() : cb_peoplesafe2027_nav_parts();
?>
<header id="masthead" class="site-header">
	<nav class="site-nav<?php echo $cb_logo_only ? ' site-nav--logo-only' : ''; ?>" aria-label="Primary navigation">
		<div class="container">
			<div class="site-nav__bar">
				<?php if ( ! $cb_logo_only ) { ?>
				<button class="site-nav__burger" id="site-nav-burger" type="button" aria-expanded="false" aria-controls="site-nav-items" aria-label="<?php esc_attr_e( 'Menu', 'cb-peoplesafe2027' ); ?>">
					<span></span>
					<span></span>
					<span></span>
					<span></span>
				</button>
				<?php } ?>
				<a href="<?php echo esc_url( function_exists( 'cb_peoplesafe2027_region_url' ) && pll_current_language() ? cb_peoplesafe2027_region_url( pll_current_language() ) : home_url( '/' ) ); ?>" class="site-nav__logo">
					<img src="<?php echo esc_url( get_template_directory_uri() . '/img/ps-logo-full--dark.svg' ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" width="200" height="45">
				</a>
				<?php if ( ! $cb_logo_only ) { ?>
				<div class="site-nav__items" id="site-nav-items">
					<div class="site-nav__main">
						<?php echo $cb_nav['main']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped when built, see inc/nav-render.php. ?>
					</div>
					<div class="site-nav__extras">
						<?php echo $cb_nav['extras']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped when built, see inc/nav-render.php. ?>
						<?php cb_peoplesafe2027_language_switcher(); ?>
					</div>
				</div>
				<?php } ?>
			</div>
			<?php if ( ! $cb_logo_only ) { ?>
			<div class="site-nav__menus" id="site-nav-menus">
				<?php echo $cb_nav['menus']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped when built, see inc/nav-render.php. ?>
			</div>
			<?php } ?>
		</div>
	</nav>
</header>
<!-- HEADER-NAV:END -->

<main id="main">
