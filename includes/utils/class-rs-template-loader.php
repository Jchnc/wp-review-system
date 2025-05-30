<?php
defined('ABSPATH') || exit;

class RS_Template_Loader {

  public static function locate($template_name) {
    $theme_template = get_stylesheet_directory() . '/review-system/' . $template_name;
    $plugin_template = RS_PATH . 'templates/' . $template_name;

    if (file_exists($theme_template)) {
      return $theme_template;
    }

    return file_exists($plugin_template) ? $plugin_template : false;
  }

  public static function render($template_name, $vars = []) {
    $template_path = self::locate($template_name);

    if (!$template_path) {
      return;
    }

    extract($vars);
    include $template_path;
  }
}
