<?php
$host = "localhost";  // XAMPP host
$user = "root";       // Default username
$pass = "";           // Default password (leave empty)
$dbname = "leave_management";  // Your database name

$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
//  echo "Connected successfully"; 
?>
