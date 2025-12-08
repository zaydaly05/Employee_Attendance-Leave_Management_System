<?php
require_once 'dbConnect.php';

class Attendance {
    private $conn;
    private $table = "attendance";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->conn;
    }

   public function markAttendance($employee_id, $status, $date)
    {
        $sql = "INSERT INTO {$this->table} (employee_id, date, status) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iss", $employee_id, $date, $status);
        return $stmt->execute();
    }

    /**
     * Check if the user has already marked attendance today
     */
    public function hasMarkedToday($employee_id, $date)
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE employee_id = ? AND date = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("is", $employee_id, $date);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['count'] > 0;
    }

    /**
     * Get today’s attendance status (optional, for displaying in view)
     */
    public function getTodayStatus($employee_id, $date)
    {
        $sql = "SELECT status FROM {$this->table} WHERE employee_id = ? AND date = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("is", $employee_id, $date);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            return $row['status'];
        }
        return null;
    }

    public function getAttendanceHistory($employee_id) {
        $sql = "SELECT * FROM {$this->table} WHERE employee_id = ? ORDER BY date DESC";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return ["success" => false, "message" => "Prepare failed: " . $this->conn->error];
        }
        $stmt->bind_param("i", $employee_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $attendance = [];
        while ($row = $result->fetch_assoc()) {
            $attendance[] = $row;
        }
        return ["success" => true, "data" => $attendance];
    }

    public function getAttendanceByDate($date) {
        $sql = "SELECT * FROM {$this->table} WHERE date = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $date);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function getEmployeeAttendance($employee_id) {
        $sql = "SELECT * FROM {$this->table} WHERE employee_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $employee_id);
        $stmt->execute();
        return $stmt->get_result();
    }
}
?>
