<?php
/**
 * Plugin Name: Folio Core
 * Description: Portfolio content for the Folio theme: Projects, Case Studies, Services, Testimonials and Experience with their own fields, a Portfolio Settings page (profile, home page sections, skills, social links, colours) and a contact form whose messages are saved in WordPress.
 * Version: 1.0.0
 * Requires at least: 6.6
 * Requires PHP: 7.4
 * Author: Folio
 * License: GPL-2.0-or-later
 * Text Domain: folio-core
 *
 * @package folio-core
 */

defined( 'ABSPATH' ) || exit;

define( 'FOLIO_CORE_VERSION', '1.0.0' );
define( 'FOLIO_CORE_DB_VERSION', '1' );
define( 'FOLIO_CORE_URL', plugin_dir_url( __FILE__ ) );
define( 'FOLIO_CORE_DIR', __DIR__ );

require __DIR__ . '/includes/helpers.php';
require __DIR__ . '/includes/fields.php';
require __DIR__ . '/includes/post-types.php';
require __DIR__ . '/includes/meta-boxes.php';
require __DIR__ . '/includes/settings.php';
require __DIR__ . '/includes/messages.php';
require __DIR__ . '/includes/admin.php';
require __DIR__ . '/includes/seo.php';

register_activation_hook(
	__FILE__,
	function () {
		folio_register_content_types();
		folio_messages_install();
		flush_rewrite_rules();
	}
);

register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );

// Creates or upgrades the messages table when the plugin was copied in without being re-activated (e.g. after a migration).
add_action(
	'plugins_loaded',
	function () {
		if ( get_option( 'folio_db_version' ) !== FOLIO_CORE_DB_VERSION ) {
			folio_messages_install();
		}
	}
);
