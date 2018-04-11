<?php

require_once 'vendor/autoload.php';
require_once 'config.php';

$consumer = new HTTP_OAuth_Consumer(
    $consumerKey, $consumerSecret, $_SESSION['token'],
    $_SESSION['token_secret']
);
$consumer->accept($httpRequest);

$error         = '';
$errorContent  = '';
$success       = false;

if (isset($_GET['oauth_verifier'])) {
    try {
        $consumer->getAccessToken($apiUrl . '/access_token', $_GET['oauth_verifier']);

        // Store tokens
        $_SESSION['token']        = $consumer->getToken();
        $_SESSION['token_secret'] = $consumer->getTokenSecret();

        // $response is an instance of HTTP_OAuth_Consumer_Response
        $response = $consumer->sendRequest($apiUrl . '/user', array(), 'GET');
        $success = true;
    } catch (Exception $e) {
        $error        = $e->getMessage();
        $errorContent = $consumer->getLastResponse()->getBody();
    }
} else {
    $error = 'oauth_verifier was not present';
}

include 'template.php';
