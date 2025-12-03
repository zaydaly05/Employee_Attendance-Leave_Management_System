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
        // $sql = "SELECT * FROM {$this->table} WHERE email = ? and approved = 1 ";
        $sql = "SELECT password_hash, email FROM {$this->table} WHERE email = ? AND approved = 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        
        // Verify password using password_verify since passwords are hashed
        if ($user && password_verify($password, $user['password_hash'])) {
            return $user;
        }
        
        return false;
    }
        public function register($name, $email, $password, $role = 'User', $forAdminPassword="101", $approved = 0) {

            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // FIX 1 & 2: Updated column names to match the database image
            $sql = "INSERT INTO {$this->table} 
                    (name, email, password_hash, role, admin_password_hash, approved) 
                    VALUES (?, ?, ?, ?, ?, ?)";

            $stmt = $this->conn->prepare($sql);

            if (!$stmt) {
                return ["success" => false, "message" => "Prepare failed: " . $this->conn->error];
            }

            // Optional: If 'forAdminPassword' is meant to be a secure password, 
            // it should also be hashed here before binding. 
            // If it is just a plain text code, leave as is.
            
            $stmt->bind_param("sssssi", 
                $name, 
                $email, 
                $hashed_password, 
                $role,
                $forAdminPassword, // This maps to admin_password_hash
                $approved
            );

            if ($stmt->execute()) {
                return ["success" => true, "message" => "User registered successfully", "id" => $stmt->insert_id];
            } else {
                return ["success" => false, "message" => $stmt->error];
            }
        }

}

?>
