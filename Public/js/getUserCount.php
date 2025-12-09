<?php
require_once __DIR__ . '/../../Models/dbConnect.php';

// Set headers for JSON response and CORS
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

try {
    $database = new Database();
    $conn = $database->conn;

    // total users - count only approved users
    $resultUsers = $conn->query("SELECT COUNT(*) AS total FROM users WHERE approved = 1");
    if (!$resultUsers) {
        throw new Exception("Error counting users: " . $conn->error);
    }
    $totalUsers = $resultUsers->fetch_assoc()['total'];

    // total leave requests - count only approved leaves
    $resultLeaves = $conn->query("SELECT COUNT(*) AS total FROM leaves WHERE status = 'approved'");
    if (!$resultLeaves) {
        throw new Exception("Error counting leaves: " . $conn->error);
    }
    $totalLeaves = $resultLeaves->fetch_assoc()['total'];

    // celebrations (example: birthdays & anniversaries)
    $resultCelebrations = $conn->query("
        SELECT username AS name, celebration_type AS type, celebration_date AS date
        FROM celebrations
        ORDER BY celebration_date ASC
        LIMIT 10
    ");
    
    $celebrations = [];
    if ($resultCelebrations && $resultCelebrations->num_rows > 0) {
        $celebrations = $resultCelebrations->fetch_all(MYSQLI_ASSOC);
    }

    echo json_encode([
        "totalUsers" => (int)$totalUsers,
        "totalLeaves" => (int)$totalLeaves,
        "celebrations" => $celebrations
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "error" => $e->getMessage(),
        "totalUsers" => 0,
        "totalLeaves" => 0,
        "celebrations" => []
    ]);
}
