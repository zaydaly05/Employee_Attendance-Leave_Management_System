<?php
require_once 'dbConnect.php';

class Leave {
    private $conn;
    private $table = "leaves";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->conn;
    }

    public function requestLeave($employee_id, $start_date, $end_date, $reason) {
        $sql = "INSERT INTO {$this->table} (employee_id, start_date, end_date, reason, status) VALUES (?, ?, ?, ?, 'Pending')";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("isss", $employee_id, $start_date, $end_date, $reason);
        return $stmt->execute();
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
