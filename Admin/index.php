<?php
include("Conn.php");

//Fetching total number of predictions
$predictionsQuery = "SELECT COUNT(*) as total_predictions FROM predictions";
$predictionsResult = $conn->query($predictionsQuery);
$predictionsCount = $predictionsResult->fetch_assoc()['total_predictions'];

//Fetching total number of users
$usersQuery = "SELECT COUNT(*) as total_users FROM users";
$usersResult = $conn->query($usersQuery);
$usersCount = $usersResult->fetch_assoc()['total_users'];

// $modelsQuery = "SELECT COUNT(*) as total_models FROM models";
// $modelsResult = $conn->query($modelsQuery);
// $modelsCount = $modelsResult->fetch_assoc()['total_models'];

// // Fetching recent predictions
// $recent_predictions_query = "SELECT * FROM predictions ORDER BY date DESC LIMIT 4";
// $recent_predictions_result = $conn->query($recent_predictions_query);
// $recent_predictions = [];
// if ($recent_predictions_result->num_rows > 0) {
//     while ($row = $recent_predictions_result->fetch_assoc()) {
//         $recent_predictions[] = $row;
//     }
// }

$conn->close();
?>
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="index.css">
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <h2>Admin Dashboard</h2>
        <ul>
            <li><a href="Index.php" class="active">Dashboard</a></li>
            <li><a href="datamanage.php">Data Management</a></li>
            <li><a href="prediction.php">Predictions</a></li>
            <li><a href="Usersdetails.php">Users Details</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>

    <!-- Content Area -->
    <div class="content">
        <div class="header">
            <span class="menu-icon" id="menu-icon" onclick="toggleSidebar()">&#9776;</span>
            <h1>Dashboard</h1>
            <p>Welcome, Admin!</p>
        </div>

        <!-- Statistics Section -->
        <div class="statistics">
            <div class="stat-box">
                <h3>Total Predictions</h3>
                <p id="total-predictions"><?php echo $predictionsCount; ?></p>
            </div>
            
            <div class="stat-box">
                <h3>Users</h3>
                <p id="total-users"><?php echo $usersCount; ?></p>
            </div>

            <div class="stat-box">
                <h3>Models</h3>
                <p id="models">2</p>
                <!-- Dynamically display the total number of models from the database
                <p id="models"><?php echo $modelsCount; ?></p> -->
            </div>

        </div>

        <!-- Recent Predictions Section -->
        <div class="recent-predictions">
            <h2>Recent Predictions</h2>
            <table id="predictions-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Location</th>
                        <th>Size (sq ft)</th>
                        <th>Price</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>2024-08-20</td>
                        <td>Kathmandu</td>
                        <td>1,200</td>
                        <td>Nrs. 3050,000</td>
                        <td><button onclick="deletePrediction(this)">Delete</button></td>
                    </tr>
                    <tr>
                        <td>2024-08-19</td>
                        <td>Pokhara</td>
                        <td>1,800</td>
                        <td>Nrs. 450,0000</td>
                        <td><button onclick="deletePrediction(this)">Delete</button></td>
                    </tr>
                    <tr>
                        <td>2024-08-18</td>
                        <td>Lalitpur</td>
                        <td>900</td>
                        <td>Nrs. 250,0000</td>
                        <td><button onclick="deletePrediction(this)">Delete</button></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    
    <script>
        // Toggle Sidebar
        function toggleSidebar() {
            var sidebar = document.getElementById("sidebar");
            var content = document.querySelector(".content");
            if (sidebar.style.width === "0px" || sidebar.style.width === "") {
                sidebar.style.width = "250px";
                content.style.marginLeft = "270px";
                content.style.width = "calc(100% - 270px)";
            } else {
                sidebar.style.width = "0";
                content.style.marginLeft = "0";
                content.style.width = "100%";
            }
        }

        // Delete Prediction
        function deletePrediction(button) {
            var row = button.parentNode.parentNode;
            row.parentNode.removeChild(row);
            alert('Prediction deleted successfully!');
        }
    </script>
</body>

</html>
