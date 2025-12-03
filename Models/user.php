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
   public function register($name, $email, $password, $role = 'User', $forAdminPassword, $approved = 0) {
    
    
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    
    $sql = "INSERT INTO {$this->table} (name, email, password, role, forAdminPassword, approved) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $this->conn->prepare($sql);

    if($forAdminPassword !== 'Admin@123'){
        $role = 'User';
        return ["success" => false, "message" => "Invalid admin password for registration"];
    }
    else 
        $role = 'Admin';
    
    if (!$stmt) {
        return ["success" => false, "message" => "Prepare failed: " . $this->conn->error];
    }
    
    $stmt->bind_param("ssss", $name, $email, $hashed_password, $role, $approved);
    
    if ($stmt->execute()) {
        return ["success" => true, "message" => "User registered successfully", "id" => $stmt->insert_id];
    } else {
        
        return ["success" => false, "message" => $stmt->error];
    }
}
}

?>
