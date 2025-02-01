<?php
$mpesaResponse = file_get_contents('php://input');
file_put_contents("mpesa_response.json", $mpesaResponse);

$responseData = json_decode($mpesaResponse, true);
if (isset($responseData["Body"]["stkCallback"]["ResultCode"])) {
    $resultCode = $responseData["Body"]["stkCallback"]["ResultCode"];

    if ($resultCode == 0) {
        $mpesaReceiptNumber = $responseData["Body"]["stkCallback"]["CallbackMetadata"]["Item"][1]["Value"];
        $amountPaid = $responseData["Body"]["stkCallback"]["CallbackMetadata"]["Item"][0]["Value"];

        $db = new mysqli("localhost", "root", "", "coppers_ivy");
        $stmt = $db->prepare("INSERT INTO payments (mpesa_receipt, amount) VALUES (?, ?)");
        $stmt->bind_param("sd", $mpesaReceiptNumber, $amountPaid);
        $stmt->execute();
        $stmt->close();
        $db->close();
    }
}
?>
