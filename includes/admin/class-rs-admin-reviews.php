<?php
defined('ABSPATH') || exit;

class RS_Admin_Reviews {

  public static function init() {
    add_action('admin_menu', [__CLASS__, 'add_menu']);
  }

  public static function add_menu() {
    add_menu_page(
      __('Reviews', 'review-system'),
      __('Reviews', 'review-system'),
      'manage_options',
      'rs_reviews',
      [__CLASS__, 'render_page'],
      'dashicons-star-filled'
    );
  }

  public static function render_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'rs_reviews';
    require_once RS_PATH . 'includes/utils/helper.php';

    if (isset($_GET['action'], $_GET['id']) && $_GET['action'] === 'view' && !empty($_GET['id'])) {
      $id = (int) $_GET['id'];
      $review = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

      if (!$review) {
        echo '<div class="notice notice-error"><p>' . __('Review not found.', 'review-system') . '</p></div>';
        return;
      }
?>
      <div class="wrap">
        <h1><?php echo RS_Helper::rs_clean_text($review->name); ?> &mdash; <?php _e('Review Details', 'review-system'); ?></h1>
        <p><strong><?php _e('Business:', 'review-system'); ?></strong> <?= RS_Helper::rs_clean_text($review->business_name); ?></p>
        <p><strong><?php _e('Rating:', 'review-system'); ?></strong> <?= RS_Helper::rs_clean_text($review->rating); ?> / <?= RS_Helper::rs_clean_text(get_option('rs_max_stars', 5)); ?> <span class="dashicons dashicons-star-filled" style="color: #ffa500; font-size: 12px;"></span></p>
        <p><strong><?php _e('Status:', 'review-system'); ?></strong> <?= RS_Helper::rs_clean_text($review->status); ?></p>
        <p><strong><?php _e('Date:', 'review-system'); ?></strong> <?= RS_Helper::rs_clean_text(date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($review->created_at))); ?></p>
        <p><strong><?php _e('IP Address:', 'review-system'); ?></strong> <?= RS_Helper::rs_clean_text($review->ip) ?? 'Unknown'; ?></p>
        <hr>
        <h2><?php _e('Full Comment:', 'review-system'); ?></h2>
        <p><?= RS_Helper::rs_clean_text($review->comment); ?></p>
        <p><a href="<?= esc_url(admin_url('admin.php?page=rs_reviews')); ?>"><?php _e('Back to Reviews', 'review-system'); ?></a></p>
      </div>
    <?php
      return;
    }

    require_once RS_PATH . 'includes/admin/class-rs-admin-table.php';
    $reviews_table = new RS_Admin_Reviews_Table();
    $reviews_table->prepare_items();

    $status = $_GET['status'] ?? 'all';

    $counts = [
      'all' => (int) $wpdb->get_var("SELECT COUNT(*) FROM $table_name"),
      'approved' => (int) $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE status = 'approved'"),
      'pending' => (int) $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE status = 'pending'"),
      'rejected' => (int) $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE status = 'rejected'"),
      'hidden' => (int) $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE status = 'hidden'"),
    ];
    $statuses = [
      'all' => __('All', 'review-system'),
      'approved' => __('Approved', 'review-system'),
      'pending' => __('Pending', 'review-system'),
      'rejected' => __('Rejected', 'review-system'),
      'hidden' => __('Hidden', 'review-system'),
    ];
    $filter_links = [];
    foreach ($statuses as $key => $label) {
      $url = esc_url(add_query_arg(['status' => $key], admin_url('admin.php?page=rs_reviews')));
      $class = ($status === $key) ? 'current' : '';
      $count = isset($counts[$key]) ? " <span class='count'>({$counts[$key]})</span>" : '';
      $filter_links[] = "<a href='{$url}' class='{$class}'>{$label}</a>{$count}";
    }
    ?>
    <div class="wrap">
      <h1><?php esc_html_e('Reviews', 'review-system'); ?></h1>
      <ul class="subsubsub">
        <li><?php echo implode(' | </li><li>', $filter_links); ?></li>
      </ul>

      <!-- Search form -->
      <form method="get" id="nsm-search-form">
        <input type="hidden" name="page" value="rs_reviews" />
        <?php if (isset($_GET['status'])): ?>
          <input type="hidden" name="status" value="<?php echo esc_attr($_GET['status']); ?>" />
        <?php endif; ?>
        <?php $reviews_table->search_box(__('Search Reviews', 'review-system'), 'review-search-input'); ?>
      </form>

      <!-- Bulk actions form -->
      <form method="post">
        <input type="hidden" name="page" value="rs_reviews">
        <?php if (isset($_GET['status'])): ?>
          <input type="hidden" name="status" value="<?php echo esc_attr($_GET['status']); ?>" />
        <?php endif; ?>
        <?php $reviews_table->display(); ?>
      </form>
      
      <div class="copy-right">
        <p>Developed with <span class="dashicons dashicons-heart" style="color: #ff0000;"></span> by <a href="https://github.com/Jchnc" target="_blank">Jean Navarro</a></p>
        <p><?php _e('Plugin Version:', 'review-system'); ?> <?php echo RS_Helper::rs_clean_text(RS_VERSION); ?></p>
      </div>
    </div>
<?php
  }
}
