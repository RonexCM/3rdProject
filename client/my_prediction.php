<?php
session_start();
require 'connection.php';

if (isset($_GET['action']) && $_GET['action'] == 'get_model_counts') {
    // Ensure that the user session is valid
    if (!isset($_SESSION['userid'])) {
        echo json_encode(['error' => 'User not logged in']);
        exit();
    }

    $user_id = $_SESSION['userid'];

    // SQL to count the number of predictions for each model type
    $sql = "SELECT model_type, COUNT(*) as count FROM predictions WHERE user_id = '" . mysqli_real_escape_string($conn, $user_id) . "' GROUP BY model_type";
    
    $result = $conn->query($sql);
    $counts = ['linear' => 0, 'lasso' => 0]; // Default counts

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $model_type = strtolower($row['model_type']);
            if (isset($counts[$model_type])) {
                $counts[$model_type] = $row['count'];
            }
        }
    }

    echo json_encode($counts);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>House Price Prediction System</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<style>
    .actions a {
        padding: 5px 10px;
        margin: none;
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
        border-bottom: 3px solid; 
    }

    /* Add some styling for the chart container */
    .chart-container {
        /* height:300px; */
        margin:auto;
        width: 700px;
    }
</style>
<body>
<header>
    <h1>House Price Prediction System</h1>
    <nav>
        <ul>
            <li><a href="Index.php">Home</a></li>
            <li><a href="#about">About</a></li>
            <li><a href="#contact">Contact</a></li>
            <?php if(isset($_SESSION['userid'])): ?>
                <li><a href="my_prediction.php?model_type=">My Prediction</a></li>
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
            <div class="actions">
                <a href="?model_type=analysis" class="<?= isActive('analysis', $model_type) ?>">Analytics</a>
            </div>
        </div>

        <div class="data-table">
            <?php
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);

            // Ensure that the user session is valid
            if (!isset($_SESSION['userid'])) {
                echo "User not logged in.";
                exit();
            }

            // Get the user ID from the session
            $user_id = $_SESSION['userid'];

            // Prepare SQL query based on model type and user ID
            $sql = "SELECT * FROM predictions WHERE user_id = '" . mysqli_real_escape_string($conn, $user_id) . "'";
            if ($model_type && $model_type !== 'all') {
                $sql .= " AND model_type = '" . mysqli_real_escape_string($conn, $model_type) . "'";
            }

            $result = $conn->query($sql);

            // Output the result as table rows
            if ($model_type !== 'analysis') {
                echo "<table id='data-table'>
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
                        <tbody>";

                if ($result && $result->num_rows > 0) {
                    $sn = 1; // Serial number
                    while ($row = $result->fetch_assoc()) {
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
                                    <form method='POST' action='my_prediction.php' style='display:inline;'>
                                        <input type='hidden' name='sn' value='{$row['SN']}'>
                                        <button type='submit' class='delete-button' onclick='return confirm(\"Are you sure you want to delete this entry?\");'>Delete</button>
                                    </form>
                                </td>
                            </tr>";
                        $sn++;
                    }
                } else {
                    echo "<tr><td colspan='9'>No records found</td></tr>";
                }
                echo "</tbody></table>";
            } else {
            
                // If the model type is 'analysis', prepare data for bar chart
                $chart_data_sql = "SELECT model_type, COUNT(*) as count FROM predictions WHERE user_id = '" . mysqli_real_escape_string($conn, $user_id) . "' GROUP BY model_type";
                $chart_data_result = $conn->query($chart_data_sql);
                
                $labels = ['Linear Regression', 'Lasso Regression'];
                $counts = [0, 0]; // Default counts

                if ($chart_data_result && $chart_data_result->num_rows > 0) {
                    while ($row = $chart_data_result->fetch_assoc()) {
                        if (strtolower($row['model_type']) == 'linear') {
                            $counts[0] = $row['count'];
                        } elseif (strtolower($row['model_type']) == 'lasso') {
                            $counts[1] = $row['count'];
                        }
                    }
                }

                // Encode data for JavaScript
                $labels_js = json_encode($labels);
                $counts_js = json_encode($counts);
                ?>
                <div class="chart-container">
                    <canvas id="myChart"></canvas>
                </div>
                <script>
                    const ctx = document.getElementById('myChart').getContext('2d');
                    const myChart = new Chart(ctx, {
                        type: 'bar', // Use 'bar' chart for displaying counts
                        data: {
                            labels: <?php echo $labels_js; ?>,
                            datasets: [{
                                label: 'Number of Predictions',
                                data: <?php echo $counts_js; ?>,
                                backgroundColor: [
                                    '#72A0C1',
                                    '#fd5c63'
                                ],
                                borderColor: [
                                    '#72A0C1',
                                    '#fd5c63'
                                ],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: 'Count'
                                    }
                                }
                            },
                            responsive: true,
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'top',
                                },
                                title: {
                                    display: true,
                                    text: 'Predictions Count by Model Type'
                                }
                            }
                        }
                    });
                </script>
                <?php
            }
            ?>
        </div>
    </div>
</div>

<footer>
    <p>&copy; 2024 House Price Prediction System. All rights reserved.</p>
</footer>
</body>
</html>
