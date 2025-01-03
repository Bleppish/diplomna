<?php
include('config.php');
session_start();

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

$sql_logs = "SELECT log_date, status FROM habit_logs WHERE habit_id = $habit_id ORDER BY log_date ASC";
$result_logs = $conn->query($sql_logs);

$logs = [];
while ($row = $result_logs->fetch_assoc()) {
    $logs[] = $row;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Habit Details</title>
    <link rel="stylesheet" href="master.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

                    <div class="job-secondary">
                        <h3>Habit Progress</h3>
                        <canvas id="habitChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <?php include('footer.php'); ?>

    <script>
        const logs = <?php echo json_encode($logs); ?>;

        const labels = logs.map(log => log.log_date);
        const data = logs.map(log => log.status ? 1 : 0); /

        const ctx = document.getElementById('habitChart').getContext('2d');
        const habitChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Habit Completion',
                    data: data,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1,
                    fill: true, 
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            callback: function(value) {
                                return value === 1 ? 'Completed' : 'Not Completed';
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.raw === 1 ? 'Completed' : 'Not Completed';
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>