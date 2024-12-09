<?php
session_start();
if ($_SESSION['user']['role'] != 'HR') {
    header("Location: index.php");
    exit();
}

include_once 'models.php';

// Fetch job posts
$jobPosts = getJobPosts();

// Handle the delete request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_job_post'])) {
    $jobPostId = $_POST['job_post_id'];
    deleteJobPost($jobPostId);
    header('Location: hr_dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FindHire - HR Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="hr-dashboard">
    <header class="dashboard-header">
        <div class="header-content">
            <div class="logo-section">
                <h1 class="site-title">FindHire</h1>
                <p class="dashboard-label">HR Dashboard</p>
            </div>
            <nav>
                <a href="logout.php" class="btn btn-logout" id="logout-link">Logout</a>
            </nav>
        </div>
    </header>

    <main class="dashboard-main">
        <div class="container">
            <section class="actions-section">
                <form action="create_job_post.php" method="GET">
                    <button type="submit" class="btn btn-primary" id="create-job-btn">
                        Create New Job Post
                    </button>
                </form>
            </section>

            <section class="jobs-section card">
                <h2>Active Job Listings</h2>

                <?php if (empty($jobPosts)): ?>
                    <div class="empty-state">
                        <p class="empty-message">No job posts available</p>
                        <p class="empty-submessage">Create your first job post to get started</p>
                    </div>
                <?php else: ?>
                    <table class="jobs-table">
                        <thead>
                            <tr>
                                <th>Job Title</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($jobPosts as $post): ?>
                                <tr class="job-row" id="job-<?= $post['id'] ?>">
                                    <td class="job-title">
                                        <?= htmlspecialchars($post['title']) ?>
                                    </td>
                                    <td class="job-actions">
                                        <br><a href="view_job_post.php?id=<?= $post['id'] ?>" class="btn btn-primary" style="margin-right: 10px;">
                                            View Details
                                        </a>
                                        <a href="message.php?job_id=<?= $post['id'] ?>" class="btn btn-primary" id="messages-link" style="margin-right: 10px;">
                                            Messages
                                        </a>
                                        <form action="hr_dashboard.php" method="POST" class="delete-form" style="display: inline;">
                                            <input type="hidden" name="job_post_id" value="<?= $post['id'] ?>">
                                            <button type="submit" name="delete_job_post" 
                                                    class="btn btn-danger"
                                                    onclick="return confirm('Are you sure you want to delete this job post?');">
                                                Delete Post
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </section>
        </div>
    </main>

</body>
</html>
