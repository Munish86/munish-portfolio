<?php
/**
 * Fills a fresh install: pages (Home, About, Contact, Blog, Privacy Policy), menus, front/posts page, and sample
 * projects, case studies, services, testimonials, experience items and blog posts with images from content/images.
 * Every sample item and image is marked "_folio_sample" so Dashboard > "Delete sample content" removes them all.
 *
 * Usage (Git Bash, from the project folder):
 *   tools/wp.sh eval-file tools/setup-content.php --user=folioadmin          # adds what is missing
 *   tools/wp.sh eval-file tools/setup-content.php force --user=folioadmin    # deletes samples first, then adds again
 *
 * @package folio
 */

defined( 'ABSPATH' ) || exit;
require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';

$force  = in_array( 'force', isset( $args ) ? (array) $args : array(), true );
define( 'FOLIO_SETUP_IMAGES', dirname( __DIR__ ) . '/content/images' );

if ( $force ) {
	$old = get_posts( array( 'post_type' => array( 'project', 'case_study', 'service', 'testimonial', 'experience', 'post', 'attachment' ), 'post_status' => 'any', 'numberposts' => -1, 'meta_key' => '_folio_sample', 'fields' => 'ids' ) ); // phpcs:ignore WordPress.DB.SlowDBQuery
	foreach ( $old as $id ) {
		'attachment' === get_post_type( $id ) ? wp_delete_attachment( $id, true ) : wp_delete_post( $id, true );
	}
	WP_CLI::log( 'Deleted ' . count( $old ) . ' sample items.' );
}

/** Uploads an image from content/images once (by file name) and returns its id. */
function folio_setup_image( $file, $alt ) {
	$existing = get_posts( array( 'post_type' => 'attachment', 'numberposts' => 1, 'meta_key' => '_folio_sample_file', 'meta_value' => $file, 'fields' => 'ids' ) ); // phpcs:ignore WordPress.DB.SlowDBQuery
	if ( $existing ) {
		return (int) $existing[0];
	}
	$tmp = wp_tempnam( $file );
	copy( FOLIO_SETUP_IMAGES . "/$file", $tmp );
	$id = media_handle_sideload( array( 'name' => $file, 'tmp_name' => $tmp ), 0, $alt );
	if ( is_wp_error( $id ) ) {
		WP_CLI::warning( "$file: " . $id->get_error_message() );
		return 0;
	}
	update_post_meta( $id, '_wp_attachment_image_alt', $alt );
	update_post_meta( $id, '_folio_sample', 1 );
	update_post_meta( $id, '_folio_sample_file', $file );
	return (int) $id;
}

/** Creates a post once (by type + title). Returns its id. */
function folio_setup_post( $type, $title, $data, $meta = array(), $terms = array(), $sample = true ) {
	$existing = get_posts( array( 'post_type' => $type, 'title' => $title, 'post_status' => 'any', 'numberposts' => 1, 'fields' => 'ids' ) );
	if ( $existing ) {
		return (int) $existing[0];
	}
	$id = wp_insert_post(
		array_merge(
			array(
				'post_type'   => $type,
				'post_title'  => $title,
				'post_status' => 'publish',
			),
			$data
		),
		true
	);
	if ( is_wp_error( $id ) ) {
		WP_CLI::warning( "$title: " . $id->get_error_message() );
		return 0;
	}
	foreach ( $meta as $k => $v ) {
		update_post_meta( $id, '_folio_' . $k, $v );
	}
	foreach ( $terms as $tax => $names ) {
		wp_set_object_terms( $id, $names, $tax );
	}
	if ( $sample ) {
		update_post_meta( $id, '_folio_sample', 1 );
	}
	WP_CLI::log( "Created $type: $title" );
	return (int) $id;
}

