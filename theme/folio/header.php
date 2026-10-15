<?php
/**
 * Site header: logo, menu, light/dark switch, contact button, mobile menu.
 *
 * @package folio
 */

defined( 'ABSPATH' ) || exit;
$contact = folio_contact_url();
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#main"><?php esc_html_e( 'Skip to content', 'folio' ); ?></a>
<div class="cursor-glow" aria-hidden="true"></div>
<header class="site-header" id="top">
	<div class="container site-header__inner">
		<?php folio_logo(); ?>
		<nav class="site-nav" id="site-nav" aria-label="<?php esc_attr_e( 'Main', 'folio' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'menu',
					'depth'          => 2,
					'fallback_cb'    => 'folio_menu_fallback',
				)
			);
			?>
			<div class="site-nav__mobile-extra">
				<?php echo folio_social_links(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside. ?>
			</div>
		</nav>
		<div class="site-header__actions">
			<button class="icon-btn theme-toggle" type="button" aria-label="<?php esc_attr_e( 'Switch light / dark mode', 'folio' ); ?>" title="<?php esc_attr_e( 'Light / dark', 'folio' ); ?>">
				<?php echo folio_icon( 'sun', 'theme-toggle__sun' ) . folio_icon( 'moon', 'theme-toggle__moon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
			</button>
			<?php if ( $contact ) : ?>
				<a class="btn btn--primary btn--sm site-header__cta" href="<?php echo esc_url( $contact ); ?>"><span><?php esc_html_e( "Let's talk", 'folio' ); ?></span></a>
			<?php endif; ?>
			<button class="icon-btn nav-toggle" type="button" aria-controls="site-nav" aria-expanded="false" aria-label="<?php esc_attr_e( 'Open menu', 'folio' ); ?>">
				<?php echo folio_icon( 'menu', 'nav-toggle__open' ) . folio_icon( 'close', 'nav-toggle__close' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
			</button>
		</div>
	</div>
</header>
<main id="main" class="site-main">
