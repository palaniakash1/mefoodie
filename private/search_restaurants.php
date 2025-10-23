<?php
require_once('initialize.php');

header('Content-Type: application/json; charset=UTF-8');

$q = trim($_GET['q'] ?? '');
$mode = $_GET['mode'] ?? 'suggest'; // "suggest" or "full"

if (empty($q)) {
    echo json_encode([]);
    exit;
}

$stmt = $db->connection->prepare("
    SELECT * FROM restaurants
    WHERE status = 'approved'
    AND (
        name LIKE ? OR
        tags LIKE ? OR
        city LIKE ? OR
        state LIKE ? OR
        district LIKE ?
    )
    ORDER BY name ASC
");

$like = "%$q%";
$stmt->bind_param('sssss', $like, $like, $like, $like, $like);
$stmt->execute();
$result = $stmt->get_result();

$data = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

if ($mode === 'full') {
    // Return as JSON for grid rendering
    echo json_encode($data);
    exit;
}

// Else return HTML suggestions for dropdown
if (count($data) > 0) {
    foreach ($data as $r) {
        echo "
            <a href='#' 
                data-name='" . htmlspecialchars($r['name'], ENT_QUOTES) . "' 
                data-query='" . htmlspecialchars($q, ENT_QUOTES) . "'
                class='result-item block px-4 py-2 hover:bg-gray-100 rounded-lg transition'>
                <div class='font-semibold text-tomato'>" . htmlspecialchars($r['district']) . "</div>
                <!--<div class='text-sm text-gray-500'>" . htmlspecialchars($r['tags']) . "</div> -->
            </a>
        ";
    }
} else {
    echo "<p class='text-gray-500 text-center py-2'>No results found for <b>" . htmlspecialchars($q) . "</b>.</p>";
}
