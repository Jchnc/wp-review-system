<?php
defined('ABSPATH') || exit;

class RS_Install {
  public static function activate() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'rs_reviews';
    $charset_collate = $wpdb->get_charset_collate();

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';

    $sql = "CREATE TABLE {$table_name} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id BIGINT UNSIGNED DEFAULT NULL,
            name VARCHAR(100) NOT NULL,
            business_name VARCHAR(150) DEFAULT NULL,
            ip VARCHAR(45) DEFAULT NULL,
            rating TINYINT UNSIGNED NOT NULL,
            comment TEXT,
            status ENUM('pending', 'approved', 'rejected', 'hidden') DEFAULT 'pending',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY rating (rating),
            KEY status (status)
        ) $charset_collate;";

    dbDelta($sql);

    // Set default settings
    add_option('rs_max_stars', 5);
    add_option('rs_logged_in_only', false);
    add_option('rs_enable_comments', true);
  }
}
