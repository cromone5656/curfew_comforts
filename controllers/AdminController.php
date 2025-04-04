<?php

declare(strict_types=1);

use PDO;
use PDOException;

class AdminController
{
    private PDO $dbh;

    public function __construct(PDO $dbh)
    {
        $this->dbh = $dbh;
    }

    /**
     * Get all recipe submissions
     * 
     * @return array Array of recipe submissions
     */
    public function getSubmissions(): array
    {
        try {
            $stmt = $this->dbh->prepare("
                SELECT * FROM recipe_submissions 
                ORDER BY created_at DESC
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching recipe submissions: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Import a recipe submission into the main recipes table
     * 
     * @param int $submissionId The submission ID to import
     * @return bool True if successful, false otherwise
     */
    private function importRecipe(int $submissionId): bool
    {
        try {
            // Get the submission
            $submission = $this->getSubmission($submissionId);
            if (!$submission) {
                error_log("Failed to import recipe: Submission not found");
                return false;
            }

            // Insert into recipes table
            $stmt = $this->dbh->prepare("
                INSERT INTO recipes 
                (name, description, ingredients, instructions, category, image_url, created_at, updated_at, source)
                VALUES 
                (:name, :description, :ingredients, :instructions, :category, :image_url, NOW(), NOW(), 'user_submitted')
            ");

            return $stmt->execute([
                'name' => $submission['recipe_name'],
                'description' => $submission['notes'] ?? '',
                'ingredients' => $submission['ingredients'],
                'instructions' => $submission['instructions'],
                'category' => $submission['category'],
                'image_url' => $submission['image_url']
            ]);
        } catch (PDOException $e) {
            error_log("Error importing recipe: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update the status of a recipe submission
     * 
     * @param int $id The submission ID
     * @param string $status The new status (pending, approved, rejected)
     * @return bool True if successful, false otherwise
     */
    public function updateSubmissionStatus(int $id, string $status): bool
    {
        try {
            $this->dbh->beginTransaction();

            // Update the status
            $stmt = $this->dbh->prepare("
                UPDATE recipe_submissions 
                SET status = :status 
                WHERE id = :id
            ");
            
            $success = $stmt->execute([
                'id' => $id,
                'status' => $status
            ]);

            // If approved, import into recipes table
            if ($success && $status === 'approved') {
                $success = $this->importRecipe($id);
            }

            if ($success) {
                $this->dbh->commit();
            } else {
                $this->dbh->rollBack();
            }

            return $success;
        } catch (PDOException $e) {
            $this->dbh->rollBack();
            error_log("Error updating submission status: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get a single recipe submission by ID
     * 
     * @param int $id The submission ID
     * @return array|null The submission data or null if not found
     */
    public function getSubmission(int $id): ?array
    {
        try {
            $stmt = $this->dbh->prepare("
                SELECT * FROM recipe_submissions 
                WHERE id = :id
            ");
            $stmt->execute(['id' => $id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: null;
        } catch (PDOException $e) {
            error_log("Error fetching submission: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get all comments with recipe and user information
     * 
     * @return array Array of comments with related data
     */
    public function getComments(): array
    {
        try {
            $stmt = $this->dbh->prepare("
                SELECT c.*, r.name as recipe_name, u.username
                FROM comments c
                LEFT JOIN recipes r ON c.recipe_id = r.id
                LEFT JOIN users u ON c.user_id = u.id
                ORDER BY c.created_at DESC
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching comments: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Delete a comment
     * 
     * @param int $commentId The comment ID to delete
     * @return bool True if successful, false otherwise
     */
    public function deleteComment(int $commentId): bool
    {
        try {
            $stmt = $this->dbh->prepare("
                DELETE FROM comments 
                WHERE id = :id
            ");
            return $stmt->execute(['id' => $commentId]);
        } catch (PDOException $e) {
            error_log("Error deleting comment: " . $e->getMessage());
            return false;
        }
    }
} 