/** Paragraph / heading / list blocks from a compact list. */
function folio_blocks( $items ) {
	$out = '';
	foreach ( $items as $item ) {
		if ( is_array( $item ) ) {
			$out .= "<!-- wp:list -->\n<ul class=\"wp-block-list\">";
			foreach ( $item as $li ) {
				$out .= "<!-- wp:list-item -->\n<li>" . $li . "</li>\n<!-- /wp:list-item -->";
			}
			$out .= "</ul>\n<!-- /wp:list -->\n\n";
		} elseif ( 0 === strpos( $item, '## ' ) ) {
			$out .= "<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">" . substr( $item, 3 ) . "</h2>\n<!-- /wp:heading -->\n\n";
		} elseif ( 0 === strpos( $item, '> ' ) ) {
			$out .= "<!-- wp:quote -->\n<blockquote class=\"wp-block-quote\"><!-- wp:paragraph -->\n<p>" . substr( $item, 2 ) . "</p>\n<!-- /wp:paragraph --></blockquote>\n<!-- /wp:quote -->\n\n";
		} else {
			$out .= "<!-- wp:paragraph -->\n<p>" . $item . "</p>\n<!-- /wp:paragraph -->\n\n";
		}
	}
	return trim( $out );
}

/* ---------- Terms ---------- */
foreach ( array( 'Web Design', 'Frontend Development', 'UI/UX Design', 'WordPress' ) as $t ) {
	if ( ! term_exists( $t, 'work_type' ) ) {
		wp_insert_term( $t, 'work_type' );
	}
}

/* ---------- Services ---------- */
$services = array(
	array( 'UI/UX Design', 'pen', 'Research-led interfaces that are easy to use and lovely to look at, from user flows to clickable prototypes.', "User research & personas\nUser flows & wireframes\nHigh-fidelity UI in Figma\nClickable prototypes\nUsability testing", 'From $1,200' ),
	array( 'Web Design', 'layout', 'Modern, on-brand website designs that tell your story clearly and turn visitors into customers.', "Landing pages & full websites\nResponsive layouts for every screen\nDesign systems & style guides\nCopy & content structure", 'From $900' ),
	array( 'Frontend Development', 'code', 'Pixel-perfect, accessible and fast frontends built with modern HTML, CSS, JavaScript and React.', "HTML, CSS, JavaScript, TypeScript\nReact & Next.js\nAnimations with GSAP\nAccessibility (WCAG 2.2)\nCore Web Vitals tuning", 'From $1,500' ),
	array( 'WordPress Development', 'globe', 'Custom WordPress themes and plugins you can edit yourself, without heavy page builders.', "Custom block themes\nCustom post types & fields\nWooCommerce stores\nMigration & hosting setup", 'From $1,000' ),
	array( 'Speed & SEO Optimisation', 'zap', 'Make an existing site faster and easier to find, with measurable before-and-after results.', "Lighthouse & Core Web Vitals audit\nImage, font and script optimisation\nTechnical SEO fixes\nSchema markup", 'From $400' ),
	array( 'Website Care', 'wrench', 'Updates, backups, small changes and peace of mind, every month.', "Plugin & core updates\nDaily backups & uptime monitoring\nSmall content changes\nMonthly report", 'From $79 / month' ),
);
foreach ( $services as $i => $s ) {
	folio_setup_post(
		'service',
		$s[0],
		array(
			'post_excerpt' => $s[2],
			'menu_order'   => $i,
			'post_content' => folio_blocks(
				array(
					$s[2],
					'## How it works',
					'We start with a short call about your goals and audience. You get a clear plan, a fixed price and a timeline before any work begins. During the project you see progress every week and can give feedback at each step.',
					'## Who it is for',
					'Startups, small businesses, agencies that need an extra pair of hands, and online stores that want to grow.',
				)
			),
		),
		array( 'icon' => $s[1], 'deliverables' => $s[3], 'price' => $s[4] )
	);
}

