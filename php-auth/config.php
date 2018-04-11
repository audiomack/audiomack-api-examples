<?php

$apiUrl         = 'https://www.audiomack.com/v1';
$authorizeUrl   = 'https://react.audiomack.com/oauth/authenticate';
$consumerKey    = 'consumerKey';
$consumerSecret = 'consumerSecret';

$httpRequest = new HTTP_Request2(null, null, array('ssl_verify_peer' => false));

session_name('audiomack-third-party-auth');
session_start();
