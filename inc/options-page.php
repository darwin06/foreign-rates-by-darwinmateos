<?php
/* Option Page */

// If this file is called directly, abort.
if (!defined('WPINC')) {
  die;
}

require plugin_dir_path(__DIR__) . 'inc/fetch-save-api.php';

// Get data from transient in fetch_save_api function.

/* Add Administration menu item  */
/* Function to create option page */
function doms_foreign_rates_menu()
{
  // Create Foreign Rates item into Adjustments page
  add_options_page(__('Foreign Rates Options', 'doms-rates'), __('Foreign Rates', 'doms-rates'), 'manage_options', 'doms-foreign-rates', 'doms_foreign_rates_options', 7);

  // Call register settings function
  add_action('admin_init', 'register_doms_foreign_rates_settings');
}
/* Register menu item into adminn menu */
add_action('admin_menu', 'doms_foreign_rates_menu');



/* Function to show option page */
function doms_foreign_rates_options()
{
  if (!current_user_can('manage_options')) {
    wp_die(__('You do not have sufficient permissions to access this page.'));
  }
?>
  <div class="wrap">
    <h1><?php _e('Foreign Rates Options', 'doms-rates'); ?></h1>
    <p><?php _e('Here you can set the options to Foreign Rates to show', 'doms-rates'); ?></p>
    <form method="post" action="options.php">
      <?php settings_fields('doms-foreign-rates-group'); ?>
      <?php do_settings_sections('options-general.php?page=doms-foreign-rates'); ?>
      <?php submit_button(); ?>
    </form>
  </div>
<?php
}

/* Register Settings */
function register_doms_foreign_rates_settings()
{
  register_setting(
    'doms-foreign-rates-group',
    'foreign_rate_base',
  );

  register_setting(
    'doms-foreign-rates-group',
    'foreign_rate_currencies',
  );

  register_setting(
    'doms-foreign-rates-group',
    'foreign_rate_categories',
  );

  register_setting(
    'doms-foreign-rates-group',
    'foreign_rate_enabled',
  );

  add_settings_section(
    'doms_foreign_rates_section',
    __('Currency Exchange Rate', 'doms-rates'),
    'doms_foreign_rates_section',
    'options-general.php?page=doms-foreign-rates'
  );

  add_settings_field(
    'doms-foreign-rates-base',
    __('Select Base Rate', 'doms-rates'),
    'doms_foreign_rates_base',
    'options-general.php?page=doms-foreign-rates',
    'doms_foreign_rates_section'
  );

  add_settings_field(
    'doms-foreign-rates-currencies',
    __('Select Currencies', 'doms-rates'),
    'doms_foreign_rates_currencies',
    'options-general.php?page=doms-foreign-rates',
    'doms_foreign_rates_section'
  );

  add_settings_field(
    'doms-foreign-rates-categories',
    __('Select in which category it will be displayed', 'doms-rates'),
    'doms_foreign_rates_categories',
    'options-general.php?page=doms-foreign-rates',
    'doms_foreign_rates_section'
  );

  add_settings_field(
    'doms-foreign-rates-enabled',
    __('Enable/disable to display box in posts.', 'doms-rates'),
    'doms_foreign_rates_enabled',
    'options-general.php?page=doms-foreign-rates',
    'doms_foreign_rates_section'
  );
}

/* Section function */
function doms_foreign_rates_section($arg)
{
  echo '<p>Title: ' . $arg['title'] . '</p>';
}

/* Sanitize settings */
function doms_sanitize_text_field($input)
{
  $output = sanitize_text_field($input);
  return $output;
}

/* Settings Fields Function */
function doms_foreign_rates_base()
{
  $base_rate = esc_attr(get_option('foreign_rate_base', 'USD'));
  $fetchAPI  = fetch_save_api();
  $rates     = $fetchAPI->rates;
?>
  <select name="foreign_rate_base" id="foreign_rate_base_id">
    <?php
    foreach ($rates as $key => $value) {
    ?>
      <option value="<?php echo $key; ?>" <?php selected($base_rate, $key); ?>><?php echo $key; ?></option>
    <?php
    }
    ?>
  </select>
  <?php
}

/* Currencies */
function doms_foreign_rates_currencies()
{
  $fetchCurrencies = fetch_save_api();
  $currencies = $fetchCurrencies->rates;
  $currency = get_option('foreign_rate_currencies', []);

  foreach ($currencies as $key => $value) {
    $toChecked = isset($currency[$key]) ? (array) $currency[$key] : [];
  ?>
    <label style="padding-right: 1rem;"> <?php echo $key; ?> <input type="checkbox" name="<?php echo 'foreign_rate_currencies[' . $key . ']'; ?>" id="<?php echo 'foreign_rate_currencies_' . $key; ?>" value="<?php echo $value; ?>" <?php checked(in_array((string)$value, $toChecked), 1) ?>></label>
  <?php
  }
}

/* Select Categories */
function doms_foreign_rates_categories()
{
  $currentCats = get_option('foreign_rate_categories', 'currency');
  $args = array(
    'orderby' => 'name',
    'order'   => 'ASC'
  );
  $categories = get_categories($args);
  ?>
  <select name="foreign_rate_categories" id="foreign_rate_categories_id">
    <?php
    foreach ($categories as $cat) {
    ?>
      <option value="<?php echo $cat->name; ?>" <?php selected($currentCats, $cat->name); ?>><?php echo $cat->name; ?></option>
    <?php
    }
    ?>
  </select>
<?php
}

/* Enable currency box */
function doms_foreign_rates_enabled()
{
  $isEnabled = get_option('foreign_rate_enabled', 0);
?>
  <label style="padding-right: 1rem;"> <?php _e('Enable to show in Posts','doms-rates'); ?> <input type="checkbox" name="<?php echo 'foreign_rate_enabled'; ?>" id="<?php echo 'foreign_rate_enabled_id'; ?>" value="1" <?php checked($isEnabled, 1) ?> ></label>
<?php
}
