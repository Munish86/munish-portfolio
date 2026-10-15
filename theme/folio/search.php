<?php
/**
 * Search results: projects, case studies, services, posts and pages in one grid of simple cards.
 *
 * @package folio
 */

defined( 'ABSPATH' ) || exit;
get_header();
/* translators: %s: search words */
folio_page_hero( __( 'Search', 'folio' ), esc_html( sprintf( __( 'Results for "%s"', 'folio' ), get_search_query() ) ), '', true );
$labels = array(
	'project'    => __( 'Project', 'folio' ),
	'case_study' => __( 'Case study', 'folio' ),
	'service'    => __( 'Service', 'folio' ),
	'post'       => __( 'Article', 'folio' ),
	'page'       => __( 'Page', 'folio' ),
);
?>
<section class="section section--flush-top">
	<div class="container">
		<div class="search-again reveal"><?php get_search_form(); ?></div>
		<?php if ( have_posts() ) : ?>
			<div class="posts-grid">
				<?php
				while ( have_posts() ) :
					the_post();
					$type = get_post_type();
					?>
					<article class="post-card reveal">
						<?php if ( has_post_thumbnail() ) : ?>
							<a class="post-card__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true"><?php the_post_thumbnail( 'large', array( 'loading' => 'lazy', 'alt' => '' ) ); ?></a>
						<?php endif; ?>
						<p class="post-card__meta"><?php echo esc_html( isset( $labels[ $type ] ) ? $labels[ $type ] : $type ); ?></p>
						<h2 class="post-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
						<p class="post-card__text"><?php echo esc_html( get_the_excerpt() ); ?></p>
					</article>
				<?php endwhile; ?>
			</div>
			<?php the_posts_pagination( array( 'class' => 'pagination', 'prev_text' => '&larr;', 'next_text' => '&rarr;' ) ); ?>
		<?php else : ?>
			<p class="empty-note"><?php esc_html_e( 'Nothing found. Try other words.', 'folio' ); ?></p>
		<?php endif; ?>
	</div>
</section>
<?php
get_footer();
