<?php
include('config.php');
    session_start();


if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = intval($_SESSION['user_id']);

$sql_get_user = "SELECT user_id FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql_get_user);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result_user = $stmt->get_result();

if ($result_user->num_rows !== 1) {
    header("Location: login.php");
    exit();
}

$errors = [];
$success_message = '';
$title = '';
$description = '';
$category_id = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $category_id = trim($_POST['category']); 

    if (empty($title)) {
        $errors[] = "Habit title is required.";
    }
    if (empty($description)) {
        $errors[] = "Description is required.";
    }
    if (empty($category_id)) { 
        $errors[] = "Category is required.";
    }

    if (empty($errors)) {
        $user_id = 1; 
        $sql = "INSERT INTO habits (title, description, category_id, user_id) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssii", $title, $description, $category_id, $user_id);
        if ($stmt->execute()) {
            $success_message = "Habit created successfully!";
            $title = '';
            $description = '';
            $category_id = '';
        } else {
            $errors[] = "Error creating habit: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Habit</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include('header.php'); ?>
    <main class="site-main">
        <section class="section-fullwidth">
            <div class="row">
                <div class="flex-container centered-vertically centered-horizontally">
                    <div class="form-box box-shadow">
                        <div class="section-heading">
                            <h2 class="heading-title">Create New Habit</h2>
                        </div>
                        <?php if (!empty($errors)): ?>
                            <div class="error-message">
                                <?php foreach ($errors as $error): ?>
                                    <p><?php echo $error; ?></p>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($success_message)): ?>
                            <div class="success-message">
                                <p><?php echo $success_message; ?></p>
                            </div>
                        <?php endif; ?>
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                            <div class="flex-container flex-wrap">
                                <div class="form-field-wrapper width-large">
                                    <input type="text" name="title" placeholder="Habit title*" value="<?php echo htmlspecialchars($title); ?>" required />
                                </div>
                                <div class="form-field-wrapper width-large">
                                    <select name="category" id="category" required>
                                        <option value="" disabled selected>Select Category</option>
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
                                <div class="form-field-wrapper width-large">
                                    <textarea name="description" placeholder="Description*" required><?php echo htmlspecialchars($description); ?></textarea>
                                </div>
                            </div>
                            <button type="submit" class="button">Create Habit</button>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <?php include('footer.php'); ?>
</body>
</html>