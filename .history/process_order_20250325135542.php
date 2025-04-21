<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mail = new PHPMailer(true);

    try {
        // SMTP settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'malingubarnice@gmail.com';
        $mail->Password = 'olxf hiln uxom xenr'; // Use an App Password instead
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        // Sender
        $mail->setFrom('malingubarnice@gmail.com', 'Coppers Ivy Order Team');

        // Recipient (Sanitize email input)
        $customerEmail = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
        if (!filter_var($customerEmail, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format.");
        }
        $mail->addAddress($customerEmail);

        // Get customer details
        $phone = htmlspecialchars($_POST["phone"]);
        $totalAmount = htmlspecialchars($_POST["amount"]);
        $deliveryCost = htmlspecialchars($_POST["delivery_cost"]);

        // Get ordered items
        $orderDetails = "";
        if (isset($_POST["order_items"])) {
          $orderItems = json_decode($_POST["order_items"], true); // Decode JSON

          if (is_array($orderItems) && !empty($orderItems)) {
             foreach ($orderItems as $item) {
                 $name = htmlspecialchars($item["name"] ?? "Unknown Item");
                 $price = htmlspecialchars($item["price"] ?? "0");
                 $orderDetails .= "<p>$name - Ksh $price</p>";
             }
        } else {
              $orderDetails = "<p>No items ordered.</p>";
        }
        } else {
             $orderDetails = "<p>No items ordered.</p>";
         }


        // Construct email content
        $message = "
         <h2>Order Confirmation</h2>
         <p><strong>Phone:</strong> $phone</p>
         <h3>Ordered Items:</h3>
         $orderDetails
         <p><strong>Delivery Cost:</strong> Ksh $deliveryCost</p>
         <p><strong>Total Amount:</strong> Ksh $totalAmount</p>
         <p>Your order is being processed. You will receive updates soon.</p>
         <p>Thank you for ordering from Coppers Ivy!</p>
         ";

        // Email settings
        $mail->isHTML(true);
        $mail->Subject = "Your Order Confirmation";
        $mail->Body = $message;

        // Send email
        $mail->send();

        echo "
        <script> 
        alert('Order confirmation email sent successfully!');
        document.location.href = 'index.html';
        </script>
        ";
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>