/* ---------- Case studies ---------- */
$case_nw = folio_setup_post(
	'case_study',
	'Redesigning checkout for Northwind Coffee',
	array(
		'post_excerpt' => 'A full e-commerce redesign for a specialty coffee roaster: a simpler checkout, faster pages and subscriptions that customers actually understand.',
		'menu_order'   => 0,
		'post_content' => folio_blocks(
			array(
				'## Research',
				'I started with the data: analytics funnels, 12 session recordings and short interviews with 6 regular customers. Three problems stood out: shipping costs appeared only at the last step, the subscription options were confusing, and product pages took over 4 seconds to load on mobile.',
				'## Design',
				'Wireframes explored three checkout flows. We tested the two strongest as clickable Figma prototypes with 8 people. The winner showed shipping early, used a single-page checkout and explained subscriptions with a simple "deliver every 2 / 4 weeks" choice.',
				array( 'One-page checkout with address autocomplete', 'Shipping cost and delivery date shown on the product page', 'Subscription picker with savings shown upfront', 'New product cards with roast level and tasting notes' ),
				'## Build',
				'The new theme was built as a lightweight custom WooCommerce theme. Images were converted to modern formats with responsive sizes, fonts were self-hosted and third-party scripts were cut from 14 to 4.',
				'> Every decision was tested with real customers before a single line of production code was written.',
			)
		),
	),
	array(
		'client'       => 'Northwind Coffee',
		'industry'     => 'E-commerce, food & drink',
		'role'         => 'UX research, UI design, WordPress development',
		'timeline'     => '10 weeks',
		'team'         => 'Me + the founder + 1 copywriter',
		'deliverables' => 'Research report, prototypes, design system, WooCommerce theme',
		'live_url'     => 'https://example.com',
		'challenge'    => "Northwind's online sales had stalled. Most visitors added coffee to the cart, but 7 in 10 left at checkout. The site was slow on phones and the subscription offer, their most profitable product, was hard to understand.\n\nThey needed a store that felt as warm as their café and made buying (and re-buying) effortless.",
		'solution'     => "A research-led redesign of the whole buying journey: clear product pages, shipping costs shown early, a one-page checkout and a simple subscription picker.\n\nThe store was rebuilt as a fast custom WooCommerce theme the team can edit themselves.",
		'results'      => array(
			array( 'value' => '+38%', 'label' => 'Conversion rate' ),
			array( 'value' => '-52%', 'label' => 'Page load time' ),
			array( 'value' => '+24%', 'label' => 'Average order value' ),
			array( 'value' => '2.1x', 'label' => 'Subscription sign-ups' ),
		),
		'quote'        => 'Our checkout used to be where customers gave up. Now it is the easiest part of the site. The numbers speak for themselves.',
		'quote_name'   => 'Daniel Brooks',
		'quote_role'   => 'Founder, Northwind Coffee',
		'accent'       => '#1F3D2B',
		'featured'     => 1,
	),
	array( 'work_type' => array( 'Web Design', 'UI/UX Design', 'WordPress' ), 'tool' => array( 'Figma', 'WordPress', 'WooCommerce', 'PHP', 'JavaScript' ) )
);
$case_pulse = folio_setup_post(
	'case_study',
	'Pulse Analytics: a dashboard people actually understand',
	array(
		'post_excerpt' => 'Turning a crowded SaaS dashboard into a calm, focused workspace, with a design system that the product team now uses for every new feature.',
		'menu_order'   => 1,
		'post_content' => folio_blocks(
			array(
				'## Understanding the users',
				'Interviews with 10 customers showed that most people opened Pulse for three numbers, but had to scan 24 widgets to find them. Support tickets confirmed it: "Where do I find..." was the most common question.',
				'## A calmer layout',
				'I rebuilt the information architecture around jobs-to-be-done. The overview now shows four key numbers with their trend, one main chart and a short activity list. Everything else lives one click away in focused report pages.',
				array( 'Customisable KPI cards with plain-language labels', 'Dark and light themes with accessible contrast', 'Empty states that teach instead of confuse', 'Keyboard shortcuts for power users' ),
				'## Design system',
				'Every component was documented in Figma with variants and tokens, then built in React with Storybook. New screens now take days instead of weeks.',
			)
		),
	),
	array(
		'client'       => 'Pulse Analytics',
		'industry'     => 'SaaS, B2B analytics',
		'role'         => 'Lead product designer, frontend',
		'timeline'     => '4 months',
		'team'         => '1 designer (me), 3 developers, 1 PM',
		'deliverables' => 'UX audit, new IA, design system, React components',
		'challenge'    => "Pulse had powerful features, but new customers felt lost. Trials were not converting and the support team spent most of its time explaining the dashboard.\n\nThe interface had grown feature by feature without a system, so every screen looked a little different.",
		'solution'     => "A new information architecture built around what users actually do, a calmer overview page and a complete design system shared by design and development.\n\nWe shipped in three releases so customers could give feedback along the way.",
		'results'      => array(
			array( 'value' => '-41%', 'label' => 'Support tickets' ),
			array( 'value' => '2.3x', 'label' => 'Feature adoption' ),
			array( 'value' => '+18%', 'label' => 'Trial to paid' ),
			array( 'value' => '86', 'label' => 'Usability score (SUS)' ),
		),
		'quote'        => 'The new dashboard finally matches how our customers think. And the design system has changed how our whole team ships.',
		'quote_name'   => 'Sara Lindqvist',
		'quote_role'   => 'Head of Product, Pulse Analytics',
		'accent'       => '#16112E',
		'featured'     => 1,
	),
	array( 'work_type' => array( 'UI/UX Design', 'Frontend Development' ), 'tool' => array( 'Figma', 'React', 'TypeScript', 'Storybook' ) )
);

