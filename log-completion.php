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

$sql_check = "SELECT log_id FROM habit_logs WHERE habit_id = ? AND log_date = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("is", $habit_id, $log_date);
$stmt_check->execute();
$stmt_check->store_result();

if ($stmt_check->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Habit already completed today']);
    exit();
}

$sql_insert = "INSERT INTO habit_logs (habit_id, log_date, status) VALUES (?, ?, ?)";
$stmt_insert = $conn->prepare($sql_insert);
$status = true; 
$stmt_insert->bind_param("isi", $habit_id, $log_date, $status);

if ($stmt_insert->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
}

$stmt_check->close();
$stmt_insert->close();
$conn->close();
?>