<?php

session_start();

require "php/db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.html");
    exit;
}

if ($_SESSION["user_role"] !== "admin") {
    die("Access denied. Admins only.");
}

$sql = "SELECT * FROM issues ORDER BY votes DESC, created_at DESC";

$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Panel | Campus Problem Solver</title>

    <link rel="stylesheet" href="css/style.css">

</head>

<body>

<header>

    <h1>Campus Problem Solver</h1>

    <nav>
        <a href="index.html">Home</a>
        <a href="dashboard.php">Student Dashboard</a>
        <a href="php/logout.php">Logout</a>
    </nav>

</header>

<main class="form-container">

    <h2>Admin Panel</h2>

    <p style="text-align:center;">
        Manage and monitor campus issues
    </p>

    <?php if ($result->num_rows === 0): ?>

        <p style="text-align:center;">
            No issues have been reported yet.
        </p>

    <?php else: ?>

        <?php while ($issue = $result->fetch_assoc()): ?>

            <div class="issue-card">

                <h3>
                    <?php echo htmlspecialchars($issue["title"]); ?>
                </h3>

                <p>
                    <strong>Category:</strong>
                    <?php echo htmlspecialchars($issue["category"]); ?>
                </p>

                <p>
                    <strong>Location:</strong>
                    <?php echo htmlspecialchars($issue["location"]); ?>
                </p>

                <p>
                    <strong>Description:</strong>
                    <?php echo htmlspecialchars($issue["description"]); ?>
                </p>

                <p>
                    <strong>Status:</strong>
                    <?php echo htmlspecialchars($issue["status"]); ?>
                </p>
                <form action="php/update_status.php" method="POST">

    <input
        type="hidden"
        name="issue_id"
        value="<?php echo (int)$issue["id"]; ?>"
    >

    <select name="status" required>

        <option value="Pending"
            <?php if ($issue["status"] === "Pending") echo "selected"; ?>>
            Pending
        </option>

        <option value="In Progress"
            <?php if ($issue["status"] === "In Progress") echo "selected"; ?>>
            In Progress
        </option>

        <option value="Resolved"
            <?php if ($issue["status"] === "Resolved") echo "selected"; ?>>
            Resolved
        </option>

    </select>

    <button type="submit">
        Update Status
    </button>

</form>

                <p>
                    <strong>Votes:</strong>
                    <?php echo (int)$issue["votes"]; ?>
                </p>

                <p>
                    <strong>Reported:</strong>
                    <?php echo htmlspecialchars($issue["created_at"]); ?>
                </p>

            </div>

        <?php endwhile; ?>

    <?php endif; ?>

</main>

<footer>

    <p>© 2026 Campus Problem Solver</p>

</footer>

</body>
</html>

<?php

$conn->close();

?>