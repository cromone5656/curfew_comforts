<!-- header.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title : 'Curfew Comforts'; ?></title>
    <!-- You can include stylesheets or scripts here -->
</head>

<body>
    <!-- The content inside the body, like navigation, can be added here -->
    <header>
        <nav>
            <ul>
                <li><a href="index.php?page=landing">Home</a></li>
                <li><a href="index.php?page=all_recipes">All Recipes</a></li>
                <li><a href="index.php?page=about">About</a></li>
            </ul>
        </nav>
    </header>