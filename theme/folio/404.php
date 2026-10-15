<?php
/**
 * Page not found.
 *
 * @package folio
 */

defined( 'ABSPATH' ) || exit;
get_header();
?>
<section class="not-found">
	<div class="hero__bg" aria-hidden="true"><span class="blob blob--1"></span><span class="blob blob--2"></span></div>
	<div class="container not-found__inner">
		<p class="not-found__code reveal">404</p>
		<h1 class="page-title reveal"><?php echo folio_highlight( __( 'This page took a *wrong turn*.', 'folio' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside. ?></h1>
		<p class="page-intro reveal"><?php esc_html_e( 'The link may be old or mistyped. Try the home page, my work, or search.', 'folio' ); ?></p>
		<div class="btn-row btn-row--center reveal">
			<?php echo folio_btn( __( 'Go home', 'folio' ), home_url( '/' ), 'primary' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside. ?>
			<?php echo folio_btn( __( 'See my work', 'folio' ), (string) get_post_type_archive_link( 'project' ), 'ghost', '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside. ?>
		</div>
		<div class="search-again reveal"><?php get_search_form(); ?></div>
	</div>
</section>
<?php
get_footer();
