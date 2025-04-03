<?php

declare(strict_types=1);

class Router
{
    private PDO $dbh;
    private const DEFAULT_PAGE = 'landing';
    private const FEATURED_RECIPES_LIMIT = 6;

    public function __construct(PDO $dbh)
    {
        $this->dbh = $dbh;
    }

    public function dispatch(string $uri, string $method): array
    {
        $params = [];
        parse_str(parse_url($uri, PHP_URL_QUERY) ?? '', $params);
        $page = $params['page'] ?? 'landing';

        switch ($page) {
            case 'login':
                return $this->handleLoginPage($method);
            case 'register':
                return $this->handleRegisterPage($method);
            case 'logout':
                return $this->handleLogout();
            case 'recipe':
                return $this->handleRecipePage($params, $method);
            case 'about':
                return ['view' => 'about'];
            case 'all_recipes':
                return $this->handleAllRecipesPage($params);
            case 'landing':
            default:
                return $this->handleLandingPage();
        }
    }

    private function handleRecipePage(array $params, string $method): array
    {
        require_once(__DIR__ . '/../controllers/RecipeController.php');
        $controller = new RecipeController($this->dbh);
        
        if (!isset($params['id'])) {
            return ['view' => '404'];
        }

        $recipeId = filter_var($params['id'], FILTER_VALIDATE_INT);
        if (!$recipeId) {
            return ['view' => '404'];
        }

        // Handle POST requests
        if ($method === 'POST') {
            error_log("POST request received for recipe $recipeId");
            error_log("POST data: " . print_r($_POST, true));
            
            $this->validateCsrfToken($_POST['csrf_token'] ?? '');

            // Handle comment deletion
            if (isset($_POST['delete_comment']) && isset($_POST['comment_id'])) {
                $commentId = filter_var($_POST['comment_id'], FILTER_VALIDATE_INT);
                
                if (!isset($_SESSION['user_id'])) {
                    return [
                        'view' => 'recipe',
                        'viewData' => [
                            'error' => 'You must be logged in to delete comments.',
                            'recipe' => $controller->show($recipeId)['recipe'],
                            'comments' => $controller->show($recipeId)['comments']
                        ]
                    ];
                }
                
                if ($commentId) {
                    $success = $controller->deleteComment($commentId, $_SESSION['user_id']);
                    if ($success) {
                        return [
                            'status' => 302,
                            'headers' => ["Location: ?page=recipe&id=$recipeId"]
                        ];
                    } else {
                        return [
                            'view' => 'recipe',
                            'viewData' => [
                                'error' => 'You can only delete your own comments.',
                                'recipe' => $controller->show($recipeId)['recipe'],
                                'comments' => $controller->show($recipeId)['comments']
                            ]
                        ];
                    }
                }
            }

            // Handle comment submission
            if (isset($_POST['comment'])) {
                if (!isset($_SESSION['user_id'])) {
                    return [
                        'view' => 'recipe',
                        'viewData' => [
                            'error' => 'You must be logged in to comment.',
                            'recipe' => $controller->show($recipeId)['recipe'],
                            'comments' => $controller->show($recipeId)['comments']
                        ]
                    ];
                }

                if (empty($_POST['content'])) {
                    return [
                        'view' => 'recipe',
                        'viewData' => [
                            'error' => 'Comment content is required',
                            'recipe' => $controller->show($recipeId)['recipe'],
                            'comments' => $controller->show($recipeId)['comments']
                        ]
                    ];
                }

                $commentData = [
                    'recipe_id' => $recipeId,
                    'user_id' => $_SESSION['user_id'],
                    'content' => $this->sanitizeInput($_POST['content'] ?? '')
                ];
                $result = $controller->storeComment(
                    $recipeId,
                    CommentDTO::fromArray($commentData)
                );
                if ($result['success']) {
                    return [
                        'status' => 302,
                        'headers' => ["Location: ?page=recipe&id=$recipeId"]
                    ];
                }
            }
        }

        // Handle GET requests with actions
        if (isset($params['action']) && $params['action'] === 'like_comment' && isset($params['comment_id'])) {
            error_log("Like action detected for comment {$params['comment_id']}");
            
            if (!isset($_SESSION['user_id'])) {
                return [
                    'view' => 'recipe',
                    'viewData' => [
                        'error' => 'You must be logged in to like comments.',
                        'recipe' => $controller->show($recipeId)['recipe'],
                        'comments' => $controller->show($recipeId)['comments']
                    ]
                ];
            }

            $commentId = filter_var($params['comment_id'], FILTER_VALIDATE_INT);
            if ($commentId) {
                $success = $controller->handleLike($commentId, $_SESSION['user_id']);
                if ($success) {
                    return [
                        'status' => 302,
                        'headers' => ["Location: ?page=recipe&id=$recipeId"]
                    ];
                }
            }
        }

        // Fetch recipe data
        $data = $controller->show($recipeId);
        if (empty($data['recipe'])) {
            return ['view' => '404'];
        }

        return [
            'view' => 'recipe',
            'viewData' => [
                'recipe' => $data['recipe'],
                'comments' => $data['comments']
            ]
        ];
    }

