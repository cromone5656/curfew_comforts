<?php

declare(strict_types=1);

use PDO;
use PDOException;

/**
 * Data Transfer Objects
 */
class CommentDTO
{
    public function __construct(
        public readonly int $recipeId,
        public readonly int $userId,
        public readonly string $content
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            recipeId: (int) $data['recipe_id'],
            userId: (int) $data['user_id'],
            content: trim($data['content'])
        );
    }
}

class RecipeController
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Fetch recipe details with comments and likes
     * 
     * @param int $id Recipe ID
     * @return array{recipe: array|null, comments: array} Array containing recipe and comments data
     * @throws PDOException If database query fails
     */
    public function show(int $id): array
    {
        try {
            // Get recipe details
            $recipe = $this->getRecipe($id);

            if (!$recipe) {
                return [
                    'recipe' => null,
                    'comments' => []
                ];
            }

            // Get comments with like counts
            $comments = $this->getCommentsWithLikes($id);

            return [
                'recipe' => $recipe,
                'comments' => $comments
            ];
        } catch (PDOException $e) {
            error_log("Error fetching recipe: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get a single recipe by ID
     * 
     * @param int $id Recipe ID
     * @return array|null Recipe data or null if not found
     * @throws PDOException If database query fails
     */
    private function getRecipe(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT * 
            FROM recipes 
            WHERE id = :id
        ");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Get comments with like counts for a recipe
     * 
     * @param int $recipeId Recipe ID
     * @return array Array of comments with like counts
     * @throws PDOException If database query fails
     */
    private function getCommentsWithLikes(int $recipeId): array
    {
        $stmt = $this->db->prepare("
            SELECT 
                c.*,
                u.username,
                COUNT(cl.comment_id) AS like_count,
                GROUP_CONCAT(DISTINCT lu.username) AS liked_by
            FROM comments c
            INNER JOIN users u ON c.user_id = u.id
            LEFT JOIN comment_likes cl ON c.id = cl.comment_id
            LEFT JOIN users lu ON cl.user_id = lu.id
            WHERE c.recipe_id = :recipe_id
            GROUP BY c.id
            ORDER BY c.created_at DESC
        ");
        $stmt->bindParam(':recipe_id', $recipeId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Store a new comment
     * 
     * @param int $recipeId Recipe ID
     * @param CommentDTO $comment Comment data
     * @return array{success: bool, comment_id: int|null} Success status and comment ID if successful
     * @throws PDOException If database query fails
     */
    public function storeComment(int $recipeId, CommentDTO $comment): array
    {
        try {
            // Validate recipe exists
            if (!$this->getRecipe($recipeId)) {
                return ['success' => false, 'comment_id' => null];
            }

            $stmt = $this->db->prepare("
                INSERT INTO comments 
                (recipe_id, user_id, content)
                VALUES (:recipe_id, :user_id, :content)
            ");

            $success = $stmt->execute([
                ':recipe_id' => $recipeId,
                ':user_id' => $comment->userId,
                ':content' => $comment->content
            ]);

            return [
                'success' => $success,
                'comment_id' => $success ? (int)$this->db->lastInsertId() : null
            ];
        } catch (PDOException $e) {
            error_log("Error storing comment: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Handle comment likes
     * 
     * @param int $commentId Comment ID
     * @param int $userId User ID
     * @return bool Success status
     * @throws PDOException If database query fails
     */
    public function handleLike(int $commentId, int $userId): bool
    {
        try {
            error_log("Attempting to like comment $commentId by user $userId");
            
            $this->db->beginTransaction();

            // Check if like already exists
            $checkStmt = $this->db->prepare("
                SELECT 1 
                FROM comment_likes 
                WHERE comment_id = :comment_id 
                AND user_id = :user_id
            ");
            $checkStmt->execute([
                ':comment_id' => $commentId,
                ':user_id' => $userId
            ]);

            if ($checkStmt->fetch()) {
                error_log("Like already exists for comment $commentId by user $userId");
                $this->db->rollBack();
                return false; // Already liked
            }

            error_log("No existing like found, proceeding with insert");

            // Insert new like
            $insertStmt = $this->db->prepare("
                INSERT INTO comment_likes 
                (comment_id, user_id)
                VALUES (:comment_id, :user_id)
            ");

            $success = $insertStmt->execute([
                ':comment_id' => $commentId,
                ':user_id' => $userId
            ]);

            if ($success) {
                error_log("Successfully inserted like for comment $commentId by user $userId");
                $this->db->commit();
            } else {
                error_log("Failed to insert like for comment $commentId by user $userId");
                $this->db->rollBack();
            }

            return $success;
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Error handling like: " . $e->getMessage());
            error_log("Error code: " . $e->getCode());
            error_log("Error info: " . print_r($e->errorInfo, true));
            throw $e;
        }
    }

    /**
     * Delete a comment
     * 
     * @param int $commentId Comment ID
     * @param int $userId User ID of the person trying to delete
     * @return bool Success status
     * @throws PDOException If database query fails
     */
    public function deleteComment(int $commentId, int $userId): bool
    {
        try {
            error_log("Attempting to delete comment $commentId by user $userId");

            // Check if the user owns this comment
            $checkStmt = $this->db->prepare("
                SELECT 1 
                FROM comments 
                WHERE id = :comment_id 
                AND user_id = :user_id
            ");
            $checkStmt->execute([
                ':comment_id' => $commentId,
                ':user_id' => $userId
            ]);

            if (!$checkStmt->fetch()) {
                error_log("Delete failed: Comment $commentId does not belong to user $userId in database");
                return false;
            }

            // Delete the comment
            $deleteStmt = $this->db->prepare("
                DELETE FROM comments 
                WHERE id = :comment_id 
                AND user_id = :user_id
            ");

            $success = $deleteStmt->execute([
                ':comment_id' => $commentId,
                ':user_id' => $userId
            ]);

            if ($success) {
                error_log("Successfully deleted comment $commentId");
            } else {
                error_log("Failed to delete comment $commentId");
            }

            return $success;
        } catch (PDOException $e) {
            error_log("Error deleting comment: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get all recipes
     * 
     * @return array Array of recipes
     * @throws PDOException If database query fails
     */
    public function index(): array
    {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    r.*,
                    COUNT(DISTINCT c.id) as comment_count
                FROM recipes r
                LEFT JOIN comments c ON r.id = c.recipe_id
                GROUP BY r.id
                ORDER BY r.created_at DESC
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching recipes: " . $e->getMessage());
            throw $e;
        }
    }
}
