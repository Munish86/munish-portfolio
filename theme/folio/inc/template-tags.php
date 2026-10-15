<?php
/**
 * Small renderers shared by the templates: section heads, cards, buttons, CTA band.
 *
 * @package folio
 */

defined( 'ABSPATH' ) || exit;

/**
 * A button link.
 *
 * @param string $label   Text.
 * @param string $url     Link.
 * @param string $variant primary|ghost|light.
 * @param string $icon    Icon name or ''.
 * @param array  $attrs   Extra attributes.
 * @return string
 */
function folio_btn( $label, $url, $variant = 'primary', $icon = 'arrow-right', $attrs = array() ) {
	if ( '' === trim( (string) $label ) || '' === trim( (string) $url ) ) {
		return '';
	}
	$extra = '';
	foreach ( $attrs as $k => $v ) {
		$extra .= ' ' . esc_attr( $k ) . '="' . esc_attr( $v ) . '"';
	}
	return '<a class="btn btn--' . esc_attr( $variant ) . '" href="' . esc_url( $url ) . '"' . $extra . '><span>' . esc_html( $label ) . '</span>' . ( $icon ? folio_icon( $icon ) : '' ) . '</a>';
}

/**
 * Section heading block.
 *
 * @param string $eyebrow    Small label above (e.g. "Services").
 * @param string $title      Title; *stars* highlight words.
 * @param string $text       Intro text.
 * @param string $link_url   Optional "see all" link.
 * @param string $link_label Its text.
 */
function folio_section_head( $eyebrow, $title, $text = '', $link_url = '', $link_label = '' ) {
	echo '<div class="section-head reveal">';
	echo '<div class="section-head__main">';
	if ( $eyebrow ) {
		echo '<p class="eyebrow">' . esc_html( $eyebrow ) . '</p>';
	}
	echo '<h2 class="section-title">' . folio_highlight( $title ) . '</h2>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside.
	if ( $text ) {
		echo '<p class="section-intro">' . esc_html( $text ) . '</p>';
	}
	echo '</div>';
	if ( $link_url && $link_label ) {
		echo '<a class="link-arrow" href="' . esc_url( $link_url ) . '">' . esc_html( $link_label ) . folio_icon( 'arrow-up-right' ) . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG.
	}
	echo '</div>';
}

/**
 * Top of an inner page.
 *
 * @param string $eyebrow Small label.
 * @param string $title   Title (*stars* highlight).
 * @param string $text    Intro.
 * @param bool   $raw     Title is already safe HTML.
 */
function folio_page_hero( $eyebrow, $title, $text = '', $raw = false ) {
	echo '<header class="page-hero"><div class="container">';
	if ( $eyebrow ) {
		echo '<p class="eyebrow reveal">' . esc_html( $eyebrow ) . '</p>';
	}
	echo '<h1 class="page-title reveal">' . ( $raw ? $title : folio_highlight( $title ) ) . '</h1>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by caller or inside.
	if ( $text ) {
		echo '<p class="page-intro reveal">' . esc_html( $text ) . '</p>';
	}
	echo '</div></header>';
}

/**
 * Plain text with blank lines turned into paragraphs, escaped.
 *
 * @param string $text Text.
 * @return string
 */
function folio_paragraphs( $text ) {
	return wpautop( esc_html( (string) $text ) );
}

/**
 * Term names of a post as chips.
 *
 * @param int    $post_id Post.
 * @param string $tax     Taxonomy.
 * @param string $class   Wrapper class.
 * @return string
 */
function folio_term_chips( $post_id, $tax, $class = 'chips' ) {
	$terms = get_the_terms( $post_id, $tax );
	if ( ! $terms || is_wp_error( $terms ) ) {
		return '';
	}
	$out = '<ul class="' . esc_attr( $class ) . '">';
	foreach ( $terms as $t ) {
		$out .= '<li>' . esc_html( $t->name ) . '</li>';
	}
	return $out . '</ul>';
}

