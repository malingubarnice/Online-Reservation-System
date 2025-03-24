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

        // Construct email content
        $message = "
         <h2>Order Confirmation</h2>
         <p><strong>Phone:</strong> " . htmlspecialchars($_POST["phone"]) . "</p>
         <p><strong>Total Amount:</strong> Ksh " . htmlspecialchars($_POST["amount"]) . "</p>
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
