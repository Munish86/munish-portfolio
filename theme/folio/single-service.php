<?php
/**
 * One service: description, what is included, price, related projects.
 *
 * @package folio
 */

defined( 'ABSPATH' ) || exit;
get_header();

while ( have_posts() ) :
	the_post();
	$id    = get_the_ID();
	$items = folio_lines( folio_meta( 'deliverables' ) );
	$price = folio_meta( 'price' );
	?>
	<article <?php post_class(); ?>>
		<header class="page-hero">
			<div class="container">
				<a class="back-link reveal" href="<?php echo esc_url( get_post_type_archive_link( 'service' ) ); ?>"><?php echo folio_icon( 'arrow-left' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?><?php esc_html_e( 'All services', 'folio' ); ?></a>
				<span class="icon-tile icon-tile--lg reveal"><?php echo folio_icon( folio_meta( 'icon' ) ? folio_meta( 'icon' ) : 'code' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?></span>
				<h1 class="page-title reveal"><?php the_title(); ?></h1>
				<?php if ( has_excerpt() ) : ?>
					<p class="page-intro reveal"><?php echo esc_html( get_the_excerpt() ); ?></p>
				<?php endif; ?>
			</div>
		</header>
		<section class="section section--flush-top">
			<div class="container service-layout">
				<div class="prose entry-content"><?php the_content(); ?></div>
				<aside class="service-aside reveal">
					<?php if ( $items ) : ?>
						<h2><?php esc_html_e( "What's included", 'folio' ); ?></h2>
						<ul class="checks">
							<?php foreach ( $items as $it ) : ?>
								<li><?php echo folio_icon( 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?><?php echo esc_html( $it ); ?></li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
					<?php if ( $price ) : ?>
						<p class="service-aside__price"><?php echo esc_html( $price ); ?></p>
					<?php endif; ?>
					<?php echo folio_btn( __( 'Ask about this service', 'folio' ), folio_contact_url(), 'primary' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside. ?>
				</aside>
			</div>
		</section>
		<?php
		$projects = get_posts( array( 'post_type' => 'project', 'numberposts' => 2, 'orderby' => array( 'menu_order' => 'ASC', 'date' => 'DESC' ) ) );
		if ( $projects ) :
			?>
			<section class="section section--alt">
				<div class="container">
					<?php folio_section_head( __( 'Work', 'folio' ), __( 'Recent *projects*', 'folio' ), '', get_post_type_archive_link( 'project' ), __( 'View all projects', 'folio' ) ); ?>
					<div class="work-grid">
						<?php
						foreach ( $projects as $p ) {
							folio_project_card( $p->ID );
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
