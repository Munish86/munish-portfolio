<?php
/**
 * Default page (privacy, terms, any page you add). Wide and full-width blocks are allowed in the editor.
 *
 * @package folio
 */

defined( 'ABSPATH' ) || exit;
get_header();

while ( have_posts() ) :
	the_post();
	folio_page_hero( '', esc_html( get_the_title() ), has_excerpt() ? get_the_excerpt() : '', true );
	?>
	<section class="section section--flush-top">
		<div class="container">
			<div class="prose entry-content"><?php the_content(); ?></div>
		</div>
	</section>
	<?php
endwhile;

get_footer();
