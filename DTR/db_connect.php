<?php
$servername = "localhost";
$db_username = "root"; // Default XAMPP username
$db_password = "";     // Default XAMPP password
$dbname = "dtr_practice";

// Create connection
$conn = new mysqli($servername, $db_username, $db_password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>