<?php
class RecipeController
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    // Fetch recipe details with comments and likes
    public function show($id)
    {
        // Get recipe details
        $recipe = $this->getRecipe($id);

        if (!$recipe) return null;

        // Get comments with like counts
        $comments = $this->getCommentsWithLikes($id);

        return [
            'recipe' => $recipe,
            'comments' => $comments
        ];
    }

    private function getRecipe($id)
    {
        $stmt = $this->db->prepare("
            SELECT * 
            FROM recipes 
            WHERE id = :id
        ");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function getCommentsWithLikes($recipeId)
    {
        $stmt = $this->db->prepare("
            SELECT c.*, COUNT(cl.comment_id) AS like_count
            FROM comments c
            LEFT JOIN comment_likes cl ON c.id = cl.comment_id
            WHERE c.recipe_id = :recipe_id
            GROUP BY c.id
            ORDER BY c.created_at DESC
        ");
        $stmt->bindParam(':recipe_id', $recipeId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Handle new comment submission
    public function storeComment($recipeId, $data)
    {
        // Basic validation
        if (empty($data['user_name']) || empty($data['content'])) {
            return false;
        }

        $stmt = $this->db->prepare("
            INSERT INTO comments 
            (recipe_id, user_name, content)
            VALUES (:recipe_id, :user_name, :content)
        ");

        return $stmt->execute([
            ':recipe_id' => $recipeId,
            ':user_name' => substr(trim($data['user_name']), 0, 255),
            ':content' => trim($data['content'])
        ]);
    }

    // Handle comment likes
    public function handleLike($commentId, $userName)
    {
        // Check if like already exists
        $checkStmt = $this->db->prepare("
            SELECT 1 
            FROM comment_likes 
            WHERE comment_id = :comment_id 
            AND user_name = :user_name
        ");
        $checkStmt->execute([
            ':comment_id' => $commentId,
            ':user_name' => $userName
        ]);

        if ($checkStmt->fetch()) {
            return false; // Already liked
        }

        // Insert new like
        $insertStmt = $this->db->prepare("
            INSERT INTO comment_likes 
            (comment_id, user_name)
            VALUES (:comment_id, :user_name)
        ");

        return $insertStmt->execute([
            ':comment_id' => $commentId,
            ':user_name' => $userName
        ]);
    }
}
