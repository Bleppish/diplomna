<?php
session_start();

if (!isset($_SESSION['registration_success'])) {
    header("Location: register.php"); 
    exit();
}

unset($_SESSION['registration_success']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Successful</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'header.php'; ?>
    <main class="site-main">
        <section class="section-fullwidth">
            <div class="row">
                <div class="flex-container centered-vertically centered-horizontally">
                    <div class="form-box box-shadow">
                        <div class="section-heading">
                            <h2 class="heading-title">Registration Successful</h2>
                        </div>
                        <div class="success-message">
                            <p>Thank you for registering! Your account has been created successfully.</p>
                            <p>Please check your email to confirm your registration.</p>
                        </div>
                        <div class="action-links">
                            <p><a href="login.php">Click here to log in</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <?php include 'footer.php'; ?>
</body>
</html>