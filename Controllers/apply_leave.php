<?php
include 'dbConnect.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $leave_type = $_POST['leave_type'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $reason = $_POST['reason'];

    // Dummy employee name for now (you can use session later)
    $employee_name = "Zayd Ali";

    // Insert into database
    $sql = "INSERT INTO leaves (employee_name, leave_type, start_date, end_date, reason)
            VALUES ('$employee_name', '$leave_type', '$start_date', '$end_date', '$reason')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Leave request submitted successfully!');
              window.location.href='apply_leave.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