    private function handleAllRecipesPage(array $params = []): array
    {
        require_once(__DIR__ . '/../controllers/RecipeController.php');
        $controller = new RecipeController($this->dbh);
        $recipes = $controller->index();

        return [
            'view' => 'all_recipes',
            'viewData' => ['recipes' => $recipes]
        ];
    }

    private function handleLandingPage(): array
    {
        try {
            error_log("Handling landing page request");

            $query = "SELECT id, name, description, image_url, category, created_at FROM recipes WHERE featured = 1";
            error_log("Executing featured recipes query: " . $query);

            $stmt = $this->dbh->prepare($query);
            $stmt->execute();
            $featuredRecipes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            error_log("Found " . count($featuredRecipes) . " featured recipes");
            foreach ($featuredRecipes as $recipe) {
                error_log("Featured recipe: " . $recipe['name'] . " (ID: " . $recipe['id'] . ")");
            }

            return [
                'status' => 200,
                'view' => 'landing',
                'viewData' => [
                    'featuredRecipes' => $featuredRecipes
                ]
            ];
        } catch (PDOException $e) {
            error_log("Database error in handleLandingPage: " . $e->getMessage());
            error_log("Error code: " . $e->getCode());
            error_log("Error info: " . print_r($e->errorInfo, true));
            return [
                'status' => 500,
                'view' => 'error',
                'viewData' => ['error' => 'Database error: ' . $e->getMessage()]
            ];
        } catch (Exception $e) {
            error_log("General error in handleLandingPage: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return [
                'status' => 500,
                'view' => 'error',
                'viewData' => ['error' => 'An unexpected error occurred']
            ];
        }
    }

    private function sanitizeInput(string $input): string
    {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    private function validateCsrfToken(string $token): void
    {
        if (!isset($_SESSION['csrf_token'])) {
            error_log("CSRF token not found in session");
            throw new Exception('CSRF token validation failed: token not found in session');
        }
        
        if (!hash_equals($_SESSION['csrf_token'], $token)) {
            error_log("CSRF token validation failed: token mismatch");
            throw new Exception('CSRF token validation failed: token mismatch');
        }
    }

    private function handleLoginPage(string $method): array
    {
        require_once(__DIR__ . '/Auth.php');
        $auth = new Auth($this->dbh);

        if ($method === 'POST' && isset($_POST['login'])) {
            $this->validateCsrfToken($_POST['csrf_token'] ?? '');

            $username = $this->sanitizeInput($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($username) || empty($password)) {
                return [
                    'view' => 'login',
                    'viewData' => ['error' => 'Username and password are required']
                ];
            }

            $result = $auth->authenticate($username, $password);
            if ($result['success']) {
                return [
                    'status' => 302,
                    'headers' => ['Location: ?page=landing']
                ];
            }

            return [
                'view' => 'login',
                'viewData' => ['error' => $result['message']]
            ];
        }

        return ['view' => 'login'];
    }

    private function handleRegisterPage(string $method): array
    {
        require_once(__DIR__ . '/Auth.php');
        $auth = new Auth($this->dbh);

        if ($method === 'POST' && isset($_POST['register'])) {
            $this->validateCsrfToken($_POST['csrf_token'] ?? '');

            $username = $this->sanitizeInput($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            if (empty($username) || empty($password) || empty($confirmPassword)) {
                return [
                    'view' => 'register',
                    'viewData' => ['error' => 'All fields are required']
                ];
            }

            if ($password !== $confirmPassword) {
                return [
                    'view' => 'register',
                    'viewData' => ['error' => 'Passwords do not match']
                ];
            }

            if (strlen($password) < 8) {
                return [
                    'view' => 'register',
                    'viewData' => ['error' => 'Password must be at least 8 characters long']
                ];
            }

            $result = $auth->register($username, $password);
            if ($result['success']) {
                return [
                    'status' => 302,
                    'headers' => ['Location: ?page=landing']
                ];
            }

            return [
                'view' => 'register',
                'viewData' => ['error' => $result['message']]
            ];
        }

        return ['view' => 'register'];
    }

    private function handleLogout(): array
    {
        require_once(__DIR__ . '/Auth.php');
        $auth = new Auth($this->dbh);
        $auth->logout();

        return [
            'status' => 302,
            'headers' => ['Location: ?page=landing']
        ];
    }
} 