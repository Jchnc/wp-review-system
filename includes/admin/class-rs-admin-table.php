<?php
defined('ABSPATH') || exit;

if (!class_exists('WP_List_Table')) {
  require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

require_once RS_PATH . 'includes/utils/helper.php';

class RS_Admin_Reviews_Table extends WP_List_Table {

  public function __construct() {
    parent::__construct([
      'singular' => 'review',
      'plural'   => 'reviews',
      'ajax'     => false,
    ]);
  }

  public function get_columns() {
    return [
      'cb'            => '<input type="checkbox" />',
      'name'          => __('Name', 'review-system'),
      'business_name' => __('Business', 'review-system'),
      'rating'        => __('Rating', 'review-system'),
      'comment'       => __('Comment', 'review-system'),
      'status'        => __('Status', 'review-system'),
      'created_at'    => __('Date', 'review-system'),
    ];
  }

  public function column_cb($item) {
    return sprintf('<input type="checkbox" name="review[]" value="%d" />', $item->id);
  }

  public function column_name($item) {
    $delete_url   = wp_nonce_url(admin_url("admin.php?page=rs_reviews&action=delete&id={$item->id}"), 'nsm_delete_' . $item->id);
    $reject_url   = wp_nonce_url(admin_url("admin.php?page=rs_reviews&action=reject&id={$item->id}"), 'nsm_reject_' . $item->id);
    $approve_url  = wp_nonce_url(admin_url("admin.php?page=rs_reviews&action=approve&id={$item->id}"), 'nsm_approve_' . $item->id);
    $hide_url     = wp_nonce_url(admin_url("admin.php?page=rs_reviews&action=hidden&id={$item->id}"), 'nsm_hidden_' . $item->id);
    $name         = RS_Helper::rs_clean_text($item->name);
    $actions      = [
      'approve' => sprintf(
        '<a href="%s" style="color:#2d7a39;">%s</a>',
        esc_url($approve_url),
        __('Approve', 'review-system')
      ),
      'hide' => sprintf(
        '<a href="%s">%s</a>',
        esc_url($hide_url),
        __('Hide', 'review-system')
      ),
      'reject' => sprintf(
        '<a href="%s" style="color:#a00;" onclick="return confirm(\'%s\');">%s</a>',
        esc_url($reject_url),
        esc_js(__('Are you sure you want to reject this review?', 'review-system')),
        __('Reject', 'review-system')
      ),
      'delete' => sprintf(
        '<a href="%s" style="color:#a00;" onclick="return confirm(\'%s\');">%s</a>',
        esc_url($delete_url),
        esc_js(__('Are you sure you want to delete this review? This action cannot be undone.', 'review-system')),
        __('Delete', 'review-system')
      )
    ];
    return $name . '<br>' . implode(' | ', $actions);
  }

  public function column_business_name($item) {
    return RS_Helper::rs_clean_text($item->business_name);
  }

  public function column_rating($item) {
    $max_rating = (int) get_option('rs_max_stars', 5);
    $rating = (float) $item->rating;

    return sprintf(
      '<span class="nsm-chip rating"><span class="nsm-chip-icon star"></span>%s / %s</span>',
      esc_html($rating),
      esc_html($max_rating)
    );
  }

  public function column_comment($item) {
    $comment_short = wp_trim_words($item->comment, 10, '...');
    $view_url = admin_url("admin.php?page=rs_reviews&action=view&id={$item->id}");
    $read_more = '<br><a href="' . esc_url($view_url) . '">' . __('Read More', 'review-system') . '</a>';
    return RS_Helper::rs_clean_text($comment_short) . $read_more;
  }

  public function column_status($item) {
    $status = esc_html($item->status);
    $label = ucwords($status);

    $class = match ($status) {
      'approved' => 'nsm-chip approved',
      'pending'  => 'nsm-chip pending',
      'rejected' => 'nsm-chip rejected',
      'hidden'   => 'nsm-chip hidden',
      default    => 'nsm-chip',
    };

    return sprintf('<span class="%s">%s</span>', esc_attr($class), esc_html($label));
  }


  public function column_created_at($item) {
    $gmt = get_date_from_gmt($item->created_at);
    return date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($gmt));
  }

  public function get_sortable_columns() {
    return [
      'name'       => ['name', true],
      'business_name' => ['business_name', true],
      'rating'     => ['rating', true],
      'comment'    => ['comment', true],
      'status'     => ['status', true],
      'created_at' => ['created_at', true],
    ];
  }


  public function get_bulk_actions() {
    return [
      'approved' => __('Approved', 'review-system'),
      'pending'  => __('Pending', 'review-system'),
      'rejected' => __('Rejected', 'review-system'),
      'hidden'   => __('Hidden', 'review-system'),
      'delete'   => __('Delete', 'review-system'),
    ];
  }

  public function process_bulk_action() {
    global $wpdb;
    $table = $wpdb->prefix . 'rs_reviews';

    $action = $this->current_action();

    if (!empty($_POST['review']) && is_array($_POST['review'])) {
      $ids = array_map('intval', $_POST['review']);
      if (empty($ids)) {
        return;
      }

      if ($action === 'delete') {
        // Bulk delete
        $wpdb->query("DELETE FROM $table WHERE id IN (" . implode(',', $ids) . ")");
      } elseif (in_array($action, ['approved', 'pending', 'rejected', 'hidden'], true)) {
        // Bulk update status
        $status_map = [
          'approved' => 'approved',
          'pending'  => 'pending',
          'rejected' => 'rejected',
          'hidden'   => 'hidden',
        ];

        $new_status = $status_map[$action];
        $ids_placeholder = implode(',', array_fill(0, count($ids), '%d'));
        $query = $wpdb->prepare(
          "UPDATE $table SET status = %s WHERE id IN ($ids_placeholder)",
          array_merge([$new_status], $ids)
        );
        $wpdb->query($query);
      }
    }

    // Handle individual actions
    if (isset($_GET['action'], $_GET['id'], $_GET['_wpnonce'])) {
      $id = (int) $_GET['id'];
      $action = $_GET['action'];
      $nonce_action = 'nsm_' . $action . '_' . $id;

      if (wp_verify_nonce($_GET['_wpnonce'], $nonce_action)) {
        switch ($action) {
          case 'delete':
            $wpdb->delete($table, ['id' => $id], ['%d']);
            break;

          case 'approve':
            $wpdb->update($table, ['status' => 'approved'], ['id' => $id], ['%s'], ['%d']);
            break;

          case 'reject':
            $wpdb->update($table, ['status' => 'rejected'], ['id' => $id], ['%s'], ['%d']);
            break;

          case 'hidden':
            $wpdb->update($table, ['status' => 'hidden'], ['id' => $id], ['%s'], ['%d']);
            break;

          default:
            error_log('Review System Plugin - Unknown action: ' . $action);
            break;
        }
      }
    }
  }


  public function get_search_box($text, $input_id) {
    $this->search_box($text, $input_id);
  }


  public function prepare_items() {
    $this->process_bulk_action();

    global $wpdb;
    $table = $wpdb->prefix . 'rs_reviews';

    $per_page = 20;
    $paged = $this->get_pagenum();
    $offset = ($paged - 1) * $per_page;

    $search = isset($_GET['s']) ? trim($_GET['s']) : '';
    $status_filter = $_GET['status'] ?? '';

    $orderby = isset($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : 'created_at';
    $order = isset($_GET['order']) ? sanitize_text_field($_GET['order']) : 'DESC';

    $valid_orderby_columns = ['name', 'business_name', 'rating', 'comment', 'status', 'created_at'];
    if (!in_array($orderby, $valid_orderby_columns, true)) {
      $orderby = 'created_at';
    }

    $order = strtoupper($order);
    if (!in_array($order, ['ASC', 'DESC'], true)) {
      $order = 'DESC';
    }

    $where = "WHERE 1=1";

    if ($search) {
      $where .= $wpdb->prepare(" AND (name LIKE %s OR business_name LIKE %s)", '%' . $wpdb->esc_like($search) . '%', '%' . $wpdb->esc_like($search) . '%');
    }
    if (in_array($status_filter, ['pending', 'approved', 'rejected', 'hidden'], true)) {
      $where .= $wpdb->prepare(" AND status = %s", $status_filter);
    }

    $total_items = $wpdb->get_var("SELECT COUNT(*) FROM $table $where");

    $this->set_pagination_args([
      'total_items' => $total_items,
      'per_page'    => $per_page,
    ]);

    $this->_column_headers = [$this->get_columns(), [], $this->get_sortable_columns()];

    $sql = $wpdb->prepare(
      "SELECT * FROM $table $where ORDER BY $orderby $order LIMIT %d OFFSET %d",
      $per_page,
      $offset
    );
    $this->items = $wpdb->get_results($sql);
  }
}
