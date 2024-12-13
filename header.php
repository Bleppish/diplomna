<?php 
require 'config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Habits</title>
    <link rel="stylesheet" href="./css/master.css">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
</head>
<body>
<div class="site-wrapper">
    <header class="site-header">
        <div class="row site-header-inner">
            <div class="site-header-branding">
                <h1 class="site-title"><a href="/index.php">Ribit</a></h1>
            </div>
            <nav class="site-header-navigation">
                <ul class="menu">
                    <li class="menu-item"><a href="/Ribit/diplomna/app/index.php">Home</a></li>
                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <li class="menu-item"><a href="/Ribit/diplomna/app/register.php">Register</a></li>
                        <li class="menu-item"><a href="/Ribit/diplomna/app/login.php">Login</a></li>
                    <?php else: ?>
                        <li class="menu-item"><a href="/Ribit/diplomna/app/create-habit.php">Add Habit</a></li>
                        <li class="menu-item"><a href="/Ribit/diplomna/app/dashboard.php">My Habits</a></li>
                        <li class="menu-item"><a href="/Ribit/diplomna/app/profile.php">Profile</a></li>
                        <li class="menu-item"><a href="/Ribit/diplomna/app/logout.php">Logout</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
            <button class="menu-toggle">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                    <path fill="none" d="M0 0h24v24H0z"/>
                    <path fill="currentColor" class="menu-toggle-bars" d="M3 4h18v2H3V4zm0 7h18v2H3v-2zm0 7h18v2H3v-2z"/>
                </svg>
            </button>
        </div>
    </header>
</div>
</body>
</html>
