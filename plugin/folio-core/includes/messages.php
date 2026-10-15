<?php
/**
 * Contact form ([folio_contact] and the Contact page template). Messages are saved in {prefix}folio_messages,
 * listed under Messages in the admin and emailed to the owner.
 * Spam protection without cookies or third parties: a hidden field, a signed time stamp and a per-IP limit.
 *
 * @package folio-core
 */

defined( 'ABSPATH' ) || exit;

/** Table name. */
function folio_messages_table() {
	global $wpdb;
	return $wpdb->prefix . 'folio_messages';
}

/** Creates or upgrades the table. */
function folio_messages_install() {
	global $wpdb;
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	$charset = $wpdb->get_charset_collate();
	$table   = folio_messages_table();
	dbDelta(
		"CREATE TABLE $table (
		id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		name varchar(190) NOT NULL DEFAULT '',
		email varchar(190) NOT NULL,
		company varchar(190) NOT NULL DEFAULT '',
		service varchar(190) NOT NULL DEFAULT '',
		budget varchar(190) NOT NULL DEFAULT '',
		message text NOT NULL,
		page varchar(190) NOT NULL DEFAULT '',
		is_read tinyint(1) NOT NULL DEFAULT 0,
		created_at datetime NOT NULL,
		PRIMARY KEY  (id),
		KEY created (created_at),
		KEY email (email)
		) $charset;"
	);
	update_option( 'folio_db_version', FOLIO_CORE_DB_VERSION );
}

/** Signed time stamp: forms posted in under 3 seconds or with a forged stamp are refused. */
function folio_stamp() {
	$t = (string) time();
	return $t . '.' . hash_hmac( 'sha256', $t, wp_salt( 'nonce' ) );
}

/**
 * Checks a posted time stamp.
 *
 * @param string $stamp Value from the form.
 * @return bool
 */
function folio_stamp_ok( $stamp ) {
	$parts = explode( '.', (string) $stamp, 2 );
	if ( 2 !== count( $parts ) || ! ctype_digit( $parts[0] ) ) {
		return false;
	}
	if ( ! hash_equals( hash_hmac( 'sha256', $parts[0], wp_salt( 'nonce' ) ), $parts[1] ) ) {
		return false;
	}
	$age = time() - (int) $parts[0];
	return $age >= 3 && $age <= 14 * DAY_IN_SECONDS;
}

/** Service names offered in the form: published services, plus "Something else". */
function folio_service_choices() {
	$names = array();
	foreach ( get_posts( array( 'post_type' => 'service', 'numberposts' => 30, 'orderby' => array( 'menu_order' => 'ASC', 'date' => 'DESC' ) ) ) as $p ) {
		$names[] = get_the_title( $p );
	}
	if ( $names ) {
		$names[] = __( 'Something else', 'folio-core' );
	}
	return $names;
}

/** Message shown above the form after it was sent (read from the redirect's query string). */
function folio_form_notice() {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display only.
	$status   = isset( $_GET['folio_status'] ) ? sanitize_key( $_GET['folio_status'] ) : '';
	$messages = array(
		'ok'      => (string) folio_opt( 'form_success' ),
		'email'   => __( 'Please enter a valid email address.', 'folio-core' ),
		'message' => __( 'Please write a message (up to 5,000 characters).', 'folio-core' ),
		'limit'   => __( 'Too many messages from your connection in the last hour. Please try again later or email me directly.', 'folio-core' ),
		'expired' => __( 'The form had been open too long or was sent too quickly. Please send it again.', 'folio-core' ),
	);
	if ( ! isset( $messages[ $status ] ) ) {
		return '';
	}
	$class = 'ok' === $status ? 'alert alert--ok' : 'alert alert--error';
	return '<div class="' . esc_attr( $class ) . '" role="status">' . esc_html( $messages[ $status ] ) . '</div>';
}

