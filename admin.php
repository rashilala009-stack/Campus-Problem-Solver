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

$total_result = $conn->query("SELECT COUNT(*) AS total FROM issues");
$total_issues = $total_result->fetch_assoc()["total"];

$pending_result = $conn->query(
    "SELECT COUNT(*) AS total FROM issues WHERE status = 'Pending'"
);
$pending_issues = $pending_result->fetch_assoc()["total"];

$progress_result = $conn->query(
    "SELECT COUNT(*) AS total FROM issues WHERE status = 'In Progress'"
);
$progress_issues = $progress_result->fetch_assoc()["total"];

$resolved_result = $conn->query(
    "SELECT COUNT(*) AS total FROM issues WHERE status = 'Resolved'"
);
$resolved_issues = $resolved_result->fetch_assoc()["total"];

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

<main class="admin-dashboard">

    <section class="admin-header">

        <h2>Admin Panel</h2>

        <p>
            Manage and monitor reported campus issues.
        </p>

    </section>

    <section class="admin-summary">

        <div class="summary-card">
            <h3><?php echo $total_issues; ?></h3>
            <p>Total Issues</p>
        </div>

        <div class="summary-card">
            <h3><?php echo $pending_issues; ?></h3>
            <p>Pending</p>
        </div>

        <div class="summary-card">
            <h3><?php echo $progress_issues; ?></h3>
            <p>In Progress</p>
        </div>

        <div class="summary-card">
            <h3><?php echo $resolved_issues; ?></h3>
            <p>Resolved</p>
        </div>

    </section>

    <section class="admin-issues">

        <h2>Reported Issues</h2>

        <?php if ($result->num_rows === 0): ?>

            <div class="no-issues">

                <h3>No issues have been reported yet.</h3>

                <p>
                    New campus reports will appear here.
                </p>

            </div>

        <?php else: ?>

            <?php while ($issue = $result->fetch_assoc()): ?>

                <div class="admin-issue-card">

                    <div class="admin-issue-top">

                        <div>

                            <span class="category">
                                <?php echo htmlspecialchars($issue["category"]); ?>
                            </span>

                            <h3>
                                <?php echo htmlspecialchars($issue["title"]); ?>
                            </h3>

                        </div>

                        <div class="admin-votes">
                            👍 <?php echo (int)$issue["votes"]; ?> votes
                        </div>

                    </div>

                    <div class="admin-issue-details">

                        <p>
                            <strong>📍 Location:</strong>
                            <?php echo htmlspecialchars($issue["location"]); ?>
                        </p>

                        <p>
                            <strong>Description:</strong><br>
                            <?php echo nl2br(htmlspecialchars($issue["description"])); ?>
                        </p>

                        <p>
                            <strong>Current Status:</strong>
                            <span class="status">
                                <?php echo htmlspecialchars($issue["status"]); ?>
                            </span>
                        </p>

                        <p class="reported-date">
                            <strong>Reported:</strong>
                            <?php echo htmlspecialchars($issue["created_at"]); ?>
                        </p>

                    </div>

                    <form action="php/update_status.php" method="POST" class="status-form">

                        <input
                            type="hidden"
                            name="issue_id"
                            value="<?php echo (int)$issue["id"]; ?>"
                        >

                        <label for="status-<?php echo (int)$issue["id"]; ?>">
                            Update Status
                        </label>

                        <select
                            id="status-<?php echo (int)$issue["id"]; ?>"
                            name="status"
                            required
                        >

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

                </div>

            <?php endwhile; ?>

        <?php endif; ?>

    </section>

</main>

<footer>

    <p>© 2026 Campus Problem Solver</p>

</footer>

</body>

</html>

<?php

$conn->close();

?>