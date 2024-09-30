<?php
include("Conn.php");

// Fetching total number of predictions
$predictionsQuery = "SELECT COUNT(*) as total_predictions FROM predictions";
$predictionsResult = $conn->query($predictionsQuery);
$predictionsCount = $predictionsResult->fetch_assoc()['total_predictions'];

// Fetching total number of users
$usersQuery = "SELECT COUNT(*) as total_users FROM users";
$usersResult = $conn->query($usersQuery);
$usersCount = $usersResult->fetch_assoc()['total_users'];

// Fetching recent predictions
$recentPredictionsQuery = "SELECT * FROM predictions ORDER BY date DESC LIMIT 4";
$recentPredictionsResult = $conn->query($recentPredictionsQuery);
$recentPredictions = [];
if ($recentPredictionsResult && $recentPredictionsResult->num_rows > 0) {
    while ($row = $recentPredictionsResult->fetch_assoc()) {
        $recentPredictions[] = $row;
    }
} else {
    $recentPredictions[] = []; // Ensure $recentPredictions is an array even if no records are found
}
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
            <li><a href="index.php" class="active">Dashboard</a></li>
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
        </div>

        <!-- Recent Predictions Section -->
        <div class="recent-predictions">
            <h2>Recent Predictions</h2>
            <table id="predictions-table">
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
                <tbody id="predictions-body">
                    <?php
                    if (!empty($recentPredictions)) {
                        $sn = 1; // For serial number
                        foreach ($recentPredictions as $row) {
                            echo "<tr>";
                            echo "<td>" . $sn++ . "</td>"; // Increment serial number
                            echo "<td>" . (isset($row["aana"]) ? $row["aana"] : 'N/A') . "</td>";
                            echo "<td>" . (isset($row["bedroom"]) ? $row["bedroom"] : 'N/A') . "</td>";
                            echo "<td>" . (isset($row["bathroom"]) ? $row["bathroom"] : 'N/A') . "</td>";
                            echo "<td>" . (isset($row["floor"]) ? $row["floor"] : 'N/A') . "</td>";
                            echo "<td>" . (isset($row["road"]) ? $row["road"] : 'N/A') . "</td>";
                            echo "<td>Nrs. " . (isset($row["price"]) ? $row["price"] : 'N/A') . "</td>";
                            echo "<td>" . (isset($row["date"]) ? $row["date"] : 'N/A') . "</td>";
                            echo "<td><button onclick='deletePrediction(this)'>Delete</button></td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='9'>No predictions found</td></tr>";
                    }
                    ?>
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
            alert('Delete Prediction?');
        }
    </script>
</body>
</html>

<?php
$conn->close();
?>
