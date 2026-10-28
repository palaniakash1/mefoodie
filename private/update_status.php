<?php
session_start();

if (empty($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(403);
    echo "Unauthorized access.";
    exit;
}

$csrf_token = $_POST['csrf_token'] ?? '';
if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $csrf_token)) {
    http_response_code(403);
    echo "Invalid CSRF token.";
    exit;
}

require_once("../private/initialize.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $status = $_POST['status'] ?? null;

    $allowed_statuses = ['approved', 'disapproved', 'deleted'];

    if ($id > 0 && in_array($status, $allowed_statuses, true)) {
        if ($status === 'deleted') {
            $stmt = $db->connection->prepare("DELETE FROM businesses WHERE id = ?");
            $stmt->bind_param("i", $id);
            $action = "deleted";
        } else {
            $stmt = $db->connection->prepare("UPDATE businesses SET status = ? WHERE id = ?");
            $stmt->bind_param("si", $status, $id);
            $action = "updated to $status";
        }

        if ($stmt && $stmt->execute()) {
            echo "business record successfully $action.";
            $stmt->close();
        } else {
            echo "Database error: " . $db->connection->error;
        }
    } else {
        echo "Invalid request: invalid ID or status.";
    }
}
