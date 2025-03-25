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

// Routing: Check if a page is requested (default to 'landing' if no page is provided)
$page = $_GET['page'] ?? 'landing'; // Default to 'landing' page if not set

// Handle displaying individual recipe
if ($page === 'recipe' && isset($_GET['id'])) {
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

// Fetch categories from the database for the filter
$categoryStmt = $dbh->query("SELECT DISTINCT category FROM recipes");
$categories = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);

// Query for landing page (Featured Recipes)
$featuredStmt = $dbh->query("SELECT * FROM recipes WHERE featured = 1 LIMIT 6");
$featuredRecipes = $featuredStmt->fetchAll(PDO::FETCH_ASSOC);

// Search and Filter Query for All Recipes Page
if ($page === 'all_recipes') {
    $sql = "SELECT * FROM recipes";

    // Handle search term if provided
    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $searchTerm = '%' . $_GET['search'] . '%'; // Add wildcard for partial matching
        $sql .= " WHERE name LIKE :searchTerm";
    }

    // Handle category filter if provided
    if (isset($_GET['category']) && !empty($_GET['category'])) {
        $category = $_GET['category'];
        if (isset($searchTerm)) {
            $sql .= " AND category = :category";
        } else {
            $sql .= " WHERE category = :category";
        }
    }

    // Prepare the query for recipes
    $stmt = $dbh->prepare($sql);

    // Bind parameters for search and category filters if they exist
    if (isset($searchTerm)) {
        $stmt->bindParam(':searchTerm', $searchTerm, PDO::PARAM_STR);
    }
    if (isset($category)) {
        $stmt->bindParam(':category', $category, PDO::PARAM_STR);
    }

    // Execute the query
    $stmt->execute();

    // Fetch all recipes for the All Recipes page
    $recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Include the header view
include('../views/header.php');

// Include the content/view depending on the page
switch ($page) {
    case 'recipe':
        // If recipe exists, display the recipe page
        include('../views/recipe.php');
        break;

    case 'all_recipes':
        // Show all recipes with search and filters
        include('../views/all_recipes.php');
        break;
    case 'about':
        include('../views/about.php'); // Include the About page view
        break;
    case 'landing':
    default:
        // Show the landing page with featured recipes
        include('../views/landing.php');
        break;
}

// Include the footer view
include('../views/footer.php');
