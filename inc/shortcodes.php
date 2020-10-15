<?php

/** Shortcodes
 * Create shortcode foreing_rates with attributes base and currencies which will display exchange rates for base currency and selected currencies

    example: [foreing_rates base=”EUR” currencies=”USD,CHF,CAD”]
    output: 1 USD = 0,86 EUR
        1 USD = 0,82 CHF
        1 USD = 1,34 CAD
 */

function doms_foreing_rates_shortcode ($atts = array() ) {
  extract(shortcode_atts(array(
    'base' => 'USD',
    'currencies' => 'EUR,CAD,MXN',
  ), $atts));

  $fetchAPI = fetch_save_api();
  $currencycard = '<div class="card my-3"><div class="card-body">';

  if(array_key_exists($base, $fetchAPI->rates) ) {
    $arrCurrencies = explode(',', $currencies);
    $baseRate = $fetchAPI->rates->$base;

    foreach ($arrCurrencies as $key => $value) {
      $currentExchange = $fetchAPI->rates->$value / $baseRate;
      $currencycard .= '1 ' . $base . ' = ' . $currentExchange . ' ' . $value . '<br>';
    }
  } else {
    $currencycard .= __('No Currency available!', 'doms-rates');
  }

  $currencycard .= '</div></div>';
  return $currencycard;
}
add_shortcode('foreign_rates', 'doms_foreing_rates_shortcode');
