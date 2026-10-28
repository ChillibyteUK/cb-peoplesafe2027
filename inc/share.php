<?php
/**
 * Share this page: a button that opens a dialog of ways to share it, and the dialog.
 *
 * No plugin and no third-party script: each network is an ordinary link to its own share address, so nothing is
 * loaded from them until someone chooses one. The dialog is the site's native <dialog> (src/js/dialog.js); the Copy
 * link button and the phone's own share sheet are src/js/share.js.
 *
 * @package cb-peoplesafe2027
 */

defined( 'ABSPATH' ) || exit;

/**
 * Icon paths for the share dialog (24 x 24). Brand marks are filled, the others are drawn in line.
 *
 * @param string $name x, linkedin, facebook, whatsapp, email, link or share.
 * @return string SVG markup.
 */
function cb_peoplesafe2027_share_icon( $name ) {
	$filled = array(
		'x'        => 'M18.9 1.15h3.68l-8.04 9.19L24 22.85h-7.4l-5.8-7.58-6.63 7.58H.49l8.6-9.83L0 1.15h7.59l5.24 6.93 6.07-6.93zm-1.29 19.5h2.04L6.48 3.24H4.3l13.31 17.41z',
		'linkedin' => 'M20.45 20.45h-3.56v-5.57c0-1.33-.03-3.04-1.85-3.04-1.85 0-2.14 1.45-2.14 2.94v5.67H9.35V9h3.41v1.56h.05c.48-.9 1.64-1.85 3.37-1.85 3.6 0 4.27 2.37 4.27 5.46v6.28zM5.34 7.43a2.06 2.06 0 1 1 0-4.12 2.06 2.06 0 0 1 0 4.12zM7.12 20.45H3.56V9h3.56v11.45z',
		'facebook' => 'M24 12.07C24 5.4 18.63 0 12 0S0 5.4 0 12.07C0 18.1 4.39 23.1 10.13 24v-8.44H7.08v-3.49h3.05V9.41c0-3.02 1.79-4.69 4.53-4.69 1.31 0 2.69.24 2.69.24v2.97h-1.51c-1.49 0-1.96.93-1.96 1.88v2.26h3.33l-.53 3.49h-2.8V24C19.61 23.1 24 18.1 24 12.07z',
		'whatsapp' => 'M17.47 14.38c-.3-.15-1.76-.87-2.03-.97-.27-.1-.47-.15-.67.15-.2.3-.77.97-.94 1.17-.17.2-.35.22-.64.07-.3-.15-1.26-.46-2.4-1.48-.89-.79-1.49-1.77-1.66-2.07-.17-.3-.02-.46.13-.61.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.03-.52-.07-.15-.67-1.62-.92-2.22-.24-.58-.49-.5-.67-.51h-.57c-.2 0-.52.07-.8.37-.27.3-1.04 1.02-1.04 2.48 0 1.46 1.07 2.88 1.21 3.07.15.2 2.1 3.2 5.08 4.49.71.31 1.26.49 1.69.63.71.23 1.36.2 1.87.12.57-.08 1.76-.72 2.01-1.41.25-.7.25-1.29.17-1.41-.07-.12-.27-.2-.57-.35zM12.05 21.8h-.01a9.87 9.87 0 0 1-5.03-1.38l-.36-.21-3.74.98 1-3.65-.24-.37a9.86 9.86 0 0 1-1.51-5.26c0-5.45 4.44-9.88 9.89-9.88 2.64 0 5.12 1.03 6.99 2.9a9.82 9.82 0 0 1 2.89 6.99c0 5.45-4.44 9.88-9.88 9.88zM20.52 3.45A11.8 11.8 0 0 0 12.05 0C5.5 0 .16 5.34.16 11.89c0 2.1.55 4.14 1.59 5.95L.06 24l6.3-1.65a11.88 11.88 0 0 0 5.68 1.45h.01c6.55 0 11.89-5.34 11.89-11.89 0-3.18-1.24-6.16-3.42-8.46z',
	);

	$line = array(
		'email' => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>',
		'link'  => '<path d="M10 13a5 5 0 0 0 7.5.5l3-3a5 5 0 0 0-7-7l-1.7 1.7"/><path d="M14 11a5 5 0 0 0-7.5-.5l-3 3a5 5 0 0 0 7 7l1.7-1.7"/>',
		'share' => '<circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><path d="m8.6 13.5 6.8 4M15.4 6.5l-6.8 4"/>',
	);

	if ( isset( $filled[ $name ] ) ) {
		return '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false"><path d="' . esc_attr( $filled[ $name ] ) . '"/></svg>';
	}

	return isset( $line[ $name ] ) ? '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $line[ $name ] . '</svg>' : '';
}

