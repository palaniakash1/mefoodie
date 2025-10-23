<?php
require_once('../private/initialize.php');

$search = trim($_GET['q'] ?? '');

if ($search === '') {
    echo "<p class='text-gray-500 text-center mt-5'>Please enter a search term.</p>";
    exit;
}

$stmt = $db->connection->prepare("
    SELECT * FROM restaurants 
    WHERE status = 'approved'
    AND (
        city LIKE ? 
        OR state LIKE ? 
        OR district LIKE ?
        OR JSON_CONTAINS(tags, JSON_QUOTE(?)) -- if stored as JSON
        OR tags LIKE ?                       -- fallback if stored as plain text
    )
");

$like = "%$search%";
$stmt->bind_param("sssss", $like, $like, $like, $search, $like);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<p class='text-gray-500 text-center mt-5'>No matching results found for '<strong>$search</strong>'.</p>";
    exit;
}

while ($r = $result->fetch_assoc()) {
    echo "
    <div class='bg-white rounded-xl shadow-lg p-4 hover:shadow-xl transition mb-4'>
        <h3 class='font-bold text-lg text-tomato mb-2'>{$r['name']}</h3>
        <p><strong>Location:</strong> {$r['district']}, {$r['state']}</p>
        <p><strong>Website:</strong> <a href='{$r['website']}' target='_blank' class='text-blue-500 hover:underline'>{$r['website']}</a></p>
        <p><strong>Tags:</strong> {$r['tags']}</p>
    </div>";
}

$stmt->close();
