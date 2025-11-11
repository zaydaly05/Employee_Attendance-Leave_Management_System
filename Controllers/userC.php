<?php
require_once '../Models/user.php';
header('Content-Type: application/json');

$user = new User();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? null;

    switch ($action) {
        case 'register':
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $role = $_POST['role'] ?? 'employee';
            echo json_encode($user->register($name, $email, $password, $role));
            break;

        case 'login':
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            echo json_encode($user->login($email, $password));
            break;

        default:
            echo json_encode(["success" => false, "message" => "Invalid action"]);
            break;
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method"]);
}
?>
