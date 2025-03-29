<?php
class Database
{
    private $host;
    private $db;
    private $user;
    private $pass;
    private $dbh;

    public function __construct()
    {
        // Load environment variables (already loaded by Dotenv in index.php)
        $this->host = $_ENV['DB_HOST'];
        $this->db = $_ENV['DB_NAME'];
        $this->user = $_ENV['DB_USER'];
        $this->pass = $_ENV['DB_PASS'];
    }

    // In Database.php connect() method
    public function connect()
    {
        try {
            $dsn = "mysql:host=$this->host;dbname=$this->db";
            $this->dbh = new PDO($dsn, $this->user, $this->pass);
            $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $this->dbh;
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage()); // This will show the error
        }
    }
}
