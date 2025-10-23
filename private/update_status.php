<?php
require_once("../private/initialize.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $status = $_POST['status'] ?? null;

    if ($id && $status) {
        $sql = "UPDATE restaurants SET status = '" . $db->connection->real_escape_string($status) . "' WHERE id = " . intval($id);
        if ($db->query($sql)) {
            echo "Status updated successfully to $status.";
        } else {
            echo "Error updating status.";
        }
    } else {
        echo "Invalid request.";
    }
}
