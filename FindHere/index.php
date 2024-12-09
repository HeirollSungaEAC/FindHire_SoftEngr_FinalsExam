<?php
// Start session and check if user is already logged in
session_start();

// If user is logged in, redirect to appropriate dashboard
if (isset($_SESSION['user'])) {
    header("Location: " . ($_SESSION['user']['role'] == 'HR' ? 'hr_dashboard.php' : 'applicant_dashboard.php'));
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FindHire - Homepage</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="homepage">
    <header class="homepage-header">
        <div class="header-content">
            <h1 class="site-title">Welcome to FindHire</h1>
            <p class="site-description">Your go-to platform for job applications</p>
        </div>
    </header>
    <div class="container homepage-container">
        <div class="btn-group">
            <a href="login.php" class="btn btn-primary">Login</a>
            <a href="register.php" class="btn btn-primary">Register</a>
        </div>
    </div>
</body>
</html>
