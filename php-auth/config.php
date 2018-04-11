<?php

$apiUrl         = 'https://audiomack.test/v1';
$authorizeUrl   = 'https://react.audiomack.test/oauth/authenticate';
$consumerKey    = 'crowdedhouse';
$consumerSecret = '2749ba2f1d885e61e7b801bc14b8277e';

$httpRequest = new HTTP_Request2(null, null, array('ssl_verify_peer' => false));

session_name('audiomack-third-party-auth');
session_start();
