<?php
session_start();
include 'db.php';

if (isset($_POST['regenerate'])) {
    $email = $_SESSION['email']; // Retrieve email from session

    // Generate new OTP
    $otp = rand(100000, 999999);
    $otp_expires_at = date('Y-m-d H:i:s', strtotime('+3 minutes')); // OTP expires in 3 minutes

    // Save OTP and expiration time in the database
    $stmt = $conn->prepare("INSERT INTO otp_verification (id, otp, otp_expires_at) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE otp = ?, otp_expires_at = ?");
    $stmt->bind_param("iisss", $_SESSION['id'], $otp, $otp_expires_at, $otp, $otp_expires_at);
    $stmt->execute();

    // Send OTP to the user's email
    require 'vendor/autoload.php';
    $mail = new PHPMailer\PHPMailer\PHPMailer();
    // Server setting
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'akosiclent07@gmail.com'; // Sender email
    $mail->Password = 'vach yluz ojex lxsx'; // Sender app password
    $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom('akosiclent07@gmail.com', 'Clent James Molina');
    $mail->addAddress($email);

    $mail->isHTML(true);
    $mail->Subject = 'Your New OTP Code';
    $mail->Body = "Your new OTP code is: $otp";

    if ($mail->send()) {
        echo 'A new OTP has been sent to your email.';
    } else {
        echo 'Mailer Error: ' . $mail->ErrorInfo;
    }

    // Redirect back to the verification page with a message
    $_SESSION['msg'] = $msg;
    header("Location: verify-login-otp.php");
    exit();
} else {
    // If someone accesses this script directly without POST data, handle it appropriately
    header("Location: verify-login-otp.php"); // Redirect to verification page
    exit();
}
