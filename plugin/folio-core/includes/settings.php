<?php
/**
 * Portfolio Settings page (top-level admin menu): profile, home page sections, about, contact, social links, look.
 * All values live in one option, "folio_settings".
 *
 * @package folio-core
 */

defined( 'ABSPATH' ) || exit;

/** Tabs of the settings page: key => label. */
function folio_settings_tabs() {
	return array(
		'profile'    => __( 'Profile', 'folio-core' ),
		'home'       => __( 'Home page', 'folio-core' ),
		'about'      => __( 'About & skills', 'folio-core' ),
		'pages'      => __( 'Page headings', 'folio-core' ),
		'contact'    => __( 'Contact', 'folio-core' ),
		'social'     => __( 'Social links', 'folio-core' ),
		'appearance' => __( 'Colours & footer', 'folio-core' ),
	);
}

/** Every setting, with its tab. */
function folio_settings_fields() {
	static $fields = null;
	if ( null !== $fields ) {
		return $fields;
	}
	$fields = array(
		// Profile.
		array( 'tab' => 'profile', 'key' => 'name', 'label' => __( 'Your name', 'folio-core' ), 'type' => 'text', 'default' => '', 'help' => __( 'Shown in the header logo, footer and page titles. Empty = the site title (Settings > General).', 'folio-core' ) ),
		array( 'tab' => 'profile', 'key' => 'role', 'label' => __( 'Your title', 'folio-core' ), 'type' => 'text', 'default' => 'Web Developer & UI/UX Designer' ),
		array( 'tab' => 'profile', 'key' => 'roles', 'label' => __( 'What you do (one per line)', 'folio-core' ), 'type' => 'textarea', 'rows' => 4, 'default' => "Web Developer\nFrontend Developer\nUI/UX Designer\nWeb Designer", 'help' => __( 'Shown as labels next to your photo in the hero.', 'folio-core' ) ),
		array( 'tab' => 'profile', 'key' => 'portrait', 'label' => __( 'Your photo', 'folio-core' ), 'type' => 'image', 'default' => '', 'help' => __( 'Portrait, about 900 x 1100 px. Used in the hero and on the About page.', 'folio-core' ) ),
		array( 'tab' => 'profile', 'key' => 'bio', 'label' => __( 'Short bio (1-2 sentences)', 'folio-core' ), 'type' => 'textarea', 'rows' => 3, 'default' => 'I design and build fast, accessible websites and interfaces for startups, agencies and growing brands.', 'help' => __( 'Used in the footer and as the search-engine description of the home page.', 'folio-core' ) ),
		array( 'tab' => 'profile', 'key' => 'location', 'label' => __( 'Location', 'folio-core' ), 'type' => 'text', 'default' => 'India · Working worldwide' ),
		array( 'tab' => 'profile', 'key' => 'available', 'label' => __( 'Show "available for work" badge', 'folio-core' ), 'type' => 'checkbox', 'default' => 1 ),
		array( 'tab' => 'profile', 'key' => 'available_text', 'label' => __( 'Badge text', 'folio-core' ), 'type' => 'text', 'default' => 'Available for new projects' ),
		array( 'tab' => 'profile', 'key' => 'resume', 'label' => __( 'CV / résumé file (PDF)', 'folio-core' ), 'type' => 'file', 'default' => '', 'help' => __( 'Adds a "Download CV" button on the About page.', 'folio-core' ) ),

		// Home page.
		array( 'tab' => 'home', 'key' => 'hero_title', 'label' => __( 'Hero headline', 'folio-core' ), 'type' => 'text', 'wide' => true, 'default' => 'I design & build *websites* that look sharp and work hard.', 'help' => __( 'Put <code>*stars*</code> around words to colour them.', 'folio-core' ) ),
		array( 'tab' => 'home', 'key' => 'hero_text', 'label' => __( 'Hero text', 'folio-core' ), 'type' => 'textarea', 'rows' => 3, 'default' => 'Web developer, frontend developer and UI/UX designer. I turn ideas into clean interfaces and fast, accessible websites that people enjoy using.' ),
		array( 'tab' => 'home', 'key' => 'cta1_label', 'label' => __( 'Main button text', 'folio-core' ), 'type' => 'text', 'default' => 'View my work' ),
		array( 'tab' => 'home', 'key' => 'cta1_url', 'label' => __( 'Main button link', 'folio-core' ), 'type' => 'url', 'default' => '/work/' ),
		array( 'tab' => 'home', 'key' => 'cta2_label', 'label' => __( 'Second button text', 'folio-core' ), 'type' => 'text', 'default' => "Let's talk" ),
		array( 'tab' => 'home', 'key' => 'cta2_url', 'label' => __( 'Second button link', 'folio-core' ), 'type' => 'url', 'default' => '/contact/' ),
		array(
			'tab'       => 'home',
			'key'       => 'stats',
			'label'     => __( 'Numbers under the hero', 'folio-core' ),
			'type'      => 'repeater',
			'add_label' => __( 'Add number', 'folio-core' ),
			'sub'       => array(
				array( 'key' => 'value', 'label' => __( 'Number', 'folio-core' ), 'type' => 'text', 'placeholder' => '50+' ),
				array( 'key' => 'label', 'label' => __( 'Label', 'folio-core' ), 'type' => 'text', 'placeholder' => 'Projects delivered' ),
			),
			'default'   => array(
				array( 'value' => '6+', 'label' => 'Years of experience' ),
				array( 'value' => '80+', 'label' => 'Projects delivered' ),
				array( 'value' => '40+', 'label' => 'Happy clients' ),
				array( 'value' => '98', 'label' => 'Avg. Lighthouse score' ),
			),
			'help'      => __( 'Write only numbers you can stand behind. 4 look best.', 'folio-core' ),
		),
		array( 'tab' => 'home', 'key' => 'marquee', 'label' => __( 'Tools strip (one per line)', 'folio-core' ), 'type' => 'textarea', 'rows' => 4, 'default' => "Figma\nHTML5\nCSS3\nJavaScript\nTypeScript\nReact\nNext.js\nTailwind CSS\nWordPress\nGSAP\nShopify\nWebflow", 'help' => __( 'The moving strip of tool names under the hero. Empty = hidden.', 'folio-core' ) ),
		array( 'tab' => 'home', 'key' => 'clients', 'label' => __( 'Client logos (optional)', 'folio-core' ), 'type' => 'gallery', 'default' => '', 'help' => __( 'Logos with transparent background (PNG/SVG) look best. Empty = hidden.', 'folio-core' ) ),
		array( 'tab' => 'home', 'key' => 'show_services', 'label' => __( 'Show "Services" section', 'folio-core' ), 'type' => 'checkbox', 'default' => 1 ),
		array( 'tab' => 'home', 'key' => 'services_title', 'label' => __( 'Services title', 'folio-core' ), 'type' => 'text', 'default' => 'What I can do *for you*' ),
		array( 'tab' => 'home', 'key' => 'services_text', 'label' => __( 'Services intro', 'folio-core' ), 'type' => 'text', 'wide' => true, 'default' => 'From the first wireframe to the final line of code: one person who cares about both the design and how it is built.' ),
		array( 'tab' => 'home', 'key' => 'show_work', 'label' => __( 'Show "Selected work" section', 'folio-core' ), 'type' => 'checkbox', 'default' => 1 ),
		array( 'tab' => 'home', 'key' => 'work_title', 'label' => __( 'Selected work title', 'folio-core' ), 'type' => 'text', 'default' => 'Selected *work*' ),
		array( 'tab' => 'home', 'key' => 'work_text', 'label' => __( 'Selected work intro', 'folio-core' ), 'type' => 'text', 'wide' => true, 'default' => 'A few recent projects. Tick "Show on the home page" on a project to list it here.' ),
		array( 'tab' => 'home', 'key' => 'show_cases', 'label' => __( 'Show "Case studies" section', 'folio-core' ), 'type' => 'checkbox', 'default' => 1 ),
		array( 'tab' => 'home', 'key' => 'cases_title', 'label' => __( 'Case studies title', 'folio-core' ), 'type' => 'text', 'default' => 'Case *studies*' ),
		array( 'tab' => 'home', 'key' => 'cases_text', 'label' => __( 'Case studies intro', 'folio-core' ), 'type' => 'text', 'wide' => true, 'default' => 'How I think, not just what I make: the problem, the process and the numbers after launch.' ),
		array( 'tab' => 'home', 'key' => 'show_about', 'label' => __( 'Show "About" section (text and title from the About & skills tab)', 'folio-core' ), 'type' => 'checkbox', 'default' => 1 ),
		array( 'tab' => 'home', 'key' => 'show_process', 'label' => __( 'Show "How I work" section', 'folio-core' ), 'type' => 'checkbox', 'default' => 1 ),
		array( 'tab' => 'home', 'key' => 'process_title', 'label' => __( 'How I work title', 'folio-core' ), 'type' => 'text', 'default' => 'How I *work*' ),
		array(
			'tab'       => 'home',
			'key'       => 'process',
			'label'     => __( 'Process steps', 'folio-core' ),
			'type'      => 'repeater',
			'add_label' => __( 'Add step', 'folio-core' ),
			'sub'       => array(
				array( 'key' => 'title', 'label' => __( 'Step', 'folio-core' ), 'type' => 'text' ),
				array( 'key' => 'text', 'label' => __( 'What happens', 'folio-core' ), 'type' => 'textarea' ),
			),
			'default'   => array(
				array( 'title' => 'Discover', 'text' => 'A short call to understand your goals, users and deadline. You get a clear scope and a fixed quote.' ),
				array( 'title' => 'Design', 'text' => 'Wireframes first, then a polished UI in Figma. You review a clickable prototype before any code is written.' ),
				array( 'title' => 'Build', 'text' => 'Clean, responsive, accessible code. Fast pages, tested on real phones and browsers.' ),
				array( 'title' => 'Launch & support', 'text' => 'Go live, SEO basics, analytics and a hand-over. Then support whenever you need changes.' ),
			),
		),
		array( 'tab' => 'home', 'key' => 'show_testimonials', 'label' => __( 'Show "Testimonials" section', 'folio-core' ), 'type' => 'checkbox', 'default' => 1 ),
		array( 'tab' => 'home', 'key' => 'testimonials_title', 'label' => __( 'Testimonials title', 'folio-core' ), 'type' => 'text', 'default' => 'Kind words from *clients*' ),
		array( 'tab' => 'home', 'key' => 'show_blog', 'label' => __( 'Show latest blog posts', 'folio-core' ), 'type' => 'checkbox', 'default' => 1 ),
		array( 'tab' => 'home', 'key' => 'blog_title', 'label' => __( 'Blog title', 'folio-core' ), 'type' => 'text', 'default' => 'Latest *articles*' ),
		array( 'tab' => 'home', 'key' => 'cta_title', 'label' => __( 'Bottom call-to-action title', 'folio-core' ), 'type' => 'text', 'default' => 'Have a project in mind? *Let\'s build it.*' ),
		array( 'tab' => 'home', 'key' => 'cta_text', 'label' => __( 'Bottom call-to-action text', 'folio-core' ), 'type' => 'text', 'wide' => true, 'default' => 'Tell me a little about it. I reply within one working day.' ),

		// About.
		array( 'tab' => 'about', 'key' => 'about_title', 'label' => __( 'About page headline', 'folio-core' ), 'type' => 'text', 'default' => 'Designer who codes. *Developer who designs.*' ),
		array( 'tab' => 'about', 'key' => 'about_text', 'label' => __( 'About text', 'folio-core' ), 'type' => 'textarea', 'rows' => 8, 'default' => "I'm a web developer and UI/UX designer who loves the space where design meets code. For the last few years I've helped startups, agencies and online stores turn rough ideas into websites and products that are clear, fast and pleasant to use.\n\nI start with the people who will use what we build: what they need, what confuses them and what makes them act. Then I design it, build it with clean, modern code, and measure whether it works.", 'help' => __( 'Shown on the About page (page template "About") and in the About section of the home page. A blank line starts a new paragraph.', 'folio-core' ) ),
		array(
			'tab'       => 'about',
			'key'       => 'skills',
			'label'     => __( 'Skills (groups)', 'folio-core' ),
			'type'      => 'repeater',
			'add_label' => __( 'Add skill group', 'folio-core' ),
			'sub'       => array(
				array( 'key' => 'group', 'label' => __( 'Group', 'folio-core' ), 'type' => 'text', 'placeholder' => 'Design' ),
				array( 'key' => 'items', 'label' => __( 'Skills, separated by commas', 'folio-core' ), 'type' => 'textarea', 'placeholder' => 'Figma, Prototyping, Design systems' ),
			),
			'default'   => array(
				array( 'group' => 'UI/UX Design', 'items' => 'User research, Wireframing, Prototyping, Design systems, Usability testing, Figma' ),
				array( 'group' => 'Frontend', 'items' => 'HTML, CSS / Sass, JavaScript, TypeScript, React, Next.js, Tailwind CSS, GSAP' ),
				array( 'group' => 'Web Development', 'items' => 'WordPress (custom themes & plugins), Shopify, PHP, REST APIs, Git' ),
				array( 'group' => 'Quality', 'items' => 'Accessibility (WCAG), Core Web Vitals, SEO basics, Cross-browser testing' ),
			),
		),
		array( 'tab' => 'about', 'key' => 'show_experience', 'label' => __( 'Show the Experience timeline (from Experience items)', 'folio-core' ), 'type' => 'checkbox', 'default' => 1 ),

		// Headings of the list pages (Work, Case studies, Services, Blog).
		array( 'tab' => 'pages', 'key' => 'work_page_title', 'label' => __( 'Work page (/work/) headline', 'folio-core' ), 'type' => 'text', 'default' => 'Projects I\'m *proud of*' ),
		array( 'tab' => 'pages', 'key' => 'work_page_text', 'label' => __( 'Work page intro', 'folio-core' ), 'type' => 'textarea', 'rows' => 2, 'default' => 'Websites, web apps and interfaces I have designed and built. Filter by type to find what you are looking for.' ),
		array( 'tab' => 'pages', 'key' => 'cases_page_title', 'label' => __( 'Case studies page (/case-studies/) headline', 'folio-core' ), 'type' => 'text', 'default' => 'Case *studies*' ),
		array( 'tab' => 'pages', 'key' => 'cases_page_text', 'label' => __( 'Case studies page intro', 'folio-core' ), 'type' => 'textarea', 'rows' => 2, 'default' => 'Deep dives into selected projects: the problem, the research, the design decisions and the results after launch.' ),
		array( 'tab' => 'pages', 'key' => 'services_page_title', 'label' => __( 'Services page (/services/) headline', 'folio-core' ), 'type' => 'text', 'default' => 'Services built around *your goals*' ),
		array( 'tab' => 'pages', 'key' => 'services_page_text', 'label' => __( 'Services page intro', 'folio-core' ), 'type' => 'textarea', 'rows' => 2, 'default' => 'Design, development or both. Pick one service or let me take your project from idea to launch.' ),
		array( 'tab' => 'pages', 'key' => 'blog_page_title', 'label' => __( 'Blog page headline', 'folio-core' ), 'type' => 'text', 'default' => 'Notes on *design & code*' ),
		array( 'tab' => 'pages', 'key' => 'blog_page_text', 'label' => __( 'Blog page intro', 'folio-core' ), 'type' => 'textarea', 'rows' => 2, 'default' => 'Practical articles about UI/UX, frontend development, WordPress and building better websites.' ),

		// Contact.
		array( 'tab' => 'contact', 'key' => 'email', 'label' => __( 'Public email', 'folio-core' ), 'type' => 'email', 'default' => '', 'help' => __( 'Shown on the Contact page and in the footer.', 'folio-core' ) ),
		array( 'tab' => 'contact', 'key' => 'phone', 'label' => __( 'Phone (optional)', 'folio-core' ), 'type' => 'text', 'default' => '' ),
		array( 'tab' => 'contact', 'key' => 'booking_url', 'label' => __( 'Book-a-call link (optional)', 'folio-core' ), 'type' => 'url', 'default' => '', 'help' => __( 'Calendly, Cal.com or Google booking page. Adds a "Book a call" button on the Contact page.', 'folio-core' ) ),
		array( 'tab' => 'contact', 'key' => 'contact_title', 'label' => __( 'Contact page headline', 'folio-core' ), 'type' => 'text', 'default' => "Let's work *together*" ),
		array( 'tab' => 'contact', 'key' => 'contact_text', 'label' => __( 'Contact page text', 'folio-core' ), 'type' => 'textarea', 'rows' => 3, 'default' => 'Tell me about your project, timeline and budget. I reply within one working day with next steps or a few questions.' ),
		array( 'tab' => 'contact', 'key' => 'budgets', 'label' => __( 'Budget choices in the form (one per line)', 'folio-core' ), 'type' => 'textarea', 'rows' => 4, 'default' => "Under $1,000\n$1,000 - $3,000\n$3,000 - $7,500\n$7,500+\nNot sure yet", 'help' => __( 'Empty = no budget question.', 'folio-core' ) ),
		array( 'tab' => 'contact', 'key' => 'notify_email', 'label' => __( 'Send new messages to', 'folio-core' ), 'type' => 'email', 'default' => '', 'help' => __( 'Every message is saved under Messages anyway. Empty = the admin email (Settings > General). Emails need working mail on the hosting (or an SMTP plugin).', 'folio-core' ) ),
		array( 'tab' => 'contact', 'key' => 'form_success', 'label' => __( 'Message after sending', 'folio-core' ), 'type' => 'text', 'wide' => true, 'default' => 'Thanks! Your message is on its way. I will get back to you within one working day.' ),
	);

	foreach ( folio_social_networks() as $key => $label ) {
		$fields[] = array( 'tab' => 'social', 'key' => 'social_' . $key, 'label' => $label, 'type' => 'url', 'default' => '', 'placeholder' => 'whatsapp' === $key ? 'https://wa.me/91XXXXXXXXXX' : 'https://' );
	}

	$fields = array_merge(
		$fields,
		array(
			array( 'tab' => 'appearance', 'key' => 'accent', 'label' => __( 'Accent colour', 'folio-core' ), 'type' => 'color', 'default' => '#6c4cf5', 'help' => __( 'Buttons, links, highlighted words.', 'folio-core' ) ),
			array( 'tab' => 'appearance', 'key' => 'accent2', 'label' => __( 'Second colour (gradients)', 'folio-core' ), 'type' => 'color', 'default' => '#ff6a3d' ),
			array(
				'tab'     => 'appearance',
				'key'     => 'mode',
				'label'   => __( 'Light / dark', 'folio-core' ),
				'type'    => 'select',
				'options' => array(
					'system' => __( 'Follow the visitor\'s device', 'folio-core' ),
					'light'  => __( 'Always start light', 'folio-core' ),
					'dark'   => __( 'Always start dark', 'folio-core' ),
				),
				'default' => 'system',
				'help'    => __( 'Visitors can still switch with the sun/moon button in the header.', 'folio-core' ),
			),
			array( 'tab' => 'appearance', 'key' => 'footer_text', 'label' => __( 'Footer line', 'folio-core' ), 'type' => 'text', 'wide' => true, 'default' => 'Designed & built with care.' ),
		)
	);
	return $fields;
}

