<?php
session_start();
if ($_SESSION['user']['role'] != 'Applicant') {
    header("Location: index.php");
    exit();
}
include_once 'models.php';
$jobPosts = getJobPosts();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Applicant Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header class="dashboard-header">
        <div class="header-content">
            <div class="logo-section">
                <h1 class="site-title">FindHire</h1>
                <p class="dashboard-label">Applicant Dashboard</p>
            </div>
            <div class="header-controls">
                <?php if (isset($_SESSION['user'])) { ?>
                    <a href="logout.php" class="btn btn-logout" id="logout-link">Logout</a>
                <?php } ?>
            </div>
        </div>
    </header>

    <main class="dashboard-main">
        <div class="container">
            <section class="job-posts">
                <div class="card">
                    <h2>Available Job Positions</h2>
                    <div class="job-list-container">
                        <?php if (empty($jobPosts)): ?>
                            <p class="no-jobs">No job positions available at the moment.</p>
                        <?php else: ?>
                            <ul class="job-list">
                                <?php foreach ($jobPosts as $post) { ?>
                                    <li>
                                        <a href="view_job_post.php?id=<?= $post['id'] ?>">
                                            <span><?= htmlspecialchars($post['title']) ?></span>
                                        </a>
                                    </li>
                                <?php } ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
        </div>
    </main>
</body>
</html>
