<?php
function fetch_save_api() {
  // Validate if transient doesn't exist then set transient
  if (false === $doms_exchange_results = get_transient('doms_exchange')) {
    //* Request API URL
    $request = wp_remote_get('https://api.exchangeratesapi.io/latest?base=USD');

    //* Validate it and ensure that we got back a response that we expected.
    if (is_wp_error($request)) {
      return false; // Bail early
    }

    //* Retrieving the data
    $body = wp_remote_retrieve_body($request);

    //* Translate the JSON into a format we can read
    $rates = json_decode($body);
    $doms_exchange_results = set_transient('doms_exchange', $rates, 86400);
  }

  return $doms_exchange_results;
}
