<?php
// LeaveC.php - Leave Controller
// This controller handles leave-related requests

require_once __DIR__ . '/../Models/leave.php';
require_once __DIR__ . '/../Models/dbConnect.php';
require_once __DIR__ . '/../Models/user.php';

class LeaveC {
    private $leaveModel;
     private $conn;

   
    public function __construct() {
        $this->leaveModel = new Leave();
        $database = new Database();

        $this->conn = $database->conn;
        $this->userModel = new User();

    }

   // Handle leave request submission
    public function handleRequestLeave() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo 'Method Not Allowed';
            return;
        }

        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['flash'] = 'Please login to request leave.';
            header('Location: /login');
            exit;
        }

        $employee_id = $_SESSION['user_id'] ?? null;
        $employee_name = $_SESSION['user_name'] ?? 'Unknown';
        $leave_type = $_POST['leave_type'] ?? '';
        $start_date = $_POST['start_date'] ?? '';
        $end_date = $_POST['end_date'] ?? '';
        $reason = $_POST['reason'] ?? '';

        if ($leave_type === '' || $start_date === '' || $end_date === '') {
            $_SESSION['flash'] = 'Please fill in all required fields.';
            header('Location: /request-time-off');
            exit;
        }

        $result = $this->leaveModel->requestLeave($employee_id, $employee_name, $leave_type, $start_date, $end_date, $reason);

        if ($result['success']) {
            $_SESSION['flash'] = 'Leave request submitted successfully!';
            header('Location: /request-time-off');
            exit;
        } else {
            $_SESSION['flash'] = $result['message'] ?? 'Failed to submit leave request.';
            header('Location: /request-time-off');
            exit;
        }
    }

    // Get leave summary for a user
    public function getLeaveSummary() {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(["success" => false, "message" => "Unauthorized"]);
            return;
        }

        $employee_id = $_SESSION['user_id'];
        $summary = $this->leaveModel->getLeaveSummary($employee_id);
        header('Content-Type: application/json');
        echo json_encode($summary);
    }



       public function getLeaves()
    {
    // 1. Fetch leave requests with existing query (no JOIN)
    $sql = "SELECT * FROM leaves";
    $result = $this->conn->query($sql);

    $leaveRequests = [];

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // 2. For each leave request, fetch user name from users table
            $userId = (int)$row['id'];

            $stmt = $this->conn->prepare("SELECT name FROM users WHERE id = ? AND role = 'user' AND approved = 1 LIMIT 1");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $userResult = $stmt->get_result();

            $adminName = "Unknown";

            if ($userResult && $userResult->num_rows > 0) {
                $adminRow = $userResult->fetch_assoc();
                $adminName = $adminRow['name'];
            }

            // Add admin name to the announcement
            $row['admin_name'] = $adminName;

            $leaveRequests[] = $row;
        }
    }

    return $leaveRequests;
    }
}
?>
