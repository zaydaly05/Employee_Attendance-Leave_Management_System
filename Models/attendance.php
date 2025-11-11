<?php
require_once 'db.php';

class Attendance {
    private $conn;
    private $table = "attendance";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->conn;
    }

    public function markAttendance($employee_id, $date, $status) {
        $sql = "INSERT INTO {$this->table} (employee_id, date, status) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iss", $employee_id, $date, $status);
        return $stmt->execute();
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
