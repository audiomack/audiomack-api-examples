<?php

$apiUrl         = 'https://audiomack.com/v1';
$authorizeUrl   = 'https://audiomack.com/oauth/authenticate';
$consumerKey    = 'yourconsumerkey';
$consumerSecret = 'yourconsumersecret';
$callback       = 'https://localhost:8080/callback.php';

$httpRequest = new HTTP_Request2(null, null, array('ssl_verify_peer' => false));

session_name('audiomack-third-party-auth');
session_start();
