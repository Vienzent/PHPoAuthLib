<?php

/**
 * Example of retrieving an authentication token of the Shopify service
 * PHP version 5.6
 * @author     Vienzent Jan Lugar <vienzent.0000@gmail.com>
 * @copyright  Copyright (c) 2016 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

use OAuth\Common\Consumer\ShopifyCredentials;
use OAuth\Common\Storage\Session;
use OAuth\OAuth2\Service\Shopify;

/**
 * Bootstrap the example
 */
require_once __DIR__ . '/bootstrap.php';

// Session storage
$storage = new Session();

// Setup the credentials for the requests
$credentials = new ShopifyCredentials(
    $servicesCredentials['shopify']['key'],
    $servicesCredentials['shopify']['secret'],
    $currentUri->getAbsoluteUri(),
    $servicesCredentials['shopify']['shop_domain']
);

// Instantiate the Shopify service using the credentials, http client and storage mechanism for the token
/** @var $shopifyService Shopify */
$shopifyService = $serviceFactory->createService('Shopify', $credentials, $storage, array(''));

if (!empty($_GET['code'])) {

    // This was a callback request from Shopify, get the token
    $token = $shopifyService->requestAccessToken($_GET['code']);

    // Send a request with it
    $result = json_decode($shopifyService->request('orders.json'), true);

    // Show some of the resultant data
    var_dump($result);
    die();

} elseif (!empty($_GET['go']) && $_GET['go'] === 'go') {
    $url = $shopifyService->getAuthorizationUri();
    header('Location: ' . $url);
} else {
    $url = $currentUri->getRelativeUri() . '?go=go';
    print "<a href='$url'>Login with Shopify!</a>";
}
