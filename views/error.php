<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error - Curfew Comforts</title>
    <link rel="stylesheet" href="/css/styles.css">
</head>
<body>
    <div class="error-container">
        <h1>Oops! Something went wrong</h1>
        <p>We're sorry, but there was an error processing your request.</p>
        <?php if (isset($error)): ?>
            <div class="error-details">
                <p><?= htmlspecialchars($error) ?></p>
            </div>
        <?php endif; ?>
        <a href="index.php" class="btn btn-primary">Return to Home</a>
    </div>
</body>
</html> 