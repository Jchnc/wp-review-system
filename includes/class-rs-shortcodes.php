<?php
defined('ABSPATH') || exit;

class RS_Shortcodes {

  /**
   * Initialize shortcodes
   * [rs_review_form]
   * [rs_review_breakdown]
   * [rs_review_list]
   */
  public static function init() {
    add_shortcode('rs_review_form', [__CLASS__, 'review_form']);
    add_shortcode('rs_review_breakdown', [__CLASS__, 'review_breakdown']);
    add_shortcode('rs_review_list', [__CLASS__, 'review_list']);
  }

  public static function review_form() {
    ob_start();
    include RS_PATH . 'templates/review-form.php';
    return ob_get_clean();
  }

  public static function review_breakdown() {
    ob_start();
    include RS_PATH . 'templates/review-breakdown.php';
    return ob_get_clean();
  }

  public static function review_list() {
    if (!get_option('rs_enable_comments')) {
      return ''; // Return nothing if comments are disabled
    }
    ob_start();
    include RS_PATH . 'templates/review-list.php';
    return ob_get_clean();
  }
}
