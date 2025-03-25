<!-- landing.php -->
<main>
  <section id="intro">
    <h1>Welcome to Curfew Comforts!</h1>
    <p>Your go-to spot for delicious and comforting recipes.</p>
    <p>Explore our collection of home-cooked meals that will warm your heart!</p>
  </section>

  <!-- Featured Recipes Section -->
  <?php if (!empty($featuredRecipes)): ?>
    <div class="featured-recipes">
      <h2>Featured Recipes</h2>
      <?php foreach ($featuredRecipes as $recipe): ?>
        <div class="recipe-card">
          <h3><?php echo htmlspecialchars($recipe['name']); ?></h3>
          <p><?php echo htmlspecialchars($recipe['description']); ?></p>
          <a href="index.php?page=recipe&id=<?php echo $recipe['id']; ?>">View Recipe</a>
        </div>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <p>No featured recipes available.</p>
  <?php endif; ?>

  <!-- Browse All Recipes Button -->
  <div class="browse-all">
    <a href="index.php?page=all_recipes">Browse All Recipes</a>
  </div>


  <!-- About Us Section -->
  <section id="about">
    <h2>About Us</h2>
    <p>Curfew Comforts is a family-driven recipe website, bringing together simple and delicious recipes that you can prepare at home. Whether you're cooking for a crowd or just for yourself, we’ve got something for everyone!</p>
  </section>
</main>