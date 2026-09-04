<?php

require "db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $issue_id = intval($_POST["issue_id"]);

    if ($issue_id <= 0) {
        die("Invalid issue.");
    }

    $sql = "UPDATE issues 
            SET votes = votes + 1 
            WHERE id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $issue_id);

    if ($stmt->execute()) {
        header("Location: ../dashboard.php");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}

?>