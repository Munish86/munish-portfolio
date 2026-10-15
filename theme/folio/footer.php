<?php
/**
 * Site footer: name and bio, menu, contact details, social links, copyright.
 *
 * @package folio
 */

defined( 'ABSPATH' ) || exit;
$email    = folio_opt( 'email' );
$phone    = folio_opt( 'phone' );
$location = folio_opt( 'location' );
?>
</main>
<footer class="site-footer">
	<div class="container">
		<div class="site-footer__top">
			<div class="site-footer__brand">
				<?php folio_logo(); ?>
				<p><?php echo esc_html( folio_opt( 'bio' ) ); ?></p>
				<?php echo folio_social_links(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside. ?>
			</div>
			<nav class="site-footer__col" aria-label="<?php esc_attr_e( 'Footer', 'folio' ); ?>">
				<h2 class="site-footer__heading"><?php esc_html_e( 'Explore', 'folio' ); ?></h2>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => has_nav_menu( 'footer' ) ? 'footer' : 'primary',
						'container'      => false,
						'menu_class'     => 'footer-menu',
						'depth'          => 1,
						'fallback_cb'    => 'folio_menu_fallback',
					)
				);
				?>
			</nav>
			<div class="site-footer__col">
				<h2 class="site-footer__heading"><?php esc_html_e( 'Get in touch', 'folio' ); ?></h2>
				<ul class="footer-contact">
					<?php if ( $email ) : ?>
						<li><?php echo folio_icon( 'mail' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?><a href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>"><?php echo esc_html( antispambot( $email ) ); ?></a></li>
					<?php endif; ?>
					<?php if ( $phone ) : ?>
						<li><?php echo folio_icon( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?><a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a></li>
					<?php endif; ?>
					<?php if ( $location ) : ?>
						<li><?php echo folio_icon( 'pin' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?><span><?php echo esc_html( $location ); ?></span></li>
					<?php endif; ?>
				</ul>
				<?php $contact = folio_contact_url(); ?>
				<?php if ( $contact ) : ?>
					<?php echo folio_btn( __( 'Start a project', 'folio' ), $contact, 'primary' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside. ?>
				<?php endif; ?>
			</div>
		</div>
		<div class="site-footer__bottom">
			<p>&copy; <?php echo esc_html( gmdate( 'Y' ) . ' ' . folio_owner_name() ); ?>. <?php echo esc_html( folio_opt( 'footer_text' ) ); ?></p>
			<a class="back-top" href="#top"><?php esc_html_e( 'Back to top', 'folio' ); ?> <span aria-hidden="true">&uarr;</span></a>
		</div>
	</div>
</footer>
<div class="lightbox" hidden>
	<button class="icon-btn lightbox__close" type="button" aria-label="<?php esc_attr_e( 'Close', 'folio' ); ?>"><?php echo folio_icon( 'close' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?></button>
	<img alt="">
</div>
<?php wp_footer(); ?>
</body>
</html>
