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
                <?php foreach ($viewData['categories'] as $category): ?>
                    <option value="<?= htmlspecialchars($category['category']) ?>"
                        <?= isset($_GET['category']) && $_GET['category'] === $category['category'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($category['category']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </form>

    <?php if (isset($viewData['error'])): ?>
        <div class="alert alert-danger" role="alert">
            <?= htmlspecialchars($viewData['error']) ?>
        </div>
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
        <div class="no-results">
            <div class="no-results-content">
                <i class="fas fa-search fa-3x"></i>
                <h2>No Recipes Found</h2>
                <?php if (isset($viewData['search']) || isset($viewData['category'])): ?>
                    <p>We couldn't find any recipes 
                        <?php if ($viewData['search']): ?>
                            matching "<?= htmlspecialchars($viewData['search']) ?>"
                        <?php endif; ?>
                        <?php if ($viewData['category']): ?>
                            in the category "<?= htmlspecialchars($viewData['category']) ?>"
                        <?php endif; ?>
                    </p>
                    <div class="suggestions">
                        <p>Try these suggestions:</p>
                        <ul>
                            <li>Check your spelling</li>
                            <li>Try different keywords</li>
                            <li>Clear the filters</li>
                        </ul>
                    </div>
                    <a href="index.php?page=all_recipes" class="btn btn-primary">
                        <i class="fas fa-times"></i> Clear Filters
                    </a>
                <?php else: ?>
                    <p>There are currently no recipes available.</p>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>