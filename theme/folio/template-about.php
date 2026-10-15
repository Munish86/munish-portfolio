<?php
/**
 * Template Name: About
 * Template Post Type: page
 *
 * About page: photo, headline and text (Portfolio > About & skills), CV button, skills, experience timeline,
 * testimonials. Anything written in the page editor is shown after the skills.
 *
 * @package folio
 */

defined( 'ABSPATH' ) || exit;
get_header();

$name     = folio_owner_name();
$portrait = (int) folio_opt( 'portrait' );
$skills   = folio_opt( 'skills' );
$resume   = folio_opt( 'resume' );
$stats    = folio_opt( 'stats' );
?>
<section class="about-hero">
	<div class="container about-hero__inner">
		<div class="about-hero__media reveal">
			<?php if ( $portrait ) : ?>
				<?php echo wp_get_attachment_image( $portrait, 'large', false, array( 'loading' => 'eager', 'fetchpriority' => 'high', 'alt' => $name ) ); ?>
			<?php else : ?>
				<div class="about-split__monogram" aria-hidden="true"><?php echo esc_html( function_exists( 'mb_substr' ) ? mb_substr( $name, 0, 1 ) : substr( $name, 0, 1 ) ); ?></div>
			<?php endif; ?>
		</div>
		<div class="about-hero__body">
			<p class="eyebrow reveal"><?php echo esc_html( get_the_title() ); ?></p>
			<h1 class="page-title reveal"><?php echo folio_highlight( folio_opt( 'about_title' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside. ?></h1>
			<div class="prose prose--lead reveal"><?php echo folio_paragraphs( folio_opt( 'about_text' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside. ?></div>
			<div class="btn-row reveal">
				<?php echo folio_btn( __( 'Work with me', 'folio' ), folio_contact_url(), 'primary' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside. ?>
				<?php if ( $resume ) : ?>
					<?php echo folio_btn( __( 'Download CV', 'folio' ), $resume, 'ghost', 'download', array( 'target' => '_blank', 'rel' => 'noopener' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside. ?>
				<?php endif; ?>
			</div>
			<?php echo folio_social_links( 'socials reveal' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside. ?>
		</div>
	</div>
	<?php if ( is_array( $stats ) && $stats ) : ?>
		<div class="container">
			<dl class="stats">
				<?php foreach ( $stats as $s ) : ?>
					<div class="stats__item reveal"><dd data-count="<?php echo esc_attr( $s['value'] ); ?>"><?php echo esc_html( $s['value'] ); ?></dd><dt><?php echo esc_html( $s['label'] ); ?></dt></div>
				<?php endforeach; ?>
			</dl>
		</div>
	<?php endif; ?>
</section>

<?php if ( is_array( $skills ) && $skills ) : ?>
	<section class="section">
		<div class="container">
			<?php folio_section_head( __( 'Skills', 'folio' ), __( 'What I *bring*', 'folio' ) ); ?>
			<div class="skills-grid">
				<?php foreach ( $skills as $i => $g ) : ?>
					<div class="skill-group reveal">
						<span class="skill-group__num"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
						<h3><?php echo esc_html( $g['group'] ); ?></h3>
						<ul class="chips">
							<?php foreach ( array_filter( array_map( 'trim', explode( ',', $g['items'] ) ) ) as $skill ) : ?>
								<li><?php echo esc_html( $skill ); ?></li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
<?php endif; ?>

<?php
while ( have_posts() ) :
	the_post();
	if ( '' !== trim( get_the_content() ) ) :
		?>
		<section class="section section--tight"><div class="container"><div class="prose entry-content"><?php the_content(); ?></div></div></section>
		<?php
	endif;
endwhile;
?>

<?php
$items = folio_opt( 'show_experience' ) ? get_posts( array( 'post_type' => 'experience', 'numberposts' => 30, 'orderby' => array( 'menu_order' => 'ASC', 'date' => 'DESC' ) ) ) : array();
if ( $items ) :
	$kinds = array(
		'work'      => array( 'briefcase', __( 'Job', 'folio' ) ),
		'freelance' => array( 'rocket', __( 'Freelance', 'folio' ) ),
		'education' => array( 'pen', __( 'Education', 'folio' ) ),
		'award'     => array( 'star', __( 'Award', 'folio' ) ),
	);
	?>
	<section class="section section--alt">
		<div class="container">
			<?php folio_section_head( __( 'Experience', 'folio' ), __( 'Where I have *worked*', 'folio' ) ); ?>
			<ol class="timeline">
				<?php
				foreach ( $items as $e ) :
					$kind  = folio_meta( 'kind', $e->ID );
					$kind  = isset( $kinds[ $kind ] ) ? $kind : 'work';
					$start = folio_meta( 'start', $e->ID );
					$end   = folio_meta( 'end', $e->ID );
					$when  = $start && $start === $end ? $start : trim( $start . ( $start || $end ? ' – ' : '' ) . ( $end ? $end : ( $start ? __( 'Present', 'folio' ) : '' ) ) );
					?>
					<li class="timeline__item reveal">
						<span class="timeline__dot"><?php echo folio_icon( $kinds[ $kind ][0] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?></span>
						<div class="timeline__card">
							<p class="timeline__when"><?php echo esc_html( $when ); ?> <span class="tag"><?php echo esc_html( $kinds[ $kind ][1] ); ?></span></p>
							<h3><?php echo esc_html( get_the_title( $e ) ); ?></h3>
							<p class="timeline__where"><?php echo esc_html( implode( ' · ', array_filter( array( folio_meta( 'company', $e->ID ), folio_meta( 'location', $e->ID ) ) ) ) ); ?></p>
							<?php if ( '' !== trim( $e->post_content ) ) : ?>
								<div class="timeline__text"><?php echo wp_kses_post( wpautop( $e->post_content ) ); ?></div>
							<?php endif; ?>
						</div>
					</li>
				<?php endforeach; ?>
			</ol>
		</div>
	</section>
<?php endif; ?>

<?php
$quotes = get_posts( array( 'post_type' => 'testimonial', 'numberposts' => 3, 'orderby' => array( 'menu_order' => 'ASC', 'date' => 'DESC' ) ) );
if ( $quotes ) :
	?>
	<section class="section">
		<div class="container">
			<?php folio_section_head( __( 'Testimonials', 'folio' ), folio_opt( 'testimonials_title' ) ); ?>
			<div class="quotes-grid">
				<?php
				foreach ( $quotes as $q ) {
					folio_testimonial_card( $q->ID );
				}
				?>
			</div>
		</div>
	</section>
	<?php
endif;

folio_cta_band();
get_footer();
