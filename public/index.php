<?php

declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '1');

// ======================
// Initial Setup
// ======================

// Load dependencies
require_once __DIR__ . '/../vendor/autoload.php';
require_once(__DIR__ . '/../config/Database.php');

// Environment setup
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Database connection
try {
    $database = new Database();
    $dbh = $database->connect();
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// ======================
// Configuration
// ======================
const DEFAULT_PAGE = 'landing';
const FEATURED_RECIPES_LIMIT = 6;

// ======================
// Security Functions
// ======================
function sanitizeInput(string $input): string
{
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// ======================
// Routing & Logic
// ======================
$page = isset($_GET['page']) ? sanitizeInput($_GET['page']) : DEFAULT_PAGE;
$title = "Welcome to Curfew Comforts";
$viewData = [];

try {
    // Handle Recipe Page
    if ($page === 'recipe' && isset($_GET['id'])) {
        require_once(__DIR__ . '/../controllers/RecipeController.php');
        $controller = new RecipeController($dbh);
        $recipeId = filter_var($_GET['id'], FILTER_VALIDATE_INT);

        if (!$recipeId) {
            throw new InvalidArgumentException("Invalid recipe ID");
        }

        // Handle Form Submissions
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['comment'])) {
                $success = $controller->storeComment(
                    $recipeId,
                    [
                        'user_name' => sanitizeInput($_POST['user_name'] ?? ''),
                        'content' => sanitizeInput($_POST['content'] ?? '')
                    ]
                );
                if ($success) {
                    header("Location: ?page=recipe&id=$recipeId");
                    exit;
                }
            } elseif (isset($_GET['action']) && $_GET['action'] === 'like_comment') {
                if (isset($_POST['user_name'], $_GET['comment_id'])) {
                    $commentId = filter_var($_GET['comment_id'], FILTER_VALIDATE_INT);
                    $userName = sanitizeInput($_POST['user_name']);
                    if ($commentId) {
                        $controller->handleLike($commentId, $userName);
                    }
                }
                header("Location: ?page=recipe&id=$recipeId");
                exit;
            }
        }

        // Fetch Recipe Data
        $data = $controller->show($recipeId);
        if (empty($data['recipe'])) {
            $title = "Recipe Not Found";
        } else {
            $title = sanitizeInput($data['recipe']['name']);
            $viewData = [
                'recipe' => $data['recipe'],
                'comments' => $data['comments']
            ];
        }
    }

    // Handle All Recipes Page
    if ($page === 'all_recipes') {
        $searchTerm = isset($_GET['search']) ? sanitizeInput($_GET['search']) : null;
        $category = isset($_GET['category']) ? sanitizeInput($_GET['category']) : null;

        $sql = "SELECT * FROM recipes";
        $params = [];
        $conditions = [];

        if ($searchTerm) {
            $conditions[] = "name LIKE :search";
            $params[':search'] = "%$searchTerm%";
        }
        if ($category) {
            $conditions[] = "category = :category";
            $params[':category'] = $category;
        }
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $stmt = $dbh->prepare($sql);
        $stmt->execute($params);
        $viewData['recipes'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Handle Landing Page
    if ($page === 'landing') {
        try {
            $viewData['featuredRecipes'] = [];
            $stmt = $dbh->prepare("
                SELECT id, name, description 
                FROM recipes 
                WHERE featured = 1 
                ORDER BY created_at DESC 
                LIMIT :limit
            ");
            $stmt->bindValue(':limit', FEATURED_RECIPES_LIMIT, PDO::PARAM_INT);
            $stmt->execute();
            $viewData['featuredRecipes'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Featured recipes error: " . $e->getMessage());
            $viewData['error'] = "Couldn't load featured recipes: " . $e->getMessage();
        }
    }

    // Common Data
    $categories = $dbh->query("SELECT DISTINCT category FROM recipes")
        ->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $viewData['error'] = "A database error occurred";
} catch (Exception $e) {
    error_log("General error: " . $e->getMessage());
    $viewData['error'] = "An unexpected error occurred";
}

// ======================
// View Rendering
// ======================
include('../views/header.php');

switch ($page) {
    case 'recipe':
        isset($viewData['recipe']) ? include('../views/recipe.php') : include('../views/404.php');
        break;
    case 'all_recipes':
        include('../views/all_recipes.php');
        break;
    case 'about':
        include('../views/about.php');
        break;
    case 'landing':
    default:
        include('../views/landing.php');
        break;
}

include('../views/footer.php');
