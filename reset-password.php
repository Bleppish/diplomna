<?php
require 'config.php';
require 'valid-pass.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST['token'];
    $new_password = $_POST['password']; 
    $confirm_password = $_POST['confirm-password'];

    $stmt = $conn->prepare("SELECT user_id FROM password_resets WHERE token = ? AND expires_at > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) 
        die('Invalid or expired token.');

    $row = $result->fetch_assoc();
    $user_id = $row['user_id'];

    if(strcmp($new_password, $confirm_password))
        $errors[] = "Passwords must match!";
    if (validate_password($new_password))
        $errors[] = "Password doesn't match the requirements";
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->bind_param("si", $hashed_password, $user_id);
    $stmt->execute();

    $stmt = $conn->prepare("DELETE FROM password_resets WHERE token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();

    header("Location: login.php");
    exit();
}

if (!isset($_GET['token'])) 
    die('No token provided.');

$token = $_GET['token'];
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="./css/master.css">
    <link rel="stylesheet" href="./css/login.css">
</head>
<body>
    <?php require "header.php"; ?>
    <main class="site-main">
        <section class="section-fullwidth section-login">
            <div class="row">
                <div class="flex-container centered-vertically centered-horizontally">
                    <div class="form-box box-shadow">
                        <div class="section-heading">
                            <h2 class="heading-title">Reset Password</h2>
                        </div>
                        <?php
						if (!empty($errors)) {
							foreach ($errors as $error) 
								echo "<div class='error-message'>{$error}</div>";
						}
						?>
                        <form method="POST" action="reset-password.php">
                            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>" />
                            <div class="form-field-wrapper">
                                <input type="password" name="password" placeholder="New Password" required />
                                <input type="password" name="confirm-password" placeholder="Confirm Password" required />
                            </div>
                            <button type="submit" class="button">Reset Password</button>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <?php include('footer.php'); ?>
</body>
</html>
