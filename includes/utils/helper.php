<?php
defined('ABSPATH') || exit;

class RS_Helper {

  public static function rs_clean_text($text) {
    return esc_html(stripslashes($text));
  }
}
