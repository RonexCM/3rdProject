<?php
include("connection.php");
session_start()
?>
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
                <div class="actions">
                    <button onclick="addNewEntry()">Linear Regression</button>
                </div>
                <div class="actions">
                    <button onclick="addNewEntry()">Lasso Regression</button>
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
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>Kathmandu</td>
                            <td>1200</td>
                            <td>Nrs. 3050,000</td>
                            <td>
                                <button onclick="editEntry(this)">Edit</button>
                                <button onclick="deleteEntry(this)">Delete</button>
                            </td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>Pokhara</td>
                            <td>1800</td>
                            <td>Nrs. 450,0000</td>
                            <td>
                                <button onclick="editEntry(this)">Edit</button>
                                <button onclick="deleteEntry(this)">Delete</button>
                            </td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>Lalitpur</td>
                            <td>900</td>
                            <td>Nrs. 2500,000</td>
                            <td>
                                <button onclick="editEntry(this)">Edit</button>
                                <button onclick="deleteEntry(this)">Delete</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>