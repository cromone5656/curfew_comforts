<?php

declare(strict_types=1);

use PDO;
use PDOException;

class ContactController
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Handle contact form submission
     * 
     * @param array $data Form data
     * @return array{success: bool, error: string|null} Submission result
     */
    public function submitRecipe(array $data): array
    {
        try {
            // Validate required fields
            $requiredFields = ['name', 'email', 'recipe_name', 'category', 'ingredients', 'instructions', 'terms'];
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    return [
                        'success' => false,
                        'error' => 'All required fields must be filled out.'
                    ];
                }
            }

            // Validate email
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                return [
                    'success' => false,
                    'error' => 'Please enter a valid email address.'
                ];
            }

            // Store submission in database
            $stmt = $this->db->prepare("
                INSERT INTO recipe_submissions 
                (name, email, recipe_name, category, ingredients, instructions, notes, image_url, created_at)
                VALUES (:name, :email, :recipe_name, :category, :ingredients, :instructions, :notes, :image_url, NOW())
            ");

            $success = $stmt->execute([
                ':name' => $data['name'],
                ':email' => $data['email'],
                ':recipe_name' => $data['recipe_name'],
                ':category' => $data['category'],
                ':ingredients' => $data['ingredients'],
                ':instructions' => $data['instructions'],
                ':notes' => $data['notes'] ?? null,
                ':image_url' => $data['image_url'] ?? null
            ]);

            if (!$success) {
                return [
                    'success' => false,
                    'error' => 'Failed to submit recipe. Please try again.'
                ];
            }

            return [
                'success' => true,
                'error' => null
            ];
        } catch (PDOException $e) {
            error_log("Error submitting recipe: " . $e->getMessage());
            return [
                'success' => false,
                'error' => 'An error occurred while submitting your recipe. Please try again later.'
            ];
        }
    }
} 