$covers = array(
	$case_nw    => 'case-northwind.png',
	$case_pulse => 'case-pulse.png',
);
foreach ( $covers as $id => $file ) {
	if ( $id && ! has_post_thumbnail( $id ) ) {
		set_post_thumbnail( $id, folio_setup_image( $file, get_the_title( $id ) ) );
	}
}

/* ---------- Projects ---------- */
$projects = array(
	array( 'northwind', 'Northwind Coffee', 'E-commerce store for a specialty coffee roaster, with subscriptions and a one-page checkout.', array( 'Web Design', 'WordPress' ), array( 'Figma', 'WordPress', 'WooCommerce', 'PHP' ), 'Northwind Coffee', 'UI/UX design, WordPress development', '2026', '10 weeks', '#1F3D2B', $case_nw ),
	array( 'pulse', 'Pulse Analytics', 'SaaS analytics dashboard and design system, redesigned around what users really need.', array( 'UI/UX Design', 'Frontend Development' ), array( 'Figma', 'React', 'TypeScript', 'Storybook' ), 'Pulse Analytics', 'Product design, frontend', '2026', '4 months', '#16112E', $case_pulse ),
	array( 'atlas', 'Atlas Travel', 'Trip-planning web app with smart search, flexible dates and a fast, friendly booking flow.', array( 'Frontend Development' ), array( 'Next.js', 'React', 'Tailwind CSS', 'GSAP' ), 'Atlas Travel Co.', 'Frontend development', '2025', '8 weeks', '#0B4F6C', 0 ),
	array( 'bloom', 'Bloom Studio', 'Bold, animated website for a creative studio, built to show off their work.', array( 'Web Design' ), array( 'Figma', 'Webflow', 'GSAP' ), 'Bloom Studio', 'Web design, interaction design', '2025', '5 weeks', '#FF6A3D', 0 ),
	array( 'fittrack', 'FitTrack App', 'Mobile fitness app UI: daily goals, workouts and progress that keep people moving.', array( 'UI/UX Design' ), array( 'Figma', 'Prototyping', 'User testing' ), 'FitTrack', 'UI/UX design', '2025', '6 weeks', '#111827', 0 ),
	array( 'kite', 'Kite Legal', 'Trustworthy WordPress website for a law firm that the team updates themselves.', array( 'WordPress', 'Web Design' ), array( 'WordPress', 'PHP', 'Block themes' ), 'Kite Legal LLP', 'Web design, WordPress development', '2024', '4 weeks', '#E9E2D6', 0 ),
);
foreach ( $projects as $i => $p ) {
	list( $key, $title, $excerpt, $types, $tools, $client, $role, $year, $duration, $accent, $case ) = $p;
	$id = folio_setup_post(
		'project',
		$title,
		array(
			'post_excerpt' => $excerpt,
			'menu_order'   => $i,
			'post_content' => folio_blocks(
				array(
					'## Overview',
					$excerpt . ' The goal was a product that looks great, loads fast and is simple to use on any device.',
					'## What I did',
					array( 'Discovery workshop and competitor review', 'Wireframes, then high-fidelity designs in Figma', 'Responsive, accessible build', 'Launch, analytics and hand-over' ),
					'## The result',
					'A clean, consistent experience the client is proud to share, with a Lighthouse performance score above 95 and a content structure their team can manage on their own.',
				)
			),
		),
		array(
			'client'   => $client,
			'role'     => $role,
			'year'     => $year,
			'duration' => $duration,
			'live_url' => 'https://example.com',
			'accent'   => $accent,
			'featured' => 1,
		),
		array( 'work_type' => $types, 'tool' => $tools )
	);
	if ( $case ) {
		update_post_meta( $id, '_folio_case_study', $case );
	}
	if ( $id && ! has_post_thumbnail( $id ) ) {
		set_post_thumbnail( $id, folio_setup_image( "project-$key.png", "$title website design" ) );
		$gallery = array( folio_setup_image( "project-$key-screens.png", "$title screens" ), folio_setup_image( "project-$key-system.png", "$title design system" ) );
		update_post_meta( $id, '_folio_gallery', implode( ',', array_filter( $gallery ) ) );
		if ( 'northwind' === $key || 'pulse' === $key ) {
			$case_id = 'northwind' === $key ? $case_nw : $case_pulse;
			if ( ! get_post_meta( $case_id, '_folio_gallery', true ) ) {
				update_post_meta( $case_id, '_folio_gallery', implode( ',', array_filter( $gallery ) ) );
			}
		}
	}
}

