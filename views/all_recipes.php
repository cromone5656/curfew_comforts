<main>
    <section id="search-filter">
        <h1>All Recipes</h1>
        <form id="filter-form" action="index.php?page=all_recipes" method="get">
            <input type="hidden" name="page" value="all_recipes">
            <div>
                <label for="search">Search by Recipe Name</label>
                <input type="text" name="search" id="search" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            </div>
            <div>
                <label for="category">Filter by Category</label>
                <select name="category" id="category">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo htmlspecialchars($category['category']); ?>"
                            <?php echo (isset($_GET['category']) && $_GET['category'] == $category['category']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category['category']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <button type="submit">Apply Filters</button>
            </div>
        </form>
    </section>

    <section id="recipe-list">
        <div class="recipe-cards" id="recipe-cards">
            <?php if (!empty($recipes)): ?>
                <?php foreach ($recipes as $recipe): ?>
                    <div class="recipe-card">
                        <h3><?php echo htmlspecialchars($recipe['name']); ?></h3>
                        <p><?php echo htmlspecialchars($recipe['description']); ?></p>
                        <a href="index.php?page=recipe&id=<?php echo $recipe['id']; ?>">View Recipe</a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No recipes found. Try adjusting your filters or search terms!</p>
            <?php endif; ?>
        </div>
    </section>
</main>