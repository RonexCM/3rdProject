

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>House Price Prediction System</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="app.js"></script>
    <link rel="stylesheet" href="../Admin/datamanage.css">
</head>
<body>
    
    <!-- Content Area -->
    <div class="content">
        <!-- Header with Sidebar Toggle -->
        <div class="header">
            <span class="menu-icon" id="menu-icon" onclick="toggleSidebar()">&#9776;</span>
            <h1>Data Management</h1>
        </div>

        <!-- Data Management Section -->
        <div class="data-management">
            <div class="flex" style="display:flex; gap:10px;">
            <form method="POST" action="">
                <div class="flex" style="display:flex; gap:15px">

                    <div class="actions">
                        <button type="submit" name="model_type" value="linear">Linear Regression</button>
                    </div>
                    <div class="actions">
                        <button type="submit" name="model_type" value="lasso">Lasso Regression</button>
                    </div>
                </div>
            </form>

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
                        </tr>
                    </thead>
                    <tbody>
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

                        // Fetch the model_type from the form POST request
                        $model_type = isset($_POST['model_type']) ? $_POST['model_type'] : '';

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
                                            <button onclick='editEntry(this)'>Edit</button>
                                            <button onclick='deleteEntry(this)'>Delete</button>
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


</body>
</html>
