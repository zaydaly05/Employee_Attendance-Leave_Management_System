<?php
// UserC.php - User Controller
// This controller handles user-related requests (login, signup, logout)

require_once __DIR__ . '/../Models/user.php';

class UserC {
    private $userModel;
    private $db;
    public function __construct() {
        $this->userModel = new User();
    }

    // Handle login request
    public function handleLogin() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo 'Method Not Allowed';
            return;
        }
        
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($email === '' || $password === '') {
            // Store the attempted email before redirection
            $_SESSION['old_email'] = htmlspecialchars($email);
            $_SESSION['flash'] = 'Email and password are required.';
            header('Location: /');
            exit;
        }

        $user = $this->userModel->login($email, $password);

        if ($user) {
            // Authentication successful
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'] ?? '';
            $_SESSION['user_email'] = $user['email'] ?? '';
            $_SESSION['user_role'] = $user['role'] ?? 'user';

            // Clean up any failed login attempts' data
            unset($_SESSION['old_email']);
            // Regenerate session id to prevent fixation
            session_regenerate_id(true);
            header('Location: /dashboard');
            exit;
        }

        // Authentication failed
        // Store the attempted email so the user doesn't have to re-type it
        $_SESSION['old_email'] = htmlspecialchars($email);
        $_SESSION['flash'] = 'Invalid email or password.';
        header('Location: /');
        exit;
    }
    private const ADMIN_SECRET_KEY = 'supersecretadmin123';
    // Handle signup/registration request
    public function handleSignup() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo 'Method Not Allowed';
            return;
        }

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirmPassword'] ?? '';
        $role = $_POST['role'] ?? 'employee';

        if ($name === '' || $email === '' || $password === '') {
            $_SESSION['flash'] = 'Name, email and password are required.';
            header('Location: /signup');
            exit;
        }

        // Validate password confirmation
        if ($password !== $confirmPassword) {
            $_SESSION['flash'] = 'Passwords do not match.';
            header('Location: /signup');
            exit;
        }
            // New variable to capture the secret key
        $adminKey = $_POST['adminKey'] ?? ''; 
        
        // Default role is'user'
        $role = 'User';

        // --- Role Logic ---
        // If the submitted admin key matches the secret key, change the role to 'admin'
        if (!empty($adminKey) && $adminKey === self::ADMIN_SECRET_KEY) {
            $role = 'admin';
        }
        // --- End Role Logic ---
        $result = $this->userModel->register($name, $email, $password, $role);

        if ($result['success']) {
            $_SESSION['flash'] = 'Registration successful! Please login.';
            header('Location: /login');
            exit;
        } else {
            $_SESSION['flash'] = $result['message'] ?? 'Registration failed.';
            header('Location: /signup');
            exit;
        }
    }

    // Handle logout request
    public function handleLogout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
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

    // API endpoint for AJAX requests (if needed)
    public function apiLogin() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $user = $this->userModel->login($email, $password);
            if ($user) {
                echo json_encode(["success" => true, "message" => "Login successful", "user" => $user]);
            } else {
                echo json_encode(["success" => false, "message" => "Invalid credentials"]);
            }
        } else {
            echo json_encode(["success" => false, "message" => "Invalid request method"]);
        }
    }
}
?>
