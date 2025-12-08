<?php
require "dbConnect.php"; // your DB connection

// total users
$resultUsers = $conn->query("SELECT COUNT(*) AS total FROM users");
$totalUsers = $resultUsers->fetch_assoc()['total'];

// total leave requests
$resultLeaves = $conn->query("SELECT COUNT(*) AS total FROM leaves WHERE status = 'approved'");
$totalLeaves = $resultLeaves->fetch_assoc()['total'];

// celebrations (example: birthdays & anniversaries)
$resultCelebrations = $conn->query("
    SELECT username AS name, celebration_type AS type, celebration_date AS date
    FROM celebrations
    ORDER BY celebration_date ASC
    LIMIT 10
");

$celebrations = $resultCelebrations->fetch_all(MYSQLI_ASSOC);

echo json_encode([
    "totalUsers" => $totalUsers,
    "totalLeaves" => $totalLeaves,
    "celebrations" => $celebrations
]);
