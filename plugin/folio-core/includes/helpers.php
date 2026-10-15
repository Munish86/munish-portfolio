<?php
/**
 * Small helpers used by the plugin and the theme: settings, post fields, icons, social links.
 *
 * @package folio-core
 */

defined( 'ABSPATH' ) || exit;

/**
 * One Portfolio Settings value, or the field's default when it was never saved.
 *
 * @param string $key     Setting key (see folio_settings_fields()).
 * @param mixed  $default Used when the setting has no default either.
 * @return mixed
 */
function folio_opt( $key, $default = '' ) {
	static $saved = null;
	if ( null === $saved || doing_action( 'update_option_folio_settings' ) ) {
		$saved = get_option( 'folio_settings', array() );
		$saved = is_array( $saved ) ? $saved : array();
	}
	if ( array_key_exists( $key, $saved ) ) {
		return $saved[ $key ];
	}
	$field = folio_settings_field( $key );
	return $field && array_key_exists( 'default', $field ) ? $field['default'] : $default;
}

/**
 * A field of a portfolio item.
 *
 * @param string   $key  Field key without the leading underscore (e.g. "client").
 * @param int|null $post Post id; the current post when empty.
 * @return mixed
 */
function folio_meta( $key, $post = null ) {
	$post = $post ? $post : get_the_ID();
	return get_post_meta( (int) $post, '_folio_' . $key, true );
}

/**
 * Text with *stars* around words turned into highlighted words. Everything else is escaped.
 *
 * @param string $text Plain text from a setting.
 * @return string Safe HTML.
 */
function folio_highlight( $text ) {
	$html = esc_html( (string) $text );
	return preg_replace( '/\*([^*]+)\*/', '<span class="hl">$1</span>', $html );
}

/**
 * Lines of a textarea setting, trimmed, without empty lines.
 *
 * @param string $text Textarea value.
 * @return string[]
 */
