<?php
include 'mpesa_config.php';

$credentials = base64_encode(CONSUMER_KEY . ":" . CONSUMER_SECRET);
$url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_HTTPHEADER, ["Authorization: Basic $credentials"]);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($curl);
curl_close($curl);

$accessToken = json_decode($response)->access_token;
echo $accessToken;
?>
