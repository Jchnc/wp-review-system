<?php
defined('ABSPATH') || exit;

class RS_Admin_Settings {

  public static function init() {
    add_action('admin_menu', [__CLASS__, 'add_settings_page']);
    add_action('admin_init', [__CLASS__, 'register_settings']);
  }

  public static function add_settings_page() {
    add_submenu_page(
      'rs_reviews',
      __('Review Settings', 'review-system'),
      __('Settings', 'review-system'),
      'manage_options',
      'rs_settings',
      [__CLASS__, 'render_settings_page']
    );
  }

  public static function register_settings() {
    register_setting('rs_settings_group', 'rs_max_stars');
    register_setting('rs_settings_group', 'rs_logged_in_only');
    register_setting('rs_settings_group', 'rs_enable_comments');
    register_setting('rs_settings_group', 'rs_max_comment_length');
    register_setting('rs_settings_group', 'rs_need_approval');
  }

  public static function render_settings_page() {
?>
    <div class="wrap">
      <h1><?php esc_html_e('Review System Settings', 'review-system'); ?></h1>

      <form method="post" action="options.php">
        <?php settings_fields('rs_settings_group'); ?>

        <table class="form-table" role="presentation">
          <tbody>
            <tr>
              <th scope="row">
                <label for="rs_max_stars"><?php esc_html_e('Max Stars', 'review-system'); ?></label>
              </th>
              <td>
                <input
                  type="number"
                  id="rs_max_stars"
                  name="rs_max_stars"
                  value="<?php echo esc_attr(get_option('rs_max_stars', 5)); ?>"
                  min="1" max="5"
                  class="small-text" />
                <p class="description">
                  <?php esc_html_e('Set the maximum number of stars a review can have (1 to 5).', 'review-system'); ?>
                </p>
              </td>
            </tr>

            <tr>
              <th scope="row"><?php esc_html_e('Only allow logged-in users?', 'review-system'); ?></th>
              <td>
                <fieldset>
                  <label for="rs_logged_in_only">
                    <input
                      type="checkbox"
                      id="rs_logged_in_only"
                      name="rs_logged_in_only"
                      value="1"
                      <?php checked(get_option('rs_logged_in_only'), 1); ?> />
                    <?php esc_html_e('Yes', 'review-system'); ?>
                  </label>
                  <p class="description">
                    <?php esc_html_e('If enabled, only users who are logged in can submit reviews.', 'review-system'); ?>
                  </p>
                </fieldset>
              </td>
            </tr>

            <tr>
              <th scope="row"><?php esc_html_e('Enable comments section?', 'review-system'); ?></th>
              <td>
                <fieldset>
                  <label for="rs_enable_comments">
                    <input
                      type="checkbox"
                      id="rs_enable_comments"
                      name="rs_enable_comments"
                      value="1"
                      <?php checked(get_option('rs_enable_comments'), 1); ?> />
                    <?php esc_html_e('Yes', 'review-system'); ?>
                  </label>
                  <p class="description">
                    <?php esc_html_e('Toggle to allow users to leave comments alongside their reviews.', 'review-system'); ?>
                  </p>
                </fieldset>
              </td>
            </tr>

            <tr>
              <th scope="row">
                <label for="rs_max_comment_length"><?php esc_html_e('Max comment length', 'review-system'); ?></label>
              </th>
              <td>
                <input
                  type="number"
                  id="rs_max_comment_length"
                  name="rs_max_comment_length"
                  value="<?php echo esc_attr(get_option('rs_max_comment_length', 100)); ?>"
                  min="1" max="1000"
                  class="small-text" />
                <p class="description">
                  <?php esc_html_e('Maximum number of characters allowed in a review comment.', 'review-system'); ?>
                </p>
              </td>
            </tr>

            <tr>
              <th scope="row"><?php esc_html_e('Need approval?', 'review-system'); ?></th>
              <td>
                <fieldset>
                  <label for="rs_need_approval">
                    <input
                      type="checkbox"
                      id="rs_need_approval"
                      name="rs_need_approval"
                      value="1"
                      <?php checked(get_option('rs_need_approval'), 1); ?> />
                    <?php esc_html_e('Yes', 'review-system'); ?>
                  </label>
                  <p class="description">
                    <?php esc_html_e('If enabled, reviews must be approved by an admin before becoming publicly visible.', 'review-system'); ?>
                  </p>
                </fieldset>
              </td>
            </tr>
          </tbody>
        </table>

        <?php submit_button(); ?>
      </form>

      <footer style="margin-top: 2em; font-size: 13px; color: #555;">
        <p>
          <?php
          printf(
            esc_html__('Developed with %s by %s', 'review-system'),
            '<span class="dashicons dashicons-heart" style="color:#ff0000;"></span>',
            '<a href="https://github.com/Jchnc" target="_blank" rel="noopener noreferrer">Jean Navarro</a>'
          );
          ?>
        </p>
        <p><?php printf(esc_html__('Plugin Version: %s', 'review-system'), esc_html(RS_VERSION)); ?></p>
      </footer>
    </div>

<?php
  }
}
