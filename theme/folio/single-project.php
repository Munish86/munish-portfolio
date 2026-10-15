<?php
/**
 * One project: title, facts, links, cover, write-up, gallery, tools, next project.
 *
 * @package folio
 */

defined( 'ABSPATH' ) || exit;
get_header();

while ( have_posts() ) :
	the_post();
	$id    = get_the_ID();
	$types = get_the_terms( $id, 'work_type' );
	$case  = (int) folio_meta( 'case_study' );
	?>
	<article <?php post_class( 'single-work' ); ?>>
		<header class="page-hero page-hero--work">
			<div class="container">
				<a class="back-link reveal" href="<?php echo esc_url( get_post_type_archive_link( 'project' ) ); ?>"><?php echo folio_icon( 'arrow-left' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?><?php esc_html_e( 'All work', 'folio' ); ?></a>
				<?php if ( $types && ! is_wp_error( $types ) ) : ?>
					<p class="eyebrow reveal"><?php echo esc_html( implode( ' · ', wp_list_pluck( $types, 'name' ) ) ); ?></p>
				<?php endif; ?>
				<h1 class="page-title reveal"><?php the_title(); ?></h1>
				<?php if ( has_excerpt() ) : ?>
					<p class="page-intro reveal"><?php echo esc_html( get_the_excerpt() ); ?></p>
				<?php endif; ?>
				<div class="work-head reveal">
					<?php
					echo folio_facts( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside.
						array(
							__( 'Client', 'folio' )   => folio_meta( 'client' ),
							__( 'My role', 'folio' )  => folio_meta( 'role' ),
							__( 'Year', 'folio' )     => folio_meta( 'year' ),
							__( 'Duration', 'folio' ) => folio_meta( 'duration' ),
						)
					);
					?>
					<div class="btn-row">
						<?php echo folio_link_buttons( $id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside. ?>
						<?php if ( $case && 'publish' === get_post_status( $case ) ) : ?>
							<?php echo folio_btn( __( 'Read the case study', 'folio' ), get_permalink( $case ), 'ghost', 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside. ?>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</header>

		<?php if ( has_post_thumbnail() ) : ?>
			<div class="container">
				<figure class="cover reveal"<?php echo folio_card_style( $id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside. ?>>
					<?php the_post_thumbnail( 'folio-wide', array( 'fetchpriority' => 'high', 'loading' => 'eager', 'sizes' => '(max-width: 1240px) 100vw, 1200px' ) ); ?>
				</figure>
			</div>
		<?php endif; ?>

		<section class="section section--tight">
			<div class="container">
				<div class="prose entry-content">
					<?php the_content(); ?>
				</div>
				<?php $tools = folio_term_chips( $id, 'tool', 'chips chips--center' ); ?>
				<?php if ( $tools ) : ?>
					<div class="tools-used reveal">
						<h2 class="tools-used__title"><?php esc_html_e( 'Tools & tech', 'folio' ); ?></h2>
						<?php echo $tools; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside. ?>
					</div>
				<?php endif; ?>
			</div>
		</section>

		<?php $gallery = folio_gallery( folio_meta( 'gallery' ) ); ?>
		<?php if ( $gallery ) : ?>
			<section class="section section--tight"><div class="container"><?php echo $gallery; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside. ?></div></section>
		<?php endif; ?>

		<?php get_template_part( 'template-parts/next', null, array( 'type' => 'project', 'label' => __( 'Next project', 'folio' ) ) ); ?>
	</article>
	<?php
endwhile;

folio_cta_band();
get_footer();
