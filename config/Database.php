<?php
class Database
{
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $conn;

    public function __construct()
    {
        // Load environment variables (already loaded by Dotenv in index.php)
        $this->host = $_ENV['DB_HOST'];
        $this->db_name = $_ENV['DB_NAME'];
        $this->username = $_ENV['DB_USER'];
        $this->password = $_ENV['DB_PASS'];
        
        // Debug connection details
        error_log("Database connection details:");
        error_log("Host: " . $this->host);
        error_log("Database: " . $this->db_name);
        error_log("Username: " . $this->username);
    }

    // In Database.php connect() method
    public function connect()
    {
        $this->conn = null;
        error_log("Attempting database connection with:");
        error_log("Host: " . $this->host);
        error_log("Database: " . $this->db_name);
        error_log("Username: " . $this->username);

        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name;
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            error_log("Database connection established successfully");

            // Test query to verify connection
            $stmt = $this->conn->query("SELECT COUNT(*) as count FROM recipes");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            error_log("Number of recipes in database: " . $result['count']);

        } catch(PDOException $e) {
            error_log("Connection Error: " . $e->getMessage());
            error_log("Error Code: " . $e->getCode());
            error_log("Error Info: " . print_r($e->errorInfo, true));
            throw $e;
        }

        return $this->conn;
    }
}
