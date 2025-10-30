<?php
require_once('../private/initialize.php');
header('Content-Type: application/json; charset=UTF-8');

$q = trim($_GET['q'] ?? '');
$mode = $_GET['mode'] ?? 'suggest'; // "suggest" for dropdown, "full" for card view
$exact = isset($_GET['exact']) ? (int)$_GET['exact'] : 0;
$lat = isset($_GET['lat']) ? floatval($_GET['lat']) : null;
$lon = isset($_GET['lon']) ? floatval($_GET['lon']) : null;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 40;
$offset = ($page - 1) * $limit;

if (empty($q)) {
    echo json_encode([]);
    exit;
}

if ($exact) {
    // 🎯 Exact match
    $stmt = $db->connection->prepare("
        SELECT *,
(6371 * acos(
cos(radians(?)) * cos(radians(latitude)) *
cos(radians(longitude) - radians(?)) +
sin(radians(?)) * sin(radians(latitude))
)) AS distance FROM restaurants
        WHERE status = 'approved'
        AND (
            LOWER(city) = LOWER(?) OR
            LOWER(district) = LOWER(?) OR
            LOWER(state) = LOWER(?) OR
            LOWER(name) = LOWER(?) OR
            LOWER(tags) LIKE LOWER(?)
        )
                ORDER BY distance ASC
                LIMIT ? OFFSET ?
    ");
    $likeTag = "%$q%";
    $stmt->bind_param('dddssssii', $lat, $lon, $lat, $q, $q, $q, $q, $limit, $offset);
} else {
    // 🔍 Partial match
    $like = "%$q%";
    $stmt = $db->connection->prepare("
        SELECT *,
(6371 * acos(
cos(radians(?)) * cos(radians(latitude)) *
cos(radians(longitude) - radians(?)) +
sin(radians(?)) * sin(radians(latitude))
)) AS distance FROM restaurants
        WHERE status = 'approved'
        AND (
            name LIKE ? OR
            tags LIKE ? OR
            city LIKE ? OR
            state LIKE ? OR
            district LIKE ?
        )
        ORDER BY distance ASC
        LIMIT ? OFFSET ? ");

    $stmt->bind_param('dddsssssii', $lat, $lon, $lat, $like, $like, $like, $like, $like, $limit, $offset);
}

$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();





// =====================================
// 🚀 MODE 1: FULL SEARCH (GRID RENDER)
// =====================================
if ($mode === 'full') {

    // ✅ Separate total-count query for pagination
    $countStmt = $db->connection->prepare("
    SELECT COUNT(*) AS total
    FROM restaurants
    WHERE status = 'approved'
      AND (name LIKE ? OR tags LIKE ? OR city LIKE ? OR state LIKE ? OR district LIKE ?)
");
    $countStmt->bind_param('sssss', $like, $like, $like, $like, $like);
    $countStmt->execute();
    $total = $countStmt->get_result()->fetch_assoc()['total'];
    $countStmt->close();
    $total_pages = ceil($total / $limit);

    $restaurants = [];
    while ($row = $result->fetch_assoc()) {
        if (is_string($row['tags'])) {
            $decoded = json_decode($row['tags'], true);
            $row['tags'] = $decoded ?: $row['tags'];
        }
        $restaurants[] = $row;
    }

    echo json_encode([
        "restaurants" => $data,
        "total_pages" => $total_pages,
        "current_page" => $page,
        "query" => $q
    ]);

    exit;
}

// =====================================
// 🚀 MODE 2: SUGGESTION DROPDOWN (SMART FILTER)
// =====================================
if (count($data) > 0) {

    $suggestions = [];
    $typed = strtolower(trim($_GET['q'] ?? ''));

    foreach ($data as $r) {
        // 🧩 Check which fields actually match the query
        if (!empty($r['name']) && stripos($r['name'], $typed) !== false)
            $suggestions[] = trim($r['name']);

        if (!empty($r['tags'])) {
            $rawTags = trim($r['tags']);
            if (str_starts_with($rawTags, '[') && str_ends_with($rawTags, ']')) {
                $decoded = json_decode($rawTags, true);
                if (is_array($decoded)) {
                    foreach ($decoded as $tag) {
                        if (stripos($tag, $typed) !== false) {
                            $suggestions[] = trim($tag);
                        }
                    }
                }
            } else {
                $tags = explode(',', $rawTags);
                foreach ($tags as $tag) {
                    if (stripos($tag, $typed) !== false) {
                        $suggestions[] = trim($tag);
                    }
                }
            }
        }

        // 🧭 Include location only if it matches query
        foreach (['city', 'district', 'state', 'location'] as $locField) {
            if (!empty($r[$locField]) && stripos($r[$locField], $typed) !== false) {
                $suggestions[] = trim($r[$locField]);
            }
        }
    }

    // 🧹 Clean up and prioritize
    $suggestions = array_map('trim', $suggestions);
    $suggestions = array_filter($suggestions);
    $suggestions = array_unique($suggestions);
    natcasesort($suggestions);

    // 🎯 Prioritize ones starting with typed query
    usort($suggestions, function ($a, $b) use ($typed) {
        $aMatch = stripos($a, $typed) === 0 ? 0 : 1;
        $bMatch = stripos($b, $typed) === 0 ? 0 : 1;
        return $aMatch <=> $bMatch;
    });

    // 💅 Render
    echo "<div class='px-3 py-1 text-xs text-gray-400 uppercase'>Suggestions</div>";
    foreach ($suggestions as $item) {
        $isTag = strpos($item, ' ') === false && strlen($item) <= 15;
        $colorClass = $isTag ? "text-black" : "text-black";
        echo "
        <a href='#'
           data-query='" . htmlspecialchars($item, ENT_QUOTES) . "'
           class=' result-item block px-4 py-2 hover:bg-gray-100 rounded-lg transition'>
           <div class='font-semibold {$colorClass}'>" . htmlspecialchars($item) . "</div>
        </a>
        ";
    }

    exit;
} else {
    echo "<p class='text-gray-500 text-center py-2'>No results found for <b>" . htmlspecialchars($q) . "</b>.</p>";
    exit;
}
