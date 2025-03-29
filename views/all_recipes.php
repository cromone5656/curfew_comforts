<div class="all-recipes-container">
    <h1>All Recipes</h1>

    <form method="GET" class="search-filter">
        <input type="hidden" name="page" value="all_recipes">

        <div class="search-group">
            <input type="search" name="search" placeholder="Search recipes..."
                value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
            <button type="submit">🔍 Search</button>
        </div>

        <div class="filter-group">
            <label for="category">Filter by Category:</label>
            <select name="category">
                <option value="">All Categories</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= htmlspecialchars($category['category']) ?>"
                        <?= (($_GET['category'] ?? '') === $category['category']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($category['category']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </form>

    <?php if (isset($viewData['error'])): ?>
        <div class="error-message"><?= htmlspecialchars($viewData['error']) ?></div>
    <?php elseif (!empty($viewData['recipes'])): ?>
        <div class="recipe-grid">
            <?php foreach ($viewData['recipes'] as $recipe): ?>
                <article class="recipe-card">
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
        <p class="no-results">No recipes found matching your criteria.</p>
    <?php endif; ?>
</div>