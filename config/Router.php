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
            case 'contact':
                return $this->handleContactPage($method);
            case 'admin':
                return $this->handleAdminPage($params);
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
        
        $category = isset($params['category']) ? $params['category'] : null;
        $search = isset($params['search']) ? $params['search'] : null;
        $recipes = $controller->index($category, $search);
        $categories = $controller->getCategories();

        return [
            'view' => 'all_recipes',
            'viewData' => [
                'recipes' => $recipes,
                'categories' => $categories,
                'search' => $search,
                'category' => $category
            ]
        ];
    }

    private function handleLandingPage(): array
    {
        try {
            error_log("Handling landing page request");

            // Get featured recipes
            $query = "SELECT id, name, description, image_url, category, created_at FROM recipes WHERE featured = 1";
            error_log("Executing featured recipes query: " . $query);

            $stmt = $this->dbh->prepare($query);
            $stmt->execute();
            $featuredRecipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log("Featured recipes fetched: " . count($featuredRecipes));

            // Get recent recipes
            $recentQuery = "SELECT id, name, description, image_url, category, created_at FROM recipes ORDER BY created_at DESC LIMIT 6";
            error_log("Executing recent recipes query: " . $recentQuery);

            $stmt = $this->dbh->prepare($recentQuery);
            $stmt->execute();
            $recentRecipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log("Recent recipes fetched: " . count($recentRecipes));

            return [
                'view' => 'landing',
                'viewData' => [
                    'featuredRecipes' => $featuredRecipes,
                    'recentRecipes' => $recentRecipes
                ]
            ];
        } catch (PDOException $e) {
            error_log("Error in handleLandingPage: " . $e->getMessage());
            return [
                'view' => 'landing',
                'viewData' => [
                    'error' => 'Error loading recipes. Please try again later.',
                    'featuredRecipes' => [],
                    'recentRecipes' => []
                ]
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
        require_once(__DIR__ . '/../controllers/AuthController.php');
        $authController = new AuthController($this->dbh);

        if ($method === 'POST' && isset($_POST['login'])) {
            $this->validateCsrfToken($_POST['csrf_token'] ?? '');

            $result = $authController->login($_POST);
            
            // If login was successful and we have a redirect
            if (isset($result['status']) && $result['status'] === 302) {
                return $result;
            }
            
            // If login was successful but no redirect specified
            if (isset($result['success']) && $result['success']) {
                // Redirect to admin page if user is admin
                if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']) {
                    return [
                        'status' => 302,
                        'headers' => ['Location: /index.php?page=admin']
                    ];
                }
                // Redirect to landing page for regular users
                return [
                    'status' => 302,
                    'headers' => ['Location: /index.php?page=landing']
                ];
            }

            return $result;
        }

        return ['view' => 'login'];
    }

    private function handleRegisterPage(string $method): array
    {
        if ($method === 'POST') {
            require_once(__DIR__ . '/../controllers/AuthController.php');
            $controller = new AuthController($this->dbh);
            return $controller->register($_POST);
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

    private function handleContactPage(string $method): array
    {
        require_once(__DIR__ . '/../controllers/ContactController.php');
        $controller = new ContactController($this->dbh);

        if ($method === 'POST') {
            $this->validateCsrfToken($_POST['csrf_token'] ?? '');

            $result = $controller->submitRecipe($_POST);
            
            if ($result['success']) {
                return [
                    'view' => 'contact',
                    'viewData' => [
                        'success' => true
                    ]
                ];
            }

            return [
                'view' => 'contact',
                'viewData' => [
                    'error' => $result['error'],
                    'formData' => $_POST
                ]
            ];
        }

        return ['view' => 'contact'];
    }

    private function handleAdminPage(array $params = []): array
    {
        require_once(__DIR__ . '/../controllers/AdminController.php');
        
        // Check if user is logged in and is admin
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
            return [
                'view' => 'login',
                'error' => 'Please log in as an admin to access this page.'
            ];
        }

        $adminController = new AdminController($this->dbh);

        // Handle POST requests
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                return [
                    'view' => 'admin/submissions',
                    'viewData' => [
                        'error' => 'Invalid CSRF token.'
                    ]
                ];
            }

            if (isset($_POST['action'])) {
                switch ($_POST['action']) {
                    case 'update_status':
                        if (isset($_POST['submission_id']) && isset($_POST['status'])) {
                            $success = $adminController->updateSubmissionStatus(
                                (int)$_POST['submission_id'],
                                $_POST['status']
                            );
                            return [
                                'view' => 'admin/submissions',
                                'viewData' => [
                                    'submissions' => $adminController->getSubmissions(),
                                    'success' => $success ? 'Status updated successfully.' : 'Failed to update status.'
                                ]
                            ];
                        }
                        break;

                    case 'delete_comment':
                        if (isset($_POST['comment_id'])) {
                            $success = $adminController->deleteComment((int)$_POST['comment_id']);
                            return [
                                'view' => 'admin/comments',
                                'viewData' => [
                                    'comments' => $adminController->getComments(),
                                    'success' => $success ? 'Comment deleted successfully.' : 'Failed to delete comment.'
                                ]
                            ];
                        }
                        break;
                }
            }
        }

        // Handle GET requests
        if (isset($params['tab'])) {
            switch ($params['tab']) {
                case 'comments':
                    return [
                        'view' => 'admin/comments',
                        'viewData' => [
                            'comments' => $adminController->getComments()
                        ]
                    ];
                default:
                    return [
                        'view' => 'admin/submissions',
                        'viewData' => [
                            'submissions' => $adminController->getSubmissions()
                        ]
                    ];
            }
        }

        // Default view
        return [
            'view' => 'admin/submissions',
            'viewData' => [
                'submissions' => $adminController->getSubmissions()
            ]
        ];
    }
} 