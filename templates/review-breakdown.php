<?php

/**
 * Template for review breakdown.
 * You can override this template by copying it to your theme's directory.
 */
defined('ABSPATH') || exit;

/** @var float $average */
/** @var int $max_stars */
/** @var int $total_reviews */
/** @var array $star_counts */
?>

<div class="rs-breakdown">
  <div class="rs-breakdown-header">
    <div class="rs-breakdown-average">
      <?= number_format($average, 1); ?>
    </div>
    <div class="rs-breakdown-stars">
      <?= render_stars($average, $max_stars); ?>
    </div>
    <div class="rs-breakdown-total">
      <?= $total_reviews; ?> <?= esc_html_e('total reviews', 'review-system'); ?>
    </div>
  </div>

  <div class="rs-breakdown-bars">
    <?php for ($i = $max_stars; $i >= 1; $i--):
      $percent = $total_reviews ? ($star_counts[$i] / $total_reviews) * 100 : 0;
    ?>
      <div class="rs-breakdown-row">
        <span class="rs-breakdown-label"><?= $i; ?></span>
        <div class="rs-breakdown-bar">
          <div class="rs-breakdown-fill" style="width: <?= esc_attr($percent); ?>%;"></div>
        </div>
      </div>
    <?php endfor; ?>
  </div>
</div>

<?php
function render_stars($average, $max_stars = 5) {
  $html = '';
  for ($i = 1; $i <= $max_stars; $i++) {
    if ($average >= $i) {
      $html .= '<span class="star full">★</span>';
    } elseif ($average >= $i - 0.75) {
      $html .= '<span class="star half">★</span>';
    } else {
      $html .= '<span class="star empty">★</span>';
    }
  }
  return $html;
}

?>