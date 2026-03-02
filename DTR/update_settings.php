<?php
session_start();
include 'db_connect.php';

if(isset($_POST['save_profile'])) {
    $user_id = $_SESSION['user_id'];
    $username = $_POST['username'];
    $course = $_POST['course'];
    $school = $_POST['school'];
    $agency = $_POST['agency'];
    $target_hours = $_POST['target_hours'];

    // 1. I-update muna ang Text Information
    $sql = "UPDATE users SET username=?, course=?, school=?, agency=?, target_hours=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssii", $username, $course, $school, $agency, $target_hours, $user_id);
    $stmt->execute();

    // 2. I-handle ang Photo Upload (Kung meron)
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $file = $_FILES['profile_image'];
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        // Gumawa ng unique filename gamit ang user ID at timestamp
        $fileName = "profile_" . $user_id . "_" . time() . "." . $extension;
        $uploadDir = 'uploads/profile/';
        
        // Siguraduhing mayroon 'uploads/profile/' folder
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        if (move_uploaded_file($file['tmp_name'], $uploadDir . $fileName)) {
            // I-update ang path sa database
            $update_pic = $conn->prepare("UPDATE users SET profile_pic = ? WHERE id = ?");
            $update_pic->bind_param("si", $fileName, $user_id);
            $update_pic->execute();
        }
    }

    // 3. Isang redirect lang sa dulo
    header("Location: dashboard.php?update=success");
    exit();
}
?>