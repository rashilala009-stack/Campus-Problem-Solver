<?php

session_start();

require "php/db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.html");
    exit;
}

$user_id = $_SESSION["user_id"];

$sql = "SELECT * FROM issues 
        WHERE user_id = ?
        ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>My Reports | Campus Problem Solver</title>

    <link rel="stylesheet" href="css/style.css">

</head>

<body>

<header>

    <h1>Campus Problem Solver</h1>

    <nav>
        <a href="index.html">Home</a>
        <a href="dashboard.php">Dashboard</a>
        <a href="report.html">Report Issue</a>
        <a href="php/logout.php">Logout</a>
    </nav>

</header>

<main class="form-container">

    <h2>My Reports</h2>

    <p style="text-align:center;">
        Issues reported by 
        <strong>
            <?php echo htmlspecialchars($_SESSION["user_name"]); ?>
        </strong>
    </p>

    <?php if ($result->num_rows === 0): ?>

        <p style="text-align:center;">
            You haven't reported any issues yet.
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

                <p>
                    <strong>Votes:</strong>
                    <?php echo (int)$issue["votes"]; ?>
                </p>

                <p>
                    <strong>Reported:</strong>
                    <?php echo htmlspecialchars($issue["created_at"]); ?>
                </p>

                <?php if (!empty($issue["photo"])): ?>

                    <img
                        src="uploads/<?php echo htmlspecialchars($issue["photo"]); ?>"
                        alt="Issue Photo"
                        style="max-width:300px;"
                    >

                <?php endif; ?>

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

$stmt->close();
$conn->close();

?>