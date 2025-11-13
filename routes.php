<?php
// routes.php
// This file is included by Public/index.php
// It expects $path to be defined in index.php

// --- Define Base Paths ---
// __DIR__ is the root folder (where this routes.php file is)
$view_path = __DIR__ . '/Views/';
$controller_path = __DIR__ . '/Controllers/';

// --- Include Controllers ---
// We include the controller here so the routes can use it
require_once $controller_path . 'userC.php';

// You can include other models/controllers as needed
// require_once __DIR__ . '/Models/dbConnect.php'; // Will be needed by controller

// --- Create Controller Instances ---
// Assumes your class in userC.php is named 'UserC'
// FIX 1: Change 'new User()' to 'new UserC()'
$userController = new User(); 

// --- URL Routing ---
// This switch statement reads the $path variable from index.php
// and loads the correct file.

switch ($path) {

    case '/': // Root of your site
    case '/login':
        require $view_path . 'login.html';
        break;

    case '/signup':
        require $view_path . 'SignUp.html'; // Matches your file name
        break;

    case '/reset-password':
        require $view_path . 'resetPassword.html';
        break;

    case '/dashboard':
        // TODO: Add security check here to make sure user is logged in
        require $view_path . 'userDashboard.html';
        break;

    case '/history':
        // TODO: Add security check
        require $view_path . 'history dashboard.html'; // Matches your file name
        break;

    case '/leave-summary':
        // TODO: Add security check
        require $view_path . 'leaveSummary.html';
        break;

    case '/request-time-off':
        // TODO: Add security check
        require $view_path . 'requestTimeOff.html';
        break;

    // --- Action Routes (Controllers) ---
    // These routes handle form submissions (e.g., POST requests)
    // They call functions (methods) in your UserC.php controller.

    case '/auth/login':
        // FIX 2: Add the method call and the 'break;'
        $userController->handleLogin();
        break;

    case '/auth/signup':
        // Assumes you have a 'handleSignup' method in your UserC class
        $userController->handleSignup();
        break;

    case '/auth/logout':
        // Assumes you have a 'handleLogout' method
        $userController->handleLogout();
        break;

    // --- 404 Not Found ---
    // This 'default' case handles any URL that doesn't match above
    default:
        http_response_code(404);
        echo "<h1>404 Page Not Found</h1>";
        echo "<p>The page you are looking for was not found.</p>";
        break;
}
?>