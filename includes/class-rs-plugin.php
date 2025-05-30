<?php
defined('ABSPATH') || exit;

class RS_Plugin {

  private static $instance = null;

  public static function get_instance() {
    if (self::$instance === null) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  private function __construct() {
    $this->includes();
    $this->init_hooks();
  }

  private function includes() {
    // Core classes needed everywhere
    require_once RS_PATH . 'includes/class-rs-form.php';
    require_once RS_PATH . 'includes/class-rs-display.php';
    require_once RS_PATH . 'includes/class-rs-shortcodes.php';
    require_once RS_PATH . 'includes/utils/class-rs-template-loader.php';

    // Admin-only classes
    if (is_admin()) {
      require_once RS_PATH . 'includes/admin/class-rs-admin-reviews.php';
      require_once RS_PATH . 'includes/admin/class-rs-admin-settings.php';

      RS_Admin_Reviews::init();
      RS_Admin_Settings::init();
    }
  }

  private function init_hooks() {
    // Register shortcodes
    add_shortcode('rs_review_form', [RS_Form::class, 'render_form']);
    add_shortcode('rs_review_list', [RS_Display::class, 'render_list']);
    add_shortcode('rs_review_breakdown', [RS_Display::class, 'render_breakdown']);

    // Enqueue frontend assets
    add_action('wp_enqueue_scripts', [$this, 'load_frontend_assets']);
    // Enqueue admin assets
    add_action('admin_enqueue_scripts', [$this, 'load_admin_assets']);
  }

  public function load_frontend_assets() {
    wp_enqueue_style('rs-frontend', RS_URL . 'assets/css/style.css', [], RS_VERSION);
    wp_enqueue_script('rs-frontend', RS_URL . 'assets/js/frontend.js', ['jquery'], RS_VERSION, true);

    wp_localize_script('rs-frontend', 'rsData', [
      'ajax_url' => admin_url('admin-ajax.php'),
      'nonce'    => wp_create_nonce('rs_nonce'),
    ]);
  }

  public function load_admin_assets() {
    wp_enqueue_style('rs-admin', RS_URL . 'assets/css/admin.css', [], RS_VERSION);
  }
}
