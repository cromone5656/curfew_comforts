<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);


// Include Composer's autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// Load the .env file for environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Include the Database configuration class
require_once(__DIR__ . '/../config/Database.php');

// Create a new instance of the Database class
$database = new Database();

// Get the database connection
$dbh = $database->connect();

// Set the page title (default to the landing page title)
$title = "Welcome to Curfew Comforts";

// Check if we're on the recipe page
if (isset($_GET['id'])) {
    // Use a controller to handle fetching the recipe
    require_once(__DIR__ . '/../controllers/RecipeController.php');
    $controller = new RecipeController($dbh);
    $recipe = $controller->show($_GET['id']); // Fetch the recipe

    // If the recipe is not found, we'll set a different title
    if (!$recipe) {
        $title = "Recipe Not Found";
    } else {
        $title = $recipe['name'];
    }
}

if (!isset($_GET['id'])) {
    // query to get all the recipes for the landing page
    $stmt = $dbh->prepare("SELECT id, name FROM recipes");
    $stmt->execute();
    $recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
// Include the header view
include('../views/header.php');

// Include the content/view depending on the page
if (isset($recipe)) {
    // If recipe exists, display the recipe page
    include('../views/recipe.php');
} else {
    // If no recipe ID, show the landing page
    include('../views/landing.php');
}

// Include the footer view
include('../views/footer.php');
?>
