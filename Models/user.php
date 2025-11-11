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
   

            private $pdo;


            // handleLogin: validates POSTed credentials, starts session and redirects on success
            public function handleLogin() {
                if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                    http_response_code(405);
                    echo 'Method Not Allowed';
                    return;
                }

                $email = trim($_POST['email'] ?? '');
                $password = $_POST['password'] ?? '';

                if ($email === '' || $password === '') {
                    $_SESSION['flash'] = 'Email and password are required.';
                    header('Location: /login');
                    exit;
                }

                // Adjust table/column names to match your schema (e.g., users, password_hash)
                $sql = 'SELECT id, password_hash, name FROM users WHERE email = :email LIMIT 1';
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([':email' => $email]);
                $user = $stmt->fetch();

                if ($user && password_verify($password, $user['password_hash'])) {
                    // Authentication successful
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['name'] ?? '';
                    // Regenerate session id to prevent fixation
                    session_regenerate_id(true);
                    header('Location: /dashboard');
                    exit;
                }

                // Authentication failed
                $_SESSION['flash'] = 'Invalid email or password.';
                header('Location: /login');
                exit;
            }

            // Minimal stubs you already call from routes.php
            public function handleSignup() {
                // implement signup handling
            }

            public function handleLogout() {
                if (session_status() === PHP_SESSION_NONE) session_start();
                $_SESSION = [];
                if (ini_get("session.use_cookies")) {
                    $params = session_get_cookie_params();
                    setcookie(session_name(), '', time() - 42000,
                        $params["path"], $params["domain"],
                        $params["secure"], $params["httponly"]
                    );
                }
                session_destroy();
                header('Location: /login');
                exit;
            }
}

?>
