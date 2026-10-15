<?php
/**
 * Home page. Every section reads Portfolio Settings and the portfolio post types; sections can be switched off
 * in Portfolio > Home page. If the home page (a Page) has its own text, it is shown above the testimonials.
 *
 * @package folio
 */

defined( 'ABSPATH' ) || exit;
get_header();

$name     = folio_owner_name();
$roles    = folio_lines( folio_opt( 'roles' ) );
$portrait = (int) folio_opt( 'portrait' );
$stats    = folio_opt( 'stats' );
$stats    = is_array( $stats ) ? $stats : array();
$tools    = folio_lines( folio_opt( 'marquee' ) );
$n        = 0; // Section counter for the eyebrow numbers.

/** Eyebrow with a running number, e.g. "01 / Services". */
$eyebrow = function ( $label ) use ( &$n ) {
	++$n;
	return str_pad( (string) $n, 2, '0', STR_PAD_LEFT ) . ' / ' . $label;
};
?>

<section class="hero">
	<div class="hero__bg" aria-hidden="true"><span class="blob blob--1"></span><span class="blob blob--2"></span><span class="grid-lines"></span></div>
	<div class="container hero__inner">
		<div class="hero__copy">
			<?php if ( folio_opt( 'available' ) ) : ?>
				<p class="status-pill reveal"><span class="status-pill__dot"></span><?php echo esc_html( folio_opt( 'available_text' ) ); ?></p>
			<?php endif; ?>
			<p class="hero__hello reveal"><?php echo esc_html( sprintf( /* translators: %s: name */ __( "Hi, I'm %s", 'folio' ), $name ) ); ?> <span class="wave" aria-hidden="true">&#128075;</span></p>
			<h1 class="hero__title reveal"><?php echo folio_highlight( folio_opt( 'hero_title' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside. ?></h1>
			<p class="hero__text reveal"><?php echo esc_html( folio_opt( 'hero_text' ) ); ?></p>
			<div class="btn-row reveal">
				<?php echo folio_btn( folio_opt( 'cta1_label' ), folio_opt( 'cta1_url' ), 'primary', 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside. ?>
				<?php echo folio_btn( folio_opt( 'cta2_label' ), folio_opt( 'cta2_url' ), 'ghost', '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside. ?>
			</div>
			<?php echo folio_social_links( 'socials hero__socials reveal' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside. ?>
		</div>

		<div class="hero__visual reveal">
			<?php if ( $portrait ) : ?>
				<div class="portrait">
					<?php echo wp_get_attachment_image( $portrait, 'large', false, array( 'class' => 'portrait__img', 'fetchpriority' => 'high', 'loading' => 'eager', 'alt' => $name ) ); ?>
				</div>
			<?php else : ?>
				<div class="code-card" role="img" aria-label="<?php echo esc_attr( implode( ', ', $roles ) ); ?>">
					<div class="code-card__bar"><span></span><span></span><span></span><em>about-me.ts</em></div>
					<pre class="code-card__code"><code><span class="k">const</span> <span class="v">me</span> <span class="p">=</span> {
  <span class="a">name</span>: <span class="s">"<?php echo esc_html( $name ); ?>"</span>,
  <span class="a">does</span>: [<?php echo implode( ', ', array_map( fn( $r ) => '<span class="s">"' . esc_html( $r ) . '"</span>', array_slice( $roles, 0, 2 ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in the map. ?>],
  <span class="a">stack</span>: [<?php echo implode( ', ', array_map( fn( $r ) => '<span class="s">"' . esc_html( $r ) . '"</span>', array_slice( $tools, 0, 3 ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in the map. ?>],
  <span class="a">pixelPerfect</span>: <span class="b">true</span>,
  <span class="a">available</span>: <span class="b"><?php echo folio_opt( 'available' ) ? 'true' : 'false'; ?></span>,
};<span class="caret"></span></code></pre>
				</div>
			<?php endif; ?>
			<?php
			$chip_icons = array( 'code', 'layout', 'pen', 'palette' );
			foreach ( array_slice( $roles, 0, 4 ) as $i => $role ) :
				?>
				<span class="float-chip float-chip--<?php echo (int) $i + 1; ?>"><?php echo folio_icon( $chip_icons[ $i ] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?><?php echo esc_html( $role ); ?></span>
			<?php endforeach; ?>
		</div>
	</div>

	<?php if ( $stats ) : ?>
		<div class="container">
			<dl class="stats">
				<?php foreach ( $stats as $s ) : ?>
					<div class="stats__item reveal"><dd data-count="<?php echo esc_attr( $s['value'] ); ?>"><?php echo esc_html( $s['value'] ); ?></dd><dt><?php echo esc_html( $s['label'] ); ?></dt></div>
				<?php endforeach; ?>
			</dl>
		</div>
	<?php endif; ?>
</section>

<?php if ( $tools ) : ?>
	<section class="marquee" aria-label="<?php esc_attr_e( 'Tools I use', 'folio' ); ?>">
		<div class="marquee__track">
			<?php for ( $copy = 0; $copy < 2; $copy++ ) : ?>
				<ul class="marquee__list"<?php echo $copy ? ' aria-hidden="true"' : ''; ?>>
					<?php foreach ( $tools as $t ) : ?>
						<li><?php echo esc_html( $t ); ?></li>
					<?php endforeach; ?>
				</ul>
			<?php endfor; ?>
		</div>
	</section>
<?php endif; ?>

<?php
$clients = array_filter( array_map( 'absint', explode( ',', (string) folio_opt( 'clients' ) ) ) );
if ( $clients ) :
	?>
	<section class="section section--tight clients">
		<div class="container">
			<p class="clients__label"><?php esc_html_e( 'Trusted by teams at', 'folio' ); ?></p>
			<ul class="clients__list">
				<?php foreach ( $clients as $logo ) : ?>
					<li><?php echo wp_get_attachment_image( $logo, 'medium', false, array( 'loading' => 'lazy' ) ); ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
	</section>
<?php endif; ?>

<?php
$services = folio_opt( 'show_services' ) ? get_posts( array( 'post_type' => 'service', 'numberposts' => 6, 'orderby' => array( 'menu_order' => 'ASC', 'date' => 'DESC' ) ) ) : array();
if ( $services ) :
	?>
	<section class="section" id="services">
		<div class="container">
			<?php folio_section_head( $eyebrow( __( 'Services', 'folio' ) ), folio_opt( 'services_title' ), folio_opt( 'services_text' ), get_post_type_archive_link( 'service' ), __( 'All services', 'folio' ) ); ?>
			<div class="services-grid">
				<?php
				foreach ( $services as $i => $s ) {
					folio_service_card( $s->ID, $i + 1 );
				}
				?>
			</div>
		</div>
	</section>
<?php endif; ?>

<?php
$work = array();
if ( folio_opt( 'show_work' ) ) {
	$work = get_posts(
		array(
			'post_type'   => 'project',
			'numberposts' => 6,
			'meta_key'    => '_folio_featured', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_value'  => '1', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			'orderby'     => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
		)
	);
	// Nothing ticked yet: show the newest projects.
	if ( ! $work ) {
		$work = get_posts( array( 'post_type' => 'project', 'numberposts' => 4, 'orderby' => array( 'menu_order' => 'ASC', 'date' => 'DESC' ) ) );
	}
}
if ( $work ) :
	?>
	<section class="section section--alt" id="work">
		<div class="container">
			<?php folio_section_head( $eyebrow( __( 'Work', 'folio' ) ), folio_opt( 'work_title' ), folio_opt( 'work_text' ), get_post_type_archive_link( 'project' ), __( 'View all projects', 'folio' ) ); ?>
			<div class="work-grid">
				<?php
				foreach ( $work as $i => $p ) {
					folio_project_card( $p->ID, 0 === $i % 3 ? 'is-large' : '' );
				}
				?>
			</div>
		</div>
	</section>
<?php endif; ?>

<?php
$cases = array();
if ( folio_opt( 'show_cases' ) ) {
	$cases = get_posts(
		array(
			'post_type'   => 'case_study',
			'numberposts' => 3,
			'meta_key'    => '_folio_featured', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_value'  => '1', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			'orderby'     => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
		)
	);
	if ( ! $cases ) {
		$cases = get_posts( array( 'post_type' => 'case_study', 'numberposts' => 2 ) );
	}
}
if ( $cases ) :
	?>
	<section class="section" id="case-studies">
		<div class="container">
			<?php folio_section_head( $eyebrow( __( 'Case studies', 'folio' ) ), folio_opt( 'cases_title' ), folio_opt( 'cases_text' ), get_post_type_archive_link( 'case_study' ), __( 'All case studies', 'folio' ) ); ?>
			<div class="case-list">
				<?php
				foreach ( $cases as $i => $c ) {
					folio_case_card( $c->ID, (bool) ( $i % 2 ) );
				}
				?>
			</div>
		</div>
	</section>
<?php endif; ?>

<?php
$about_page = get_page_by_path( 'about' );
$skills     = folio_opt( 'skills' );
if ( folio_opt( 'show_about' ) && folio_opt( 'about_text' ) ) :
	$paras = preg_split( '/\n\s*\n/', trim( (string) folio_opt( 'about_text' ) ) );
	?>
	<section class="section section--alt" id="about">
		<div class="container about-split">
			<div class="about-split__media reveal">
				<?php if ( $portrait ) : ?>
					<?php echo wp_get_attachment_image( $portrait, 'large', false, array( 'loading' => 'lazy', 'alt' => $name ) ); ?>
				<?php else : ?>
					<div class="about-split__monogram" aria-hidden="true"><?php echo esc_html( function_exists( 'mb_substr' ) ? mb_substr( $name, 0, 1 ) : substr( $name, 0, 1 ) ); ?></div>
				<?php endif; ?>
				<?php if ( folio_opt( 'location' ) ) : ?>
					<span class="about-split__badge"><?php echo folio_icon( 'pin' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?><?php echo esc_html( folio_opt( 'location' ) ); ?></span>
				<?php endif; ?>
			</div>
			<div class="about-split__body">
				<p class="eyebrow reveal"><?php echo esc_html( $eyebrow( __( 'About', 'folio' ) ) ); ?></p>
				<h2 class="section-title reveal"><?php echo folio_highlight( folio_opt( 'about_title' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside. ?></h2>
				<div class="prose prose--lead reveal"><?php echo folio_paragraphs( $paras[0] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside. ?></div>
				<?php if ( is_array( $skills ) && $skills ) : ?>
					<ul class="chips chips--lg reveal">
						<?php foreach ( array_slice( $skills, 0, 4 ) as $g ) : ?>
							<li><?php echo esc_html( $g['group'] ); ?></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
				<?php if ( $about_page ) : ?>
					<div class="btn-row reveal"><?php echo folio_btn( __( 'More about me', 'folio' ), get_permalink( $about_page ), 'ghost' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside. ?></div>
				<?php endif; ?>
			</div>
		</div>
	</section>
<?php endif; ?>

<?php
$steps = folio_opt( 'process' );
if ( folio_opt( 'show_process' ) && is_array( $steps ) && $steps ) :
	?>
	<section class="section" id="process">
		<div class="container">
			<?php folio_section_head( $eyebrow( __( 'Process', 'folio' ) ), folio_opt( 'process_title' ) ); ?>
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
<?php endif; ?>

<?php
// Text written on the home Page itself (optional), for anything the sections above do not cover.
while ( have_posts() ) :
	the_post();
	if ( '' !== trim( get_the_content() ) ) :
		?>
		<section class="section"><div class="container prose prose--wide entry-content"><?php the_content(); ?></div></section>
		<?php
	endif;
endwhile;
?>

<?php
$quotes = folio_opt( 'show_testimonials' ) ? get_posts( array( 'post_type' => 'testimonial', 'numberposts' => 6, 'orderby' => array( 'menu_order' => 'ASC', 'date' => 'DESC' ) ) ) : array();
if ( $quotes ) :
	?>
	<section class="section section--alt" id="testimonials">
		<div class="container">
			<?php folio_section_head( $eyebrow( __( 'Testimonials', 'folio' ) ), folio_opt( 'testimonials_title' ) ); ?>
			<div class="quotes-grid">
				<?php
				foreach ( $quotes as $q ) {
					folio_testimonial_card( $q->ID );
				}
				?>
			</div>
		</div>
	</section>
<?php endif; ?>

<?php
$posts = folio_opt( 'show_blog' ) ? get_posts( array( 'post_type' => 'post', 'numberposts' => 3 ) ) : array();
if ( $posts ) :
	$blog_url = get_option( 'page_for_posts' ) ? get_permalink( (int) get_option( 'page_for_posts' ) ) : '';
	?>
	<section class="section" id="blog">
		<div class="container">
			<?php folio_section_head( $eyebrow( __( 'Blog', 'folio' ) ), folio_opt( 'blog_title' ), '', $blog_url, __( 'All articles', 'folio' ) ); ?>
			<div class="posts-grid">
				<?php
				foreach ( $posts as $p ) {
					folio_post_card( $p->ID );
				}
				?>
			</div>
		</div>
	</section>
<?php endif; ?>

<?php
folio_cta_band();
get_footer();
