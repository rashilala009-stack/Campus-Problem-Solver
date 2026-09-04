<?php

require "db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    // Check if email already exists
    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {

        echo "<h2>Email already registered!</h2>";
        echo '<a href="../register.html">Try another email</a>';

        $check->close();
        $conn->close();
        exit;
    }

    $check->close();

    // Securely hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert user
    $sql = "INSERT INTO users (name, email, password)
            VALUES (?, ?, ?)";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "sss",
        $name,
        $email,
        $hashedPassword
    );

    if ($stmt->execute()) {

        echo "<h2>Registration successful!</h2>";
        echo "<p>Your student account has been created.</p>";
        echo '<a href="../login.html">Login Now</a>';

    } else {

        echo "Registration failed: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();

} else {

    echo "Invalid request.";
}

?>