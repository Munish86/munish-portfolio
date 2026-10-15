<?php
/**
 * Search engines: Person schema on the home page, and a meta description / Open Graph fallback when no SEO plugin
 * (Yoast, Rank Math, SEOPress, AIOSEO) is active.
 *
 * @package folio-core
 */

defined( 'ABSPATH' ) || exit;

/** Whether a known SEO plugin prints its own tags. */
function folio_seo_plugin_active() {
	return defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'SEOPRESS_VERSION' ) || defined( 'AIOSEO_VERSION' );
}

/** Owner name: setting or site title. */
function folio_owner_name() {
	$name = trim( (string) folio_opt( 'name' ) );
	return '' !== $name ? $name : get_bloginfo( 'name' );
}

add_action(
	'wp_head',
	function () {
		if ( ! is_front_page() ) {
			return;
		}
		$person = array(
			'@context'    => 'https://schema.org',
			'@type'       => 'Person',
			'name'        => folio_owner_name(),
			'jobTitle'    => (string) folio_opt( 'role' ),
			'description' => (string) folio_opt( 'bio' ),
			'url'         => home_url( '/' ),
		);
		$same = wp_list_pluck( folio_socials(), 2 );
		if ( $same ) {
			$person['sameAs'] = array_values( $same );
		}
		$photo = (int) folio_opt( 'portrait' );
		if ( $photo ) {
			$person['image'] = wp_get_attachment_image_url( $photo, 'large' );
		}
		echo '<script type="application/ld+json">' . wp_json_encode( array_filter( $person ), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "</script>\n";
	}
);

add_action(
	'wp_head',
	function () {
		if ( folio_seo_plugin_active() ) {
			return;
		}
		$desc  = '';
		$image = '';
		if ( is_front_page() ) {
			$desc  = (string) folio_opt( 'bio' );
			$photo = (int) folio_opt( 'portrait' );
			$image = $photo ? (string) wp_get_attachment_image_url( $photo, 'large' ) : '';
		} elseif ( is_singular() ) {
			$post = get_queried_object();
			$desc = has_excerpt( $post ) ? get_the_excerpt( $post ) : wp_trim_words( wp_strip_all_tags( strip_shortcodes( $post->post_content ) ), 30, '...' );
			if ( has_post_thumbnail( $post ) ) {
				$image = (string) get_the_post_thumbnail_url( $post, 'large' );
			}
		} elseif ( is_post_type_archive() ) {
			$desc = (string) get_the_archive_description();
		}
		$desc = trim( wp_strip_all_tags( $desc ) );
		if ( $desc ) {
			echo '<meta name="description" content="' . esc_attr( wp_html_excerpt( $desc, 160, '...' ) ) . '">' . "\n";
			echo '<meta property="og:description" content="' . esc_attr( wp_html_excerpt( $desc, 200, '...' ) ) . '">' . "\n";
		}
		echo '<meta property="og:title" content="' . esc_attr( wp_get_document_title() ) . '">' . "\n";
		echo '<meta property="og:type" content="' . ( is_singular( 'post' ) ? 'article' : 'website' ) . '">' . "\n";
		echo '<meta property="og:site_name" content="' . esc_attr( get_bloginfo( 'name' ) ) . '">' . "\n";
		if ( $image ) {
			echo '<meta property="og:image" content="' . esc_url( $image ) . '">' . "\n";
			echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
		}
	},
	5
);
