<?php
require 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = intval($_SESSION['user_id']);

$sql_get_user = "SELECT username, email, first_name, last_name, confirmed FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql_get_user);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result_user = $stmt->get_result();

if ($result_user->num_rows !== 1) {
    header("Location: login.php");
    exit();
}

$user_data = $result_user->fetch_assoc();
$username = htmlspecialchars($user_data['username']);
$email = htmlspecialchars($user_data['email']);
$first_name = htmlspecialchars($user_data['first_name']);
$last_name = htmlspecialchars($user_data['last_name']);
$confirmed = $user_data['confirmed'] ? 'Yes' : 'No';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <?php include('header.php'); ?>
    <main class="site-main">
        <section class="section-fullwidth">
            <div class="row">
                <div class="flex-container centered-vertically centered-horizontally">
                    <div class="form-box box-shadow">
                        <div class="section-heading">
                            <h2 class="heading-title">User Profile</h2>
                        </div>

                        <div class="profile-info">
                            <div class="profile-field">
                                <label>Username:</label>
                                <span><?php echo $username; ?></span>
                            </div>
                            <div class="profile-field">
                                <label>Email:</label>
                                <span><?php echo $email; ?></span>
                            </div>
                            <div class="profile-field">
                                <label>First Name:</label>
                                <span><?php echo $first_name; ?></span>
                            </div>
                            <div class="profile-field">
                                <label>Last Name:</label>
                                <span><?php echo $last_name; ?></span>
                            </div>
                            <div class="profile-field">
                                <label>Account Confirmed:</label>
                                <span><?php echo $confirmed; ?></span>
                            </div>
                        </div>

                        <div class="profile-actions">
                            <a href="http://localhost/Ribit/diplomna/dashboard.php" class="button">Your habits</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <?php include('footer.php'); ?>
</body>

</html>