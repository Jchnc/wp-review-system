<?php

/**
 * Template for review form.
 * You can override this template by copying it to your theme's directory.
 */
defined('ABSPATH') || exit;

/** @var int $max_stars */
/** @var int $max_comment_length */
/** @var int $max_name_length */
/** @var int $max_business_length */
/** @var string $error */
/** @var string $success */

// Get current user nickname if logged in
$user_nickname = is_user_logged_in() ? wp_get_current_user()->display_name : '';

// Defaults
$max_name_length     = (int) get_option('rs_max_name_length', 50);
$max_business_length = (int) get_option('rs_max_business_length', 100);
$max_comment_length  = (int) get_option('rs_max_comment_length', 100);
?>

<form method="post" class="rs-review-form modern-review-form" novalidate>
  <?php if (!empty($error)): ?>
    <div class="rs-message error"><?php echo esc_html($error); ?></div>
  <?php endif; ?>

  <?php if (!empty($success)): ?>
    <div class="rs-message success"><?php echo esc_html($success); ?></div>
  <?php endif; ?>

  <div class="form-group">
    <label for="rs_name"><?php esc_html_e('Your Name', 'review-system'); ?>*</label>
    <input type="text"
      name="rs_name"
      id="rs_name"
      class="form-input"
      required
      maxlength="<?php echo esc_attr($max_name_length); ?>"
      value="<?php echo esc_attr($user_nickname); ?>">
  </div>

  <div class="form-group">
    <label for="rs_business"><?php esc_html_e('Business Name', 'review-system'); ?></label>
    <input type="text"
      name="rs_business"
      id="rs_business"
      class="form-input"
      maxlength="<?php echo esc_attr($max_business_length); ?>">
  </div>

  <div class="form-group">
    <label><?php esc_html_e('Rating', 'review-system'); ?>*</label>
    <div class="star-rating-container">
      <div class="star-rating">
        <?php for ($i = $max_stars; $i >= 1; $i--): ?>
          <input type="radio" id="star<?php echo $i; ?>" name="rs_rating" value="<?php echo $i; ?>" required>
          <label for="star<?php echo $i; ?>" title="<?php echo esc_attr($i . ' stars'); ?>"></label>
        <?php endfor; ?>
        <div class="rating-text"><?php esc_html_e('Unrated', 'review-system'); ?></div>
      </div>
    </div>
  </div>

  <div class="form-group">
    <label for="rs_comment"><?php esc_html_e('Comment', 'review-system'); ?>*</label>
    <textarea name="rs_comment"
      id="rs_comment"
      class="form-input"
      rows="5"
      required
      maxlength="<?php echo esc_attr($max_comment_length); ?>"></textarea>
    <small id="remaining_chars" class="char-counter"><?php echo esc_html($max_comment_length); ?> <?php esc_html_e('characters remaining', 'review-system'); ?></small>
  </div>

  <?php wp_nonce_field('rs_submit_review', 'rs_nonce'); ?>
  <div class="form-group">
    <button type="submit" class="submit-btn"><?php esc_html_e('Submit Review', 'review-system'); ?></button>
  </div>
</form>