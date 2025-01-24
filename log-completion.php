<?php
session_start();
require 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$habit_id = isset($data['habit_id']) ? intval($data['habit_id']) : 0;

if ($habit_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid habit ID']);
    exit();
}

$user_id = intval($_SESSION['user_id']);
$log_date = date('Y-m-d'); 

$sql_check = "SELECT log_id, completion_count FROM habit_logs WHERE habit_id = ? AND log_date = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("is", $habit_id, $log_date);
$stmt_check->execute();
$stmt_check->store_result();

if ($stmt_check->num_rows > 0) {
    $stmt_check->bind_result($log_id, $completion_count);
    $stmt_check->fetch();
    $new_completion_count = $completion_count + 1;

    $sql_update = "UPDATE habit_logs SET completion_count = ? WHERE log_id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("ii", $new_completion_count, $log_id);

    if ($stmt_update->execute()) {
        echo json_encode(['success' => true, 'completion_count' => $new_completion_count]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update completion count']);
    }

    $stmt_update->close();
} else {
    $sql_insert = "INSERT INTO habit_logs (habit_id, log_date, status, completion_count) VALUES (?, ?, ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    $status = true; 
    $completion_count = 1; 
    $stmt_insert->bind_param("isii", $habit_id, $log_date, $status, $completion_count);

    if ($stmt_insert->execute()) {
        echo json_encode(['success' => true, 'completion_count' => $completion_count]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to log completion']);
    }

    $stmt_insert->close();
}

$stmt_check->close();
$conn->close();
?>