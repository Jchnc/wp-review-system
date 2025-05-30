<?php
defined('ABSPATH') || exit;

class RS_Assets {

  public static function init() {
    add_action('wp_enqueue_scripts', [__CLASS__, 'enqueue']);
  }

  public static function enqueue() {
    wp_enqueue_style('rs-style', RS_URL . 'assets/css/style.css', [], RS_VERSION);
    wp_enqueue_script('rs-script', RS_URL . 'assets/js/script.js', ['jquery'], RS_VERSION, true);

    wp_localize_script('rs-script', 'RS_Ajax', [
      'ajax_url' => admin_url('admin-ajax.php'),
      'nonce'    => wp_create_nonce('rs_nonce')
    ]);
  }
}
