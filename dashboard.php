<?php

session_start();

require "php/db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.html");
    exit;
}

if (isset($_GET["voted"])) {

    if ($_GET["voted"] === "success") {
        $voteMessage = '<div class="vote-message success">
                            ✅ Your support has been recorded!
                        </div>';
    }

    if ($_GET["voted"] === "already") {
        $voteMessage = '<div class="vote-message already">
                            👍 You have already supported this issue.
                        </div>';
    }
}

$sql = "SELECT * FROM issues ORDER BY votes DESC, created_at DESC";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Campus Issues | Campus Problem Solver</title>

    <link rel="stylesheet" href="css/style.css">

</head>

<body>

<header>

    <h1>Campus Problem Solver</h1>

    <nav>
        <a href="index.html">Home</a>
        <a href="report.html">Report Issue</a>
        <a href="my_reports.php">My Reports</a>
        <a href="php/logout.php">Logout</a>
    </nav>

</header>

<?php

if (isset($voteMessage)) {
    echo $voteMessage;
}

?>

<main class="dashboard">

    <h2>Campus Issues</h2>

    <p style="text-align: center;">
        Welcome,
        <strong>
            <?php echo htmlspecialchars($_SESSION["user_name"]); ?>
        </strong>
    </p>

    <p class="dashboard-intro">
        View reported problems and help prioritize important issues.
    </p>

    <div class="issues-container">

        <?php if ($result->num_rows > 0): ?>

            <?php while ($issue = $result->fetch_assoc()): ?>

                <div class="issue-card">

                    <span class="category">
                        <?php echo htmlspecialchars($issue["category"]); ?>
                    </span>

                    <h3>
                        <?php echo htmlspecialchars($issue["title"]); ?>
                    </h3>

                    <p>
                        <strong>📍 Location:</strong>
                        <?php echo htmlspecialchars($issue["location"]); ?>
                    </p>

                    <p>
                        <strong>Description:</strong><br>
                        <?php echo nl2br(htmlspecialchars($issue["description"])); ?>
                    </p>

                    <?php if (!empty($issue["photo"])): ?>

                        <img
                            src="uploads/<?php echo htmlspecialchars($issue["photo"]); ?>"
                            alt="Issue photo"
                            class="issue-photo"
                        >

                    <?php endif; ?>

                    <div class="status">
                        Status:
                        <?php echo htmlspecialchars($issue["status"]); ?>
                    </div>

                    <div class="votes">
                        👍 Votes: <?php echo (int)$issue["votes"]; ?>
                    </div>

                    <form action="php/vote.php" method="POST">

                        <input
                            type="hidden"
                            name="issue_id"
                            value="<?php echo (int)$issue["id"]; ?>"
                        >

                        <button type="submit">
                            👍 Support This Issue
                        </button>

                    </form>

                </div>

            <?php endwhile; ?>

        <?php else: ?>

            <div class="no-issues">

                <h3>No issues reported yet.</h3>

                <p>
                    Be the first person to report a campus problem.
                </p>

            </div>

        <?php endif; ?>

    </div>

</main>

<footer>

    <p>© 2026 Campus Problem Solver</p>

</footer>

</body>

</html>

<?php

$conn->close();

?>