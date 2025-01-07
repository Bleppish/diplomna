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

$sql_logs = "SELECT log_date, COUNT(*) AS completions 
             FROM habit_logs 
             WHERE habit_id = $habit_id 
               AND status != 0 
               AND log_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
             GROUP BY log_date 
             ORDER BY log_date ASC";
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
    <style>
        .job-single {
            display: flex;
            gap: 20px;
            margin-top: 20px;
        }

        .job-main {
            flex: 1;
        }

        .job-secondary {
            flex: 0 0 300px;
            background: var(--color-base-light);
            padding: 20px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-default);
        }

        .job-secondary h3 {
            margin-top: 0;
            font-size: 1.5em;
            color: var(--color-primary);
        }

        .chart-container {
            width: 100%;
            height: 300px; 
            margin-top: 20px;
        }

        .job-card {
            background: var(--color-base-light);
            padding: 20px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-default);
        }

        .job-title {
            font-size: 2em;
            color: var(--color-primary);
            margin-bottom: 10px;
        }

        .job-meta {
            font-size: 1.1em;
            color: var(--color-base-invert);
            margin-bottom: 20px;
        }

        .job-body {
            font-size: 1.1em;
            line-height: 1.6;
            color: var(--color-base-invert);
        }
    </style>
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
                        <h3>Habit Progress (Last 7 Days)</h3>
                        <div class="chart-container">
                            <canvas id="habitChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <?php include('footer.php'); ?>

    <script>
        const logs = <?php echo json_encode($logs); ?>;

        const labels = logs.map(log => log.log_date);
        const data = logs.map(log => log.completions);

        const ctx = document.getElementById('habitChart').getContext('2d');
        const habitChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Completions per Day',
                    data: data,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)', 
                    borderColor: 'rgba(75, 192, 192, 1)', 
                    borderWidth: 2,
                    fill: true, 
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    pointBackgroundColor: 'rgba(75, 192, 192, 1)', 
                    tension: 0.4, 
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                aspectRatio: 2, 
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Number of Completions', 
                        },
                        ticks: {
                            stepSize: 1, 
                            precision: 0, 
                            callback: function(value) {
                                return Number.isInteger(value) ? value : null; 
                            }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)', 
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Date', 
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)', 
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `: ${context.raw}`; 
                            }
                        }
                    },
                    legend: {
                        display: true,
                        labels: {
                            color: 'black', 
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>