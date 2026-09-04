<?php

session_start();

require "db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.html");
    exit;
}

$user_id = $_SESSION["user_id"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $title = trim($_POST["title"]);
    $category = trim($_POST["category"]);
    $location = trim($_POST["location"]);
    $description = trim($_POST["description"]);

    $anonymous = isset($_POST["anonymous"]) ? 1 : 0;

    $photoName = null;

    // Handle photo upload
    if (isset($_FILES["photo"]) && $_FILES["photo"]["error"] === 0) {

        $fileName = $_FILES["photo"]["name"];
        $tmpName = $_FILES["photo"]["tmp_name"];

        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $allowedExtensions = ["jpg", "jpeg", "png", "webp"];

        if (in_array($extension, $allowedExtensions)) {

            $photoName = uniqid("issue_", true) . "." . $extension;

            $uploadPath = "../uploads/" . $photoName;

            move_uploaded_file($tmpName, $uploadPath);
        }
    }

    // Insert issue into database
    $sql = "INSERT INTO issues 
        (user_id, title, category, location, description, photo, anonymous)
        VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
    "isssssi",
    $user_id,
    $title,
    $category,
    $location,
    $description,
    $photoName,
    $anonymous
);

    if ($stmt->execute()) {

        echo "<h2>Issue submitted successfully!</h2>";
        echo "<p>Your complaint has been recorded.</p>";
        echo '<a href="../index.html">Back to Home</a>';

    } else {

        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();

} else {

    echo "Invalid request.";
}

?>