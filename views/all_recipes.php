<div class="all-recipes-container">
    <h1>All Recipes</h1>

    <!-- ... (search form remains the same) ... -->

    <?php if (isset($viewData['error'])): ?>
        <!-- ... (error message remains the same) ... -->
    <?php elseif (!empty($viewData['recipes'])): ?>
        <div class="recipe-grid">
            <?php foreach ($viewData['recipes'] as $recipe): ?>
                <article class="recipe-card">
                    <!-- Add image container here -->
                    <?php if (!empty($recipe['image_url'])): ?>
                        <img src="<?= htmlspecialchars($recipe['image_url']) ?>"
                            alt="<?= htmlspecialchars($recipe['name']) ?>"
                            class="recipe-card-image">
                    <?php else: ?>
                        <div class="recipe-card-image image-placeholder"></div>
                    <?php endif; ?>

                    <h2><?= htmlspecialchars($recipe['name']) ?></h2>
                    <div class="recipe-meta">
                        <span class="category"><?= htmlspecialchars($recipe['category']) ?></span>
                        <time><?= date('M Y', strtotime($recipe['created_at'])) ?></time>
                    </div>
                    <p class="description"><?= nl2br(htmlspecialchars(mb_substr($recipe['description'], 0, 150) . '...')) ?></p>
                    <a href="index.php?page=recipe&id=<?= $recipe['id'] ?>" class="view-recipe">
                        View Recipe →
                    </a>
                </article>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <!-- ... (no results remains the same) ... -->
    <?php endif; ?>
</div>