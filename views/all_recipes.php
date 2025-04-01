<div class="all-recipes-container">
    <h1>All Recipes</h1>

    <form method="GET" class="search-filter">
        <input type="hidden" name="page" value="all_recipes">

        <div class="search-group">
            <input type="search" 
                   name="search" 
                   placeholder="Search recipes..."
                   value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                   aria-label="Search recipes">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i> Search
            </button>
        </div>

        <div class="filter-group">
            <label for="category">Filter by Category:</label>
            <select name="category" id="category">
                <option value="">All Categories</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= htmlspecialchars($category['category']) ?>"
                        <?= isset($_GET['category']) && $_GET['category'] === $category['category'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($category['category']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </form>
    <?php if (isset($viewData['error'])): ?>
    <?php elseif (!empty($viewData['recipes'])): ?>
        <div class="recipe-grid">
            <?php foreach ($viewData['recipes'] as $recipe): ?>
                <article class="recipe-card">
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