function folio_lines( $text ) {
	return array_values( array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', (string) $text ) ), 'strlen' ) );
}

/** Icon names offered in the admin (services) with their labels. */
function folio_icon_choices() {
	return array(
		'code'       => __( 'Code', 'folio-core' ),
		'layout'     => __( 'Layout', 'folio-core' ),
		'pen'        => __( 'Pen tool (design)', 'folio-core' ),
		'palette'    => __( 'Palette', 'folio-core' ),
		'figma'      => __( 'Figma', 'folio-core' ),
		'smartphone' => __( 'Mobile', 'folio-core' ),
		'monitor'    => __( 'Monitor', 'folio-core' ),
		'globe'      => __( 'Globe / WordPress', 'folio-core' ),
		'cart'       => __( 'Shop / e-commerce', 'folio-core' ),
		'zap'        => __( 'Speed', 'folio-core' ),
		'search'     => __( 'Search / SEO', 'folio-core' ),
		'chart'      => __( 'Growth chart', 'folio-core' ),
		'layers'     => __( 'Layers / design system', 'folio-core' ),
		'rocket'     => __( 'Rocket / launch', 'folio-core' ),
		'users'      => __( 'Users / research', 'folio-core' ),
		'sparkles'   => __( 'Sparkles', 'folio-core' ),
		'wrench'     => __( 'Wrench / maintenance', 'folio-core' ),
	);
}

/**
 * Inline SVG icon (24x24, stroke). Shapes are from Lucide (ISC licence) and Tabler (MIT).
 *
 * @param string $name  Icon name.
 * @param string $class Extra class.
 * @return string
 */
function folio_icon( $name, $class = '' ) {
	$paths = array(
		'code'         => '<path d="m16 18 6-6-6-6M8 6l-6 6 6 6"/>',
		'layout'       => '<rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/>',
		'pen'          => '<path d="m12 19 7-7 3 3-7 7-3-3z"/><path d="m18 13-1.5-7.5L2 2l3.5 14.5L13 18l5-5z"/><path d="m2 2 7.586 7.586"/><circle cx="11" cy="11" r="2"/>',
		'palette'      => '<circle cx="13.5" cy="6.5" r=".5"/><circle cx="17.5" cy="10.5" r=".5"/><circle cx="8.5" cy="7.5" r=".5"/><circle cx="6.5" cy="12.5" r=".5"/><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10c.93 0 1.65-.75 1.65-1.69 0-.44-.18-.84-.44-1.13-.29-.29-.44-.65-.44-1.13a1.64 1.64 0 0 1 1.67-1.67h2c3.05 0 5.55-2.5 5.55-5.55C21.97 6.01 17.46 2 12 2z"/>',
		'figma'        => '<path d="M5 5.5A3.5 3.5 0 0 1 8.5 2H12v7H8.5A3.5 3.5 0 0 1 5 5.5z"/><path d="M12 2h3.5a3.5 3.5 0 1 1 0 7H12V2z"/><path d="M12 12.5a3.5 3.5 0 1 1 7 0 3.5 3.5 0 1 1-7 0z"/><path d="M5 19.5A3.5 3.5 0 0 1 8.5 16H12v3.5a3.5 3.5 0 1 1-7 0z"/><path d="M5 12.5A3.5 3.5 0 0 1 8.5 9H12v7H8.5A3.5 3.5 0 0 1 5 12.5z"/>',
		'smartphone'   => '<rect x="5" y="2" width="14" height="20" rx="2"/><path d="M12 18h.01"/>',
		'monitor'      => '<rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/>',
		'globe'        => '<circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>',
		'cart'         => '<circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/>',
		'zap'          => '<path d="M13 2 3 14h9l-1 8 10-12h-9l1-8z"/>',
		'search'       => '<circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>',
		'chart'        => '<path d="M3 3v18h18"/><path d="m19 9-5 5-4-4-3 3"/>',
		'layers'       => '<path d="m12 2 10 5-10 5L2 7l10-5z"/><path d="m2 17 10 5 10-5M2 12l10 5 10-5"/>',
		'rocket'       => '<path d="M4.5 16.5c-1.5 1.26-2 5-2 5s3.74-.5 5-2c.71-.84.7-2.13-.09-2.91a2.18 2.18 0 0 0-2.91-.09z"/><path d="m12 15-3-3a22 22 0 0 1 2-3.95A12.88 12.88 0 0 1 22 2c0 2.72-.78 7.5-6 11a22.35 22.35 0 0 1-4 2z"/><path d="M9 12H4s.55-3.03 2-4c1.62-1.08 5 0 5 0M12 15v5s3.03-.55 4-2c1.08-1.62 0-5 0-5"/>',
		'users'        => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>',
		'sparkles'     => '<path d="m12 3-1.9 5.8a2 2 0 0 1-1.3 1.3L3 12l5.8 1.9a2 2 0 0 1 1.3 1.3L12 21l1.9-5.8a2 2 0 0 1 1.3-1.3L21 12l-5.8-1.9a2 2 0 0 1-1.3-1.3Z"/>',
		'wrench'       => '<path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>',
		'arrow-right'  => '<path d="M5 12h14M12 5l7 7-7 7"/>',
		'arrow-left'   => '<path d="M19 12H5M12 19l-7-7 7-7"/>',
		'arrow-up-right' => '<path d="M7 17 17 7M7 7h10v10"/>',
		'external'     => '<path d="M15 3h6v6M10 14 21 3M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>',
		'sun'          => '<circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/>',
		'moon'         => '<path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/>',
		'menu'         => '<path d="M4 7h16M4 12h16M4 17h16"/>',
		'close'        => '<path d="M18 6 6 18M6 6l12 12"/>',
		'mail'         => '<rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-10 5L2 7"/>',
		'phone'        => '<path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z"/>',
		'pin'          => '<path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>',
		'download'     => '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/>',
		'check'        => '<path d="M20 6 9 17l-5-5"/>',
		'star'         => '<path fill="currentColor" d="m12 2 3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>',
		'quote'        => '<path d="M3 21c3 0 7-1 7-8V5c0-1.25-.76-2.02-2-2H4c-1.25 0-2 .75-2 1.97V11c0 1.25.75 2 2 2 1 0 1 0 1 1v1c0 1-1 2-2 2s-1 .01-1 1.03V20c0 1 0 1 1 1z"/><path d="M15 21c3 0 7-1 7-8V5c0-1.25-.76-2.02-2-2h-4c-1.25 0-2 .75-2 1.97V11c0 1.25.75 2 2 2h.75c0 2.25.25 4-2.75 4v3c0 1 0 1 1 1z"/>',
		'calendar'     => '<rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>',
		'briefcase'    => '<rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>',
		'clock'        => '<circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>',
		'user'         => '<circle cx="12" cy="8" r="4"/><path d="M4 21v-1a6 6 0 0 1 6-6h4a6 6 0 0 1 6 6v1"/>',
		'tag'          => '<path d="M12.59 2.59A2 2 0 0 0 11.17 2H4a2 2 0 0 0-2 2v7.17a2 2 0 0 0 .59 1.42l8.7 8.7a2.43 2.43 0 0 0 3.42 0l6.58-6.58a2.43 2.43 0 0 0 0-3.42z"/><circle cx="7.5" cy="7.5" r=".5"/>',
		'github'       => '<path d="M15 22v-4a4.8 4.8 0 0 0-1-3.5c3 0 6-2 6-5.5.08-1.25-.27-2.48-1-3.5.28-1.15.28-2.35 0-3.5 0 0-1 0-3 1.5-2.64-.5-5.36-.5-8 0C6 2 5 2 5 2c-.3 1.15-.3 2.35 0 3.5A5.4 5.4 0 0 0 4 9c0 3.5 3 5.5 6 5.5-.39.49-.68 1.05-.85 1.65-.17.6-.22 1.23-.15 1.85v4"/><path d="M9 18c-4.51 2-5-2-7-2"/>',
		'linkedin'     => '<path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-4 0v7h-4v-7a6 6 0 0 1 6-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/>',
		'dribbble'     => '<circle cx="12" cy="12" r="10"/><path d="M19.13 5.09C15.22 9.14 10 10.44 2.25 10.94M21.75 12.84c-6.62-1.41-12.14 1-16.38 6.32M8.56 2.75c4.37 6 6 9.42 8 17.72"/>',
		'behance'      => '<path d="M3 6h5.5a3 3 0 0 1 0 6H3zM3 12h6a3 3 0 0 1 0 6H3zM14 14.5h7a3.5 3.5 0 1 0-1 2.5M15 7h5"/>',
		'x'            => '<path d="m4 4 11.73 16H20L8.27 4zM4 20l6.77-6.77m2.46-2.46L20 4"/>',
		'instagram'    => '<rect x="2" y="2" width="20" height="20" rx="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37zM17.5 6.5h.01"/>',
		'youtube'      => '<path d="M2.5 17a24.12 24.12 0 0 1 0-10 2 2 0 0 1 1.4-1.4 49.56 49.56 0 0 1 16.2 0A2 2 0 0 1 21.5 7a24.12 24.12 0 0 1 0 10 2 2 0 0 1-1.4 1.4 49.55 49.55 0 0 1-16.2 0A2 2 0 0 1 2.5 17"/><path d="m10 15 5-3-5-3z"/>',
		'codepen'      => '<path d="m12 2 10 6.5v7L12 22 2 15.5v-7L12 2zM12 22v-6.5M22 8.5l-10 7-10-7M2 15.5l10-7 10 7M12 2v6.5"/>',
		'whatsapp'     => '<path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/><path d="M9 10a.5.5 0 0 0 1 0V9a.5.5 0 0 0-1 0v1a5 5 0 0 0 5 5h1a.5.5 0 0 0 0-1h-1a.5.5 0 0 0 0 1"/>',
	);
	if ( ! isset( $paths[ $name ] ) ) {
		$name = 'sparkles';
	}
	return '<svg class="icon icon-' . esc_attr( $name ) . ( $class ? ' ' . esc_attr( $class ) : '' ) . '" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $paths[ $name ] . '</svg>';
}

/** Social networks offered in the settings: key => label. The key is also the icon name. */
function folio_social_networks() {
	return array(
		'linkedin'  => 'LinkedIn',
		'github'    => 'GitHub',
		'dribbble'  => 'Dribbble',
		'behance'   => 'Behance',
		'x'         => 'X (Twitter)',
		'instagram' => 'Instagram',
		'youtube'   => 'YouTube',
		'codepen'   => 'CodePen',
		'whatsapp'  => 'WhatsApp',
	);
}

/** Social links that have a URL: list of [ key, label, url ]. */
function folio_socials() {
	$out = array();
	foreach ( folio_social_networks() as $key => $label ) {
		$url = (string) folio_opt( 'social_' . $key );
		if ( '' !== $url ) {
			$out[] = array( $key, $label, $url );
		}
	}
	return $out;
}

/**
 * Normalises a URL a person typed: "www.site.com" becomes "https://www.site.com". Empty stays empty.
 *
 * @param string $url Raw value.
 * @return string
 */
function folio_clean_url( $url ) {
	$url = trim( (string) $url );
	if ( '' === $url ) {
		return '';
	}
	if ( ! preg_match( '#^(https?:|mailto:|tel:|/|\#)#i', $url ) ) {
		$url = 'https://' . $url;
	}
	return esc_url_raw( $url );
}

/**
 * Hex colour or empty.
 *
 * @param string $value Raw value.
 * @return string
 */
function folio_clean_color( $value ) {
	$value = sanitize_hex_color( trim( (string) $value ) );
	return $value ? $value : '';
}
