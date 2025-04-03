<div class="recipes-container">
    <h1>Our Recipes</h1>
    
    <div class="recipes-grid">
        <?php foreach ($viewData['recipes'] as $recipe): ?>
            <div class="recipe-card">
                <a href="?page=recipe&id=<?= urlencode($recipe['id']) ?>" class="recipe-link">
                    <img src="<?= htmlspecialchars($recipe['image_url']) ?>" alt="<?= htmlspecialchars($recipe['title']) ?>" class="recipe-image">
                    <div class="recipe-info">
                        <h2><?= htmlspecialchars($recipe['title']) ?></h2>
                        <p class="recipe-description"><?= htmlspecialchars(substr($recipe['instructions'], 0, 100)) ?>...</p>
                        <div class="recipe-meta">
                            <span class="recipe-date">
                                <?= htmlspecialchars((new DateTime($recipe['created_at']))->format('M j, Y')) ?>
                            </span>
                            <?php if (isset($recipe['comment_count']) && $recipe['comment_count'] > 0): ?>
                                <span class="comment-count">
                                    💬 <?= htmlspecialchars($recipe['comment_count']) ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
.recipes-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.recipes-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 30px;
    margin-top: 30px;
}

.recipe-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.recipe-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.recipe-link {
    text-decoration: none;
    color: inherit;
    display: block;
}

.recipe-image {
    width: 100%;
    height: 200px;
    object-fit: cover;
}

.recipe-info {
    padding: 20px;
}

.recipe-info h2 {
    margin: 0 0 10px 0;
    font-size: 1.4em;
    color: #333;
}

.recipe-description {
    color: #666;
    margin: 0 0 15px 0;
    line-height: 1.5;
    font-size: 0.95em;
}

.recipe-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: #888;
    font-size: 0.9em;
}

.recipe-date {
    color: #888;
}

.comment-count {
    display: flex;
    align-items: center;
    gap: 4px;
}
</style> 