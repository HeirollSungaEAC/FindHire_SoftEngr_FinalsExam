<?php
session_start();
include_once 'handleForms.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];
    handleRegistration($username, $password, $role);
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FindHire - Register</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="register-page">
        <header class="register-header">
            <h1>Create Your Account</h1>
            <p>Join FindHire today and start your journey</p>
        </header>
        
        <div class="register-container">
            <form action="register.php" method="POST" class="register-form" id="registerForm">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" placeholder="Enter your username" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                </div>
                
                <div class="form-group">
                    <label for="role">Select Role</label>
                    <select id="role" name="role" required>
                        <option value="" disabled selected>Choose your role</option>
                        <option value="HR">HR Representative</option>
                        <option value="Applicant">Job Seeker</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Create Account</button>
            </form>

            <div class="register-footer">
                <p>
                    Already have an account? 
                    <a href="login.php">Sign in here</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