/* ---------- Testimonials ---------- */
$quotes = array(
	array( 'Daniel Brooks', 'Founder', 'Northwind Coffee', 'Working together felt easy from day one. Clear plan, weekly updates and a store that our customers love. Sales are up and our team can finally update the site ourselves.' ),
	array( 'Sara Lindqvist', 'Head of Product', 'Pulse Analytics', 'A rare mix of a sharp designer and a careful developer. The design system alone saved us months of work, and our customers noticed the difference right away.' ),
	array( 'Marco Rossi', 'Creative Director', 'Bloom Studio', 'Pixel-perfect, fast and full of great ideas. The animations are smooth on every device and the site gets compliments from every new client we pitch.' ),
);
foreach ( $quotes as $i => $q ) {
	folio_setup_post( 'testimonial', $q[0], array( 'post_content' => $q[3], 'menu_order' => $i ), array( 'role' => $q[1], 'company' => $q[2], 'rating' => '5' ) );
}

/* ---------- Experience ---------- */
$exp = array(
	array( 'Freelance Web Developer & UI/UX Designer', 'freelance', 'Self-employed', 'Remote', '2023', '', 'Designing and building websites, web apps and online stores for clients in India, Europe and the US. From discovery and UX research to launch and ongoing care.' ),
	array( 'Senior Frontend Developer', 'work', 'Pixel Forge Studio', 'Bengaluru', '2021', '2023', 'Led frontend for 20+ client projects. Built a shared React component library and introduced accessibility and performance checks into every release.' ),
	array( 'Web Designer & Developer', 'work', 'Brightside Digital Agency', 'Pune', '2019', '2021', 'Designed and built WordPress websites and landing pages for small businesses. Created the agency\'s first design system in Figma.' ),
	array( 'Google UX Design Professional Certificate', 'education', 'Google / Coursera', 'Online', '2022', '2022', 'User research, wireframing, prototyping and usability testing.' ),
	array( 'B.Sc. Computer Science', 'education', 'University of Delhi', 'New Delhi', '2016', '2019', '' ),
);
foreach ( $exp as $i => $e ) {
	folio_setup_post( 'experience', $e[0], array( 'post_content' => $e[6], 'menu_order' => $i ), array( 'kind' => $e[1], 'company' => $e[2], 'location' => $e[3], 'start' => $e[4], 'end' => $e[5] ) );
}

