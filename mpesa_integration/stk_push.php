<?php
include 'mpesa_config.php';
include 'get_token.php';

$phone = $_POST['phone'];
$amount = $_POST['amount'];

$timestamp = date('YmdHis');
$password = base64_encode(BUSINESS_SHORTCODE . PASSKEY . $timestamp);

$url = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';

$data = [
    "BusinessShortCode" => BUSINESS_SHORTCODE,
    "Password" => $password,
    "Timestamp" => $timestamp,
    "TransactionType" => "CustomerPayBillOnline",
    "Amount" => $amount,
    "PartyA" => $phone,
    "PartyB" => BUSINESS_SHORTCODE,
    "PhoneNumber" => $phone,
    "CallBackURL" => CALLBACK_URL,
    "AccountReference" => "Order123",
    "TransactionDesc" => "Payment for Order123"
];

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_HTTPHEADER, ["Authorization: Bearer $accessToken", "Content-Type: application/json"]);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($curl);
curl_close($curl);

echo $response;
?>
