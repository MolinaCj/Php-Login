<!doctype html>
<html lang="en">
   <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  
      <title>User Account Activation by Email Verification using PHP</title>
       <!-- CSS -->
       <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
   </head>
   <body>
          <?php
            if (isset($_GET['key']) && isset($_GET['token'])) {
                include "db.php";
            
                $email = $_GET['key'];
                $token = $_GET['token'];
            
                $stmt = $conn->prepare("SELECT * FROM users WHERE email_verification_link = ? AND email = ?");
                $stmt->bind_param("ss", $token, $email);
                $stmt->execute();
                $result = $stmt->get_result();
            
                $d = date('Y-m-d H:i:s');
            
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();

                    if ($row['email_verified_at'] == NULL) {
                        $update_stmt = $conn->prepare("UPDATE users SET email_verified_at = ? WHERE email = ?");
                        $update_stmt->bind_param("ss", $d, $email);
                        $update_stmt->execute();
            
                        if ($update_stmt->affected_rows > 0) {
                            $msg = "Congratulations! Your email has been verified.";
                        } else {
                            $msg = "Failed to verify your email. Please try again.";
                        }
                    } else {
                        $msg = "You have already verified your account with us.";
                    }
                } else {
                    $msg = "This email is not registered with us.";
                }
                $stmt->close();
            } else {
                $msg = "Danger! Something went wrong.";
            }
            $conn->close();
            ?>

      <div class="container mt-3">
          <div class="card">
            <div class="card-header text-center">
              User Account Activation by Email Verification using PHP
            </div>
            <div class="card-body">
             <p><?php echo $msg; ?></p><br>
              <a href="login.php" class="btn btn-default">Login</a>
            </div>
          </div>
      </div>

   </body>
</html>