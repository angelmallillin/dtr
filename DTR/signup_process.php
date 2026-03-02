<?php
include 'db_connect.php'; // Correctly link your database connection

if(isset($_POST['signup_btn'])) {
    $fullname = $_POST['fullname'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Double-check password length on server-side
    if(strlen($password) < 8) {
        header("Location: signup.php?error=shortpass");
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Securely hash password

    // SQL to insert the user into the 'users' table
    $stmt = $conn->prepare("INSERT INTO users (fullname, username, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $fullname, $username, $hashed_password);

    if($stmt->execute()) {
        $stmt->close();
        $conn->close();
        header("Location: index.php?signup=success"); // Redirect to login on success
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>