/**
 * Term slugs for filtering (space separated).
 *
 * @param int    $post_id Post.
 * @param string $tax     Taxonomy.
 * @return string
 */
function folio_term_slugs( $post_id, $tax ) {
	$terms = get_the_terms( $post_id, $tax );
	return $terms && ! is_wp_error( $terms ) ? implode( ' ', wp_list_pluck( $terms, 'slug' ) ) : '';
}

/**
 * Inline style with the item's card colour.
 *
 * @param int $post_id Post.
 * @return string Attribute (with leading space) or ''.
 */
function folio_card_style( $post_id ) {
	$c = folio_meta( 'accent', $post_id );
	return $c ? ' style="--card:' . esc_attr( $c ) . '"' : '';
}

/**
 * Project card.
 *
 * @param int    $post_id Post.
 * @param string $extra   Extra class.
 */
function folio_project_card( $post_id, $extra = '' ) {
	$types = get_the_terms( $post_id, 'work_type' );
	$type  = $types && ! is_wp_error( $types ) ? $types[0]->name : '';
	$year  = folio_meta( 'year', $post_id );
	?>
	<article class="work-card reveal <?php echo esc_attr( $extra ); ?>" data-types="<?php echo esc_attr( folio_term_slugs( $post_id, 'work_type' ) ); ?>">
		<a class="work-card__link" href="<?php echo esc_url( get_permalink( $post_id ) ); ?>">
			<div class="work-card__media"<?php echo folio_card_style( $post_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside. ?>>
				<?php
				if ( has_post_thumbnail( $post_id ) ) {
					echo get_the_post_thumbnail( $post_id, 'folio-card', array( 'loading' => 'lazy', 'sizes' => '(max-width: 760px) 100vw, 50vw' ) );
				} else {
					echo '<span class="work-card__placeholder">' . esc_html( get_the_title( $post_id ) ) . '</span>';
				}
				?>
				<span class="work-card__go" aria-hidden="true"><?php echo folio_icon( 'arrow-up-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?></span>
			</div>
			<div class="work-card__body">
				<div>
					<h3 class="work-card__title"><?php echo esc_html( get_the_title( $post_id ) ); ?></h3>
					<?php if ( has_excerpt( $post_id ) ) : ?>
						<p class="work-card__text"><?php echo esc_html( get_the_excerpt( $post_id ) ); ?></p>
					<?php endif; ?>
				</div>
				<p class="work-card__meta"><?php echo esc_html( implode( ' · ', array_filter( array( $type, $year ) ) ) ); ?></p>
			</div>
		</a>
	</article>
	<?php
}

/**
 * Results row of a case study (up to $max).
 *
 * @param int $post_id Post.
 * @param int $max     Max items.
 * @return string
 */
function folio_results( $post_id, $max = 4 ) {
	$rows = folio_meta( 'results', $post_id );
	if ( ! is_array( $rows ) || ! $rows ) {
		return '';
	}
	$out = '<dl class="results">';
	foreach ( array_slice( $rows, 0, $max ) as $r ) {
		$out .= '<div class="results__item"><dt>' . esc_html( $r['label'] ) . '</dt><dd data-count="' . esc_attr( $r['value'] ) . '">' . esc_html( $r['value'] ) . '</dd></div>';
	}
	return $out . '</dl>';
}

/**
 * Wide case study card (image + story + results).
 *
 * @param int  $post_id Post.
 * @param bool $flip    Image on the left.
 */
function folio_case_card( $post_id, $flip = false ) {
	$client = folio_meta( 'client', $post_id );
	$ind    = folio_meta( 'industry', $post_id );
	?>
	<article class="case-card reveal<?php echo $flip ? ' is-flipped' : ''; ?>">
		<div class="case-card__body">
			<p class="case-card__client"><?php echo esc_html( implode( ' · ', array_filter( array( $client, $ind ) ) ) ); ?></p>
			<h3 class="case-card__title"><a href="<?php echo esc_url( get_permalink( $post_id ) ); ?>"><?php echo esc_html( get_the_title( $post_id ) ); ?></a></h3>
			<?php if ( has_excerpt( $post_id ) ) : ?>
				<p class="case-card__text"><?php echo esc_html( get_the_excerpt( $post_id ) ); ?></p>
			<?php endif; ?>
			<?php echo folio_results( $post_id, 3 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside. ?>
			<?php echo folio_btn( __( 'Read case study', 'folio' ), get_permalink( $post_id ), 'ghost' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside. ?>
		</div>
		<a class="case-card__media" href="<?php echo esc_url( get_permalink( $post_id ) ); ?>" tabindex="-1" aria-hidden="true"<?php echo folio_card_style( $post_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside. ?>>
			<?php
			if ( has_post_thumbnail( $post_id ) ) {
				echo get_the_post_thumbnail( $post_id, 'folio-card', array( 'loading' => 'lazy', 'alt' => '' ) );
			}
			?>
		</a>
	</article>
	<?php
}

/**
 * Service card.
 *
 * @param int $post_id Post.
 * @param int $i       Position (for the number).
 */
function folio_service_card( $post_id, $i ) {
	$items = folio_lines( folio_meta( 'deliverables', $post_id ) );
	$price = folio_meta( 'price', $post_id );
	?>
	<article class="service-card reveal">
		<div class="service-card__top">
			<span class="icon-tile"><?php echo folio_icon( folio_meta( 'icon', $post_id ) ? folio_meta( 'icon', $post_id ) : 'code' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?></span>
			<span class="service-card__num"><?php echo esc_html( str_pad( (string) $i, 2, '0', STR_PAD_LEFT ) ); ?></span>
		</div>
		<h3 class="service-card__title"><a href="<?php echo esc_url( get_permalink( $post_id ) ); ?>"><?php echo esc_html( get_the_title( $post_id ) ); ?></a></h3>
		<?php if ( has_excerpt( $post_id ) ) : ?>
			<p class="service-card__text"><?php echo esc_html( get_the_excerpt( $post_id ) ); ?></p>
		<?php endif; ?>
		<?php if ( $items ) : ?>
			<ul class="checks">
				<?php foreach ( array_slice( $items, 0, 5 ) as $it ) : ?>
					<li><?php echo folio_icon( 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?><?php echo esc_html( $it ); ?></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
		<?php if ( $price ) : ?>
			<p class="service-card__price"><?php echo esc_html( $price ); ?></p>
		<?php endif; ?>
	</article>
	<?php
}

/**
 * Testimonial card.
 *
 * @param int $post_id Post.
 */
function folio_testimonial_card( $post_id ) {
	$stars   = (int) folio_meta( 'rating', $post_id );
	$role    = implode( ', ', array_filter( array( folio_meta( 'role', $post_id ), folio_meta( 'company', $post_id ) ) ) );
	$name    = get_the_title( $post_id );
	$initial = function_exists( 'mb_substr' ) ? mb_substr( $name, 0, 1 ) : substr( $name, 0, 1 );
	?>
	<figure class="quote-card reveal">
		<?php if ( $stars ) : ?>
			<div class="stars" role="img" aria-label="<?php echo esc_attr( sprintf( /* translators: %d: stars */ __( '%d out of 5 stars', 'folio' ), $stars ) ); ?>">
				<?php echo str_repeat( folio_icon( 'star' ), max( 0, min( 5, $stars ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
			</div>
		<?php endif; ?>
		<blockquote><?php echo wp_kses_post( wpautop( get_post_field( 'post_content', $post_id ) ) ); ?></blockquote>
		<figcaption>
			<?php if ( has_post_thumbnail( $post_id ) ) : ?>
				<?php echo get_the_post_thumbnail( $post_id, 'folio-avatar', array( 'class' => 'avatar', 'loading' => 'lazy', 'alt' => '' ) ); ?>
			<?php else : ?>
				<span class="avatar avatar--letter" aria-hidden="true"><?php echo esc_html( $initial ); ?></span>
			<?php endif; ?>
			<span><strong><?php echo esc_html( $name ); ?></strong><?php echo $role ? '<span>' . esc_html( $role ) . '</span>' : ''; ?></span>
		</figcaption>
	</figure>
	<?php
}

/** Blog post card (inside the loop or with a post id). */
function folio_post_card( $post_id = 0 ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$cats    = get_the_category( $post_id );
	?>
	<article class="post-card reveal">
		<a class="post-card__media" href="<?php echo esc_url( get_permalink( $post_id ) ); ?>" tabindex="-1" aria-hidden="true">
			<?php
			if ( has_post_thumbnail( $post_id ) ) {
				echo get_the_post_thumbnail( $post_id, 'large', array( 'loading' => 'lazy', 'alt' => '' ) );
			}
			?>
		</a>
		<p class="post-card__meta"><?php echo esc_html( implode( ' · ', array_filter( array( $cats ? $cats[0]->name : '', get_the_date( '', $post_id ), folio_reading_time( $post_id ) ) ) ) ); ?></p>
		<h3 class="post-card__title"><a href="<?php echo esc_url( get_permalink( $post_id ) ); ?>"><?php echo esc_html( get_the_title( $post_id ) ); ?></a></h3>
		<p class="post-card__text"><?php echo esc_html( get_the_excerpt( $post_id ) ); ?></p>
	</article>
	<?php
}

/**
 * "5 min read".
 *
 * @param int $post_id Post.
 * @return string
 */
function folio_reading_time( $post_id ) {
	$words = str_word_count( wp_strip_all_tags( get_post_field( 'post_content', $post_id ) ) );
	/* translators: %d: minutes */
	return sprintf( __( '%d min read', 'folio' ), max( 1, (int) round( $words / 220 ) ) );
}

/**
 * Definition list of facts (label => value); empty values are skipped.
 *
 * @param array $facts Label => value.
 * @return string
 */
function folio_facts( $facts ) {
	$facts = array_filter( $facts, fn( $v ) => '' !== trim( (string) $v ) );
	if ( ! $facts ) {
		return '';
	}
	$out = '<dl class="facts">';
	foreach ( $facts as $label => $value ) {
		$out .= '<div><dt>' . esc_html( $label ) . '</dt><dd>' . esc_html( $value ) . '</dd></div>';
	}
	return $out . '</dl>';
}

/**
 * Buttons for live / code / design links of a project or case study.
 *
 * @param int $post_id Post.
 * @return string
 */
function folio_link_buttons( $post_id ) {
	$out  = '';
	$live = folio_meta( 'live_url', $post_id );
	$code = folio_meta( 'code_url', $post_id );
	$des  = folio_meta( 'design_url', $post_id );
	$ext  = array( 'target' => '_blank', 'rel' => 'noopener' );
	if ( $live ) {
		$out .= folio_btn( __( 'Visit live site', 'folio' ), $live, 'primary', 'arrow-up-right', $ext );
	}
	if ( $des ) {
		$out .= folio_btn( __( 'View design', 'folio' ), $des, 'ghost', 'figma', $ext );
	}
	if ( $code ) {
		$out .= folio_btn( __( 'View code', 'folio' ), $code, 'ghost', 'github', $ext );
	}
	return $out;
}

/**
 * Gallery of images (ids, comma separated) with a lightbox link on each.
 *
 * @param string $ids Comma separated attachment ids.
 * @return string
 */
function folio_gallery( $ids ) {
	$ids = array_filter( array_map( 'absint', explode( ',', (string) $ids ) ) );
	if ( ! $ids ) {
		return '';
	}
	$out = '<div class="gallery-grid' . ( 1 === count( $ids ) ? ' is-single' : '' ) . '">';
	foreach ( $ids as $id ) {
		$full = wp_get_attachment_image_url( $id, 'folio-wide' );
		if ( ! $full ) {
			continue;
		}
		$alt  = (string) get_post_meta( $id, '_wp_attachment_image_alt', true );
		$out .= '<a class="gallery-grid__item reveal" href="' . esc_url( $full ) . '" data-lightbox>' . wp_get_attachment_image( $id, 'folio-wide', false, array( 'loading' => 'lazy', 'alt' => $alt, 'sizes' => '(max-width: 900px) 100vw, 1200px' ) ) . '</a>';
	}
	return $out . '</div>';
}

/** The call-to-action band used at the bottom of most pages. */
function folio_cta_band() {
	$email = folio_opt( 'email' );
	?>
	<section class="section section--cta">
		<div class="container">
			<div class="cta-band reveal">
				<div class="cta-band__glow" aria-hidden="true"></div>
				<h2 class="cta-band__title"><?php echo folio_highlight( folio_opt( 'cta_title', __( 'Have a project in mind?', 'folio' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside. ?></h2>
				<p class="cta-band__text"><?php echo esc_html( folio_opt( 'cta_text' ) ); ?></p>
				<div class="btn-row btn-row--center">
					<?php echo folio_btn( __( 'Start a project', 'folio' ), folio_contact_url(), 'light' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside. ?>
					<?php if ( $email ) : ?>
						<a class="btn btn--outline-light" href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>"><?php echo folio_icon( 'mail' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?><span><?php echo esc_html( antispambot( $email ) ); ?></span></a>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</section>
	<?php
}

/** Social icon links. */
function folio_social_links( $class = 'socials' ) {
	$links = folio_socials();
	if ( ! $links ) {
		return '';
	}
	$out = '<ul class="' . esc_attr( $class ) . '">';
	foreach ( $links as $l ) {
		$out .= '<li><a href="' . esc_url( $l[2] ) . '" target="_blank" rel="noopener me" aria-label="' . esc_attr( $l[1] ) . '" title="' . esc_attr( $l[1] ) . '">' . folio_icon( $l[0] ) . '</a></li>';
	}
	return $out . '</ul>';
}

/** Header logo: custom logo, else the owner's name. */
function folio_logo() {
	if ( has_custom_logo() ) {
		the_custom_logo();
		return;
	}
	$name = folio_owner_name();
	echo '<a class="logo" href="' . esc_url( home_url( '/' ) ) . '" rel="home"><span class="logo__mark" aria-hidden="true">' . esc_html( function_exists( 'mb_substr' ) ? mb_substr( $name, 0, 1 ) : substr( $name, 0, 1 ) ) . '</span><span class="logo__name">' . esc_html( $name ) . '</span></a>';
}

/** Menu fallback when no menu is assigned: the main sections that exist. */
function folio_menu_fallback( $args = array() ) {
	$links = array(
		array( __( 'Work', 'folio' ), get_post_type_archive_link( 'project' ) ),
		array( __( 'Case studies', 'folio' ), get_post_type_archive_link( 'case_study' ) ),
		array( __( 'Services', 'folio' ), get_post_type_archive_link( 'service' ) ),
	);
	foreach ( array( 'about' => __( 'About', 'folio' ), 'blog' => __( 'Blog', 'folio' ) ) as $slug => $label ) {
		$p = get_page_by_path( $slug );
		if ( $p ) {
			$links[] = array( $label, get_permalink( $p ) );
		}
	}
	echo '<ul class="' . esc_attr( isset( $args['menu_class'] ) ? $args['menu_class'] : 'menu' ) . '">';
	foreach ( $links as $l ) {
		if ( $l[1] ) {
			echo '<li class="menu-item"><a href="' . esc_url( $l[1] ) . '">' . esc_html( $l[0] ) . '</a></li>';
		}
	}
	echo '</ul>';
}

/** Link of the Contact page: the page using the Contact template, else /contact/. */
function folio_contact_url() {
	static $url = null;
	if ( null === $url ) {
		$pages = get_posts( array( 'post_type' => 'page', 'numberposts' => 1, 'meta_key' => '_wp_page_template', 'meta_value' => 'template-contact.php', 'fields' => 'ids' ) ); // phpcs:ignore WordPress.DB.SlowDBQuery
		$url   = $pages ? get_permalink( $pages[0] ) : home_url( '/contact/' );
	}
	return $url;
}