/**
 * One setting definition.
 *
 * @param string $key Key.
 * @return array|null
 */
function folio_settings_field( $key ) {
	static $by_key = null;
	if ( null === $by_key ) {
		$by_key = array();
		foreach ( folio_settings_fields() as $f ) {
			$by_key[ $f['key'] ] = $f;
		}
	}
	return isset( $by_key[ $key ] ) ? $by_key[ $key ] : null;
}

add_action(
	'admin_init',
	function () {
		register_setting(
			'folio_settings',
			'folio_settings',
			array(
				'type'              => 'array',
				'sanitize_callback' => 'folio_sanitize_settings',
				'default'           => array(),
			)
		);
	}
);

/**
 * Cleans the whole settings array.
 *
 * @param mixed $input Posted values.
 * @return array
 */
function folio_sanitize_settings( $input ) {
	$input = is_array( $input ) ? $input : array();
	$out   = array();
	foreach ( folio_settings_fields() as $f ) {
		$raw               = isset( $input[ $f['key'] ] ) ? $input[ $f['key'] ] : ( 'repeater' === $f['type'] ? array() : '' );
		$out[ $f['key'] ] = folio_clean_field( $f, $raw );
	}
	return $out;
}

add_action(
	'admin_menu',
	function () {
		add_menu_page(
			__( 'Portfolio Settings', 'folio-core' ),
			__( 'Portfolio', 'folio-core' ),
			'manage_options',
			'folio-settings',
			'folio_render_settings_page',
			'dashicons-art',
			3
		);
	}
);

