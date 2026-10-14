<?php

$apiUrl         = 'https://dcf.aws.audiomack.com/v1';
$authorizeUrl   = 'https://am5-dev-stg.aws.audiomack.com/oauth/authenticate';
$consumerKey    = 'thirdpartyoauth';
$consumerSecret = '5eb6aa218ede2ac8fd6469ef95dc0592';
$callback       = 'https://am-oauth-explorer-dev126.aws.audiomack.com/callback.php';

$httpRequest = new HTTP_Request2(null, null, array('ssl_verify_peer' => false));

session_name('audiomack-third-party-auth');
session_start();
