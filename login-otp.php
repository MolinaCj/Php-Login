<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Load Composer's autoloader

session_start();

if (isset($_POST['email'])) {
    include 'db.php';

    $email = $_POST['email'];
    
    // Check if the user exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $user_id = $user['id'];

        // Generate OTP
        $otp = rand(100000, 999999);
        $otp_expires_at = date('Y-m-d H:i:s', strtotime('+3 minutes'));

        // Insert or update OTP in the database
        $stmt = $conn->prepare("INSERT INTO otp_verification (user_id, otp, otp_expires_at) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE otp = ?, otp_expires_at = ?");
        $stmt->bind_param("issss", $user_id, $otp, $otp_expires_at, $otp, $otp_expires_at);
        $stmt->execute();

        // Send OTP via email
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->SMTPDebug = 0;
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'akosiclent07@gmail.com'; // sender email
            $mail->Password = 'vach yluz ojex lxsx'; // sender app password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            $mail->setFrom('akosiclent07@gmail.com', 'Clent James Molina');

            // Add Recipient
            $mail->addAddress($email);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'OTP Code';
            $mail->Body    = 'Your OTP code is ' . $otp;

            $mail->send();
            // Redirect to verify OTP page
            echo "<script>alert('OTP has been sent to your email.'); window.location.href='verify-login-otp.php?email=" . $email . "';</script>";
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        echo 'Email not found in our system.';
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Verify OTP</title>
    <!-- CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-3">
        <div class="card">
            <div class="card-header text-center">
                Request OTP
            </div>
            <div class="card-body">
                <form method="post" action="login-otp.php">
                    <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Request OTP</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

