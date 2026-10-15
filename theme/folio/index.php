<?php
/**
 * Blog (posts page) and the fallback for any list without its own template.
 *
 * @package folio
 */

defined( 'ABSPATH' ) || exit;
get_header();

if ( is_home() ) {
	folio_page_hero( __( 'Blog', 'folio' ), folio_opt( 'blog_page_title', __( 'Blog', 'folio' ) ), folio_opt( 'blog_page_text' ) );
} elseif ( is_search() ) {
	/* translators: %s: search words */
	folio_page_hero( __( 'Search', 'folio' ), esc_html( sprintf( __( 'Results for "%s"', 'folio' ), get_search_query() ) ), '', true );
} else {
	folio_page_hero( is_category() || is_tag() ? __( 'Topic', 'folio' ) : __( 'Archive', 'folio' ), esc_html( wp_strip_all_tags( get_the_archive_title() ) ), wp_strip_all_tags( get_the_archive_description() ), true );
}

$cats = is_home() ? get_categories( array( 'hide_empty' => true ) ) : array();
?>
<section class="section section--flush-top">
	<div class="container">
		<?php if ( count( $cats ) > 1 ) : ?>
			<nav class="filters reveal" aria-label="<?php esc_attr_e( 'Topics', 'folio' ); ?>">
				<a class="filters__btn is-active" href="<?php echo esc_url( get_permalink( (int) get_option( 'page_for_posts' ) ) ); ?>"><?php esc_html_e( 'All', 'folio' ); ?></a>
				<?php foreach ( $cats as $c ) : ?>
					<a class="filters__btn" href="<?php echo esc_url( get_category_link( $c ) ); ?>"><?php echo esc_html( $c->name ); ?></a>
				<?php endforeach; ?>
			</nav>
		<?php endif; ?>
		<?php if ( is_search() ) : ?>
			<div class="search-again reveal"><?php get_search_form(); ?></div>
		<?php endif; ?>
		<?php if ( have_posts() ) : ?>
			<div class="posts-grid">
				<?php
				while ( have_posts() ) :
					the_post();
					folio_post_card();
				endwhile;
				?>
			</div>
			<?php the_posts_pagination( array( 'class' => 'pagination', 'prev_text' => '&larr;', 'next_text' => '&rarr;' ) ); ?>
		<?php else : ?>
			<p class="empty-note"><?php is_search() ? esc_html_e( 'Nothing found. Try other words.', 'folio' ) : esc_html_e( 'Nothing here yet.', 'folio' ); ?></p>
		<?php endif; ?>
	</div>
</section>
<?php
get_footer();
