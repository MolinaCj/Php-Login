<?php
session_start();
include 'db.php';

if (isset($_POST['otp'])) {
    include 'db.php';

    $otp = $_POST['otp'];

    // Check if the OTP is correct and not expired

    $stmt = $conn->prepare("SELECT id, otp_expires_at FROM otp_verification WHERE otp=?");
    $stmt->bind_param("s", $otp);
    $stmt->execute();
    $result = $stmt->get_result();


    //Use this when you need to verify both email and otp
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $current_time = time();
        $otp_expiration_time = strtotime($user['otp_expires_at']);

        if ($otp_expiration_time >= $current_time) {
            // OTP is correct and not expired, log in the user
            session_start();
            $_SESSION['user_id'] = $user['id'];
            header("Location: logsuccess.php");
            exit();
        } else {
            echo 'Invalid or expired OTP.';
        }
    } else {
        echo 'Invalid or expired OTP.';
    }
}
/*$msg = "";

if (isset($_POST['otp'])) {
    $otp = $_POST['otp'];
    $email = $_SESSION['email']; // Retrieve email from session
    //var_dump($otp, $email); 
    // Check if the OTP is correct and not expired
        $stmt = $conn->prepare("SELECT id, otp_expires_at FROM otp_verification WHERE otp=?");
        $stmt->bind_param("s", $otp);
        $stmt->execute();
        $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $current_time = date('Y-m-d H:i:s');
        $otp_expiration_time = strtotime($user['otp_expires_at']);

        if ($otp_expiration_time > $current_time) {
            // OTP is correct and not expired, log in the user
            session_start();
            $_SESSION['user_id'] = $user['id'];
            header("Location: logsuccess.php");
            exit();
        }  else {
            echo 'OTP has expired.';
        }
    } else {
        echo 'Invalid OTP.';
    }
}*/
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
     <!--CSS-->
     <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-3">
        <div class="card">
            <div class="card-header text-center">
                Verify OTP
            </div>
            <div class="card-body">
                <form method="post" action="verify-login-otp.php">
                    <div class="form-group">
                        <label for="otp">Enter OTP</label>
                        <input type="text" class="form-control" id="otp" name="otp" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Verify OTP</button>
                </form>
                <form method="post" action="regenerate-otp.php">
                    <button type="submit" class="btn btn-secondary mt-2" name="regenerate">Regenerate OTP</button>
                </form>
            </div>
        </div>
    </div>
   
</body>
</html>