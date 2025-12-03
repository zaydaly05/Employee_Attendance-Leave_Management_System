<?php
require_once 'dbConnect.php';

class Employee {
    private $conn;
    private $table = "employees";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->conn;
    }

    public function getAll() {
        $sql = "SELECT * FROM {$this->table}";
        return $this->conn->query($sql);
    }

    public function getById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function create($name, $email, $department_name, $role_id) {
        $sql = "INSERT INTO {$this->table} (name, email, department_name, role_id) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssii",$name,$email, $department_name, $role_id);
        return $stmt->execute();
    }
}
?>
