<?php

/**
 * Plugin Name: JReviews
 * Description: Review System for WordPress. Collect, manage, and display user reviews on your WordPress site.
 * Version: 1.0.0
 * Author: Jean Navarro
 * Author URI: https://github.com/Jchnc
 * Text Domain: review-system
 * Domain Path: /languages
 */

defined('ABSPATH') || exit;

// Define constants
define('RS_VERSION', '1.0.0');
define('RS_PATH', plugin_dir_path(__FILE__));
define('RS_URL', plugin_dir_url(__FILE__));
define('RS_BASENAME', plugin_basename(__FILE__));

// Autoload core files
require_once RS_PATH . 'includes/install.php';
require_once RS_PATH . 'includes/class-rs-plugin.php';
require_once RS_PATH . 'includes/class-rs-form.php';
require_once RS_PATH . 'includes/class-rs-display.php';
require_once RS_PATH . 'includes/admin/class-rs-admin-reviews.php';
require_once RS_PATH . 'includes/admin/class-rs-admin-settings.php';

register_activation_hook(__FILE__, ['RS_Install', 'activate']);

if (is_admin()) {
  require_once RS_PATH . 'includes/admin/class-rs-admin-reviews.php';
  require_once RS_PATH . 'includes/admin/class-rs-admin-settings.php';
  RS_Admin_Reviews::init();
  RS_Admin_Settings::init();
}

// Shortcodes
require_once RS_PATH . 'includes/class-rs-shortcodes.php';
RS_Shortcodes::init();

// Assets
require_once RS_PATH . 'includes/class-rs-assets.php';
RS_Assets::init();

// Boot plugin
add_action('plugins_loaded', function () {
  load_plugin_textdomain('review-system', false, dirname(RS_BASENAME) . '/languages');
  RS_Plugin::get_instance();
});
