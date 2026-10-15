<?php
/**
 * Template Name: Contact
 * Template Post Type: page
 *
 * Contact page: headline and text (Portfolio > Contact), details, social links, book-a-call button and the form.
 * Text written in the page editor is shown under the contact details (e.g. FAQ).
 *
 * @package folio
 */

defined( 'ABSPATH' ) || exit;
get_header();

$email   = folio_opt( 'email' );
$phone   = folio_opt( 'phone' );
$loc     = folio_opt( 'location' );
$booking = folio_opt( 'booking_url' );
?>
<section class="contact-page">
	<div class="hero__bg" aria-hidden="true"><span class="blob blob--1"></span><span class="blob blob--2"></span></div>
	<div class="container contact-layout">
		<div class="contact-layout__info">
			<p class="eyebrow reveal"><?php echo esc_html( get_the_title() ); ?></p>
			<h1 class="page-title reveal"><?php echo folio_highlight( folio_opt( 'contact_title' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside. ?></h1>
			<p class="page-intro reveal"><?php echo esc_html( folio_opt( 'contact_text' ) ); ?></p>
			<?php if ( folio_opt( 'available' ) ) : ?>
				<p class="status-pill reveal"><span class="status-pill__dot"></span><?php echo esc_html( folio_opt( 'available_text' ) ); ?></p>
			<?php endif; ?>
			<ul class="contact-list reveal">
				<?php if ( $email ) : ?>
					<li><span class="icon-tile icon-tile--soft"><?php echo folio_icon( 'mail' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?></span><span><small><?php esc_html_e( 'Email', 'folio' ); ?></small><a href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>"><?php echo esc_html( antispambot( $email ) ); ?></a></span></li>
				<?php endif; ?>
				<?php if ( $phone ) : ?>
					<li><span class="icon-tile icon-tile--soft"><?php echo folio_icon( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?></span><span><small><?php esc_html_e( 'Phone', 'folio' ); ?></small><a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a></span></li>
				<?php endif; ?>
				<?php if ( $loc ) : ?>
					<li><span class="icon-tile icon-tile--soft"><?php echo folio_icon( 'pin' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?></span><span><small><?php esc_html_e( 'Based in', 'folio' ); ?></small><?php echo esc_html( $loc ); ?></span></li>
				<?php endif; ?>
			</ul>
			<?php if ( $booking ) : ?>
				<div class="btn-row reveal"><?php echo folio_btn( __( 'Book a call', 'folio' ), $booking, 'ghost', 'calendar', array( 'target' => '_blank', 'rel' => 'noopener' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside. ?></div>
			<?php endif; ?>
			<?php echo folio_social_links( 'socials reveal' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside. ?>
		</div>
		<div class="contact-layout__form reveal">
			<?php echo folio_contact_form(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside. ?>
		</div>
	</div>
</section>
<?php
while ( have_posts() ) :
	the_post();
	if ( '' !== trim( get_the_content() ) ) :
		?>
		<section class="section section--tight"><div class="container"><div class="prose entry-content"><?php the_content(); ?></div></div></section>
		<?php
	endif;
endwhile;
get_footer();
