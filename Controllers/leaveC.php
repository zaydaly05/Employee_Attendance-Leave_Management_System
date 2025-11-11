<?php
require_once __DIR__ . '/../Models/dbConnect.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $leave_type = $_POST['leave_type'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $reason = $_POST['reason'];

    // Dummy employee name for now (you can use session later)
    $employee_name = "Zayd Ali";

    // initialize database connection
    $database = new Database();
    $conn = $database->conn;

    // Insert into database (use prepared statement to avoid SQL injection)
    $stmt = $conn->prepare("INSERT INTO leaves (employee_name, leave_type, start_date, end_date, reason) VALUES (?, ?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param('sssss', $employee_name, $leave_type, $start_date, $end_date, $reason);
        if ($stmt->execute()) {
            echo "<script>alert('Leave request submitted successfully!');
                  window.location.href='apply_leave.php';</script>";
        } else {
            echo "Error executing statement: " . $stmt->error;
        }
    } else {
        echo "Prepare failed: " . $conn->error;
    }
}
?>
