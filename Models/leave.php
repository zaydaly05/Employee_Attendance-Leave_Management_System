<?php
require_once 'dbConnect.php';

class Leave {
    private $conn;
    private $table = "leaves";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->conn;
    }

    public function requestLeave($employee_id, $employee_name, $leave_type, $start_date, $end_date, $reason, $status = 'Pending') {
        $sql = "INSERT INTO {$this->table} (start_date, end_date, leave_type, reason, status, user_id) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return ["success" => false, "message" => "Prepare failed: " . $this->conn->error];
        }
        $stmt->bind_param("sssssi", $start_date, $end_date, $leave_type, $reason, $status, $employee_id);
        if ($stmt->execute()) {
            return ["success" => true, "message" => "Leave request submitted successfully", "id" => $stmt->insert_id];
        } else {
            return ["success" => false, "message" => $stmt->error];
        }
    }

    public function getLeaveSummary($employee_id) {
        $sql = "SELECT * FROM {$this->table} WHERE employee_id = ? ORDER BY start_date DESC";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return ["success" => false, "message" => "Prepare failed: " . $this->conn->error];
        }
        $stmt->bind_param("i", $employee_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $leaves = [];
        while ($row = $result->fetch_assoc()) {
            $leaves[] = $row;
        }
        return ["success" => true, "data" => $leaves];
    }

    public function getAllRequests() {
        $sql = "SELECT * FROM {$this->table}";
        return $this->conn->query($sql);
    }

    public function updateStatus($id, $status) {
        $sql = "UPDATE {$this->table} SET status = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $status, $id);
        return $stmt->execute();
    }

    public function getLeaveById($id, $employee_id) {
        $sql = "SELECT id, employee_id, employee_name, leave_type, start_date, end_date, reason, status, created_at FROM {$this->table} WHERE id = ? AND employee_id = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param("ii", $id, $employee_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
}
?>
