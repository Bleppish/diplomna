<?php 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'plugins/PHPMailer/src/Exception.php';
require 'plugins/PHPMailer/src/PHPMailer.php';
require 'plugins/PHPMailer/src/SMTP.php';
require __DIR__ . '/../config.php';

$email = isset($_POST['email']) ? $_POST['email'] : '';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "Invalid email format";
    exit;
}

$stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->bind_result($user_id);
    $stmt->fetch();
    $stmt->close();

    $token = bin2hex(random_bytes(50)); 
    $expires_at = date('Y-m-d H:i:s', strtotime('+3 hours'));  
    $stmt = $conn->prepare("INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $token, $expires_at);  
    if ($stmt->execute()) {
        $stmt->close();

        $mail = new PHPMailer(true);
        try {
            $mail->SMTPDebug = 0;                     
            $mail->isSMTP();                          
            $mail->Host = 'smtp.gmail.com';          
            $mail->SMTPAuth = true;                  
            $mail->Username = 'devrix.test.first.team@gmail.com';   
            $mail->Password = 'ttzp ghot zewe jbrh'; 
            $mail->SMTPSecure = 'tls';                
            $mail->Port = 587;  

            $mail->setFrom('devrix.test.first.team@gmail.com', 'JOB OFFERS');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Email Confirmation';
            $mail->Body = "Please click the link to confirm your email address and complete your registration: <a href='http://localhost/tues-Internship-2024-first-team/confirm-email.php?token=$token'>Confirm Email</a>";
            $mail->AltBody = "Please click the link to confirm your email address and complete your registration: http://localhost/tues-Internship-2024-first-team/confirm-email.php?token=$token";

            $mail->send();
            echo 'Email confirmation link has been sent to your email.';
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        echo "Failed to insert confirmation data.";
    }
} else {
    echo "No user found with the provided email.";
}

$conn->close();
?>