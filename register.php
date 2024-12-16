<?php
require 'config.php';  
require 'valid-pass.php';  
require 'name-validator.php'; 

$first_name = $last_name = $email = $password = $confirm_password = '';
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    function sanitize_input($data, $conn) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return mysqli_real_escape_string($conn, $data);
    }

    $first_name = sanitize_input($_POST['first_name'], $conn);
    $last_name = sanitize_input($_POST['last_name'], $conn);
    $email = sanitize_input($_POST['email'], $conn);
    $password = sanitize_input($_POST['password'], $conn);
    $confirm_password = sanitize_input($_POST['confirm_password'], $conn);

    if (!validate_name($first_name) || !validate_name($last_name)) {
        $errors[] = "Name must be a valid name.";
    }
    if (!validate_password($password)) {
        $errors[] = "Password must be at least 8 characters long and include uppercase, lowercase, and special characters.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $errors[] = "This email is already registered.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $username = strtolower($first_name . '.' . $last_name);

            $stmt = $conn->prepare("INSERT INTO users (username, email, password, first_name, last_name, confirmed) VALUES (?, ?, ?, ?, ?, ?)");
            $confirmed = 0; 
            $stmt->bind_param("sssssi", $username, $email, $hashed_password, $first_name, $last_name, $confirmed);

            if ($stmt->execute()) {
                header("Location: registration-success.php");
                exit();
            } else {
                $errors[] = "Error occurred while registering the user.";
            }
        }
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
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
                            <h2 class="heading-title">Register</h2>
                        </div>
                        <?php
                        if (!empty($errors)) {
                            foreach ($errors as $error) {
                                echo "<div class='error-message'>" . htmlspecialchars($error) . "</div>";
                            }
                        }
                        ?>
                        <form action="register.php" method="POST">
                            <div class="flex-container justified-horizontally">
                                <div class="primary-container">
                                    <h4 class="form-title">About me</h4>
                                    <div class="form-field-wrapper">
                                        <input type="text" placeholder="First Name*" name="first_name" value="<?= htmlspecialchars($first_name) ?>" required>
                                    </div>
                                    <div class="form-field-wrapper">
                                        <input type="text" placeholder="Last Name*" name="last_name" value="<?= htmlspecialchars($last_name) ?>" required>
                                    </div>
                                    <div class="form-field-wrapper">
                                        <input type="email" placeholder="Email*" name="email" value="<?= htmlspecialchars($email) ?>" required>
                                    </div>
                                    <div class="form-field-wrapper">
                                        <input type="password" placeholder="Password*" name="password" required>
                                        <span class="password-requirements">At least 8 characters including uppercase, lowercase, and special characters.</span>
                                    </div>
                                    <div class="form-field-wrapper">
                                        <input type="password" placeholder="Repeat Password*" name="confirm_password" required>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="button">Register</button>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <?php include 'footer.php'; ?>
</body>
</html>