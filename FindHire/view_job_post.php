<?php
session_start();
include_once 'models.php';
include_once 'handleForms.php';

$jobPostId = $_GET['id'];

// Fetch job post details
$jobPost = getJobPostsById($jobPostId);

// If the job post doesn't exist, redirect to another page or show an error
if (!$jobPost) {
    header('Location: index.php'); // Or show an error page
    exit;
}

// Fetch applicants for the job post (only for HR view)
$applicants = [];
if ($_SESSION['user']['role'] == 'HR') {
    $applicants = getApplicantsForJobPost($jobPostId);
}

// If the user is an Applicant
if ($_SESSION['user']['role'] == 'Applicant') {
    $applicantId = $_SESSION['user']['id'];
    
    // Fetch the applicant's application details for the job post
    $application = getApplicationByApplicantAndJobPost($applicantId, $jobPostId);

    // Handle job application submission
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['resume'])) {
        handleApplication($applicantId, $jobPostId, $_POST['cover_letter'], $_FILES['resume']);
        $applicationStatus = 'Application submitted successfully!';
        // Re-fetch the application status after submission
        $application = getApplicationByApplicantAndJobPost($applicantId, $jobPostId);
    }
}

// Handle hiring or rejecting applicants for HR role
if ($_SESSION['user']['role'] == 'HR' && $_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $applicationId = $_POST['application_id'];
    $action = $_POST['action'];

    if ($action == 'hire') {
        hireApplicant($applicationId);
    } elseif ($action == 'reject') {
        rejectApplicant($applicationId);
    }

    // Redirect to refresh the page after action
    header("Location: view_job_post.php?id=$jobPostId");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Job Post</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header class="view-job-header">
        <div class="header-content">
            <h1 class="page-title"><?= htmlspecialchars($jobPost['title']) ?></h1>
            <nav class="header-nav">
                <a href="message.php" class="nav-link">Messages</a>
                <a href="<?php echo ($_SESSION['user']['role'] == 'Applicant') ? 'applicant_dashboard.php' : 'hr_dashboard.php'; ?>" class="nav-link">Dashboard</a>
                <a href="logout.php" class="btn btn-logout">Logout</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="job-details">
            <p class="job-description"><?= htmlspecialchars($jobPost['description']) ?></p>

            <?php if (isset($applicationStatus)) { ?>
                <p class="success-message"><?= $applicationStatus ?></p>
            <?php } ?>

            <?php if ($_SESSION['user']['role'] == 'Applicant') { ?>
                <?php if ($application) { ?>
                    <div class="applicant-card">
                        <h3>Your Application</h3>
                        <div>
                            <p><strong>Cover Letter:</strong> <?= htmlspecialchars($application['cover_letter']) ?></p>
                            <p><strong>Status:</strong> <?= htmlspecialchars($application['status']) ?></p>
                            <p><strong>Resume:</strong> <a href="<?= htmlspecialchars($application['resume_path']) ?>" target="_blank">View Resume</a></p>
                        </div><br>
                        <br><a href="message.php" class="btn btn-primary">Message HR Representative</a>
                    </div>
                <?php } else { ?>
                    <form action="view_job_post.php?id=<?= $jobPostId ?>" method="POST" enctype="multipart/form-data" class="application-form">
                        <div class="form-group">
                            <label for="cover_letter" class="form-label">Cover Letter</label>
                            <textarea id="cover_letter" name="cover_letter" class="form-textarea" placeholder="Cover Letter" required></textarea>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Resume (PDF ONLY)</label>
                            <input type="file" name="resume" accept=".pdf" required class="form-input">
                        </div>
                        <button type="submit" class="btn btn-primary">Apply Now</button>
                    </form>
                <?php } ?>
            <?php } elseif ($_SESSION['user']['role'] == 'HR') { ?>
                <div class="applicants-section">
                    <h2>Applicants</h2>
                    <?php if (empty($applicants)) { ?>
                        <p>No applicants yet for this job post.</p>
                    <?php } else { ?>
                        <?php foreach ($applicants as $applicant) { ?>
                            <div class="applicant-card">
                                <h3><?= htmlspecialchars($applicant['username']) ?></h3>
                                <p><strong>Cover Letter:</strong> <?= htmlspecialchars($applicant['cover_letter']) ?></p>
                                <p><strong>Resume:</strong> <a href="<?= htmlspecialchars($applicant['resume_path']) ?>" target="_blank">View Resume</a></p>
                                <p><strong>Status:</strong> <?= htmlspecialchars($applicant['status']) ?></p>
                                <div class="applicant-actions">
                                    <form action="view_job_post.php?id=<?= $jobPostId ?>" method="POST">
                                        <input type="hidden" name="application_id" value="<?= $applicant['application_id'] ?>">
                                        <button type="submit" name="action" value="hire" class="btn btn-hire">Hire</button>
                                        <button type="submit" name="action" value="reject" class="btn btn-reject">Reject</button>
                                    </form>
                                    <a href="message.php?receiver_id=<?= $applicant['applicant_id'] ?>" class="btn btn-primary">Message Applicant</a>
                                </div>
                            </div>
                        <?php } ?>
                    <?php } ?>
                </div>
            <?php } ?>

            <button onclick="window.location.href='<?php echo ($_SESSION['user']['role'] == 'Applicant') ? 'applicant_dashboard.php' : 'hr_dashboard.php'; ?>';" class="btn btn-primary">Go Back</button>
        </div>
    </div>
</body>
</html>