/** The settings page. */
function folio_render_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$tabs = folio_settings_tabs();
	?>
	<div class="wrap folio-settings">
		<h1><?php esc_html_e( 'Portfolio Settings', 'folio-core' ); ?></h1>
		<p class="folio-settings__lead">
			<?php esc_html_e( 'Everything on the home, about and contact pages that is not a project, case study, service, testimonial or experience item is set here.', 'folio-core' ); ?>
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'View site', 'folio-core' ); ?> &rarr;</a>
		</p>
		<?php settings_errors(); ?>
		<nav class="nav-tab-wrapper folio-tabs" role="tablist">
			<?php $first = true; foreach ( $tabs as $key => $label ) : // phpcs:ignore Squiz.ControlStructures.ControlSignature.NewlineAfterOpenBrace ?>
				<a href="#tab-<?php echo esc_attr( $key ); ?>" class="nav-tab<?php echo $first ? ' nav-tab-active' : ''; ?>" role="tab" data-tab="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></a>
				<?php $first = false; endforeach; // phpcs:ignore Generic.WhiteSpace.ScopeIndent.Incorrect ?>
		</nav>
		<form method="post" action="options.php">
			<?php settings_fields( 'folio_settings' ); ?>
			<?php $first = true; foreach ( $tabs as $key => $label ) : // phpcs:ignore Squiz.ControlStructures.ControlSignature.NewlineAfterOpenBrace ?>
				<section class="folio-tab-panel" id="tab-<?php echo esc_attr( $key ); ?>" data-panel="<?php echo esc_attr( $key ); ?>"<?php echo $first ? '' : ' hidden'; ?>>
					<div class="folio-box"><div class="folio-grid">
					<?php
					foreach ( folio_settings_fields() as $f ) {
						if ( $f['tab'] === $key ) {
							folio_render_field( $f, 'folio_settings[' . $f['key'] . ']', folio_opt( $f['key'] ) );
						}
					}
					?>
					</div></div>
				</section>
				<?php $first = false; endforeach; // phpcs:ignore Generic.WhiteSpace.ScopeIndent.Incorrect ?>
			<?php submit_button( __( 'Save settings', 'folio-core' ) ); ?>
		</form>
	</div>
	<?php
}
