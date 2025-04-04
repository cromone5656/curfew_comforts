<?php
// Ensure $viewData is available
$viewData = $viewData ?? [];
$featuredRecipes = $viewData['featuredRecipes'] ?? [];
$recentRecipes = $viewData['recentRecipes'] ?? [];

// Debug log
error_log("Number of featured recipes in view: " . count($featuredRecipes));
if (!empty($featuredRecipes)) {
    error_log("Featured recipe names in view: " . implode(", ", array_column($featuredRecipes, 'name')));
    error_log("First recipe data in view: " . print_r($featuredRecipes[0], true));
} else {
    error_log("No featured recipes in viewData");
    error_log("viewData contents: " . print_r($viewData, true));
}
?>

<main class="landing-container">
  <section class="intro-section">
    <h1>Welcome to Curfew Comforts!</h1>
    <p class="tagline">Your go-to spot for delicious and comforting recipes</p>
    <p class="subtag">Explore our collection of home-cooked meals that will warm your heart!</p>
  </section>

  <?php if (isset($viewData['error'])): ?>
    <div class="error-message" role="alert">
      <?= htmlspecialchars($viewData['error']) ?>
    </div>
  <?php endif; ?>

  <section class="featured-section">
    <h2>Featured Recipes</h2>
    <div class="recipe-grid">
      <?php if (!empty($featuredRecipes)): ?>
        <?php foreach ($featuredRecipes as $recipe): ?>
          <div class="recipe-card">
            <?php if (!empty($recipe['image_url'])): ?>
              <img src="<?= htmlspecialchars($recipe['image_url']) ?>"
                alt="<?= htmlspecialchars($recipe['name']) ?>"
                class="recipe-card-image">
            <?php else: ?>
              <div class="recipe-card-image image-placeholder"></div>
            <?php endif; ?>

            <h3><?= htmlspecialchars($recipe['name']) ?></h3>
            <div class="recipe-meta">
              <span class="category">
                <?= htmlspecialchars($recipe['category'] ?? 'Uncategorized') ?>
              </span>
              <time>
                <?= date('M Y', strtotime($recipe['created_at'] ?? 'now')) ?>
              </time>
            </div>
            <p class="recipe-description">
              <?= nl2br(htmlspecialchars(mb_substr($recipe['description'] ?? '', 0, 150) . '...')) ?>
            </p>
            <a href="index.php?page=recipe&id=<?= $recipe['id'] ?>" class="view-recipe">
              View Recipe →
            </a>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="no-recipes-message">
          <p>No featured recipes available at the moment.</p>
        </div>
      <?php endif; ?>
    </div>
  </section>

  <section class="recent-section">
    <h2>Recently Added</h2>
    <div class="recipe-grid">
      <?php if (!empty($recentRecipes)): ?>
        <?php foreach ($recentRecipes as $recipe): ?>
          <div class="recipe-card">
            <?php if (!empty($recipe['image_url'])): ?>
              <img src="<?= htmlspecialchars($recipe['image_url']) ?>"
                alt="<?= htmlspecialchars($recipe['name']) ?>"
                class="recipe-card-image">
            <?php else: ?>
              <div class="recipe-card-image image-placeholder"></div>
            <?php endif; ?>

            <h3><?= htmlspecialchars($recipe['name']) ?></h3>
            <div class="recipe-meta">
              <span class="category">
                <?= htmlspecialchars($recipe['category'] ?? 'Uncategorized') ?>
              </span>
              <time>
                <?= date('M Y', strtotime($recipe['created_at'] ?? 'now')) ?>
              </time>
            </div>
            <p class="recipe-description">
              <?= nl2br(htmlspecialchars(mb_substr($recipe['description'] ?? '', 0, 150) . '...')) ?>
            </p>
            <a href="index.php?page=recipe&id=<?= $recipe['id'] ?>" class="view-recipe">
              View Recipe →
            </a>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="no-recipes-message">
          <p>No recent recipes available at the moment.</p>
        </div>
      <?php endif; ?>
    </div>
  </section>

  <div class="browse-section">
    <a href="index.php?page=all_recipes" class="browse-button">
      <i class="fas fa-utensils"></i>
      Browse All Recipes
    </a>
  </div>

  <section class="about-section">
    <div class="about-content">
      <h2>About Us</h2>
      <p class="about-text">
        Curfew Comforts is a family-driven recipe website, bringing together simple and
        delicious recipes you can prepare at home. Whether cooking for a crowd or just
        yourself, we've got something for everyone!
      </p>
    </div>
  </section>
</main>