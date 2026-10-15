<?php
/**
 * Content types: Projects (/work/), Case Studies (/case-studies/), Services (/services/), Testimonials and
 * Experience (shown inside pages, no own address). Categories ("Work type") and "Tools" are shared by
 * projects and case studies.
 *
 * @package folio-core
 */

defined( 'ABSPATH' ) || exit;

add_action( 'init', 'folio_register_content_types' );

/** Registers the post types and taxonomies. */
function folio_register_content_types() {
	$types = array(
		'project'     => array(
			'labels'   => array( __( 'Projects', 'folio-core' ), __( 'Project', 'folio-core' ) ),
			'slug'     => 'work',
			'icon'     => 'dashicons-portfolio',
			'public'   => true,
			'supports' => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes', 'revisions' ),
			'position' => 5,
		),
		'case_study'  => array(
			'labels'   => array( __( 'Case Studies', 'folio-core' ), __( 'Case Study', 'folio-core' ) ),
			'slug'     => 'case-studies',
			'icon'     => 'dashicons-analytics',
			'public'   => true,
			'supports' => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes', 'revisions' ),
			'position' => 6,
		),
		'service'     => array(
			'labels'   => array( __( 'Services', 'folio-core' ), __( 'Service', 'folio-core' ) ),
			'slug'     => 'services',
			'icon'     => 'dashicons-hammer',
			'public'   => true,
			'supports' => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes', 'revisions' ),
			'position' => 7,
		),
		'testimonial' => array(
			'labels'   => array( __( 'Testimonials', 'folio-core' ), __( 'Testimonial', 'folio-core' ) ),
			'slug'     => '',
			'icon'     => 'dashicons-format-quote',
			'public'   => false,
			'supports' => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
			'position' => 8,
		),
		'experience'  => array(
			'labels'   => array( __( 'Experience', 'folio-core' ), __( 'Experience item', 'folio-core' ) ),
			'slug'     => '',
			'icon'     => 'dashicons-id-alt',
			'public'   => false,
			'supports' => array( 'title', 'editor', 'page-attributes' ),
			'position' => 9,
		),
	);

	foreach ( $types as $type => $t ) {
		list( $plural, $single ) = $t['labels'];
		register_post_type(
			$type,
			array(
				'labels'              => array(
					'name'               => $plural,
					'singular_name'      => $single,
					/* translators: %s: item type, e.g. Project */
					'add_new_item'       => sprintf( __( 'Add new %s', 'folio-core' ), strtolower( $single ) ),
					/* translators: %s: item type */
					'edit_item'          => sprintf( __( 'Edit %s', 'folio-core' ), strtolower( $single ) ),
					/* translators: %s: item type */
					'new_item'           => sprintf( __( 'New %s', 'folio-core' ), strtolower( $single ) ),
					/* translators: %s: item type */
					'view_item'          => sprintf( __( 'View %s', 'folio-core' ), strtolower( $single ) ),
					/* translators: %s: item type plural */
					'search_items'       => sprintf( __( 'Search %s', 'folio-core' ), strtolower( $plural ) ),
					/* translators: %s: item type plural */
					'not_found'          => sprintf( __( 'No %s yet.', 'folio-core' ), strtolower( $plural ) ),
					/* translators: %s: item type plural */
					'all_items'          => sprintf( __( 'All %s', 'folio-core' ), strtolower( $plural ) ),
					'featured_image'     => 'testimonial' === $type ? __( 'Photo of the person', 'folio-core' ) : __( 'Cover image', 'folio-core' ),
					'set_featured_image' => 'testimonial' === $type ? __( 'Set photo', 'folio-core' ) : __( 'Set cover image', 'folio-core' ),
					'menu_name'          => $plural,
				),
				'public'              => $t['public'],
				'publicly_queryable'  => $t['public'],
				'exclude_from_search' => ! $t['public'],
				'show_ui'             => true,
				'show_in_nav_menus'   => $t['public'],
				'show_in_rest'        => true,
				'has_archive'         => $t['public'] ? $t['slug'] : false,
				'rewrite'             => $t['public'] ? array( 'slug' => $t['slug'], 'with_front' => false ) : false,
				'menu_icon'           => $t['icon'],
				'menu_position'       => 20 + $t['position'],
				'supports'            => $t['supports'],
				'hierarchical'        => false,
			)
		);
	}

	register_taxonomy(
		'work_type',
		array( 'project', 'case_study' ),
		array(
			'labels'            => array(
				'name'          => __( 'Work types', 'folio-core' ),
				'singular_name' => __( 'Work type', 'folio-core' ),
				'add_new_item'  => __( 'Add new work type', 'folio-core' ),
				'menu_name'     => __( 'Work types', 'folio-core' ),
			),
			'description'       => __( 'Shown as filter buttons on the Work page (e.g. Web Design, Frontend, UI/UX, WordPress).', 'folio-core' ),
			'hierarchical'      => true,
			'public'            => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'rewrite'           => array( 'slug' => 'work-type', 'with_front' => false ),
		)
	);

	register_taxonomy(
		'tool',
		array( 'project', 'case_study' ),
		array(
			'labels'            => array(
				'name'          => __( 'Tools & tech', 'folio-core' ),
				'singular_name' => __( 'Tool', 'folio-core' ),
				'add_new_item'  => __( 'Add new tool', 'folio-core' ),
				'separate_items_with_commas' => __( 'Separate tools with commas (e.g. Figma, React, WordPress)', 'folio-core' ),
				'menu_name'     => __( 'Tools & tech', 'folio-core' ),
			),
			'hierarchical'      => false,
			'public'            => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'rewrite'           => array( 'slug' => 'tool', 'with_front' => false ),
		)
	);
}

