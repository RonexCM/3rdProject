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
            <li><a href="datamanage.php">Data Management</a></li>
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
                        <th>Date</th>
                        <th>Location</th>
                        <th>Size (sq ft)</th>
                        <th>Price</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="predictions-body">
                    <!-- Predictions will be dynamically loaded here -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        //API halne yaaa)
        const predictions = [{
            date: '2024-08-20',
            location: 'Kathmandu',
            size: 1200,
            price: '3,050,000'
        }, {
            date: '2024-08-19',
            location: 'Pokhara',
            size: 1800,
            price: '4,500,000'
        }, {
            date: '2024-08-18',
            location: 'Lalitpur',
            size: 900,
            price: '2,500,000'
        }, ];

        // Function to dynamically load predictions into the table
        function loadPredictions() {
            const tableBody = document.getElementById('predictions-body');
            tableBody.innerHTML = ''; // Clear any existing rows

            predictions.forEach((prediction, index) => {
                const row = document.createElement('tr');

                // Create table cells for each data field
                const dateCell = document.createElement('td');
                dateCell.textContent = prediction.date;

                const locationCell = document.createElement('td');
                locationCell.textContent = prediction.location;

                const sizeCell = document.createElement('td');
                sizeCell.textContent = `${prediction.size} sq ft`;

                const priceCell = document.createElement('td');
                priceCell.textContent = `Nrs. ${prediction.price}`;

                const actionCell = document.createElement('td');
                const deleteButton = document.createElement('button');
                deleteButton.textContent = 'Delete';
                deleteButton.onclick = function() {
                    deletePrediction(index);
                };
                actionCell.appendChild(deleteButton);

                // Append all cells to the row
                row.appendChild(dateCell);
                row.appendChild(locationCell);
                row.appendChild(sizeCell);
                row.appendChild(priceCell);
                row.appendChild(actionCell);

                // Append the row to the table body
                tableBody.appendChild(row);
            });
        }

        // Function to delete a prediction
        function deletePrediction(index) {
            predictions.splice(index, 1); 
            loadPredictions(); 
            alert('Delete the Prediction ?');
        }

        // Toggle Sidebar
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

        // Load predictions when the page loads
        window.onload = loadPredictions;
    </script>
</body>

</html>