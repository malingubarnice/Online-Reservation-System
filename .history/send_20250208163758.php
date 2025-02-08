<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

if(isset($_POST["send"])){
    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->Host ='stmp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'malingubarnice@gmail.com';
    $mail->Password = 'olxf hiln uxom xenr';
    $mail->SMTPSecure = 'ssl';
    $mail->Port = 465;


    $mail->setFrom('malingubarnice@gmail.com');

    $mail->addAddress($_POST["email"]);

    $mail->isHTML(true);

    $mail->Subject = $_POST["Subject"];
    $mail->Body = $_POST["message"];

    $mail->send();

    echo"
    <script> 
    alert('Sent Successfully');
    document.location.href = 'index.html';
    </script>
    ";

}

?>