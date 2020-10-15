<?php

function doms_query_currency($content) {


  if (is_single() && 'post' == get_post_type()  ) {
    // Query Currency category, EUR tag and Rate custom field greater than 1 and post date older 1 week.
    // The Query
    $args = [
      'post_type'     => 'post',
      'category_name' => 'currency',
      'tag'           => 'eur',
      'meta_query'    => array(
        array(
          'key'            => 'rate',
          'meta_value_num' => 1,
          'compare'        => '>',
        ),
      ),
      'date_query' => array(
        array(
          'column' => 'post_date_gmt',
          'before' => '1 week ago',
        )
      ),
      'posts_per_page' => -1,
    ];
    $the_query = new WP_Query($args);
    $add_post = '';
    if ($the_query->have_posts()) {
      while ($the_query->have_posts()) {
        $the_query->the_post();
        $add_post .= '<h5>' . get_the_title() . '</h5>';
      }
    } else {
      $add_post = _e('Posts Not Founds!', 'doms-rates');
    }
    /* Restore original Post Data */
    wp_reset_postdata();

    $content .= $add_post;


    /*
   *  Is "foreign_rate_enabled" is true, Currency Foreign Rate it will be displayed
   */
    $fetchAPI         = fetch_save_api();
    $optionBaseRate   = get_option('foreign_rate_base');
    $optionCurrencies = get_option('foreign_rate_currencies');
    $optionDisplayIn  = get_option('foreign_rate_categories');
    $optionIsEnable   = get_option('foreign_rate_enabled');
    $currentValue     = '';
    $baseRate = $fetchAPI->rates->$optionBaseRate;

    if ((boolean)$optionIsEnable) {
      $optionDisplayIn;
      if (in_category($optionDisplayIn)) {
        $currentValue = '<div class="card my-3"><div class="card-body">';
        $currentValue .= __('Current value for <strong>$1 ', 'doms-rates');
        $currentValue .= $optionBaseRate . __('</strong> is: ', 'doms-rates') . '<br>';

        foreach ($optionCurrencies as $key => $value) {
          $exchange = $value / $baseRate;
          $currentValue .= '$' . $exchange . ' <strong>' . $key . '</strong><br>';
        }

        $currentValue .= '</div></div>';

      }
    }

    $content .= $currentValue;
  }

  return $content;

}

add_filter('the_content', 'doms_query_currency', 100, 1);
