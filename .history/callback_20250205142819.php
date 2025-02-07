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
$MerchantRequestID = mysqli_real_escape_string($db, $data->Body->stkCallback->MerchantRequestID);
$CheckoutRequestID = mysqli_real_escape_string($db, $data->Body->stkCallback->CheckoutRequestID);
$ResultCode = mysqli_real_escape_string($db, $data->Body->stkCallback->ResultCode);
$ResultDesc = mysqli_real_escape_string($db, $data->Body->stkCallback->ResultDesc);
$Amount = mysqli_real_escape_string($db, $data->Body->stkCallback->CallbackMetadata->Item[0]->Value);
$TransactionId = mysqli_real_escape_string($db, $data->Body->stkCallback->CallbackMetadata->Item[1]->Value);
$UserPhoneNumber = mysqli_real_escape_string($db, $data->Body->stkCallback->CallbackMetadata->Item[4]->Value);


// Check if the transaction was successful
if ($ResultCode == 0) {
    // Prepare the SQL query
    $query = "INSERT INTO transactions (MerchantRequestID, CheckoutRequestID, ResultCode, Amount, MpesaReceiptNumber, PhoneNumber) 
              VALUES ('$MerchantRequestID', '$CheckoutRequestID', '$ResultCode', '$Amount', '$TransactionId', '$UserPhoneNumber')";
    
    // Execute the query and check for errors
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