/**
 * The Share button: opens the share dialog printed by cb_peoplesafe2027_share_dialog().
 *
 * @return string Button markup.
 */
function cb_peoplesafe2027_share_button() {
	return '<button type="button" class="share-button" data-dialog-target="share-dialog">' . cb_peoplesafe2027_share_icon( 'share' ) . '<span>' . esc_html__( 'Share', 'cb-peoplesafe2027' ) . '</span></button>';
}

/**
 * The share dialog for a page: X, LinkedIn, Facebook, WhatsApp, email, copy link, and the phone's own sheet where
 * the browser offers one.
 *
 * @param string $url   Address to share.
 * @param string $title Title to share.
 * @return void
 */
function cb_peoplesafe2027_share_dialog( $url, $title ) {
	$encoded_url   = rawurlencode( $url );
	$encoded_title = rawurlencode( $title );

	$targets = array(
		'x'        => array( 'X', 'https://twitter.com/intent/tweet?url=' . $encoded_url . '&text=' . $encoded_title ),
		'linkedin' => array( 'LinkedIn', 'https://www.linkedin.com/sharing/share-offsite/?url=' . $encoded_url ),
		'facebook' => array( 'Facebook', 'https://www.facebook.com/sharer/sharer.php?u=' . $encoded_url ),
		'whatsapp' => array( 'WhatsApp', 'https://wa.me/?text=' . $encoded_title . '%20' . $encoded_url ),
		'email'    => array( __( 'Email', 'cb-peoplesafe2027' ), 'mailto:?subject=' . $encoded_title . '&body=' . $encoded_url ),
	);
	?>
	<dialog id="share-dialog" class="site-dialog site-dialog--narrow" aria-labelledby="share-dialog-title" data-share-url="<?php echo esc_url( $url ); ?>" data-share-title="<?php echo esc_attr( $title ); ?>">
		<div class="site-dialog__header">
			<h2 class="site-dialog__title" id="share-dialog-title"><?php esc_html_e( 'Share this page', 'cb-peoplesafe2027' ); ?></h2>
			<button type="button" class="site-dialog__close" data-dialog-close aria-label="<?php esc_attr_e( 'Close', 'cb-peoplesafe2027' ); ?>">&times;</button>
		</div>
		<div class="site-dialog__body">
			<ul class="share-list">
				<?php foreach ( $targets as $slug => $target ) { ?>
					<li><a class="share-list__item" href="<?php echo esc_url( $target[1] ); ?>" target="_blank" rel="noopener noreferrer"><?php echo cb_peoplesafe2027_share_icon( $slug ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed SVG. ?><span><?php echo esc_html( $target[0] ); ?></span></a></li>
				<?php } ?>
				<li><button type="button" class="share-list__item" data-share-copy><?php echo cb_peoplesafe2027_share_icon( 'link' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed SVG. ?><span data-share-copy-label><?php esc_html_e( 'Copy link', 'cb-peoplesafe2027' ); ?></span></button></li>
				<li hidden data-share-native><button type="button" class="share-list__item" data-share-native-button><?php echo cb_peoplesafe2027_share_icon( 'share' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed SVG. ?><span><?php esc_html_e( 'More options', 'cb-peoplesafe2027' ); ?></span></button></li>
			</ul>
		</div>
	</dialog>
	<?php
}
