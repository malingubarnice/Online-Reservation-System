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
        $mail->Host = 'smtp.gmail.com'; // Corrected spelling
        $mail->SMTPAuth = true;
        $mail->Username = 'malingubarnice@gmail.com';
        $mail->Password = 'olxf hiln uxom xenr'; // Use an App Password instead
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        // Sender
        $mail->setFrom('malingubarnice@gmail.com', 'Coppers Ivy Reservation Team');

        // Recipient
        $mail->addAddress($_POST["contact-info"]); // Using the email from form

        // Email content
        $mail->isHTML(true);
        $mail->Subject = "Reservation Confirmation";
        
        // Construct message body
        $message = "
         <h2>Reservation Details</h2>
         <p><strong>Room:</strong> {$_POST["room_name"]}</p> 
         <p><strong>Check-in Date:</strong> {$_POST["check_in_date"]}</p>
         <p><strong>Check-out Date:</strong> {$_POST["check_out_date"]}</p>
         <p><strong>Number of Guests:</strong> {$_POST["guest_count"]}</p>
         <p><strong>Contact Information:</strong> {$_POST["contact-info"]}</p>
         <p><strong>Total Price:</strong> {$_POST["price"]}</p> 
         <p>Thank you for booking with Coppers Ivy!</p>
         ";


        $mail->Body = $message;

        // Send email
        $mail->send();

        echo "
        <script> 
        alert('Reservation request sent successfully!');
        document.location.href = 'index.html';
        </script>
        ";
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>
