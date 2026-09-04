<?php

session_start();

require "db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    // Find user by email
    $stmt = $conn->prepare(
        "SELECT id, name, password, role FROM users WHERE email = ?"
    );

    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows === 1) {

        $user = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $user["password"])) {

    $_SESSION["user_id"] = $user["id"];
    $_SESSION["user_name"] = $user["name"];
    $_SESSION["user_email"] = $email;
    $_SESSION["user_role"] = $user["role"];

    echo "<h2>Login successful!</h2>";
            echo "<p>Welcome, " . htmlspecialchars($user["name"]) . "!</p>";
            echo '<a href="../dashboard.php">Go to Dashboard</a>';

        } else {

            echo "<h2>Incorrect password!</h2>";
            echo '<a href="../login.html">Try again</a>';
        }

    } else {

        echo "<h2>Account not found!</h2>";
        echo '<a href="../register.html">Create an account</a>';
    }

    $stmt->close();
    $conn->close();

} else {

    echo "Invalid request.";
}

?>