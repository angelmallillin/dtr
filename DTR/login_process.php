<?php
// Itakda ang tamang oras para sa Pilipinas
date_default_timezone_set('Asia/Manila');
session_start();
include 'db_connect.php';

if (isset($_POST['login_btn'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $now = date("Y-m-d H:i:s");

    // 1. Hanapin ang user
    $stmt = $conn->prepare("SELECT id, password, login_attempts, lockout_time FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        
        // 2. CHECK LOCKOUT: Kung locked pa, i-calculate ang seconds na tira
        if ($user['lockout_time'] && strtotime($user['lockout_time']) > strtotime($now)) {
            $remaining = strtotime($user['lockout_time']) - strtotime($now);
            header("Location: index.php?error=locked&wait=" . $remaining);
            exit();
        }

        // 3. VERIFY PASSWORD
        if (password_verify($password, $user['password'])) {
            // TAMA: I-reset ang attempts at lockout
            $reset = $conn->prepare("UPDATE users SET login_attempts = 0, lockout_time = NULL WHERE id = ?");
            $reset->bind_param("i", $user['id']);
            $reset->execute();

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user'] = $username;
            header("Location: dashboard.php");
            exit();
        } else {
            // MALI: Dagdagan ang attempts
            $new_attempts = $user['login_attempts'] + 1;
            
            if ($new_attempts >= 5) {
                // LOCKOUT TRIGGER: I-lock ng 1 minute (60 seconds)
                $lockout_until = date("Y-m-d H:i:s", strtotime("+1 minute"));
                $update = $conn->prepare("UPDATE users SET login_attempts = ?, lockout_time = ? WHERE id = ?");
                $update->bind_param("isi", $new_attempts, $lockout_until, $user['id']);
            } else {
                $update = $conn->prepare("UPDATE users SET login_attempts = ? WHERE id = ?");
                $update->bind_param("ii", $new_attempts, $user['id']);
            }
            $update->execute();
            header("Location: index.php?error=invalid");
            exit();
        }
    } else {
        header("Location: index.php?error=invalid");
        exit();
    }
}
?>