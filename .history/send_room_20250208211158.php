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
        $mail->Host = 'smtp.gmail.com'; // SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'malingubarnice@gmail.com'; // Your Gmail address
        $mail->Password = 'olxf hiln uxom xenr'; // Use an App Password instead
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        // Sender details
        $mail->setFrom('malingubarnice@gmail.com', 'Coppers Ivy Reservation Team');

        // Recipient email
        $recipientEmail = $_POST["contact-info"]; // User's contact email
        if (empty($recipientEmail)) {
            throw new Exception("No recipient email provided.");
        }
        $mail->addAddress($recipientEmail); // Add recipient

        // Email content
        $mail->isHTML(true);
        $mail->Subject = "Room Booking Confirmation";

        // Construct message body
        $roomName = $_POST["room_name"] ?? "Not specified";
        $checkInDate = $_POST["check_in_date"] ?? "Not specified";
        $checkOutDate = $_POST["check_out_date"] ?? "Not specified";
        $guestCount = $_POST["guest_count"] ?? "Not specified";
        $price = $_POST["price"] ?? "TBD"; // Ensure price is coming from the form

        $message = "
        <h2>Reservation Details</h2>
        <p><strong>Name:</strong> {$_POST["customer-name"]}</p>
        <p><strong>Email:</strong> {$_POST["contact-info"]}</p>
        <p><strong>Date:</strong> {$_POST["date"]}</p>
        <p><strong>Time:</strong> {$_POST["time"]}</p>
        <p><strong>Party Size:</strong> {$_POST["party-size"]}</p>
        <p><strong>Special Requests:</strong> {$_POST["special-requests"]}</p>
        <p><strong>Selected Table:</strong> {$_POST["selected-table"]}</p>
        <p><strong>Room:</strong> {$roomName}</p> 
        <p><strong>Price:</strong> {$price}</p> 
        <p>Thank you for booking with Coppers Ivy!</p>
       ";


        $mail->Body = $message;

        // Send email
        $mail->send();

        // Notify user
        echo "
        <script> 
        alert('Room booking email sent successfully!');
        document.location.href = 'index.html'; // Redirect to homepage or any other page
        </script>
        ";
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>
