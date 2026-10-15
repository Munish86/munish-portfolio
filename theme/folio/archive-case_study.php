<?php
/**
 * Case studies page (/case-studies/).
 *
 * @package folio
 */

defined( 'ABSPATH' ) || exit;
get_header();
folio_page_hero( __( 'Case studies', 'folio' ), folio_opt( 'cases_page_title', __( 'Case studies', 'folio' ) ), folio_opt( 'cases_page_text' ) );
?>
<section class="section section--flush-top">
	<div class="container">
		<?php if ( have_posts() ) : ?>
			<div class="case-list">
				<?php
				$i = 0;
				while ( have_posts() ) :
					the_post();
					folio_case_card( get_the_ID(), (bool) ( $i++ % 2 ) );
				endwhile;
				?>
			</div>
			<?php the_posts_pagination( array( 'class' => 'pagination', 'prev_text' => '&larr;', 'next_text' => '&rarr;' ) ); ?>
		<?php else : ?>
			<p class="empty-note"><?php esc_html_e( 'Case studies will appear here soon.', 'folio' ); ?></p>
		<?php endif; ?>
	</div>
</section>
<?php
folio_cta_band();
get_footer();
