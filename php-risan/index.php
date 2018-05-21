<?php

// Includes the Composer autoload file.
require 'vendor/autoload.php';

// Start the session.
session_start();

$audiomackApiBase = 'https://api.audiomack.com/v1';

// Create an instance of Risan\OAuth1\OAuth1 class.
$oauth1 = Risan\OAuth1\OAuth1Factory::create([
    'client_credentials_identifier' => 'YOUR_AUDIOMACK_KEY',
    'client_credentials_secret'     => 'YOUR_AUDIOMACK_SECRET',
    'temporary_credentials_uri'     => $audiomackApiBase . '/request_token',
    'authorization_uri'             => 'https://www.audiomack.com/oauth/authenticate',
    'token_credentials_uri'         => $audiomackApiBase . '/access_token',
    'callback_uri'                  => 'http://localhost:3030/index.php',
]);

if (isset($_SESSION['token_credentials'])) {
    // Get back the previosuly obtain token credentials (step 3).
    $tokenCredentials = unserialize($_SESSION['token_credentials']);
    $oauth1->setTokenCredentials($tokenCredentials);

    // STEP 4: Retrieve the user's details.
    // It will return the Psr\Http\Message\ResponseInterface instance.
    $response = $oauth1->request('GET', $audiomackApiBase . '/user');

    // Convert the response to array and display it.
    var_dump(json_decode($response->getBody()->getContents(), true));
} elseif (isset($_GET['oauth_token']) && isset($_GET['oauth_verifier'])) {
    // Get back the previosuly generated temporary credentials (step 1).
    $temporaryCredentials = unserialize($_SESSION['temporary_credentials']);
    unset($_SESSION['temporary_credentials']);

    // STEP 3: Obtain the token credentials (also known as access token).
    $tokenCredentials = $oauth1->requestTokenCredentials($temporaryCredentials, $_GET['oauth_token'], $_GET['oauth_verifier']);

    // Store the token credentials in session for later use.
    $_SESSION['token_credentials'] = serialize($tokenCredentials);

    // this basically just redirecting to the current page so that the query string is removed.
    header('Location: ' . (string) $oauth1->getConfig()->getCallbackUri());
    exit();
} else {
    try {
        // STEP 1: Obtain a temporary credentials (also known as the request token)
        $temporaryCredentials = $oauth1->requestTemporaryCredentials();

        // Store the temporary credentials in session so we can use it on step 3.
        $_SESSION['temporary_credentials'] = serialize($temporaryCredentials);

        // STEP 2: Generate and redirect user to authorization URI.
        $authorizationUri = $oauth1->buildAuthorizationUri($temporaryCredentials);
        header("Location: {$authorizationUri}");
        exit();
    } catch (Exception $e) {
        echo "Error requesting temporary credentials: " . $e->getMessage();
    }
}
