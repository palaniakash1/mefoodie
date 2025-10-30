<?php
session_start();

// Optional: redirect to login if admin is not logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../private/admin-login.php");
    exit;
}

// Default page title if not set
if (!isset($page_title)) {
    $page_title = "Admin Dashboard";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MeFoodie | <?php echo htmlspecialchars($page_title); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../public/stylesheets/style.css">
    
</head>

<body>

    <header class="bg-white text-white py-4 shadow-md">
        <div class="container mx-auto flex justify-between items-center px-6">
            <h1 class="text-2xl font-bold tracking-wide">🍽️ MeFoodie Admin</h1>

            <div class="flex gap-2 items-center">
                <a href="../../index.php" class="bg-white text-tomato font-semibold px-3 py-1 rounded hover:bg-tomato hover:text-white transition">
                    Go to Site
                </a>
                <a href="admin-logout.php" class="bg-red-600 text-white font-semibold px-3 py-1 rounded hover:bg-red-700 transition">
                    Sign Out
                </a>
            </div>
        </div>
    </header>

    <div class="admin-banner"></div>

    <main class="container mx-auto px-4 py-8">