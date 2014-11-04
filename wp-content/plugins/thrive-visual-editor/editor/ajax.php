<?php
/**
 * Executes AJAX requests - mimics the admin-ajax.php file - we need some shortcodes to be executed in the "Frontend" part of the site.
 * Some plugins do not register shortcodes if inside administration
 *
 * @since 2.1.0
 */
define('DOING_AJAX', true);

define('WP_ADMIN', false);

/** Load WordPress Bootstrap */
require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/wp-load.php');

/** Allow for cross-domain requests (from the frontend). */
send_origin_headers();

// Require an action parameter
if (empty($_REQUEST['action']))
    die('0');

/** Load WordPress Administration APIs */
//require_once(ABSPATH . 'wp-admin/includes/admin.php');

/** Load Ajax Handlers for WordPress Core */
//require_once(ABSPATH . 'wp-admin/includes/ajax-actions.php');

@header('Content-Type: text/html; charset=' . get_option('blog_charset'));
@header('X-Robots-Tag: noindex');

send_nosniff_header();
nocache_headers();

/** This action is documented in wp-admin/admin.php */
//do_action('init');

$allowed_actions_post = array('tcb_ajax_tve_render_shortcode');

if (empty($_POST['action']) || !is_user_logged_in()) {
    die('0');
}

$action = 'tcb_ajax_' . $_POST['action'];
if (!in_array($action, $allowed_actions_post)) {
    die('0');
}

do_action($action);

// Default status
die('0');