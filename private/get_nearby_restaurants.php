<?php
require_once('../private/initialize.php');

header('Content-Type: application/json');

$lat = isset($_GET['lat']) ? floatval($_GET['lat']) : null;
$lon = isset($_GET['lon']) ? floatval($_GET['lon']) : null;

if ($lat && $lon) {
    $stmt = $db->connection->prepare("
        SELECT *,
        (6371 * acos(
            cos(radians(?)) * cos(radians(latitude)) *
            cos(radians(longitude) - radians(?)) +
            sin(radians(?)) * sin(radians(latitude))
        )) AS distance
        FROM restaurants
        WHERE status = 'approved'
        ORDER BY distance ASC
    ");
    $stmt->bind_param("ddd", $lat, $lon, $lat);
} else {
    $stmt = $db->connection->prepare("
        SELECT * FROM restaurants 
        WHERE status = 'approved'
        ORDER BY name ASC
    ");
}

$stmt->execute();
$result = $stmt->get_result();
$restaurants = [];

while ($row = $result->fetch_assoc()) {
    // Decode tags if JSON
    if (is_string($row['tags'])) {
        $decoded = json_decode($row['tags'], true);
        $row['tags'] = $decoded ?: $row['tags'];
    }
    $restaurants[] = $row;
}

echo json_encode($restaurants);
