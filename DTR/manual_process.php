<?php
session_start();
include 'db_connect.php';

if (isset($_POST['save_manual'])) {
    $user_id = $_SESSION['user_id'];
    $log_date = $_POST['manual_date'];
    $am_in = $_POST['am_in'];
    $am_out = $_POST['am_out'];
    $pm_in = $_POST['pm_in'];
    $pm_out = $_POST['pm_out'];
    $status = $_POST['status_exception'];
    
    // Updated to catch the 'internal_note' name from your dashboard.php textarea
    $note = $_POST['internal_note'] ?? ''; 

    // Check if a record already exists for this date to update or insert
    $check = $conn->prepare("SELECT id FROM attendance WHERE user_id = ? AND log_date = ?");
    $check->bind_param("is", $user_id, $log_date);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        // Update existing record
        $sql = "UPDATE attendance SET am_in=?, am_out=?, pm_in=?, pm_out=?, status=?, note=? WHERE user_id=? AND log_date=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssis", $am_in, $am_out, $pm_in, $pm_out, $status, $note, $user_id, $log_date);
    } else {
        // Insert new record
        $sql = "INSERT INTO attendance (user_id, log_date, am_in, am_out, pm_in, pm_out, status, note) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isssssss", $user_id, $log_date, $am_in, $am_out, $pm_in, $pm_out, $status, $note);
    }

    if ($stmt->execute()) {
        header("Location: dashboard.php?status=success");
    } else {
        header("Location: dashboard.php?status=error");
    }
    exit();
}
?>