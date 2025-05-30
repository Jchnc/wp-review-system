<?php
defined('ABSPATH') || exit;
/** @var array $reviews */
/** @var int $pages */
/** @var int $current */
/** @var int $stars */
?>

<div class="rs-review-container">

  <!-- ⭐️ Star Filter -->
  <form method="get" class="rs-review-filter-form">
    <label for="rs-star-filter"><?php esc_html_e('Filter by stars:', 'review-system'); ?></label>
    <select name="stars" id="rs-star-filter" onchange="this.form.submit()">
      <option value=""><?php esc_html_e('All Ratings', 'review-system'); ?></option>
      <?php for ($i = get_option('rs_max_stars', 5); $i >= 1; $i--): ?>
        <option value="<?php echo $i; ?>" <?php selected($stars, $i); ?>>
          <?php echo sprintf(_n('%d star', '%d stars', $i, 'review-system'), $i); ?>
        </option>
      <?php endfor; ?>
    </select>

    <?php
    // Preserve other query params
    foreach ($_GET as $key => $value) {
      if ($key !== 'stars' && $key !== 'rs_page') {
        echo '<input type="hidden" name="' . esc_attr($key) . '" value="' . esc_attr($value) . '">';
      }
    }
    ?>
  </form>

  <!-- Review List -->
  <div class="rs-review-list" id="rs-review-container">
    <?php foreach ($reviews as $r): ?>
      <article class="rs-review-item">
        <header class="rs-review-header">
          <span class="rs-review-name"><?php echo esc_html($r->name); ?> &mdash; <small style="font-size: 12px; color: #444; font-weight: normal; ">
              <?php echo esc_html($r->business_name); ?></small>
          </span>
          <span class="rs-review-rating" aria-label="<?php echo esc_attr($r->rating); ?> stars">
            <?php for ($i = 1; $i <= get_option('rs_max_stars', 5); $i++): ?>
              <span class="star<?php echo $i <= (int) $r->rating ? ' filled' : ''; ?>">★</span>
            <?php endfor; ?>
          </span>
        </header>
        <div class="rs-review-comment"><?php echo esc_html($r->comment); ?></div>
        <footer class="rs-review-footer">
          <time datetime="<?php echo esc_attr(date('Y-m-d', strtotime($r->created_at))); ?>">
            <?php echo esc_html(date('F j, Y', strtotime($r->created_at))); ?>
          </time>
        </footer>
      </article>
    <?php endforeach; ?>
  </div>

  <!-- Pagination -->
  <?php if ($pages > 1): ?>
    <nav class="rs-pagination" aria-label="Review pagination">
      <ul class="rs-pagination-list">
        <?php
        $base_url = remove_query_arg(['rs_page']);
        for ($i = 1; $i <= $pages; $i++):
          $page_url = esc_url(add_query_arg(array_merge($_GET, ['rs_page' => $i]), $base_url));
        ?>
          <li>
            <a href="<?php echo $page_url . '#rs-review-container'; ?>"
              class="rs-page-link<?php echo $current == $i ? ' current' : ''; ?>"
              <?php echo $current == $i ? ' aria-current="page"' : ''; ?>>
              <?php echo esc_html($i); ?>
            </a>
          </li>
        <?php endfor; ?>
      </ul>
    </nav>
  <?php endif; ?>

</div>