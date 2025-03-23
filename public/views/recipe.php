<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recipe</title>
</head>
<body>
    <h1><?= $recipe['name']; ?></h1>
    <p><strong>Ingredients:</strong></p>
    <ul>
        <?php foreach (explode(',', $recipe['ingredients']) as $ingredient): ?>
            <li><?= htmlspecialchars($ingredient); ?></li>
        <?php endforeach; ?>
    </ul>
    <p><strong>Instructions:</strong></p>
    <p><?= nl2br(htmlspecialchars($recipe['instructions'])); ?></p>
</body>
</html>
