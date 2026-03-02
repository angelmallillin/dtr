<?php
session_start();
include 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$date = date('Y-m-d');
$time = date('H:i:s');

if (isset($_POST['punch_in'])) {
    // Check if user already clocked in today
    $check = $conn->prepare("SELECT id FROM attendance WHERE user_id = ? AND log_date = ?");
    $check->bind_param("is", $user_id, $date);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows == 0) {
        // First punch of the day: Create the record
        $stmt = $conn->prepare("INSERT INTO attendance (user_id, log_date, time_in) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $date, $time);
        $stmt->execute();
    }
    header("Location: dashboard.php?status=clocked_in");
}

if (isset($_POST['punch_out'])) {
    // Update the record for today with the time out
    $stmt = $conn->prepare("UPDATE attendance SET time_out = ? WHERE user_id = ? AND log_date = ? AND time_out IS NULL");
    $stmt->bind_param("sis", $time, $user_id, $date);
    $stmt->execute();
    header("Location: dashboard.php?status=clocked_out");
}
?>