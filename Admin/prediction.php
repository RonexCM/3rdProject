<?php
include("Conn.php"); 

// Fetch predictions from the database
$sql = "SELECT * FROM predictions"; 
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Predictions</title>
    <link rel="stylesheet" href="prediction.css">
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <h2>Admin Dashboard</h2>
        <ul>
            <li><a href="Index.php">Dashboard</a></li>
            <li><a href="prediction.php" class="active">Predictions</a></li>
            <li><a href="Usersdetails.php">Users Details</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>

    <!-- Content Area -->
    <div class="content">
        <div class="header">
            <span class="menu-icon" id="menu-icon" onclick="toggleSidebar()">&#9776;</span>
            <h1>Predictions</h1>
        </div>

        <!-- Recent Predictions Section -->
        <div class="recent-predictions">
            <h2>The Recent Predictions</h2>
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
                    if ($result->num_rows > 0) {
                        $sn = 1; // For serial number
                        // Output data of each row
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $sn++ . "</td>"; // Increment serial number
                            echo "<td>" . $row["aana"] . "</td>";
                            echo "<td>" . $row["bedroom"] . "</td>";
                            echo "<td>" . $row["bathroom"] . "</td>";
                            echo "<td>" . $row["floor"] . "</td>";
                            echo "<td>" . $row["road"] . "</td>";
                            echo "<td>Nrs. " . $row["price"] . "</td>";
                            echo "<td>" . $row["date"] . "</td>";
                            echo "<td><button onclick='deletePrediction(" . $row["SN"] . ")'>Delete</button></td>";
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

    <!-- JavaScript -->
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById("sidebar");
            const content = document.querySelector(".content");
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

        function deletePrediction(id) {
            if (confirm("Are you sure you want to delete this prediction?")) {
                window.location.href = "delete_prediction.php?id=" + id;
            }
        }
    </script>
</body>

</html>

<?php
$conn->close();
?>
