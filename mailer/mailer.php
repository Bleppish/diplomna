<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require 'plugins/PHPMailer/src/Exception.php';
    require 'plugins/PHPMailer/src/PHPMailer.php';
    require 'plugins/PHPMailer/src/SMTP.php';
    require __DIR__ . '/../config.php';

    $email = $_POST['email']; 

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($user_id);
    $stmt->fetch();
    $stmt->close();

    if ($user_id) 
    {
        $token = bin2hex(random_bytes(50));
        $expires_at = date('Y-m-d H:i:s', strtotime('+3 hour'));
        $stmt = $conn->prepare("INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $token, $expires_at);
        if ($stmt->execute()) {
            $stmt->close();

            $mail = new PHPMailer(true);
            try {
                // SMTP configuration
                $mail->SMTPDebug = 0;                     
                $mail->isSMTP();                          
                $mail->Host = 'smtp.gmail.com';          
                $mail->SMTPAuth = true;                  
                $mail->Username = 'devrix.test.first.team@gmail.com';   // Your Gmail address
                $mail->Password = 'ttzp ghot zewe jbrh';    // Your App Password
                $mail->SMTPSecure = 'tls';                
                $mail->Port = 587;  

                // Email content
                $mail->setFrom('yourname@gmail.com', 'Your Name');
                $mail->addAddress($email);
                $mail->isHTML(true);
                $mail->Subject = 'Password Reset';
                $mail->Body = "Please click the link to reset your password: <a href='http://localhost/tues-Internship-2024-first-team/reset-password.php?token=$token'>Reset Password</a>";
                $mail->AltBody = "Please click the link to reset your password: http://localhost/tues-Internship-2024-first-team/reset-password.php?token=$token";

                // Send email
                $mail->send();
                header("Location: reset-password-success.php");
            } catch (Exception $e) {
                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
        } else {
            echo "Failed to insert password reset data.";
        }
    } else {
        echo "No user found with the provided email.";
    }

    $conn->close();
?>
