<main class="landing-container">
  <section class="intro-section">
    <h1>Welcome to Curfew Comforts!</h1>
    <p class="tagline">Your go-to spot for delicious and comforting recipes</p>
    <p class="subtag">Explore our collection of home-cooked meals that will warm your heart!</p>
  </section>

  <div class="recipe-grid">
    <?php foreach ($viewData['featuredRecipes'] as $recipe): ?>
      <div class="recipe-card">
        <?php if (!empty($recipe['image_url'])): ?>
          <img src="<?= htmlspecialchars('/img/' . basename($recipe['image_url'])) ?>"
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
  </div>

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