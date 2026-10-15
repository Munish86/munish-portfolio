<?php
/**
 * Search form.
 *
 * @package folio
 */

defined( 'ABSPATH' ) || exit;
$sid = wp_unique_id( 'search-' );
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="<?php echo esc_attr( $sid ); ?>"><?php esc_html_e( 'Search for', 'folio' ); ?></label>
	<?php echo folio_icon( 'search' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
	<input type="search" id="<?php echo esc_attr( $sid ); ?>" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'Search projects, case studies, articles...', 'folio' ); ?>">
	<button type="submit" class="btn btn--primary btn--sm"><span><?php esc_html_e( 'Search', 'folio' ); ?></span></button>
</form>
