<?php
require 'config.php';
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Habit Tracker - Build Better Habits</title>
    <link rel="stylesheet" href="./css/master.css">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        .hero-section {
            background-color: var(--color-primary);
            color: var(--color-base-light);
            padding: 100px 20px;
            text-align: center;
        }

        .hero-section h1 {
            font-size: 3rem;
            margin-bottom: 20px;
        }

        .hero-section p {
            font-size: 1.2rem;
            margin-bottom: 40px;
        }

        .cta-buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
        }

        .cta-buttons .button {
            padding: 15px 30px;
            font-size: 1rem;
            border-radius: var(--border-radius-small);
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .cta-buttons .button.primary {
            background-color: var(--color-secondary);
            color: var(--color-base-light);
        }

        .cta-buttons .button.secondary {
            background-color: transparent;
            border: 2px solid var(--color-secondary);
            color: var(--color-secondary);
        }

        .cta-buttons .button:hover {
            background-color: var(--color-secondary-dark);
            color: var(--color-base-light);
        }

        .features-section {
            padding: 60px 20px;
            text-align: center;
        }

        .features-section h2 {
            font-size: 2rem;
            margin-bottom: 40px;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .feature-card {
            background-color: var(--color-base-light);
            padding: 20px;
            border-radius: var(--border-radius-small);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .feature-card h3 {
            font-size: 1.5rem;
            margin-bottom: 10px;
        }

        .feature-card p {
            font-size: 1rem;
            color: var(--color-text-muted);
        }
    </style>
</head>
<body>
    <div class="site-wrapper">
        <?php include('header.php'); ?>

        <section class="hero-section">
            <h1>Welcome to Ribit</h1>
            <p>Build better habits, track your progress, and achieve your goals with our easy-to-use habit tracking tool.</p>
            <div class="cta-buttons">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="dashboard.php" class="button primary">Go to Dashboard</a>
                <?php else: ?>
                    <a href="login.php" class="button primary">Login</a>
                    <a href="register.php" class="button secondary">Register</a>
                <?php endif; ?>
            </div>
        </section>

        <section class="features-section">
            <h2>Why Choose Ribit?</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <h3>Track Daily Habits</h3>
                    <p>Easily log and monitor your daily habits to stay consistent and motivated.</p>
                </div>
                <div class="feature-card">
                    <h3>Set Goals</h3>
                    <p>Define your goals and track your progress over time to achieve success.</p>
                </div>
                <div class="feature-card">
                    <h3>Visualize Progress</h3>
                    <p>View detailed charts and reports to see how far you've come.</p>
                </div>
            </div>
        </section>

        <?php include('footer.php'); ?>
    </div>
</body>
</html>