/** Result rows (big number + label), used by case studies. */
function folio_metric_sub() {
	return array(
		array( 'key' => 'value', 'label' => __( 'Number', 'folio-core' ), 'type' => 'text', 'placeholder' => '+38%' ),
		array( 'key' => 'label', 'label' => __( 'What it measures', 'folio-core' ), 'type' => 'text', 'placeholder' => 'Conversion rate' ),
	);
}

/**
 * Fields of each post type, shown in a box under the editor.
 *
 * @return array post type => [ 'title' => box title, 'fields' => field list ]
 */
function folio_post_fields() {
	$common_links = array(
		array( 'key' => 'live_url', 'label' => __( 'Live website URL', 'folio-core' ), 'type' => 'url', 'placeholder' => 'https://' ),
		array( 'key' => 'code_url', 'label' => __( 'Code URL (GitHub etc.)', 'folio-core' ), 'type' => 'url', 'placeholder' => 'https://github.com/...' ),
		array( 'key' => 'design_url', 'label' => __( 'Design / prototype URL (Figma etc.)', 'folio-core' ), 'type' => 'url', 'placeholder' => 'https://figma.com/...' ),
	);

	return array(
		'project'     => array(
			'title'  => __( 'Project details', 'folio-core' ),
			'intro'  => __( 'Title = project name. Cover image (right side) = card image. Excerpt = the short line on cards. The editor above is the full project write-up.', 'folio-core' ),
			'fields' => array_merge(
				array(
					array( 'key' => 'client', 'label' => __( 'Client', 'folio-core' ), 'type' => 'text', 'placeholder' => 'Acme Ltd' ),
					array( 'key' => 'role', 'label' => __( 'My role', 'folio-core' ), 'type' => 'text', 'placeholder' => 'UI/UX design, frontend' ),
					array( 'key' => 'year', 'label' => __( 'Year', 'folio-core' ), 'type' => 'text', 'placeholder' => '2026' ),
					array( 'key' => 'duration', 'label' => __( 'Duration', 'folio-core' ), 'type' => 'text', 'placeholder' => '6 weeks' ),
				),
				$common_links,
				array(
					array( 'key' => 'case_study', 'label' => __( 'Full case study (optional)', 'folio-core' ), 'type' => 'post', 'post_type' => 'case_study', 'help' => __( 'Adds a "Read the case study" button to this project.', 'folio-core' ) ),
					array( 'key' => 'accent', 'label' => __( 'Card colour', 'folio-core' ), 'type' => 'color', 'help' => __( 'Background behind the cover image on cards. Empty = theme accent.', 'folio-core' ) ),
					array( 'key' => 'featured', 'label' => __( 'Show on the home page (Selected work)', 'folio-core' ), 'type' => 'checkbox' ),
					array( 'key' => 'gallery', 'label' => __( 'Gallery (screens, mock-ups)', 'folio-core' ), 'type' => 'gallery', 'help' => __( 'Shown on the project page under the write-up. Tip: 1600px wide images look sharp.', 'folio-core' ) ),
				)
			),
		),
		'case_study'  => array(
			'title'  => __( 'Case study details', 'folio-core' ),
			'intro'  => __( 'Title = case study headline. Excerpt = summary on cards and at the top. The editor above holds the full story (process, research, wireframes...). The fields below build the overview, challenge/solution and results blocks.', 'folio-core' ),
			'fields' => array_merge(
				array(
					array( 'key' => 'client', 'label' => __( 'Client', 'folio-core' ), 'type' => 'text' ),
					array( 'key' => 'industry', 'label' => __( 'Industry', 'folio-core' ), 'type' => 'text', 'placeholder' => 'E-commerce, fashion' ),
					array( 'key' => 'role', 'label' => __( 'My role', 'folio-core' ), 'type' => 'text', 'placeholder' => 'Lead UI/UX designer & frontend developer' ),
					array( 'key' => 'timeline', 'label' => __( 'Timeline', 'folio-core' ), 'type' => 'text', 'placeholder' => '10 weeks, 2026' ),
					array( 'key' => 'team', 'label' => __( 'Team', 'folio-core' ), 'type' => 'text', 'placeholder' => '1 designer, 2 developers, 1 PM' ),
					array( 'key' => 'deliverables', 'label' => __( 'Deliverables', 'folio-core' ), 'type' => 'text', 'placeholder' => 'Research, UX flows, UI kit, website' ),
				),
				$common_links,
				array(
					array( 'key' => 'challenge', 'label' => __( 'The challenge', 'folio-core' ), 'type' => 'textarea', 'rows' => 5, 'help' => __( 'What problem did the client have? A blank line starts a new paragraph.', 'folio-core' ) ),
					array( 'key' => 'solution', 'label' => __( 'The solution', 'folio-core' ), 'type' => 'textarea', 'rows' => 5, 'help' => __( 'What did you do, in short? The full process goes in the editor above.', 'folio-core' ) ),
					array( 'key' => 'results', 'label' => __( 'Results (numbers)', 'folio-core' ), 'type' => 'repeater', 'sub' => folio_metric_sub(), 'add_label' => __( 'Add result', 'folio-core' ), 'help' => __( 'Up to 4 look best. Example: "+38%" / "Conversion rate".', 'folio-core' ) ),
					array( 'key' => 'quote', 'label' => __( 'Client quote', 'folio-core' ), 'type' => 'textarea', 'rows' => 3 ),
					array( 'key' => 'quote_name', 'label' => __( 'Quote by (name)', 'folio-core' ), 'type' => 'text' ),
					array( 'key' => 'quote_role', 'label' => __( 'Their role / company', 'folio-core' ), 'type' => 'text' ),
					array( 'key' => 'accent', 'label' => __( 'Card colour', 'folio-core' ), 'type' => 'color' ),
					array( 'key' => 'featured', 'label' => __( 'Show on the home page (Case studies)', 'folio-core' ), 'type' => 'checkbox' ),
					array( 'key' => 'gallery', 'label' => __( 'Gallery (final screens)', 'folio-core' ), 'type' => 'gallery' ),
				)
			),
		),
		'service'     => array(
			'title'  => __( 'Service details', 'folio-core' ),
			'intro'  => __( 'Title = service name. Excerpt = short text on the card. The editor above is the service page text.', 'folio-core' ),
			'fields' => array(
				array( 'key' => 'icon', 'label' => __( 'Icon', 'folio-core' ), 'type' => 'select', 'options' => folio_icon_choices(), 'default' => 'code' ),
				array( 'key' => 'price', 'label' => __( 'Starting price (optional)', 'folio-core' ), 'type' => 'text', 'placeholder' => 'From $1,500' ),
				array( 'key' => 'deliverables', 'label' => __( 'What is included (one per line)', 'folio-core' ), 'type' => 'textarea', 'rows' => 5, 'placeholder' => "Responsive design\nSpeed optimisation" ),
			),
		),
		'testimonial' => array(
			'title'  => __( 'About the person', 'folio-core' ),
			'intro'  => __( 'Title = the person\'s name. Editor = what they said. Photo = image on the right (optional).', 'folio-core' ),
			'fields' => array(
				array( 'key' => 'role', 'label' => __( 'Role', 'folio-core' ), 'type' => 'text', 'placeholder' => 'Founder' ),
				array( 'key' => 'company', 'label' => __( 'Company', 'folio-core' ), 'type' => 'text' ),
				array(
					'key'     => 'rating',
					'label'   => __( 'Stars', 'folio-core' ),
					'type'    => 'select',
					'options' => array( '5' => '5', '4' => '4', '3' => '3', '0' => __( 'Do not show stars', 'folio-core' ) ),
					'default' => '5',
				),
				array( 'key' => 'project', 'label' => __( 'Related project (optional)', 'folio-core' ), 'type' => 'post', 'post_type' => 'project' ),
			),
		),
		'experience'  => array(
			'title'  => __( 'Experience details', 'folio-core' ),
			'intro'  => __( 'Title = your role (e.g. Senior Frontend Developer). Editor = what you did there. Shown as a timeline on the About page; order by "Order" (lower first) or newest start date.', 'folio-core' ),
			'fields' => array(
				array(
					'key'     => 'kind',
					'label'   => __( 'Type', 'folio-core' ),
					'type'    => 'select',
					'options' => array(
						'work'      => __( 'Job', 'folio-core' ),
						'freelance' => __( 'Freelance', 'folio-core' ),
						'education' => __( 'Education / course', 'folio-core' ),
						'award'     => __( 'Award / certificate', 'folio-core' ),
					),
					'default' => 'work',
				),
				array( 'key' => 'company', 'label' => __( 'Company / institute', 'folio-core' ), 'type' => 'text' ),
				array( 'key' => 'location', 'label' => __( 'Location', 'folio-core' ), 'type' => 'text', 'placeholder' => 'Remote' ),
				array( 'key' => 'start', 'label' => __( 'From', 'folio-core' ), 'type' => 'text', 'placeholder' => '2022' ),
				array( 'key' => 'end', 'label' => __( 'To', 'folio-core' ), 'type' => 'text', 'placeholder' => __( 'Present', 'folio-core' ) ),
			),
		),
	);
}

