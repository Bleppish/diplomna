<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'mailer/plugins/PHPMailer/src/Exception.php';
require 'mailer/plugins/PHPMailer/src/PHPMailer.php';
require 'mailer/plugins/PHPMailer/src/SMTP.php';
require 'mailer/config.php';

$today = date('l');

$sql = "SELECT hr.habitReminder_id, hr.scheduled_time, r.message, h.title, u.email, u.username
        FROM habitReminder hr
        JOIN reminders r ON hr.reminder_id = r.reminder_id
        JOIN habits h ON hr.habit_id = h.habit_id
        JOIN users u ON h.user_id = u.user_id
        WHERE DAYOFWEEK(hr.scheduled_time) = DAYOFWEEK(CURDATE())"; 
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    $scheduled_time = $row['scheduled_time'];
    $message = $row['message'];
    $title = $row['title'];
    $email = $row['email'];
    $username = $row['username'];

    $mail = new PHPMailer(true);
    try {
        $mail->SMTPDebug = 0;                     
        $mail->isSMTP();                          
        $mail->Host = 'smtp.gmail.com';          
        $mail->SMTPAuth = true;                  
        $mail->Username = 'somethin11g@gmail.com';   
        $mail->Password = 'ttzp ghot zewe jbrh';    
        $mail->SMTPSecure = 'tls';                
        $mail->Port = 587;  

        $mail->setFrom('something11@gmail.com', 'Habit Tracker');
        $mail->addAddress($email, $username);
        $mail->isHTML(true);
        $mail->Subject = 'Habit Reminder: ' . $title;
        $mail->Body = "Hi $username,<br><br>Don't forget to complete your habit: <strong>$title</strong>.<br><br>Reminder: $message<br><br>Best regards,<br>Habit Tracker Team";
        $mail->AltBody = "Hi $username,\n\nDon't forget to complete your habit: $title.\n\nReminder: $message\n\nBest regards,\nHabit Tracker Team";

        $mail->send();
        echo "Reminder sent to $email for habit: $title<br>";
    } catch (Exception $e) {
        echo "Failed to send reminder to $email: {$mail->ErrorInfo}<br>";
    }
}

$conn->close();
?>