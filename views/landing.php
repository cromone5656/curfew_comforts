<!-- landing.php -->
<?php if (isset($recipes) && count($recipes) > 0): ?>

  <main>
    <section id="intro">
      <h1>Welcome to Curfew Comforts!</h1>
      <p>Your go-to spot for delicious and comforting recipes.</p>
      <p>Explore our collection of home-cooked meals that will warm your heart!</p>
    </section>

    <section id="featured-recipes">
      <ul>
        <?php foreach ($recipes as $recipe): ?>
          <li>
            <a href="index.php?id=<?php echo $recipe['id']; ?>">
              <?php echo htmlspecialchars($recipe['name']); ?>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </section>
    <section>
    <?php else: ?>
      <p>No recipes found. Time to get cooking, babe! 🍳</p>
    <?php endif; ?>
    </section>

    <section id="about">
      <h2>About Us</h2>
      <p>Curfew Comforts is a family-driven recipe website, bringing together simple and delicious recipes that you can prepare at home. Whether you're cooking for a crowd or just for yourself, we’ve got something for everyone!</p>
    </section>
  </main>