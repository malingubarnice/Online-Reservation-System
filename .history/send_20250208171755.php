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
            <p><strong>Name:</strong> {$_POST["customer-name"]}</p>
            <p><strong>Email:</strong> {$_POST["contact-info"]}</p>
            <p><strong>Date:</strong> {$_POST["date"]}</p>
            <p><strong>Time:</strong> {$_POST["time"]}</p>
            <p><strong>Party Size:</strong> {$_POST["party-size"]}</p>
            <p><strong>Special Requests:</strong> {$_POST["special-requests"]}</p>
            <p><strong>Selected Table:</strong> {$_POST["selected-table"]}</p>
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
