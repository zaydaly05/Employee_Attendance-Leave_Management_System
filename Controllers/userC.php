<?php
// UserC.php - User Controller
// This controller handles user-related requests (login, signup, logout)

require_once __DIR__ . '/../Models/user.php';

class UserC {
    private $userModel;

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
            $_SESSION['flash'] = 'Email and password are required.';
            header('Location: /login');
            exit;
        }

        $user = $this->userModel->login($email, $password);

        if ($user) {
            // Authentication successful
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'] ?? '';
            $_SESSION['user_email'] = $user['email'] ?? '';
            $_SESSION['user_role'] = $user['role'] ?? 'employee';
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
        $role = $_POST['role'] ?? 'employee';

        if ($name === '' || $email === '' || $password === '') {
            $_SESSION['flash'] = 'Name, email and password are required.';
            header('Location: /signup');
            exit;
        }

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
