<?php

header('Content-Type: application/json');
ini_set('display_errors', 0);
error_reporting(0);

require_once('../private/initialize.php');

$lat = isset($_GET['lat']) ? floatval($_GET['lat']) : null;
$lon = isset($_GET['lon']) ? floatval($_GET['lon']) : null;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 40;
$offset = ($page - 1) * $limit;

if (!$lat || !$lon) {
    echo json_encode(["error" => "Missing latitude or longitude"]);
    exit;
}

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
    LIMIT ? OFFSET ? ");
$stmt->bind_param("dddii", $lat, $lon, $lat, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

$restaurants = [];
while ($row = $result->fetch_assoc()) {
    if (is_string($row['tags'])) {
        $decoded = json_decode($row['tags'], true);
        $row['tags'] = $decoded ?: $row['tags'];
    }
    $restaurants[] = $row;
}

$total = $db->connection->query("SELECT COUNT(*) AS cnt FROM restaurants WHERE status='approved'")->fetch_assoc()['cnt'];
$total_pages = ceil($total / $limit);

echo json_encode([
    "restaurants" => $restaurants,
    "total_pages" => $total_pages,
    "current_page" => $page
]);
