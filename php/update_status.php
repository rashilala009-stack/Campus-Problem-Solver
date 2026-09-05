<?php

session_start();
require "db.php";

/* Only logged-in admins can update status */
if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.html");
    exit;
}

if (!isset($_SESSION["user_role"]) || $_SESSION["user_role"] !== "admin") {
    http_response_code(403);
    die("Access denied. Admins only.");
}

/* Only allow POST requests */
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../admin.php");
    exit;
}

/* Get issue ID and status */
$issue_id = filter_input(INPUT_POST, "issue_id", FILTER_VALIDATE_INT);
$status = trim($_POST["status"] ?? "");

/* Validate issue ID */
if ($issue_id === false || $issue_id === null || $issue_id <= 0) {
    die("Invalid issue ID.");
}

/* Allow only valid status values */
$allowedStatuses = [
    "Pending",
    "In Progress",
    "Resolved"
];

if (!in_array($status, $allowedStatuses, true)) {
    die("Invalid status.");
}

/* Update issue status */
$sql = "UPDATE issues SET status = ? WHERE id = ?";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Unable to process the request.");
}

$stmt->bind_param("si", $status, $issue_id);

if (!$stmt->execute()) {
    $stmt->close();
    $conn->close();
    die("Unable to update issue status.");
}

$stmt->close();
$conn->close();

/* Return to Admin Panel */
header("Location: ../admin.php");
exit;

?>