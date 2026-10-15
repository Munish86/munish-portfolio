<?php
/**
 * Blog post.
 *
 * @package folio
 */

defined( 'ABSPATH' ) || exit;
get_header();

while ( have_posts() ) :
	the_post();
	$cats = get_the_category();
	?>
	<article <?php post_class( 'single-post' ); ?>>
		<header class="page-hero page-hero--post">
			<div class="container container--narrow">
				<?php if ( get_option( 'page_for_posts' ) ) : ?>
					<a class="back-link reveal" href="<?php echo esc_url( get_permalink( (int) get_option( 'page_for_posts' ) ) ); ?>"><?php echo folio_icon( 'arrow-left' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?><?php esc_html_e( 'All articles', 'folio' ); ?></a>
				<?php endif; ?>
				<p class="eyebrow reveal"><?php echo esc_html( implode( ' · ', array_filter( array( $cats ? $cats[0]->name : '', get_the_date(), folio_reading_time( get_the_ID() ) ) ) ) ); ?></p>
				<h1 class="page-title page-title--post reveal"><?php the_title(); ?></h1>
				<?php if ( has_excerpt() ) : ?>
					<p class="page-intro reveal"><?php echo esc_html( get_the_excerpt() ); ?></p>
				<?php endif; ?>
				<p class="byline reveal"><?php echo get_avatar( get_the_author_meta( 'ID' ), 40, '', '', array( 'class' => 'avatar' ) ); ?><span><?php echo esc_html( folio_owner_name() ); ?></span></p>
			</div>
		</header>
		<?php if ( has_post_thumbnail() ) : ?>
			<div class="container">
				<figure class="cover cover--post reveal"><?php the_post_thumbnail( 'folio-wide', array( 'fetchpriority' => 'high', 'loading' => 'eager' ) ); ?></figure>
			</div>
		<?php endif; ?>
		<div class="section section--tight">
			<div class="container">
				<div class="prose entry-content"><?php the_content(); ?></div>
				<?php
				wp_link_pages();
				$tags = get_the_tags();
				if ( $tags ) :
					?>
					<ul class="chips chips--center post-tags">
						<?php foreach ( $tags as $t ) : ?>
							<li><a href="<?php echo esc_url( get_tag_link( $t ) ); ?>">#<?php echo esc_html( $t->name ); ?></a></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
				<?php if ( comments_open() || get_comments_number() ) : ?>
					<div class="prose"><?php comments_template(); ?></div>
				<?php endif; ?>
			</div>
		</div>
		<?php
		$related = get_posts( array( 'post_type' => 'post', 'numberposts' => 3, 'post__not_in' => array( get_the_ID() ) ) );
		if ( $related ) :
			?>
			<section class="section section--alt">
				<div class="container">
					<?php folio_section_head( __( 'Keep reading', 'folio' ), __( 'More *articles*', 'folio' ) ); ?>
					<div class="posts-grid">
						<?php
						foreach ( $related as $r ) {
							folio_post_card( $r->ID );
						}
						?>
					</div>
				</div>
			</section>
		<?php endif; ?>
	</article>
	<?php
endwhile;

folio_cta_band();
get_footer();