/* ---------- Blog posts ---------- */
foreach ( array( 'UI/UX', 'Frontend', 'WordPress' ) as $cat ) {
	if ( ! term_exists( $cat, 'category' ) ) {
		wp_insert_term( $cat, 'category' );
	}
}
$posts = array(
	array(
		'10 UI principles every developer should know',
		'ui-principles',
		'UI/UX',
		'Small design rules that make a big difference: spacing, hierarchy, contrast and more, explained for developers.',
		array(
			'You do not need to be a designer to build interfaces that feel polished. Most "this looks off" moments come from a handful of rules. Here are the ten I use on every project.',
			'## 1. Use a spacing scale',
			'Pick a scale (4, 8, 12, 16, 24, 32, 48, 64) and use only those values. Consistent spacing is the fastest way to make a layout feel designed.',
			'## 2. One primary action per screen',
			'If everything is a bright button, nothing is. Make the main action obvious and style the rest as secondary or links.',
			'## 3. Hierarchy comes from size, weight and colour',
			'Not every label needs to be bold. Use lighter colours for supporting text and save weight for what matters.',
			array( 'Limit yourself to 2-3 font sizes per component', 'Line length: 60-75 characters for body text', 'Contrast: at least 4.5:1 for normal text', 'Touch targets: at least 44 x 44 px', 'Align everything to something', 'Show states: hover, focus, disabled, loading, empty, error', 'Use real content while designing' ),
			'> Good UI is mostly invisible: people just get things done.',
		),
	),
	array(
		'How I make WordPress sites load in under a second',
		'fast-wordpress',
		'WordPress',
		'My practical checklist for fast WordPress websites: themes, images, fonts, scripts and hosting.',
		array(
			'Speed is a feature. Faster sites rank better, convert better and simply feel better. This is the checklist I follow for every WordPress build.',
			'## Start with a light theme',
			'Page builders add hundreds of kilobytes of CSS and JavaScript. A custom block theme ships only what the page needs.',
			'## Images and fonts',
			array( 'Serve WebP/AVIF in responsive sizes', 'Lazy-load everything below the fold', 'Self-host fonts and preload the main one', 'Use font-display: swap' ),
			'## Scripts and plugins',
			'Every plugin should earn its place. Remove sliders, heavy analytics and anything that loads on every page but is used on one.',
			'## Hosting and caching',
			'Use PHP 8.2+, full-page caching and a CDN. Measure with Lighthouse and real-user Core Web Vitals, not just your fast office Wi-Fi.',
		),
	),
	array(
		'From Figma to code: my hand-off checklist',
		'figma-to-code',
		'Frontend',
		'How I prepare Figma files so the build is fast, accurate and nothing gets lost between design and development.',
		array(
			'The gap between a beautiful Figma file and a working website is where most projects lose time. Whether I hand off to another developer or to myself, I use the same checklist.',
			'## Tokens first',
			'Colours, type styles, spacing and radius live as variables. In code they become CSS custom properties with the same names.',
			'## Components with every state',
			array( 'Default, hover, focus, active, disabled', 'Loading and empty states', 'Error messages and validation', 'Mobile, tablet and desktop variants' ),
			'## Real content and edge cases',
			'Long names, missing images, 1 item and 100 items. Designing for these up front saves days of back-and-forth later.',
			'## Annotate behaviour',
			'Animations, breakpoints and what happens on click are written next to the frame, not left to guesswork.',
		),
	),
);
foreach ( $posts as $i => $pp ) {
	$id = folio_setup_post(
		'post',
		$pp[0],
		array(
			'post_excerpt' => $pp[3],
			'post_content' => folio_blocks( $pp[4] ),
			'post_date'    => gmdate( 'Y-m-d H:i:s', strtotime( '-' . ( 3 + $i * 9 ) . ' days' ) ),
		),
		array(),
		array( 'category' => array( $pp[2] ) )
	);
	if ( $id && ! has_post_thumbnail( $id ) ) {
		set_post_thumbnail( $id, folio_setup_image( 'post-' . $pp[1] . '.png', $pp[0] ) );
	}
}
// The default "Uncategorized" category is not needed when posts have their own topics.
$uncat = get_term_by( 'slug', 'uncategorized', 'category' );
if ( $uncat && 0 === (int) $uncat->count && (int) get_option( 'default_category' ) === (int) $uncat->term_id ) {
	$guides = get_term_by( 'name', 'UI/UX', 'category' );
	if ( $guides ) {
		update_option( 'default_category', $guides->term_id );
		wp_delete_term( $uncat->term_id, 'category' );
	}
}

