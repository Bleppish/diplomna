<?php
include('config.php');

$habit_id = isset($_GET['habit_id']) ? intval($_GET['habit_id']) : 0;

$sql = "SELECT h.habit_id, h.title, h.description, h.category_id, c.title AS category_title
        FROM habits h
        LEFT JOIN categories c ON h.category_id = c.category_id
        WHERE h.habit_id = $habit_id";

$result = $conn->query($sql);

if (!$result) {
    die("Error: " . $conn->error);
}

if ($result->num_rows == 0) {
    die("Habit not found");
}

$habit = $result->fetch_assoc();


$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Habit Details</title>
    <link rel="stylesheet" href="master.css">
</head>
<body>
    <?php include('header.php'); ?>
    <main class="site-main">
        <section class="section-fullwidth">
            <div class="row">
                <div class="job-single">
                    <div class="job-main">
                        <div class="job-card">
                            <div class="job-primary">
                                <header class="job-header">
                                    <h2 class="job-title"><?php echo htmlspecialchars($habit['title']); ?></h2>
                                    <div class="job-meta">
                                        <span class="meta-category">Category: <?php echo htmlspecialchars($habit['category_title']); ?></span>
                                    </div>
                                </header>

                                <div class="job-body">
                                    <p><?php echo nl2br(htmlspecialchars($habit['description'])); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <?php include('footer.php'); ?>
</body>
</html>