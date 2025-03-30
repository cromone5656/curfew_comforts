<!-- header.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title : 'Curfew Comforts'; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Lora:wght@400;700&family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/css/styles.css">
    <!-- You can include stylesheets or scripts here -->
</head>

<body>
    <header>
        <nav>
            <ul>
                <li><a href="index.php?page=landing">Home</a></li>
                <li><a href="index.php?page=all_recipes">All Recipes</a></li>
                <li><a href="index.php?page=about">About</a></li>
            </ul>
        </nav>
    </header>