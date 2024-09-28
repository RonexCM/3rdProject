<?php
$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "housepredict"; 

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user details from the database
$sql = "SELECT * FROM users"; // Adjust the table name to your users table
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Users Details</title>
    <link rel="stylesheet" href="userdetails.css">
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <h2>Admin Dashboard</h2>
        <ul>
            <li><a href="Index.php">Dashboard</a></li>
            <li><a href="datamanage.php">Data Management</a></li>
            <li><a href="prediction.php">Predictions</a></li>
            <li><a href="Userdetails.php" class="active">Users Details</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>

    <!-- Content Area -->
    <div class="content">
        <!-- Header with Sidebar Toggle -->
        <div class="header">
            <span class="menu-icon" id="menu-icon" onclick="toggleSidebar()">&#9776;</span>
            <h1>Users Details</h1>
        </div>

        <!-- Users Section -->
        <div class="users">
            <div class="table-container">
                <table id="users-table">
                    <thead>
                        <tr>
                            <th>User ID</th>
                            <th>Email</th>
                            <th>Full Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $row["id"] . "</td>";
                                echo "<td>" . $row["email"] . "</td>";
                                echo "<td><button onclick='viewUser(this)'>View</button></td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5'>No users found</td></tr>";
                        }
                        ?>

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
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

        // View User Details
        function viewUser(button) {
            var row = button.parentNode.parentNode;
            var userId = row.cells[0].innerHTML;
            var email = row.cells[1].innerHTML;

            alert(`User ID: ${userId}\n email: ${email}`);
        }
    </script>
</body>

</html>

<?php
$conn->close();
?>
