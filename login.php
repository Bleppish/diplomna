<?php
require 'config.php';

session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$email = $password = '';
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email)) {
        $errors[] = "Email is required.";
    }
    if (empty($password)) {
        $errors[] = "Password is required.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT user_id, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 1) {
            $stmt->bind_result($user_id, $hashed_password);
            $stmt->fetch();

            if (password_verify($password, $hashed_password)) {
                $_SESSION['user_id'] = $user_id;
                header("Location: dashboard.php");
                exit();
            } else {
                $errors[] = "Invalid email or password.";
            }
        } else {
            $errors[] = "Invalid email or password.";
        }

        $stmt->close();
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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
                            <h2 class="heading-title">Login</h2>
                        </div>
                        <?php
                        if (!empty($errors)) {
                            foreach ($errors as $error) {
                                echo "<div class='error-message'>" . htmlspecialchars($error) . "</div>";
                            }
                        }
                        ?>
                        <form action="login.php" method="POST">
                            <div class="flex-container justified-horizontally">
                                <div class="primary-container">
                                    <div class="form-field-wrapper">
                                        <input type="email" placeholder="Email*" name="email" value="<?= htmlspecialchars($email) ?>" required>
                                    </div>
                                    <div class="form-field-wrapper">
                                        <input type="password" placeholder="Password*" name="password" required>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="button">Login</button>
                        </form>
                        <div class="register-link">
                            <p>Don't have an account? <a href="register.php">Register here</a>.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <?php include 'footer.php'; ?>
</body>
</html>