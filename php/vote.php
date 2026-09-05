<?php

session_start();

require "db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.html");
    exit;
}

$user_id = $_SESSION["user_id"];

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Invalid request.");
}

$issue_id = intval($_POST["issue_id"]);

if ($issue_id <= 0) {
    die("Invalid issue.");
}

/* Check whether the student has already voted */

$check = $conn->prepare(
    "SELECT id FROM issue_votes WHERE issue_id = ? AND user_id = ?"
);

$check->bind_param("ii", $issue_id, $user_id);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {

    $check->close();
    $conn->close();

    header("Location: ../dashboard.php?voted=already");
    exit;
}

$check->close();

/* Record the vote */

$insert = $conn->prepare(
    "INSERT INTO issue_votes (issue_id, user_id) VALUES (?, ?)"
);

$insert->bind_param("ii", $issue_id, $user_id);

if (!$insert->execute()) {
    die("Error recording vote: " . $insert->error);
}

$insert->close();

/* Increase vote count */

$update = $conn->prepare(
    "UPDATE issues SET votes = votes + 1 WHERE id = ?"
);

$update->bind_param("i", $issue_id);
$update->execute();

$update->close();
$conn->close();

header("Location: ../dashboard.php?voted=success");
exit;

?>