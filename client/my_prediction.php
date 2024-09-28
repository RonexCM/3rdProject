<?php
session_start();
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
<style>
    .actions a {
        padding: 5px 10px;
        margin:none;
        color: black;
        text-decoration: none;
        font-size: 16px;
        transition: color 0.3s ease;
        color: #5e656b;

}

.actions a:hover {
    color: black;

}

.actions a.active {
    color: #28a745; /* Highlight active link in green */
    text-decoration: none; /* Remove underline from active link */
    font-weight: bold;
    border-bottom:3px solid; 
}

</style>
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
        <div class="flex" style="display:flex;">
            <?php
            // Check if a model type is selected
            $model_type = isset($_GET['model_type']) ? $_GET['model_type'] : 'all'; // Default to 'all'

            // Function to add active class
            function isActive($type, $model_type) {
                return $type === $model_type ? 'active' : '';
            }
            ?>
            <div class="actions">
                <a href="?model_type=" class="<?= isActive('', $model_type) ?>">All</a>
            </div>
            <div class="actions">
                <a href="?model_type=linear" class="<?= isActive('linear', $model_type) ?>">Linear Regression</a>
            </div>
            <div class="actions">
                <a href="?model_type=lasso" class="<?= isActive('lasso', $model_type) ?>">Lasso Regression</a>
            </div>
</div>


            <div class="data-table">
                <table id="data-table">
                    <thead>
                        <tr>
                            <th>S.N</th>
                            <th>Aana</th>
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

                        ini_set('display_errors', 1);
                        ini_set('display_startup_errors', 1);
                        error_reporting(E_ALL);
                        include("connection.php");

                        // Ensure that the user session is valid
                        if (!isset($_SESSION['userid'])) {
                            echo "User not logged in.";
                            exit();
                        }

                        // Get the user ID from the session
                        $user_id = $_SESSION['userid'];

                        // Fetch the model_type from the form POST request
                        $model_type = isset($_GET['model_type']) ? $_GET['model_type'] : '';

                        // Prepare SQL query based on model type and user ID
                        $sql = "SELECT * FROM predictions WHERE user_id = '" . mysqli_real_escape_string($conn, $user_id) . "'";
                        if ($model_type) {
                            $sql .= " AND model_type = '" . mysqli_real_escape_string($conn, $model_type) . "'";
                        }

                        $result = $conn->query($sql);

                        // Output the result as table rows
                        if ($result && $result->num_rows > 0) {
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
                                            <button class='delete-button' data-id='{$row['user_id']}'>Delete</button>
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


    <footer>
        <p>&copy; 2024 House Price Prediction System. All rights reserved.</p>
    </footer>
</body>
</html>
