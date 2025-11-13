<?php
require_once 'dbConnect.php';

class Leave {
    private $conn;
    private $table = "leaves";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->conn;
    }

    public function requestLeave($employee_id, $employee_name, $leave_type, $start_date, $end_date, $reason) {
        $sql = "INSERT INTO {$this->table} (employee_id, employee_name, leave_type, start_date, end_date, reason, status) VALUES (?, ?, ?, ?, ?, ?, 'Pending')";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return ["success" => false, "message" => "Prepare failed: " . $this->conn->error];
        }
        $stmt->bind_param("isssss", $employee_id, $employee_name, $leave_type, $start_date, $end_date, $reason);
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
}
?>
