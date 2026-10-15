<?php
/**
 * Work page (/work/) and work-type / tool pages: all projects with filter buttons by work type.
 *
 * @package folio
 */

defined( 'ABSPATH' ) || exit;
get_header();

$term = is_tax() ? get_queried_object() : null;
if ( $term ) {
	folio_page_hero( __( 'Work', 'folio' ), single_term_title( '', false ), wp_strip_all_tags( term_description() ) );
} else {
	folio_page_hero( __( 'Work', 'folio' ), folio_opt( 'work_page_title', __( 'Work', 'folio' ) ), folio_opt( 'work_page_text' ) );
}
$types = get_terms( array( 'taxonomy' => 'work_type', 'hide_empty' => true ) );
?>
<section class="section section--flush-top">
	<div class="container">
		<?php if ( ! $term && $types && ! is_wp_error( $types ) && count( $types ) > 1 ) : ?>
			<div class="filters reveal" role="group" aria-label="<?php esc_attr_e( 'Filter projects', 'folio' ); ?>">
				<a class="filters__btn is-active" href="<?php echo esc_url( get_post_type_archive_link( 'project' ) ); ?>" data-filter="*"><?php esc_html_e( 'All', 'folio' ); ?></a>
				<?php foreach ( $types as $t ) : ?>
					<a class="filters__btn" href="<?php echo esc_url( get_term_link( $t ) ); ?>" data-filter="<?php echo esc_attr( $t->slug ); ?>"><?php echo esc_html( $t->name ); ?> <span><?php echo (int) $t->count; ?></span></a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php if ( have_posts() ) : ?>
			<div class="work-grid work-grid--archive" data-filter-grid>
				<?php
				while ( have_posts() ) :
					the_post();
					if ( 'project' === get_post_type() ) {
						folio_project_card( get_the_ID() );
					} else {
						folio_case_card( get_the_ID() );
					}
				endwhile;
				?>
			</div>
			<p class="filters__empty" hidden><?php esc_html_e( 'No projects of this type yet.', 'folio' ); ?></p>
			<?php the_posts_pagination( array( 'class' => 'pagination', 'prev_text' => '&larr;', 'next_text' => '&rarr;' ) ); ?>
		<?php else : ?>
			<p class="empty-note"><?php esc_html_e( 'Projects will appear here soon.', 'folio' ); ?></p>
		<?php endif; ?>
	</div>
</section>
<?php
folio_cta_band();
get_footer();
