<?php
require_once("../private/initialize.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $status = $_POST['status'] ?? null;

    if ($id && $status) {
        // Clean inputs
        $id = intval($id);
        $status = $db->connection->real_escape_string($status);

        if ($status === 'delete') {
            // Delete the record instead of updating
            $sql = "DELETE FROM restaurants WHERE id = $id";
            $action = "deleted";
        } else {
            // Normal status update
            $sql = "UPDATE restaurants SET status = '$status' WHERE id = $id";
            $action = "updated to $status";
        }

        if ($db->query($sql)) {
            echo "Restaurant record successfully $action.";
        } else {
            echo "Database error: " . $db->connection->error;
        }
    } else {
        echo "Invalid request: missing ID or status.";
    }
}
