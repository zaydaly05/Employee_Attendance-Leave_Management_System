<?php
// Public/index.php
// This is the single entry point for your application.

// 1. Start the session (for login)
session_start();

// 2. Get the requested URL path
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);

// Optional: If your project runs in a subfolder (e.g., localhost/my_project/)
// you might need to strip the subfolder name from the path.
// $path = str_replace('/my_project', '', $path);

// 3. Define the project's root directory
// We go one level up ('..') from 'Public' to get to the root
$root_dir = __DIR__ . '/..';

// 4. Include the router
// The router file will handle the request and show the correct page.
// We use the $path variable inside routes.php
require_once $root_dir . '/routes.php';

?>