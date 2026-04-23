<?php
// Database credentials
$host = "localhost";
$user = "root";
$pass = ""; // Default is empty for XAMPP and Fedora MariaDB
$dbname = "pr7_db";

// Create connection
$conn = mysqli_connect($host, $user, $pass, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
