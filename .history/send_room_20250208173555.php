<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mail = new PHPMailer(true);

    try {
        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'malingubarnice@gmail.com';
        $mail->Password = 'olxf hiln uxom xenr'; // Use an App Password
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        // Sender Information
        $mail->setFrom('malingubarnice@gmail.com', 'Coppers Ivy Reservations');

        // Recipient Email
        $recipientEmail = $_POST["contact-info"] ?? "";
        if (empty($recipientEmail)) throw new Exception("No recipient email provided.");

        $mail->addAddress($recipientEmail);
        $mail->isHTML(true);
        $mail->Subject = "Room Booking Confirmation";

        // Capture form data
        $roomName = $_POST["room_name"] ?? "Not specified";
        $checkIn = $_POST["check_in_date"] ?? "Not specified";
        $checkOut = $_POST["check_out_date"] ?? "Not specified";
        $guestCount = $_POST["guest_count"] ?? "Not specified";
        $price = $_POST["price"] ?? "TBD";

        $mail->Body = "
            <h2>Your Room Booking Details</h2>
            <p><strong>Room:</strong> $roomName</p>
            <p><strong>Check-in Date:</strong> $checkIn</p>
            <p><strong>Check-out Date:</strong> $checkOut</p>
            <p><strong>Number of Guests:</strong> $guestCount</p>
            <p><strong>Total Price:</strong> $price</p>
            <p>Thank you for booking with Coppers Ivy!</p>
        ";

        // Send Email
        $mail->send();

        echo "
        <script> 
        alert('Room booking email sent successfully!');
        document.location.href = 'index.html';
        </script>
        ";

    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>
