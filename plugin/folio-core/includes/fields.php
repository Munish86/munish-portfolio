<?php
/**
 * Form fields shared by the meta boxes and the settings page: one renderer and one cleaner per field type.
 *
 * Field definition keys: key, label, type, help, default, options (select), sub (repeater), post_type (post),
 * placeholder, wide (full row), add_label (repeater button).
 * Types: text, textarea, url, email, number, select, checkbox, color, image, gallery, file, repeater, post.
 *
 * @package folio-core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Prints one field.
 *
 * @param array  $f     Field definition.
 * @param string $name  Input name.
 * @param mixed  $value Current value.
 */
function folio_render_field( $f, $name, $value ) {
	$id   = 'folio-' . sanitize_html_class( str_replace( array( '[', ']' ), '-', $name ) );
	$type = $f['type'];
	$ph   = isset( $f['placeholder'] ) ? ' placeholder="' . esc_attr( $f['placeholder'] ) . '"' : '';

	echo '<div class="folio-field folio-field--' . esc_attr( $type ) . ( ! empty( $f['wide'] ) || in_array( $type, array( 'textarea', 'gallery', 'repeater' ), true ) ? ' is-wide' : '' ) . '">';
	if ( 'checkbox' !== $type ) {
		echo '<label class="folio-label" for="' . esc_attr( $id ) . '">' . esc_html( $f['label'] ) . '</label>';
	}

	switch ( $type ) {
		case 'textarea':
			$rows = isset( $f['rows'] ) ? (int) $f['rows'] : 4;
			echo '<textarea id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" rows="' . (int) $rows . '"' . $ph . '>' . esc_textarea( (string) $value ) . '</textarea>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $ph escaped above.
			break;

		case 'select':
			echo '<select id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '">';
			foreach ( $f['options'] as $k => $label ) {
				echo '<option value="' . esc_attr( $k ) . '"' . selected( (string) $value, (string) $k, false ) . '>' . esc_html( $label ) . '</option>';
			}
			echo '</select>';
			break;

		case 'post':
			$posts = get_posts(
				array(
					'post_type'   => $f['post_type'],
					'numberposts' => 200,
					'post_status' => array( 'publish', 'draft', 'pending', 'private' ),
					'orderby'     => 'title',
					'order'       => 'ASC',
				)
			);
			echo '<select id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '"><option value="">' . esc_html__( 'None', 'folio-core' ) . '</option>';
			foreach ( $posts as $p ) {
				echo '<option value="' . (int) $p->ID . '"' . selected( (int) $value, $p->ID, false ) . '>' . esc_html( get_the_title( $p ) ) . '</option>';
			}
			echo '</select>';
			break;

		case 'checkbox':
			echo '<label class="folio-check"><input type="hidden" name="' . esc_attr( $name ) . '" value="0"><input type="checkbox" id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" value="1"' . checked( (bool) $value, true, false ) . '> ' . esc_html( $f['label'] ) . '</label>';
			break;

		case 'color':
			echo '<input type="text" class="folio-color" id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" value="' . esc_attr( (string) $value ) . '" data-default-color="' . esc_attr( isset( $f['default'] ) ? $f['default'] : '' ) . '">';
			break;

		case 'image':
			$src = $value ? wp_get_attachment_image_url( (int) $value, 'medium' ) : '';
			echo '<div class="folio-media" data-multiple="0">';
			echo '<input type="hidden" id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" value="' . esc_attr( (string) $value ) . '">';
			echo '<div class="folio-media__preview">' . ( $src ? '<img src="' . esc_url( $src ) . '" alt="">' : '' ) . '</div>';
			echo '<button type="button" class="button folio-media__pick">' . esc_html__( 'Choose image', 'folio-core' ) . '</button> ';
			echo '<button type="button" class="button-link folio-media__clear"' . ( $value ? '' : ' hidden' ) . '>' . esc_html__( 'Remove', 'folio-core' ) . '</button>';
			echo '</div>';
			break;

		case 'gallery':
			$ids = array_filter( array_map( 'absint', explode( ',', (string) $value ) ) );
			echo '<div class="folio-media" data-multiple="1">';
			echo '<input type="hidden" id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" value="' . esc_attr( implode( ',', $ids ) ) . '">';
			echo '<div class="folio-media__preview folio-media__preview--grid">';
			foreach ( $ids as $aid ) {
				$src = wp_get_attachment_image_url( $aid, 'thumbnail' );
				if ( $src ) {
					echo '<img src="' . esc_url( $src ) . '" alt="" data-id="' . (int) $aid . '">';
				}
			}
			echo '</div>';
			echo '<button type="button" class="button folio-media__pick">' . esc_html__( 'Add or arrange images', 'folio-core' ) . '</button> ';
			echo '<button type="button" class="button-link folio-media__clear"' . ( $ids ? '' : ' hidden' ) . '>' . esc_html__( 'Remove all', 'folio-core' ) . '</button>';
			echo '</div>';
			break;

		case 'file':
			echo '<div class="folio-file">';
			echo '<input type="url" id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" value="' . esc_attr( (string) $value ) . '" class="regular-text">';
			echo ' <button type="button" class="button folio-file__pick">' . esc_html__( 'Upload / choose file', 'folio-core' ) . '</button>';
			echo '</div>';
			break;

		case 'repeater':
			$rows = is_array( $value ) ? $value : array();
			echo '<div class="folio-repeater" data-name="' . esc_attr( $name ) . '">';
			echo '<div class="folio-repeater__rows">';
			foreach ( array_values( $rows ) as $i => $row ) {
				folio_render_repeater_row( $f, $name, $i, $row );
			}
			echo '</div>';
			echo '<template class="folio-repeater__template">';
			folio_render_repeater_row( $f, $name, '__i__', array() );
			echo '</template>';
			echo '<button type="button" class="button folio-repeater__add">' . esc_html( isset( $f['add_label'] ) ? $f['add_label'] : __( 'Add row', 'folio-core' ) ) . '</button>';
			echo '</div>';
			break;

		case 'number':
			echo '<input type="number" id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" value="' . esc_attr( (string) $value ) . '"' . $ph . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $ph escaped above.
			break;

		default: // text, url, email.
			$input = in_array( $type, array( 'url', 'email' ), true ) ? ( 'url' === $type ? 'text' : 'email' ) : 'text';
			echo '<input type="' . esc_attr( $input ) . '" id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" value="' . esc_attr( (string) $value ) . '"' . $ph . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $ph escaped above.
	}

	if ( ! empty( $f['help'] ) ) {
		echo '<p class="description">' . wp_kses( $f['help'], array( 'code' => array(), 'strong' => array(), 'a' => array( 'href' => array() ) ) ) . '</p>';
	}
	echo '</div>';
}

