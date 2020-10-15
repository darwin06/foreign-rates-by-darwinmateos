<?php
/**
 * Plugin Name: Foreign Rates by Darwin Mateos
 * Plugin URI: http://github.com/darwin06/foreign-rates-by-darwinmateos
 * Description: WordPress plugin to show Foreign rates. 💸
 * Version: 0.0.1
 * Author: Darwin Mateos
 * Author URI: http://darwin06.github.io/
 * License: GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: doms-rates
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
  die;
}

// * Add Styles
function doms_exchange_style()
{
  wp_enqueue_style('doms-exchange-rate', 'https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css', false, 'all');
}
add_action('wp_enqueue_scripts', 'doms_exchange_style');

// Get Option page
require plugin_dir_path(__FILE__). 'inc/options-page.php';

// Get an array of post objects which belongs to “Currency” category and has an “EUR” tag, also post has a custom field “rate”with value greater than 1 and post publication date is older than one week.
require plugin_dir_path(__FILE__). 'inc/currency-query.php';

// Get Widgets
require plugin_dir_path(__FILE__) . 'inc/widgets.php';

// Get Shortcodes
require plugin_dir_path(__FILE__) . 'inc/shortcodes.php';

// Activation
function doms_foreign_rates_activation()
{
  /*
   * Create Categories and Tags
   */
  /* Categories */
  $catFinance = wp_create_category('Finance');
  wp_create_category('Currency', $catFinance);

  /* Tags */
  $tags = array('EUR', 'CHF');
  wp_set_post_tags(999999999, $tags, true);

}
register_activation_hook(__FILE__, 'doms_foreign_rates_activation');

// Deactivation
function doms_foreign_rates_deactivation()
{
  // Flush cache
  wp_cache_flush();
}
register_deactivation_hook(__FILE__, 'doms_foreign_rates_deactivation');

// Uninstall
function doms_foreign_rates_uninstall()
{
}
register_uninstall_hook(__FILE__, 'doms_foreign_rates_uninstall');
