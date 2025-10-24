<?php
require_once('../private/initialize.php'); // ensures $db is available

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 🔹 Step 1: Sanitize and collect input data
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $ph = trim($_POST['ph'] ?? '');
    $fssai = trim($_POST['fssai'] ?? '');
    $state = trim($_POST['state'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $district = trim($_POST['district'] ?? '');
    $pincode = trim($_POST['pincode'] ?? '');
    $website = trim($_POST['website'] ?? '');
    $tags = trim($_POST['tags'] ?? '');

    // 🔹 Step 2: Basic validation
    if ($name === '' || $email === '' || $ph === '' || $website === '') {
        die("❌ Required fields missing.");
    }

    // 🔹 Step 3: Normalize tags (comma-separated)
    $tagsArray = array_filter(array_map('trim', explode(',', strtolower($tags))));
    $tagsFormatted = json_encode($tagsArray);

    // 🔹 Step 4: Convert address fields to coordinates
    list($latitude, $longitude) = getCoordinatesFromAddress($city, $district, $state);

    // (Optional fallback if API fails)
    if ($latitude == 0 && $longitude == 0) {
        // fallback: try with less detailed address
        list($latitude, $longitude) = getCoordinatesFromAddress($city, '', $state);
    }

    // 🔹 Step 5: Insert query using prepared statements
    $stmt = $db->connection->prepare("
        INSERT INTO restaurants 
        (name, email, ph, fssai, state, city, district, pincode, website, tags, latitude, longitude, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')
    ");

    if (!$stmt) {
        die("❌ Prepare failed: " . $db->connection->error);
    }

    $stmt->bind_param(
        "ssssssssssdd",
        $name,
        $email,
        $ph,
        $fssai,
        $state,
        $city,
        $district,
        $pincode,
        $website,
        $tagsFormatted,
        $latitude,
        $longitude
    );

    // 🔹 Step 6: Execute and handle result
    if ($stmt->execute()) {
        echo "<script> window.location.href='../index.php';</script>";
    } else {
        echo "<script>alert('❌ Failed to register. Please try again.'); window.location.href='index.php';</script>";
    }

    $stmt->close();
    $db->close();
}
