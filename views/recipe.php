<!-- views/recipe.php -->
<h1><?php echo htmlspecialchars($recipe['name']); ?></h1>
<p><strong>Description:</strong> <?php echo htmlspecialchars($recipe['description']); ?></p>
<p><strong>Ingredients:</strong> <?php echo htmlspecialchars($recipe['ingredients']); ?></p>
<p><strong>Instructions:</strong> <?php echo htmlspecialchars($recipe['instructions']); ?></p>