/* ---------- Pages ---------- */
$home    = folio_setup_post( 'page', 'Home', array( 'post_content' => '', 'post_name' => 'home', 'post_excerpt' => '' ), array(), array(), false );
$about   = folio_setup_post( 'page', 'About', array( 'post_content' => '', 'post_name' => 'about' ), array(), array(), false );
$contact = folio_setup_post(
	'page',
	'Contact',
	array(
		'post_name'    => 'contact',
		'post_content' => folio_blocks(
			array(
				'## Frequently asked questions',
				'<strong>How much does a website cost?</strong> Most projects are between $1,000 and $7,500 depending on pages, features and content. You always get a fixed quote before we start.',
				'<strong>How long does it take?</strong> A landing page takes about 2 weeks; a full website 4-8 weeks.',
				'<strong>Do you work with clients abroad?</strong> Yes. I work remotely with clients worldwide and overlap with European and US hours.',
			)
		),
	),
	array(),
	array(),
	false
);
$blog    = folio_setup_post( 'page', 'Blog', array( 'post_content' => '', 'post_name' => 'blog' ), array(), array(), false );
$privacy = folio_setup_post(
	'page',
	'Privacy Policy',
	array(
		'post_name'    => 'privacy-policy',
		'post_excerpt' => 'What this website collects and why.',
		'post_content' => folio_blocks(
			array(
				'This website does not use tracking cookies or third-party analytics. Fonts are loaded from this website, not from Google.',
				'## Contact form',
				'When you send the contact form, your name, email, company and message are saved on this website so I can reply to you. They are not shared with anyone and are deleted on request.',
				'## Your rights',
				'You can ask to see, correct or delete your data at any time by email.',
			)
		),
	),
	array(),
	array(),
	false
);
update_post_meta( $about, '_wp_page_template', 'template-about.php' );
update_post_meta( $contact, '_wp_page_template', 'template-contact.php' );
update_option( 'show_on_front', 'page' );
update_option( 'page_on_front', $home );
update_option( 'page_for_posts', $blog );
update_option( 'wp_page_for_privacy_policy', $privacy );

/* ---------- Menus ---------- */
$menus = array(
	'primary' => array( 'Header menu', array( array( 'archive', 'project', 'Work' ), array( 'archive', 'case_study', 'Case Studies' ), array( 'archive', 'service', 'Services' ), array( 'page', $about, 'About' ), array( 'page', $blog, 'Blog' ) ) ),
	'footer'  => array( 'Footer menu', array( array( 'page', $home, 'Home' ), array( 'archive', 'project', 'Work' ), array( 'archive', 'case_study', 'Case Studies' ), array( 'archive', 'service', 'Services' ), array( 'page', $about, 'About' ), array( 'page', $blog, 'Blog' ), array( 'page', $contact, 'Contact' ), array( 'page', $privacy, 'Privacy Policy' ) ) ),
);
$locations = get_theme_mod( 'nav_menu_locations', array() );
foreach ( $menus as $loc => $m ) {
	$menu = wp_get_nav_menu_object( $m[0] );
	if ( $menu ) {
		$locations[ $loc ] = $menu->term_id;
		continue;
	}
	$menu_id = wp_create_nav_menu( $m[0] );
	foreach ( $m[1] as $item ) {
		if ( 'archive' === $item[0] ) {
			wp_update_nav_menu_item( $menu_id, 0, array( 'menu-item-title' => $item[2], 'menu-item-type' => 'post_type_archive', 'menu-item-object' => $item[1], 'menu-item-status' => 'publish' ) );
		} else {
			wp_update_nav_menu_item( $menu_id, 0, array( 'menu-item-title' => $item[2], 'menu-item-type' => 'post_type', 'menu-item-object' => 'page', 'menu-item-object-id' => $item[1], 'menu-item-status' => 'publish' ) );
		}
	}
	$locations[ $loc ] = $menu_id;
	WP_CLI::log( "Created menu: {$m[0]}" );
}
set_theme_mod( 'nav_menu_locations', $locations );

flush_rewrite_rules();
WP_CLI::success( 'Content ready.' );
