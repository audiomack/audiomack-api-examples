<?php

require_once 'vendor/autoload.php';
require_once 'config.php';

$callback = 'http://localhost:3030/callback.php';

$error        = '';
$errorContent = '';
$success      = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $httpRequest = new HTTP_Request2(null, null, array('ssl_verify_peer' => false));
    $consumer = new HTTP_OAuth_Consumer($consumerKey, $consumerSecret);
    $consumer->accept($httpRequest);

    try {
        $consumer->getRequestToken($apiUrl . '/request_token', $callback);

        // Store tokens
        $_SESSION['token']        = $consumer->getToken();
        $_SESSION['token_secret'] = $consumer->getTokenSecret();

        $url = $consumer->getAuthorizeUrl($authorizeUrl);
        header("Location: $url");
        exit;
    } catch (Exception $e) {
        $error = $e->getMessage();
        $errorContent = $consumer->getLastResponse()->getBody();
    }
}

include 'template.php';
