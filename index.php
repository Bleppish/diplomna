<?php
require 'config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
</head>
<body>
    <h1>Welcome, User <?= htmlspecialchars($user_id); ?></h1>
    <a href="logout.php">Logout</a>
</body>
</html>
