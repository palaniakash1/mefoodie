<?php
if (!isset($page_title)) {
    $page_title = "Admin Dashboard";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MeFoodie | <?php echo $page_title; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../public/stylesheets/style.css">
</head>

<body class="bg-gray-100 font-sans">

    <header class="bg-tomato text-white py-4 shadow-md">
        <div class="container mx-auto flex justify-between items-center px-6">
            <h1 class="text-2xl font-bold tracking-wide">🍽️ MeFoodie Admin</h1>
            <a href="../../index.php" class="bg-white tomato font-semibold px-3 py-1 rounded hover:tomato-bg hover:text-white transition">
                Go to Site
            </a>
        </div>
    </header>

    <main class="container mx-auto px-4 py-8">