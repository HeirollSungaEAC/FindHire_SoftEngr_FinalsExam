<?php
session_start();
if ($_SESSION['user']['role'] != 'HR') {
    header("Location: index.php");
    exit();
}
include_once 'handleForms.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    createJobPost($title, $description);
    header("Location: hr_dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Job Post</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="create-job-page">
    <header class="dashboard-header">
        <div class="header-content">
            <h1 class="page-title">Create Job Post</h1>
            <nav class="header-nav">
                <a href="hr_dashboard.php" class="nav-link">Back to Dashboard</a>
            </nav>
        </div>
    </header>
    
    <main class="main-content">
        <div class="container">
            <div class="form-wrapper">
                <form action="create_job_post.php" method="POST" class="job-post-form">
                    <div class="form-group">
                        <label for="title" class="form-label">Job Title</label>
                        <input type="text" id="title" name="title" class="form-input" placeholder="Job Title" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="description" class="form-label">Job Description</label>
                        <textarea id="description" name="description" class="form-textarea" placeholder="Job Description" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <input type="submit" value="Create Job Post" class="btn btn-danger">
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>
</html>
