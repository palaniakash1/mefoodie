<?php
session_start();

// Hardcoded admin credentials
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD', 'yourpasswordgoeshere');

// Redirect if already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: admin.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($username === ADMIN_USERNAME && $password === ADMIN_PASSWORD) {
        $_SESSION['admin_logged_in'] = true;
        header("Location: admin.php");
        exit;
    } else {
        $error = "Invalid username or password";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>MeFoodie Admin Login</title>
    <link rel="stylesheet" href="../public/stylesheets/style.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-background-color flex items-center justify-center min-h-screen">

    <div class="login-container bg-white p-8 rounded-xl shadow-md w-96">
        <h2 class="text-2xl font-bold text-tomato mb-6 text-center">🍽️ Admin Login</h2>

        <?php if ($error): ?>
            <div class="text-red-600 text-center mb-4"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" class="flex flex-col gap-4">
            <input type="text" name="username" placeholder="Username" class="border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-tomato" required>
            <input type="password" name="password" placeholder="Password" class="border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-tomato" required>
            <button type="submit" class="tomato-bg text-white font-semibold py-2 rounded hover:bg-secondary-color transition">
                Login
            </button>
        </form>
    </div>

</body>

</html>