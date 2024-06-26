<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if(isset($_POST['password-reset-token']) && $_POST['email'])
{
    include "db.php";

    // Prepare statement to avoid SQL injection
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $_POST['email']);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows < 1)
    {
        // Generate a token
        $token = md5($_POST['email']).rand(10,9999);

        // Insert new user
        $stmt = $conn->prepare("INSERT INTO users (name, email, email_verification_link, password) VALUES (?, ?, ?, ?)");
        $password_hashed = md5($_POST['password']);
        $stmt->bind_param("ssss", $_POST['name'], $_POST['email'], $token, $password_hashed);
        $stmt->execute();

        // Create verification link
        $link = "<a href='http://localhost/PHP Login/verification-email.php?key=".$_POST['email']."&token=".$token."'>Click and Verify Email</a>";

        // Load Composer's autoloader
        require 'vendor/autoload.php';

        // Create an instance of PHPMailer
        $mail = new PHPMailer(true);

        try {

            $mail->SMTPDebug = 0;

            // Fetch recipient's email and name from the database
            $stmt = $conn->prepare("SELECT email, name FROM users WHERE email = ?");
            $stmt->bind_param("s", $_POST['email']);
            $stmt->execute();
            $result = $stmt->get_result();
            $recipient = $result->fetch_assoc();

            if (!$recipient) {
                echo "User not found in the database.";
                exit;
            }

            //Server settings
            $mail->CharSet = "utf-8";
            $mail->isSMTP();
            $mail->SMTPAuth = true;
            $mail->Username = 'akosiclent07@gmail.com'; // sender email
            $mail->Password = 'vach yluz ojex lxsx';    // sender app password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Host = 'smtp.gmail.com';
            $mail->Port = 587;
            $mail->setFrom('akosiclent07@gmail.com', 'Clent James Molina'); // sender email and name

            // Add recipient
            //$mail->addAddress($_POST['email'], $_POST['name']); // Replace with the recipient email and name

            // Add recipient from the database
            $mail->addAddress($recipient['email'], $recipient['name']); // Use the fetched recipient email and name

            // Content
            $mail->isHTML(true);                                  
            $mail->Subject = 'Email Verification';
            $mail->Body    = 'Click On This Link to Verify Email: ' . $link;

            // Send the email
            $mail->send();
            echo 'Check your email box and click on the email verification link.';
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }

    } else {
        echo "You have already registered with us. Check your email box and verify email.";
    }
}

