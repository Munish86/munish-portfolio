<?php
/**
 * Services page (/services/): all services, the process steps and the CTA.
 *
 * @package folio
 */

defined( 'ABSPATH' ) || exit;
get_header();
folio_page_hero( __( 'Services', 'folio' ), folio_opt( 'services_page_title', __( 'Services', 'folio' ) ), folio_opt( 'services_page_text' ) );
?>
<section class="section section--flush-top">
	<div class="container">
		<?php if ( have_posts() ) : ?>
			<div class="services-grid">
				<?php
				$i = 0;
				while ( have_posts() ) :
					the_post();
					folio_service_card( get_the_ID(), ++$i );
				endwhile;
				?>
			</div>
		<?php else : ?>
			<p class="empty-note"><?php esc_html_e( 'Services will appear here soon.', 'folio' ); ?></p>
		<?php endif; ?>
	</div>
</section>
<?php
$steps = folio_opt( 'process' );
if ( is_array( $steps ) && $steps ) :
	?>
	<section class="section section--alt">
		<div class="container">
			<?php folio_section_head( __( 'Process', 'folio' ), folio_opt( 'process_title' ) ); ?>
			<ol class="process">
				<?php foreach ( $steps as $i => $step ) : ?>
					<li class="process__step reveal">
						<span class="process__num"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
						<h3><?php echo esc_html( $step['title'] ); ?></h3>
						<p><?php echo esc_html( $step['text'] ); ?></p>
					</li>
				<?php endforeach; ?>
			</ol>
		</div>
	</section>
	<?php
endif;
folio_cta_band();
get_footer();
