<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include("connection.php");
session_start();

// Ensure that the user session is valid
if (!isset($_SESSION['userid'])) {
    echo "User not logged in.";
    exit();
}

// Get the user ID from the session
$user_id = $_SESSION['userid'];

// Fetch predictions from the database for the logged-in user
$model_type = isset($_POST['model_type']) ? $_POST['model_type'] : '';

// Prepare SQL query based on model type and user ID
$sql = "SELECT * FROM predictions WHERE user_id = '" . mysqli_real_escape_string($conn, $user_id) . "'";
if ($model_type) {
    $sql .= " AND model_type = '" . mysqli_real_escape_string($conn, $model_type) . "'";
}
$result = $conn->query($sql);


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>House Price Prediction System</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="app.js"></script>
    <!-- <link rel="stylesheet" href="../Admin/datamanage.css"> -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
        <h1>House Price Prediction System</h1>
        <nav>
            <ul>
                <!-- <li><a href="#">Home</a></li> -->
                <li><a href="Index.php">Home</a></li>
                <li><a href="#about">About</a></li>
                <li><a href="#contact">Contact</a></li>
                <?php if(isset($_SESSION['userid'])): ?>
                    <li><a href="my_prediction.php">My Prediction</a></li>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                <li><a href="login.php">Login</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <div class="content">

        <div class="data-management">
            <div class="flex" style="display:flex; gap:10px;">
                <div class="actions">
                    <button onclick="filterData('linear')">All</button>
                </div>
                <div class="actions">
                    <button onclick="filterData('linear')">Linear Regression</button>
                </div>
                <div class="actions">
                    <button onclick="filterData('lasso')">Lasso Regression</button>
                </div>
            </div>

            <div class="data-table">
                <table id="data-table">
                    <thead>
                        <tr>
                            <th>S.N</th>
                            <th>Aana</tgsdh>
                            <th>Bedroom</th>
                            <th>Bathroom</th>
                            <th>Floor</th>
                            <th>Road</th>
                            <th>Price</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Check if the result has rows
                        if ($result && $result->num_rows > 0) {
                            // Output data of each row
                            $sn = 1; // Serial number
                            while($row = $result->fetch_assoc()) {
                                echo "<tr>
                                        <td>{$sn}.</td>
                                        <td>{$row['aana']}</td>
                                        <td>{$row['bedroom']}</td>
                                        <td>{$row['bathroom']}</td>
                                        <td>{$row['floor']}</td>
                                        <td>{$row['road']}</td>
                                        <td>{$row['price']} Crores</td>
                                        <td>{$row['date']}</td>
                                        <td>
                                            <button class='delete-button' data-id='{$row['id']}'>Delete</button>
                                        </td>
                                    </tr>";
                                $sn++;
                            }
                        } else {
                            echo "<tr><td colspan='8'>No records found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function filterData(modelType) {
            $.ajax({
                type: "POST",
                url: "your_php_file.php", // Update with the path to this PHP file
                data: { model_type: modelType },
                success: function(data) {
                    // Replace the table body with new data
                    $('#data-table tbody').html(data);
                }
            });
        }
    </script>

    <footer>
        <p>&copy; 2024 House Price Prediction System. All rights reserved.</p>
    </footer>
</body>
</html>
