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
    public function handleMarkAttendance()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(false, "Method Not Allowed", 405);
        }

        if (!isset($_SESSION['user_id'])) {
            return $this->jsonResponse(false, "Unauthorized", 401);
        }

        $employee_id = $_SESSION['user_id']; 
        $status = $_POST['status'] ?? '';
        $date = $_POST['date'] ?? date('Y-m-d');

        if ($status === '') {
            return $this->respond("Missing required fields", false);
        }

        // ✅ Check if the user has already marked attendance today
        if ($this->attendanceModel->hasMarkedToday($employee_id, $date)) {
            return $this->respond("You have already marked attendance for today.", false);
        }

        // Mark attendance
        $result = $this->attendanceModel->markAttendance($employee_id, $status, $date);

        if ($result) {
            return $this->respond("Attendance marked successfully", true);
        } else {
            return $this->respond("Failed to mark attendance", false);
        }
    }

    /**
     * Detect if request is AJAX or FORM and respond accordingly
     */
    private function respond(string $message, bool $success)
    {
        $isAjax = (
            !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
        );

        if ($isAjax) {
            return $this->jsonResponse($success, $message);
        }

        // FORM POST → redirect and show flash message
        $_SESSION['attendance_message'] = $message;
        header("Location: /EALMS/dashboard"); // your dashboard page
        exit;
    }

    /**
     * Send JSON response
     */
    private function jsonResponse(bool $success, string $message, int $code = 200)
    {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode([
            "success" => $success,
            "message" => $message
        ]);
        exit;
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
