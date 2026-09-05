<?php

session_start();
require "db.php";

/* Only allow POST requests */
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../login.html");
    exit;
}

/* Get form data */
$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";

/* Validate input */
if ($email === "" || $password === "") {
    die("Please enter both email and password.");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Please enter a valid email address.");
}

/* Find user */
$sql = "SELECT id, name, email, password, role
        FROM users
        WHERE email = ?
        LIMIT 1";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Unable to process login.");
}

$stmt->bind_param("s", $email);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    $stmt->close();
    $conn->close();
    die("Invalid email or password.");
}

$user = $result->fetch_assoc();

/* Verify password */
if (!password_verify($password, $user["password"])) {
    $stmt->close();
    $conn->close();
    die("Invalid email or password.");
}

/* Prevent session fixation */
session_regenerate_id(true);

/* Store session information */
$_SESSION["user_id"] = (int) $user["id"];
$_SESSION["user_name"] = $user["name"];
$_SESSION["user_email"] = $user["email"];
$_SESSION["user_role"] = $user["role"];

/* Close database connection */
$stmt->close();
$conn->close();

/* Redirect based on role */
if ($user["role"] === "admin") {
    header("Location: ../admin.php");
    exit;
}

header("Location: ../dashboard.php");
exit;

?>