<?php
if (!isset($_GET['lat']) || !isset($_GET['lon'])) {
    http_response_code(400);
    echo "Missing coordinates.";
    exit;
}

$lat = floatval($_GET['lat']);
$lon = floatval($_GET['lon']);

$url = "https://nominatim.openstreetmap.org/reverse?format=json&lat=$lat&lon=$lon&zoom=10&addressdetails=1";

$options = [
    "http" => [
        "header" => "User-Agent: MeFoodieApp/1.0\r\n"
    ]
];
$context = stream_context_create($options);

$response = file_get_contents($url, false, $context);
if (!$response) {
    echo "Unable to fetch location.";
    exit;
}

$data = json_decode($response, true);
$address = $data['address'] ?? [];

$city = $address['city']
    ?? $address['town']
    ?? $address['municipality']
    ?? $address['county']
    ?? $address['district']
    ?? $address['state_district']
    ?? $address['village']
    ?? $address['suburb']
    ?? $address['state']
    ?? "Unknown";

$state = $address['state'] ?? '';
$district = $address['district'] ?? '';
$address = $data['address'] ?? [];

// if (!empty($address)) {
//     echo "<h3>📍 Your Detected Location:</h3>";
//     echo "<ul style='list-style: none; padding: 0;'>";
//     foreach ($address as $key => $value) {
//         echo "<li><strong>" . ucfirst($key) . ":</strong> " . htmlspecialchars($value) . "</li>";
//     }
//     echo "</ul>";
// } else {
//     echo "Unable to detect location details.";
// }

$order = ['city', 'town', 'village', 'county', 'district', 'state_district', 'state', 'postcode', 'country'];
foreach ($order as $key) {
    if (!empty($address[$key])) {
        echo "<li><strong>" . ucfirst($key) . ":</strong> " . htmlspecialchars($address[$key]) . "</li>";
    }
}
