<?php

/**
 * Advanced Ads – Tracking – Event tracker
 *
 * Plugin Name:       Advanced Ads – Tracking
 * Plugin URI:        http://wpadvancedads.com/add-ons/tracking/
 * Description:       Track events from external pages.
 * Author:            Thomas Maier
 * Author URI:        https://wpadvancedads.com
 *
 * @return true   tracked event
 * @return false  untracked event; may try again later
 * @return null   fix me and do not try again; untracked
 * @return string unexpected error: may try again later or report
 *
 * Appropriate error logging will keep you informed.
 *
 * Usage:
 * 
 * $event = array(
 *   'id' => 'YOUR ID HERE', // any string you like to track the event with
 *   // ensure prices are floats/ doubles or format them using
 *   // .. somethings like `(double) sprintf('%.2f', $priceFloat);`
 *   // .. or `(double) sprintf('%d.%d', $priceInteger / 100, $priceInteger % 100);`
 *   # 'price' => $priceAsFloat, // price
 *   # 'priceCurrency' => 'USD', // 3-char-code
 *   # 'provision' => 10.00, // affiliate’s share
 *   # 'provisionCurrency' => 'EUR', // 3-char-code
 * );
 * 
 * // identification
 * $apiClientToken = '%%API_CLIENT_TOKEN%%';
 * $apiClientName = '%%API_CLIENT_NAME%%';
 * $apiTarget = '%%API_TARGET_URI%%';
 *
 * $response = include 'path/to/script.php'; // server path, not url path
 * if ($response !== true) {
 *   // false: API failed temporarily: may try again later
 *   // null: API rejected request/ requirements not met: try to resolve this!
 *   // string: API-Server or Proxy failed for unknown reason (maybe given in response)
 * }
 */

// basic settings
$apiAgent = '%%API_AGENT%%';
$apiVersion = '%%API_VERSION%%';
$apiHashMethod = '%%API_HASH_METHOD%%';
$apiAction = '%%API_ACTION%%';

// verify requirements
$hash_algos = array_flip(hash_algos());
if (!isset($hash_algos[$apiHashMethod]) || !function_exists('curl_init') || !function_exists('json_encode') || version_compare(PHP_VERSION, '5.2.0', '<=')) {
    error_log("advads tracking: requirements not met.", E_ERROR);
    return null;
}

// test input
if (!isset($event) || !is_array($event) || !isset($event['id'])) {
    @error_log("advads tracking requires \$event['id'] to be passed.");
    return false;
}

// generate event
$now = time();
$payload = json_encode($event);
$data['data'] = $payload;
$data['action'] = $apiAction;
$data['source'] = $apiClientName;
$data['time'] = $now;
$data['v'] = $apiVersion;

// generate signature
$signature = hash_hmac($apiHashMethod, "$apiClientName $now $payload", $apiClientToken);
$data['signature'] = $signature;

$url = $apiTarget;
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_USERAGENT, $apiAgent);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json', 'Content-Length: ' . strlen($payload)) );

// may contain error message, otherwise ignored
$response = curl_exec($ch);
$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// handle response
switch ((int) $status) {
    case 202: // accepted
        return true;

    case 401:
        @error_log("advads-tracking: not authorized.", E_ERROR);
        return null;

    case 400:
        @error_log("advads-tracking: malformed request.", E_ERROR);
        return null;

    case 503:
        @error_log("advads-tracking: temporary failure", E_NOTICE);
        return false;

    default:
        @error_log("advads-tracking: unexpected API fail [$status]: $response.", E_WARNING);
        return $response;
}

// fallback
return false;
