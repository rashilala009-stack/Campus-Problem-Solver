<?php

session_start();

require "php/db.php";
if (isset($_GET["voted"])) {

    if ($_GET["voted"] === "success") {
        echo "<p style='text-align:center; color:green; font-weight:bold;'>
                ✅ Your support has been recorded!
              </p>";
    }

    if ($_GET["voted"] === "already") {
        echo "<p style='text-align:center; color:#d97706; font-weight:bold;'>
                👍 You have already supported this issue.
              </p>";
    }
}
if (!isset($_SESSION["user_id"])) {
    header("Location: login.html");
    exit;
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

    <style>
        .dashboard {
            max-width: 1100px;
            margin: 40px auto;
            padding: 20px;
        }

        .dashboard h2 {
            text-align: center;
            color: #1e3a8a;
            margin-bottom: 10px;
        }

        .dashboard-intro {
            text-align: center;
            margin-bottom: 30px;
        }

        .issues-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
        }

        .issue-card {
            background: white;
            padding: 22px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .issue-card h3 {
            color: #1e3a8a;
            margin-bottom: 12px;
        }

        .issue-card p {
            margin: 8px 0;
            line-height: 1.5;
        }

        .category {
            display: inline-block;
            background: #e0e7ff;
            color: #1e3a8a;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
        }

        .status {
            display: inline-block;
            margin-top: 10px;
            padding: 6px 12px;
            border-radius: 20px;
            background: #fff3cd;
            color: #856404;
            font-size: 13px;
            font-weight: bold;
        }

        .votes {
            margin-top: 15px;
            font-weight: bold;
        }

        .issue-photo {
            width: 100%;
            max-height: 200px;
            object-fit: cover;
            border-radius: 8px;
            margin-top: 15px;
        }

        .no-issues {
            text-align: center;
            background: white;
            padding: 40px;
            border-radius: 12px;
        }
    </style>

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


<main class="dashboard">

    <h2>Campus Issues</h2>>.
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
                        Status: <?php echo htmlspecialchars($issue["status"]); ?>
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

                <p>Be the first person to report a campus problem.</p>

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