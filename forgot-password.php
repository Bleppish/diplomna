<?php
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $error = 'Invalid email address.';
    } else {
        require 'mailer/mailer.php';
    }
}
?>

<?php require "header.php"; ?>
    <main class="site-main">
        <section class="section-fullwidth section-login">
            <div class="row">
                <div class="flex-container centered-vertically centered-horizontally">
                    <div class="form-box box-shadow">
                        <div class="section-heading">
                            <h2 class="heading-title">Forgot Password</h2>
                        </div>
                        <?php if (!empty($error)): ?>
                            <div class="error-message"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>
                        <?php if (!empty($success)): ?>
                            <div class="success-message"><?= htmlspecialchars($success) ?></div>
                        <?php endif; ?>
                        <form method="POST" action="">
                            <div class="form-field-wrapper">
                                <input type="email" name="email" placeholder="Your Email" required />
                            </div>
                            <button type="submit" class="button">Send Reset Link</button>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <?php include('footer.php'); ?>
</body>
</html>
