<?php

declare(strict_types=1);

use PDO;

class AuthController
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Handle user registration
     * 
     * @param array $postData POST data from the registration form
     * @return array{view: string, viewData: array} View data
     */
    public function register(array $postData): array
    {
        // Validate CSRF token
        if (!isset($postData['csrf_token']) || $postData['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
            error_log("CSRF token validation failed");
            return [
                'view' => 'register',
                'viewData' => ['error' => 'Invalid request']
            ];
        }

        // Validate required fields
        if (empty($postData['username']) || empty($postData['password']) || empty($postData['confirm_password'])) {
            error_log("Missing required fields in registration");
            return [
                'view' => 'register',
                'viewData' => ['error' => 'All fields are required']
            ];
        }

        // Validate password match
        if ($postData['password'] !== $postData['confirm_password']) {
            error_log("Password confirmation mismatch");
            return [
                'view' => 'register',
                'viewData' => ['error' => 'Passwords do not match']
            ];
        }

        // Validate password length
        if (strlen($postData['password']) < 8) {
            error_log("Password too short");
            return [
                'view' => 'register',
                'viewData' => ['error' => 'Password must be at least 8 characters long']
            ];
        }

        try {
            // Check if username already exists
            $stmt = $this->db->prepare("SELECT 1 FROM users WHERE username = :username");
            $stmt->execute(['username' => $postData['username']]);
            
            if ($stmt->fetch()) {
                error_log("Username already exists: " . $postData['username']);
                return [
                    'view' => 'register',
                    'viewData' => ['error' => 'Username already exists']
                ];
            }

            // Insert new user
            $stmt = $this->db->prepare("
                INSERT INTO users (username, password_hash, is_admin)
                VALUES (:username, :password_hash, :is_admin)
            ");

            $passwordHash = password_hash($postData['password'], PASSWORD_DEFAULT);
            if ($passwordHash === false) {
                error_log("Password hashing failed");
                return [
                    'view' => 'register',
                    'viewData' => ['error' => 'Failed to create user account']
                ];
            }

            $success = $stmt->execute([
                'username' => $postData['username'],
                'password_hash' => $passwordHash,
                'is_admin' => 0  // Explicitly set to 0 for new users
            ]);

            if (!$success) {
                $errorInfo = $stmt->errorInfo();
                error_log("Database error during registration: " . implode(", ", $errorInfo));
                return [
                    'view' => 'register',
                    'viewData' => ['error' => 'Failed to create user account']
                ];
            }

            return [
                'view' => 'login',
                'viewData' => ['success' => 'Registration successful! Please login.']
            ];
        } catch (PDOException $e) {
            error_log("Registration error: " . $e->getMessage());
            error_log("Error code: " . $e->getCode());
            error_log("Error trace: " . $e->getTraceAsString());
            return [
                'view' => 'register',
                'viewData' => ['error' => 'An error occurred during registration: ' . $e->getMessage()]
            ];
        }
    }

    /**
     * Handle user login
     * 
     * @param array $postData POST data from the login form
     * @return array{view: string, viewData: array} View data
     */
    public function login(array $postData): array
    {
        // Validate CSRF token
        if (!isset($postData['csrf_token']) || $postData['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
            error_log("CSRF token validation failed");
            return [
                'view' => 'login',
                'viewData' => ['error' => 'Invalid request']
            ];
        }

        // Validate required fields
        if (empty($postData['username']) || empty($postData['password'])) {
            error_log("Missing required fields in login");
            return [
                'view' => 'login',
                'viewData' => ['error' => 'All fields are required']
            ];
        }

        try {
            // Get user from database
            $stmt = $this->db->prepare("SELECT id, username, password_hash, is_admin FROM users WHERE username = :username");
            $stmt->execute(['username' => $postData['username']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                error_log("User not found: " . $postData['username']);
                return [
                    'view' => 'login',
                    'viewData' => ['error' => 'Invalid username or password']
                ];
            }

            // Verify password
            if (!password_verify($postData['password'], $user['password_hash'])) {
                error_log("Invalid password for user: " . $postData['username']);
                return [
                    'view' => 'login',
                    'viewData' => ['error' => 'Invalid username or password']
                ];
            }

            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['is_admin'] = (bool)$user['is_admin'];

            // Redirect to appropriate page based on admin status
            if ($user['is_admin']) {
                return [
                    'status' => 302,
                    'headers' => ['Location: /index.php?page=admin']
                ];
            } else {
                return [
                    'status' => 302,
                    'headers' => ['Location: /index.php?page=landing']
                ];
            }
        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            error_log("Error code: " . $e->getCode());
            error_log("Error trace: " . $e->getTraceAsString());
            return [
                'view' => 'login',
                'viewData' => ['error' => 'An error occurred during login: ' . $e->getMessage()]
            ];
        }
    }
} 