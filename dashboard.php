<?php
require 'config.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = intval($_SESSION['user_id']);

error_log("Logged in User ID: " . $user_id);

$sql_get_user = "SELECT user_id FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql_get_user);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result_user = $stmt->get_result();

if ($result_user->num_rows !== 1) {
    header("Location: login.php");
    exit();
}

$row_user = $result_user->fetch_assoc();
$user_id = $row_user['user_id'];

$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;
$category_filter = $category_id ? "AND h.category_id = $category_id" : "";
$search_filter = $search ? "AND h.title LIKE '%$search%'" : "";

$user_filter = "AND h.user_id = $user_id";

$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'id_desc';
switch ($sort_by) {
    case 'id_asc':
        $order_by = "ORDER BY h.habit_id ASC";
        break;
    case 'title_asc':
        $order_by = "ORDER BY h.title ASC";
        break;
    case 'title_desc':
        $order_by = "ORDER BY h.title DESC";
        break;
    case 'id_desc':
    default:
        $order_by = "ORDER BY h.habit_id DESC";
        break;
}

$count_query = "SELECT COUNT(*) AS total_habits FROM habits h WHERE 1=1 $user_filter $category_filter $search_filter";
$count_result = mysqli_query($conn, $count_query);
$total_habits = mysqli_fetch_assoc($count_result)['total_habits'];

$limit = 5; 
$total_pages = ceil($total_habits / $limit);
$page_number = isset($_GET['page']) ? intval($_GET['page']) : 1;
$page_number = max(1, $page_number);
$initial_page = ($page_number - 1) * $limit;

$sql = "SELECT h.habit_id, h.title, h.description, h.category_id, c.title AS category_title
        FROM habits h
        LEFT JOIN categories c ON h.category_id = c.category_id
        WHERE 1=1 $user_filter $category_filter $search_filter
        $order_by
        LIMIT $initial_page, $limit";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Habit Tracker</title>
    <link rel="stylesheet" href="master.css">
    <style>
        .filter-form {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
        }

        .filter-form .form-field-wrapper {
            flex: 1;
            min-width: 150px; 
        }

        .filter-form input,
        .filter-form select {
            width: 100%;
            padding: 8px;
            border: 1px solid var(--color-separator);
            border-radius: var(--border-radius-small);
            font-size: 0.9em; 

        .filter-form button {
            flex: 0 0 auto;
            align-self: flex-end;
            padding: 8px 16px; 
            font-size: 0.9em; 
        }

        .habits-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .habits-table th,
        .habits-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid var(--color-separator);
        }

        .habits-table th {
            background-color: var(--color-muted-light);
            font-weight: bold;
        }

        .habits-table tbody tr {
            cursor: pointer; 
            transition: background-color 0.3s ease;
        }

        .habits-table tbody tr:hover {
            background-color: var(--color-muted);
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 5px;
            margin-top: 20px;
        }

        .pagination a {
            padding: 8px 12px;
            text-decoration: none;
            color: var(--color-primary);
            border: 1px solid var(--color-primary);
            border-radius: var(--border-radius-small);
            transition: background-color 0.3s ease;
        }

        .pagination a.active {
            background-color: var(--color-primary);
            color: var(--color-base-light);
        }

        .pagination a:hover {
            background-color: var(--color-primary);
            color: var(--color-base-light);
        }
    </style>
</head>
<body>
    <?php include('header.php'); ?>
    <main class="site-main">
        <section class="section-fullwidth">
            <div class="row">
                <div class="flex-container centered-vertically centered-horizontally">
                    <div class="form-box box-shadow">
                        <div class="section-heading">
                            <h2 class="heading-title">All Habits</h2>
                        </div>

                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="GET" class="filter-form">
                            <div class="form-field-wrapper">
                                <input type="text" name="search" placeholder="Search by title" value="<?php echo htmlspecialchars($search); ?>">
                            </div>
                            <div class="form-field-wrapper">
                                <select name="category_id">
                                    <option value="0">All Categories</option>
                                    <?php
                                    $sql_categories = "SELECT category_id, title FROM categories";
                                    $result_categories = $conn->query($sql_categories);
                                    if ($result_categories->num_rows > 0) {
                                        while ($row_category = $result_categories->fetch_assoc()) {
                                            $selected = ($category_id == $row_category['category_id']) ? 'selected' : '';
                                            echo '<option value="' . htmlspecialchars($row_category['category_id']) . '" ' . $selected . '>' . htmlspecialchars($row_category['title']) . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-field-wrapper">
                                <select name="sort_by">
                                    <option value="id_desc" <?php echo ($sort_by == 'id_desc') ? 'selected' : ''; ?>>Newest First</option>
                                    <option value="id_asc" <?php echo ($sort_by == 'id_asc') ? 'selected' : ''; ?>>Oldest First</option>
                                    <option value="title_asc" <?php echo ($sort_by == 'title_asc') ? 'selected' : ''; ?>>Title (A-Z)</option>
                                    <option value="title_desc" <?php echo ($sort_by == 'title_desc') ? 'selected' : ''; ?>>Title (Z-A)</option>
                                </select>
                            </div>
                            <button type="submit" class="button">Apply Filters</button>
                        </form>

                        <table class="habits-table">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Description</th>
                                    <th>Category</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo '<tr onclick="window.location=\'http://localhost/Ribit/diplomna/single-view.php?habit_id=' . htmlspecialchars($row['habit_id']) . '\'">
                                                <td>' . htmlspecialchars($row['title']) . '</td>
                                                <td>' . htmlspecialchars($row['description']) . '</td>
                                                <td>' . htmlspecialchars($row['category_title']) . '</td>
                                              </tr>';
                                    }
                                } else {
                                    echo '<tr><td colspan="3">No habits found.</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>

                        <div class="pagination">
                            <?php
                            for ($i = 1; $i <= $total_pages; $i++) {
                                echo '<a href="?page=' . $i . '&search=' . htmlspecialchars($search) . '&category_id=' . $category_id . '&sort_by=' . $sort_by . '" class="' . ($page_number == $i ? 'active' : '') . '">' . $i . '</a>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <?php include('footer.php'); ?>
</body>
</html>