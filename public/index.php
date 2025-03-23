<?php

// Include Composer's autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// Load the .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Define .env variables
$host = $_ENV['DB_HOST'];
$db = $_ENV['DB_NAME'];
$user = $_ENV['DB_USER'];
$pass = $_ENV['DB_PASS'];

// Connect to the Database
try {
    // Connect to the database
    $dbh = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get the recipe ID from the query parameter
    $recipeId = isset($_GET['id']) && is_numeric($_GET['id']) ? $_GET['id'] : null;

    // Check if the recipe ID is provided and valid
    if ($recipeId) {
        $stmt = $dbh->prepare("SELECT * FROM recipes WHERE id = :id");
        $stmt->execute(['id' => $recipeId]);
        $recipe = $stmt->fetch(PDO::FETCH_ASSOC);

        // If recipe exists, include the view and pass the data
        if ($recipe) {
            // Include the recipe view
            include __DIR__ . '/views/recipe.php';
        } else {
            echo "Recipe not found!";
        }
    } else {
        echo "Invalid or missing recipe ID.";
    }

} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}