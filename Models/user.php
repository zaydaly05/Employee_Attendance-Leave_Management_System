<?php
require_once 'dbConnect.php';

class User {
    private $conn;
    private $table = "users";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->conn;
    }

    public function login($email, $password) {
        $sql = "SELECT * FROM {$this->table} WHERE email = ? AND password = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $email, $password);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
   
    public function register($name, $email, $password, $role = 'employee') {
        $sql = "INSERT INTO {$this->table} (name, email, password, role) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return ["success" => false, "message" => "Prepare failed: " . $this->conn->error];
        }
        $stmt->bind_param("ssss", $name, $email, $password, $role);
        if ($stmt->execute()) {
            return ["success" => true, "message" => "User registered successfully", "id" => $stmt->insert_id];
        } else {
            return ["success" => false, "message" => $stmt->error];
        }
    }
}

?>
