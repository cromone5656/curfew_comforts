<?php
$viewData = $viewData ?? [];
$success = $viewData['success'] ?? false;
$error = $viewData['error'] ?? null;
?>

<main class="contact-container">
    <section class="contact-section">
        <h1>Submit a Recipe</h1>
        <p class="subtitle">Have a delicious recipe you'd like to share? Fill out the form below and we'll review it for publication!</p>

        <?php if ($success): ?>
            <div class="success-message">
                <p>Thank you for your submission! We'll review your recipe and get back to you soon.</p>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="error-message">
                <p><?= htmlspecialchars($error) ?></p>
            </div>
        <?php endif; ?>

        <form method="POST" action="index.php?page=contact" class="contact-form">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
            
            <div class="form-group">
                <label for="name">Your Name</label>
                <input type="text" id="name" name="name" required 
                       value="<?= htmlspecialchars($viewData['formData']['name'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required 
                       value="<?= htmlspecialchars($viewData['formData']['email'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="recipe_name">Recipe Name</label>
                <input type="text" id="recipe_name" name="recipe_name" required 
                       value="<?= htmlspecialchars($viewData['formData']['recipe_name'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="category">Category</label>
                <select id="category" name="category" required>
                    <option value="">Select a category</option>
                    <option value="Breakfast" <?= ($viewData['formData']['category'] ?? '') === 'Breakfast' ? 'selected' : '' ?>>Breakfast</option>
                    <option value="Lunch" <?= ($viewData['formData']['category'] ?? '') === 'Lunch' ? 'selected' : '' ?>>Lunch</option>
                    <option value="Dinner" <?= ($viewData['formData']['category'] ?? '') === 'Dinner' ? 'selected' : '' ?>>Dinner</option>
                    <option value="Dessert" <?= ($viewData['formData']['category'] ?? '') === 'Dessert' ? 'selected' : '' ?>>Dessert</option>
                    <option value="Snack" <?= ($viewData['formData']['category'] ?? '') === 'Snack' ? 'selected' : '' ?>>Snack</option>
                </select>
            </div>

            <div class="form-group">
                <label for="ingredients">Ingredients</label>
                <textarea id="ingredients" name="ingredients" required 
                          placeholder="List ingredients, one per line"><?= htmlspecialchars($viewData['formData']['ingredients'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label for="instructions">Instructions</label>
                <textarea id="instructions" name="instructions" required 
                          placeholder="Step by step instructions"><?= htmlspecialchars($viewData['formData']['instructions'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label for="notes">Additional Notes</label>
                <textarea id="notes" name="notes" 
                          placeholder="Any special tips or variations?"><?= htmlspecialchars($viewData['formData']['notes'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label for="image_url">Recipe Image URL (optional)</label>
                <input type="url" id="image_url" name="image_url" 
                       value="<?= htmlspecialchars($viewData['formData']['image_url'] ?? '') ?>"
                       placeholder="https://example.com/image.jpg">
            </div>

            <div class="form-group checkbox-group">
                <input type="checkbox" id="terms" name="terms" required>
                <label for="terms">I agree to the <a href="index.php?page=terms">Terms of Service</a> and confirm this is my original recipe.</label>
            </div>

            <button type="submit" class="submit-button">Submit Recipe</button>
        </form>
    </section>
</main> 