<?php

session_start();
require "db.php";

/* Check whether the user is logged in */
if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.html");
    exit;
}

$user_id = (int) $_SESSION["user_id"];

/* Only allow POST requests */
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../report.html");
    exit;
}

/* Get and validate form data */
$title = trim($_POST["title"] ?? "");
$category = trim($_POST["category"] ?? "");
$location = trim($_POST["location"] ?? "");
$description = trim($_POST["description"] ?? "");
$anonymous = isset($_POST["anonymous"]) ? 1 : 0;

/* Required-field validation */
if ($title === "" || $category === "" || $location === "" || $description === "") {
    die("Please fill in all required fields.");
}

/* Length validation */
if (mb_strlen($title) > 255) {
    die("Issue title is too long.");
}

if (mb_strlen($category) > 100) {
    die("Category is too long.");
}

if (mb_strlen($location) > 255) {
    die("Location is too long.");
}

if (mb_strlen($description) > 5000) {
    die("Description is too long.");
}

$photoName = null;

/* Secure image upload handling */
if (isset($_FILES["photo"]) && $_FILES["photo"]["error"] !== UPLOAD_ERR_NO_FILE) {

    /* Check upload error */
    if ($_FILES["photo"]["error"] !== UPLOAD_ERR_OK) {
        die("There was a problem uploading the image.");
    }

    /* Maximum file size: 5 MB */
    $maxFileSize = 5 * 1024 * 1024;

    if ($_FILES["photo"]["size"] > $maxFileSize) {
        die("Image size must be 5 MB or less.");
    }

    $tmpName = $_FILES["photo"]["tmp_name"];

    /* Verify that the uploaded file is actually an image */
    $imageInfo = getimagesize($tmpName);

    if ($imageInfo === false) {
        die("Only valid image files are allowed.");
    }

    /* Allow only specific image MIME types */
    $allowedMimeTypes = [
        "image/jpeg" => "jpg",
        "image/png"  => "png",
        "image/webp" => "webp"
    ];

    $mimeType = $imageInfo["mime"];

    if (!isset($allowedMimeTypes[$mimeType])) {
        die("Only JPG, PNG, and WEBP images are allowed.");
    }

    /* Generate a safe unique filename */
    $extension = $allowedMimeTypes[$mimeType];
    $photoName = bin2hex(random_bytes(16)) . "." . $extension;

    /* Make sure upload directory exists */
    $uploadDirectory = "../uploads/";

    if (!is_dir($uploadDirectory)) {
        if (!mkdir($uploadDirectory, 0755, true)) {
            die("Unable to create upload directory.");
        }
    }

    $uploadPath = $uploadDirectory . $photoName;

    /* Move uploaded file */
    if (!move_uploaded_file($tmpName, $uploadPath)) {
        die("Unable to save the uploaded image.");
    }
}

/* Insert issue using prepared statement */
$sql = "INSERT INTO issues
        (user_id, title, category, location, description, photo, anonymous)
        VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Unable to process the request.");
}

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

/* Save issue */
if ($stmt->execute()) {

    $stmt->close();
    $conn->close();

    echo "<!DOCTYPE html>";
    echo "<html lang='en'>";
    echo "<head>";
    echo "<meta charset='UTF-8'>";
    echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
    echo "<title>Issue Submitted</title>";
    echo "</head>";
    echo "<body>";
    echo "<h2>Issue submitted successfully!</h2>";
    echo "<p>Your complaint has been recorded.</p>";
    echo "<a href='../dashboard.php'>Go to Dashboard</a>";
    echo "</body>";
    echo "</html>";

    exit;

} else {

    /* Remove uploaded image if database insertion fails */
    if ($photoName !== null) {
        $uploadedFile = "../uploads/" . $photoName;

        if (file_exists($uploadedFile)) {
            unlink($uploadedFile);
        }
    }

    $stmt->close();
    $conn->close();

    die("Unable to submit the issue. Please try again.");
}
?>