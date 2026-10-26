<?php
/**
 * Footer template.
 *
 * Three menus (Footer: Products, About us, Resources, set under Appearance,
 * Menus), a Sectors list built from the pages under Sectors, and the intro
 * column. The menus follow the region the same way the rest of the site does
 * (inc/regions/menu.php).
 *
 * @package cb-peoplesafe2027
 */

$cb_region = function_exists( 'pll_current_language' ) && pll_current_language() ? pll_current_language() : '';
$cb_home   = $cb_region && function_exists( 'cb_peoplesafe2027_region_url' ) ? cb_peoplesafe2027_region_url( $cb_region ) : home_url( '/' );
?>
</main>

<footer id="footer" class="site-footer">
	<div class="container site-footer__main">
		<div class="site-footer__grid">
			<div class="site-footer__intro">
				<img src="<?php echo esc_url( get_template_directory_uri() . '/img/ps-logo-full.svg' ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" width="200" height="45" loading="lazy">
				<p><?php esc_html_e( 'The UK\'s leading provider of technology-enabled employee safety solutions.', 'cb-peoplesafe2027' ); ?></p>
				<a class="button button--primary" href="<?php echo esc_url( trailingslashit( $cb_home ) . 'contact-us/' ); ?>"><span><?php esc_html_e( 'Get in touch', 'cb-peoplesafe2027' ); ?></span></a>
				<?php echo do_shortcode( '[social_icons class="site-footer__social"]' ); ?>
				<div class="site-footer__newsletter">
					<div class="site-footer__title"><?php esc_html_e( 'Newsletter sign up', 'cb-peoplesafe2027' ); ?></div>
					<form target="_blank" method="get" action="https://peoplesafe.co.uk/newsletter-signup/">
						<label class="visually-hidden" for="footer-newsletter-email"><?php esc_html_e( 'Email address', 'cb-peoplesafe2027' ); ?></label>
						<p class="site-footer__help" id="footer-newsletter-help"><?php esc_html_e( 'We\'ll never share your email with anyone else.', 'cb-peoplesafe2027' ); ?></p>
						<div class="site-footer__field">
							<input type="email" id="footer-newsletter-email" name="email" aria-describedby="footer-newsletter-help" placeholder="<?php esc_attr_e( 'Email address', 'cb-peoplesafe2027' ); ?>" required>
							<button class="button button--primary" type="submit"><span><?php esc_html_e( 'Sign up', 'cb-peoplesafe2027' ); ?></span></button>
						</div>
					</form>
				</div>
			</div>

			<nav class="site-footer__col" aria-labelledby="footer-products">
				<div class="site-footer__title" id="footer-products"><?php esc_html_e( 'Products', 'cb-peoplesafe2027' ); ?></div>
				<?php cb_peoplesafe2027_footer_menu( 'footer_products' ); ?>
			</nav>

			<nav class="site-footer__col" aria-labelledby="footer-sectors">
				<div class="site-footer__title" id="footer-sectors"><?php esc_html_e( 'Sectors', 'cb-peoplesafe2027' ); ?></div>
				<ul class="menu">
					<?php foreach ( cb_peoplesafe2027_footer_sectors() as $cb_sector ) { ?>
						<li><a href="<?php echo esc_url( get_permalink( $cb_sector ) ); ?>"><?php echo esc_html( cb_peoplesafe2027_footer_sector_title( $cb_sector ) ); ?></a></li>
					<?php } ?>
				</ul>
			</nav>

			<div class="site-footer__col">
				<nav aria-labelledby="footer-about">
					<div class="site-footer__title" id="footer-about"><?php esc_html_e( 'About us', 'cb-peoplesafe2027' ); ?></div>
					<?php cb_peoplesafe2027_footer_menu( 'footer_about' ); ?>
				</nav>
				<nav aria-labelledby="footer-resources">
					<div class="site-footer__title" id="footer-resources"><?php esc_html_e( 'Resources', 'cb-peoplesafe2027' ); ?></div>
					<?php cb_peoplesafe2027_footer_menu( 'footer_resources' ); ?>
				</nav>
			</div>
		</div>
	</div>

	<div id="colophon" class="site-footer__colophon">
		<div class="container">
			<p>
				&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?>
				<?php esc_html_e( 'Skyguard Ltd T/A Peoplesafe. Company Registration Number: 04107459. All rights reserved. Peoplesafe is part of the Send for Help Group.', 'cb-peoplesafe2027' ); ?>
			</p>
			<a href="https://www.chillibyte.co.uk/" rel="nofollow noopener" target="_blank" class="site-footer__credit" title="Digital Marketing by Chillibyte">
				<img src="<?php echo esc_url( get_template_directory_uri() . '/img/cb.png' ); ?>" alt="Digital Marketing by Chillibyte" width="33" height="16" loading="lazy">
			</a>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
