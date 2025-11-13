<?php
// routes.php
// This file is included by index.php
// It expects $path to be defined in index.php

// --- Define Base Paths ---
// __DIR__ is the root folder (where this routes.php file is)
$view_path = __DIR__ . '/Views/';
$controller_path = __DIR__ . '/Controllers/';
$model_path = __DIR__ . '/Models/';

// --- Calculate Base URL for Assets (CSS, JS, Images) ---
// This ensures asset paths work correctly regardless of the route
$script_name = dirname($_SERVER['SCRIPT_NAME']);
$base_url = rtrim(str_replace('\\', '/', $script_name), '/');
if ($base_url === '' || $base_url === '.' || $base_url === '/') {
    $base_url = '';
} else {
    // Ensure it starts with / and ends with /
    if ($base_url[0] !== '/') {
        $base_url = '/' . $base_url;
    }
    $base_url = $base_url . '/';
}
// Make base_url available to views
// Note: Views should be .php files to use this variable
$base_url_for_views = $base_url;

// --- Include Models (needed by controllers) ---
require_once $model_path . 'dbConnect.php';

// --- Include Controllers ---
// We include the controller here so the routes can use it
require_once $controller_path . 'userC.php';
require_once $controller_path . 'leaveC.php';
require_once $controller_path . 'attendanceC.php';

// --- Create Controller Instances ---
$userController = new UserC();
$leaveController = new LeaveC();
$attendanceController = new AttendanceC(); 

// --- URL Routing ---
// This switch statement reads the $path variable from index.php
// and loads the correct file.

// Check if path variable is set
if (!isset($path)) {
    die("Error: Path variable is not set. Make sure index.php is being accessed.");
}

// Debug: Show the path being matched (remove in production)
echo "<!-- Debug: Path = " . htmlspecialchars($path) . " | REQUEST_URI = " . htmlspecialchars($_SERVER['REQUEST_URI']) . " -->";

// Path is already converted to lowercase in index.php, but ensure it's lowercase here too
$path_lower = strtolower($path);

switch ($path_lower) {

    case '/':
        // Extract base_url for use in view
        $base_url = $base_url_for_views;
        require $view_path . 'index.php';
        break;
        
    case '/login':
        $base_url = $base_url_for_views;
        require $view_path . 'login.php';
        break;

    case '/signup':
        $base_url = $base_url_for_views;
        require $view_path . 'SignUp.php';
        break;

    case '/reset-password':
        $base_url = $base_url_for_views;
        require $view_path . 'resetPassword.php';
        break;

    case '/dashboard':
        // TODO: Add security check here to make sure user is logged in
        $base_url = $base_url_for_views;
        require $view_path . 'userDashboard.php';
        break;

    case '/history':
        // TODO: Add security check
        $base_url = $base_url_for_views;
        require $view_path . 'history dashboard.php';
        break;

    case '/leave-summary':
        // TODO: Add security check
        $base_url = $base_url_for_views;
        require $view_path . 'leaveSummary.php';
        break;

    case '/request-time-off':
        // TODO: Add security check
        $base_url = $base_url_for_views;
        require $view_path . 'requestTimeOff.php';
        break;

    // --- Action Routes (Controllers) ---
    // These routes handle form submissions (e.g., POST requests)
    // They call functions (methods) in your controllers.

    case '/auth/login':
        $userController->handleLogin();
        break;

    case '/auth/signup':
        $userController->handleSignup();
        break;

    case '/auth/logout':
        $userController->handleLogout();
        break;

    case '/leave/request':
        $leaveController->handleRequestLeave();
        break;

    case '/leave/summary':
        $leaveController->getLeaveSummary();
        break;

    case '/attendance/mark':
        $attendanceController->handleMarkAttendance();
        break;

    case '/attendance/history':
        $attendanceController->getAttendanceHistory();
        break;

    // --- 404 Not Found ---
    // This 'default' case handles any URL that doesn't match above
    default:
        http_response_code(404);
        echo "<h1>404 Page Not Found</h1>";
        echo "<p>The page you are looking for was not found.</p>";
        // Debug: Show the path that was requested (remove this in production)
        echo "<p>Debug: Requested path was: <strong>" . htmlspecialchars($path) . "</strong></p>";
        echo "<p>Available routes: /, /login, /signup, /dashboard, /history, /leave-summary, /request-time-off</p>";
        break;
}
?>