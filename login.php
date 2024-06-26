<?php
session_start();

require_once "db.php";

// Redirect logged-in users to dashboard
if(isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Validate email format
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $email_error = "Please enter a valid email";
    }

    // Validate password length
    if(strlen($password) < 8) {
        $password_error = "Password must be at least 8 characters";
    }

    // Query to check login credentials
    $result = mysqli_query($conn, "SELECT * FROM users WHERE email = '" . $email. "' AND password = '" . md5($password). "'");

    if(mysqli_num_rows($result) > 0) {
        // Fetch user details
        $row = mysqli_fetch_array($result);

        // Set session variables
        $_SESSION['user_id'] = $row['uid'];
        $_SESSION['user_name'] = $row['name'];
        $_SESSION['user_email'] = $row['email'];
        $_SESSION['user_mobile'] = $row['mobile'];

        // Redirect to OTP verification page
        header("Location: login-otp.php");
        exit();
    } else {
        $error_message = "Incorrect Email or Password!!!";
    }
}
  /*  $result = mysqli_query($conn, "SELECT * FROM users WHERE email = '" . $email. "' and password = '" . md5($password). "'");
    if(mysqli_num_rows($result) > 0) {
        if ($row = mysqli_fetch_array($result)) {
            // Generate OTP
            $otp = rand(100000, 999999);
            $otp_expiration = date('Y-m-d H:i:s', strtotime('+3 minutes')); // OTP expires in 3 minutes

            // Save OTP and expiration time in the database
            $stmt = $conn->prepare("INSERT INTO otp_verification (user_id, otp, otp_expires_at) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE otp = ?, otp_expires_at = ?");
            $stmt->bind_param("issss", $user_id, $otp, $otp_expires_at, $otp, $otp_expires_at);
            $stmt->execute();

            // Send OTP to the user's email
            require 'vendor/autoload.php';
            $mail = new PHPMailer\PHPMailer\PHPMailer();
            //Server setting
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'akosiclent07@gmail.com'; //sender email
            $mail->Password = 'vach yluz ojex lxsx'; //sender app password
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('akosiclent07@gmail.com', 'Clent James Molina');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Your OTP Code';
            $mail->Body = "Your OTP code is: $otp";

            if($mail->send()) {
                // Save email in session
                $_SESSION['email'] = $email;
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['user_name'] = $row['name'];
                $_SESSION['user_email'] = $row['email'];
                $_SESSION['user_mobile'] = $row['mobile'];

                // Redirect to OTP verification page
                header("Location: verify-login-otp.php");
            } else {
                echo 'Mailer Error: ' . $mail->ErrorInfo;
            }
        }
    } else {
        $error_message = "Incorrect Email or Password!!!";
    }
}*/
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Simple Login Form in PHP with Validation</title>
     <link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-lg-10">
                <div class="page-header">
                    <h2>Login Form in PHP with Validation</h2>
                </div>
                <p>Please fill all fields in the form</p>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">

                    <div class="form-group ">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" value="" maxlength="30" required="">
                        <span class="text-danger"><?php if (isset($email_error)) echo $email_error; ?></span>
                    </div>

                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" value="" maxlength="8" required="">
                        <span class="text-danger"><?php if (isset($password_error)) echo $password_error; ?></span>
                    </div>  
                    
                    <input type="submit" class="btn btn-primary" name="login" value="submit">
                    <br>
                    You don't have account?<a href="reg.php" class="mt-3">Click Here</a>
                    
                    
                </form>
            </div>
        </div>     
    </div>
</body>
</html>