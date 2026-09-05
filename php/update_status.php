<?php

session_start();

require "db.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["user_role"] !== "admin") {
    die("Access denied.");
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Invalid request.");
}

$issue_id = intval($_POST["issue_id"]);
$status = trim($_POST["status"]);

$allowed_statuses = ["Pending", "In Progress", "Resolved"];

if ($issue_id <= 0 || !in_array($status, $allowed_statuses)) {
    die("Invalid issue or status.");
}

$stmt = $conn->prepare(
    "UPDATE issues SET status = ? WHERE id = ?"
);

$stmt->bind_param("si", $status, $issue_id);

if ($stmt->execute()) {
    header("Location: ../admin.php");
    exit;
}

echo "Error updating status: " . $stmt->error;

$stmt->close();
$conn->close();

?>