/** The contact form ([folio_contact]). */
function folio_contact_form() {
	$services = folio_service_choices();
	$budgets  = folio_lines( folio_opt( 'budgets' ) );
	ob_start();
	?>
	<div id="contact-form" class="contact-form">
		<?php echo folio_form_notice(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside. ?>
		<form class="form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="folio_contact">
			<input type="hidden" name="folio_stamp" value="<?php echo esc_attr( folio_stamp() ); ?>">
			<input type="hidden" name="folio_page" value="<?php echo (int) get_queried_object_id(); ?>">
			<div class="form__hp" aria-hidden="true"><label>Website <input type="text" name="folio_website" tabindex="-1" autocomplete="off"></label></div>
			<div class="form__row">
				<label><span><?php esc_html_e( 'Your name', 'folio-core' ); ?></span>
					<input type="text" name="folio_name" required maxlength="190" autocomplete="name" placeholder="Jane Cooper">
				</label>
				<label><span><?php esc_html_e( 'Email', 'folio-core' ); ?></span>
					<input type="email" name="folio_email" required maxlength="190" autocomplete="email" placeholder="jane@company.com">
				</label>
			</div>
			<label><span><?php esc_html_e( 'Company or website', 'folio-core' ); ?> <em><?php esc_html_e( '(optional)', 'folio-core' ); ?></em></span>
				<input type="text" name="folio_company" maxlength="190" autocomplete="organization">
			</label>
			<?php if ( $services || $budgets ) : ?>
			<div class="form__row">
				<?php if ( $services ) : ?>
				<label><span><?php esc_html_e( 'What do you need?', 'folio-core' ); ?></span>
					<select name="folio_service">
						<option value=""><?php esc_html_e( 'Choose one', 'folio-core' ); ?></option>
						<?php foreach ( $services as $s ) : ?>
							<option><?php echo esc_html( $s ); ?></option>
						<?php endforeach; ?>
					</select>
				</label>
				<?php endif; ?>
				<?php if ( $budgets ) : ?>
				<label><span><?php esc_html_e( 'Budget', 'folio-core' ); ?></span>
					<select name="folio_budget">
						<option value=""><?php esc_html_e( 'Choose one', 'folio-core' ); ?></option>
						<?php foreach ( $budgets as $b ) : ?>
							<option><?php echo esc_html( $b ); ?></option>
						<?php endforeach; ?>
					</select>
				</label>
				<?php endif; ?>
			</div>
			<?php endif; ?>
			<label><span><?php esc_html_e( 'Tell me about your project', 'folio-core' ); ?></span>
				<textarea name="folio_message" required maxlength="5000" rows="6" placeholder="<?php esc_attr_e( 'Goals, timeline, links to anything that helps...', 'folio-core' ); ?>"></textarea>
			</label>
			<button type="submit" class="btn btn--primary btn--lg"><?php esc_html_e( 'Send message', 'folio-core' ); ?> <?php echo folio_icon( 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?></button>
			<p class="form__fine"><?php esc_html_e( 'Your details are used only to reply to you.', 'folio-core' ); ?></p>
		</form>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'folio_contact', 'folio_contact_form' );

/**
 * Sends the visitor back to the form with a status.
 *
 * @param string $status Status key for folio_form_notice().
 * @param int    $page   Page the form was on.
 */
function folio_form_back( $status, $page ) {
	$url = $page ? get_permalink( $page ) : '';
	if ( ! $url ) {
		$url = home_url( '/' );
	}
	wp_safe_redirect( add_query_arg( 'folio_status', $status, $url ) . '#contact-form', 303 );
	exit;
}

/**
 * Field from the posted form, cleaned and cut to a length.
 *
 * @param string $name Field name.
 * @param int    $max  Max characters.
 * @return string
 */
function folio_post_field( $name, $max = 190 ) {
	// phpcs:ignore WordPress.Security.NonceVerification.Missing -- public form; protected by the signed stamp, honeypot and rate limit.
	$value = isset( $_POST[ $name ] ) ? sanitize_text_field( wp_unslash( $_POST[ $name ] ) ) : '';
	return mb_substr( $value, 0, $max );
}

/** Handles the form (admin-post.php?action=folio_contact). */
function folio_handle_contact() {
	// phpcs:disable WordPress.Security.NonceVerification.Missing -- see folio_post_field().
	$page = isset( $_POST['folio_page'] ) ? absint( $_POST['folio_page'] ) : 0;

	// A filled honeypot is a bot: pretend it worked.
	if ( ! empty( $_POST['folio_website'] ) ) {
		folio_form_back( 'ok', $page );
	}
	if ( ! folio_stamp_ok( folio_post_field( 'folio_stamp', 200 ) ) ) {
		folio_form_back( 'expired', $page );
	}
	$email = sanitize_email( folio_post_field( 'folio_email' ) );
	if ( ! is_email( $email ) ) {
		folio_form_back( 'email', $page );
	}
	$message = isset( $_POST['folio_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['folio_message'] ) ) : '';
	// phpcs:enable
	if ( '' === trim( $message ) || mb_strlen( $message ) > 5000 ) {
		folio_form_back( 'message', $page );
	}

	// At most 6 messages per hour from one connection. The IP itself is never stored.
	$ip    = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	$key   = 'folio_rl_' . substr( hash_hmac( 'sha256', $ip, wp_salt( 'auth' ) ), 0, 32 );
	$count = (int) get_transient( $key );
	if ( $count >= 6 ) {
		folio_form_back( 'limit', $page );
	}
	set_transient( $key, $count + 1, HOUR_IN_SECONDS );

	$service = folio_post_field( 'folio_service' );
	$budget  = folio_post_field( 'folio_budget' );
	$row     = array(
		'name'       => folio_post_field( 'folio_name' ),
		'email'      => $email,
		'company'    => folio_post_field( 'folio_company' ),
		'service'    => in_array( $service, folio_service_choices(), true ) ? $service : '',
		'budget'     => in_array( $budget, folio_lines( folio_opt( 'budgets' ) ), true ) ? $budget : '',
		'message'    => $message,
		'page'       => $page ? (string) get_the_title( $page ) : '',
		'is_read'    => 0,
		'created_at' => current_time( 'mysql', true ),
	);
	global $wpdb;
	$wpdb->insert( folio_messages_table(), $row ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
	folio_notify( $row );
	folio_form_back( 'ok', $page );
}
add_action( 'admin_post_nopriv_folio_contact', 'folio_handle_contact' );
add_action( 'admin_post_folio_contact', 'folio_handle_contact' );

/**
 * Emails a new message to the owner. Failure is fine: the message is already saved.
 *
 * @param array $row Saved row.
 */
function folio_notify( $row ) {
	$to = folio_opt( 'notify_email' );
	if ( ! $to ) {
		$to = get_option( 'admin_email' );
	}
	$lines = array(
		'Name: ' . $row['name'],
		'Email: ' . $row['email'],
		'Company: ' . $row['company'],
		'Needs: ' . $row['service'],
		'Budget: ' . $row['budget'],
		'',
		$row['message'],
		'',
		'All messages: ' . admin_url( 'admin.php?page=folio-messages' ),
	);
	wp_mail( $to, 'New project enquiry from ' . ( $row['name'] ? $row['name'] : $row['email'] ), implode( "\n", $lines ), array( 'Reply-To: ' . $row['email'] ) );
}

add_action(
	'admin_menu',
	function () {
		global $wpdb;
		$table  = folio_messages_table();
		$unread = (int) $wpdb->get_var( "SELECT COUNT(*) FROM $table WHERE is_read = 0" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$label  = __( 'Messages', 'folio-core' ) . ( $unread ? ' <span class="awaiting-mod">' . $unread . '</span>' : '' );
		add_menu_page( __( 'Messages', 'folio-core' ), $label, 'manage_options', 'folio-messages', 'folio_render_messages_page', 'dashicons-email-alt', 4 );
	}
);

/** The Messages page. */
function folio_render_messages_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	global $wpdb;
	$table = folio_messages_table();
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only view parameter.
	$paged = isset( $_GET['paged'] ) ? max( 1, absint( $_GET['paged'] ) ) : 1;
	$per   = 30;
	$total = (int) $wpdb->get_var( "SELECT COUNT(*) FROM $table" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$rows  = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table ORDER BY created_at DESC, id DESC LIMIT %d OFFSET %d", $per, ( $paged - 1 ) * $per ), ARRAY_A ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$base  = admin_url( 'admin.php?page=folio-messages' );
	?>
	<div class="wrap">
		<h1 class="wp-heading-inline"><?php esc_html_e( 'Messages', 'folio-core' ); ?></h1>
		<a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=folio_export' ), 'folio_export' ) ); ?>" class="page-title-action"><?php esc_html_e( 'Download CSV', 'folio-core' ); ?></a>
		<hr class="wp-header-end">
		<p><?php esc_html_e( 'Everything sent through the contact form. New messages are marked in bold until you open this page.', 'folio-core' ); ?></p>
		<table class="widefat striped">
			<thead><tr>
				<th style="width:120px"><?php esc_html_e( 'Date', 'folio-core' ); ?></th>
				<th><?php esc_html_e( 'From', 'folio-core' ); ?></th>
				<th><?php esc_html_e( 'Needs / budget', 'folio-core' ); ?></th>
				<th style="width:45%"><?php esc_html_e( 'Message', 'folio-core' ); ?></th>
				<th></th>
			</tr></thead>
			<tbody>
			<?php if ( ! $rows ) : ?>
				<tr><td colspan="5"><?php esc_html_e( 'No messages yet.', 'folio-core' ); ?></td></tr>
			<?php endif; ?>
			<?php foreach ( $rows as $r ) : ?>
				<?php $del = wp_nonce_url( admin_url( 'admin-post.php?action=folio_delete_message&id=' . (int) $r['id'] ), 'folio_delete_' . (int) $r['id'] ); ?>
				<tr<?php echo $r['is_read'] ? '' : ' style="font-weight:600"'; ?>>
					<td><?php echo esc_html( get_date_from_gmt( $r['created_at'], 'j M Y, H:i' ) ); ?></td>
					<td><?php echo esc_html( $r['name'] ); ?><br><a href="mailto:<?php echo esc_attr( $r['email'] ); ?>"><?php echo esc_html( $r['email'] ); ?></a><?php echo $r['company'] ? '<br>' . esc_html( $r['company'] ) : ''; ?></td>
					<td><?php echo esc_html( $r['service'] ); ?><?php echo $r['budget'] ? '<br>' . esc_html( $r['budget'] ) : ''; ?></td>
					<td><?php echo nl2br( esc_html( $r['message'] ) ); ?></td>
					<td><a href="<?php echo esc_url( $del ); ?>" onclick="return confirm('Delete this message?')" style="color:#b32d2e"><?php esc_html_e( 'Delete', 'folio-core' ); ?></a></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		<?php
		$pages = (int) ceil( $total / $per );
		if ( $pages > 1 ) {
			echo '<div class="tablenav"><div class="tablenav-pages">' . wp_kses_post( paginate_links( array( 'base' => add_query_arg( 'paged', '%#%', $base ), 'format' => '', 'current' => $paged, 'total' => $pages ) ) ) . '</div></div>';
		}
		?>
	</div>
	<?php
	// Opening the page marks everything shown as read.
	$wpdb->query( "UPDATE $table SET is_read = 1 WHERE is_read = 0" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
}

/**
 * A CSV cell that spreadsheet programs will not run as a formula.
 *
 * @param string $value Cell value.
 * @return string
 */
function folio_csv_cell( $value ) {
	$value = (string) $value;
	if ( '' !== $value && in_array( $value[0], array( '=', '+', '-', '@', "\t", "\r" ), true ) ) {
		$value = "'" . $value;
	}
	return $value;
}

add_action(
	'admin_post_folio_export',
	function () {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Not allowed.', 'folio-core' ), 403 );
		}
		check_admin_referer( 'folio_export' );
		global $wpdb;
		$table = folio_messages_table();
		$rows  = $wpdb->get_results( "SELECT * FROM $table ORDER BY created_at ASC, id ASC", ARRAY_A ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		nocache_headers();
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename="messages-' . gmdate( 'Y-m-d' ) . '.csv"' );
		header( 'X-Content-Type-Options: nosniff' );
		$out = fopen( 'php://output', 'w' );
		fwrite( $out, chr( 0xEF ) . chr( 0xBB ) . chr( 0xBF ) ); // BOM, so Excel reads UTF-8.
		fputcsv( $out, array( 'Date (UTC)', 'Name', 'Email', 'Company', 'Needs', 'Budget', 'Message', 'Page' ) );
		foreach ( $rows as $r ) {
			fputcsv( $out, array_map( 'folio_csv_cell', array( $r['created_at'], $r['name'], $r['email'], $r['company'], $r['service'], $r['budget'], $r['message'], $r['page'] ) ) );
		}
		fclose( $out ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose
		exit;
	}
);

add_action(
	'admin_post_folio_delete_message',
	function () {
		$id = isset( $_GET['id'] ) ? absint( $_GET['id'] ) : 0;
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Not allowed.', 'folio-core' ), 403 );
		}
		check_admin_referer( 'folio_delete_' . $id );
		global $wpdb;
		$wpdb->delete( folio_messages_table(), array( 'id' => $id ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
		wp_safe_redirect( admin_url( 'admin.php?page=folio-messages' ) );
		exit;
	}
);
