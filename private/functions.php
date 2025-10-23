<?php
function getCoordinatesFromAddress($city, $district, $state)
{
    $fullAddress = urlencode(trim("$city, $district, $state, India"));

    // Use OpenStreetMap (Nominatim)
    $url = "https://nominatim.openstreetmap.org/search?q=$fullAddress&format=json&limit=1";

    // Initialize cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'MeFoodie-App'); // required by OSM

    $response = curl_exec($ch);
    curl_close($ch);

    if (!$response) return [0, 0];

    $data = json_decode($response, true);

    if (!empty($data[0])) {
        $lat = floatval($data[0]['lat']);
        $lon = floatval($data[0]['lon']);
        return [$lat, $lon];
    }

    return [0, 0];
}
