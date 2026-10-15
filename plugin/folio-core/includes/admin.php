<?php
/**
 * Admin comforts: image / featured / order columns in the lists, a "Portfolio" dashboard box with quick links,
 * and one button that deletes the sample content.
 *
 * @package folio-core
 */

defined( 'ABSPATH' ) || exit;

foreach ( array( 'project', 'case_study', 'service', 'testimonial' ) as $folio_type ) {
	add_filter(
		"manage_{$folio_type}_posts_columns",
		function ( $cols ) use ( $folio_type ) {
			$new = array();
			foreach ( $cols as $key => $label ) {
				if ( 'title' === $key ) {
					$new['folio_thumb'] = '<span class="screen-reader-text">' . esc_html__( 'Image', 'folio-core' ) . '</span>';
				}
				$new[ $key ] = $label;
				if ( 'title' === $key ) {
					if ( in_array( $folio_type, array( 'project', 'case_study' ), true ) ) {
						$new['folio_featured'] = __( 'Home page', 'folio-core' );
					}
					$new['folio_order'] = __( 'Order', 'folio-core' );
				}
			}
			return $new;
		}
	);
	add_action(
		"manage_{$folio_type}_posts_custom_column",
		function ( $col, $post_id ) {
			if ( 'folio_thumb' === $col ) {
				echo get_the_post_thumbnail( $post_id, array( 60, 60 ), array( 'style' => 'width:60px;height:45px;object-fit:cover;border-radius:4px' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core output.
			} elseif ( 'folio_featured' === $col ) {
				echo folio_meta( 'featured', $post_id ) ? '<span class="dashicons dashicons-star-filled" style="color:#dba617" title="' . esc_attr__( 'Shown on the home page', 'folio-core' ) . '"></span>' : '<span class="dashicons dashicons-star-empty" style="color:#c3c4c7"></span>';
			} elseif ( 'folio_order' === $col ) {
				echo (int) get_post_field( 'menu_order', $post_id );
			}
		},
		10,
		2
	);
}

// Lists in the admin follow the same order as the site.
add_action(
	'pre_get_posts',
	function ( $q ) {
		if ( ! is_admin() || ! $q->is_main_query() || $q->get( 'orderby' ) ) {
			return;
		}
		if ( in_array( $q->get( 'post_type' ), array( 'project', 'case_study', 'service', 'testimonial', 'experience' ), true ) ) {
			$q->set( 'orderby', array( 'menu_order' => 'ASC', 'date' => 'DESC' ) );
		}
	}
);

add_action(
	'wp_dashboard_setup',
	function () {
		wp_add_dashboard_widget( 'folio_dashboard', __( 'Your portfolio', 'folio-core' ), 'folio_render_dashboard_widget' );
		// Put it first.
		global $wp_meta_boxes;
		$normal = $wp_meta_boxes['dashboard']['normal']['core'];
		$ours   = array( 'folio_dashboard' => $normal['folio_dashboard'] );
		unset( $normal['folio_dashboard'] );
		$wp_meta_boxes['dashboard']['normal']['core'] = array_merge( $ours, $normal ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
	}
);

/** Number of sample items still on the site. */
function folio_sample_count() {
	$q = new WP_Query(
		array(
			'post_type'      => array( 'project', 'case_study', 'service', 'testimonial', 'experience', 'post' ),
			'post_status'    => 'any',
			'meta_key'       => '_folio_sample', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'fields'         => 'ids',
			'posts_per_page' => -1,
		)
	);
	return count( $q->posts );
}

/** The dashboard box. */
function folio_render_dashboard_widget() {
	$items = array(
		array( 'project', __( 'Projects', 'folio-core' ) ),
		array( 'case_study', __( 'Case studies', 'folio-core' ) ),
		array( 'service', __( 'Services', 'folio-core' ) ),
		array( 'testimonial', __( 'Testimonials', 'folio-core' ) ),
		array( 'experience', __( 'Experience', 'folio-core' ) ),
		array( 'post', __( 'Blog posts', 'folio-core' ) ),
	);
	echo '<ul style="display:grid;grid-template-columns:1fr 1fr;gap:6px 16px;margin:0 0 12px">';
	foreach ( $items as $it ) {
		$n = wp_count_posts( $it[0] );
		echo '<li><a href="' . esc_url( admin_url( 'edit.php?post_type=' . $it[0] ) ) . '"><strong>' . (int) $n->publish . '</strong> ' . esc_html( $it[1] ) . '</a> &middot; <a href="' . esc_url( admin_url( 'post-new.php?post_type=' . $it[0] ) ) . '">' . esc_html__( 'Add', 'folio-core' ) . '</a></li>';
	}
	echo '</ul>';
	echo '<p><a class="button button-primary" href="' . esc_url( admin_url( 'admin.php?page=folio-settings' ) ) . '">' . esc_html__( 'Portfolio Settings', 'folio-core' ) . '</a> <a class="button" href="' . esc_url( admin_url( 'admin.php?page=folio-messages' ) ) . '">' . esc_html__( 'Messages', 'folio-core' ) . '</a> <a class="button" href="' . esc_url( admin_url( 'nav-menus.php' ) ) . '">' . esc_html__( 'Menus', 'folio-core' ) . '</a></p>';

	$samples = folio_sample_count();
	if ( $samples ) {
		$url = wp_nonce_url( admin_url( 'admin-post.php?action=folio_delete_samples' ), 'folio_delete_samples' );
		/* translators: %d: number of sample items */
		echo '<p style="margin-top:14px;padding-top:12px;border-top:1px solid #dcdcde">' . esc_html( sprintf( _n( 'The site still has %d sample item (marked "Sample" in the lists). Replace it with your own work, or delete all samples at once:', 'The site still has %d sample items (marked "Sample" in the lists). Replace them with your own work, or delete all samples at once:', $samples, 'folio-core' ), $samples ) ) . '</p>';
		echo '<p><a class="button" style="color:#b32d2e;border-color:#b32d2e" href="' . esc_url( $url ) . '" onclick="return confirm(\'Delete all sample projects, case studies, services, testimonials, experience items and blog posts? Your own items are kept.\')">' . esc_html__( 'Delete sample content', 'folio-core' ) . '</a></p>';
	}
}

// "Sample" label next to sample items in the lists.
add_filter(
	'display_post_states',
	function ( $states, $post ) {
		if ( get_post_meta( $post->ID, '_folio_sample', true ) ) {
			$states['folio_sample'] = __( 'Sample', 'folio-core' );
		}
		return $states;
	},
	10,
	2
);

add_action(
	'admin_post_folio_delete_samples',
	function () {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Not allowed.', 'folio-core' ), 403 );
		}
		check_admin_referer( 'folio_delete_samples' );
		$q = new WP_Query(
			array(
				'post_type'      => array( 'project', 'case_study', 'service', 'testimonial', 'experience', 'post' ),
				'post_status'    => 'any',
				'meta_key'       => '_folio_sample', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'fields'         => 'ids',
				'posts_per_page' => -1,
			)
		);
		foreach ( $q->posts as $id ) {
			$thumb = (int) get_post_thumbnail_id( $id );
			$ids   = array_merge( array( $thumb ), array_map( 'absint', explode( ',', (string) folio_meta( 'gallery', $id ) ) ) );
			wp_delete_post( $id, true );
			// Sample images are deleted too, when they were uploaded only for the samples.
			foreach ( array_filter( array_unique( $ids ) ) as $aid ) {
				if ( get_post_meta( $aid, '_folio_sample', true ) ) {
					wp_delete_attachment( $aid, true );
				}
			}
		}
		wp_safe_redirect( admin_url( 'index.php' ) );
		exit;
	}
);
