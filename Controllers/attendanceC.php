<?php
require_once '../Models/attendance.php';

$attendance = new Attendance();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $employee_id = $_POST['employee_id'];
    $status = $_POST['status'];
    $date = $_POST['date'];

    $result = $attendance->markAttendance($employee_id, $status, $date);
    echo json_encode(["success" => $result, "message" => $result ? "Attendance marked" : "Failed"]);
}
?>
