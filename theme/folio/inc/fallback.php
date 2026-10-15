<?php
/**
 * Keeps the theme working (with defaults) when the Folio Core plugin is not active, and tells the admin to turn it on.
 *
 * @package folio
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'after_setup_theme',
	function () {
		if ( function_exists( 'folio_opt' ) ) {
			return;
		}
		add_action(
			'admin_notices',
			function () {
				echo '<div class="notice notice-warning"><p>' . esc_html__( 'The Folio theme needs the "Folio Core" plugin for projects, case studies, services and Portfolio Settings. Please activate it under Plugins.', 'folio' ) . '</p></div>';
			}
		);
	}
);

if ( ! function_exists( 'folio_opt' ) ) {
	/**
	 * Fallback: always the given default.
	 *
	 * @param string $key     Key.
	 * @param mixed  $default Default.
	 */
	function folio_opt( $key, $default = '' ) {
		return $default;
	}
	/**
	 * Fallback post field.
	 *
	 * @param string   $key  Key.
	 * @param int|null $post Post.
	 */
	function folio_meta( $key, $post = null ) {
		return get_post_meta( (int) ( $post ? $post : get_the_ID() ), '_folio_' . $key, true );
	}
	/**
	 * Fallback: plain escaped text.
	 *
	 * @param string $text Text.
	 */
	function folio_highlight( $text ) {
		return esc_html( str_replace( '*', '', (string) $text ) );
	}
	/**
	 * Fallback lines.
	 *
	 * @param string $text Text.
	 */
	function folio_lines( $text ) {
		return array_values( array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', (string) $text ) ), 'strlen' ) );
	}
	/**
	 * Fallback: no icons.
	 *
	 * @param string $name  Name.
	 * @param string $class Class.
	 */
	function folio_icon( $name, $class = '' ) {
		return '';
	}
	/** Fallback: no social links. */
	function folio_socials() {
		return array();
	}
	/** Fallback owner name. */
	function folio_owner_name() {
		return get_bloginfo( 'name' );
	}
	/** Fallback: no form. */
	function folio_contact_form() {
		return '';
	}
}
