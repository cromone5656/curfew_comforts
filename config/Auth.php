<?php

declare(strict_types=1);

use PDO;

class Auth
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Authenticate a user
     * 
     * @param string $username Username
     * @param string $password Password
     * @return array{success: bool, user_id: int|null, message: string} Authentication result
     */
    public function authenticate(string $username, string $password): array
    {
        try {
            $stmt = $this->db->prepare("
                SELECT id, password_hash
                FROM users
                WHERE username = :username
            ");
            $stmt->execute(['username' => $username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                return [
                    'success' => false,
                    'user_id' => null,
                    'message' => 'Invalid username or password'
                ];
            }

            if (!password_verify($password, $user['password_hash'])) {
                return [
                    'success' => false,
                    'user_id' => null,
                    'message' => 'Invalid username or password'
                ];
            }

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $username;

            return [
                'success' => true,
                'user_id' => $user['id'],
                'message' => 'Login successful'
            ];
        } catch (PDOException $e) {
            error_log("Authentication error: " . $e->getMessage());
            return [
                'success' => false,
                'user_id' => null,
                'message' => 'An error occurred during authentication'
            ];
        }
    }

    /**
     * Register a new user
     * 
     * @param string $username Username
     * @param string $password Password
     * @return array{success: bool, user_id: int|null, message: string} Registration result
     */
    public function register(string $username, string $password): array
    {
        try {
            // Check if username already exists
            $stmt = $this->db->prepare("
                SELECT 1 FROM users WHERE username = :username
            ");
            $stmt->execute(['username' => $username]);
            
            if ($stmt->fetch()) {
                return [
                    'success' => false,
                    'user_id' => null,
                    'message' => 'Username already exists'
                ];
            }

            // Insert new user
            $stmt = $this->db->prepare("
                INSERT INTO users (username, password_hash)
                VALUES (:username, :password_hash)
            ");

            $success = $stmt->execute([
                'username' => $username,
                'password_hash' => password_hash($password, PASSWORD_DEFAULT)
            ]);

            if (!$success) {
                return [
                    'success' => false,
                    'user_id' => null,
                    'message' => 'Failed to create user'
                ];
            }

            $userId = (int)$this->db->lastInsertId();
            $_SESSION['user_id'] = $userId;
            $_SESSION['username'] = $username;

            return [
                'success' => true,
                'user_id' => $userId,
                'message' => 'Registration successful'
            ];
        } catch (PDOException $e) {
            error_log("Registration error: " . $e->getMessage());
            return [
                'success' => false,
                'user_id' => null,
                'message' => 'An error occurred during registration'
            ];
        }
    }

    /**
     * Check if a user is logged in
     * 
     * @return bool True if user is logged in
     */
    public function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']) && isset($_SESSION['username']);
    }

    /**
     * Get current user's ID
     * 
     * @return int|null User ID if logged in, null otherwise
     */
    public function getCurrentUserId(): ?int
    {
        return $_SESSION['user_id'] ?? null;
    }

    /**
     * Get current username
     * 
     * @return string|null Username if logged in, null otherwise
     */
    public function getCurrentUsername(): ?string
    {
        return $_SESSION['username'] ?? null;
    }

    /**
     * Log out the current user
     */
    public function logout(): void
    {
        unset($_SESSION['user_id'], $_SESSION['username']);
        session_regenerate_id(true);
    }
} 