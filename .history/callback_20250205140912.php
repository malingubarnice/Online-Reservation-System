<?php
// Database connection
$host = "localhost"; 
$username = "root"; 
$password = ""; 
$database = "coppers_ivy";

$db = new mysqli($host, $username, $password, $database);

// Check connection
if ($db->connect_error) {
    die("Database connection failed: " . $db->connect_error);
}

// Set content-type for JSON response
header("Content-Type: application/json");

// Receive response from STK Push
$stkCallbackResponse = file_get_contents('php://input');

// Log the response for debugging purposes
$logFile = "Mpesastkresponse.json";
$log = fopen($logFile, "a");
fwrite($log, $stkCallbackResponse);
fclose($log);

// Decode the JSON response
$data = json_decode($stkCallbackResponse);

// Extract necessary data
$MerchantRequestID = $data->Body->stkCallback->MerchantRequestID;
$CheckoutRequestID = $data->Body->stkCallback->CheckoutRequestID;
$ResultCode = $data->Body->stkCallback->ResultCode;
$ResultDesc = $data->Body->stkCallback->ResultDesc;
$Amount = $data->Body->stkCallback->CallbackMetadata->Item[0]->Value;
$TransactionId = $data->Body->stkCallback->CallbackMetadata->Item[1]->Value;
$UserPhoneNumber = $data->Body->stkCallback->CallbackMetadata->Item[4]->Value;

// Check if the transaction was successful
if ($ResultCode == 0) {
    // Store transaction details in the database
    $query = "INSERT INTO transactions (MerchantRequestID, CheckoutRequestID, ResultCode, Amount, MpesaReceiptNumber, PhoneNumber) 
              VALUES ('$MerchantRequestID', '$CheckoutRequestID', '$ResultCode', '$Amount', '$TransactionId', '$UserPhoneNumber')";
    
    if (mysqli_query($db, $query)) {
        echo "Payment successful and transaction details stored!";
    } else {
        echo "Error storing transaction details: " . mysqli_error($db);
    }
} else {
    echo "Payment failed: " . $ResultDesc;
}

// Close database connection
$db->close();
?>
