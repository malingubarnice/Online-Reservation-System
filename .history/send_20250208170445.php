use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

if(isset($_POST["send"])) {
    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com'; // Fixed typo (stmp → smtp)
    $mail->SMTPAuth = true;
    $mail->Username = 'malingubarnice@gmail.com';
    $mail->Password = 'olxf hiln uxom xenr'; // Ensure security (use environment variables instead)
    $mail->SMTPSecure = 'ssl';
    $mail->Port = 465;

    $mail->setFrom('malingubarnice@gmail.com');
    $mail->addAddress($_POST["contact-info"]); // Ensure correct field name

    $mail->isHTML(true);
    $mail->Subject = "Table Reservation Confirmation";

    // Get selected table safely
    $selectedTable = isset($_POST["selected-table"]) ? $_POST["selected-table"] : "Not selected";

    // Email body
    $mail->Body = "
        <p><strong>Selected Table:</strong> " . $selectedTable . "</p>
        <p><strong>Customer Name:</strong> " . $_POST["customer-name"] . "</p>
        <p><strong>Email:</strong> " . $_POST["contact-info"] . "</p>
        <p><strong>Party Size:</strong> " . $_POST["party-size"] . "</p>
        <p><strong>Check-in Date:</strong> " . $_POST["date"] . "</p>
        <p><strong>Check-in Time:</strong> " . $_POST["time"] . "</p>
        <p><strong>Special Requests:</strong> " . $_POST["special-requests"] . "</p>
    ";

    if($mail->send()) {
        echo "
        <script> 
        alert('Reservation Sent Successfully!');
        document.location.href = 'index.html';
        </script>
        ";
    } else {
        echo "Error: " . $mail->ErrorInfo;
    }
}
