<?php
defined('WP_UNINSTALL_PLUGIN') || exit;

global $wpdb;

$table_name = $wpdb->prefix . 'rs_reviews';
$wpdb->query("DROP TABLE IF EXISTS {$table_name}");

$option_names = [
  'rs_max_stars',
  'rs_logged_in_only',
  'rs_enable_comments',
  'rs_max_comment_length',
  'rs_need_approval',
];

foreach ($option_names as $option) {
  delete_option($option);
}
