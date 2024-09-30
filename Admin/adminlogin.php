<?php
session_start(); 

$validEmail = "admin@gmail.com";
$validPassword = "password123";

$error = ""; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if ($email === $validEmail && $password === $validPassword) {
        $_SESSION['admin_logged_in'] = true;

        header("Location: index.php");
        exit();
    } else {
        $error = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Login</title>
    <link rel="stylesheet" href="adminlogin.css">
</head>
<body>
    <div class="auth-form">
        <h2>Admin Login</h2>
        <form id="loginForm" method="POST">
            <label for="loginEmail">Email:</label>
            <input type="email" id="loginEmail" name="email" required>
            
            <label for="loginPassword">Password:</label>
            <input type="password" id="loginPassword" name="password" required>
            
            <button type="submit" name="login">Login</button>
            <?php if (!empty($error)): ?>
                <p class="error"><?php echo $error; ?></p>
            <?php endif; ?>
        </form>
    </div>
    
    
</body>
</html>
