<?php
defined('ABSPATH') || exit;

class RS_Form {

  public static function render_form() {
    $max_stars = intval(get_option('rs_max_stars', 5));
    $max_comment_length = intval(get_option('rs_max_comment_length', 100));
    $max_name_length = intval(get_option('rs_max_name_length', 50));
    $max_business_length = intval(get_option('rs_max_business_length', 100));
    $only_logged_in = filter_var(get_option('rs_logged_in_only', false), FILTER_VALIDATE_BOOLEAN);
    $error = '';
    $success = '';

    // Handle redirect success message
    if (isset($_GET['rs_review_success'])) {
      if (get_option('rs_need_approval', true)) {
        $success = __('Thank you for your review. It is pending approval.', 'review-system');
      } else {
        $success = __('Thank you for your review. It is visible now.', 'review-system');
      }
    }

    // Process the form
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rs_nonce']) && wp_verify_nonce($_POST['rs_nonce'], 'rs_submit_review')) {
      $name     = sanitize_text_field($_POST['rs_name'] ?? '');
      $business = sanitize_text_field($_POST['rs_business'] ?? '');
      $rating   = intval($_POST['rs_rating'] ?? 0);
      $comment  = sanitize_textarea_field($_POST['rs_comment'] ?? '');
      $ip       = $_SERVER['REMOTE_ADDR'];

      if (empty($name) || empty($rating) || empty($comment)) {
        $error = __('Please fill in all required fields.', 'review-system');
      } elseif ($rating < 1 || $rating > $max_stars) {
        $error = __('Invalid rating value.', 'review-system');
      } elseif (strlen($name) > $max_name_length) {
        $error = __('Name exceeds maximum length.', 'review-system');
      } elseif (strlen($business) > $max_business_length) {
        $error = __('Business name exceeds maximum length.', 'review-system');
      } elseif (strlen($comment) > $max_comment_length) {
        $error = __('Comment exceeds maximum length.', 'review-system');
      } elseif ($only_logged_in && !is_user_logged_in()) {
        $error = __('You must be logged in to submit a review.', 'review-system');
      } else {
        global $wpdb;
        $table = $wpdb->prefix . 'rs_reviews';

        $wpdb->insert($table, [
          'user_id'       => get_current_user_id(),
          'name'          => $name,
          'business_name' => $business,
          'rating'        => $rating,
          'comment'       => $comment,
          'ip'            => $ip,
          'status'        => get_option('rs_need_approval', true) ? 'pending' : 'approved',
          'created_at'    => current_time('mysql'),
        ]);

        $redirect_url = (is_ssl() ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $redirect_url = add_query_arg('rs_review_success', '1', $redirect_url);
        $success = __('Thank you for your review. It is pending approval.', 'review-system');
        wp_safe_redirect($redirect_url);
        exit;
      }
    }

    ob_start();
    RS_Template_Loader::render('review-form.php', [
      'max_stars'          => $max_stars,
      'max_comment_length' => $max_comment_length,
      'max_name_length'    => $max_name_length,
      'max_business_length' => $max_business_length,
      'error'              => $error,
      'success'            => $success,
    ]);
    return ob_get_clean();
  }


  // TODO: Maybe for the future
  // public static function handle_ajax() {
  //   check_ajax_referer('rs_nonce', 'nonce');

  //   $rating = intval($_POST['rating']);
  //   $comment = sanitize_textarea_field($_POST['comment']);
  //   $name = sanitize_text_field($_POST['name']);
  //   $business = sanitize_text_field($_POST['business']);

  //   $max_stars = intval(get_option('rs_max_stars', 5));
  //   $max_comment_length = intval(get_option('rs_max_comment_length', 100));
  //   $max_name_length = intval(get_option('rs_max_name_length', 50));
  //   $max_business_length = intval(get_option('rs_max_business_length', 100));

  //   if ($rating < 1 || $rating > $max_stars) {
  //     wp_send_json_error(['message' => 'Invalid rating.']);
  //     return;
  //   }

  //   if (strlen($comment) > $max_comment_length) {
  //     wp_send_json_error(['message' => 'Comment exceeds maximum length.']);
  //     return;
  //   }

  //   if (strlen($name) > $max_name_length) {
  //     wp_send_json_error(['message' => 'Name exceeds maximum length.']);
  //     return;
  //   }

  //   if (strlen($business) > $max_business_length) {
  //     wp_send_json_error(['message' => 'Business name exceeds maximum length.']);
  //     return;
  //   }

  //   $data = [
  //     'user_id'      => get_current_user_id(),
  //     'name'         => $name ?: 'Anonymous',
  //     'business_name' => $business,
  //     'ip'           => $_SERVER['REMOTE_ADDR'] ?: 'Unknown',
  //     'rating'       => $rating,
  //     'comment'      => $comment,
  //     'status'       => get_option('rs_need_approval', true) ? 'pending' : 'approved',
  //     'created_at'   => current_time('mysql'),
  //   ];

  //   global $wpdb;
  //   $table = $wpdb->prefix . 'rs_reviews';
  //   $success = $wpdb->insert($table, [
  //     'user_id'    => $data['user_id'],
  //     'name'       => $data['name'],
  //     'business_name' => $data['business_name'],
  //     'ip'         => $data['ip'],
  //     'rating'     => $data['rating'],
  //     'comment'    => $data['comment'],
  //     'status'     => $data['status'],
  //     'created_at' => $data['created_at'],
  //   ]);

  //   if ($success) {
  //     wp_send_json_success(['message' => 'Thank you for your review! It will be visible after approval.']);
  //   } else {
  //     wp_send_json_error(['message' => 'Failed to submit review. Please try again.']);
  //   }
  // }
}

// Hook into AJAX
// add_action('wp_ajax_rs_submit_review', [RS_Review_Form::class, 'handle_ajax']);
// add_action('wp_ajax_nopriv_rs_submit_review', [RS_Review_Form::class, 'handle_ajax']);
