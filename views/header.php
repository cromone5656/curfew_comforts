<!-- header.php -->
<!DOCTYPE html>
<html lang="en" class="no-js">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Curfew Comforts - Your source for delicious comfort food recipes">
    <meta name="theme-color" content="#ffffff" media="(prefers-color-scheme: light)">
    <meta name="theme-color" content="#1a1a1a" media="(prefers-color-scheme: dark)">
    
    <!-- Open Graph / Social Media -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?= htmlspecialchars($title ?? 'Curfew Comforts') ?>">
    <meta property="og:description" content="Your source for delicious comfort food recipes">
    <meta property="og:image" content="/img/og-image.jpg">
    <meta property="og:url" content="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
    
    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= htmlspecialchars($title ?? 'Curfew Comforts') ?>">
    <meta name="twitter:description" content="Your source for delicious comfort food recipes">
    <meta name="twitter:image" content="/img/og-image.jpg">
    
    <title><?= htmlspecialchars($title ?? 'Curfew Comforts') ?></title>
    
    <!-- Preload critical assets -->
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Lora:wght@400;700&family=Open+Sans:wght@400;600;700&display=swap" as="style">
    <link rel="preload" href="/css/styles.css" as="style">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="/img/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/img/favicon-16x16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/img/apple-touch-icon.png">
    <link rel="manifest" href="/site.webmanifest">
    
    <!-- Styles -->
    <link href="https://fonts.googleapis.com/css2?family=Lora:wght@400;700&family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/css/styles.css">
    
    <!-- Remove no-js class -->
    <script>document.documentElement.classList.remove('no-js');</script>
    
    <noscript>
        <style>
            .hamburger { display: none !important; }
            nav ul { display: flex !important; }
        </style>
    </noscript>
</head>

<body>
    <a href="#main-content" class="skip-link">Skip to main content</a>
    
    <header role="banner">
        <nav role="navigation" aria-label="Main navigation">
            <div class="container">
                <div class="hamburger">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
                
                <ul class="nav-menu">
                    <li><a href="index.php?page=landing" <?= ($_GET['page'] ?? 'landing') === 'landing' ? 'aria-current="page"' : '' ?>>Home</a></li>
                    <li><a href="index.php?page=all_recipes" <?= ($_GET['page'] ?? '') === 'all_recipes' ? 'aria-current="page"' : '' ?>>All Recipes</a></li>
                    <li><a href="index.php?page=about" <?= ($_GET['page'] ?? '') === 'about' ? 'aria-current="page"' : '' ?>>About</a></li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="user-menu">
                            <span class="welcome-text">Welcome, <?= htmlspecialchars($_SESSION['username']) ?></span>
                            <a href="index.php?page=logout" class="logout-link">Logout</a>
                        </li>
                    <?php else: ?>
                        <li class="user-menu">
                            <a href="index.php?page=login">Login</a>
                            <a href="index.php?page=register">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </nav>
    </header>

    <main id="main-content" role="main">