<?php
// AttendanceC.php - Attendance Controller
// This controller handles attendance-related requests

require_once __DIR__ . '/../Models/attendance.php';

class AttendanceC {
    private $attendanceModel;

    public function __construct() {
        $this->attendanceModel = new Attendance();
    }

    // Handle marking attendance
    public function handleMarkAttendance() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            header('Content-Type: application/json');
            echo json_encode(["success" => false, "message" => "Method Not Allowed"]);
            return;
        }

        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(["success" => false, "message" => "Unauthorized"]);
            return;
        }

        $employee_id = $_SESSION['user_id'] ?? $_POST['employee_id'] ?? null;
        $status = $_POST['status'] ?? '';
        $date = $_POST['date'] ?? date('Y-m-d');

        if ($employee_id === null || $status === '') {
            header('Content-Type: application/json');
            echo json_encode(["success" => false, "message" => "Missing required fields"]);
            return;
        }

        $result = $this->attendanceModel->markAttendance($employee_id, $status, $date);
        
        header('Content-Type: application/json');
        echo json_encode([
            "success" => $result, 
            "message" => $result ? "Attendance marked successfully" : "Failed to mark attendance"
        ]);
    }

    // Get attendance history
    public function getAttendanceHistory() {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(["success" => false, "message" => "Unauthorized"]);
            return;
        }

        $employee_id = $_SESSION['user_id'];
        $history = $this->attendanceModel->getAttendanceHistory($employee_id);
        
        header('Content-Type: application/json');
        echo json_encode($history);
    }
}
?>
