<?php
defined('ABSPATH') || exit;

class RS_Display {

  public static function render_breakdown() {
    global $wpdb;
    $table = $wpdb->prefix . 'rs_reviews';
    $max_stars = intval(get_option('rs_max_stars', 5));

    $results = $wpdb->get_results("SELECT rating, COUNT(*) as count FROM {$table} WHERE status = 'approved' GROUP BY rating", OBJECT_K);

    $total_reviews = 0;
    $sum_ratings = 0;
    $star_counts = [];

    for ($i = 1; $i <= $max_stars; $i++) {
      $star_counts[$i] = isset($results[$i]) ? (int) $results[$i]->count : 0;
      $total_reviews += $star_counts[$i];
      $sum_ratings += $i * $star_counts[$i];
    }

    if ($total_reviews === 0) {
      return '<p>' . esc_html__('No reviews yet.', 'review-system') . '</p>';
    }

    $average = round($sum_ratings / $total_reviews, 1);

    ob_start();
    RS_Template_Loader::render('review-breakdown.php', compact(
      'average',
      'max_stars',
      'star_counts',
      'total_reviews'
    ));
    return ob_get_clean();
  }


  public static function render_list($atts = []) {
    global $wpdb;
    require_once RS_PATH . 'includes/utils/helper.php';

    $atts = shortcode_atts([
      'per_page' => 5,
      'paged'    => isset($_GET['rs_page']) ? max(1, intval($_GET['rs_page'])) : 1,
      'stars'    => isset($_GET['stars']) ? intval($_GET['stars']) : 0,
    ], $atts);

    $table = $wpdb->prefix . 'rs_reviews';
    $offset = ($atts['paged'] - 1) * $atts['per_page'];

    $where = "WHERE status = 'approved'";
    $params = [];

    if ($atts['stars'] > 0 && $atts['stars'] <= 5) {
      $where .= " AND rating = %d";
      $params[] = $atts['stars'];
    }

    $params[] = $atts['per_page'];
    $params[] = $offset;

    $query = "SELECT * FROM {$table} {$where} ORDER BY created_at DESC LIMIT %d OFFSET %d";
    $reviews = $wpdb->get_results($wpdb->prepare($query, ...$params));

    $reviews = array_map(function ($review) {
      $review->comment = RS_Helper::rs_clean_text($review->comment);
      $review->name = RS_Helper::rs_clean_text($review->name);
      $review->business_name = RS_Helper::rs_clean_text($review->business_name);
      return $review;
    }, $reviews);

    // Count total matching reviews for pagination
    $count_query = "SELECT COUNT(*) FROM {$table} {$where}";
    $count_params = array_slice($params, 0, count($params) - 2);

    if (!empty($count_params)) {
      $total = $wpdb->get_var($wpdb->prepare($count_query, ...$count_params));
    } else {
      $total = $wpdb->get_var($count_query);
    }
    $pages = ceil($total / $atts['per_page']);

    ob_start();
    RS_Template_Loader::render('review-list.php', [
      'reviews' => $reviews,
      'pages'   => $pages,
      'current' => $atts['paged'],
      'stars'   => $atts['stars'],
    ]);
    return ob_get_clean();
  }
}
