<?php

session_start();
require "db.php";

/* Only allow POST requests */
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../register.html");
    exit;
}

/* Get form data */
$name = trim($_POST["name"] ?? "");
$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";

/* Validate required fields */
if ($name === "" || $email === "" || $password === "") {
    die("Please fill in all required fields.");
}

/* Validate name */
if (mb_strlen($name) < 2 || mb_strlen($name) > 100) {
    die("Name must be between 2 and 100 characters.");
}

/* Validate email */
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Please enter a valid email address.");
}

if (mb_strlen($email) > 150) {
    die("Email address is too long.");
}

/* Validate password */
if (strlen($password) < 8) {
    die("Password must be at least 8 characters long.");
}

if (strlen($password) > 72) {
    die("Password is too long.");
}

/* Check whether email already exists */
$checkSql = "SELECT id FROM users WHERE email = ? LIMIT 1";
$checkStmt = $conn->prepare($checkSql);

if (!$checkStmt) {
    die("Unable to process registration.");
}

$checkStmt->bind_param("s", $email);
$checkStmt->execute();

$checkResult = $checkStmt->get_result();

if ($checkResult->num_rows > 0) {
    $checkStmt->close();
    $conn->close();
    die("An account with this email already exists.");
}

$checkStmt->close();

/* Securely hash the password */
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

if ($hashedPassword === false) {
    $conn->close();
    die("Unable to secure the password.");
}

/* Create the student account */
$sql = "INSERT INTO users (name, email, password, role)
        VALUES (?, ?, ?, 'student')";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    $conn->close();
    die("Unable to process registration.");
}

$stmt->bind_param("sss", $name, $email, $hashedPassword);

/* Save account */
if ($stmt->execute()) {

    $stmt->close();
    $conn->close();

    echo "<!DOCTYPE html>";
    echo "<html lang='en'>";
    echo "<head>";
    echo "<meta charset='UTF-8'>";
    echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
    echo "<title>Registration Successful</title>";
    echo "</head>";
    echo "<body>";
    echo "<h2>Registration successful!</h2>";
    echo "<p>Your student account has been created.</p>";
    echo "<a href='../login.html'>Go to Login</a>";
    echo "</body>";
    echo "</html>";

    exit;

} else {

    $stmt->close();
    $conn->close();

    die("Unable to create account. Please try again.");
}

?>