<?php
http_response_code(404); // Set HTTP status to 404
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 Page Not Found | MeFoodie</title>
    <link rel="stylesheet" href="public/stylesheets/style.css">
    <!-- <script src="https://cdn.tailwindcss.com"></script> -->
    <style>
        body {
            font-family: var(--font-family-open-sans);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: var(--background-color);
            margin: 0;
        }

        .container {
            text-align: center;
        }

        h1 {
            font-size: 6rem;
            color: var(--primary-color);
        }

        h2 {
            color: var(--text-black);
            margin-bottom: 20px;
        }

        a {
            margin-top: 2rem !important;
            color: white;
            background-color: var(--primary-color);
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: 0.3s;
        }

        a:hover {
            background-color: var(--secondary-color);
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>404</h1>
        <h2>Oops! Page Not Found</h2>
        <p class="" style="margin-bottom: 1rem;">The page you are looking for does not exist.</p>
        <a href="/" class="" style="margin-top: 1rem;">Go to Homepage</a>
    </div>
</body>

</html>