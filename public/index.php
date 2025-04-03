<?php

declare(strict_types=1);

// Start session before any output
session_start();

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', '/var/www/html/php_error.log');

error_log("Starting application...");

// Load dependencies
require_once __DIR__ . '/../vendor/autoload.php';

// Environment setup
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

error_log("Environment variables loaded");

// Initialize core components
try {
    // Database connection
    require_once(__DIR__ . '/../config/Database.php');
    $database = new Database();
    $dbh = $database->connect();
    
    error_log("Database connection initialized");

    // CSRF Protection
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        error_log("New CSRF token generated");
    }

    // Initialize Router
    require_once(__DIR__ . '/../config/Router.php');
    $router = new Router($dbh);

    // Get the current URI
    $uri = $_SERVER['REQUEST_URI'];
    $method = $_SERVER['REQUEST_METHOD'];

    error_log("Processing request: $method $uri");

    // Handle the request
    $result = $router->dispatch($uri, $method);

    error_log("Request processed. View: " . $result['view']);
    error_log("ViewData contents before view: " . print_r($result['viewData'] ?? [], true));
    
    // Send response
    if (isset($result['status'])) {
        http_response_code($result['status']);
    }
    
    if (isset($result['headers'])) {
        foreach ($result['headers'] as $header) {
            header($header);
        }
    }
    
    if (isset($result['view'])) {
        // Extract view data
        $viewData = $result['viewData'] ?? [];
        error_log("ViewData extracted for view: " . print_r($viewData, true));
        
        // Include header
        include('../views/header.php');
        
        // Include the view
        $viewPath = __DIR__ . '/../views/' . $result['view'] . '.php';
        if (file_exists($viewPath)) {
            require $viewPath;
        } else {
            error_log("View not found: $viewPath");
            http_response_code(404);
            echo "404 - Page not found";
        }
        
        // Include footer
        include('../views/footer.php');
    } elseif (isset($result['json'])) {
        header('Content-Type: application/json');
        echo json_encode($result['json']);
    }

} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    error_log("Error trace: " . $e->getTraceAsString());
    http_response_code(500);
    $error = "Database error: " . $e->getMessage();
    include('../views/error.php');
} catch (Exception $e) {
    error_log("General error: " . $e->getMessage());
    error_log("Error trace: " . $e->getTraceAsString());
    http_response_code(500);
    $error = "An unexpected error occurred: " . $e->getMessage();
    include('../views/error.php');
}