/**
 * One row of a repeater.
 *
 * @param array      $f    Repeater definition.
 * @param string     $name Repeater input name.
 * @param int|string $i    Row index or the "__i__" placeholder.
 * @param array      $row  Row values.
 */
function folio_render_repeater_row( $f, $name, $i, $row ) {
	echo '<div class="folio-repeater__row">';
	echo '<span class="folio-repeater__handle" title="' . esc_attr__( 'Drag to reorder', 'folio-core' ) . '">&#8942;&#8942;</span>';
	foreach ( $f['sub'] as $sub ) {
		$v   = isset( $row[ $sub['key'] ] ) ? $row[ $sub['key'] ] : '';
		$n   = $name . '[' . $i . '][' . $sub['key'] . ']';
		$ph  = isset( $sub['placeholder'] ) ? $sub['placeholder'] : $sub['label'];
		$cls = 'folio-repeater__cell' . ( 'textarea' === $sub['type'] ? ' is-grow' : '' );
		echo '<label class="' . esc_attr( $cls ) . '"><span class="screen-reader-text">' . esc_html( $sub['label'] ) . '</span>';
		if ( 'textarea' === $sub['type'] ) {
			echo '<textarea name="' . esc_attr( $n ) . '" rows="2" placeholder="' . esc_attr( $ph ) . '">' . esc_textarea( (string) $v ) . '</textarea>';
		} elseif ( 'select' === $sub['type'] ) {
			echo '<select name="' . esc_attr( $n ) . '">';
			foreach ( $sub['options'] as $k => $label ) {
				echo '<option value="' . esc_attr( $k ) . '"' . selected( (string) $v, (string) $k, false ) . '>' . esc_html( $label ) . '</option>';
			}
			echo '</select>';
		} else {
			echo '<input type="text" name="' . esc_attr( $n ) . '" value="' . esc_attr( (string) $v ) . '" placeholder="' . esc_attr( $ph ) . '">';
		}
		echo '</label>';
	}
	echo '<button type="button" class="button-link folio-repeater__remove" aria-label="' . esc_attr__( 'Remove row', 'folio-core' ) . '">&times;</button>';
	echo '</div>';
}

/**
 * Cleans a posted value for a field.
 *
 * @param array $f   Field definition.
 * @param mixed $raw Raw posted value (already unslashed).
 * @return mixed
 */
function folio_clean_field( $f, $raw ) {
	switch ( $f['type'] ) {
		case 'textarea':
			return mb_substr( sanitize_textarea_field( (string) $raw ), 0, 20000 );
		case 'url':
		case 'file':
			return folio_clean_url( (string) $raw );
		case 'email':
			$email = sanitize_email( (string) $raw );
			return is_email( $email ) ? $email : '';
		case 'number':
			return '' === trim( (string) $raw ) ? '' : (string) (float) $raw;
		case 'select':
			return array_key_exists( (string) $raw, $f['options'] ) ? (string) $raw : (string) ( isset( $f['default'] ) ? $f['default'] : '' );
		case 'checkbox':
			return ! empty( $raw ) ? 1 : 0;
		case 'color':
			return folio_clean_color( (string) $raw );
		case 'image':
		case 'post':
			$n = absint( $raw );
			return $n ? $n : '';
		case 'gallery':
			return implode( ',', array_filter( array_map( 'absint', explode( ',', (string) $raw ) ) ) );
		case 'repeater':
			$out = array();
			foreach ( is_array( $raw ) ? $raw : array() as $row ) {
				if ( ! is_array( $row ) ) {
					continue;
				}
				$clean = array();
				foreach ( $f['sub'] as $sub ) {
					$v                     = isset( $row[ $sub['key'] ] ) ? (string) $row[ $sub['key'] ] : '';
					$clean[ $sub['key'] ] = 'textarea' === $sub['type'] ? sanitize_textarea_field( $v ) : sanitize_text_field( $v );
					if ( 'select' === $sub['type'] && ! array_key_exists( $clean[ $sub['key'] ], $sub['options'] ) ) {
						$clean[ $sub['key'] ] = (string) array_key_first( $sub['options'] );
					}
				}
				// Rows where every text cell is empty are dropped (select cells always have a value).
				$texts = array();
				foreach ( $f['sub'] as $sub ) {
					if ( 'select' !== $sub['type'] ) {
						$texts[] = $clean[ $sub['key'] ];
					}
				}
				if ( implode( '', $texts ) !== '' ) {
					$out[] = $clean;
				}
				if ( count( $out ) >= 50 ) {
					break;
				}
			}
			return $out;
		default:
			return mb_substr( sanitize_text_field( (string) $raw ), 0, 500 );
	}
}
