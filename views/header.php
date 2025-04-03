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

    <style>
        .delete-button {
            background: none;
            border: none;
            cursor: pointer;
            padding: 4px 8px;
            font-size: 1.2em;
            opacity: 0.7;
            transition: opacity 0.2s;
        }

        .delete-button:hover {
            opacity: 1;
        }

        .delete-form {
            display: inline-block;
            margin-left: 10px;
        }

        .auth-container {
            max-width: 400px;
            margin: 2rem auto;
            padding: 2rem;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .auth-form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .auth-form .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .auth-form label {
            font-weight: 600;
            color: #333;
        }

        .auth-form input {
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }

        .auth-form button {
            padding: 0.75rem;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .auth-form button:hover {
            background: #0056b3;
        }

        .auth-links {
            margin-top: 1rem;
            text-align: center;
            color: #666;
        }

        .auth-links a {
            color: #007bff;
            text-decoration: none;
        }

        .auth-links a:hover {
            text-decoration: underline;
        }

        .success-message {
            padding: 1rem;
            margin: 1rem 0;
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            border-radius: 4px;
        }

        .user-menu {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .welcome-text {
            color: #666;
            font-size: 0.95rem;
            font-weight: 500;
        }

        .logout-link {
            color: #e74c3c;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.95rem;
            background: rgba(231, 76, 60, 0.1);
            border: 1px solid rgba(231, 76, 60, 0.2);
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .logout-link:hover {
            background: rgba(231, 76, 60, 0.2);
            transform: translateY(-1px);
        }

        nav {
            padding: 1rem 2rem;
            background: #fff;
            border-bottom: 1px solid #eee;
        }

        nav ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            gap: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        nav ul li {
            margin: 0;
        }

        nav ul li a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
            padding: 0.5rem 0;
            transition: color 0.2s ease;
        }

        nav ul li a:hover {
            color: #e74c3c;
        }

        @media (max-width: 768px) {
            nav ul {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }

            .user-menu {
                margin-left: 0;
                margin-top: 1rem;
                width: 100%;
                justify-content: space-between;
            }
        }
    </style>
</head>

<body>
    <a href="#main-content" class="skip-link">Skip to main content</a>
    
    <header role="banner">
        <nav role="navigation" aria-label="Main navigation">
            <button class="mobile-menu-toggle" aria-expanded="false" aria-controls="main-menu" aria-label="Toggle menu">
                <span class="sr-only">Menu</span>
                <span class="hamburger"></span>
            </button>
            
            <ul id="main-menu">
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
        </nav>
    </header>

    <main id="main-content" role="main">