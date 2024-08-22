<?php
include("connection.php");
session_start();

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (!empty($email) && !empty($password)) {
        // Prepare and execute the query
        $stmt = $conn->prepare("SELECT id, password FROM users WHERE email=?");
        if (!$stmt) {
            die("Preparation failed: " . $conn->error);
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($id, $hashed_password);

        if ($stmt->num_rows > 0) {
            $stmt->fetch();
            // Verify the password
            if (password_verify($password, $hashed_password)) {
                $_SESSION['userid'] = $id;
                header("Location: Index.php");
                exit(); // Ensure no further code runs after redirect
            } else {
                $error = "<p class='alert alert-warning'>Email or Password does not match!</p>";
            }
        } else {
            $error = "<p class='alert alert-warning'>Email or Password does not match!</p>";
        }

        $stmt->close();
    } else {
        $error = "<p class='alert alert-warning'>Please enter both email and password.</p>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>House Price Prediction System - Login</title>
    <link rel="stylesheet" href="login.css">
    <!-- <script src="validation.js" defer></script> -->
</head>
<body>
    <header>
        <h1>House Price Prediction System</h1>
        <nav>
            <ul>
                <li><a href="Index.php">Home</a></li>
                <li><a href="Index.php">About</a></li>
                <li><a href="Index.php">Contact</a></li>
                <li><a href="Index.php">Back</a></li>
            </ul>
        </nav>
    </header>
    
    <div class="auth-form">
        <h2>Login</h2>
        <form id="loginForm" method="POST">
            <label for="loginEmail">Email:</label>
            <input type="email" id="loginEmail" name="email" required>
            
            <label for="loginPassword">Password:</label>
            <input type="password" id="loginPassword" name="password" required>
            
            <button type="submit" name="login">Login</button>
            <?php if (isset($error)): ?>
                <p class="error"><?php echo $error; ?></p>
            <?php endif; ?>
        </form>
        <p>Don't have an account? <a href="signup.php">Signup here</a></p>
    </div>
    
    <footer>
        <p>&copy; 2024 House Price Prediction System. All rights reserved.</p>
    </footer>
</body>
</html>
