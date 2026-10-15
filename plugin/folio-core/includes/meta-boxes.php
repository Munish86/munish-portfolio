<?php
/**
 * The details box under the editor of each portfolio post type. Values are saved as post meta "_folio_<key>".
 *
 * @package folio-core
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'add_meta_boxes',
	function ( $post_type ) {
		$all = folio_post_fields();
		if ( ! isset( $all[ $post_type ] ) ) {
			return;
		}
		add_meta_box( 'folio-details', $all[ $post_type ]['title'], 'folio_render_meta_box', $post_type, 'normal', 'high' );
	}
);

/**
 * Prints the box.
 *
 * @param WP_Post $post Post being edited.
 */
function folio_render_meta_box( $post ) {
	$def = folio_post_fields()[ $post->post_type ];
	wp_nonce_field( 'folio_meta_' . $post->post_type, 'folio_meta_nonce' );
	echo '<div class="folio-box">';
	if ( ! empty( $def['intro'] ) ) {
		echo '<p class="folio-box__intro">' . esc_html( $def['intro'] ) . '</p>';
	}
	echo '<div class="folio-grid">';
	foreach ( $def['fields'] as $f ) {
		$value = get_post_meta( $post->ID, '_folio_' . $f['key'], true );
		if ( '' === $value && isset( $f['default'] ) && 'auto-draft' === $post->post_status ) {
			$value = $f['default'];
		}
		folio_render_field( $f, 'folio_meta[' . $f['key'] . ']', $value );
	}
	echo '</div></div>';
}

add_action(
	'save_post',
	function ( $post_id, $post ) {
		$all = folio_post_fields();
		if ( ! isset( $all[ $post->post_type ], $_POST['folio_meta_nonce'] ) ) {
			return;
		}
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['folio_meta_nonce'] ) ), 'folio_meta_' . $post->post_type ) ) {
			return;
		}
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_revision( $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- every value is cleaned by folio_clean_field().
		$posted = isset( $_POST['folio_meta'] ) && is_array( $_POST['folio_meta'] ) ? wp_unslash( $_POST['folio_meta'] ) : array();
		foreach ( $all[ $post->post_type ]['fields'] as $f ) {
			$raw   = isset( $posted[ $f['key'] ] ) ? $posted[ $f['key'] ] : ( 'repeater' === $f['type'] ? array() : '' );
			$value = folio_clean_field( $f, $raw );
			if ( '' === $value || array() === $value || ( 'checkbox' === $f['type'] && ! $value ) ) {
				delete_post_meta( $post_id, '_folio_' . $f['key'] );
			} else {
				update_post_meta( $post_id, '_folio_' . $f['key'], $value );
			}
		}
	},
	10,
	2
);

add_action(
	'admin_enqueue_scripts',
	function ( $hook ) {
		$screen = get_current_screen();
		$ours   = $screen && ( isset( folio_post_fields()[ $screen->post_type ] ) && in_array( $hook, array( 'post.php', 'post-new.php' ), true ) );
		$ours   = $ours || ( $screen && 'toplevel_page_folio-settings' === $screen->id );
		if ( ! $ours ) {
			return;
		}
		wp_enqueue_media();
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style( 'folio-admin', FOLIO_CORE_URL . 'assets/admin.css', array(), FOLIO_CORE_VERSION );
		wp_enqueue_script( 'folio-admin', FOLIO_CORE_URL . 'assets/admin.js', array( 'jquery', 'jquery-ui-sortable', 'wp-color-picker' ), FOLIO_CORE_VERSION, true );
	}
);
