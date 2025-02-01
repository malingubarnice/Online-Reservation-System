<?php
$mpesaReceipt = $_POST['mpesa_receipt'];

$db = new mysqli("localhost", "root", "", "coppers_ivy");
$result = $db->query("SELECT * FROM payments WHERE mpesa_receipt = '$mpesaReceipt'");

if ($result->num_rows > 0) {
    echo "Payment verified! Processing order...";
} else {
    echo "Payment not found. Please try again.";
}
?>
