<?php
/**
 * "Next project" / "Next case study" link at the bottom of a single item. Wraps around to the first item.
 * Args: type (post type), label.
 *
 * @package folio
 */

defined( 'ABSPATH' ) || exit;

$type  = isset( $args['type'] ) ? $args['type'] : 'project';
$label = isset( $args['label'] ) ? $args['label'] : __( 'Next', 'folio' );
$ids   = get_posts(
	array(
		'post_type'   => $type,
		'numberposts' => 200,
		'fields'      => 'ids',
		'orderby'     => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
	)
);
if ( count( $ids ) < 2 ) {
	return;
}
$pos  = array_search( get_the_ID(), $ids, true );
$next = $ids[ ( false === $pos ? 0 : $pos + 1 ) % count( $ids ) ];
?>
<nav class="next-item" aria-label="<?php echo esc_attr( $label ); ?>">
	<div class="container">
		<a class="next-item__link reveal" href="<?php echo esc_url( get_permalink( $next ) ); ?>">
			<span class="next-item__label"><?php echo esc_html( $label ); ?></span>
			<span class="next-item__title"><?php echo esc_html( get_the_title( $next ) ); ?> <?php echo folio_icon( 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?></span>
			<?php if ( has_post_thumbnail( $next ) ) : ?>
				<span class="next-item__thumb"<?php echo folio_card_style( $next ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside. ?>><?php echo get_the_post_thumbnail( $next, 'folio-card', array( 'loading' => 'lazy', 'alt' => '' ) ); ?></span>
			<?php endif; ?>
		</a>
	</div>
</nav>
