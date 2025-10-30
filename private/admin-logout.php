<?php
session_start();

// Destroy all session data
$_SESSION = []; // clear session array
session_destroy();

// Redirect to homepage
header("Location: ../../index.php");
exit;