// Portfolio items use the classic editor: the write-up and every detail field are on one screen. (In the block
// editor of WordPress 7 the fields sit in a collapsed "Meta Boxes" pane that is easy to miss.) Posts and pages keep blocks.
add_filter(
	'use_block_editor_for_post_type',
	function ( $use, $type ) {
		return in_array( $type, array( 'project', 'case_study', 'service', 'testimonial', 'experience' ), true ) ? false : $use;
	},
	10,
	2
);

// Clearer placeholder for the title box.
add_filter(
	'enter_title_here',
	function ( $text, $post ) {
		$titles = array(
			'project'     => __( 'Project name', 'folio-core' ),
			'case_study'  => __( 'Case study headline, e.g. "Redesigning checkout for a fashion store"', 'folio-core' ),
			'service'     => __( 'Service name', 'folio-core' ),
			'testimonial' => __( 'Person\'s name', 'folio-core' ),
			'experience'  => __( 'Your role, e.g. Senior Frontend Developer', 'folio-core' ),
		);
		return isset( $titles[ $post->post_type ] ) ? $titles[ $post->post_type ] : $text;
	},
	10,
	2
);

// Archives list items by "Order" (menu_order) first, then newest. Projects can be dragged into order by number.
add_action(
	'pre_get_posts',
	function ( $q ) {
		if ( is_admin() || ! $q->is_main_query() ) {
			return;
		}
		if ( $q->is_post_type_archive( array( 'project', 'case_study', 'service' ) ) || $q->is_tax( array( 'work_type', 'tool' ) ) ) {
			$q->set( 'orderby', array( 'menu_order' => 'ASC', 'date' => 'DESC' ) );
			$q->set( 'posts_per_page', $q->is_post_type_archive( 'service' ) ? 50 : 24 );
		}
	}
);
