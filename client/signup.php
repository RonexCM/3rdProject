<?php
include("connection.php");
if (isset($_POST['signup'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];

    // Check if passwords match
    if ($password !== $cpassword) {
        echo "Passwords do not match!";
    } else {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        $conn = new mysqli("localhost", "root", "", "housepredict");

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $hashed_password);

        if ($stmt->execute()) {
            echo '<script>alert("Signed Up Successfully"); window.location.href="login.php";</script>';
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
        $conn->close();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>House Price Prediction System</title>
    <link rel="stylesheet" href="signup.css">
    <script src="validation.js" defer></script>
</head>
<body>
    <header>
        <h1>House Price Prediction System</h1>
        <nav>
            <ul>
                <li><a href="Index.html#home">Home</a></li>
                <li><a href="Index.html#about">About</a></li>
                <li><a href="Index.html#contact">Contact</a></li>
                <li><a href="login.php">Back</a></li>
            </ul>
        </nav>
    </header>
    
    <div class="auth-form">
        <h2>Sign Up</h2>
        <form id="signupForm" method="POST" >
            <label for="username">Name:</label>
            <input type="text" id="signupName" name="username" required>
            
            <label for="signupEmail">Email:</label>
            <input type="email" id="signupEmail" name="email" required>
            
            <label for="signupPassword">Password:</label>
            <input type="password" id="signupPassword" name="password" required>
            
            <label for="signupConfirmPassword">Confirm Password:</label>
            <input type="password" id="signupConfirmPassword" name="cpassword" required>
            
            <button type="submit" name="signup">Sign Up</button>
            <p id="signupError" class="error"></p>
        </form>
    </div>
    
    <footer>
        <p>&copy; 2024 House Price Prediction System. All rights reserved.</p>
    </footer>
</body>
</html>