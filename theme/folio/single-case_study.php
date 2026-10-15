<?php
/**
 * One case study: headline, overview facts, cover, challenge / solution, the full story (editor), results,
 * client quote, gallery, next case study.
 *
 * @package folio
 */

defined( 'ABSPATH' ) || exit;
get_header();

while ( have_posts() ) :
	the_post();
	$id        = get_the_ID();
	$challenge = folio_meta( 'challenge' );
	$solution  = folio_meta( 'solution' );
	$quote     = folio_meta( 'quote' );
	$results   = folio_results( $id, 6 );
	?>
	<article <?php post_class( 'single-case' ); ?>>
		<header class="page-hero page-hero--work">
			<div class="container">
				<a class="back-link reveal" href="<?php echo esc_url( get_post_type_archive_link( 'case_study' ) ); ?>"><?php echo folio_icon( 'arrow-left' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?><?php esc_html_e( 'All case studies', 'folio' ); ?></a>
				<p class="eyebrow reveal"><?php echo esc_html( implode( ' · ', array_filter( array( __( 'Case study', 'folio' ), folio_meta( 'client' ) ) ) ) ); ?></p>
				<h1 class="page-title reveal"><?php the_title(); ?></h1>
				<?php if ( has_excerpt() ) : ?>
					<p class="page-intro reveal"><?php echo esc_html( get_the_excerpt() ); ?></p>
				<?php endif; ?>
				<div class="work-head reveal">
					<?php
					echo folio_facts( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside.
						array(
							__( 'Client', 'folio' )       => folio_meta( 'client' ),
							__( 'Industry', 'folio' )     => folio_meta( 'industry' ),
							__( 'My role', 'folio' )      => folio_meta( 'role' ),
							__( 'Timeline', 'folio' )     => folio_meta( 'timeline' ),
							__( 'Team', 'folio' )         => folio_meta( 'team' ),
							__( 'Deliverables', 'folio' ) => folio_meta( 'deliverables' ),
						)
					);
					?>
					<div class="btn-row"><?php echo folio_link_buttons( $id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside. ?></div>
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

		<?php if ( $challenge || $solution ) : ?>
			<section class="section section--tight">
				<div class="container cs-split">
					<?php if ( $challenge ) : ?>
						<div class="cs-split__item reveal">
							<span class="icon-tile icon-tile--soft"><?php echo folio_icon( 'search' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?></span>
							<h2><?php esc_html_e( 'The challenge', 'folio' ); ?></h2>
							<div class="prose"><?php echo folio_paragraphs( $challenge ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside. ?></div>
						</div>
					<?php endif; ?>
					<?php if ( $solution ) : ?>
						<div class="cs-split__item reveal">
							<span class="icon-tile"><?php echo folio_icon( 'sparkles' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?></span>
							<h2><?php esc_html_e( 'The solution', 'folio' ); ?></h2>
							<div class="prose"><?php echo folio_paragraphs( $solution ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside. ?></div>
						</div>
					<?php endif; ?>
				</div>
			</section>
		<?php endif; ?>

		<?php if ( '' !== trim( get_the_content() ) ) : ?>
			<section class="section section--tight">
				<div class="container"><div class="prose entry-content"><?php the_content(); ?></div></div>
			</section>
		<?php endif; ?>

		<?php if ( $results ) : ?>
			<section class="section section--tight">
				<div class="container">
					<div class="results-band reveal">
						<h2 class="results-band__title"><?php esc_html_e( 'The results', 'folio' ); ?></h2>
						<?php echo $results; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside. ?>
					</div>
				</div>
			</section>
		<?php endif; ?>

		<?php if ( $quote ) : ?>
			<section class="section section--tight">
				<div class="container">
					<figure class="big-quote reveal">
						<?php echo folio_icon( 'quote', 'big-quote__mark' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
						<blockquote><?php echo folio_paragraphs( $quote ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside. ?></blockquote>
						<figcaption><strong><?php echo esc_html( folio_meta( 'quote_name' ) ); ?></strong> <span><?php echo esc_html( folio_meta( 'quote_role' ) ); ?></span></figcaption>
					</figure>
				</div>
			</section>
		<?php endif; ?>

		<?php $gallery = folio_gallery( folio_meta( 'gallery' ) ); ?>
		<?php if ( $gallery ) : ?>
			<section class="section section--tight"><div class="container"><?php echo $gallery; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside. ?></div></section>
		<?php endif; ?>

		<?php $tools = folio_term_chips( $id, 'tool', 'chips chips--center' ); ?>
		<?php if ( $tools ) : ?>
			<div class="container tools-used reveal">
				<h2 class="tools-used__title"><?php esc_html_e( 'Tools & tech', 'folio' ); ?></h2>
				<?php echo $tools; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside. ?>
			</div>
		<?php endif; ?>

		<?php get_template_part( 'template-parts/next', null, array( 'type' => 'case_study', 'label' => __( 'Next case study', 'folio' ) ) ); ?>
	</article>
	<?php
endwhile;

folio_cta_band();
get_footer();
