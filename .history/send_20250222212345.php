<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

function sendReservationEmail($contact_info, $customer_name, $date, $time, $party_size, $special_requests) {
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
        $mail->setFrom('malingubarnice@gmail.com', 'Coppers Ivy Reservation Team');

        // Recipient (Sanitize email input)
        $customerEmail = filter_var($contact_info, FILTER_SANITIZE_EMAIL);
        if (!filter_var($customerEmail, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format.");
        }
        $mail->addAddress($customerEmail);

        // Construct email content
        $message = "
         <h2>Reservation Confirmation</h2>
         <p><strong>Name:</strong> " . htmlspecialchars($customer_name) . "</p>
         <p><strong>Date:</strong> " . htmlspecialchars($date) . "</p>
         <p><strong>Time:</strong> " . htmlspecialchars($time) . "</p>
         <p><strong>Party Size:</strong> " . htmlspecialchars($party_size) . "</p>
         <p><strong>Special Requests:</strong> " . nl2br(htmlspecialchars($special_requests)) . "</p>
         <p>Thank you for reserving with Coppers Ivy!</p>
         ";

        // Email settings
        $mail->isHTML(true);
        $mail->Subject = "Your Reservation Confirmation";
        $mail->Body = $message;

        // Send email
        $mail->send();
        return true; // Email sent successfully
    } catch (Exception $e) {
        error_log("Email sending failed: " . $e->getMessage());
        return false; // Email sending failed
    }
}
?>